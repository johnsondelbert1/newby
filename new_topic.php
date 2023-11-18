<?php
require_once("includes/session.php");
require_once("includes/functions.php");
if(!isset($_GET['action'])||!isset($_GET['forum'])){
	redirect_to($GLOBALS['HOST']."/forums?error=".urlencode("Missing argument to new_topic.php"));
}
if(!check_permission("Forum","add_thread")&&(isset($_GET['action'])&&$_GET['action']=="newthread")){
	redirect_to($GLOBALS['HOST']."/forums?error=".urlencode("You don't have permission to post a thread!"));
}elseif(!check_permission("Forum","edit_thread")&&(isset($_GET['action'])&&$_GET['action']=="editpost")){
	redirect_to($GLOBALS['HOST']."/forums?error=".urlencode("You don't have permission to edit your post!"));
}
?>
<?php
	if(isset($_POST['newthread'])||isset($_POST['newmessage'])||isset($_POST['editpost'])){
		if(isset($_POST['threadtitle'])){
			$title=strip_tags($_POST['threadtitle']);
		}
		$messagebody=strip_tags($_POST['content'], "<b><i><u><del><a><p><img><br><hr><ol><ul><li><sub><sup>");
		$forumid=$_GET['forum'];
		$creator=$_SESSION['user_id'];
		date_default_timezone_set($GLOBALS['site_info']['timezone']);
		$date=date("Y/m/d H:i:s");
			
		if(isset($_POST['newthread'])){
			if(!empty($title)||!empty($content)){
				//Insert new thread
				$query="INSERT INTO `forum_threads` (
						`forumid`, `creator`, `name`, `lastpostdate`, `datestarted` 
					) VALUES (
						{$forumid}, '{$creator}', '{$title}', '{$date}', '{$date}')";
				$insthread=mysqli_query( $connection, $query);
				confirm_query($insthread);
				//get new thread ID
					$query="SELECT * FROM `forum_threads` ORDER BY `id` DESC";
					$threadcount=mysqli_query( $connection, $query);
					confirm_query($threadcount);
					$newthreadid=mysqli_fetch_array($threadcount);
				//Insert message into the thread
 				$query="INSERT INTO `forum_posts` (
						`forumid`, `threadid`, `poster`, `date`, `message` 
					) VALUES (
						{$forumid}, {$newthreadid['id']}, '{$creator}', '{$date}', '{$messagebody}')";
				$insmessage=mysqli_query( $connection, $query); 
				confirm_query($insmessage);
				//add post count
				addpostcount($_SESSION['user_id']);
				redirect_to($GLOBALS['HOST']."/view_forum.php?forum=".urlencode($_GET['forum']));
			}else{
				$error="Required field not filled!";
			}
		}elseif(isset($_POST['editpost'])){
				$query="SELECT * FROM `forum_posts` WHERE `id`={$_GET['msg']}";
				$messagequery=mysqli_query( $connection, $query); 
				confirm_query($messagequery);
				if(mysqli_num_rows($messagequery)!=0){
					$query="UPDATE `forum_posts` SET `lasteditdate` = '{$date}', `message` = '{$messagebody}' WHERE `id` ={$_GET['msg']}";
					$updatepost=mysqli_query( $connection, $query); 
					confirm_query($updatepost);
					redirect_to($GLOBALS['HOST']."/view_thread.php?thread=".urlencode($_GET['thread'])."&&forum=".urlencode($_GET['forum']));
				}else{
					$error="This is not your post to edit!";
				}
		}elseif(isset($_POST['newmessage'])){
			if(!empty($messagebody)){
				//insert message
				$query="INSERT INTO `forum_posts` (
					`forumid`, `threadid`, `poster`, `date`, `message` 
				) VALUES (
					{$forumid}, {$_GET['thread']}, '{$creator}', '{$date}', '{$messagebody}')";
				$insmessage=mysqli_query( $connection, $query); 
				confirm_query($insmessage);
				//update last post date
				$query="UPDATE `forum_threads` SET `lastpostdate` =  '{$date}' WHERE `id`={$_GET['thread']}";
				$updatedate=mysqli_query( $connection, $query); 
				confirm_query($updatedate);
				//add post count
				addpostcount($_SESSION['user_id']);
				//redirect to posted thread
				redirect_to($GLOBALS['HOST']."/view_thread.php?thread=".urlencode($_GET['thread'])."&&forum=".urlencode($_GET['forum']));
			}else{
				$error="No message entered!";
			}
		}
	}

    $query="SELECT id, name 
		FROM  `forums` 
		WHERE `id`={$_GET['forum']}";
			
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$forum=mysqli_fetch_array($result);
	
	if(isset($_GET['action'])&&$_GET['action']=="editpost"){
		$query="SELECT * FROM `forum_posts` WHERE `id`={$_GET['msg']} AND `poster`='{$_SESSION['user_id']}'";
		$messagequery=mysqli_query( $connection, $query); 
		confirm_query($messagequery);
		if(mysqli_num_rows($messagequery)!=0){
			$messagebody=mysqli_fetch_array($messagequery);
		}else{
			redirect_to($GLOBALS['HOST']."/view_thread.php?thread=".urlencode($_GET['thread'])."&&forum=".urlencode($_GET['forum']));
		}
	}
?>

<?php if(isset($_GET['action'])&&$_GET['action']=="newthread"){
	$pgsettings = array(
		"title" => "Creating a new thread in \"".$forum['name']."\"",
		"pageselection" => "forum",
		"nav" => true,
		"banner" => 0,
		"use_google_analytics" => 1,
	);
	require_once("includes/begin_html.php");
	?>
	<h1>Creating a new thread in "<?php echo $forum['name']; ?>"</h1>
<?php }elseif((isset($_GET['action'])&&$_GET['action']=="newmessage")||(isset($_GET['action'])&&$_GET['action']=="editpost")){
    $query="SELECT id, name, locked 
		FROM  `forum_threads` 
		WHERE `id`={$_GET['thread']}";
			
	$threadquery=mysqli_query($connection, $query);
	confirm_query($threadquery);

	$thread=mysqli_fetch_array($threadquery);
	if($thread['locked']==1&&$_SESSION['rank']!=1){
		$pgsettings = array(
			"title" => "Error: Thread is locked and you do not have permission to edit this post!",
			"pageselection" => "forum",
			"nav" => true,
			"banner" => 1,
			"use_google_analytics" => 1,
		);
		$error="Thread is locked and you do not have permission to edit this post!";
		require_once("includes/begin_html.php");
		
	}
if($_GET['action']=="newmessage"){
	$pgsettings = array(
		"title" => "Creating a new post in \"".$thread['name']."\"",
		"pageselection" => "forum",
		"nav" => true,
		"banner" => 1,
	);
	require_once("includes/begin_html.php");
	?>
	<h1>Creating a new post in "<?php echo $thread['name']; ?>"</h1>
<?php }elseif($_GET['action']=="editpost"){ 
	$pgsettings = array(
		"title" => "Editing post in \"".$thread['name']."\"",
		"pageselection" => "forum",
		"nav" => true,
		"banner" => 1,
		"use_google_analytics" => 1,
	);
	require_once("includes/begin_html.php");
	?>
	<h1>Editing post in "<?php echo $thread['name']; ?>"</h1>
<?php }
	} ?>
<a href="<?php echo $GLOBALS['HOST'].'/page/'.$GLOBALS['forum_page']; ?>"><?php echo $GLOBALS['site_info']['name']; ?> Forums</a> &gt; <a href="view_forum.php?forum=<?php echo $forum['id']; ?>"><?php echo $forum['name']; ?></a><?php if((isset($_GET['action'])&&$_GET['action']=="newmessage")||(isset($_GET['action'])&&$_GET['action']=="editpost")){?> &gt; <a href="view_thread.php?thread=<?php echo $thread['id']."&&forum=".$forum['id']; ?>"><?php echo $thread['name']; ?></a><?php } ?><br /><br /><br />
<?php $actionurl="?forum=".urlencode($_GET['forum']);
		if(isset($_GET['action'])&&$_GET['action']=="newthread"){ 
			$actionurl.="&&action=newthread";
		}elseif(isset($_GET['action'])&&$_GET['action']=="newmessage"){ 
			$actionurl.="&&thread=".urlencode($_GET['thread'])."&&action=newmessage";
		}elseif(isset($_GET['action'])&&$_GET['action']=="editpost"){ 
			$actionurl.="&&msg=".urlencode($_GET['msg'])."&&thread=".urlencode($thread['id'])."&&action=editpost";
		}?>
<script type="text/javascript" src="tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	tinymce.init({
		selector: "textarea",
		theme: "modern",
		skin: 'light',
		width : '100%',
		plugins: [
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars fullscreen",
			"insertdatetime media nonbreaking save contextmenu directionality",
			"emoticons paste textcolor"
		],
		toolbar1: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		toolbar2: "print preview media | emoticons",
		image_advtab: true,
		templates: [
			{title: 'Test template 1', content: 'Test 1'},
			{title: 'Test template 2', content: 'Test 2'}
		]
	});
</script>
<form action="new_topic.php<?php echo $actionurl; ?>" method="post">
<table width="100%" border="0" cellspacing="5" cellpadding="0">
<?php if(isset($_GET['action'])&&$_GET['action']=="newthread"){?>
	<tr align="center">
    <td>Thread Title: <input type="text" name="threadtitle" value=""/></td>
  </tr>
<?php } ?>
  <tr align="center">
    <td colspan="2">Message</td>
  </tr>
  <tr align="center">
    <td colspan="2"><textarea name="content" id="topiccontent" style="width:100%; height:400px;"/><?php if(isset($_GET['action'])&&$_GET['action']=="editpost"){echo $messagebody['message'];} ?></textarea></td>
  </tr>
  <tr align="center">
    <td colspan="2"><input class="btn green" type= "submit" name="<?php if(isset($_GET['action'])&&$_GET['action']=="newthread"){echo "newthread";}elseif(isset($_GET['action'])&&$_GET['action']=="newmessage"){echo "newmessage";}elseif(isset($_GET['action'])&&$_GET['action']=="editpost"){echo "editpost";}?>" value="<?php if(isset($_GET['action'])&&$_GET['action']=="newthread"){echo "Post New Thread";}elseif(isset($_GET['action'])&&$_GET['action']=="newmessage"){echo "Post";}elseif(isset($_GET['action'])&&$_GET['action']=="editpost"){echo "Edit Post";} ?>" /></td>
  </tr>
</table>
</form>
<?php
	require_once("includes/end_html.php");
?>