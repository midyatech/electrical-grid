<?php
if (!class_exists('Log')) {
	class Log
	{
		public function __construct() {

		}

		public function Add($type, $source, $description, $messageCode)
		{

			$file = realpath(__DIR__ . '/..').'/log/log.txt';
			//echo "<br>".$file."<br>";
			$line = date('Y-m-d H:i:s')."\t".$type."\t". $source. "\t". $description."\t".$messageCode."\n";
			file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

		}


		public static function AddRequestLog($result, $query, $notes)
		{

			$file = realpath(__DIR__ . '/..').'/log/request_log.txt';
			//echo "<br>".$file."<br>";
			$line = date('Y-m-d H:i:s')."\t".$result.": \t[". $query. "]\t". $notes."\n\n";
			file_put_contents($file, $line, FILE_APPEND);

		}
	}
}
?>