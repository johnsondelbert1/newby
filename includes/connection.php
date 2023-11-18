<?php
require("constants.php");
require("globals.php");
	$connection = @mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if(DB_SERVER==""||DB_USER==""||DB_NAME==""){
		header("Location: setup/setuplogin.php");
	}
	if(!$connection){
		//die("Database connection failed: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		header("Location: setup/setuplogin.php");
	}
	$query="SELECT * FROM `site_info` WHERE `id` = 1";
	$result=mysqli_query( $connection, $query);
	$dbversion=mysqli_fetch_array($result);
	$compare_result = version_compare($db_compatability,  $dbversion['version']);
	$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
	if(isset($dbversion['version'])){
		if($compare_result != 0){
			header("Location: {$GLOBALS['HOST']}/db_mismatch.php");
		}
	}else{
		header("Location: {$GLOBALS['HOST']}/db_mismatch.php");
	}
	/*$db_connect=((bool)mysqli_query($connection, "USE " . constant('DB_NAME')));
	if(!$db_connect){
		//die("Database selection failed: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		header("Location : ".dirname(__FILE__)."\administrator/dbconnect.php");
	}*/
?>