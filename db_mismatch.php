<?php 
require("includes/constants.php");
require("includes/globals.php");
	$connection = @mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if(DB_SERVER==""||DB_USER==""||DB_NAME==""){
		header("Location: setup/setuplogin.php");
	}
	if(!$connection){
		header("Location: setup/setuplogin.php");
	}
	$query="SELECT * FROM `site_info` WHERE `id` = 1";
	$result=mysqli_query( $connection, $query);
	$dbversion=mysqli_fetch_array($result);
	
	if(isset($dbversion['version'])){
		$compare_result = version_compare($db_compatability,  $dbversion['version']);
		
		if($compare_result == 0){
			header("Location: index.php");
		}
	}else{
		$dbversion['version'] = 'N/A';
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Database Version Mismatch</title>
<style type="text/css">
	body{
		background-color:#333;
		color:#E3E3E3;
		text-align:center;
		display:flex;
		justify-content:center;
		align-items:center;
		font-family:Arial, Helvetica, sans-serif;
	}
	div.content{
		background-color:#656565;
		border-radius:5px;
		padding:30px;
		width:500px;
		height:450px;
		margin-top:215px;
		top:50%;
		left:50%;
		-webkit-box-shadow: 3px 3px 5px 0px rgba(0,0,0,0.75);
		-moz-box-shadow: 3px 3px 5px 0px rgba(0,0,0,0.75);
		box-shadow: 3px 3px 5px 0px rgba(0,0,0,0.75);
	}
	div.content h2, div.content h3{
		color:#FFA3A4;
	}
	div.content span{
		position: absolute;
		right: 0;
		bottom: 0;
		padding: 5px;
	}
</style>
</head>

<body>
<div class="content">
    <br><img src="images/logo.png" alt="Logo" /><br>
    <h1>IlluminateCMS</h1><br>
    <h2>Error: Database Version Mismatch</h2>
    <h3>Database needs to be updated to version <?php echo $db_compatability; ?></h3>
    <h3>Current database version: <?php echo $dbversion['version']; ?></h3>
    <span>Web Ver: <?php echo $site_version; ?></span>
</div>
</body>
</html>