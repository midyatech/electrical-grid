<?php
if (session_id() == '')
	session_start();

if(isset($_SESSION["language"])){
	include_once realpath(__DIR__ . '/..').'/include/checksession.php';
    $LANGUAGE = $_SESSION["language"];
}


require_once realpath(__DIR__ . '/..') . '/class/Dictionary.php';

if(isset($_REQUEST["keyword"])){
    $dictionary = new Dictionary($LANGUAGE);
    echo $dictionary->GetDictionaryWord($_REQUEST["keyword"]);
}
?>
