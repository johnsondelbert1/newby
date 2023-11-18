<?php

session_name("setup");
session_start();

if(!isset($_SESSION['username'])){
	header("Location: setuplogin.php");
}

function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
	// error was suppressed with the @-operator
	if (0 === error_reporting()) {
		return false;
	}

	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('handleError');

if(file_exists(dirname(__FILE__).'/../../includes/backupaccount.xml')){
	$account = simplexml_load_file(dirname(__FILE__).'/../../includes/backupaccount.xml');
}
if(file_exists(dirname(__FILE__).'/../../includes/database.xml')){
	$dbconnection = simplexml_load_file(dirname(__FILE__).'/../../includes/database.xml');
}