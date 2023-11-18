<?php
require_once("../includes/functions.php");
?>
<?php
if(!check_permission(array("Uploading;upload_files","Uploading;delete_files","Uploading;create_folders","Uploading;rename_folders","Uploading;delete_folders"))){
	redirect_to("index.php");
}
?>
<?php
	$pgsettings = array(
		"title" => "Upload Files",
		"icon" => "icon-upload"
	);
	require_once("includes/begin_cpanel.php");
?>
<object data="url" type=text/html>
<embed src="../filemanager/dialog.php?type=0&fldr=" width="100%" height="600px">
</embed>
</object>


<?php require_once("includes/end_cpanel.php"); ?>