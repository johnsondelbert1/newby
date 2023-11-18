<?php
require_once("includes/session.php");
require_once("includes/functions.php");

?>
<?php
	if((isset($_GET['action'])&&$_GET['action'] = 'delforum')&&isset($_GET['forumid'])){
			if(check_permission("Forum","add_delete_forum")){
				if($_GET['action']=="delforum"){
					$query="SELECT `name`, `description` FROM `forums` WHERE `id`={$_GET['forumid']}";
					$result=mysqli_query( $connection, $query);
					if(mysqli_num_rows($result)!=0){
						$query="DELETE FROM `forums` WHERE `id` = {$_GET['forumid']}";
						$result=mysqli_query($connection, $query);
						confirm_query($result);
						$query="DELETE FROM `forum_threads` WHERE `forumid` = {$_GET['forumid']}";
						$result=mysqli_query($connection, $query);
						confirm_query($result);
						$query="DELETE FROM `forum_posts` WHERE `forumid` = {$_GET['forumid']}";
						$result=mysqli_query($connection, $query);
						confirm_query($result);
						$success="Forum deleted!";
					}else{
						$error="Forum does not exist!";
					}
				}
			}else{
				$error="You do not have permission to perform this action!";
			}
	}else{
		if(!isset($_GET['action'])){
			$message='fdgfdg';
		}
	}
?>
<?php
$query="SELECT * FROM `pages` WHERE `type` = 'Forum'";
$result_page_prop=mysqli_query( $connection, $query);
$page_properties = mysqli_fetch_array($result_page_prop);
?>

<script type="text/javascript">
$(document).ready(function () {
	$(".btn-click-action").click(function(){
		$("#del_button").attr("href", "<?php echo $GLOBALS['forum_page'];?>?action=delforum&forumid="+$(this).attr('name'));
	});
});
</script>

<h1>Welcome to the Forums!</h1>
  <?php
	if(check_permission("Forum","add_delete_forum")){?>
        <a href="<?php echo $GLOBALS['HOST']; ?>/administrator/list-forums" class="btn green">Edit Forums</a><br/><br/>
	<?php }
  ?>
<table class="forum" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <th class="forumtitle" colspan="2">Forum</th>
    <th class="forumtitle">Last thread posted</th>
  </tr>
  <?php
	
    $query="SELECT * 
		FROM  `forums` 
		ORDER BY  `id` ASC";
			
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	if(mysqli_num_rows($result)!=0){
		while($forum=mysqli_fetch_array($result)){
			if(canView(unserialize($forum['visible']))){
				$query="SELECT id, forumid FROM  `forum_threads` 
					WHERE forumid={$forum['id']}";	
				$threadquery=mysqli_query($connection, $query);
				confirm_query($threadquery);
				$threadcount=mysqli_num_rows($threadquery);
				$query="SELECT id, date FROM `forum_posts` 
					WHERE forumid={$forum['id']}
					ORDER BY `date` ASC";
				$messagequery=mysqli_query($connection, $query);
				confirm_query($messagequery);
				$messagedate=mysqli_fetch_array($messagequery);
				$messagecount=mysqli_num_rows($messagequery);?>
                <tr>
                	<td><a href="../view_forum.php?forum=<?php echo urlencode($forum['id']);?>"><?php echo $forum['name'];?></a><br /><?php echo $forum['description'];?></td>
                    <td><b><?php echo $threadcount; ?></b> Topics<br />
                    <b><?php echo $messagecount; ?></b> Replies</td>
                    <td><?php if($threadcount != 0){echo date("m/d/Y h:i A" ,strtotime($messagedate['date']));}else{echo "N/A";} ?></td>
                </tr>
				<?php
			}
		}
	}else{?>
		<tr><td colspan="5" align="center">No forums found!</td></tr>
	<?php 
	}
  ?>
</table>
