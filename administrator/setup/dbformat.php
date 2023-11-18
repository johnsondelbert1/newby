<?php
require_once('begin.php');
require_once('../../includes/globals.php');

try{
	$connection = mysqli_connect($dbconnection->server,  $dbconnection->username,  $dbconnection->password, $dbconnection->name);
	
	if($dbconnection->firstrun == "false"){
		$_SESSION=array();
		
		if(isset($_COOKIE[session_name()])){
			unset($_SESSION['username']);
			unset($_SESSION['password']);
		}
		
		session_destroy();
		header("Location: ../index.php");
	}
	
}catch (ErrorException $e){
	header("Location: dbconnect.php");
}

if(isset($_GET['install'])&&$_GET['install'] == 'true'){
	require_once("sql_parse.php");
	
	$dbms_schema = 'illuminate.sql';
	
	$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) or die('problem ');
	$sql_query = remove_remarks($sql_query);
	$sql_query = split_sql_file($sql_query, ';');
	
	$i=1;
	foreach($sql_query as $sql){
		mysqli_query($connection, $sql) or die('error in query');
	}
	header("Location: sitesetup.php");
}

$previnstall = false;
$blankinstall = false;
$otherinstall = false;

if(mysqli_num_rows(mysqli_query($connection, "SHOW TABLES LIKE 'site_info'"))==1) {
	$version = mysqli_query($connection, "SELECT * FROM `site_info` WHERE `id` = 1");
	$data = mysqli_fetch_array($version);
	$previnstall = true;
	//future version recognition
}else{
	$blankinstall = true;
	/*$tables = mysqli_query($connection, "SELECT COUNT(*) FROM information_schema.TABLES
		WHERE TABLE_SCHEMA='{$dbconnection->name}'");
	if(mysqli_num_rows($tables)=0){
		$blankinstall = true;
	}else{
		$otherinstall = true;
	}*/
}

$result = mysqli_query($connection, "SHOW TABLES LIKE 'site_info'");
if(mysqli_num_rows($result)!=0){

}else{


}
$pgtitle = 'Step 2 of 3: Database Installation';
require_once('begin_html.php');
?>
<?php
if($blankinstall == true){?>
	<h2>Database connection works, Install Illuminate? (NOTE: must have CREATE TABLES permission to continue)</h2>
    <a href="dbformat.php?install=true">Install!</a>
<?php }elseif($previnstall == true){
	if($site_version!=$data['version']){?>
		<h2>Different version of Illuminate detected. Website Version: <?php echo $site_version; ?>; DB Version: <?php echo $data['version']; ?></h2>
    	<a href="dbformat.php?install=true">Install!</a>
   <?php }else{ ?>
		<h2>Same version of Illuminate detected. Version: <?php echo $data['version']; ?></h2>
        <a href="sitesetup.php">Continue</a>
   <?php
   }
}elseif($otherinstall == true){?>
	<h2>Warning: Other data detected in the database.</h2>
    <a href="dbformat.php?install=true">Install!</a>
   <?php
}
?>
</body>
</html>