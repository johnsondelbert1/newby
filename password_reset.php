<?php
require_once("includes/functions.php");
?>
<?php
if(isset($_GET['auth'])){
	$auth_code=urldecode($_GET['auth']);
	$query="SELECT * FROM `users` WHERE `chng_pass_authcode`='{$auth_code}'";		
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	if(mysqli_num_rows($result)==1){
		$user = mysqli_fetch_array($result);
		$t1 = new DateTime($user['chng_pass_authcode_date']);
		$t2 = new DateTime($date);
		$diff = $t2 -> diff($t1);
		$days = $diff -> days;

		if($days<2){
			if(isset($_POST['submit'])){
				if($_POST['resetpass']!=''){
					if($_POST['resetpass']==$_POST['confresetpass']){
						$pass_hash = password_hash($_POST['resetpass'], PASSWORD_DEFAULT);
						$query="UPDATE `users` SET 
								`hashed_pass` = '{$pass_hash}', `chng_pass_authcode` = '', `chng_pass_authcode_date` = ''
								WHERE `id` = {$user['id']}";
						$result=mysqli_query($connection, $query);
						confirm_query($result);
						if($user['old_pass']==1){
							$query="UPDATE `users` SET 
									`old_pass` = 0
									WHERE `id` = {$user['id']}";
							$result=mysqli_query($connection, $query);
							confirm_query($result);
						}
						redirect_to("login.php?success=".urlencode("Password reset!"));
					}else{
						$error='Confirm Password does not match Password.';
					}
				}else{
					$error='Password field blank.';
				}
			}
			$pgsettings = array(
				"title" => "Reset Forgotten Password",
				"pageselection" => false,
				"nav" => true,
				"banner" => 0,
			);
			require_once("includes/begin_html.php");?>
			<h1>Reset Password</h1>
			<form method="post">
				<input name="resetpass" placeholder="Password" type="password" maxlength="255" />
	        	<input name="confresetpass" placeholder="Confirm Password" type="password" maxlength="255" />
	        	<input type="submit" class="btn green" name="submit" value="Submit" class="submit"/>
        	</form>
        	<?php require_once("includes/end_html.php");?>
			<?php
		}else{
			redirect_to("login.php?error=".urlencode('Authentication Code Expired.'));
		}
	}else{
		redirect_to("login.php?error=".urlencode('Invalid Authentication Code.'));
	}
}
?>