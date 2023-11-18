<?php
require_once("../includes/functions.php");
confirm_logged_in();

?>
<?php 

if(isset($_POST['submit'])){
	$name=mysql_prep($_POST['name']);
	if($name!=''){
		$query="UPDATE `staff` SET 
			`last_edited` = '{$date}', `editor` = {$_SESSION['user_id']}, `name` = '{$name}', `order` = '{$_POST['order']}', `role` = '{$_POST['role']}', `bio` = '{$_POST['bio']}' 
			WHERE `id` = {$_GET['id']}";
		$result=mysqli_query($connection, $query);
		if(confirm_query($result)){
			$success = "Staff member has been updated!";
		}	
	}else{
		$error='Name cannot be blank';
	}
}

if(isset($_POST['upload'])){
	$dir = "../images/staff/".$_GET['id'];
	foreach (scandir($dir) as $item) {
		if ($item == '.' || $item == '..' || $item == 'Thumbs.db') continue;
		unlink($dir.DIRECTORY_SEPARATOR.$item);
	}
	
	$message = upload($_FILES, "../images/staff/".$_GET['id']."/", 2097152, array('.jpeg','.jpg','.gif','.png'));

	foreach (scandir($dir) as $item) {
		if ($item == '.' || $item == '..' || $item == 'Thumbs.db') continue;
		$file_ext = substr($item, strripos($item, '.'));
		rename($dir.DIRECTORY_SEPARATOR.$item,$dir.DIRECTORY_SEPARATOR."profile".$file_ext);
	}
}

$query="SELECT * FROM  `staff` WHERE `id` =  {$_GET['id']}";
$result=mysqli_query($connection, $query);
$staff=mysqli_fetch_array($result);


?>
<?php
	$pgsettings = array(
		"title" => "Editing Staff Member: ".$staff['name'],
		"icon" => "icon-profile"
	);
	require_once("includes/begin_cpanel.php");
?>
<form method="post">
<table cellpadding="5" id="sticker">
  <tr>
    <td width="110px"><input name="submit" type="submit" value="Update Staff Member" class="btn green"/></td>
    <td width="110px"><a class="btn red" href="staff-list.php">Cancel</a></td>
  <td></td>
  </tr>
</table>
<table width="100%" border="0" style="margin-right:auto; margin-left:auto;">
<tr>
<td width="50%">
<h1>Staff Info</h1>
Name: <input name="name" type="text" value="<?php echo $staff['name']; ?>" maxlength="128" /><br>
Order: <input name="order" type="text" value="<?php echo $staff['order']; ?>" maxlength="2" /><br>
Role: <input name="role" type="text" value="<?php echo $staff['role']; ?>" maxlength="128" /><br>
Bio: <textarea name="bio" rows="15" cols="100"><?php echo $staff['bio']; ?></textarea><br><br>
</form>
</td>
</tr>
</table>
<h1>Upload Profile Picture</h1>
<table border="0" width="100%" border="0" style="margin-right:auto; margin-left:auto;">
  <tr>
    <td></td>
        </tr>
        <tr>
    <td>
    <table>
  <tr>
    <td width="50%">
        <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="file" /><br>
        *Recommended image size is 300 pixels high by 300 pixels wide. Max filesize 2MB.
        <input name="upload" type="submit" value="Upload Image" />
        </form>
	</td>
  </tr>
</table>
</td>
  </tr>
</table>
<?php
	require_once("includes/end_cpanel.php");
?>