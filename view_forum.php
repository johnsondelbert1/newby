<?php
require_once("includes/session.php");
require_once("includes/functions.php");

 if(isset($_GET['action']) && isset($_GET['thread']) && isset($_GET['forum'])){
	 if(check_permission("Forum","pin_unpin_thread")){
		 if($_GET['action']=="pin"){
			 $query="UPDATE `forum_threads` SET `pinned` = '1' WHERE `id` ={$_GET['thread']}";
			 $result=mysqli_query($connection, $query);
			 confirm_query($result);
			 $success="Thread Pinned!";
		 }elseif($_GET['action']=="unpin"){
			 $query="UPDATE `forum_threads` SET `pinned` = '0' WHERE `id` ={$_GET['thread']}";
			 $result=mysqli_query($connection, $query);
			 confirm_query($result);
			 $success="Thread Unpinned!";
		 }
	 }else{
		 $error="You do not have permission to perform this action!";
	 }
	 if(check_permission("Forum","lock_unlock_thread")){
	 	if($_GET['action']=="lock"){
			 $query="UPDATE `forum_threads` SET `locked` = '1' WHERE `id` ={$_GET['thread']}";
			 $result=mysqli_query($connection, $query);
			 confirm_query($result);
			 $success="Thread Locked!";
		 }elseif($_GET['action']=="unlock"){
			 $query="UPDATE `forum_threads` SET `locked` = '0' WHERE `id` ={$_GET['thread']}";
			 $result=mysqli_query($connection, $query);
			 confirm_query($result);
			 $success="Thread Unlocked!";
		 }
	 }else{
		 $error="You do not have permission to perform this action!";
	 }
	 if(check_permission("Forum","delete_thread")){
	 	if($_GET['action']=="del"){
			 $query="DELETE FROM `forum_threads` WHERE `id` = {$_GET['thread']}";
			 $result=mysqli_query($connection, $query);
			 confirm_query($result);
			 $query="DELETE FROM `forum_posts` WHERE `threadid` = {$_GET['thread']}";
			 $result=mysqli_query($connection, $query);
			 confirm_query($result);
			 $success="Thread Deleted!";
		 }
	 }else{
		 $error="You do not have permission to perform this action!";
	 }
 }
 
    $query="SELECT * 
		FROM  `forums` 
		WHERE `id`={$_GET['forum']}";
			
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$forum=mysqli_fetch_array($result);
	
	if(!canView(unserialize($forum['visible']))){
		redirect_to("page/".$GLOBALS['forum_page']."?error=".urlencode('You do not have access to that!'));
	}

	$query="SELECT * 
		FROM  `forum_threads` 
		WHERE `forumid`={$_GET['forum']}";
			
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$num_threads = mysqli_num_rows($result);
	
	$num_pages = ceil($num_threads/10);
	
	if(isset($_GET['page'])&&$_GET['page']>=1){
		$current_page = $_GET['page'];
	}else{
		$current_page = 1;
	}
	
	$query="SELECT * FROM `pages` WHERE `type` = 'Forum'";
	$result_page_prop=mysqli_query( $connection, $query);
	$page_properties = mysqli_fetch_array($result_page_prop);
	
	$pgsettings = array(
		"title" => $forum['name']." Forum",
		"pageselection" => "forum",
		"nav" => $page_properties['horiz_menu_visible'],
		"banner" => $page_properties['banner'],
		"slider" => $page_properties['slider'],
		"use_google_analytics" => 1,
	);
require_once("includes/begin_html.php");
?>
<h1><?php echo $forum['name']; ?></h1>
<div class="row">
<?php if(check_permission("Forum","add_thread")){?>
	<br /><br /><a class="green btn " href="new_topic.php?forum=<?php echo urlencode($forum['id']); ?>&amp;&amp;action=newthread">New</a>
<?php }
	
	?>
	</div>
	<div class="row">
	<nav>
    <div class=" forummain nav-wrapper">
      <div class="col s12">
        <a class="breadcrumb" href="<?php echo $GLOBALS['HOST'].'/page/'.$GLOBALS['forum_page']; ?>"><?php echo $GLOBALS['site_info']['name']; ?> Forums</a>
        <a class="breadcrumb" href="view_forum.php?forum=<?php echo $forum['id']; ?>"><?php echo $forum['name']; ?></a>
      </div>
    </div>
  </nav>
    

<?php echo_page($num_pages, $current_page, "view_forum.php?forum=".$_GET['forum']); ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="forum">
  <tr>
  	<th class="forumtitle"></th>
    <th class="forumtitle" width="40%">Thread</th>
    <th class="forumtitle" width="25%">
    <th class="forumtitle" width="15%"></th>
    <th class="forumtitle" width="28%">Last Post</th>
    <th class="forumtitle"></th>
    
	<?php if(check_permission(array("Forum;pin_unpin_thread","Forum;lock_unlock_thread","Forum;delete_thread",))){?>
		<td class="forumtitle" colspan="3">Thread Controls</td>
	<?php } ?>
  </tr>
  <?php
	if($num_threads!=0){
	$query="SELECT * 
		FROM  `forum_threads` 
		WHERE `forumid`={$_GET['forum']} 
		ORDER BY  `pinned` DESC,`lastpostdate` DESC LIMIT ";
	$query.=(($current_page * 10)-10).",".($current_page * 10);
	$threadquery=mysqli_query($connection, $query);
	confirm_query($threadquery);
	
	while($thread=mysqli_fetch_array($threadquery)){
		
			$query="SELECT threadid 
				FROM  `forum_posts` 
				WHERE threadid={$thread['id']}";
			$messagequery=mysqli_query($connection, $query);
			confirm_query($messagequery);
			$messagecount=mysqli_num_rows($messagequery);
			
			$query="SELECT poster 
				FROM  `forum_posts` 
				WHERE threadid={$thread['id']}
				ORDER BY date DESC";
			$messagequery=mysqli_query($connection, $query);
			confirm_query($messagequery);
			$messageposter=mysqli_fetch_array($messagequery);
			
			$query="SELECT `rank` FROM users
			WHERE username='{$thread['creator']}'";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			$mcusercreator=mysqli_fetch_array($result);
			
			$query="SELECT `rank` FROM users
			WHERE username='{$messageposter['poster']}'";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			$mcuserlastposter=mysqli_fetch_array($result);
			
			?>
			<tr height="40" align="center">
				<td><?php if($thread['pinned']==1){?><i class="material-icons" style="margin-right: 5px;">&#xE88F;</i><?php } ?></td>
				<td><a href="view_thread.php?thread=<?php echo urlencode($thread['id']);?>"><strong><?php echo $thread['name']; ?></strong></a></td>
				<td><a href="<?php echo $GLOBALS['HOST'].'/profile/'.urlencode(get_user($thread['creator'])['username']); ?>"><?php echo get_user($thread['creator'])['username']; ?></a><br /><?php echo date("m/d/Y h:i A" ,strtotime($thread['datestarted'])); ?></td>
				<td><?php echo $messagecount."</b> Replies"; ?><br /><b><?php echo $thread['views']."</b> Views"; ?></td>
				<td>By: <a href="<?php echo $GLOBALS['HOST'].'/profile/'.urlencode(get_user($messageposter['poster'])['username']); ?>"><?php echo get_user($messageposter['poster'])['username']; ?></a><br /><?php echo date("m/d/Y h:i A" ,strtotime($thread['lastpostdate'])); ?></td>
				<?php if(check_permission("Forum","pin_unpin_thread")){?>
					<?php if($thread['pinned']==0){?>
                        <td><a class="btn green" href="view_forum.php?action=pin&thread=<?php echo urlencode($thread['id']);?>&forum=<?php echo urlencode($forum['id']);?>">Pin</a></td>
                    <?php }else{?>
                        <td><a class="btn green" href="view_forum.php?action=unpin&thread=<?php echo urlencode($thread['id']);?>&forum=<?php echo urlencode($forum['id']);?>">Unpin</a></td>
                    <?php } ?>
                <?php } ?>
                <?php if(check_permission("Forum","delete_thread")){?>
                    <td><a class="btn red" href="view_forum.php?action=del&thread=<?php echo urlencode($thread['id']);?>&forum=<?php echo urlencode($forum['id']);?>">Delete</a></td>
                <?php } ?>
                <?php if(check_permission("Forum","lock_unlock_thread")){?>
                    <?php if($thread['locked']==0){?>
                        <td><a class="btn blue" href="view_forum.php?action=lock&thread=<?php echo urlencode($thread['id']);?>&forum=<?php echo urlencode($forum['id']);?>">Lock</a></td>
                    <?php }else{?>
                        <td><a class="btn blue" href="view_forum.php?action=unlock&thread=<?php echo urlencode($thread['id'])?>&forum=<?php echo urlencode($forum['id']);?>">Unlock</a></td>
                    <?php 
                    }
                } ?>
                <td><?php if($thread['locked']==1){echo '<i class="small material-icons">&#xE899;</i>';} ?></td>
			 </tr>
			 
			<?php
	}
	?>
  </tr>
  <?php
      }else{?>
      <tr>
          <td colspan="9">
            No threads found!
          </td>
      </tr>
<?php } ?>
</table>
</div>
<div class="row">
<?php echo_page($num_pages, $current_page, "view_forum.php?forum=".$_GET['forum']); ?>
</div>

<?php
	require_once("includes/end_html.php");
?>