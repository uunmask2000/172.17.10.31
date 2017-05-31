<?php
//$starttime = microtime_float();

/*
$file = '../../log/taskServer.log';
$fp = fopen($file, "r");
$num = 10;
$chunk = 4096; //4K的块
$fs = sprintf("%u", filesize($file));
$readData = '';
$max = (intval($fs) == PHP_INT_MAX) ? PHP_INT_MAX : $fs;

for ($len = 0; $len < $max; $len += $chunk) {

$seekSize = ($max - $len > $chunk) ? $chunk : $max - $len;
fseek($fp, ($len + $seekSize) * -1, SEEK_END);
$readData = fread($fp, $seekSize) . $readData;

if (substr_count($readData, "\n") >= $num + 1) {

$ns = substr_count($readData, "\n") - $num + 2;
preg_match('/(.*?\n){' . $ns . '}/', $readData, $match);
$data = $match[1];
break;
}
}
fclose($fp);
//echo $data . "<br/>";
if (isset($data)) {
//echo "string";
echo $data . "<br/>";
} else {
//echo "string1";
}
 */
//$endtime = microtime_float();
//echo $endtime - $starttime;
//function microtime_float() {     list($usec, $sec) = explode(" ", microtime());     return ((float) $usec + (float) $sec); }

$file_path = '../../log/taskServer.log';
$file = file($file_path);
$count = count($file);
$summt = $count - 50;

foreach ($file as $key => $value) {
	//echo $key . $value . "<br>";
	if ($count < 10) {
		echo $value . "<br>";
	}if ($count > 10) {
		if ($key > $summt) {
			echo $value . "<br>";
		}
	}

}
