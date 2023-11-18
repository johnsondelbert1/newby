<?php
require_once("includes/functions.php");
?>
<?php
if(isset($_POST['submit'])){
	if($_POST['email']!=''){
		$query="SELECT username, email FROM `users` WHERE `email`='{$_POST['email']}'";		
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		if(mysqli_num_rows($result)==1){
			$user= mysqli_fetch_array($result);
			$auth_code=randstring();
			
			try{
				$query="UPDATE `users` SET `chng_pass_authcode`='{$auth_code}', `chng_pass_authcode_date`='{$date}' WHERE `email`='{$_POST['email']}'";		
				$result=mysqli_query($connection, $query);
				confirm_query($result);
				
				//send email with password reset link
				$to = $user['email'];
				$email_subject = "Forgotten Password";
				$email_message = 'Password change request for user "'.$user['username'].'"<br />';
				$email_message .= 'Change Password here: <a href="'.$GLOBALS['HOST'].'/password_reset?auth='.urlencode($auth_code).'">'.$GLOBALS['HOST'].'/password_reset?auth='.urlencode($auth_code).'</a><br />';
				$email_message .= 'This link is good for 48 hours.';
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				if($GLOBALS['site_info']['contact_email']!=""){
					$headers .= "From: ".$GLOBALS['site_info']['contact_email'].PHP_EOL;
				}else{
					$headers .= "From: ".$GLOBALS['site_info']['name'].PHP_EOL;
				}
				mail ( $to , $email_subject , $email_message , $headers );
				
				$success="An email has been sent containing a link to reset your password.";
			
			}catch (Exception $e){
				$error="Sorry, an error has occured.".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
			}
		}else{
			$error="No users have this email.";
		}
	}else{
		$error="Email field blank.";
	}
}
?>
<?php
$pgsettings = array(
	"title" => "Forgotten Password",
	"pageselection" => false,
	"nav" => true,
	"banner" => 0,
);
require_once("includes/begin_html.php");
?>
<h1>Forgotten Password Request</h1>
<form method="post">
    <table width="20%" border="0" cellspacing="5" cellpadding="0">
      <tr>
        <td>Email:</td>
        <td><input type="text" name="email" class="text" <?php if(isset($_POST['email'])){echo 'value="'.$_POST['email'].'"';} ?> /></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><input type="submit" class="btn green" name="submit" value="Submit" class="submit"/><br /><br />
        <a href="login.php">Back to login page</a></td>
      </tr>
    </table>
</form>
<?php
	require_once("includes/end_html.php");
?>