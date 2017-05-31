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
	public function RECEIVE($data, $cardData) {
		//return true;
		$parseData = json_decode($data, true);
		$push_out_card2 = (json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true)); //查詢 廢棄池牌
		$Round_row = json_decode($this->redis->hget("Round_row", $parseData['roomId']), true); //取得回合數
		$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);

		$output = explode(",", substr($push_out_card2, 0, -1));
		echo end($output) . "\n";
		//print_r($cardData) . "\n";
		//echo "\n";
		//print_r($parseData) . "\n";
		//echo "\n";
		print_r($Round_row) . "\n"; // 目前回合數
		echo "\n";
		//print_r($Round) . "\n"; //下家
		//echo "\n";

	}
	public function BUMP($data, $cardData) {
		//return true;
		$parseData = json_decode($data, true);
		$push_out_card2 = (json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true)); //查詢 廢棄池牌
		$Round_row = json_decode($this->redis->hget("Round_row", $parseData['roomId']), true); //取得回合數
		$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);

		$output = explode(",", substr($push_out_card2, 0, -1));
		echo end($output) . "\n";
		//print_r($cardData) . "\n";
		//echo "\n";
		//print_r($parseData) . "\n";
		//echo "\n";
		print_r($Round_row) . "\n"; // 目前回合數
		echo "\n";
		//print_r($Round) . "\n"; //下家
		//echo "\n";) . "\n"; //下家
		//echo "\n";

	}
	public function BARS($data, $cardData) {
		//return true;
		$parseData = json_decode($data, true);
		$push_out_card2 = (json_decode($this->redis->hget("push_out_card", $parseData['roomId']), true)); //查詢 廢棄池牌
		$Round_row = json_decode($this->redis->hget("Round_row", $parseData['roomId']), true); //取得回合數
		$Round = json_decode($this->redis->hget("Round", $parseData['roomId']), true);

		$output = explode(",", substr($push_out_card2, 0, -1));
		echo end($output) . "\n";
		//print_r($cardData) . "\n";
		//echo "\n";
		//print_r($parseData) . "\n";
		//echo "\n";
		print_r($Round_row) . "\n"; // 目前回合數
		echo "\n";
		//print_r($Round) . "\n"; //下家
		//echo "\n";

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
		if ($c = 0) {
			return $MAX = 13;
		} elseif ($c = 1) {
			return $MAX = 14;
		} elseif ($c = 2) {
			return $MAX = 15;
		} elseif ($c = 3) {
			return $MAX = 16;
		} elseif ($c = 4) {
			return $MAX = 17;
		}
	}

	public function check_hand($payer_user, $P1, $P2, $P3, $P4, $Round, $cardData) {

		//print_r($cardData);
		//var_dump($payer_user);
		//var_dump($P1);
		//var_dump($P2);
		//var_dump($P4);
		//PUTDATE BARS
		switch ($payer_user) {
		case "1":
			switch ($Round) {
			case "1234":
				echo '正確回合';
				$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				if (empty($C)) {
					$C = 0;
				} else if ($C == " ") {
					$C = 0;
				} else {
					$C = $C;
				}

				$MAX = $this->MAX_hand($C);

				if ($P1 != $MAX) {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		case "2":
			switch ($Round) {
			case "2341":
				echo '正確回合';
				$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				if (empty($C)) {
					$C = 0;
				} else if ($C == " ") {
					$C = 0;
				} else {
					$C = $C;
				}

				$MAX = $this->MAX_hand($C);
				if ($P2 != $MAX) {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		case "3":

			switch ($Round) {
			case "3412":
				echo '正確回合';
				$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				if (empty($C)) {
					$C = 0;
				} else if ($C == " ") {
					$C = 0;
				} else {
					$C = $C;
				}

				$MAX = $this->MAX_hand($C);
				if ($P3 != $MAX) {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		case "4":
			switch ($Round) {
			case "4123":
				echo '正確回合';
				$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				if (empty($C)) {
					$C = 0;
				} else if ($C == " ") {
					$C = 0;
				} else {
					$C = $C;
				}

				$MAX = $this->MAX_hand($C);
				if ($P4 != $MAX) {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		default:
			return 0;
			//echo "Your favorite color is neither red, blue, nor green!";
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
		//print_r($p);
		/*
	        echo "string\n";
	        if (empty($d)) {
	        echo "string2\n";
	        return 0;
	        } else {
	        echo "string3\n";
	        return count($d);
	        }
*/
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
		//echo $payer_user."\n";
		//echo $Round."\n";
		//echo $P1."\n";
		//echo $P2."\n";
		//echo $P3."\n";
		//echo $P4."\n";
		//

		switch ($payer_user) {
		case "1":
			switch ($Round) {
			case "1234":
				echo '正確回合';

				$p = $cardData["PUTDATE"]["player1"]["BARS"];
				if ($p == " ") {
					$C = 0;
				} else {
					$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				}

				//echo $C;
				$MAX = $this->MAX_hand($C);
				if ($P1 == '13' and $P2 == '13' and $P3 == '13' and $P4 == '13') {
					echo '尚未開局';
					$OK = 1;
					return 1;

				} else {
					//echo $P1 . " - " . $P2 . " - " . $P3 . " - " . $P4 . " - ";

					if ($P1 == $MAX or $P1 == '13' and $P2 == '13' and $P3 == '13' and $P4 == '13') {
						$OK = 0;
						return 0;
					} else {
						echo '錯誤手牌';
						$OK = 1;
						return 1;
					}
					//echo $OK;
					break;
				}

			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		case "2":
			switch ($Round) {
			case "2341":
				echo '正確回合';
				$p = $cardData["PUTDATE"]["player1"]["BARS"];
				if ($p == " ") {
					$C = 0;
				} else {
					$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				}
				$MAX = $this->MAX_hand($C);
				//if ($P2 == $MAX or $P2 == '13' and $P1 == '13' and $P3 == '13' and $P4 == '13') {
				if ($P2 == $MAX and $P1 == '13' and $P3 == '13' and $P4 == '13') {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		case "3":

			switch ($Round) {
			case "3412":
				echo '正確回合';
				$p = $cardData["PUTDATE"]["player1"]["BARS"];
				if ($p == " ") {
					$C = 0;
				} else {
					$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				}
				$MAX = $this->MAX_hand($C);
				//if ($P3 == $MAX or $P3 == '13' and $P1 == '13' and $P2 == '13' and $P4 == '13') {
				if ($P3 == $MAX and $P1 == '13' and $P2 == '13' and $P4 == '13') {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		case "4":
			switch ($Round) {
			case "4123":
				echo '正確回合';
				$p = $cardData["PUTDATE"]["player1"]["BARS"];
				if ($p == " ") {
					$C = 0;
				} else {
					$C = count($cardData["PUTDATE"]["player1"]["BARS"]);
				}
				$MAX = $this->MAX_hand($C);
				//if ($P4 == $MAX or $P4 == '13' and $P1 == '13' and $P2 == '13' and $P3 == '13') {
				if ($P4 == $MAX and $P1 == '13' and $P2 == '13' and $P3 == '13') {
					$OK = 0;
					return 0;
				} else {
					echo '錯誤手牌';
					$OK = 1;
					return 1;
				}
				break;
			default:
				echo '不正確回合';
				$OK = 1;
				return 1;
			}
			break;
		default:
			//echo "Your favorite color is neither red, blue, nor green!";
		}
	}
//check_outCard_round
}
