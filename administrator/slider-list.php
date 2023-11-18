<?php
require_once("../includes/functions.php");
?>

<?php
if(!check_permission(array("Sliders;add_slider","Sliders;edit_slider","Sliders;delete_slider","Sliders;rename_slider",))){
	redirect_to("index.php");
}
if(isset($_POST['new'])){
	if(check_permission("Sliders","add_slider")){
		if (strpbrk($_POST['name'], "\\/?%*:|\"<>") === FALSE) {
			if($name=mysql_prep($_POST['name'])!=""){
				$name=mysql_prep($_POST['name']);
				$date=date("Y/m/d H:i:s", time());
				
				$query="SELECT * FROM `slider_names` WHERE `name` = '{$_POST['name']}'";
				$result=mysqli_query( $connection, $query);
				confirm_query($result);
				if(mysqli_num_rows($result) > 0){
					$error = "A slider by this name already exists.";
				}else{
					$query="INSERT INTO `slider_names` (
						`name`, `date_created`, `creator`
					) VALUES (
						'{$name}', '{$date}', {$_SESSION['user_id']})";
					$result=mysqli_query($connection, $query);
					confirm_query($result);
					
					$query="SELECT * FROM `slider_names` WHERE `name` = '{$_POST['name']}'";
					$result=mysqli_query( $connection, $query);
					confirm_query($result);
					
					$id=mysqli_fetch_array($result);
					mkdir("../images/slider/".$id['name']);
					$success="Slider \"{$_POST['name']}\" added!";
				}
			}else{
				$error="Slider name cannot be blank.";
			}
		}
		else {
		  $error="Slider name cannot contain any of these characters: \\/?%*:|\"<>\"";
		}
	}else{
		$error="You do not have permission to add sliders!";
	}

}elseif(isset($_POST['del'])){
	if(check_permission("Sliders","delete_slider")){
		function del($id){
			global $connection;
			
			$query="SELECT * FROM `slider_names` WHERE `id` = ".$id;
			$result=mysqli_query( $connection, $query);
			$slider = mysqli_fetch_array($result);
			
			// Specify the target directory and add forward slash
			$dir = "../images/slider/".$slider['name']."/"; 
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..') continue;
				unlink($dir.DIRECTORY_SEPARATOR.$item);
			}
			rmdir($dir);
			
			$query="DELETE FROM `slider_names` WHERE `id` = {$id}";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			
			$success="Slider was deleted!";
		}
		if(isset($_POST['sliders'])){
			foreach($_POST['sliders'] as $slider){
				del($slider);
			}
		}else{
			$error="No sliders selected!";
		}
	}else{
		$error="You do not have permission to delete sliders!";
	}
}
?>

<?php
$query="SELECT * FROM `slider_names`";
$result=mysqli_query( $connection, $query);
?>

<?php
	$pgsettings = array(
		"title" => "Sliders",
		"icon" => "icon-images"
	);
	require_once("includes/begin_cpanel.php");
?>

<script type="text/javascript">
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'sliderall';
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
<!--<<?php if(check_permission("Sliders","add_slider")){?>
<div class="overlay"><div class="dialog"><h1>Add New Slider</h1>
<form method="post">
    Name: <input name="name" type="text" value="<?php if(isset($_POST['name'])){echo $_POST['name'];} ?>" maxlength="64" />
    <input name="new" type="submit" value="Add Slider" />
</form></div></div> 
<?php } ?>-->
<?php if(check_permission("Sliders","add_slider")){?>
<h1>Add New Slider</h1>
<form method="post">
    Name: <input name="name" type="text" value="<?php if(isset($_POST['name'])){echo $_POST['name'];} ?>" maxlength="64" />
    <input name="new" type="submit" value="Add Slider" />
</form>
<?php } ?>
<h1>Slider List</h1>
<form method="post">
    <table width="90%" style="text-align:left;" class="list" border="0" cellspacing="0" id="form">
        <tr>
            <th>
                Name
            </th>
            <th>
                Slides
            </th>
            <?php if(check_permission("Sliders","delete_slider")){?>
            <th style="text-align:center;">
                <input type="checkbox" id="sliderall"><label for="sliderall"></label>
            </th>
            <?php } ?>
        </tr>
    <?php
    while($slider=mysqli_fetch_array($result)){ ?>
    
        <tr>
            <td><a href="edit-slider.php?slider=<?php echo urlencode($slider['id']); ?>"><?php echo $slider['name']; ?></a></td>
            <td>
            <?php
			    $i = 0; 
				$dir = "../images/slider/".$slider['name']."/";
				if ($handle = opendir($dir)) {
					while (($file = readdir($handle)) !== false){
						if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
							$i++;
					}
				}
				if($i == -1){
					$i=0;
				}
				echo $i;
			?>
            </td>
            <?php if(check_permission("Sliders","delete_slider")){?>
            <td width="10%" style="text-align:center;"><input type="checkbox" name="sliders[]" value="<?php echo $slider['id']; ?>" id="<?php echo $slider['id']; ?>" /><label for="<?php echo $slider['id']; ?>"></label></td>
            <?php } ?>
        </tr>
    
    <?php } ?>
    	<tr>
        	<td colspan="2"></td>
            <td><?php if(check_permission("Sliders","delete_slider")){?><input name="del" type="submit" value="Delete" class="btn red" /><?php } ?></td>
        </tr>
    </table>
</form>
<?php
	require_once("includes/end_cpanel.php");
?>