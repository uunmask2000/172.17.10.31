<?php
class CheckWin {
	## 胡牌模型
	public function checkWinningModel($hand, $right, $selfTouch, $wind, $selfWind, $leftNum, $kongFlower, $banker, $circle, $new, $listen) {
		$start = microtime();
		## 內定的變数區

		$point = 0; ## 記錄番數
		$type = array(); ## 記錄胡牌的牌型
		$oldhand = array(); ## 记录目前手牌
		$oldhand = $hand; ## 目前手上的13張牌，用以判斷九蓮寶燈之牌型
		array_push($hand, $new); ## 把第14張牌加入手牌，成為新手牌，用以判斷胡牌
		sort($hand); ## 排序手牌
		$eyes = array(); ## 接收眼的陣列
		$sequence = array(); ## 接收順子的陣列
		$triplet = array(); ## 接收對(刻)子的陣列
		$kong = array(); ## 接收明槓的陣列
		$blackKong = array(); ## 接收暗槓的陣列
		$x = 0; ## 迴圈用
		$z = 0; ## 眼用
		$highCount = 0; ## 紀錄幾對般高
		$tripletCount = 0; ## 紀錄碰了幾對刻子
		$blackTripletCount = 0; ## 紀錄摸了幾對刻子
		$fourOneCount = 0; ## 判斷四歸1、四歸2
		$num = 0; ## 计算组数，5组为胡牌
		$totalhand = array(); ## 該玩家全部的牌
		$totalhand = $hand + $right; ## 该玩家所有的牌
		$left = array(); ## 记录没成组的牌
		for ($i = 0; $i < count($hand); $i++) {
			echo $hand[$i] . "    ";
		}

		if (!empty($right)) {
			while ($x < count($right)) {
				if ($right[$x] == $right[$x + 1] && $right[$x] == $right[$x + 2] && $right[$x] == $right[$x + 3]) {
					$num = $num + 1;
					$str = $right[$x] . $right[$x + 1] . $right[$x + 2] . $right[$x + 3];
					array_push($kong, $str);
					$x = $x + 4;
				} elseif ($right[$x] == $right[$x + 1] && $right[$x] == $right[$x + 2]) {
					$num = $num + 1;
					$str = $right[$x] . $right[$x + 1] . $right[$x + 2];
					array_push($triplet, $str);
					$tripletCount = $tripletCount + 1;
					$x = $x + 3;
				} elseif ($right[$x + 2] - $right[$x] == 2 && $right[$x + 1] - $right[$x] == 1 && $right[$x] < 40) {
					$num = $num + 1;
					$str = $right[$x] . $right[$x + 1] . $right[$x + 2];
					array_push($sequence, $str);
					$x = $x + 3;
				} else {
					$x = $x + 1;
				}
			}
		}

		$x = 0;
		while ($x < count($hand)) {
			/*
				echo "<br/>";
				echo $hand[$x] . "<br/>";
				echo "  x =  " . $x . "<br/>";
				echo "成对组数：" . $num . "<br/>";
				echo "<br/>";
				echo "流放牌数 :" . count($left);
				echo "<br>";
				echo "<br>";
			*/
			## 四隻同樣的牌，拆牌規則如下：
			if ($hand[$x] == $hand[$x + 1] && $hand[$x] == $hand[$x + 2] && $hand[$x] == $hand[$x + 3]) {
				$blackTripletCount = $blackTripletCount + 1;
				## 一順一对 以5為例，455556
				if ($hand[$x] < 40 && $x != 0 && $hand[$x] - $hand[$x - 1] == 1 && $hand[$x + 4] - $hand[$x] == 1 && $hand[$x + 4] != $hand[$x + 5] && $hand[$x - 1] == $left[count($left) - 1]) {
					//echo "<br>" . "455556" . "<br/>";
					$num = $num + 2;
					$str = $hand[$x - 1] . $hand[$x] . $hand[$x + 4];
					array_push($sequence, $str);
					$str = $hand[$x + 1] . $hand[$x + 2] . $hand[$x + 3];
					array_push($triplet, $str);
					$fourOneCount = 1;
					array_splice($left, count($left) - 1);
					$x = $x + 5;
					## 一順一对 以5為例，555567
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 6 && $hand[$x + 4] - $hand[$x] == 1 && $hand[$x + 5] - $hand[$x] == 2) {
					//echo "<br>" . "555567" . "<br/>";
					$num = $num + 2;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
					array_push($triplet, $str);
					$str = $hand[$x + 3] . $hand[$x + 4] . $hand[$x + 5];
					array_push($sequence, $str);
					$fourOneCount = 1;
					$x = $x + 6;
					## 二順一门 以5為例，55556677
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 8 && $hand[$x + 4] == $hand[$x + 5] && $hand[$x + 6] == $hand[$x + 7] && $hand[$x + 4] - $hand[$x] == 1 && $hand[$x + 6] - $hand[$x] == 2) {
					if ($hand[$x + 6] - $hand[$x] == 2 && $hand[$x + 5] - $hand[$x] == 1) {
						//echo "<br>" . "55556677" . "<br/>";
						$num = $num + 2;
						$eyes[$z] = $hand[$x];
						$eyes[$z + 1] = $hand[$x];
						$str = $hand[$x] . $hand[$x + 4] . $hand[$x + 6];
						array_push($sequence, $str);
						array_push($sequence, $str);
						$fourOneCount = 2;
						$x = $x + 8;
					}
					## 二顺一對，以5为例，455556678
				} elseif ($hand[$x] < 40 && $x > 0 && $x <= count($hand) - 7 && $hand[$x - 1] == $left[count($left) - 1] && $hand[$x] - $hand[$x - 1] == 1 && $hand[$x + 4] - $hand[$x] == 1 && $hand[$x + 6] - $hand[$x] == 2 && $hand[$x + 6] - $hand[$x + 5] == 1 && $hand[$x + 4] == $hand[$x + 5]) {
					if ($x <= count($hand) - 8 && $hand[$x + 7] - $hand[$x + 6] == 1) {
						//echo "<br/>" . "455556678" . "<br/>";
						$num = $num + 3;
						$str = $hand[$x - 1] . $hand[$x + 3] . $hand[$x + 4];
						array_push($sequence, $str);
						$str = $hand[$x + 5] . $hand[$x + 6] . $hand[$x + 7];
						array_push($sequence, $str);
						$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
						array_push($triplet, $str);
						$x = $x + 8;
						## 二顺一門，以5为例，45555667
					} else {
						//echo "<br/>" . "45555667" . "<br/>";
						$num = $num + 2;
						$str = $hand[$x - 1] . $hand[$x] . $hand[$x + 4];
						array_push($sequence, $str);
						$str = $hand[$x] . $hand[$x + 4] . $hand[$x + 6];
						array_push($sequence, $str);
						$eyes[$z] = $hand[$x];
						$eyes[$z + 1] = $hand[$x];
						$fourOneCount = 2;
						array_splice($left, count($left) - 1);
						$x = $x + 7;
					}

					## 二对一顺，以5为例，555566667
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 9 && $hand[$x + 4] - $hand[$x] == 1 && $hand[$x + 4] == $hand[$x + 5] && $hand[$x + 4] == $hand[$x + 6] && $hand[$x + 4] == $hand[$x + 7] && $hand[$x + 8] - $hand[$x] == 2 && $hand[$x + 8] - $hand[$x + 7] == 1) {
					//echo "<br/>" . "555566667" . "<br/>";
					$num = $num + 3;
					$str = $hand[$x] . $hand[$x + 4] . $hand[$x + 8];
					array_push($sequence, $str);
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
					array_push($triplet, $str);
					$str = $hand[$x + 4] . $hand[$x + 5] . $hand[$x + 6];
					array_push($triplet, $str);
					$x = $x + 9;
					## 一对一顺一门，以5为例，55556667
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 8 && $hand[$x + 4] - $hand[$x] == 1 && $hand[$x + 4] == $hand[$x + 5] && $hand[$x + 4] == $hand[$x + 6] && $hand[$x + 7] - $hand[$x] == 2 && $hand[$x + 7] - $hand[$x + 6] == 1) {
					//echo "<br/>" . "55556667" . "<br/>";
					$num = $num + 2;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
					array_push($triplet, $str);
					$str = $hand[$x + 3] . $hand[$x + 4] . $hand[$x + 7];
					array_push($sequence, $str);
					$eyes[$z] = $hand[$x + 5];
					$eyes[$z + 1] = $hand[$x + 6];
					$fourOneCount = 1;
					$x = $x + 8;
				} else {
					## 連4
					//echo "<br>" . "連4" . "<br/>";
					$num = $num + 1;
					$x = $x + 4;
				}
				## 三隻同樣的牌，拆牌規則如下：
			} elseif ($hand[$x] == $hand[$x + 1] && $hand[$x] == $hand[$x + 2]) {
				$blackTripletCount = $blackTripletCount + 1;
				## 一順 以5為例，45556
				if ($hand[$x] < 40 && $x <= count($hand) - 4 && $hand[$x] - $hand[$x - 1] == 1 && $hand[$x + 3] - $hand[$x] == 1 && $x != 0 && $hand[$x - 1] == $left[count($left) - 1]) {
					//echo "<br>" . "45556" . "<br/>";
					$num = $num + 1;
					$eyes[$z] = $hand[$x];
					$eyes[$z + 1] = $hand[$x];
					$str = $hand[$x - 1] . $hand[$x] . $hand[$x + 3];
					array_push($sequence, $str);
					array_splice($left, count($left) - 1);
					$x = $x + 4;
					## 對子後面接順子，以5為例，555678
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 6 && $hand[$x + 4] - $hand[$x + 3] == 1 && $hand[$x + 5] - $hand[$x + 3] == 2 && $hand[$x + 3] - $hand[$x] == 1) {
					//echo "<br>" . "555678" . "<br/>";
					$num = $num + 2;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
					array_push($triplet, $str);
					$str = $hand[$x + 3] . $hand[$x + 4] . $hand[$x + 5];
					array_push($sequence, $str);
					$x = $x + 6;
					## 一順 以5為例，55567
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 5 && $hand[$x + 3] - $hand[$x] == 1 && $hand[$x + 4] - $hand[$x] == 2 && $hand[$x + 5] != $hand[$x + 4]) {
					//echo "<br>" . "55567" . "<br/>";
					$num = $num + 1;
					$eyes[$z] = $hand[$x];
					$eyes[$z + 1] = $hand[$x];
					$str = $hand[$x + 2] . $hand[$x + 3] . $hand[$x + 4];
					array_push($sequence, $str);
					$x = $x + 5;
					## 二對一順，以5為例，555666678
				} elseif ($hand[$x] < 40 && $hand[$x] <= count($hand) - 8 && $hand[$x + 3] - $hand[$x] == 1 && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 3] == $hand[$x + 5] && $hand[$x + 3] == $hand[$x + 6] && $hand[$x + 7] - $hand[$x] == 2) {
					if ($hand[$x + 8] - $hand[$x + 7] == 1 && $x <= count($hand) - 9) {
						//echo "<br>" . "555666678" . "<br/>";
						$num = $num + 3;
						$blackTripletCount = $blackTripletCount + 1;
						$fourOneCount = 1;
						$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
						array_push($triplet, $str);
						$str = $hand[$x + 3] . $hand[$x + 4] . $hand[$x + 5];
						array_push($triplet, $str);
						$str = $hand[$x + 6] . $hand[$x + 7] . $hand[$x + 8];
						array_push($sequence, $str);
						$x = $x + 9;
						## 一順一對一門，以5為例，55566667
					} else {
						//echo "<br>" . "55566667" . "<br/>";
						$blackTripletCount = $blackTripletCount + 1;
						$fourOneCount = 1;
						$num = $num + 2;
						$str = $hand[$x + 2] . $hand[$x + 6] . $hand[$x + 7];
						array_push($sequence, $str);
						$str = $hand[$x + 3] . $hand[$x + 4] . $hand[$x + 5];
						array_push($triplet, $str);
						$eyes[$z] = $hand[$x];
						$eyes[$z + 1] = $hand[$x + 1];
						$x = $x + 8;
					}
				} else {
					## 成對子(刻子)
					//echo "<br>" . "對" . "<br/>";
					$num = $num + 1;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
					array_push($triplet, $str);
					$x = $x + 3;
				}
				## 兩隻同樣的牌，拆牌規則如下：
			} elseif ($hand[$x] == $hand[$x + 1]) {
				## 二順 以5為例，556677
				if ($hand[$x] < 40 && $x <= count($hand) - 6 && $hand[$x + 2] == $hand[$x + 3] && $hand[$x + 4] == $hand[$x + 5] && $hand[$x + 4] - $hand[$x + 3] == 1 && $hand[$x + 2] - $hand[$x] == 1 && $hand[$x + 4] - $hand[$x] == 2) {
					if ($hand[$x + 5] - $hand[$x] == 2 && $hand[$x + 3] - $hand[$x] == 1) {
						//echo "<br>" . "556677" . "<br/>";
						$num = $num + 2;
						$str = $hand[$x] . $hand[$x + 2] . $hand[$x + 4];
						array_push($sequence, $str);
						array_push($sequence, $str);
						$highCount++;
						$x = $x + 6;
					}
					## 二順一门 以5為例，後面接連4，55667777
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 8 && $hand[$x + 2] == $hand[$x + 3] && $hand[$x + 4] == $hand[$x + 5] && $hand[$x + 4] == $hand[$x + 6] && $hand[$x + 4] == $hand[$x + 7] && $hand[$x + 2] - $hand[$x] == 1 && $hand[$x + 4] - $hand[$x] == 1) {
					//echo "<br>" . "55667777" . "<br/>";
					$num = $num + 2;
					$eyes[$z] = $hand[$x + 4];
					$eyes[$z + 1] = $hand[$x + 4];
					$str = $hand[$x] . $hand[$x + 2] . $hand[$x + 4];
					array_push($sequence, $str);
					array_push($sequence, $str);
					$fourOneCount = 2;
					$blackTripletCount = $blackTripletCount + 1;
					$x = $x + 8;
					## 二順一门 以5為例，後面接連4，55666677
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 8 && $hand[$x + 2] == $hand[$x + 3] && $hand[$x + 2] == $hand[$x + 4] && $hand[$x + 2] == $hand[$x + 5] && $hand[$x + 6] == $hand[$x + 7] && $hand[$x + 2] - $hand[$x] == 1 && $hand[$x + 7] - $hand[$x] == 2) {
					if ($hand[$x + 7] - $hand[$x] == 2 && $hand[$x + 5] - $hand[$x] == 1) {
						//echo "<br>" . "55666677" . "<br/>";
						$num = $num + 2;
						$eyes[$z] = $hand[$x + 2];
						$eyes[$z + 1] = $hand[$x + 2];
						$str = $hand[$x] . $hand[$x + 2] . $hand[$x + 6];
						array_push($sequence, $str);
						array_push($sequence, $str);
						$fourOneCount = 2;
						$blackTripletCount = $blackTripletCount + 1;
						$x = $x + 8;
					}
					## 二顺 以5为例，455667
				} elseif ($hand[$x] < 40 && $x > 0 && $x <= count($hand) - 5 && $hand[$x] - $hand[$x - 1] == 1 && $hand[$x + 2] - $hand[$x] == 1 && $hand[$x + 4] - $hand[$x + 3] == 1 && $hand[$x] == $hand[$x + 1] && $hand[$x + 2] == $hand[$x + 3] && $hand[$x - 1] == $left[count($left) - 1]) {
					//echo "455667顺";
					$num = $num + 2;
					$str = $hand[$x - 1] . $hand[$x] . $hand[$x + 2];
					array_push($sequence, $str);
					$str = $hand[$x] . $hand[$x + 2] . $hand[$x + 4];
					array_push($sequence, $str);
					array_splice($left, count($left) - 1);
					$x = $x + 5;
					## 三顺 以5为例，556667778
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 9 && $hand[$x] == $hand[$x + 1] && $hand[$x + 2] - $hand[$x] == 1 && $hand[$x + 2] == $hand[$x + 3] && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 5] - $hand[$x + 4] == 1 && $hand[$x + 5] - $hand[$x] == 2 && $hand[$x + 5] == $hand[$x + 6] && $hand[$x + 6] == $hand[$x + 7] && $hand[$x + 8] - $hand[$x + 7] == 1) {
					//echo "<br/>" . "556667778" . "<br/>";
					$num = $num + 3;
					$str = $hand[$x] . $hand[$x + 2] . $hand[$x + 5];
					array_push($sequence, $str);
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 5] . $hand[$x + 8];
					array_push($sequence, $str);
					$blackTripletCount = $blackTripletCount + 2;
					$x = $x + 9;
				} else {
					## 眼(門、將)
					//echo "<br>" . "眼" . "<br/>";
					$eyes[$z] = $hand[$x];
					$eyes[$z + 1] = $hand[$x];
					$x = $x + 2;
				}
				## 取顺子
			} elseif ($hand[$x] < 40 && $hand[$x + 2] - $hand[$x] == 2 && $hand[$x + 1] - $hand[$x] == 1) {
				//echo "<br>" . "順" . "<br/>";
				$num = $num + 1;
				$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 2];
				array_push($sequence, $str);
				$x = $x + 3;
				## 以一张牌为基准，拆牌规则如下：
			} else {
				## 二顺一对 以5为例 566778888
				if ($hand[$x] < 40 && $x <= count($hand) - 9 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 3] - $hand[$x + 2] == 1 && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 5] - $hand[$x + 4] == 1 && $hand[$x + 5] == $hand[$x + 6] && $hand[$x + 5] == $hand[$x + 7] && $hand[$x + 8] == $hand[$x + 5]) {
					//echo "<br/>" . "566778888" . "<br/>";
					$num = $num + 3;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 3];
					array_push($sequence, $str);
					$str = $hand[$x + 1] . $hand[$x + 3] . $hand[$x + 5];
					array_push($sequence, $str);
					$str = $hand[$x + 5] . $hand[$x + 6] . $hand[$x + 7];
					array_push($triplet, $str);
					$blackTripletCount = $blackTripletCount + 1;
					$x = $x + 9;
					## 四顺，344556677889
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 12 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 3] - $hand[$x + 2] == 1 && $hand[$x + 3] - $hand[$x] == 2 && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 5] - $hand[$x + 4] == 1 && $hand[$x + 5] == $hand[$x + 6] && $hand[$x + 5] - $hand[$x] == 3 && $hand[$x + 7] - $hand[$x + 6] == 1 && $hand[$x + 7] == $hand[$x + 8] && $hand[$x + 7] - $hand[$x] == 4 && $hand[$x + 9] - $hand[$x + 8] == 1 && $hand[$x + 9] == $hand[$x + 10] && $hand[$x + 9] - $hand[$x] == 5 && $hand[$x + 11] - $hand[$x + 10] == 1 && $hand[$x + 11] - $hand[$x] == 6) {
					$num = $num + 4;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 3];
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 4] . $hand[$x + 5];
					array_push($sequence, $str);
					$str = $hand[$x + 6] . $hand[$x + 7] . $hand[$x + 9];
					array_push($sequence, $str);
					$str = $hand[$x + 8] . $hand[$x + 10] . $hand[$x + 11];
					array_push($sequence, $str);
					$highCount++;
					$x = $x + 12;
					## 三顺，以5为例，566777889
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 9 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 3] - $hand[$x + 1] == 1 && $hand[$x + 3] - $hand[$x] == 2 && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 3] == $hand[$x + 5] && $hand[$x + 6] - $hand[$x + 5] == 1 && $hand[$x + 6] - $hand[$x] == 3 && $hand[$x + 6] == $hand[$x + 7] && $hand[$x + 8] - $hand[$x + 7] == 1 && $hand[$x + 8] - $hand[$x] == 4) {
					echo "<br/>" . "566777889" . "<br/>";
					$num = $num + 3;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 3];
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 4] . $hand[$x + 6];
					array_push($sequence, $str);
					$str = $hand[$x + 5] . $hand[$x + 7] . $hand[$x + 8];
					array_push($sequence, $str);
					$blackTripletCount = $blackTripletCount + 1;
					$x = $x + 9;
					## 三顺一门，以5为例，56677788889
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 11 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 3] - $hand[$x] == 2 && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 3] == $hand[$x + 5] && $hand[$x + 6] - $hand[$x] == 3 && $hand[$x + 6] == $hand[$x + 7] && $hand[$x + 6] == $hand[$x + 8] && $hand[$x + 6] == $hand[$x + 9] && $hand[$x + 10] - $hand[$x] == 4) {
					//echo "<br/>" . "56677788889" . "<br/>";
					$num = $num + 3;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 3];
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 4] . $hand[$x + 6];
					array_push($sequence, $str);
					$str = $hand[$x + 5] . $hand[$x + 7] . $hand[$x + 10];
					array_push($sequence, $str);
					$eyes[$z] = $hand[$x + 8];
					$eyes[$z + 1] = $hand[$x + 9];
					$fourOneCount = 2;
					$blackTripletCount = $blackTripletCount + 2;
					$x = $x + 11;
					## 二顺，以5为例，566778
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 6 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 3] - $hand[$x] == 2 && $hand[$x + 3] == $hand[$x + 4] && $hand[$x + 5] - $hand[$x] == 3) {
					//echo "<br/>" . "566778" . "<br/>";
					$num = $num + 2;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 3];
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 4] . $hand[$x + 5];
					array_push($sequence, $str);
					if ($x <= count($hand) - 8 && $hand[$x + 5] == $hand[$x + 6] && $hand[$x + 5] == $hand[$x + 7]) {
						$blackTripletCount = $blackTripletCount + 1;
					}
					$x = $x + 6;
					## 二對一順，以5為例，566667777
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 9 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 1] == $hand[$x + 3] && $hand[$x + 1] == $hand[$x + 4] && $hand[$x + 5] - $hand[$x] == 2 && $hand[$x + 5] == $hand[$x + 6] && $hand[$x + 5] == $hand[$x + 7] && $hand[$x + 5] == $hand[$x + 8]) {
					//echo "<br/>" . "566667777" . "<br/>";
					$num = $num + 3;
					$blackTripletCount = $blackTripletCount + 2;
					$fourOneCount = 1;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 5];
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 3] . $hand[$x + 4];
					array_push($triplet, $str);
					$str = $hand[$x + 6] . $hand[$x + 7] . $hand[$x + 8];
					array_push($triplet, $str);
					$x = $x + 9;
					## 三順，以5為例，566677788
				} elseif ($hand[$x] < 40 && $x <= count($hand) - 9 && $hand[$x + 1] - $hand[$x] == 1 && $hand[$x + 1] == $hand[$x + 2] && $hand[$x + 1] == $hand[$x + 3] && $hand[$x + 4] - $hand[$x] == 2 && $hand[$x + 4] == $hand[$x + 5] && $hand[$x + 4] == $hand[$x + 6] && $hand[$x + 7] - $hand[$x] == 3 && $hand[$x + 7] == $hand[$x + 8]) {
					//echo "<br/>" . "566677788" . "<br/>";
					$num = $num + 3;
					$blackTripletCount = $blackTripletCount + 2;
					$str = $hand[$x] . $hand[$x + 1] . $hand[$x + 4];
					array_push($sequence, $str);
					$str = $hand[$x + 2] . $hand[$x + 5] . $hand[$x + 7];
					array_push($sequence, $str);
					$str = $hand[$x + 3] . $hand[$x + 6] . $hand[$x + 8];
					array_push($sequence, $str);
					$x = $x + 9;
				} else {
					## 无成组
					//echo "流放";
					array_push($left, $hand[$x]);
					$x++;
				}
			}
		}

		## 判斷眼
		if (!empty($eyes)) {
			if ($eyes[$z] == $eyes[$z + 1]) {
				$num = $num + 1;
			}
		}
/*
echo "<br/>";
$end = microtime();
$time = $end - $start;
echo "<br/>" . "執行時間為：" . $time . "秒" . "<br/>";
echo "<br/>" . "順" . "<br/>";
print_r($sequence);
echo "<br/>";
echo "<br/>" . "對" . "<br/>";
print_r($triplet);
echo "<br/>";
echo "<br/>" . "眼" . "<br/>";
print_r($eyes);
echo "<br/>";
echo "<br/>";
echo "总共组数:" . $num;
echo "<br/>";
echo "<br/>";
echo "流放牌数:" . count($left);
echo "<br/>";
 */
		## 胡牌区
		if ($num == 5) {
			## 判斷大四喜 代号1
			$res41 = $this->bigFourWinds($totalhand);
			if ($res41 != 0) {
				$point = $point + 40;
				$str = "大四喜 40番";
				array_push($type, $str);
			}

			## 判斷九蓮寶燈 代号2
			$res49 = $this->nineLotusLights($oldhand, $new);
			if ($res49 != 0) {
				$point = $point + 40;
				$str = "九莲宝灯 40番";
				array_push($type, $str);
			}

			## 判斷字一色 代号3
			$res42 = $this->pureWord($totalhand);
			if ($res42 != 0) {
				$point = $point + 40;
				$str = "字一色 40番";
				array_push($type, $str);
			}

			## 判斷清老頭 代号4
			$res = $this->pureOldMan($totalhand);
			if ($res != 0) {
				$point = $point + 40;
				$str = "清老头 40番";
				array_push($type, $str);
			}

			## 判斷四連刻 代号5
			$res43 = $this->fourTripletInARow($triplet);
			if ($res43 != 0) {
				$point = $point + 40;
				$str = "四连刻 40番";
				array_push($type, $str);
			}

			## 判断四杠
			$res = $this->fourKong($kong, $blackKong);
			if ($res != 0) {
				$point = $point + 40;
				$str = "四杠 40番";
				array_push($type, $str);
			}

			if ($res41 == 0) {
				## 判斷兩數 代号6
				$res = $this->twoNumbers($sequence, $totalhand);
				if ($res != 0) {
					$point = $point + 20;
					$str = "两数 20番";
					array_push($type, $str);
				}
			}

			## 判斷小四喜 代号7
			$res21 = $this->smallFourWinds($triplet, $eyes);
			if ($res != 0) {
				$point = $point + 20;
				$str = "小四喜 20番";
				array_push($type, $str);
			}

			## 判斷全將碰，不計三數 代号8
			$res44 = $this->all258Triplet($triplet, $sequence, $eyes);
			if ($res44 != 0) {
				$point = $point + 20;
				$str = "全将碰 20番";
				array_push($type, $str);
			}

			## 判斷天胡 代号10
			$res22 = $this->blessingOfHeaven($banker, $circle);
			if ($res22 != 0) {
				$point = $point + 20;
				$str = "天胡 20番";
				array_push($type, $str);
			}

			## 字一色($res42)、九宝莲灯($res49)，不计清一色
			if ($res42 == 0 && $res49 == 0) {
				## 判斷清一色 代号9
				$res1 = $this->pureOneSuit($totalhand);
				if ($res1 != 0) {
					$point = $point + 10;
					$str = "清一色 10番";
					array_push($type, $str);
				}
			}

			## 全将碰($res44)，不计三数
			if ($res44 == 0) {
				## 判斷三數 代号11
				$res = $this->threeNumbers($sequence, $totalhand);
				if ($res != 0) {
					$point = $point + 10;
					$str = "三数 10番";
					array_push($type, $str);
				}
			}

			## 判斷全么 代号12
			$res = $this->allOneNine($totalhand);
			if ($res != 0) {
				$point = $point + 10;
				$str = "全么 10番";
				array_push($type, $str);
			}

			## 判斷地胡 代号13
			$res11 = $this->blessingOfEarth($banker, $circle);
			if ($res11 != 0) {
				$point = $point + 10;
				$str = "地胡 10番";
				array_push($type, $str);
			}

			## 判斷大三元，不計三元牌 代号14
			$res = $this->bigThreeDragons($totalhand);
			if ($res != 0) {
				$point = $point + 10;
				$str = "大三元 10番";
				array_push($type, $str);
			} else {
				## 判斷小三元，不計三元牌 代号15
				$res1 = $this->smallThreeDragons($totalhand, $eyes);
				if ($res1 != 0) {
					$point = $point + 5;
					$str = "小三元 5番";
					array_push($type, $str);
				} else {
					## 判斷紅中的對子或槓 代号16
					$res2 = $this->red($totalhand);
					if ($res2 != 0) {
						$point = $point + 1;
						$str = "红中 1番";
						array_push($type, $str);
					}
					## 判斷發財的對子或槓 代号17
					$res3 = $this->green($totalhand);
					if ($res3 != 0) {
						$point = $point + 1;
						$str = "青发 1番";
						array_push($type, $str);
					}
					## 判斷白板的對子或槓 代号18
					$res4 = $this->white($totalhand);
					if ($res4 != 0) {
						$point = $point + 1;
						$str = "白皮 1番";
						array_push($type, $str);
					}
				}
			}

			## 判斷全帶 代号19
			$res = $this->allSameNumber($sequence, $triplet, $eyes);
			if ($res != 0) {
				$point = $point + 10;
				$str = "全带 10番";
				array_push($type, $str);
			}

			## 判斷雙龍抱 代号20
			$res = $this->doubleCouplesequence($highCount);
			if ($res != 0) {
				$point = $point + 10;
				$str = "双龙抱 10番";
				array_push($type, $str);
			}

			## 判斷四暗刻 代号21
			$res45 = $this->fourBlackTriplet($blackTripletCount);
			if ($res45 != 0) {
				$point = $point + 10;
				$str = "四暗刻 10番";
				array_push($type, $str);
			}

			## 判斷七將 代号22
			$res71 = $this->sevenPairs($hand, $right);
			if ($res71 != 0) {
				$point = $point + 7;
				$str = "七将 7番";
				array_push($type, $str);
			}

			## 判斷五門齊 代号23
			$res = $this->fiveDoors($totalhand);
			if ($res != 0) {
				$point = $point + 5;
				$str = "五门齐 5番";
				array_push($type, $str);
			}

			## 判斷三相逢 代号24
			$res = $this->threeMeet($sequence, $triplet);
			if ($res != 0) {
				$point = $point + 3;
				$str = "三相逢 3番";
				array_push($type, $str);
			}

			## 判斷凑一色 代号25
			$res = $this->mixedOneSult($totalhand);
			if ($res != 0) {
				$point = $point + 3;
				$str = "凑一色 3番";
				array_push($type, $str);
			}

			## 判斷清么 代号26
			$res = $this->clearone($sequence, $triplet, $eyes);
			if ($res != 0) {
				$point = $point + 3;
				$str = "清么 3番";
				array_push($type, $str);
			}

			## 判斷一條龍 代号27
			$res = $this->oneDargon($hand, $right);
			if ($res != 0) {
				$point = $point + 3;
				$str = "一条龙 3番";
				array_push($type, $str);
			}

			## 大四喜($res41)、字一色($res42)、四连刻($res43)、全将碰($res44)、四暗刻($res45)，不计对对胡
			if ($res41 == 0 or $res42 == 0 && $res43 != 0 && $res44 == 0 && $res45 == 0) {
				## 判断对对胡 代号28
				$res = $this->ponponfu($triplet);
				if ($res != 0) {
					$point = $point + 3;
					$str = "对对胡 3番";
					array_push($type, $str);
				}
			}

			## 判斷全求 代号29
			$res = $this->allPlease($right, $selfTouch);
			if ($res != 0) {
				$point = $point + 3;
				$str = "全求 3番";
				array_push($type, $str);
			}

			## 判断三杠
			$res = $this->threeKong($kong, $blackKong);
			if ($res != 0) {
				$point = $point + 10;
				$str = "三杠 10番";
				array_push($type, $str);
			}

			## 七将($res71)，不计门清
			if ($res71 == 0) {
				## 判断门清自摸 代号30
				$res31 = $this->doorClearAndSelfTouch($right, $selfTouch);
				if ($res31 != 0) {
					$point = $point + 3;
					$str = "门清自摸 3番";
					array_push($type, $str);
				}
			}

			## 判斷渾帶么 代号31
			$res = $this->muddy($sequence, $triplet, $eyes);
			if ($res != 0) {
				$point = $point + 2;
				$str = "浑带么 2番";
				array_push($type, $str);
			}

			## 判斷半求 代号32
			$res = $this->halfPlease($right, $selfTouch);
			if ($res != 0) {
				$point = $point + 2;
				$str = "半求 2番";
				array_push($type, $str);
			}

			## 判斷四歸二 代号33
			$res = $this->fourTow($fourOneCount);
			if ($res != 0) {
				$point = $point + 2;
				$str = "四归二 2番";
				array_push($type, $str);
			}

			## 判斷四歸一 代号34
			$res = $this->fourOne($fourOneCount);
			if ($res != 0) {
				$point = $point + 1;
				$str = "四归一 1番";
				array_push($type, $str);
			}

			## 判斷三色同刻 代号35
			$res = $this->threeSameColorTriplet($triplet);
			if ($res != 0) {
				$point = $point + 2;
				$str = "三色同刻 2番";
				array_push($type, $str);
			}

			## 判斷三連刻 代号36
			$res = $this->threeTripletInARow($triplet);
			if ($res != 0) {
				$point = $point + 2;
				$str = "三连刻 2番";
				array_push($type, $str);
			}

			## 判斷三暗刻 代号37
			$res = $this->threeBlackTriplet($blackTripletCount);
			if ($res != 0) {
				$point = $point + 2;
				$str = "三暗刻 2番";
				array_push($type, $str);
			}

			## 判斷混老頭 代号38
			$res = $this->mixOldMan($triplet, $sequence);
			if ($res != 0) {
				$point = $point + 2;
				$str = "混老头 2番";
				array_push($type, $str);
			}

			## 判斷清四碰 代号39
			$res = $this->pureFourTriplet($tripletCount);
			if ($res != 0) {
				$point = $point + 1;
				$str = "清四碰 1番";
				array_push($type, $str);
			}

			## 判斷槓上花 代号40
			$res = $this->fkongFlower($selfTouch, $kongFlower);
			if ($res != 0) {
				$point = $point + 1;
				$str = "杠上花 1番";
				array_push($type, $str);
			}

			## 判斷兩槓 代号41
			$res = $this->twoKong($kong, $blackKong);
			if ($res != 0) {
				$point = $point + 1;
				$str = "两杠 1番";
				array_push($type, $str);
			}

			## 海底捞 代号42
			$res = $this->seaFishing($selfTouch, $leftNum);
			if ($res != 0) {
				$point = $point + 1;
				$str = "海底捞 1番";
				array_push($type, $str);
			}

			## 判斷缺門 代号43
			$res = $this->noDoor($totalhand);
			if ($res != 0) {
				$point = $point + 1;
				$str = "缺门 1番";
				array_push($type, $str);
			}

			## 判斷老少或老少碰 代号44
			$res = $this->oldYoung($sequence, $triplet);
			if ($res != 0) {
				$point = $point + 1;
				$str = "老少 1番";
				array_push($type, $str);
			}

			## 判斷般高 代号45
			$res = $this->coupleSequence($highCount);
			if ($res != 0) {
				$point = $point + 1;
				$str = "般高 1番";
				array_push($type, $str);
			}
			## 判斷短么 代号46
			$res = $this->no1no9($totalhand);
			if ($res != 0) {
				$point = $point + 1;
				$str = "短么 1番";
				array_push($type, $str);
			}

			## 判斷平胡 代号47
			$res = $this->allsequences($sequence);
			if ($res != 0) {
				$point = $point + 1;
				$str = "平胡 1番";
				array_push($type, $str);
			}

			## 判斷將 代号48
			$res = $this->jum($eyes);
			if ($res != 0) {
				$point = $point + 1;
				$str = "将 1番";
				array_push($type, $str);
			}

			## 判斷缺五 代号49
			$res = $this->noFive($totalhand);
			if ($res != 0) {
				$point = $point + 1;
				$str = "缺五 1番";
				array_push($type, $str);
			}

			## 大四喜、小四喜，不计风牌
			if ($res41 == 0 && $res21 == 0) {
				## 判斷圈風牌
				$res1 = $this->circleWind($totalhand, $wind);
				if ($res1 != 0) {
					$point = $point + 1;
					$str = $wind . " 1番";
					array_push($type, $str);
				}
				## 判斷自風牌
				$res2 = $this->selfWind($totalhand, $selfWind);
				if ($res2 != 0) {
					$point = $point + 1;
					$str = $selfWind . " 1番";
					array_push($type, $str);
				}
			}

			## 七将($res71)、天胡($res22)、地胡($res11)，不计门清
			if ($res71 == 0 && $res31 == 0 && $res22 == 0 && $res11 == 0) {
				## 判断门清 代号50
				$res = $this->doorClear($right);
				if ($res != 0) {
					$point = $point + 1;
					$str = "门清 1番";
					array_push($type, $str);
				}
			}

			if ($res31 == 0) {
				## 判断自摸 代号51
				$res = $this->selfTouch($selfTouch);
				if ($res != 0) {
					$point = $point + 1;
					$str = "自摸 1番";
					array_push($type, $str);
				}
			}

			## 判断独咡
			$res = $this->onlyListen($listen);
			if ($res != 0) {
				$point = $point + 1;
				$str = "独听 1番";
				array_push($type, $str);
			}

			## 判断卡五
			$res = $this->listenFive($oldhand, $listen);
			if ($res != 0) {
				$point = $point + 1;
				$str = "卡五 1番";
				array_push($type, $str);
			}

			array_push($type, $point);
			return $type;

			## 特殊区，胡牌模型沒胡，必需去判斷一些特殊牌型，如：九蓮寶燈、十三么…等
		} else {

			## 判斷九蓮寶燈，因是特殊牌型，故此不會有清一色的問題
			$res = $this->nineLotusLights($oldhand, $new);
			if ($res != 0) {
				$point = $point + 40;
				$str = "九莲宝灯 40番";
				array_push($type, $str);

				## 判斷天胡
				$res1 = $this->blessingOfHeaven($banker, $circle);
				if ($res1 != 0) {
					$point = $point + 20;
					$str = "天胡 20番";
					array_push($type, $str);
				}

				## 判斷地胡
				$res2 = $this->blessingOfEarth($banker, $circle);
				if ($res2 != 0) {
					$point = $point + 10;
					$str = "地胡 10番";
					array_push($type, $str);
				}

				## 判断自摸
				$res = $this->selfTouch($selfTouch);
				if ($res != 0) {
					$point = $point + 1;
					$str = "自摸 1番";
					array_push($type, $str);
				}
				array_push($type, $point);
				return $type;
			}

			## 判斷十三么 (國士無雙) 代号52
			$res = $this->thirteenOne($hand, $right);
			if ($res != 0) {
				$point = $point + 40;
				$str = "国士无双 40番";
				array_push($type, $str);

				## 判斷天胡
				$res1 = $this->blessingOfHeaven($banker, $circle);
				if ($res1 != 0) {
					$point = $point + 20;
					$str = "天胡 20番";
					array_push($type, $str);
				}

				## 判斷地胡
				$res2 = $this->blessingOfEarth($banker, $circle);
				if ($res2 != 0) {
					$point = $point + 10;
					$str = "地胡 10番";
					array_push($type, $str);
				}

				## 判断自摸
				$res = $this->selfTouch($selfTouch);
				if ($res != 0) {
					$point = $point + 1;
					$str = "自摸 1番";
					array_push($type, $str);
				}
				array_push($type, $point);
				return $type;
			}

			## 判斷七將
			$res = $this->sevenPairs($hand, $right);
			if ($res != 0) {
				$point = $point + 7;
				$str = "七将 7番";
				array_push($type, $str);

				## 判斷天胡
				$res1 = $this->blessingOfHeaven($banker, $circle);
				if ($res1 != 0) {
					$point = $point + 20;
					$str = "天胡 20番";
					array_push($type, $str);
				}

				## 判斷地胡
				$res2 = $this->blessingOfEarth($banker, $circle);
				if ($res2 != 0) {
					$point = $point + 10;
					$str = "地胡 10番";
					array_push($type, $str);
				}

				## 判断自摸
				$res = $this->selfTouch($selfTouch);
				if ($res != 0) {
					$point = $point + 1;
					$str = "自摸 1番";
					array_push($type, $str);
				}

				## 判斷般高
				$res = $this->coupleSequence($highCount);
				if ($res != 0) {
					$point = $point + 1;
					$str = "般高 1番";
					array_push($type, $str);
				}

				## 判斷清一色
				$res = $this->pureOneSuit($totalhand);
				if ($res != 0) {
					$point = $point + 10;
					$str = "清一色 10番";
					array_push($type, $str);
				}

				## 判斷凑一色
				$res = $this->mixedOneSult($totalhand);
				if ($res != 0) {
					$point = $point + 3;
					$str = "凑一色 3番";
					array_push($type, $str);
				}

				array_push($type, $point);
				return $type;
			}

			//echo "<br/>" . "NoNoNo" . "<br/>";
			array_push($type, $point);
			return $type;
		}
	}

	## 牌型区

	## 平胡 1番
	private function allSequences($sequence) {
		if (count($sequence) == 4) {
			return 1;
		} else {
			return 0;
		}
	}

	## 將 1番
	private function jum($eyes) {
		if ($eyes[0] == $eyes[1]) {
			if ($eyes[0] == 18 or $eyes[0] == 28 or $eyes[0] == 38) {
				return 1;
			} else {
				return 0;
			}
		}

	}

	## 缺五 1番
	private function noFive($totalhand) {
		for ($i = 0; $i < count($totalhand); $i++) {
			if ($totalhand[$i] == 15 or $totalhand[$i] == 25 or $totalhand[$i] == 35) {
				return 0;
			}
		}
		return 1;
	}

	## 短么 1番
	private function no1no9($totalhand) {
		sort($totalhand);
		for ($i = 0; $i < count($totalhand); $i++) {
			if ($totalhand[$i] == 19 or $totalhand[$i] == 29 or $totalhand[$i] == 39 or $totalhand[$i] == 11 or $totalhand[$i] == 21 or $totalhand[$i] == 31) {
				return 0;
			}
		}
		return 1;
	}

	## 般高 1番
	private function coupleSequence($highCount) {
		if ($highCount == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	## 三元-紅中 1番
	private function red($totalhand) {
		$mystring = implode("", $totalhand);
		$findme = '454545';
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 1;
		}
	}

	## 三元-青發 1番
	private function green($totalhand) {
		$mystring = implode("", $totalhand);
		$findme = '464646';
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 1;
		}
	}

	## 三元-白板 1番
	private function white($totalhand) {
		$mystring = implode("", $totalhand);
		$findme = '474747';
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 1;
		}
	}

	## 圈風牌 1番
	private function circleWind($totalhand, $wind) {
		$wind = $this->change($wind);
		$mystring = implode("", $totalhand);
		$findme = $wind;
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 1;
		}
	}

	## 自風牌 1番
	private function selfWind($totalhand, $selfWind) {
		$selfWind = $this->change($selfWind);
		$mystring = implode("", $totalhand);
		$findme = $selfWind;
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 1;
		}
	}

	## 獨聽 1番
	private function onlyListen($listen) {
		if (count($listen) == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	## 老少 1番
	private function oldYoung($sequence, $triplet) {
		if (in_array("111213", $sequence) == true) {
			if (in_array("171819", $sequence) == true) {
				return 1;
			}
		} elseif (in_array("212223", $sequence) == true) {
			if (in_array("272829", $sequence) == true) {
				return 1;
			}
		} elseif (in_array("313233", $sequence) == true) {
			if (in_array("373839", $sequence) == true) {
				return 1;
			}
		} elseif (in_array("111111", $triplet) == true) {
			if (in_array("191919", $triplet) == true) {
				return 1;
			}
		} elseif (in_array("212121", $triplet) == true) {
			if (in_array("292929", $triplet) == true) {
				return 1;
			}
		} elseif (in_array("313131", $triplet) == true) {
			if (in_array("393939", $triplet) == true) {
				return 1;
			}
		} else {
			return 0;
		}
	}

	## 缺門 1番
	private function noDoor($totalhand) {
		foreach ($totalhand as $key => $value) {
			if ($value < 40) {
				$k = str_split($value, 1);
				$D[] = $k[0];
			} else {
				return 0;
			}
		}

		$answer = array_count_values($D);
		$count = count($answer);
		if ($count == 2) {
			return 1;
		} else {
			return 0;
		}
	}

	## 自摸 1番
	private function selfTouch($selfTouch) {
		if ($selfTouch == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	## 門清 1番
	private function doorClear($right) {
		if (empty($right)) {
			return 1;
		} else {
			return 0;
		}
	}

	## 海底撈 1番
	private function seaFishing($selfTouch, $leftNum) {
		if ($selfTouch == 1 && $leftNum == 0) {
			return 1;
		} else {
			return 0;
		}
	}

	## 兩槓 1番
	private function twoKong($kong, $blackKong) {
		$num1 = count($kong) / 4;
		$num2 = count($blackKong) / 4;
		if ($num1 + $num2 == 2) {
			return 1;
		} else {
			return 0;
		}
	}

	## 槓上花 1番
	private function fkongFlower($selfTouch, $kongFlower) {
		if ($selfTouch == 1 && $kongFlower == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	## 立直 1番

	## 搶槓胡 1番

	## 卡五 1番
	private function listenFive($oldhand, $listen) {
		$count = 0;
		if (count($listen) == 1) {
			if ($listen[0] == 15) {
				$a = array_count_values($oldhand);
				foreach ($a as $key => $value) {
					if ($value == 1) {
						if ($key == 14 or $key == 16) {
							$count++;
						}
					}
				}
				if ($count == 2) {
					return 1;
				}
			} elseif ($listen[0] == 25) {
				$a = array_count_values($oldhand);
				foreach ($a as $key => $value) {
					if ($value == 1) {
						if ($key == 24 or $key == 26) {
							$count++;
						}
					}
				}
				if ($count == 2) {
					return 1;
				}
			} elseif ($listen[0] == 35) {
				$a = array_count_values($oldhand);
				foreach ($a as $key => $value) {
					if ($value == 1) {
						if ($key == 34 or $key == 36) {
							$count++;
						}
					}
				}
				if ($count == 2) {
					return 1;
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	## 清四碰 1番
	private function pureFourTriplet($tripletCount) {
		if ($tripletCount == 4) {
			return 1;
		} else {
			return 0;
		}
	}

	## 混老頭 2番
	private function mixOldMan($triplet, $sequence) {
		if (!empty($sequence)) {
			return 0;
		}

		## 紀錄幾對字牌
		$count = 0;
		foreach ($triplet as $key => $value) {
			## 出現兩對以上的字牌，回傳false
			if ($count >= 2) {
				return 0;
			}
			$arr = str_split($value, 2);
			if ($arr[0] < 40) {
				if ($arr[0] != 11 && $arr[0] != 19 && $arr[0] != 21 && $arr[0] != 29 && $arr[0] != 31 && $arr[0] != 39) {
					return 0;
				}
			} else {
				$count++;
			}
		}
		if ($count == 1) {
			return 2;
		}
		return 0;
	}

	## 三暗刻 2番
	private function threeBlackTriplet($blackTripletCount) {
		if ($blackTripletCount == 3) {
			return 2;
		} else {
			return 0;
		}
	}

	## 三連刻 2番
	private function threeTripletInARow($triplet) {
		if (count($triplet) < 3) {
			return 0;
		}
		for ($i = 0; $i < 2; $i++) {
			if ($triplet[$i] < 40 && $triplet[$i + 1] < 40 && $triplet[$i + 2] < 40) {
				if ($triplet[$i + 1] - $triplet[$i] == 10101 && $triplet[$i + 2] - $triplet[$i] == 20202) {
					return 2;
				}
			}
		}
		return 0;
	}

	## 三色同刻 2番
	private function threeSameColorTriplet($triplet) {
		if (count($triplet) < 3) {
			return 0;
		}

		$count = 0;
		for ($i = 1; $i < count($triplet); $i++) {
			if ($triplet[$i] - $triplet[0] == 101010 or $triplet[$i] - $triplet[0] == 202020) {
				$count++;
			}
		}
		if ($count == 2) {
			return 2;
		} else {
			return 0;
		}
	}

	## 四歸一 1番
	private function fourOne($fourOneCount) {
		if ($fourOneCount == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	## 四歸二 2番
	private function fourTow($fourOneCount) {
		if ($fourOneCount == 2) {
			return 2;
		} else {
			return 0;
		}
	}

	## 半求 2番
	private function halfPlease($right, $selfTouch) {
		if (count($right) == 12 && $selfTouch == 1) {
			return 2;
		} else {
			return 0;
		}
	}

	## 渾帶么 2番
	private function muddy($sequence, $triplet, $eyes) {
		$count = 0;

		## 判斷門是否符合1,9,字
		if ($eyes[0] == $eyes[1]) {
			if ($eyes[0] < 40) {
				$arr = str_split($eyes[0], 1);
				if ($arr[1] == "1" or $arr[1] == "9") {
					$count++;
				}
			} else {
				$count++;
			}
		} else {
			return 0;
		}

		## 判斷順子是否有1,9
		if (!empty($sequence)) {
			$scount = count($sequence);
			for ($i = 0; $i < $scount; $i++) {
				if (($a = strpos($sequence[$i], "11")) === false && ($a = strpos($sequence[$i], "19")) === false && ($a = strpos($sequence[$i], "21")) === false && ($a = strpos($sequence[$i], "29")) === false && ($a = strpos($sequence[$i], "31")) === false && ($a = strpos($sequence[$i], "39")) === false) {
				} else {
					$count++;
				}
			}
		}

		## 判斷對子是否有1,9,字
		if (!empty($triplet)) {
			$tcount = count($triplet);
			for ($i = 0; $i < $tcount; $i++) {
				$arr = str_split($triplet[$i], 2);
				$b[$i] = $arr[0];
				if ($arr[0] < 40) {
					if ($arr[0] == "11" or $arr[0] == "19" or $arr[0] == "21" or $arr[0] == "29" or $arr[0] == "31" or $arr[0] == "39") {
						$count++;
					}
				} else {
					$count++;
				}
			}
			if ($tcount >= 3) {

				if ($b[2] - $b[0] == 2 && $b[1] - $b[0] == 1) {
					$count = $count + 2;
				}
			}
		}

		if ($count == 5) {
			return 2;
		} else {
			return 0;
		}
	}

	## 门清自摸 3番
	private function doorClearAndSelfTouch($right, $selfTouch) {
		$res = $this->doorClear($right);
		$res1 = $this->selfTouch($selfTouch);
		if ($res == 1 && $res1 == 1) {
			return 3;
		} else {
			return 0;
		}
	}

	## 全求 3番
	private function allPlease($right, $selfTouch) {
		if (count($right) == 12 && $selfTouch == 0) {
			return 3;
		} else {
			return 0;
		}
	}

	## 對對胡 3番
	private function ponponfu($triplet) {
		if (count($triplet) == 4) {
			return 3;
		} else {
			return 0;
		}
	}

	## 一條龍 3番
	private function oneDargon($hand, $right) {
		if (!empty($right)) {
			return 0;
		}
		$result = array_values(array_unique($hand));
		$result = implode("", $result);
		$findme1 = '111213141516171819';
		$findme2 = '212223242526272829';
		$findme3 = '313233343536373839';
		$a = strpos($result, $findme1);
		$b = strpos($result, $findme2);
		$c = strpos($result, $findme3);
		if ($a === false && $b === false && $c === false) {
			return 0;
		} else {
			return 3;
		}
	}

	## 清么 3番
	private function clearOne($sequence, $triplet, $eyes) {
		## 判斷門是否符合1,9
		if ($eyes[0] == $eyes[1]) {
			if ($eyes[0] < 40) {
				$arr = str_split($eyes[0], 1);
				if ($arr[1] == "1" or $arr[1] == "9") {
					$count++;
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}

		## 判斷順子是否有1,9
		if (!empty($sequence)) {
			$scount = count($sequence);
			for ($i = 0; $i < $scount; $i++) {
				if (($a = strpos($sequence[$i], "11")) === false && ($a = strpos($sequence[$i], "19")) === false && ($a = strpos($sequence[$i], "21")) === false && ($a = strpos($sequence[$i], "29")) === false && ($a = strpos($sequence[$i], "31")) === false && ($a = strpos($sequence[$i], "39")) === false) {
				} else {
					$count++;
				}
			}
		}

		## 判斷對子是否有1,9
		if (!empty($triplet)) {
			$tcount = count($triplet);
			for ($i = 0; $i < $tcount; $i++) {
				$arr = str_split($triplet[$i], 2);
				$b[$i] = $arr[0];
				if ($arr[0] < 40) {
					if ($arr[0] == "11" or $arr[0] == "19" or $arr[0] == "21" or $arr[0] == "29" or $arr[0] == "31" or $arr[0] == "39") {
						$count++;
					}
				} else {
					return 0;
				}
			}
			if ($tcount >= 3) {
				if ($b[2] - $b[0] == 2 && $b[1] - $b[0] == 1) {
					$count = $count + 2;
				}
			}
		}

		if ($count == 5) {
			return 3;
		} else {
			return 0;
		}
	}

	## 凑一色 3番
	private function mixedOneSult($totalhand) {
		$count = 0;
		$string_a = str_split($totalhand[0]);
		$base = $string_a[0] * 10;
		for ($i = 0; $i < 14; $i++) {
			if ($totalhand[$i] - $base > 9) {
				if ($totalhand[$i] < 40) {
					return 0;
				} else {
					$count++;
				}
			}
		}
		if ($count > 0) {
			return 3;
		} else {
			return 0;
		}

	}

	## 三相逢 3番
	private function threeMeet($sequence, $triplet) {
		$count = 1;
		$scount = count($sequence);
		for ($i = 0; $i < $scount; $i++) {
			for ($j = 1; $j < $scount; $j++) {
				if ($count == 3) {
					return 3;
				}
				$a = $sequence[$j] - $sequence[$i];
				if ($a == 202020 or $a == 101010) {
					$count++;
				}
			}
		}
		$count = 1;
		$tcount = count($triplet);
		for ($i = 0; $i < $tcount; $i++) {
			for ($j = 1; $j < $tcount; $j++) {
				if ($count == 3) {
					return 3;
				}
				$a = $triplet[$j] - $triplet[$i];
				if ($a == 202020 or $a == 101010) {
					$count++;
				}
			}
		}
		return 0;
	}

	## 五門齊 5番
	private function fiveDoors($totalhand) {
		$count = array();
		for ($i = 0; $i < count($totalhand); $i++) {
			$arr = str_split($totalhand[$i], 1);
			if ($arr[0] < 4) {
				$a = in_array($arr[0], $count);
				if ($a != true) {
					array_push($count, $arr[0]);
				}
			} else {
				## 判斷是風牌或三元牌
				if ($totalhand[$i] < 45) {
					## 是風牌統一用41
					$a = in_array("41", $count);
					if ($a != true) {
						array_push($count, 41);
					}
				} else {
					## 是三元牌統一用45
					$a = in_array("45", $count);
					if ($a != true) {
						array_push($count, 45);
					}
				}
			}

		}

		if (count($count) == 5) {
			return 5;
		} else {
			return 0;
		}
	}

	## 小三元 5番
	private function smallThreeDragons($totalhand, $eyes) {
		$red = $this->red($totalhand);
		$green = $this->green($totalhand);
		$white = $this->white($totalhand);
		## 中中中 發發發 白白
		if ($red == 1 && $green == 1 && $eyes[0] == "47" && $eyes[0] == $eyes[1]) {
			return 5;
			## 中中中 白白白 發發
		} elseif ($red == 1 && $white == 1 && $eyes[0] == "46" && $eyes[0] == $eyes[1]) {
			return 5;
			## 白白白 發發發 紅紅
		} elseif ($white == 1 && $green == 1 && $eyes[0] == "45" && $eyes[0] == $eyes[1]) {
			return 5;
		} else {
			return 0;
		}
	}

	## 七將 7番
	private function sevenPairs($hand, $right) {
		if (empty($right)) {
			$count = 0;
			$pairs = array_count_values($hand);
			foreach ($pairs as $key => $value) {
				if ($value == 2) {
					$count++;
				}
			}
			if ($count == 7) {
				return 7;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	## 清一色 10番
	private function pureOneSuit($totalhand) {
		$string_a = str_split($totalhand[0]);
		$base = $string_a[0] * 10;
		for ($i = 0; $i < 14; $i++) {
			if ($totalhand[$i] - $base > 9) {
				return 0;
			}
		}
		return 10;
	}

	## 三數 10番
	private function threeNumbers($sequence, $totalhand) {
		if (!empty($sequence)) {
			return 0;
		}

		$count = array();
		for ($i = 0; $i < count($totalhand); $i++) {
			if (count($count) > 3) {
				return 0;
			}

			## 判斷字牌
			if ($totalhand[$i] < 40) {
				## 不是字牌,取尾數,若無重覆,寫入$count陣列
				$arr = str_split($totalhand[$i], 1);
				$a = in_array($arr[1], $count);
				if ($a != true) {
					array_push($count, $arr[1]);
				}
			} else {
				## 判斷風牌,統一用41寫入陣列
				if ($totalhand[$i] < 45) {
					$a = in_array("41", $count);
					if ($a != true) {
						array_push($count, 41);
					}
				} else {
					## 三元牌，統一用45寫入陣列
					$a = in_array("45", $count);
					if ($a != true) {
						array_push($count, 45);
					}
				}
			}
		}

		if (count($count) == 3) {
			return 10;
		} else {
			return 0;
		}
	}

	## 四暗刻 10番
	private function fourBlackTriplet($blackTripletCount) {
		if ($blackTripletCount == 4) {
			return 10;
		} else {
			return 0;
		}
	}

	## 三杠 10番
	private function threeKong($kong, $blackKong) {
		$a = (count($kong) / 4) + (count($blackKong) / 4);
		if ($a == 3) {
			return 10;
		} else {
			return 0;
		}
	}

	## 雙龍抱 10番
	private function doubleCoupleSequence($highCount) {
		if ($highCount == 2) {
			return 10;
		} else {
			return 0;
		}
	}

	## 全帶 10番
	private function allSameNumber($sequence, $triplet, $eyes) {

		## 取將的尾數
		$arr = str_split($eyes[0], 1);
		if ($arr[0] == 4) {
			return 0;
		} else {
			$a = $arr[1];
		}

		## 比對每個順子是否有此數
		foreach ($sequence as $key => $value) {
			$arr = str_split($value, 1);
			if ($arr[1] != $a && $arr[3] != $a && $arr[5] != $a) {
				return 0;
			}
		}

		## 比對每個對子是否有此數
		foreach ($triplet as $key => $value) {
			$arr = str_split($value, 1);
			if ($arr[0] < 4) {
				if ($arr[1] != $a && $arr[3] != $a && $arr[5] != $a) {
					return 0;
				}
			} else {
				return 0;
			}
		}

		return 10;
	}

	## 大三元 10番
	private function bigThreeDragons($totalhand) {
		$red = $this->red($totalhand);
		$green = $this->green($totalhand);
		$white = $this->white($totalhand);
		if ($red == 1 && $green == 1 && $white == 1) {
			return 10;
		} else {
			return 0;
		}
	}

	## 地胡 10番
	private function blessingOfEarth($banker, $circle) {
		if ($banker == 0 && $circle == 1) {
			return 10;
		} else {
			return 0;
		}
	}

	## 全么 10番
	private function allOneNine($totalhand) {
		sort($totalhand);
		for ($i = 0; $i < count($totalhand); $i++) {
			if ($totalhand[$i] < 40) {
				if ($totalhand[$i] != 19 && $totalhand[$i] != 29 && $totalhand[$i] != 39 && $totalhand[$i] != 11 && $totalhand[$i] != 21 && $totalhand[$i] != 31) {
					return 0;
				}
			}
		}
		return 10;
	}

	## 天胡 20番
	private function blessingOfHeaven($banker, $circle) {
		if ($banker == 1 && $circle == 1) {
			return 20;
		} else {
			return 0;
		}
	}

	## 全將碰 20番
	private function all258Triplet($triplet, $sequence, $eyes) {
		if (!empty($sequence)) {
			return 0;
		}

		foreach ($triplet as $key => $value) {
			$arr = str_split($value, 1);
			if ($arr[0] < 4) {
				if ($arr[1] != 2 && $arr[3] != 2 && $arr[5] != 2 && $arr[1] != 5 && $arr[3] != 5 && $arr[5] != 5 && $arr[1] != 8 && $arr[3] != 8 && $arr[5] != 8) {
					return 0;
				}
			} else {
				return 0;
			}
		}

		$arr = str_split($eyes[0], 1);
		if ($arr[0] < 4) {
			if ($arr[1] != 2 && $arr[1] != 5 && $arr[1] != 8) {
				return 0;
			}
		} else {
			return 0;
		}

		return 20;
	}

	## 小四喜 20番
	private function smallFourWinds($triplet, $eyes) {
		$count = 0;
		if ($eyes[0] != 41 && $eyes[0] != 42 && $eyes[0] != 43 && $eyes[0] != 44) {
			return 0;
		} else {
			$count++;
		}

		foreach ($triplet as $key => $value) {
			$arr = str_split($value, 2);
			if ($arr[0] == 41 or $arr[0] == 42 or $arr[0] == 43 or $arr[0] == 44) {
				$count++;
			}
		}
		if ($count == 4) {
			return 20;
		} else {
			return 0;
		}

	}

	## 兩數 20番
	private function twoNumbers($sequence, $totalhand) {
		if (!empty($sequence)) {
			return 0;
		}

		$count = array();
		for ($i = 0; $i < count($totalhand); $i++) {
			if (count($count) > 2) {
				return 0;
			}

			## 判斷字牌
			if ($totalhand[$i] < 40) {
				## 不是字牌,取尾數,若無重覆,寫入$count陣列
				$arr = str_split($totalhand[$i], 1);
				$a = in_array($arr[1], $count);
				if ($a != true) {
					array_push($count, $arr[1]);
				}
			} else {
				## 判斷風牌,統一用41寫入陣列
				if ($totalhand[$i] < 45) {
					$a = in_array("41", $count);
					if ($a != true) {
						array_push($count, 41);
					}
				} else {
					## 三元牌，統一用45寫入陣列
					$a = in_array("45", $count);
					if ($a != true) {
						array_push($count, 45);
					}
				}
			}
		}

		if (count($count) == 2) {
			return 20;
		} else {
			return 0;
		}
	}

	## 四連刻 40番
	private function fourTripletInARow($triplet) {
		if (count($triplet) < 4) {
			return 0;
		}

		if ($triplet[0] < 40 && $triplet[1] < 40 && $triplet[2] < 40 or $triplet[3] < 40) {
			if ($triplet[1] - $triplet[0] == 10101 && $triplet[2] - $triplet[0] == 20202 && $triplet[3] - $triplet[0] == 30303) {
				return 40;
			} else {
				return 0;
			}
		}
	}

	## 十三么 40番
	private function thirteenOne($hand, $right) {
		if (!empty($right)) {
			return 0;
		}

		$result = array_values(array_unique($hand));
		$result = implode("", $result);
		$findme = '11192129313941424344454647';
		$pos = strpos($result, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 40;
		}
	}

	## 清老頭 40番
	private function pureOldMan($totalhand, $sequence) {
		if (!empty($sequence)) {
			return 0;
		}

		foreach ($totalhand as $key => $value) {
			if ($value != 11 && $value != 19 && $value != 21 && $value != 29 && $value != 31 && $value != 39) {
				return 0;
			}
		}
		return 40;
	}

	## 字一色 40番
	private function pureWord($totalhand) {
		foreach ($totalhand as $key => $value) {
			if ($value < 40) {
				return 0;
			} else {
				return 40;
			}
		}
	}

	## 四杠 40番
	private function fourKong($kong, $blackKong) {
		$a = (count($kong) / 4) + (count($blackKong) / 4);
		if ($a == 4) {
			return 40;
		} else {
			return 0;
		}
	}

	## 九莲宝灯  40番
	private function nineLotusLights($oldhand, $new) {
		$result = implode("", $oldhand);
		$findme = '11111112131415161718191919';
		$pos = strpos($result, $findme);
		if ($pos === false) {

		} else {
			if ($new - 10 < 10) {
				return 40;
			}
		}

		$findme = '21212122232425262728292929';
		$pos = strpos($result, $findme);
		if ($pos === false) {

		} else {
			if (($new - 20 < 10) && ($new - 20) > 0) {
				return 40;
			}
		}

		$findme = '31313132333435363738393939';
		$pos = strpos($result, $findme);
		if ($pos === false) {

		} else {
			if (($new - 30) < 10 && ($new - 30) > 0) {
				return 40;
			}
		}
		return 0;
	}

	## 大四喜 40番
	private function bigFourWinds($totalhand) {
		sort($totalhand);
		$mystring = implode("", $totalhand);
		$findme = '414141424242434343444444';
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return 0;
		} else {
			return 40;
		}
	}

	## 風牌轉換
	public function change($wind) {
		switch ($wind) {
		case '東風':
			return "414141";
			break;
		case '南風':
			return "434343";
			break;
		case '西風':
			return '424242';
			break;
		case '北風':
			return '444444';
			break;
		}
	}
}