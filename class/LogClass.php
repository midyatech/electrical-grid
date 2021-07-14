<?php

class LogClass
{
	//protected $db;
	public function __construct() {
	}

	public function Add($type, $source, $description, $messageCode)
	{
		$file = realpath(__DIR__ . '/..').'/log/log.txt';
		$line = date('Y-m-d H:i:s')."\t".$type."\t". $source. "\t". $description."\t".$messageCode."\n";
		file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

	}
}

?>