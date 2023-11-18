<?php
if(file_exists(dirname(__FILE__).'/database.xml')){
	$xml = simplexml_load_file(dirname(__FILE__).'/database.xml');
}else{
	header("Location: setup/setuplogin.php");
}
if($xml->firstrun!="true"){
	define("DB_SERVER",$xml->server);
	define("DB_USER",$xml->username);
	define("DB_PASS",$xml->password);
	define("DB_NAME",$xml->name);
}else{
	header("Location: setup/setuplogin.php");
}
?>