<?php
require_once("includes/session.php");
require_once("includes/functions.php");

if(isset($_GET['action']) && isset($_GET['user'])){
	if($_SESSION['rank']==1){
		if($_GET['action']=="makeadmin"){
			$query="UPDATE `users` SET 
					`rank` = 1 
					WHERE `username` = '{$_GET['user']}'";
			$result=mysql_query($query,$connection);
			confirm_query($result);
			$message=$_GET['user']." is now an admin.";
		}elseif($_GET['action']=="removeadmin"){
			$query="UPDATE `users` SET 
					`rank` = 0 
					WHERE `username` = '{$_GET['user']}'";
			$result=mysql_query($query,$connection);
			confirm_query($result);
			$message=$_GET['user']." is not an admin anymore.";
		}
	}
}elseif(isset($_GET['msg'])=="deleted" && isset($_GET['user'])){
	$message= "<p>".$_GET['user']."'s account has been banned.</p>";
}
?>
<?php require_once("includes/begin_html.php"); ?>
<?php
	nav($pgselection=false);
?>
<h1>User List</h1><br />
<?php if(isset($message)){echo $message;} ?>
<br />
<?php if($_SESSION['rank']==1||($_SESSION['rank']==2)){?>
	<a href="banned_list.php">Banned List</a>
<?php } ?>
<br />
<form action="user_list.php" method="post">
<table width="70%" border="0" align="center">
  <tr>
    <td>
    <input name="query" type="text" class="text" placeholder="Search for users" maxlength="50" value="<?php if(isset($_POST['search'])){echo $_POST['query'];} ?>"/>
    <select name="type" class="text">
      <option value="user"<?php if(isset($_POST['type'])&&$_POST['type']=="user"){echo " selected";} ?>>Username</option>
      <option value="mcuser"<?php if(isset($_POST['type'])&&$_POST['type']=="mcuser"){echo " selected";} ?>>Minecraft Username</option>
    </select> 
    <input name="search" type="submit" value="Search" /></td>
  </tr>
</table>
</form>
<br />

<?php if(isset($_POST['search'])){?><br /><a href="user_list.php">Back to all users -></a><br /><?php } ?>

<br />
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="userlist">
  <tr  style="background-color:#BBB; height:50px; font-size:18px;">
    <th width="55%" colspan="2">User</th>
    <th width="35%">Minecraft Username</th>
  </tr>
  	<?php
	if(isset($_POST['search'])){
		if($_POST['type']=="user"){
			$query="SELECT * FROM `users` WHERE INSTR(`username`, '{$_POST['query']}') > 0 AND `acct_status` = 'verified'";
		}elseif($_POST['type']=="mcuser"){
			$query="SELECT * FROM `users` WHERE INSTR(`minecraft_username`, '{$_POST['query']}') > 0 AND `acct_status` = 'verified'";
		}
		$result=mysql_query($query, $connection);
		confirm_query($result);
		if(mysql_num_rows($result)!=0){
			while($user=mysql_fetch_array($result)){?>
              <tr align="center">
                <td>
                	<img src="images/minecraft.php?u=<?php echo $user['minecraft_username']; ?>&s=32" style="border:5px solid; border-radius:5px; <?php echo echorank($user['rank']) ?>" alt="Minecraft Skin" />    
                </td>
                <td><a href="profile.php?user=<?php echo urlencode($user['username']);?>"><?php echo $user['username'];?></a></td>
                <td>
                    <?php echo $user['minecraft_username'];?>
                </td>
              </tr>
		<?php }
		}else{
	?>
    	<tr align="center"><td colspan="4">No users matched your search: "<?php echo $_POST['query'];?>"</td>
    <?php }
	}else{
		$query="SELECT `username`, `minecraft_username`, `rank` FROM `users` WHERE `acct_status` = 'verified' ORDER BY `id` ASC";
		$result=mysql_query($query, $connection);
		confirm_query($result);
		while($user=mysql_fetch_array($result)){?>
              <tr class="forum-topic" align="center">
                <td>
                	<img src="images/minecraft.php?u=<?php echo $user['minecraft_username']; ?>&s=48" style="border:5px solid; border-radius:5px; <?php echo echorank($user['rank']) ?>" alt="Minecraft Skin" />    
                </td>
                <td><a href="profile.php?user=<?php echo urlencode($user['username']);?>"><?php echo $user['username'];?></a></td>
                <td>
                    <?php echo $user['minecraft_username'];?>
                </td>
              </tr>
		<?php }
	}
	?>
</table>


<?php
include("includes/footer.php");
?>