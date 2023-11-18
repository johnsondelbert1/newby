<?php
require_once("includes/functions.php");
?>
<?php

$query="SELECT * FROM `pages` WHERE `type` = 'Blog' ";
$result_page_prop=mysqli_query( $connection, $query);
$page_properties = mysqli_fetch_array($result_page_prop);

if(!isset($_GET['post'])){
	redirect_to($GLOBALS['HOST']."/blog");	
}

$output_dir="blog_galleries/".urldecode($_GET['post'])."/gallery/";
$output_thumbs="blog_galleries/".urldecode($_GET['post'])."/gallery-thumbs/";

if(!file_exists("blog_galleries/".urldecode($_GET['post']))){
	mkdir("blog_galleries/".urldecode($_GET['post']));
}
if(!file_exists($output_dir)){
	mkdir($output_dir);
}
if(!file_exists($output_thumbs)){
	mkdir($output_thumbs);
}

if(isset($_POST['submit'])){
	$id=$_GET['post'];
	
	$content=strip_tags(nl2br(mysqli_real_escape_string($connection, $_POST['content'])), "<b><a><p><img><br><hr><ul><ol><li><sup><sub><video><source>");
	$title=strip_tags(nl2br(mysqli_real_escape_string($connection, $_POST['title'])));
	date_default_timezone_set($site_info['timezone']);
	$date=date("Y/m/d H:i:s", time());

	if(isset($_POST['allowComments'])){
		$allowComments = 1;
	}else{
		$allowComments = 0;
	}
	
	$query = "UPDATE `blog` SET content = '{$content}', title='{$title}', comments_allowed={$allowComments}, lastedited='{$date}' WHERE id = {$id}";
	$result = mysqli_query($connection, $query);
	confirm_query($result);
	if (mysqli_affected_rows($connection) == 1) {
		$success = "blog post was updated.";
	} else {
		$error = "blog post couldn't update.";
		$error .= "<br />" . mysqli_error($connection);
	}
}

if(isset($_POST['delfiles'])){
	function del_file($file){
		global $output_dir;
		global $output_thumbs;
		if(file_exists($output_dir.$file)){
			if(file_exists($output_thumbs.$file)){
				unlink($output_thumbs.$file);
			}
			unlink($output_dir.$file);
			return "Photo \"".$file."\" was deleted.";
		}else{
			return "Photo \"".$file."\" was not found. Perhaps it was already deleted?";
		}
	}
	if(!empty($_POST['files'])){
		$i = 0;
		foreach($_POST['files'] as $file){
			$success=del_file($file);
			$i++;
			if($i > 1){
				$success=$i." photos were deleted.";
			}
		}
	}else{
		$error="No photos selected.";
	}
}

if(isset($_POST['uploadgall'])){
	$message = upload($_FILES, $output_dir, 4194304, array('.jpeg','.jpg','.gif','.png'));
}

if(isset($_FILES["myfile"])){
	multi_upload($_FILES, $output_dir);
}
?>

<?php
	$query="SELECT * FROM `blog` WHERE id={$_GET['post']}";
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$blog=mysqli_fetch_array($result);

if(!check_permission("Blog","edit_blog")&&$blog['poster']!=$_SESSION['user_id']){
	redirect_to($GLOBALS['HOST']."/blog?error=".urlencode("You do not have permission to edit a blog!"));
}
?>

<?php
$pgsettings = array(
	"title" => "Editing: ".$blog['title'],
	"pageselection" => "blog",
	"nav" => true,
	"banner" => 0,
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php");

?><!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>-->
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
		toolbar1: "insertfile | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image | forecolor backcolor emoticons | undo redo | colorpicker",
		image_advtab: true,
		templates: [
			{title: 'Test template 1', content: 'Test 1'},
			{title: 'Test template 2', content: 'Test 2'}
		]
	});
	
	$(document).ready(function() {
			 $('input[id="imgall"]').click(function() {
			 $("#img :checkbox").attr('checked', $(this).attr('checked'));
			 });
		  
	 });
	 
	var cbcfn = function(e) {
		if (e.target.tagName.toUpperCase() != "INPUT") {
			var $tc = $(this).find('input:checkbox:first'),
				tv = $tc.attr('checked');
			$tc.attr('checked', !tv);
		}
	};
	
	$('.photo-link').live('click', cbcfn);
</script>
<h1 id="editheader">Editing: "<?php echo $blog['title']; ?>"</h1>
<form action="edit_blog_post.php?post=<?php echo $_GET['post'];?>" method="post" name="editpage">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#ffffff">
  <td>
  <input type="text" name="title" class="text" maxlength="1024" value="<?php echo $blog['title']; ?>" /> <input type="checkbox" name="allowComments" id="allowComments" <?php if($blog['comments_allowed']==1){echo 'checked';} ?>><label for="allowComments">Allow Comments</label>
  </td>
  </tr>
  <tr>
    <td style="overflow:hidden;"><textarea name="content" id="blogcontent"><?php echo $blog['content']; ?></textarea></td>
  </tr>
  <tr bgcolor="#ffffff">
  <td>
  <table width="200" border="0">
  <tr>
    <td><input class="btn green" type="submit" name="submit" value="Save" /></td>
    <td><a class="btn red" href="page/<?php echo $GLOBALS['blog_page']; ?>?delpost=<?php echo $_GET['post']; ?>">Delete</a></td>
    <td><a class="btn blue" href="<?php echo $GLOBALS['HOST']."/page/".urlencode($page_properties['name']);?>">Cancel</a></td>
  </tr>
</table>
  </td>
</table>
</form>
<br><br>
<p style="text-align:left;">Select all: <input type="checkbox" id="imgall" /></p>
<form action="edit_blog_post.php?post=<?php echo $_GET['post'];?>" method="post">
<div align="center" id="img" style="text-align:center; width:100%; padding:28px;">
<?php
/** settings **/
$images_dir = "blog_galleries/".$blog['id']."/gallery/";
$thumbs_dir = "blog_galleries/".$blog['id']."/gallery-thumbs/";
$thumbs_width = 200;
$thumbs_height = $thumbs_width;
$images_per_row = 15;

/** generate photo gallery **/
$image_files = get_files($images_dir);
if(count($image_files)) {
  $index = 0;
  foreach($image_files as $index=>$file) {
	$index++;
	$thumbnail_image = $thumbs_dir.$file;
	if(!file_exists($thumbnail_image)) {
	  $extension = get_file_extension($thumbnail_image);
	  $extension = strtolower($extension);
	  if($extension) {
		make_thumb($images_dir.$file,$thumbnail_image,$thumbs_width,$thumbs_height,$extension);
	  }
	}?>
	<div class="photo-link"><span class="galleryImage"><img src="<?php echo $thumbnail_image; ?>" style="width:150px; height:150px;" /><input type="checkbox" name="files[]" value="<?php echo $file; ?>" /><input name="delete_CheckBox" type="hidden" value="false" /></span></div>
	<?php
  }
  ?><div class="clear"></div><?php
}
else {
  ?><p>(There are no images in this gallery)</p><?php
}
?>
<input name="delfiles" type="submit" value="Delete Selected Photos" class="btn red" />
</div>
</form>
<table border="0" width="100%" border="0" style="margin-right:auto; margin-left:auto;">
  <tr>
    <td></td>
        </tr>
        <tr>
    <td>
    <table>
  <tr>
    <td width="50%"><h2>Upload Multiple photos</h2>
    	<?php print_multi_upload($output_dir,"4mb","jpg,jpeg,gif,png,JPG,JPEG,GIF,PNG", false); ?>
    </td>
    <td width="50%"><h2>Upload Single photo</h2><br />
<br />
<form action="edit_blog_post.php?post=<?php echo $_GET['post']; ?>" method="post" enctype="multipart/form-data">
<input type="file" name="file" id="file" />
<input name="uploadgall" type="submit" value="Upload Image" />
</form></td>
  </tr>
</table>
</td>
  </tr>
</table>
<?php
require_once("includes/end_html.php");
?>