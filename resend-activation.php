<?php
require_once("includes/functions.php");
if(isset($_GET['email'])){
	$query="SELECT * FROM `users` WHERE `email` = '".urldecode($_GET['email'])."'";
	$result=mysqli_query($connection, $query);
	if(mysqli_num_rows($result)==1){
		$foundUser = mysqli_fetch_array($result);
		if($foundUser['activated_email'] == 0){
			$verifcode=randstring();
			$query="UPDATE `users` SET 
				`activation_code` = '{$verifcode}', `activation_code_date` = '{$GLOBALS['date']}', `activated_email` = 0
				WHERE `id` = ".$foundUser['id'];
			if($result=mysqli_query($connection, $query)){
				//Send mail
				$to = $email;
				$email_subject = "Account Activation";
				$email_message = 'Account activation request for user "'.$foundUser['username'].'"<br />';
				$email_message .= 'Activate account here: <a href="'.$GLOBALS['HOST'].'/email_activate?auth='.urlencode($verifcode).'">'.$GLOBALS['HOST'].'/email_activate?auth='.urlencode($verifcode).'</a><br />';
				$email_message .= 'This link is good for 48 hours.';
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				if($GLOBALS['site_info']['contact_email']!=""){
					$headers .= "From: ".$GLOBALS['site_info']['contact_email'].PHP_EOL;
				}else{
					$headers .= "From: ".$GLOBALS['site_info']['name'].PHP_EOL;
				}
				mail ( $to , $email_subject , $email_message , $headers );

				redirect_to("login?success=".urlencode("Email resent to ".$foundUser['email']." for account activation."));
			}
		}else{
			redirect_to("login?error=".urlencode("Account email already activated."));
		}
	}
}else{
	redirect_to("login");
}
?>