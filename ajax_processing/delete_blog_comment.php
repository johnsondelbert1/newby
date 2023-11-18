<?php
require_once("../includes/session.php");
require_once("../includes/functions.php");
if(isset($_POST['id'])){
	//Check if comment exists and get comment data
	$query="SELECT * FROM `blog_comments` WHERE `bc_id` = {$_POST['id']}";
	$commentResult=mysqli_query( $connection, $query);
	$blogComment = mysqli_fetch_array($commentResult);
	if(mysqli_num_rows($commentResult)>=1){
		//Check delete permissions against permission system or if original poster's comment
		if(check_permission("Blog","delete_any_comment")||$blogComment['poster_id']==$user_info['id']){
			$query="DELETE FROM `blog_comments` WHERE `bc_id` = {$_POST['id']}";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
		}else{
			echo 'invalPerms';
		}
	}else{
		echo 'notExist';
	}
}
?>