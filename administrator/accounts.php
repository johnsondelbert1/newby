<?php
require_once("../includes/functions.php");
if(!check_permission(array("Users;add_users","Users;delete_users",))){
	redirect_to("index.php");
}
$query="SELECT * FROM `ranks`";
$rankresult=mysqli_query($connection, $query);

if (isset($_POST['submit'])){
	
	//Sanitize default fields
	$user=strip_tags(trim(mysql_prep($_POST['username'])));
	$email=strip_tags(trim(mysql_prep($_POST['email'])));
	$post_rank = $_POST['rank'];
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
						'{$user}', '{$GLOBALS['date']}', '{$email}', '{$hashed_pass}', {$post_rank}, 'Administrator', 0)";
			$def_result=mysqli_query($connection, $query);
			$new_user = mysqli_insert_id($connection);
			
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

			if($def_result && $cust_result){
				
				$success="Account was created successfully for ".$_POST['username']."!";
/*
				$to = $email;
				$subject = "Account activation code";
				$message = "Activate your account: http://minecraft.secondgenerationdesign.com/activate.php?code=".$verifcode." You must be logged in already on the same browser for the activation to take place.";
				$headers = "From:StormForge@minecraft.secondgenerationdesign.com".PHP_EOL;
				
				mail ( $to , $subject , $message , $headers );
				
				$to = "johnsondelbert1@gmail.com";
				$subject = "User has Registered";
				$message = $user." has registered on the website. http://stormforgemc.com/user_list.php";
				$headers = "From:StormForge@minecraft.secondgenerationdesign.com".PHP_EOL;
				
				mail ( $to , $subject , $message , $headers );
				*/
			}else{
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

if(isset($_POST['deluser'])){
	if(check_permission("Users","delete_users")){
		$message="";
		if(!empty($_POST['accounts'])){
			foreach($_POST['accounts'] as $account){
				if(del_acc($account)==true){
					$success="Accounts were deleted!";
				}else{
					$error="Cannot delete only webmaster account left.";
				}
			}
		}else{
			$error="No accounts selected.";
		}
	}else{
		$error="You do not have permission to delete users!";
	}
}

if(isset($_POST['upd_ranks'])){
	if(check_permission("Users","add_users")){
		$message="";
		if(!empty($_POST['acc_ranks'])){
			foreach($_POST['acc_ranks'] as $account => $rank){
				$query="SELECT * FROM `users` WHERE `id` = {$account}";
				$result=mysqli_query( $connection, $query);
				$deletable_user = mysqli_fetch_array($result);
				if($deletable_user['deletable']==true){
					$query="UPDATE `users` SET
								`rank` = {$rank} WHERE `id` = {$account}";
					$result=mysqli_query( $connection, $query);
					confirm_query($result);
					$success="Account ranks updated!";
				}else{
					if($deletable_user['rank']!=$rank){
						$error = 'Account "'.$deletable_user['username'].'" in the list cannot be changed!';
					}
				}
			}
		}else{
			$error="No accounts selected.";
		}
	}else{
		$error="You do not have permission to delete users!";
	}
}
?>
<?php
	$pgsettings = array(
		"title" => "Accounts",
		"icon" => "icon-user3"
	);
	require_once("includes/begin_cpanel.php");
?>
<script type="text/javascript">
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'accountall';
        $('input[id="'+$checkall+'"]').change(function() {
			var $all_check_status = $('input[id="'+$checkall+'"]').is(':checked');
             $("#form label").each(function(index, element) {
				var $target = $(this).attr("for");
				if($all_check_status!=$('input[id="'+$target+'"]').is(':checked')){
                	$(this).trigger('click');
				}
            });
        });  
     });
</script>
<?php if(check_permission("Users","add_users")){?>
    <h1>Add New Account</h1>
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
  <tr>
  	<td style="text-align:right;">Rank</td>
  	<td>
    	<select name="rank">
            <?php
            while($rank=mysqli_fetch_array($rankresult)){?>
                <option value="<?php echo $rank['id'];?>" <?php if($GLOBALS['site_info']['default_rank'] == $rank['id']){echo ' selected';} ?>><?php echo $rank['name']?></option>
            <?php
            }?>
        </select>
  	</td>
  	<td></td>
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
<!--     <form method="post" action="accounts.php" class="col s12">
      <div class="row">
        <div class="input-field col s6"><input name="newuser" placeholder="Username" type="text" value="<?php if(isset($user)){echo $user;} ?>" maxlength="50" /></div>
        <div class="input-field col s6"><input name="newemail" placeholder="email@example.com" type="email" value="<?php if(isset($email)){echo $email;} ?>"maxlength="200" /></div>
        </div>
        <div class="row">
        <div class="input-field col s6"><input name="newpass" placeholder="Password" type="password" maxlength="50" /></div>
        <div class="input-field col s6"><input name="newconfpass" placeholder="Confirm" type="password" maxlength="50" /></div>
        </div>
           <div class="row">
        <div class="input-field col s6">
        	<select name="rank">
                <?php
                while($rank=mysqli_fetch_array($rankresult)){?>
                        <option value="<?php echo $rank['id'];?>"><?php echo $rank['name']?></option>
                <?php
                }?>
            </select>
        </div>
<div class="input-field col s6"><input name="submit" type="submit" value="Create Account" class="green btn"/></div></div> -->
    </form><br />
<?php } ?>
<h1>Account List</h1>
<form method="post" action="accounts.php">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="form" style="text-align:center;">
      <tr>
        <th>User Name</th>
        <th>Email</th>
		<th>Activated</th>
		<th>Approved</th>
        <th>Created</th>
        <th>Rank</th>
        <th>Last Logged In</th>
        <?php if(check_permission("Users","delete_users")){?>
        	<th width="10%"><input type="checkbox" id="accountall" /><label name="accountall" for="accountall"></label></th>
        <?php } ?>
      </tr>
        <?php
            $query="SELECT * FROM `users` ORDER BY `id` ASC";
            $result=mysqli_query( $connection, $query);
            confirm_query($result);
			
			$ranks = array();
			$query="SELECT * FROM `ranks`";
			$rankresult=mysqli_query($connection, $query);
			while($rank=mysqli_fetch_array($rankresult)){
				 array_push($ranks, $rank);
			}
			
            while($user=mysqli_fetch_array($result)){

				?>
              <tr>
                <td><a href="../profile/<?php echo urlencode($user['username']);?>" target="_blank"><?php echo $user['username'];?></a></td>
                <td><?php echo $user['email'];?></td>
                <td><?php echo ($user['activated_email'])? 'Yes' : 'No';?></td>
                <td><?php echo ($user['approved_admin'])? 'Yes' : 'No';?></td>
                <td><?php if($user['created']!="0000-00-00 00:00:00"){echo date("m/d/Y h:i A" ,strtotime($user['created']));}else{echo "N/A";} ?></td>
                <td>
                	<select name="acc_ranks[<?php echo $user['id'];?>]">
                    	<?php
						foreach($ranks as $rank){?>
							<option value="<?php echo $rank['id']; ?>"<?php if($user['rank'] == $rank['id']){echo " selected";} ?>><?php echo $rank['name']; ?></option>
                        <?php
						}
						?>
                    </select>
                </td>
                <td><?php if($user['last_logged_in']!="0000-00-00 00:00:00"){echo date("D, m/d/Y h:i A" ,strtotime($user['last_logged_in']));}else{echo "N/A";} ?></td>
                <?php if(check_permission("Users","delete_users")){?>
                	<td><input type="checkbox" name="accounts[]"<?php if($user['deletable']==0){echo ' disabled';} ?> id="<?php echo $user['id']; ?>" value="<?php echo $user['id']; ?>" /><label for="<?php echo $user['id']; ?>"></td>
                <?php } ?>
              </tr>
            <?php } ?>
      <tr>
        <th colspan="5"></th>
        <th><input class="blue btn" type="submit" name="upd_ranks" value="Change Ranks" /></th>
        <th></th>
        <?php if(check_permission("Users","delete_users")){?>
        	<th><input class="red btn" type="submit" name="deluser" value="Delete Accounts" /></th>
        <?php } ?>
      </tr>
    </table>
</form>
<?php require_once("includes/end_cpanel.php"); ?>