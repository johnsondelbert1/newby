<?php
require_once("includes/session.php");
require_once("includes/functions.php");
?>
<?php
//Check to make sure user is set in URL
if(isset($_GET['user'])){
	$user = urldecode($_GET['user']);

	$query="SELECT * FROM `users`
			WHERE `username`='{$user}'";
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$found_user = mysqli_num_rows($result);
}else{
	$found_user = 0;
}
if($found_user == 1){
	//Get user data
	$user=mysqli_fetch_array($result);

	//Data not visible array
	if($user['data_public_not_visible']!=''){
		$not_visible = unserialize($user['data_public_not_visible']);
	}else{
		$not_visible = array();
	}

	//Not visible presets
	$nv="[Not Visible]";
	$none="--";

	if(isset($_GET['action']) && $_GET['action']=="ban"){
		if(check_permission("Users","ban_users")){
			$query="UPDATE `users` SET `banned` = 1
					WHERE `username`='{$user['username']}'";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			if($result){
				$success = "User Banned.";
			}else{
				$error = "An error has occurred. Contact Administrator";
			}
		}else{
			$error="You do not have sufficient permissions to ban this user.";
		}
	}

	if(isset($_GET['action']) && $_GET['action']=="unban"){
		if(check_permission("Users","ban_users")){
			$query="UPDATE `users` SET `banned` = 0
					WHERE `username`='{$user['username']}'";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			if($result){
				$success = "User Unbanned.";
			}else{
				$error = "An error has occurred. Contact Administrator";
			}
		}else{
			$error="You do not have sufficient permissions to ban this user.";
		}
	}
?>
<?php
$pgsettings = array(
	"title" => $user['username']."'s Profile",
	"pageselection" => false,
	"nav" => true,
	"banner" => 0,
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php"); ?>
    <script type="text/javascript">
	function confirm_ban(){
		var ans = confirm("Are you sure you want to ban this account?");
		if (ans==true){
			window.location = "<?php echo urlencode($user['username']); ?>&action=ban";
		}
	}
	function confirm_unban(){
		var ans = confirm("Are you sure you want to unban this account?");
		if (ans==true){
			window.location = "<?php echo urlencode($user['username']); ?>&action=unban";
		}
	}
	</script>

	<h1><?php echo $user['username']."'s Profile"; ?></h1>

    <?php if(check_permission("Users","ban_users")){?>
	<div style="margin-left:auto; margin-right:auto; text-align:center;">
	<?php if($user['banned']==0){ ?>
		<a href="#" onclick="confirm_ban()">[Ban this account]</a>
	<?php }else{ ?>
		<a href="#" onclick="confirm_unban()">[Unban this account]</a>
	<?php } ?>
	</div>
	<br/>
    <?php }?>
<table border="0" cellspacing="5" cellpadding="0" style="border-radius:5px; padding:5px; text-align:center;">
  <tr>
    <td>Date Joined:</td>
    <td><strong><?php echo date("m/d/Y" ,strtotime($user['created'])); ?></strong></td>
  </tr>
  <tr>
    <td>Email:</td>
    <td><strong><?php if(!in_array('email', $not_visible)||check_permission("Users","view_private_data")){echo $user['email'];}else{echo $nv;}?></strong></td>
  </tr>
  <tr>
    <td>Rank:</td>
    <td>
    	<strong>
    	<?php
		$query="SELECT * FROM `ranks`
				WHERE `id`='{$user['rank']}'";
		$result=mysqli_query($connection, $query);
		$rank_name = mysqli_fetch_array($result);
    	echo $rank_name['name'];
    	?>
    	</strong>
    </td>
  </tr>
	<?php
	//Get custom field list
	$query="SELECT * FROM `custom_field_users_properties`";
	$result=mysqli_query( $connection, $query);
    if(mysqli_num_rows($result)!=0){
    	while ($field = mysqli_fetch_array($result, MYSQLI_ASSOC)) {?>
    	<tr>
		<td><?php echo $field['name']; ?>:</td>
    	<?php
    		//Get user data under the custom field
    		$query="SELECT * FROM `users_custom_fields` WHERE `uid` = ".$user['id'];
			$cust_field_result=mysqli_query( $connection, $query);
			$user_field = mysqli_fetch_array($cust_field_result, MYSQLI_ASSOC);?>
			<td><strong><?php if(!in_array($field['id'], $not_visible)||check_permission("Users","view_private_data")){echo $user_field[$field['name']];}else{echo $nv;}?></strong></td></tr>
			<?php
    	}
    }
	?>
  <tr>
    <td>Last Logged In:</td>
    <td><strong><?php if(!in_array('last_logged_in', $not_visible)||check_permission("Users","view_private_data")){if($user['last_logged_in']!="0000-00-00 00:00:00"){echo date("D, m/d/Y h:i A" ,strtotime($user['last_logged_in']));}else{echo "N/A";}}else{echo $nv;} ?></strong></td>
  </tr>
<!--   <tr>
  	<td>
    	<a href="compose_mail.php?user=<?php echo urlencode($user['username'])?>"><img src="images/mail.png" alt"Message <?php echo $user['username']; ?>"/> (Message <?php echo $user['username'];?>)</a>
    </td>
  </tr> -->
  <tr>
    <td colspan="2" align="center">
    <h3>Forum Posts</h3>
    <?php
		$query="SELECT * FROM `forum_posts` WHERE `poster`='{$user['username']}' ORDER BY `date` DESC LIMIT 0,5";
		$messagequery=mysqli_query($connection, $query);
		confirm_query($messagequery);
		if(mysqli_num_rows($messagequery)!=0){?>
        
        <div id="CollapsiblePanel1" class="CollapsiblePanel">
          <div class="CollapsiblePanelTab" tabindex="0">Recent Forum Posts</div>
          <div class="CollapsiblePanelContent">
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">

        <?php 
		while($forummessage=mysqli_fetch_array($messagequery)){
			$query="SELECT * FROM `forum_threads`
					WHERE id={$forummessage['threadid']}";
			$threadquery=mysqli_query($connection, $query);
			confirm_query($threadquery);
			$thread=mysqli_fetch_array($threadquery);
			$query="SELECT * FROM `forums`
					WHERE `id`={$forummessage['forumid']}";
			$forumquery=mysqli_query($connection, $query);
			confirm_query($forumquery);
			$forum=mysqli_fetch_array($forumquery);
			?>
            <tr>
                <td align="center">Thread: <?php echo "<a href=\"".$GLOBALS['HOST']."/view_thread.php?thread=".$thread['id']."&&forum=".$forum['id']."\">".$thread['name']."</a>"; ?><br />Forum: <?php echo "<a href=\"".$GLOBALS['HOST']."/view_forum.php?forum=".$forum['id']."\">".$forum['name']."</a>"; ?></td>
            </tr>
            <tr>
                <td>
                    <table width="100%" cellspacing="1" cellpadding="5" class="<?php if($user['rank']==0){echo "forumsuser";}elseif($user['rank']==1){echo "forumsadmin";}elseif($user['rank']==2||$user['rank']==3){echo "forumsmod";}elseif($user['rank']==4){echo "forumsvip";}?>">
                      <tr>
                        <td colspan="2" class="forummessage" height="20">Date Posted: <?php echo date("D, d/m/Y h:i A" ,strtotime($forummessage['date']));?> <?php if($forummessage['lasteditdate']!="0000-00-00 00:00:00"){echo ", Last Edited: ".date("D, d/m/Y h:i A" ,strtotime($forummessage['lasteditdate']));} ?></td>
                      </tr>
                      <tr>
                        <td colspan="2" class="forummessage"><?php echo $forummessage['message']; ?></td>
                      </tr>
                    </table>
                </td>
            </tr>
		<?php }?>
        	</table>
        <?php
		}else{
			echo "[No posts have been made]";
		}
	?></td>
  </tr>
</table>
<?php
}else{?>
	<?php
	$pgsettings = array(
		"title" => "profile Not Found",
		"pageselection" => false,
		"nav" => true,
		"banner" => 0,
		"use_google_analytics" => 1,
	);
	require_once("includes/begin_html.php"); ?>
	<h1>This user does not exist.</h1>
<?php
}
?>
<?php
include("includes/end_html.php");
?>