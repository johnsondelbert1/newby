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
<iframe style="border:0; width:100%; height:600px; margin:-10px;" src="../filemanager/dialog.php"/>

<?php require_once("includes/end_cpanel.php"); ?>