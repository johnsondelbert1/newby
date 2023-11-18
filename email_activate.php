<?php
require_once("includes/functions.php");
?>
<?php
if(isset($_GET['auth'])){
	$auth_code=urldecode($_GET['auth']);
	$query="SELECT * FROM `users` WHERE `activation_code`='{$auth_code}'";		
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	if(mysqli_num_rows($result)==1){
		$user = mysqli_fetch_array($result);
		$t1 = new DateTime($user['activation_code_date']);
		$t2 = new DateTime($GLOBALS['date']);
		$diff = $t2 -> diff($t1);
		$days = $diff -> days;

		if($days<2){
			$query="UPDATE `users` SET 
					`activation_code` = '', `activation_code_date` = '', `activated_email` = 1
					WHERE `id` = {$user['id']}";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			redirect_to("login.php?success=".urlencode("Account activated!"));
		}else{
			redirect_to("login.php?error=".urlencode('Authentication Code Expired.'));
		}
	}else{
		redirect_to("login.php?error=".urlencode('Invalid Authentication Code.'));
	}
}else{
	redirect_to("login.php");
}
?>