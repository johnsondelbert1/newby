<?php

if(isset($_POST['submit'])){

	function setLoginCookie($found_user){
		global $connection;
		$_SESSION['user_id']=$found_user['id'];
		$_SESSION['username']=$found_user['username'];
		if(isset($_POST['remember'])){$remember=1;}else{$remember=0;}
		if($remember==1){
			setcookie("rememberme",$found_user['username'], time()+60*60*24*30, '/');
		}
		
		session_name("login");
		session_start();
		
		$date=date("Y-m-d H:i:s");
		$query="UPDATE `users` SET 
				`last_logged_in` = '{$date}' 
				WHERE `id` = {$_SESSION['user_id']}";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		
		redirect_to($_POST['redirect_to']);
	}

	$user=trim($_POST['username']);
	$pass=$_POST['password'];

	if($user == '' || $pass == ''){
		$error = "field left blank";
	}
	
	$query="SELECT * FROM users
			WHERE `username` = '{$user}'";
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	if (mysqli_num_rows($result)==1){
		$found_user = mysqli_fetch_array($result);
		//Determine if old password hash is used, if yes, update user to new password hashing
		if($found_user['old_pass']==0){
			if(password_verify($pass, $found_user['hashed_pass'])){
				$verify_login = verify_login($found_user);
				if($verify_login[0] == 1){
					setLoginCookie($found_user);
				}else{
					$error = $verify_login[1];
				}
			}else{
				$error = "Wrong password";
			}
		}else{
			$hashed_pass=sha1($pass);
			if($hashed_pass == $found_user['hashed_pass']){
				$new_hash = password_hash($pass, PASSWORD_DEFAULT);
				$query="UPDATE `users` SET 
						`hashed_pass` = '{$new_hash}', `old_pass` = 0
						WHERE `id` = {$found_user['id']}";
				$result=mysqli_query($connection, $query);
				confirm_query($result);
				$verify_login = verify_login($found_user);
				if($verify_login[0]==1){
					setLoginCookie($found_user);
				}else{
					$error = $verify_login[1];
				}
			}else{
				$error = "Wrong password";
			}
		}
	}else{
		$error = "User does not exist";
	}
}else{
	if(isset($_GET['logout']) && $_GET['logout']==1){
		$success = "You have successfully logged out!";
	}
	//redirect_to("../login.php");
}
?>