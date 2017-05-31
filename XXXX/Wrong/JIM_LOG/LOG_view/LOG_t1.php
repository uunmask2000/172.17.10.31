<?php
//$starttime = microtime_float();

$file_path = '../../log/taskServer.log';
$file = file($file_path);
foreach ($file as $key => $value) {
	echo $value . "<br>";
}
