<?php
require_once('begin.php');

$previousdb = $dbconnection;

if((isset($_POST['dbserver'])&&$_POST['dbserver']=="")||(isset($_POST['dbuser'])&&$_POST['dbuser']=="")||(isset($_POST['dbname'])&&$_POST['dbname']=="")){
	$message="<h3 class=\"error\">Server, User, or DB Name is blank.</h3>";
}else{
	if(isset($_POST['submit'])){
		try{
			mysqli_connect($_POST['dbserver'],  $_POST['dbuser'],  $_POST['dbpass'], $_POST['dbname']);
			
			$doc = new DOMDocument();
			$doc->formatOutput = true;
			
			$b = $doc->createElement( "database" );
			
				$firstrun = $doc->createElement( "firstrun" );
				if($previousdb->firstrun == "false"){
					$firstrun->appendChild(
						$doc->createTextNode( "false" )
					);
				}else{
					$firstrun->appendChild(
						$doc->createTextNode( "true" )
					);
				}
				$b->appendChild( $firstrun );
				
				$server = $doc->createElement( "server" );
				$server->appendChild(
					$doc->createTextNode( $_POST['dbserver'] ) 
				);
				$b->appendChild( $server );
				
				$username = $doc->createElement( "username" );
				$username->appendChild(
					$doc->createTextNode( $_POST['dbuser'] )
				);
				$b->appendChild( $username );
				
				$password = $doc->createElement( "password" );
				$password->appendChild(
					$doc->createTextNode( $_POST['dbpass'] )
				);
				$b->appendChild( $password );
				
				$name = $doc->createElement( "name" );
				$name->appendChild(
					$doc->createTextNode( $_POST['dbname'] )
				);
				$b->appendChild( $name );
			
			$doc->appendChild( $b );
			
			$doc->save(dirname(__FILE__).'/../../includes/database.xml');
			
			header("Location: dbformat.php");
			
		}catch (ErrorException $e){
			$message="<h3 class=\"error\">This information didn't work. Edit connection information here. \" ".$e->getMessage()." \"</h3>";
		}
	}
	if(isset($_GET['error'])&&$_GET['error']=="1"){
		
		//$message="<h3 class=\"error\">There is a problem connecting to the website database. Edit connection information here. Or is your SQL server down?</h2>";
	}
}

$dbconnect = simplexml_load_file(dirname(__FILE__).'/../../includes/database.xml');

if(!isset($_POST['submit'])){
	
	if($dbconnect->server==""||$dbconnect->username==""||$dbconnect->name==""){
		if($dbconnect->firstrun!="true"){
			$message="<h3 class=\"error\">There is a problem connecting to the website database. Edit connection information here.</h3>";
		}
		
	}else{
		try{
			$connection = mysqli_connect($dbconnect->server,  $dbconnect->username,  $dbconnect->password, $dbconnect->name);
			
		if($dbconnect->firstrun=="true"){
			header("Location: dbformat.php");
		}else{
			$_SESSION=array();
			
			if(isset($_COOKIE[session_name()])){
				unset($_SESSION['username']);
				unset($_SESSION['password']);
			}
			
			session_destroy();
			header("Location: index.php");
		}
		}catch (ErrorException $e){
			$message="<h3 class=\"error\">There is a problem connecting to the website database. Edit connection information here. Or is your SQL server down? \" ".$e->getMessage()." \"</h3>";
		}
	}
}
?>
<?php
$pgtitle = 'Step 1 of 3: Connect to Database';
require_once('begin_html.php');
?>
<a href="setuplogin.php?action=logout">Logout</a><br>
<form method="post" action="dbconnect.php">
	Server <input type="text" name="dbserver" value="<?php echo $dbconnect->server; ?>" /><br>
	User <input type="text" name="dbuser" value="<?php echo $dbconnect->username; ?>" /><br>
	Pass <input type="text" name="dbpass" value="<?php echo $dbconnect->password; ?>" /><br>
	DB Name <input type="text" name="dbname" value="<?php echo $dbconnect->name; ?>" /><br>
    <input type="submit" name="submit" value="Submit DB" />
</form>
</body>
</html>