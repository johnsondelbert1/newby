<?php
require_once("includes/connection.php");
require_once("includes/functions.php");
if(logged_in()){
	redirect_to($GLOBALS['HOST']."/index.php");
}
if($GLOBALS['site_info']['user_creation'] == 'admin'){
	redirect_to($GLOBALS['HOST']."/index.php");
}
?>
<?php
if (isset($_POST['submit'])){
	
	//Sanitize default fields
	$user=strip_tags(trim(mysql_prep($_POST['username'])));
	$email=strip_tags(trim(mysql_prep($_POST['email'])));
	$pass=$_POST['pass'];
	$confirmpass=$_POST['confirmpass'];

	$hashed_pass=password_hash($pass, PASSWORD_DEFAULT);

	$err_array = array();

	//Default field validations
	if (empty($user)){
		$err_array['username'] = "Cannot be empty.";
	}else{
		if(preg_match("/[^a-zA-Z0-9-_ ]/", $user)){
			$err_array['username'] = "Username has an invalid character in it.";
		}
	}

	if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
		$err_array['email'] = "Invalid Email.";
	}
	if (strlen($pass)<7){
		$err_array['pass'] = "Needs to be at least 7 characters.";
	}
	if ($pass != $confirmpass){
		$err_array['confirmpass'] = "Confirm password did not match entered password.";
	}

	//Custom Field validations
	$query="SELECT * FROM `custom_field_users_properties`";
	$result=mysqli_query( $connection, $query);
    if($num_cust_fields = mysqli_num_rows($result)!=0){
    	while ($field = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			switch($field['validate']){
				case "none":
					
					break;
				case "email":
					if(filter_var($_POST[$field['name']],FILTER_VALIDATE_EMAIL)){

						break;
					}else{
						$err_array[$field['name']] = "Invalid Email.";
					}
					break;
				case "notempty":
					if($_POST[$field['name']]!=""){
						break;
					}else{
						$err_array[$field['name']] = "Cannot be empty.";
					}
					break;
				case "numbers":
					if(is_numeric($_POST[$field['name']])){
						break;
					}else{
						$err_array[$field['name']] = "Only numbers allowed.";
					}
					break;
				case "phone":
					if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/" ,$_POST[$field['name']])){
						break;
					}else{
						$err_array[$field['name']] = "Invalid Phone.";
					}
					break;
			}
		}
	}

	if (empty($err_array)){
		$query="SELECT `id`, `username`, `email` FROM `users`
				WHERE `username`='{$user}' OR `email`='{$email}'";
		$result=mysqli_query($connection, $query);
		if (mysqli_num_rows($result)==1){
			$error="An account with this username or email already exists!";
		}else{
			//Insert default fields
			$query="INSERT INTO `users` (
						`username`, `created`, `email`, `hashed_pass`, `rank`, `created_via`, `old_pass`
					) VALUES (
						'{$user}', '{$GLOBALS['date']}', '{$email}', '{$hashed_pass}', {$GLOBALS['site_info']['default_rank']}, 'Registration', 0)";
			$def_result=mysqli_query($connection, $query);
			if($def_result){
				$success=array("Account was created successfully for ".$_POST['username']."!");
			}
			$new_user = mysqli_insert_id($connection);

			//Set and send email activation code
			if($GLOBALS['site_info']['require_email_activation']==1 && $def_result){
				$verifcode=randstring();
				$query="UPDATE `users` SET 
					`activation_code` = '{$verifcode}', `activation_code_date` = '{$GLOBALS['date']}', `activated_email` = 0
					WHERE `id` = ".$new_user;
				if($result=mysqli_query($connection, $query)){
					//Send mail
					$to = $email;
					$email_subject = "Account Activation";
					$email_message = 'Account activation request for user "'.$user.'"<br />';
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

					array_push($success,"Email sent for account activation.");
				}else{
					$error = "SQL Error has occurred. Contact Administrator.";
				}
			}
			echo $GLOBALS['site_info']['user_creation'];
			if($GLOBALS['site_info']['user_creation']=='approval' && $def_result){
				$query="UPDATE `users` SET 
					`approved_admin` = 0
					WHERE `id` = ".$new_user;
				if(mysqli_query($connection, $query)){
					array_push($success,"Administrator approval needed before login.");
				}else{
					$error = 'SQL error';
				}
				
			}
			
			//Insert custom fields
			if($num_cust_fields >= 1){
				$custom_field_names = array();
				$custom_field_post = array();
				$query="SELECT * FROM `custom_field_users_properties`";
				$cust_fields_result=mysqli_query( $connection, $query);
				while ($field = mysqli_fetch_array($cust_fields_result, MYSQLI_ASSOC)) {
					array_push($custom_field_names, '`'.$field['name'].'`');
					array_push($custom_field_post, "'".$_POST[$field['name']]."'");
				}
				$query="INSERT INTO `users_custom_fields` (`uid`, ".implode(', ', $custom_field_names).") VALUES ({$new_user}, ".implode(', ', $custom_field_post).")";
				$cust_result=mysqli_query($connection, $query);
			}else{
				//If no custom fields
				$query="INSERT INTO `users_custom_fields` (`uid`) VALUES ({$new_user})";
				$cust_result=mysqli_query($connection, $query);
			}

			if(!$def_result || !$cust_result){
				$error="Account was not created. ".mysqli_error($connection);
			}
		}
	}else{
		$error="Errors in the Registration form.";
	}
}else{
	$user="";
	$email="";
}
?>
<?php
$pgsettings = array(
	"title" => "Register",
	"pageselection" => false,
	"nav" => true,
	"banner" => 1,
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php"); ?>

<h1>Register an Account</h1>
<form method="post">
<table border="0" cellspacing="5" cellpadding="0" style="width:auto;">
  <tr>
  	<td style="text-align:right;">Username*</td>
    <td><input type="text" name="username" maxlength="50" placeholder="Username" style="width:300px;" value="<?php echo $user; ?>"/></td>
    <td><?php if(isset($err_array['username'])){echo '<span class="error-block">'.$err_array['username'].'</span>';} ?></td>
  </tr>
  <tr>
  	<td style="text-align:right;">Email*</td>
    <td><input type="email" name="email" maxlength="50" placeholder="Email" style="width:300px;" value="<?php echo $email; ?>"/></td>
    <td><?php if(isset($err_array['email'])){echo '<span class="error-block">'.$err_array['email'].'</span>';} ?></td>
  </tr>
  <?php
  //Custom User Fields
	$query="SELECT * FROM `custom_field_users_properties`";
	$result=mysqli_query( $connection, $query);

    if(mysqli_num_rows($result)!=0){
    	while ($field = mysqli_fetch_array($result, MYSQLI_ASSOC)) {?>
    <tr>
    	<td style="text-align:right;"><?php echo $field['name']; if($field['validate']=='notempty'){echo "*";}?></td>
    	<td>
    		<?php if($field['type'] == 'text'){ ?>
    		<input type="text" name="<?php echo $field['name']; ?>" <?php if($field['placeholder']!=''){echo 'placeholder="'.$field['placeholder'].'"';} ?> <?php if($field['maxchar']>0){echo 'maxlength="'.$field['maxchar'].'"';} ?> value="<?php if(isset($_POST[$field['name']])){echo $_POST[$field['name']];} ?>" style="width:300px;" />
    		<?php }elseif($field['type'] == 'textarea'){ ?>
    		<textarea name="<?php echo $field['name'] ?>" placeholder="<?php echo $field['placeholder'] ?>" <?php echo ($field['placeholder'])? '' : 'maxlength="'.$field['placeholder'].'"' ?> style="width:300px; height:100px; padding:5px;" data-autosize-on="false"><?php if(isset($_POST[$field['name']])){echo $_POST[$field['name']];} ?></textarea>
    		<?php } ?>
    	</td>
    	<td><?php if(isset($err_array[$field['name']])){echo '<span class="error-block">'.$err_array[$field['name']].'</span>';} ?></td>
    </tr>
    <?php
    	}
    }
  ?>
  <tr>
  	<td style="text-align:right;">Password*</td>
    <td><input type="password" name="pass" placeholder="Password" style="width:300px;" /></td>
    <td><?php if(isset($err_array['pass'])){echo '<span class="error-block">'.$err_array['pass'].'</span>';} ?></td>
  </tr>
  <tr>
  	<td style="text-align:right;">Confirm Password*</td>
    <td><input type="password" name="confirmpass" placeholder="Confirm Password" style="width:300px;" /></td>
    <td><?php if(isset($err_array['confirmpass'])){echo '<span class="error-block">'.$err_array['confirmpass'].'</span>';} ?></td>
  </tr>
</table> 
<input class="btn green" type= "submit" name="submit" value="Create Account" />


</form>
<?php
include("includes/end_html.php");
?>