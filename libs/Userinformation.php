<?php
//Userinformation

class Userinformation {
	//private $data = array();

	public function __construct() {
		$this->redis = new redis();
		$result = $this->redis->connect("127.0.0.1", 6379, 0);
	}

	public function __destruct() {

	}
	public function Call_check() {
		return true;
	}
	public function RECEIVE($data, $cardData, $push_out_card2, $Round_row, $Round) {
		//return true;
		$parseData = json_decode($data, true);
		$output = explode(",", substr($push_out_card2, 0, -1));
		echo end($output) . "\n";
		if ($parseData["data"][1] != end($output)) {
			echo "違法送Vaule\n";
			return -1;
		} else {
			//unset($parseData["data"][2]);
			$row = 0;
			$Puse = $parseData["player"];
			array_push($cardData["player" . $Puse], end($output));
			print_r($cardData["player" . $Puse]);
			foreach ($parseData["data"] as $key => $value) {
				//echo $value . "\n";
				foreach ($cardData["player" . $Puse] as $key2 => $value2) {

					if ($value == $value2 and $row != 3) {
						unset($cardData["player" . $parseData["player"]][$key2]);
						//unset($parseData["data"][$key]);
						$row++;
					}
				}
			}
			if ($row = 3) {
				$cardData["player" . $parseData["player"]] = array_values($cardData["player" . $parseData["player"]]);
				//print_r($cardData["player" . $parseData["player"]]);
				if ($cardData["PUTDATE"]["player" . $parseData["player"]]["SHUN"] == ' ') {
					$cardData["PUTDATE"]["player" . $parseData["player"]]["SHUN"] = array();
				}

				//$cardData["PUTDATE"]["player" . $parseData["player"]]["SHUN"] = array_merge($cardData["PUTDATE"]["player" . $parseData["player"]]["SHUN"], $parseData2["data"]);
				$cardData["PUTDATE"]["player" . $parseData["player"]]["SHUN"] = array_merge($cardData["PUTDATE"]["player" . $parseData["player"]]["SHUN"], $parseData["data"]);
				$deCodeData = json_decode($data, true);
				switch ($parseData["player"]) {
				case "1":
					$Round_array = "1234";
					$retVal = [];
					$retVal['event'] = $parseData['event'];
					//$retVal['event'] = "A ";
					$retVal['userData'] = $deCodeData;
					$retVal['Round'] = $Round_array;
					$retVal['Round_row'] = $Round_row + 1;
					$retVal['cardData'] = $cardData;
					$retVal = json_encode($retVal);
					return $retVal;
					break;
				case "2":
					$Round_array = "2341";
					$retVal = [];
					$retVal['event'] = $parseData['event'];
					//$retVal['event'] = " A ";
					$retVal['userData'] = $deCodeData;
					$retVal['Round'] = $Round_array;
					$retVal['Round_row'] = $Round_row + 1;
					$retVal['cardData'] = $cardData;
					$retVal = json_encode($retVal);
					return $retVal;
					break;
				case "3":
					$Round_array = "3412";
					$retVal = [];
					$retVal['event'] = $parseData['event'];
					//$retVal['event'] = " A ";
					$retVal['userData'] = $deCodeData;
					$retVal['Round'] = $Round_array;
					$retVal['Round_row'] = $Round_row + 1;
					$retVal['cardData'] = $cardData;
					$retVal = json_encode($retVal);
					return $retVal;
					break;
				case "4":
					$Round_array = "4123";
					$retVal = [];
					$retVal['event'] = $parseData['event'];
					//$retVal['event'] = " A ";
					$retVal['userData'] = $deCodeData;
					$retVal['Round'] = $Round_array;
					$retVal['Round_row'] = $Round_row + 1;
					$retVal['cardData'] = $cardData;
					$retVal = json_encode($retVal);
					return $retVal;
					break;
				}
			} else {
				echo "違法送Vaule\n";
				return -1;
			}

		}

	}
	public function BUMP($data, $cardData, $push_out_card2, $Round_row, $Round) {
		//return true;
		$parseData = json_decode($data, true);
		$output = explode(",", substr($push_out_card2, 0, -1));
		if ($parseData["data"][0] != end($output)) {
			echo "違法送Vaule\n";
			return -1;
		} else {
			$row = 0;
			foreach ($cardData["player" . $parseData["player"]] as $key => $value) {
				if ($row != 2) {

					if ($value == end($output)) {
						unset($cardData["player" . $parseData["player"]][$key]);
						$row++;
					}
				}
			}
			$cardData["player" . $parseData["player"]] = array_values($cardData["player" . $parseData["player"]]);
			//print_r($cardData["player" . $parseData["player"]]);
			if ($cardData["PUTDATE"]["player" . $parseData["player"]]["SECTION"] == ' ') {
				$cardData["PUTDATE"]["player" . $parseData["player"]]["SECTION"] = array();
			}
			$cardData["PUTDATE"]["player" . $parseData["player"]]["SECTION"] = array_merge($cardData["PUTDATE"]["player" . $parseData["player"]]["SECTION"], $parseData["data"]);
			$deCodeData = json_decode($data, true);
			switch ($parseData["player"]) {
			case "1":
				$Round_array = "1234";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = "A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			case "2":
				$Round_array = "2341";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = " A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			case "3":
				$Round_array = "3412";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = " A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			case "4":
				$Round_array = "4123";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = " A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			}

		}

	}
	public function BARS($data, $cardData, $push_out_card2, $Round_row, $Round) {
		//return true;
		$parseData = json_decode($data, true);
		$output = explode(",", substr($push_out_card2, 0, -1));
		if ($parseData["data"][0] != end($output)) {
			echo "違法送Vaule\n";
			return -1;
		} else {
			$row = 0;
			foreach ($cardData["player" . $parseData["player"]] as $key => $value) {
				if ($row != 3) {

					if ($value == end($output)) {
						unset($cardData["player" . $parseData["player"]][$key]);
						$row++;
					}
				}
			}
			// 槓 藥補一張牌
			$newCard = array_pop($cardData['endCard']);
			array_push($cardData["player" . $parseData["player"]], $newCard);

			$cardData["player" . $parseData["player"]] = array_values($cardData["player" . $parseData["player"]]);
			//print_r($cardData["player" . $parseData["player"]]);
			if ($cardData["PUTDATE"]["player" . $parseData["player"]]["BARS"] == ' ') {
				$cardData["PUTDATE"]["player" . $parseData["player"]]["BARS"] = array();
			}
			$cardData["PUTDATE"]["player" . $parseData["player"]]["BARS"] = array_merge($cardData["PUTDATE"]["player" . $parseData["player"]]["BARS"], $parseData["data"]);
			$deCodeData = json_decode($data, true);
			switch ($parseData["player"]) {
			case "1":
				$Round_array = "1234";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = "A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			case "2":
				$Round_array = "2341";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = " A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			case "3":
				$Round_array = "3412";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = " A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			case "4":
				$Round_array = "4123";
				$retVal = [];
				$retVal['event'] = $parseData['event'];
				//$retVal['event'] = " A ";
				$retVal['userData'] = $deCodeData;
				$retVal['Round'] = $Round_array;
				$retVal['Round_row'] = $Round_row + 1;
				$retVal['cardData'] = $cardData;
				$retVal = json_encode($retVal);
				return $retVal;
				break;
			}

		}

	}

/**
 *使用者丟棄手牌
 *
 */
	public function left_hand($cardData, $push_out_card, $payer_user) {
		switch ($payer_user) {

		case "1":
			echo "玩家 1 \n";
			$cardData["player1_out"] .= $push_out_card . ",";
			$cardData["player2_out"] .= "";
			$cardData["player3_out"] .= "";
			$cardData["player4_out"] .= "";
			return $cardData;
			//print_r($cardData);
			break;
		case "2":
			echo "玩家 2 \n";
			$cardData["player1_out"] .= "";
			$cardData["player2_out"] .= $push_out_card . ",";
			$cardData["player3_out"] .= "";
			$cardData["player4_out"] .= "";
			return $cardData;
			//print_r($cardData);
			break;
		case "3":
			echo "玩家 3 \n";
			$cardData["player1_out"] .= "";
			$cardData["player2_out"] .= "";
			$cardData["player3_out"] .= $push_out_card . ",";
			$cardData["player4_out"] .= "";
			return $cardData;
			//print_r($cardData);
			break;
		case "4":
			echo "玩家 4 \n";
			$cardData["player1_out"] .= "";
			$cardData["player2_out"] .= "";
			$cardData["player3_out"] .= "";
			$cardData["player4_out"] .= $push_out_card . ",";
			return $cardData;
			//print_r($cardData);
			break;
		default:
			//echo "Your favorite color is neither red, blue, nor green!";
			return $cardData;
		}

	}

	public function check_round($Round, $player) {
		$parseData['player'] = $player;
		$out[] = array();

		switch ($Round) {

		case "1234":
			echo "玩家 1 \n";
			$Round_array = '1234';
			//$key       = "player1" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			return $out;
			break;
		case "2341":
			echo "玩家 2 \n";
			$Round_array = '2341';
			//$key       = "player2" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			return $out;
			break;
		case "3412":
			echo "玩家 3 \n";
			$Round_array = '3412';
			//$key       = "player3" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			return $out;
			break;
		case "4123":
			echo "玩家 4 \n";
			$Round_array = '4123';
			//$key       = "player4" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			return $out;
			break;
		default:
			//echo "Your favorite color is neither red, blue, nor green!";
		}

	}
	public function check_null($xx) {
		if ($xx == " ") {
			return 0;
		} else {
			return count($xx);
		}
	}

	public function check_hand($payer_user, $P1, $P2, $P3, $P4, $Round, $cardData) {

		$p = $cardData["PUTDATE"]["player" . $payer_user]["BARS"];
		if ($p == " ") {
			$C = 0;
		} else {
			$C = count($cardData["PUTDATE"]["player" . $payer_user]["BARS"]);
		}
		echo $C . "\n";
		//$MAX = $this->MAX_hand($C);

		$p0 = $cardData["player" . $payer_user];
		$p1 = $cardData["PUTDATE"]["player" . $payer_user]["SHUN"];
		$p2 = $cardData["PUTDATE"]["player" . $payer_user]["SECTION"];
		$p3 = $cardData["PUTDATE"]["player" . $payer_user]["BARS"];
		$C0 = $this->check_null($p0);
		$C1 = $this->check_null($p1);
		$C2 = $this->check_null($p2);
		$C3 = $this->check_null($p3);
		$sum = $C0 + $C1 + $C2 + $C3;
		$MAX = 13;

		if ($C == '0') {
			$MAX = $MAX;
		} else if ($C == '4') {
			$MAX = $MAX + 1;
		} else if ($C == '8') {
			$MAX = $MAX + 2;
		} else if ($C == '12') {
			$MAX = $MAX + 3;
		} else if ($C == '16') {
			$MAX = $MAX + 4;
		}
		echo $MAX . "\n";
/*
$C0 = count($cardData["PUTDATE"]["player" . $payer_user]);
$C1 = count($cardData["PUTDATE"]["player" . $payer_user]["SHUN"]);
$C2 = count($cardData["PUTDATE"]["player" . $payer_user]["SECTION"]);
$C3 = count($cardData["PUTDATE"]["player" . $payer_user]["BARS"]);
 */
		switch ($payer_user) {
		case "1":
			if ($Round == "1234" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 0;
				} else {
					echo "不抽牌 \n";
					return 1;
				}
			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}

			break;
		case "2":
			if ($Round == "2341" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 0;
				} else {
					echo "不抽牌 \n";
					return 1;
				}
			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}
			break;
		case "3":
			if ($Round == "3412" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 0;
				} else {
					echo "不抽牌 \n";
					return 1;
				}
			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}
			break;
		case "4":
			if ($Round == "4123" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 0;
				} else {
					echo "不抽牌 \n";
					return 1;
				}
			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}

			break;

		default:

		}
	}
	public function cardData_player($cardData) {
		//$cardData["PUTDATE"]["player1"]["SHUN"] = array(0 => 11, 1 => 12, 3 => 13);
		//$cardData["PUTDATE"]["player1"]["SHUN"] = array(0 => 11, 1 => 12, 3 => 13);
		$P11 = $this->count_p($cardData["PUTDATE"]["player1"]["SHUN"]);
		$P12 = $this->count_p($cardData["PUTDATE"]["player1"]["SECTION"]);
		$P13 = $this->count_p($cardData["PUTDATE"]["player1"]["BARS"]);

		$P21 = $this->count_p($cardData["PUTDATE"]["player2"]["SHUN"]);
		$P22 = $this->count_p($cardData["PUTDATE"]["player2"]["SECTION"]);
		$P23 = $this->count_p($cardData["PUTDATE"]["player2"]["BARS"]);

		$P31 = $this->count_p($cardData["PUTDATE"]["player3"]["SHUN"]);
		$P32 = $this->count_p($cardData["PUTDATE"]["player3"]["SECTION"]);
		$P33 = $this->count_p($cardData["PUTDATE"]["player3"]["BARS"]);

		$P41 = $this->count_p($cardData["PUTDATE"]["player4"]["SHUN"]);
		$P42 = $this->count_p($cardData["PUTDATE"]["player4"]["SECTION"]);
		$P43 = $this->count_p($cardData["PUTDATE"]["player4"]["BARS"]);

		//echo $P11 . "-" . $P12 . "-" . $P13 . "\n";
		//echo $P21 . "-" . $P22 . "-" . $P23 . "\n";
		//echo $P31 . "-" . $P32 . "-" . $P33 . "\n";
		//echo $P41 . "-" . $P42 . "-" . $P43 . "\n";
		//$cccc = $this->count_p($cardData["PUTDATE"]["player1"]["SHUN"]);
		/*
	        $cardData_player[0] = count($cardData["player1"]);
	        $cardData_player[1] = count($cardData["player2"]);
	        $cardData_player[2] = count($cardData["player3"]);
	        $cardData_player[3] = count($cardData["player4"]);
*/

		$cardData_player[0] = count($cardData["player1"]) + $P11 + $P12 + $P13;
		$cardData_player[1] = count($cardData["player2"]) + $P21 + $P22 + $P23;
		$cardData_player[2] = count($cardData["player3"]) + $P31 + $P32 + $P43;
		$cardData_player[3] = count($cardData["player4"]) + $P41 + $P42 + $P43;

		return $cardData_player;
	}
	public function count_p($p) {
		if (empty($p)) {
			//echo "string2\n";
			return 0;
		} else if ($p == " ") {
			//echo "他為空" . "\n";
			$P11 = 0;
			return 0;
		} else {
			//echo "NO為空" . "\n";
			$P11 = count($p);
			return $P11;
		}

	}

	public function check_outCard($payer_user, $Round, $P1, $P2, $P3, $P4, $cardData) {

		$p = $cardData["PUTDATE"]["player" . $payer_user]["BARS"];
		if ($p == " ") {
			$C = 0;
		} else {
			$C = count($cardData["PUTDATE"]["player" . $payer_user]["BARS"]);
		}
		echo $C . "\n";
		//$MAX = $this->MAX_hand($C);

		$p0 = $cardData["player" . $payer_user];
		$p1 = $cardData["PUTDATE"]["player" . $payer_user]["SHUN"];
		$p2 = $cardData["PUTDATE"]["player" . $payer_user]["SECTION"];
		$p3 = $cardData["PUTDATE"]["player" . $payer_user]["BARS"];
		$C0 = $this->check_null($p0);
		$C1 = $this->check_null($p1);
		$C2 = $this->check_null($p2);
		$C3 = $this->check_null($p3);
		$sum = $C0 + $C1 + $C2 + $C3;
		$MAX = 13;

		if ($C == '0') {
			$MAX = $MAX;
		} else if ($C == '4') {
			$MAX = $MAX + 1;
		} else if ($C == '8') {
			$MAX = $MAX + 2;
		} else if ($C == '12') {
			$MAX = $MAX + 3;
		} else if ($C == '16') {
			$MAX = $MAX + 4;
		}
		echo $MAX . "\n";

		switch ($payer_user) {
		case "1":
			if ($Round == "1234" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 1;
				} else {
					echo "打牌 \n";
					return 0;
				}

			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}

			break;
		case "2":
			if ($Round == "2341" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 1;
				} else {
					echo "打牌 \n";
					return 0;
				}

			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}
			break;
		case "3":
			if ($Round == "3412" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 1;
				} else {
					echo "打牌 \n";
					return 0;
				}

			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}
			break;
		case "4":
			if ($Round == "4123" and $payer_user = 1) {
				echo '正確回合';
				echo $C0 . "-" . $C1 . "-" . $C2 . "-" . $C3 . "-" . "\n";
				if ($MAX == $sum) {
					echo "抽牌 \n";
					return 1;
				} else {
					echo "打牌 \n";
					return 0;
				}

			} else {

				echo "錯誤使用者 不抽牌 \n";
				return 1;

			}

			break;

		default:

		}
	}
//check_outCard_round

	public function check_outCard_round($Round, $player) {
		$parseData['player'] = $player;
		$out[] = array();
		switch ($Round) {

		case "1234":
			echo "玩家 1 \n";
			$Round_array = '2341';
			//$key       = "player1" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			$out['key2'] = "player2";
			return $out;
			break;
		case "2341":
			echo "玩家 2 \n";
			$Round_array = '3412';
			//$key       = "player2" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			$out['key2'] = "player3";
			return $out;
			break;
		case "3412":
			echo "玩家 3 \n";
			$Round_array = '4123';
			//$key       = "player3" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			$out['key2'] = "player4";
			return $out;
			break;
		case "4123":
			echo "玩家 4 \n";
			$Round_array = '1234';
			//$key       = "player4" ;
			$key = "player" . $parseData['player'];
			$out['Round_array'] = $Round_array;
			$out['key'] = $key;
			$out['key2'] = "player1";
			return $out;
			break;
		default:
			//echo "Your favorite color is neither red, blue, nor green!";
		}

	}
	public function MAX_hand($c) //最大手牌
	{
		/*
			if ($c = 0) {
				return 13;
			} elseif ($c = 1) {
				return 14;
			} elseif ($c = 2) {
				return 15;
			} elseif ($c = 3) {
				return 16;
			} elseif ($c = 4) {
				return 17;
			}
		*/
		if ($c = 4) {
			return 14;
		} elseif ($c = 8) {
			return 15;
		} elseif ($c = 12) {
			return 16;
		} elseif ($c = 16) {
			return 17;
		} else {
			return 13;
		}

		//echo $c . "\n";
	}
}
