<?php
/**
 *
 *
 */
set_time_limit(0);
date_default_timezone_set("Asia/Taipei");
require dirname(__FILE__) . "/libs/Cardbox.php";
require dirname(__FILE__) . "/libs/Userinformation.php";

class DataProcessServer {
	private $serv;
	private $pdo;
	private $data_fd;
	private $data_data;
	private $process;
	/**
	 * [__construct description]
	 * 构造方法中,初始化 $serv 服务
	 */
	public function __construct() {
		/**
		 * server and process setting
		 * @var redis
		 */
		$this->redis = new redis();
		$result = $this->redis->connect("127.0.0.1", 6379, 0);

		$this->serv = new swoole_websocket_server('0.0.0.0', 9513);
		//$this->serv = new swoole_websocket_server('172.17.10.31', 9513);
		//初始化swoole服务
		$this->serv->set(array(
			'daemonize' => false, //是否作为守护进程,此配置一般配合log_file使用
			'max_request' => 100000,
			'log_file' => './log/data_process.log',
		));

		//开启WorkerStart
		$this->serv->on('WorkerStart', array($this, 'onWorkerStart'));

		//设置监听
		$this->serv->on('Open', array($this, 'onStart'));
		$this->serv->on('Connect', array($this, 'onConnect'));
		$this->serv->on("Message", array($this, 'onMessage'));
		$this->serv->on("Close", array($this, 'onClose'));

		// bind callback
		$this->serv->on("Task", array($this, 'onTask'));
		$this->serv->on("Finish", array($this, 'onFinish'));
		//开启
		$this->serv->start();

	}

	/**
	 * For processing data
	 * @param  swoole_process $worker [swoole_process woker]
	 * @return void
	 */
	public function processData(swoole_process $worker) {
		while ($data = $this->redis->BRPOPLPUSH('dataProcess', 'foreverData', 0)) {
			$worker->write("$data \n");
			//var_dump($data);
			$deCodeData = json_decode($data, true);
			//var_dump($deCodeData);
			switch ($deCodeData['event']) {
			case 'getRoomPeople':
				$roomData = $this->redis->hget("Server1", $deCodeData['roomId']);
				$retVal = [];
				$retVal['userData'] = $deCodeData;
				$retVal['event'] = $deCodeData['event'];
				$retVal['connect_fd'] = $deCodeData['connect_fd'];
				$retVal['peopleNum'] = count(json_decode($roomData, true));

				$retVal = json_encode($retVal);
				$this->redis->RPUSH('message', $retVal);
				break;
			case 'JoinRoom': //加入房間

				break;
			case 'initCard': //牌盒
				$cardBox = new Carbox();
				$retVal = [];
				$retVal['event'] = $deCodeData['event'];
				$retVal['userData'] = $deCodeData;
				$retVal['cardData'] = $cardBox->dealCard();
				//print_r($retVal['cardData']);
				//$Round_array = '{"1":1,"2":2,"3":3,"4":4}';
				$Round_array = '1234';
				$this->redis->hset("CardList", $deCodeData['roomId'], json_encode($retVal['cardData']));
				$this->redis->hset("Round", $deCodeData['roomId'], json_encode($Round_array));
				$retVal = json_encode($retVal);
				//$push_out_card    = json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true);   // 建置 廢棄池牌
				$this->redis->hset("push_out_card", $deCodeData['roomId'], "");
				$this->redis->hset("Round_row", $deCodeData['roomId'], "1"); // 初始回合
				$this->redis->RPUSH('message', $retVal);
				//var_dump($startCard);
				//var_dump($playerCard);
				break;

			case 'outCard': /// 出牌
				//echo $data;
				//錯誤的code
				//echo $data;
				$Userinformation = new Userinformation();
				$parseData = json_decode($data, true);
				$cardData = json_decode($this->redis->hget("CardList", $parseData['roomId']), true);
				//$key       = "player" . $parseData['player'];
				$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);
				$push_out_card = json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true); //查詢 廢棄池牌
				$Round_row = json_decode($this->redis->hget("Round_row", $parseData['roomId']), true); //取得回合數
				$endCard1 = count($cardData['endCard']);
				switch ($endCard1) {
				case "0":
					echo "牌合空了 \n";
					$retVal = [];
					$deCodeData['type'] = "10";
					$deCodeData['event'] = "RoundEnd";
					$retVal['event'] = $deCodeData['event'];
					$retVal['cardData'] = $cardData;
					$retVal = json_encode($retVal);
					$this->redis->RPUSH('message', $retVal);
					break;
				default:
					$cardData_player = $Userinformation->cardData_player($cardData);
					//print_r($cardData_player);
					$payer_user = $parseData['player'];
					$check_outCard = $Userinformation->check_outCard($payer_user, $Round, $cardData_player[0], $cardData_player[1], $cardData_player[2], $cardData_player[3], $cardData); // check_outCard_round
					$OK = $check_outCard;

					$check_outCard_round = $Userinformation->check_outCard_round($Round, $payer_user);
					//var_dump($check_outCard_round);
					$Round_array = $check_outCard_round["Round_array"];
					$key = $check_outCard_round["key"];
					$key2 = $check_outCard_round["key2"];
					$push_out_card .= $parseData['data'] . ",";
					$Round_row = ($Round_row + 1);
					//echo $parseData['data'] ;  //打掉的牌
					//remove card from user's card
					if (($rmKey = array_search($parseData['data'], $cardData[$key])) !== false) {
						array_splice($cardData[$key], $rmKey, 1);
					}
					//print_r($cardData);
					$cardData = $Userinformation->left_hand($cardData, $parseData['data'], $payer_user); //玩家打牌
					//print_r($cardData2);
					$retVal = [];
					$retVal['event'] = $deCodeData['event'];
					$retVal['userData'] = $deCodeData;
					$retVal['cardData'] = $cardData;
					$retVal['push_out_card'] = $push_out_card;
					$retVal['Round_row'] = $Round_row;
					$retVal = json_encode($retVal);

					if ($OK != 1) {
						//
						$this->redis->hset("CardList", $parseData['roomId'], json_encode($cardData));
						$gameLog = $this->redis->hset("gameLog", $parseData['roomId'], json_encode($data));
						$this->redis->hset("Round", $parseData['roomId'], json_encode($Round_array));
						$this->redis->hset("push_out_card", $parseData['roomId'], json_encode($push_out_card)); // 打掉的牌
						$this->redis->hset("Round_row", $parseData['roomId'], json_encode($Round_row)); // 回合數+1
						$this->redis->RPUSH('message', $retVal);
						//檢查點
						$push_out_card2 = json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true); //查詢 廢棄池牌
						//送出下家結果
						$send_remind = $this->send_remind($data, $key2, $deCodeData, $Round_array);
						//print_r($send_remind);
						$retVal = [];
						$deCodeData['event'] = "SEND_REMIND";
						$deCodeData['type'] = "11";
						$retVal['event'] = $deCodeData['event'];
						$retVal['userData'] = $deCodeData;
						$retVal['cardData'] = $cardData;
						$retVal['send_remind'] = $send_remind;
						$retVal = json_encode($retVal);

						$this->redis->RPUSH('message', $retVal);
						//var_dump($push_out_card2);

						// 自動call下家 拿牌
						//$cardData = $this->auto_put($data, $key2, $deCodeData, $Round_array);

						//print_r($cardData);
						//call 拿牌
						$cardData = json_decode($this->redis->hget("CardList", $parseData['roomId']), true); //在再度check手牌
						$endCard = count($cardData['endCard']);
						echo $endCard . "\n";
						//$cardData_player = $Userinformation->cardData_player($cardData);
						//print_r($cardData_player);
						switch ($endCard) {
						case "0":
							echo "已打出最後一張 \n";
							$retVal = [];
							$deCodeData['event'] = "ROUNDEND";
							$deCodeData['type'] = "10";
							$retVal['event'] = $deCodeData['event'];
							$retVal['userData'] = $deCodeData;
							$retVal['cardData'] = $cardData;
							$retVal = json_encode($retVal);

							$this->redis->RPUSH('message', $retVal);
							//$retVal              = [];
							// $deCodeData['event'] = "RoundEnd";
							// $retVal['event']     = $deCodeData['event'];
							// $retVal              = json_encode($retVal);
							// $this->redis->RPUSH('message', $retVal);

							break;
						default:
							//echo "Your favorite color is neither red, blue, nor green!";

						}
						//
					}
				}
				break;

			case 'getCard': // 取牌
				//echo $data;
				$parseData = json_decode($data, true);
				$cardData = json_decode($this->redis->hget("CardList", $parseData['roomId']), true);
				$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);
				$Round_row = json_decode($this->redis->hget("Round_row", $parseData['roomId']), true); //取得回合數
				$endCard = count($cardData['endCard']);
				echo $endCard . "\n";
				switch ($endCard) {
				case "0":
					echo "不能抽卡 \n";
					break;
				default:
					$Userinformation = new Userinformation();
					//remove card from user's card
					$newCard = array_shift($cardData['endCard']);
					//$key       = "player" . $parseData['player'];
					$cardData_player = $Userinformation->cardData_player($cardData);
					//print_r($cardData_player);
					$payer_user = $parseData['player'];
					$check_hand = $Userinformation->check_hand($payer_user, $cardData_player[0], $cardData_player[1], $cardData_player[2], $cardData_player[3], $Round, $cardData);
					//echo $check_hand;
					$OK = $check_hand;
					$check_round = $Userinformation->check_round($Round, $parseData['player']);
					$Round_array = $check_round["Round_array"];
					$key = $check_round["key"];

					//push card to user's card
					array_push($cardData[$key], $newCard);

					$retVal = [];
					$retVal['event'] = $deCodeData['event'];
					$retVal['userData'] = $deCodeData;
					$retVal['cardData'] = $cardData;
					$retVal['Round'] = $Round_array;
					$retVal['Round_row'] = $Round_row;
					//var_dump($retVal) ;
					$retVal = json_encode($retVal);

					if ($OK != 1) {
						$this->redis->hset("CardList", $parseData['roomId'], json_encode($cardData));
						$this->redis->hset("gameLog", $parseData['roomId'], json_encode($data));
						$this->redis->hset("Round", $deCodeData['roomId'], json_encode($Round_array));
						$this->redis->RPUSH('message', $retVal);
					}
				}

				break;
			case 'NO_CHOICE': //NO_CHOICE
				echo "收到 NO_CHOICE  事件 \n";
				break;
			case 'BUMP': //碰
				echo "收到 碰 事件 \n";
				$Userinformation = new Userinformation();
				$RECEIVE = $Userinformation->BUMP($data, $cardData);
				//$RECEIVE["Round_row"] = ($RECEIVE["Round"] + 1);
				//print_r($RECEIVE);
				$RECEIVE2 = (json_decode($RECEIVE, true));
				//print_r($RECEIVE2);
				print_r($RECEIVE2["cardData"]);
				print_r($RECEIVE2["userData"]);
				$RECEIVE2["event"] = "getCard";
				$RECEIVE2["userData"]["event"] = "getCard";
				$RECEIVE2 = json_encode($RECEIVE2);
				$this->redis->hset("CardList", $parseData['roomId'], json_encode($RECEIVE2["cardData"]));
				$this->redis->hset("Round", $parseData['roomId'], json_encode($RECEIVE2["Round"]));
				$this->redis->RPUSH('message', $RECEIVE2);
				/*
					$this->redis->hset("CardList", $parseData['roomId'], json_encode($RECEIVE2["cardData"]));
					$this->redis->hset("Round", $parseData['roomId'], json_encode($RECEIVE2["Round"]));
					$Round_row = json_decode($this->redis->hget("Round_row", $parseData['roomId']), true); //取得回合數
				*/

				//print_r($RECEIVE2["cardData"]);
				//$this->redis->hset("CardList", $parseData['roomId'], json_encode($RECEIVE["cardData"]));
				//print_r($RECEIVE["cardData"]);
				//$retVal = json_encode($RECEIVE);
				/*
				$newCard = array_shift($cardData['endCard']);
				$payer_user = $parseData['player'];
				$retVal = [];
				$deCodeData['event'] = "getCard";
				$retVal['event'] = $deCodeData['event'];
				$retVal['userData'] = $deCodeData;
				$A = array();
				$cardData["player" . $parseData["player"]] = $A;
				$retVal['cardData'] = $cardData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row;
				//var_dump($retVal) ;
				print_r($$cardData["player" . $parseData["player"]]);
				print_r($retVal);
				$retVal = json_encode($retVal);
				$this->redis->hset("CardList", $parseData['roomId'], json_encode($cardData));
				$this->redis->hset("gameLog", $parseData['roomId'], json_encode($data));
				$this->redis->hset("Round", $deCodeData['roomId'], json_encode($Round_array));
				$this->redis->RPUSH('message', $retVal);
*/
				/*
					$this->redis->hset("CardList", $parseData['roomId'], json_encode($RECEIVE["cardData"]));
					$this->redis->hset("gameLog", $parseData['roomId'], json_encode($data));
					$this->redis->hset("Round", $parseData['roomId'], json_encode($RECEIVE["Round"]));
					$this->redis->hset("Round_row", $parseData['roomId'], json_encode($RECEIVE["Round_row"])); // 回合數+1
					$this->redis->RPUSH('message', $retVal);
				*/
				break;
			case 'BARS': //槓
				echo "收到 槓 事件 \n";
				$Userinformation = new Userinformation();
				$RECEIVE = $Userinformation->BARS($data, $cardData);
				/*
					                    $parseData = json_decode($data, true);
					                    $push_out_card2 = (json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true)); //查詢 廢棄池牌
					                    $output = explode(",", substr($push_out_card2, 0, -1));
					                    echo end($output) . "\n";
					                    print_r($parseData) . "\n";
				*/
				break;
			case 'RECEIVE': //吃
				echo "收到 吃 事件 \n";
				$Userinformation = new Userinformation();
				$RECEIVE = $Userinformation->RECEIVE($data, $cardData);
				/*
					                    $parseData = json_decode($data, true);
					                    $push_out_card2 = (json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true)); //查詢 廢棄池牌
					                    $output = explode(",", substr($push_out_card2, 0, -1));
					                    echo end($output) . "\n";
					                    print_r($parseData) . "\n";
				*/
				break;

			default:
				# code...
				break;
			}
			echo "Data:" . $data . PHP_EOL;
			$file = './output/date_processs_Data.txt';
			$current = file_get_contents($file);
			$current .= "-------------------------\n";
			$current .= date("Y-m-d H:i:s") . "Data 送出\n";
			$current .= $data . "\n";
			$current .= "-------------------------\n";
			file_put_contents($file, $current);

		}
		$worker->daemon(true); //2017-05-016 add
		//$worker->exit(0);

	}
	/**
	 *提醒 吃 碰 槓
	 *
	 */
	public function send_remind($data, $key2, $deCodeData, $Round_array) {

		$parseData = json_decode($data, true);
		$cardData = json_decode($this->redis->hget("CardList", $parseData['roomId']), true);
		$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);
		$player1 = $cardData['player1'];
		$player2 = $cardData['player2'];
		$player3 = $cardData['player3'];
		$player4 = $cardData['player4'];
		/// 唯獨台灣 有禁止 槓上家   //data  //player
		//echo $Round . "\n";
		switch ($parseData["player"]):
	case 1:
		//$P2_result[] = array();
		$P2_result["RECEIVE"] = $this->check_RECEIVE($player2, $parseData["data"]);
		$P2_result["BUMP"] = $this->check_BUMP($player2, $parseData["data"]);
		$P2_result["BARS"] = $this->check_BARS($player2, $parseData["data"]);
		//$P3_result[] = array();
		//$P3_result["RECEIVE"] = $this->check_RECEIVE($player3, $parseData["data"]);
		$P3_result["RECEIVE"] = -1;
		$P3_result["BUMP"] = $this->check_BUMP($player3, $parseData["data"]);
		$P3_result["BARS"] = $this->check_BARS($player3, $parseData["data"]);
		//$P4_result[] = array();
		//$P4_result["RECEIVE"] = $this->check_RECEIVE($player4, $parseData["data"]);
		$P4_result["RECEIVE"] = -1;
		$P4_result["BUMP"] = $this->check_BUMP($player4, $parseData["data"]);
		$P4_result["BARS"] = $this->check_BARS($player4, $parseData["data"]);
		$result["P2"] = $P2_result;
		$result["P3"] = $P3_result;
		$result["P4"] = $P4_result;
		//print_r($result);
		return $result;
		break;
	case 2:
		//$P1_result[] = array();
		//$P1_result["RECEIVE"] = $this->check_RECEIVE($player2, $parseData["data"]);
		$P1_result["RECEIVE"] = -1;
		$P1_result["BUMP"] = $this->check_BUMP($player1, $parseData["data"]);
		$P1_result["BARS"] = $this->check_BARS($player1, $parseData["data"]);
		//$P3_result[] = array();
		$P3_result["RECEIVE"] = $this->check_RECEIVE($player3, $parseData["data"]);
		$P3_result["BUMP"] = $this->check_BUMP($player3, $parseData["data"]);
		$P3_result["BARS"] = $this->check_BARS($player3, $parseData["data"]);
		//$P4_result[] = array();
		//$P4_result["RECEIVE"] = $this->check_RECEIVE($player4, $parseData["data"]);
		$P4_result["RECEIVE"] = -1;
		$P4_result["BUMP"] = $this->check_BUMP($player4, $parseData["data"]);
		$P4_result["BARS"] = $this->check_BARS($player4, $parseData["data"]);
		$result["P1"] = $P1_result;
		$result["P3"] = $P3_result;
		$result["P4"] = $P4_result;
		//print_r($result);
		return $result;
		break;
	case 3:
		//$P2_result[] = array();
		//$P2_result["RECEIVE"] = $this->check_RECEIVE($player2, $parseData["data"]);
		$P2_result["RECEIVE"] = -1;
		$P2_result["BUMP"] = $this->check_BUMP($player2, $parseData["data"]);
		$P2_result["BARS"] = $this->check_BARS($player2, $parseData["data"]);
		//$P1_result[] = array();
		//$P1_result["RECEIVE"] = $this->check_RECEIVE($player2, $parseData["data"]);
		$P1_result["RECEIVE"] = -1;
		$P1_result["BUMP"] = $this->check_BUMP($player1, $parseData["data"]);
		$P1_result["BARS"] = $this->check_BARS($player1, $parseData["data"]);
		//$P4_result[] = array();
		$P4_result["RECEIVE"] = $this->check_RECEIVE($player4, $parseData["data"]);
		$P4_result["BUMP"] = $this->check_BUMP($player4, $parseData["data"]);
		$P4_result["BARS"] = $this->check_BARS($player4, $parseData["data"]);
		$result["P1"] = $P1_result;
		$result["P2"] = $P2_result;
		$result["P4"] = $P4_result;
		//print_r($result);
		return $result;
		break;
	case 4:
		//$P2_result[] = array();
		//$P2_result["RECEIVE"] = $this->check_RECEIVE($player2, $parseData["data"]);
		$P2_result["RECEIVE"] = -1;
		$P2_result["BUMP"] = $this->check_BUMP($player2, $parseData["data"]);
		$P2_result["BARS"] = $this->check_BARS($player2, $parseData["data"]);
		//$P3_result[] = array();
		//$P3_result["RECEIVE"] = $this->check_RECEIVE($player3, $parseData["data"]);
		$P3_result["RECEIVE"] = -1;
		$P3_result["BUMP"] = $this->check_BUMP($player3, $parseData["data"]);
		$P3_result["BARS"] = $this->check_BARS($player3, $parseData["data"]);
		//$P1_result[] = array();
		$P1_result["RECEIVE"] = $this->check_RECEIVE($player1, $parseData["data"]);
		$P1_result["BUMP"] = $this->check_BUMP($player1, $parseData["data"]);
		$P1result["BARS"] = $this->check_BARS($player1, $parseData["data"]);
		$result["P1"] = $P1_result;
		$result["P2"] = $P2_result;
		$result["P3"] = $P3_result;
		//print_r($result);
		return $result;
		break;
	default:
		//echo "i is not equal to 0, 1 or 2";
		endswitch;

	}
	//吃
	public function check_RECEIVE($data, $data2) {
		//print_r($data);
		//$data = array(0 => 11, 1 => 12, 2 => 13, 3 => 14, 4 => 29, 5 => 26, 6 => 25, 7 => 25, 8 => 25, 9 => 26, 10 => 26, 11 => 26, 12 => 36, 13 => 36);
		//$data2 = 11;
		if ($data2 < 40) {
			$data = array_count_values($data);
			//print_r($data);
			$D = array();
			foreach ($data as $key => $value) {
				$D[] = $key;
			}
			//print_r($D);
			$A1 = ($data2 + 2);
			$A2 = ($data2 - 2);
			$A3 = ($data2 + 1);
			$A4 = ($data2 - 1);
			$data2_s = str_split($data2, 1);
			switch ($data2_s[1]) {
			case 1:
				$Arr1[0] = $A3;
				$Arr1[1] = $data2;
				$Arr1[2] = $A1;
				$row = 0;
				foreach ($Arr1 as $key => $value) {if (in_array($value, $D)) {$row++;}}
				if ($row >= 2) {
					return $Arr1;
				} else {
					return -1;
				}
				break;
			case 9:
				$Arr1[0] = $A2;
				$Arr1[1] = $data2;
				$Arr1[2] = $A4;
				$row = 0;
				foreach ($Arr1 as $key => $value) {
					if (in_array($value, $D)) {
						//echo "Match found";
						$row++;
					}
				}
				if ($row >= 2) {
					return $Arr1;
				} else {
					return -1;
				}
				break;
			case 2:
				//return 2;
				$Arr1[0] = $A3;
				$Arr1[2] = $A1;
				$Arr2[0] = $A4;
				$Arr2[2] = $A3;

				$row = 0;
				$row2 = 0;
				foreach ($Arr1 as $key => $value) {
					if (in_array($value, $D)) {
						//echo "Match found";
						//$row++;
					} else {
						$row++;
					}
				}
				foreach ($Arr2 as $key => $value) {
					if (in_array($value, $D)) {
						//echo "Match found";
						//$row2++;
					} else {
						$row2++;
					}
				}
				if ($row == '0' and $row2 == '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					return array_merge($Arr1, $Arr2);
				} else if ($row != '0' and $row2 == '0') {
					$Arr2[1] = $data2;
					return $Arr2;
				} else if ($row == '0' and $row2 != '0') {
					$Arr1[1] = $data2;
					return $Arr1;
				} else {
					return -1;
				}
				break;
			case 8:
				//return 8;
				$Arr1[0] = $A4;
				$Arr1[2] = $A3;
				$Arr2[0] = $A2;
				$Arr2[2] = $A4;
				$row = 0;
				$row2 = 0;
				foreach ($Arr1 as $key => $value) {
					if (in_array($value, $D)) {
						//echo "Match found";
						//$row++;
					} else {
						$row++;
					}
				}
				foreach ($Arr2 as $key => $value) {
					if (in_array($value, $D)) {
						//echo "Match found";
						//$row2++;
					} else {
						$row2++;
					}
				}
				//echo $row . "- " . $row2 . "<br>";

				if ($row == '0' and $row2 == '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					return array_merge($Arr1, $Arr2);
				} else if ($row != '0' and $row2 == '0') {
					$Arr2[1] = $data2;
					return $Arr2;
				} else if ($row == '0' and $row2 != '0') {
					$Arr1[1] = $data2;
					return $Arr1;
				} else {
					return -1;
				}

				break;
			case 3:
				//return 3;
				$Arr1[0] = $A4;
//$Arr1[1] = $data2;
				$Arr1[2] = $A3;

				$Arr2[0] = $A2;
//$Arr2[1] = $data2;
				$Arr2[2] = $A4;

				$Arr3[0] = $A3;
//$Arr3[1] = $data2;
				$Arr3[2] = $A1;
				$row = 0;
				$row2 = 0;
				$row3 = 0;
				foreach ($Arr1 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row++;
					} else {
						$row++;
					}
				}
				foreach ($Arr2 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row2++;
					} else {
						$row2++;
					}
				}
				foreach ($Arr3 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row2++;
					} else {
						$row3++;
					}
				}
				if ($row == '0' and $row2 == '0' and $row3 == '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					$Arr3[1] = $data2;
					return array_merge($Arr1, $Arr2, $Arr3);
				} else if ($row == '0' and $row2 != '0' and $row3 == '0') {
					$Arr1[1] = $data2;
					$Arr3[1] = $data2;
					return array_merge($Arr1, $Arr3);
				} else if ($row == '0' and $row2 == '0' and $row3 != '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					return array_merge($Arr1, $Arr2);
				} else if ($row != '0' and $row2 != '0' and $row3 == '0') {
					$Arr3[1] = $data2;
					return $Arr3;
				} else if ($row != '0' and $row2 == '0' and $row3 != '0') {
					$Arr2[1] = $data2;
					return $Arr2;
				} else {
					return -1;
				}

				break;
			case 7:
				//return 7;
				$Arr1[0] = $A4;
				$Arr1[2] = $A3;
				$Arr2[0] = $A2;
				$Arr2[2] = $A4;
				$Arr3[0] = $A3;
				$Arr3[2] = $A1;
				$row = 0;
				$row2 = 0;
				$row3 = 0;
				foreach ($Arr1 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row++;
					} else {
						$row++;
					}
				}
				foreach ($Arr2 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row2++;
					} else {
						$row2++;
					}
				}
				foreach ($Arr3 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row2++;
					} else {
						$row3++;
					}
				}
				if ($row == '0' and $row2 == '0' and $row3 == '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					$Arr3[1] = $data2;
					return array_merge($Arr1, $Arr2, $Arr3);
				} else if ($row == '0' and $row2 != '0' and $row3 == '0') {
					$Arr1[1] = $data2;
					$Arr3[1] = $data2;
					return array_merge($Arr1, $Arr3);
				} else if ($row == '0' and $row2 == '0' and $row3 != '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					return array_merge($Arr1, $Arr2);
				} else if ($row != '0' and $row2 != '0' and $row3 == '0') {
					$Arr3[1] = $data2;
					return $Arr3;
				} else if ($row != '0' and $row2 == '0' and $row3 != '0') {
					$Arr2[1] = $data2;
					return $Arr2;
				} else {
					return -1;
				}
				break;
			default:
				$Arr1[0] = $A4;
//$Arr1[1] = $data2;
				$Arr1[2] = $A3;

				$Arr2[0] = $A2;
//$Arr2[1] = $data2;
				$Arr2[2] = $A4;

				$Arr3[0] = $A3;
//$Arr3[1] = $data2;
				$Arr3[2] = $A1;
				$row = 0;
				$row2 = 0;
				$row3 = 0;
				foreach ($Arr1 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row++;
					} else {
						$row++;
					}
				}
				foreach ($Arr2 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row2++;
					} else {
						$row2++;
					}
				}
				foreach ($Arr3 as $key => $value) {
					if (in_array($value, $D)) {
//echo "Match found";
						//$row2++;
					} else {
						$row3++;
					}
				}
				if ($row == '0' and $row2 == '0' and $row3 == '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					$Arr3[1] = $data2;
					return array_merge($Arr1, $Arr2, $Arr3);
				} else if ($row == '0' and $row2 != '0' and $row3 == '0') {
					$Arr1[1] = $data2;
					$Arr3[1] = $data2;
					return array_merge($Arr1, $Arr3);
				} else if ($row == '0' and $row2 == '0' and $row3 != '0') {
					$Arr1[1] = $data2;
					$Arr2[1] = $data2;
					return array_merge($Arr1, $Arr2);
				} else if ($row != '0' and $row2 != '0' and $row3 == '0') {
					$Arr3[1] = $data2;
					return $Arr3;
				} else if ($row != '0' and $row2 == '0' and $row3 != '0') {
					$Arr2[1] = $data2;
					return $Arr2;
				} else if ($row == '0' and $row2 != '0' and $row3 != '0') {
					$Arr1[1] = $data2;
					return $Arr1;
				} else {
					return -1;
				}

				//return 99;
			}

		} else {
			return -1;
		}

	}
	//碰
	public function check_BUMP($data, $data2) {
		array_push($data, $data2);
		//print_r($data);
		$D = array_count_values($data);
		//print_r($D);
		foreach ($D as $key => $value) {
			if ($key == $data2) {
				//echo $key . '-' . $value . "\n";
				if ($value > 2) {
					$out[0] = $data2;
					$out[1] = $data2;
					$out[2] = $data2;
					return $out;
					//return 1;
				} else {
					return -1;
				}
			}
		}
		//print_r($D);
	}
	//槓
	public function check_BARS($data, $data2) {
		array_push($data, $data2);
		//print_r($data);
		$D = array_count_values($data);
		//print_r($D);
		foreach ($D as $key => $value) {

			if ($key == $data2) {
				//echo $key . '-' . $value . "\n";
				if ($value > 3) {
					$out[0] = $data2;
					$out[1] = $data2;
					$out[2] = $data2;
					$out[3] = $data2;
					return $out;
					//return 1;
				} else {
					return -1;
				}
			}
		}
	}

	/**
	 * auto_put  自動取牌
	 *
	 */
	public function auto_put($data, $key2, $deCodeData, $Round_array) {
		//print 'Inside `aMemberFunc()`';
		//print_r($data) ;
		//return 12345;

		$parseData = json_decode($data, true);
		//print_r($parseData);
		$cardData = json_decode($this->redis->hget("CardList", $parseData['roomId']), true);
		$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);
//push card to user's card
		//$key2 = "player2";
		//echo $key2 . "\n";
		$newCard = array_shift($cardData['endCard']);
		array_push($cardData[$key2], $newCard);

		$retVal = [];
		//getCard  1
		$deCodeData['event'] = "getCard";
		$deCodeData['type'] = "1";
		$retVal['event'] = $deCodeData['event'];
		$retVal['userData'] = $deCodeData;
		$retVal['cardData'] = $cardData;
		$retVal['Round'] = $Round_array;
		//$retVal['push_out_card'] = $parseData['data'];
		//print_r($cardData);

//var_dump($retVal) ;
		$retVal = json_encode($retVal);

		$this->redis->hset("CardList", $parseData['roomId'], json_encode($cardData));
		//$this->redis->hset("gameLog", $parseData['roomId'], json_encode($data));
		//$this->redis->hset("Round", $deCodeData['roomId'], json_encode($Round_array));
		$this->redis->RPUSH('message', $retVal);

		return $cardData;
	}

	/**
	 * Start Server
	 * @param  [type] $serv    [server]
	 * @param  [type] $request [request]
	 * @return [void]
	 */
	public function onStart($serv, $request) {
		//echo SWOOLE_VERSION . " onStart\n";
	}

	/**
	 * [When Woker is Start]
	 * @param  [type] $serv      [server]
	 * @param  [type] $worker_id [wokerId]
	 * @return [void]
	 */
	public function onWorkerStart($serv, $worker_id) {
		echo "master pid:" . $this->serv->master_pid;
		if ($worker_id == 0) {
			$this->process = new swoole_process(array($this, 'processData'));
			$pid = $this->process->start();

			swoole_event_add($this->process->pipe, function ($pipe) {
				$data = $this->process->read();

			});

			///2017-05-17 add
			swoole_process::signal(SIGCHLD, function ($sig) {
				//必须为false，非阻塞模式
				while ($ret = swoole_process::wait(false)) {
					echo "PID={$ret['pid']}\n";
				}
			});
			///2017-05-17 add

			// while(true)
			// {
			//     # 等待回收，如果不回收进程会变成僵死进程，很可怕的
			//     if (false === swoole_process::wait())
			//     {
			//         break;
			//     }
			// }

		} else {

		}

		echo "Woker " . $worker_id . " is Start\n";
	}

	/**
	 * [When Client is Connect]
	 * @param  [type] $serv [server]
	 * @param  [type] $fd   [clientId]
	 * @return [void]
	 */
	public function onConnect($serv, $fd) {
		echo "Client{$fd} Connect.\n";
		$serv->send($fd, "hello, welcome\n");
		array_push($this->connections, $fd);
	}

	/**
	 * When message is comming
	 * @param  [type] $serv  [server]
	 * @param  [type] $frame [data]
	 * @return [void]
	 */
	public function onMessage($serv, $frame) {
	}

	/**
	 * When Task is Start
	 * @param  [type] $serv    [server]
	 * @param  [type] $task_id [taskId]
	 * @param  [type] $from_id [fromId]
	 * @param  [type] $data    [data]
	 * @return [void]
	 */
	public function onTask($serv, $task_id, $from_id, $data) {
		$file = './temp/onTask_date_processs.txt';
		$current = file_get_contents($file);
		$current .= "-------------------------\n";
		$current .= date("Y-m-d H:i:s") . "date_processs onTask Runing \n";
		$current .= print_r($serv, true) . "\n";
		$current .= print_r($task_id, true) . "\n";
		$current .= print_r($from_id, true) . "\n";
		$current .= print_r($frame, true) . "\n";
		$current .= "-------------------------\n";
		file_put_contents($file, $current);
	}

	/**
	 * When Client close connect
	 * @param  [type] $serv [server]
	 * @param  [type] $fd   [clientId]
	 * @return [void]
	 */
	public function onClose($serv, $fd) {
		echo "Client Close.\n";
		$file = './temp/date_processs.txt';
		$current = file_get_contents($file);
		$current .= "-------------------------\n";
		$current .= date("Y-m-d H:i:s") . "Client Close 關閉\n";
		$current .= print_r($serv, true) . "\n";
		$current .= print_r($fd, true) . "\n";
		$current .= "-------------------------\n";
		file_put_contents($file, $current);

	}

	/**
	 * When Task is finish
	 * @param  [type] $serv    [server]
	 * @param  [type] $task_id [taskId]
	 * @param  [type] $data    [data]
	 * @return [void]
	 */
	public function onFinish($serv, $task_id, $data) {
		echo "Task {$task_id} finish\n";
		echo "Result: {$data}\n";
		$file = './temp/date_processs.txt';
		$current = file_get_contents($file);
		$current .= "-------------------------\n";
		$current .= date("Y-m-d H:i:s") . "Task {$task_id} finish\n";
		$current .= print_r($data, true) . "\n";
		$current .= "-------------------------\n";
		file_put_contents($file, $current);

	}

}
$server = new DataProcessServer();
