<?php

session_name("setup");
session_start();

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
}else{
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	
	$b = $doc->createElement( "account" );
	
		$username = $doc->createElement( "username" );
		$username->appendChild(
			$doc->createTextNode( "admin" )
		);
		$b->appendChild( $username );
		
		$password = $doc->createElement( "password" );
		$password->appendChild(
			$doc->createTextNode( "4e53020eb5df7531d2c0b65ef7ce02bc11ab4fbf" )
		);
		$b->appendChild( $password );
	
	$doc->appendChild( $b );
	
	$doc->save(dirname(__FILE__).'/../../includes/backupaccount.xml');
	
	$account = simplexml_load_file(dirname(__FILE__).'/../../includes/backupaccount.xml');
}

if(file_exists(dirname(__FILE__).'/../../includes/database.xml')){
	$dbconnection = simplexml_load_file(dirname(__FILE__).'/../../includes/database.xml');
}else{
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	
	$b = $doc->createElement( "database" );
	
		$firstrun = $doc->createElement( "firstrun" );
		$firstrun->appendChild(
			$doc->createTextNode( "true" )
		);
		$b->appendChild( $firstrun );
		
		$server = $doc->createElement( "server" );
		$server->appendChild(
			$doc->createTextNode( "" ) 
		);
		$b->appendChild( $server );
		
		$username = $doc->createElement( "username" );
		$username->appendChild(
			$doc->createTextNode( "" )
		);
		$b->appendChild( $username );
		
		$password = $doc->createElement( "password" );
		$password->appendChild(
			$doc->createTextNode( "" )
		);
		$b->appendChild( $password );
		
		$name = $doc->createElement( "name" );
		$name->appendChild(
			$doc->createTextNode( "" )
		);
		$b->appendChild( $name );
	
	$doc->appendChild( $b );
	
	$doc->save(dirname(__FILE__).'/../../includes/database.xml');
	
	$dbconnection = simplexml_load_file(dirname(__FILE__).'/../../includes/database.xml');
}

$logout=false;

if(isset($_SESSION['username'])){
	if(isset($_GET['action'])&&$_GET['action']=="logout"){
		$_SESSION=array();
	
		if(isset($_COOKIE[session_name()])){
			unset($_SESSION['username']);
			unset($_SESSION['password']);
		}
		
		session_destroy();
		$logout=true;
	}else{
		header("Location: dbconnect.php");
	}
}


try{
	mysqli_connect($dbconnection->server,  $dbconnection->username,  $dbconnection->password, $dbconnection->name);
	
	if($dbconnection->server==""||$dbconnection->username==""||$dbconnection->name==""){
		if($dbconnection->firstrun=="false"){
			$message="<h3 class=\"error\">There is a problem connecting to the website database. Login using the backup account to continue.</h3>";
		}
	}else{
		if($dbconnection->firstrun=="true"){
			//header("Location: sitesetup.php");
		}else{
			$_SESSION=array();
			
			if(isset($_COOKIE[session_name()])){
				unset($_SESSION['username']);
				unset($_SESSION['password']);
			}
			
			session_destroy();			
			header("Location: ../index.php");
		}
	}
}catch (ErrorException $e){
	$message="<h3 class=\"error\">There is a problem connecting to the website database. Login using the backup account here. \" ".$e->getMessage()." \"</h3>";
}


if(isset($_POST['submit'])){
	$user=$_POST['username'];
	$pass=$_POST['password'];
	$hashed_pass=sha1($pass);
	
	if(($user==$account->username)&&($hashed_pass==$account->password)){	
			
		$_SESSION['username']=$user;
		$_SESSION['password']=$hashed_pass;
		
		header("Location: dbconnect.php");
	}else{
		$message="<h3 class=\"error\">Incorrect Login.</h3>";
	}
}
if(isset($_GET['error'])&&$_GET['error']=="1"){
	//$message="<h3 class=\"error\">There is a problem connecting to the website database. Edit connection information here. Or is your SQL server down?</h2>";
}



$pgtitle = 'Login to Setup';
require_once('begin_html.php');
?>
<?php if($logout==true){echo "<h3 class=\"success\">You are now logged out</h3>";} ?>
<form method="post" action="setuplogin.php">
	Username <input type="text" name="username"  /><br>
	Password <input type="password" name="password" /><br>
    <input type="submit" name="submit" value="Login to Setup" />
</form>
</body>
</html>