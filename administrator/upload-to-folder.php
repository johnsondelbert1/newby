<?php
require_once("../includes/functions.php");
if(!check_permission(array("Uploading;upload_files","Uploading;delete_files","Uploading;create_folders","Uploading;rename_folders","Uploading;delete_folders"))){
	redirect_to("index.php");
}
?>
<?php 
function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('handleError');

$output_dir="../uploads/".urldecode($_GET['folder']);

if(isset($_POST['delfiles'])){
	if(check_permission("Uploading","delete_files")){
		function del_file($file){
			global $output_dir;
			if(file_exists($output_dir.$file)){
				unlink($output_dir.$file);
				return "File \"".$file."\" was deleted.";
			}else{
				return "File \"".$file."\" was not found. Perhaps it was already deleted?";
			}
		}
		if(!empty($_POST['files'])){
			$i = 0;
			foreach($_POST['files'] as $file){
				$success=del_file($file);
				$i++;
				if($i > 1){
					$success=$i." files were deleted.";
				}
			}
		}else{
			$error="No files selected.";
		}
	}else{
		$error="You do not have permission to delete files!";
	}
}

if(isset($_POST['submit'])){
	if (strpbrk($_POST['newname'], "\\/?%*:|\"<>") === FALSE) {
		try{
			if(rename($output_dir, "../uploads/".$_POST['newname'])){
				redirect_to("upload-to-folder.php?folder=".urlencode($_POST['newname']."/"));
			}else{
				$error="<h3 class=\"error\">There was a problem with renaming the folder.</h2>";
			}
		} catch(ErrorException $errstr){
			$error="There was a problem with renaming the folder. ".$errstr;
		}
	}else{
		$error="Folder name cannot contain any of these characters: \\/?%*:|\"<>\"";
	}
}

if(isset($_POST['upload'])){
	$message = upload($_FILES, $output_dir, 134217728);
}

if(isset($_FILES["myfile"])){
	multi_upload($_FILES, $output_dir);
}

$subquery="SELECT * FROM `galleries` ORDER BY `id` ASC";
$subgalleryquery=mysqli_query( $connection, $subquery);
confirm_query($subgalleryquery);

?>
<?php
	$pgsettings = array(
		"title" => "Uploading to folder: ".urldecode($_GET['folder']),
		"icon" => "icon-upload"
	);
	require_once("includes/begin_cpanel.php");
?>
    <script type="text/javascript">
	function confirmdelete(file){
		var ans = confirm("Are you sure you want to delete \""+file+"\" ?");
		if (ans==true){
			window.location = "upload-to-folder.php?folder=<?php echo $_GET['folder']; ?>&action=del&img=" + file;
		}
	}
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'fileall';
        $('input[id="'+$checkall+'"]').change(function() {
			var $all_check_status = $('input[id="'+$checkall+'"]').is(':checked');
             $("#form label").each(function(index, element) {
				var $target = $(this).attr("for");
				if($all_check_status!=$('input[id="'+$target+'"]').is(':checked')){
                	$(this).trigger('click');
				}
            });
        });  
     });
	</script>
<table cellpadding="5" id="sticker">
  <tr>
    <td width="110px"><a class="red btn" href="upload-files.php">Cancel</a></td>
  <td></td>
  </tr>
</table>
<h1>Folder Name</h1>
<?php if(check_permission("Uploading","rename_folders")){?>
<form method="post" action="upload-to-folder.php?folder=<?php echo urlencode($_GET['folder']); ?>">
    Name: <input name="newname" type="text" value="<?php echo substr($_GET['folder'], 0, -1); ?>" maxlength="100" />
    <input name="submit" type="submit" value="Change Folder Name" />
</form>
<br />
<?php } ?>
<h1>Files in <?php echo urldecode($_GET['folder']); ?></h1>
<form method="post" action="upload-to-folder.php?folder=<?php echo urlencode($_GET['folder']); ?>">
    <table id="form">
      <tr>
        <th scope="col" width="45%">Filename</th>
        <th scope="col" width="45%">File path (Copy link address)</th>
        <?php if(check_permission("Uploading","delete_files")){?>
            <th scope="col" width="10%" style="text-align:center;"><input type="checkbox" id="fileall"><label for="fileall"></label></th>
        <?php } ?>
      </tr>
        <?php
        $files = scandir("../uploads/".urldecode($_GET['folder']));
        array_shift ($files);
        array_shift ($files);
        if(count($files)>0){
            foreach($files as $file){
                ?>
                <tr>
                    <td><?php echo $file; ?></td>
                    <td><a href="../uploads/<?php echo urldecode($_GET['folder']).$file; ?>" target="_blank">Link</a></td>
                    <?php if(check_permission("Uploading","delete_files")){?>
                        <td style="text-align:center;"><input type="checkbox" name="files[]" id="<?php echo $file; ?>" value="<?php echo $file; ?>" /><label for="<?php echo $file; ?>"></label></td>
                    <?php } ?>
                </tr>
            <?php
            }
        }else{?>
                <tr>
                    <td colspan="3">(No files in this folder)</td>
                </tr>
        <?php } ?>
                <tr>
                    <td colspan="2"></td>
                    <?php if(check_permission("Uploading","delete_files")){?>
                        <td><input name="delfiles" type="submit" value="Delete Files" class="red btn" /></td>
                    <?php } ?>
                </tr>
    </table>
</form>
<?php if(check_permission("Uploading","upload_files")){?>
	<h1>Upload Files</h1>
	<table border="0" width="100%" border="0" style="margin-right:auto; margin-left:auto;">
	  <tr>
		<td width="50%"><h2>Upload Multiple files</h2>
			<?php print_multi_upload($output_dir, "128mb", false, true); ?>
		</td>
		<td width="50%"><h2>Upload Single file</h2><br />
            <br />
            <form action="upload-to-folder.php?folder=<?php echo $_GET['folder']; ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="file" id="file" />
            <input name="upload" type="submit" value="Upload File" />
            </form>
    	</td>
	  </tr>
	</table>
<?php } ?>
<?php
	require_once("includes/end_cpanel.php");
?>