<?php
require_once("includes/session.php");
require_once("includes/functions.php");

?>
<?php
	function style($admin,$username){
		if($admin==1){echo "class=\"forumsadmin";}if(logged_in()&&$username==$_SESSION['username']){echo " userspost";}echo "\"";
	}
	
	$query="SELECT * 
		FROM  `forum_threads` 
		WHERE  `id`={$_GET['thread']}";
			
	$threadquery=mysqli_query($connection, $query);
	confirm_query($threadquery);
	$thread=mysqli_fetch_array($threadquery);
	
	$query="SELECT * 
		FROM  `forums` 
		WHERE  `id`={$thread['forumid']}";
			
	$forumquery=mysqli_query($connection, $query);
	confirm_query($forumquery);
	$forum=mysqli_fetch_array($forumquery);

	if(!canView(unserialize($forum['visible']))){
		redirect_to("page/".$GLOBALS['forum_page']."?error=".urlencode('You do not have access to that!'));
	}
	
    $query="SELECT * 
		FROM  `forum_posts` 
		WHERE  `threadid`={$_GET['thread']}";
			
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$num_posts = mysqli_num_rows($result);
	
	$num_pages = ceil($num_posts/10);
	
	if(isset($_GET['page'])&&$_GET['page']>=1){
		$current_page = $_GET['page'];
	}else{
		$current_page = 1;
	}
	
	$query="SELECT * FROM `forum_posts` WHERE  `threadid`={$_GET['thread']} ORDER BY `date` ASC LIMIT ";
		$query.=(($current_page * 10)-10).",".($current_page * 10);
	$result_posts=mysqli_query($connection, $query);

	$query="UPDATE `forum_threads` SET `views` = `views` + 1 WHERE `id` ={$_GET['thread']}";
			
	$threadquery=mysqli_query($connection, $query);
	confirm_query($threadquery);
	?>
<?php
$query="SELECT * FROM `pages` WHERE `type` = 'Forum'";
$result_page_prop=mysqli_query( $connection, $query);
$page_properties = mysqli_fetch_array($result_page_prop);

$pgsettings = array(
	"title" => $thread['name'],
	"pageselection" => "forum",
	"nav" => $page_properties['horiz_menu_visible'],
	"banner" => $page_properties['banner'],
	"slider" => $page_properties['slider'],
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php");
?>
<h1><?php echo $thread['name']; ?></h1>
<?php if(check_permission("Forum","reply_to_thread")&&$thread['locked']==0){?>
	<a class="btn green" href="new_topic?forum=<?php echo urlencode($forum['id'])."&&thread=".urlencode($thread['id']); ?>&amp;action=newmessage">Reply</a><br/><br/>
<?php } ?>
<nav>
	<div class="forummain nav-wrapper">
		<div class="col s12">
			<a href="<?php echo $GLOBALS['HOST'].'/page/'.$GLOBALS['forum_page']; ?>" class="breadcrumb"><?php echo $GLOBALS['site_info']['name']; ?> Forums</a>
			<a href="view_forum?forum=<?php echo $forum['id']; ?>" class="breadcrumb"><?php echo $forum['name']; ?></a>
			<a href="view_thread.php?thread=<?php echo $thread['id']; ?>" class="breadcrumb"><?php echo $thread['name']; ?></a>
		</div>
	</div>
</nav>
  <?php
	echo_page($num_pages, $current_page, "view_thread?thread=".$_GET['thread']);
  ?>
<div class="thread">
  <?php
  	$count=($current_page * 10)-9;
	while($forummessage=mysqli_fetch_array($result_posts)){
		$user=get_user($forummessage['poster']);

		$query="SELECT * 
			FROM  `users_custom_fields` 
			WHERE `uid`={$user['id']}";
				
		$dataquery=mysqli_query($connection, $query);
		confirm_query($dataquery);
		$custom_user_data=mysqli_fetch_array($dataquery);
		
		?>
        <table width="100%" style="border: 1px solid; margin-bottom: 20px;" cellspacing="1" cellpadding="0">
          <tr>
            <td rowspan="3" style="width: 20%; height: 100%; border-right: 1px solid; vertical-align: text-top; padding: 5px; position: relative;">
            	<?php
					$query="SELECT * FROM `ranks` WHERE `id` = {$user['rank']}";
					$rank_data_result=mysqli_query( $connection, $query);
					$rank_data=mysqli_fetch_array($rank_data_result);
            	?>
            	<ul>
                	<li>
                		<a href="<?php echo $GLOBALS['HOST'].'/profile/'.urlencode($user['username']); ?>"><span style="color: <?php echo $rank_data['color']?>;"><b><?php echo $user['username']; ?></b></span></a>
                	</li>
                	<li>
                		<b><?php echo $rank_data['name']; ?></b>
                	</li>
                	<li>
                		Posts: <?php echo number_format($user['forum_post_count']); ?>
                	</li>
                <?php
					if($GLOBALS['site_info']['forum_post_custom_user_data']!=''){
						$displayed_data = unserialize($GLOBALS['site_info']['forum_post_custom_user_data']);
					}else{
						$displayed_data = array();
					}
					if(!empty($displayed_data)){
						foreach ($displayed_data as $field_id) {
							$query="SELECT `name` 
								FROM  `custom_field_users_properties` 
								WHERE `id`={$field_id}";
									
							$fieldquery=mysqli_query($connection, $query);
							confirm_query($fieldquery);
							$custom_user_field=mysqli_fetch_array($fieldquery);
							echo '<li>'.$custom_user_field['name'].': '.$custom_user_data[$custom_user_field['name']].'</li>';
						}

					}
                ?>
                </ul>
          		<div style="width: 100%; text-align: right; position:absolute; bottom: 0;">
	                <?php
	                    if(check_permission("Forum","edit_thread")&&$user['username']==$_SESSION['username']&&($thread['locked']==0)){?><a class="btn blue" style="margin-right: 10px; margin-bottom: 6px;" href="new_topic.php?msg=<?php echo $forummessage['id']; ?>&amp;&amp;forum=<?php echo $forum['id']; ?>&amp;&amp;thread=<?php echo $thread['id']; ?>&amp;&amp;action=editpost">Edit</a>
	                <?php } ?>
                </div>
             </td>
             <td style="height: 30px; border-bottom: 1px dashed; padding: 5px;">
             	<p style="float: left;">Posted: <?php echo date("m/d/Y h:i A" ,strtotime($forummessage['date']));?><?php if($forummessage['lasteditdate']!="0000-00-00 00:00:00"){echo ", Last Edit: ".date("m/d/Y h:i A" ,strtotime($forummessage['lasteditdate']));} ?></p><p style="float: right;" align="right"><b># <?php echo $count; ?></b></p>
             </td>
          </tr>
          <tr>
          	<td style="vertical-align: text-top; padding: 5px; position: relative;">
          		<?php echo $forummessage['message']; ?>
          	</td>
          </tr>
          <tr>
          	<td style="vertical-align: text-top; padding: 5px; position: relative; padding-bottom: 45px;">
          		<?php if($user['forum_signature']!=''){ ?>
          		<div style="width: 75%; margin-left: -38%; left:50%; position:absolute; top: 0; border-top: 2px solid #000000;"></div>
          		<div style="width: 90%; max-height: 150px; overflow: scroll; margin-left: auto; margin-right: auto;">
          			<?php echo $user['forum_signature']; ?>
          		</div>
          		<?php } ?>
          	</td>
          </tr>

        </table>
	<?php
	$count++;
    }
	echo_page($num_pages, $current_page, "view_thread?thread=".$_GET['thread']);
  ?>
  </div>
	<nav style="margin-top: -20px;">
		<div class="forummain nav-wrapper">
			<div class="col s12">
				<a href="<?php echo $GLOBALS['HOST'].'/page/'.$GLOBALS['forum_page']; ?>" class="breadcrumb"><?php echo $GLOBALS['site_info']['name']; ?> Forums</a>
				<a href="view_forum?forum=<?php echo $forum['id']; ?>" class="breadcrumb"><?php echo $forum['name']; ?></a>
				<a href="view_thread.php?thread=<?php echo $thread['id']; ?>" class="breadcrumb"><?php echo $thread['name']; ?></a>
			</div>
		</div>
	</nav>
<?php if(check_permission("Forum","reply_to_thread")&&$thread['locked']==0){?>
	<br/><a class="btn green" href="new_topic.php?forum=<?php echo urlencode($forum['id'])."&&thread=".urlencode($thread['id']); ?>&amp;&amp;action=newmessage">Reply</a><br><br>
<?php } ?>
<?php
	require_once("includes/end_html.php");
?>