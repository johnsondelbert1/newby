<?php
require_once("includes/functions.php");
?>
<?php
if(isset($_POST['submit'])){
	$query="SELECT username, email FROM users WHERE email='{$_POST['email']}'";		
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	if(mysqli_num_rows($result)==1){
		$user= mysqli_fetch_array($result);
		$newpass=randstring();
		$hashednewpass=sha1($newpass);
		
		try{
			$query="UPDATE `users` SET `hashed_pass`='{$hashednewpass}' WHERE `email`='{$_POST['email']}'";		
			$result=mysqli_query($connection, $query);
			confirm_query($result);
	
			$to = $user['email'];
			$email_subject = "Forgotten Password";
			$email_message = "Username: ".$user['username']." New Password: ".$newpass;
			if($site_info['contact_email']!=""){
				$headers = "From: ".$site_info['contact_email'].PHP_EOL;
			}else{
				$headers = "From: ".$site_info['name'].PHP_EOL;
			}
			
			mail ( $to , $email_subject , $email_message , $headers );
			
			$success="Password change success; an email has been sent containing your username and password to the email of this account.";
		
		}catch (Exception $e){
			$error="Sorry, an error has occured.".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
		}
	}else{
		$error="No users have this email.";
	}
}
?>
<?php
$pgsettings = array(
	"title" => "Forgotten Password",
	"pageselection" => false,
	"nav" => true,
	"banner" => 1,
);
require_once("includes/begin_html.php");
?>
<form action="forgot_password.php" method="post">
    <table width="20%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Email:</td>
        <td><input type="text" name="email" class="text" /></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><input type="submit" name="submit" value="Submit" class="submit"/><br /><br />
        <a href="login.php">Back to login page</a></td>
      </tr>
    </table>
</form>
<?php
	require_once("includes/end_html.php");
?>