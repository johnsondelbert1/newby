<?php
require_once("../includes/functions.php");
?>

<?php
if(!check_permission(array("Galleries;add_gallery","Galleries;edit_gallery","Galleries;delete_gallery","Galleries;rename_gallery"))){
	redirect_to("index.php");
}
if(isset($_POST['newgal'])){
	if(check_permission("Galleries","add_gallery")){
		if (strpbrk($_POST['galname'], "\\/?%*:|\"<>") === FALSE) {
			if($galname=mysql_prep($_POST['galname'])!=""){
				$galname=mysql_prep($_POST['galname']);
				$query="INSERT INTO `galleries` (
					`name`
				) VALUES (
					'{$galname}')";
				$result=mysqli_query($connection, $query);
				confirm_query($result);
				$query="SELECT * FROM `galleries` WHERE `name` = '{$_POST['galname']}'";
				$result=mysqli_query( $connection, $query);
				confirm_query($result);
				$gallid=mysqli_fetch_array($result);
				mkdir("../galleries/".$gallid['name']);
				mkdir("../galleries/".$gallid['name']."/gallery");
				mkdir("../galleries/".$gallid['name']."/gallery-thumbs");
				$success="Gallery \"{$_POST['galname']}\" added!";
			}else{
				$error="Gallery name cannot be blank.";
			}
		}
		else {
		  $error="Gallery name cannot contain any of these characters: \\/?%*:|\"<>\"";
		}
	}

}elseif(isset($_POST['delgalleries'])){
	if(check_permission("Galleries","delete_gallery")){
		function del_gall($gallid){
			global $connection;
			
			$query="SELECT * FROM `galleries` WHERE `id` = ".$gallid;
			$result=mysqli_query( $connection, $query);
			$gall = mysqli_fetch_array($result);
			
			// Specify the target directory and add forward slash
			$dir = "../galleries/".$gall['name']."/gallery/"; 
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..') continue;
				unlink($dir.DIRECTORY_SEPARATOR.$item);
			}
			rmdir($dir);
			$dir = "../galleries/".$gall['name']."/gallery-thumbs/"; 
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..') continue;
				unlink($dir.DIRECTORY_SEPARATOR.$item);
			}
			rmdir($dir);
			$dir = "../galleries/".$gall['name']."/"; 
			rmdir($dir);
			
			$query="DELETE FROM `galleries` WHERE `id` = {$gallid}";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			
			$pagegallquery="SELECT * FROM `pages`";
			$pagegallresult=mysqli_query( $connection, $pagegallquery);
			while($page=mysqli_fetch_array($pagegallresult)){
				if (is_array(unserialize($page['galleries']))){
					$pagegalleries = unserialize($page['galleries']);
					$index = array_search($gallid, $pagegalleries);
					if($index != false){
						unset($pagegalleries[$index]);
					}
					$pagegalleries = serialize($pagegalleries);
					
					$query = "UPDATE `pages` SET `galleries`='{$pagegalleries}' WHERE id = {$page['id']}";
					$result = mysqli_query( $connection, $query);
				}
			}
			
			$subgallquery="SELECT * FROM `galleries`";
			$subgallresult=mysqli_query( $connection, $subgallquery);
			while($subgallery=mysqli_fetch_array($subgallresult)){
				if (is_array(unserialize($subgallery['subgalleries']))){
					$subgalleries = unserialize($subgallery['subgalleries']);
					$index = array_search($gallid, $subgalleries);
					if($index != false){
						unset($subgalleries[$index]);
					}
					$subgalleries = serialize($subgalleries);
					
					$query = "UPDATE `pages` SET `galleries`='{$subgalleries}' WHERE id = {$subgallery['id']}";
					$result = mysqli_query( $connection, $query);
				}
			}
			
			$success="Gallery was deleted!";
		}
		if(isset($_POST['galleries'])){
			foreach($_POST['galleries'] as $gallery){
				del_gall($gallery);
			}
		}else{
			$error="No galleries selected!";
		}
	}else{
		$error="You do not have permission to delete galleries!";
	}
}
?>

<?php
$query="SELECT * FROM `galleries`";
$result=mysqli_query( $connection, $query);
?>

<?php
	$pgsettings = array(
		"title" => "Gallery List",
		"icon" => "icon-images"
	);
	require_once("includes/begin_cpanel.php");
?>
<script type="text/javascript">
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'gallall';
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
<?php if(check_permission("Galleries","add_gallery")){?>
<h1>Add New Gallery</h1>
<form method="post" action="gallery-list.php">
    Name: <input name="galname" type="text" value="<?php if(isset($_POST['galname'])){echo $_POST['galname'];} ?>" maxlength="100" />
    <input name="newgal" type="submit" value="Add Gallery" class="btn green" />
</form>
<?php } ?>
<h1>Gallery List</h1>
<form method="post" action="gallery-list.php">
    <table width="90%" style="text-align:left;" class="list" border="0" cellspacing="0" id="form">
        <tr>
            <th>
                ID
            </th>
            <th>
                Name
            </th>
            <th>
                Photos
            </th>
            <?php if(check_permission("Galleries","delete_gallery")){?>
            <th style="text-align:center;">
                <input type="checkbox" id="gallall">
                <label for="gallall">
            </th>
            <?php } ?>
        </tr>
    <?php
    while($gallery=mysqli_fetch_array($result)){
		//re-create deleted gallery folder
		$dir = "../galleries/".$gallery['name']."/";
		if(!file_exists($dir)){
			mkdir($dir);
			mkdir($dir."gallery/");
			mkdir($dir."gallery-thumbs/");
		}
		?>
    
        <tr>
            <td><?php echo $gallery['id']; ?></td>
            <td><a href="edit_gallery.php?gallid=<?php echo urlencode($gallery['id']); ?>"><?php echo $gallery['name']; ?></a></td>
            <td>
            <?php
			    $i = 0; 
				$dir = "../galleries/".$gallery['name']."/gallery/";
				$files = glob($dir . '*.{jpg,gif,png,jpeg,JPG,GIF,PNG,JPEG}', GLOB_BRACE);
				
				if ( $files !== false )
				{
					$filecount = count( $files );
					echo $filecount;
				}
				else
				{
					echo 0;
				}
			?>
            </td>
            <?php if(check_permission("Galleries","delete_gallery")){?>
            <td width="10%" style="text-align:center;"><input type="checkbox" name="galleries[]" value="<?php echo $gallery['id']; ?>" id="<?php echo $gallery['id']; ?>" /><label for="<?php echo $gallery['id']; ?>"></td>
            <?php } ?>
        </tr>
    
    <?php } ?>
    	<tr>
        	<td colspan="3"></td>
            <td><?php if(check_permission("Galleries","delete_gallery")){?><input name="delgalleries" type="submit" value="Delete" class="btn red" /><?php } ?></td>
        </tr>
    </table>
</form>
<?php
	require_once("includes/end_cpanel.php");
?>