<?php
require_once("includes/session.php");
require_once("includes/functions.php");
?>
<?php
if(!check_permission("Blog","post_blog")){
	redirect_to($GLOBALS['HOST']."/blog?error=".urlencode("You do not have permission to post a blog!"));
}
if(isset($_POST['submit'])){
	if($_POST['title']!=""){
		$content=strip_tags(nl2br(mysqli_real_escape_string($connection, $_POST['content'])), "<b><a><p><img><br><hr><ul><ol><li><sup><sub><video><source>");
		$title=strip_tags(mysqli_real_escape_string($connection, $_POST['title']));
		date_default_timezone_set($GLOBALS['site_info']['timezone']);
		$date=date("Y/m/d H:i:s", time());

		if(isset($_POST['allowComments'])){
			$allowComments = 1;
		}else{
			$allowComments = 0;
		}
		
		$query="INSERT INTO `blog` (
					`datecreated`, `poster`, `title`, `content`, `comments_allowed` 
				) VALUES (
					'{$date}', '{$_SESSION['user_id']}', '{$title}', '{$content}', {$allowComments})";
		$result=mysqli_query( $connection, $query);
		confirm_query($result);
		
		$lastid=mysqli_insert_id($connection);
		
		mkdir("blog_galleries/".$lastid);
		mkdir("blog_galleries/".$lastid."/gallery");
		mkdir("blog_galleries/".$lastid."/gallery-thumbs");
		
		redirect_to($GLOBALS['HOST']."/edit_blog_post?post=".$lastid."&success=".urlencode("Blog posted!"));
	}else{
		$error="Title cannot be blank.";
	}

}
?>
<?php
$pgsettings = array(
	"title" => "Compose New Blog Post",
	"pageselection" => false,
	"nav" => true,
	"banner" => 0,
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php");
?>
<script type="text/javascript" src="tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	tinymce.init({
		selector: "textarea",
		theme: "modern",
		skin: 'light',
		width: '100%',
		height: '400px',
		resize: false,
		menubar : false,
		statusbar : false,
		plugins: [
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars fullscreen",
			"insertdatetime media nonbreaking save contextmenu directionality",
			"emoticons paste textcolor code table colorpicker"
		],
		contextmenu: "link image print code fullscreen",
		toolbar1: "insertfile | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image | forecolor backcolor emoticons | undo redo | print colorpicker",
		image_advtab: true,
		templates: [
			{title: 'Test template 1', content: 'Test 1'},
			{title: 'Test template 2', content: 'Test 2'}
		]
	});
</script>
<h1>Compose New Blog Post</h1>
<form action="new_blog_post.php" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#ffffff">
  <td>
  <input style="width:45%;" type="text" name="title" class="text" maxlength="1024" placeholder="Title"<?php if(isset($_POST['title'])){echo $_POST['title'];} ?>/> <input type="checkbox" name="allowComments" id="allowComments" checked><label for="allowComments">Allow Comments</label>
  </td>
  </tr>
  <tr>
    <td style="overflow:hidden;"><textarea name="content" id="newblogcontent"><?php if(isset($_POST['content'])){echo $_POST['content'];};?></textarea></td>
  </tr>
  <tr bgcolor="#ffffff">
  <td>
  <table width="200" border="0">
  <tr>
    <td><input class="btn green" class="button" type="submit" name="submit" value="Submit" /></td>
    <td><a class="btn red" href="/page/<?php echo $GLOBALS['blog_page']; ?>">Cancel</a></td>
  </tr>
</table>
  </td>
</table>
</form>


<?php
	require_once("includes/end_html.php");
?>