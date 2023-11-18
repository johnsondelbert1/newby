<?php
require_once("../includes/functions.php");
?>

<?php
if(!check_permission(array("Sliders;edit_slider",))){
	redirect_to("slider-list.php?error=".urlencode('You do not have permission to edit sliders!'));
}
if(!isset($_GET['slider'])){
	redirect_to("slider-list.php");
}else{
	$query="SELECT * FROM `slider_names` WHERE `id` = ".$_GET['slider'];
	$result=mysqli_query( $connection, $query);
	$slider=mysqli_fetch_array($result);	
}
if(isset($_POST['save'])){
	//if(check_permission("Sliders","add_slider")){
		if(isset($_POST['slide_id'])&&isset($_POST['slide_order'])&&isset($_POST['slide_cap'])){
			foreach($_POST['slide_id'] as $slide_id){
				$caption = strip_tags(mysqli_real_escape_string($connection, $_POST['slide_cap'][$slide_id]), "<a><i><b><u>");
				$url = strip_tags(mysqli_real_escape_string($connection, $_POST['slide_url'][$slide_id]));
				$order = intval($_POST['slide_order'][$slide_id]);
				if(isset($_POST['slide_published'][$slide_id])){$published = 1;}else{$published = 0;}
				if(isset($_POST['new_tab'][$slide_id])){$new_tab = 1;}else{$new_tab = 0;}
				$savequery="UPDATE `slider_images` SET `order` = ".$order.", `caption` = '".$caption."', `url` = '".$url."', `new_tab` = ".$new_tab.", `published` = ".$published." WHERE `id` = ".$slide_id;
				$saveresult=mysqli_query($connection, $savequery);
				confirm_query($saveresult);
				$success="Slider updated!";
			}
			$savequery="UPDATE `slider_names` SET `date_edited` = '".$date."', `editor` = ".$_SESSION['user_id']." WHERE `id` = ".$slider['id'];
			$saveresult=mysqli_query($connection, $savequery);
			confirm_query($saveresult);
		//}
	}
	
}

if(isset($_POST['upload'])){
	$message = upload($_FILES, "../images/slider/".$slider['name']."/", 2097152, array('.jpeg','.jpg','.gif','.png'));
}
if(isset($_POST['submit'])){
	if (strpbrk($_POST['name'], "\\/?%*:|\"<>") === FALSE) {
		
		$name=mysql_prep($_POST['name']);
	
		if(rename("../images/slider/".$slider['name'], "../images/slider/".$slider['name']."/".$_POST['name'])){
			$query="UPDATE `slider_names` SET 
				`name` = '{$name}' 
				WHERE `id` = {$_GET['slider']}";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			$success = "Slider has been renamed!";
		}else{
			$error="There was a problem with renaming the slider.";
		}
	}else{
		$error="Slider name cannot contain any of these characters: \\/?%*:|\"<>\"";
	}
}
if(isset($_POST['del'])){
	//if(check_permission("Sliders","delete_slide")){
		function del($id){
			global $connection;
			global $slider;
			
			$query="SELECT * FROM `slider_images` WHERE `id` = ".$id;
			$result=mysqli_query( $connection, $query);
			$slide = mysqli_fetch_array($result);
			
			if(mysqli_num_rows($result)!=0){
				// Specify the target directory and add forward slash
				$file = "../images/slider/".$slider['name']."/".$slide['img_name'];
				if(file_exists($file)){
					unlink($file);
					$success="Slide was deleted!";
				}else{
					$error='File has already been deleted';
				}
			}else{
				$error='File has already been deleted';
			}
			
		}
		if(isset($_POST['slide'])){
			foreach($_POST['slide'] as $slide){
				del($slide);
			}
		}else{
			$error="No slides selected!";
		}
	/*}else{
		$error="You do not have permission to delete slides!";
	}*/
}
?>

<?php
$dir = '../images/slider/'.$slider['name'].'/';

$query="SELECT * FROM `slider_images` WHERE `slider_id` = ".$slider['id'];
$result=mysqli_query( $connection, $query);
$num_slides = mysqli_num_rows($result) + 1;

if(mysqli_num_rows($result)!=0){
	while($img = mysqli_fetch_array($result)){
		if(!file_exists($dir.$img['img_name'])){
			$imgquery="DELETE FROM `slider_images` WHERE `img_name` = '{$img['img_name']}' AND `slider_id` = ".$slider['id'];
			mysqli_query( $connection, $imgquery);
		}
	}
}
foreach (scandir($dir) as $item) {
	if ($item == '.' || $item == '..' || $item == 'Thumbs.db') continue;
	$file_ext = substr($item, strripos($item, '.'));

	$imagesquery="SELECT * FROM `slider_images` WHERE `img_name` = '{$item}' AND `slider_id` = ".$slider['id'];
	$imagesresult=mysqli_query( $connection, $imagesquery);
	if(mysqli_num_rows($imagesresult)==0){
		$imgquery="INSERT INTO `slider_images` (
			`slider_id`, `img_name`, `order`
		) VALUES (
			{$slider['id']}, '{$item}', {$num_slides})";
		mysqli_query($connection, $imgquery);
	}
}

$query="SELECT * FROM `slider_images` WHERE `slider_id` = ".$slider['id']." ORDER BY `order`";
$result=mysqli_query( $connection, $query);
?>

<?php
	$pgsettings = array(
		"title" => "Editing Slider: ".$slider['name'],
		"icon" => "icon-images"
	);
	require_once("includes/begin_cpanel.php");
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript">
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'slideall';
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

<table cellpadding="0" id="sticker">
  <tr>
    <td width="110"><a class=" btn red" href="slider-list.php">Cancel</a></td>
    <td></td>
  </tr>
</table>
<h1>Rename Slider</h1>
<?php if(check_permission("Sliders","rename_slider")){?>
<form method="post">
Name: <input name="name" type="text" value="<?php echo $slider['name']; ?>" maxlength="100" /><input name="submit" type="submit" value="Rename Slider" class="btn green"/>
</form><br>
<?php } ?>
<?php //if(check_permission("Slider","add_slide")){?>
<h1>Add New Slide</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="file" accept="image/*" /><br>
        *Recommended image size is 1510 pixels high by 800 pixels wide. Max filesize 2MB.
        <input name="upload" type="submit" value="Upload Image" />
    </form>
<?php //} ?>
<h1>List of Slides</h1>
<form method="post">
    <table width="100%" style="text-align:left;" class="list" border="0" cellspacing="0">
        <tr>
            <th>
                Order
            </th>
            <th width="30%">
                Picture
            </th>
            <!--<th>
                Name
            </th>-->
            <th>
                Caption
            </th>
            <th>
                URL <a href="page_list_simple.php" onclick="window.open('page_list_simple.php', 'newwindow', 'width=700, height=500'); return false;">(View Pages)</a>
            </th>
            <th style="text-align:center;">
                New Tab
            </th>
            <th style="text-align:center;">
                Published?
            </th>
            <?php //if(check_permission("Sliders","delete_slide")){?>
            <th style="text-align:center;">
                <input type="checkbox" id="slideall"><label for="slideall"></label>
            </th>
            <?php //} ?>
        </tr>
    <?php
	if(mysqli_num_rows($result)!=0){
		while($slide=mysqli_fetch_array($result)){
			?>
			<tr style="height:125px;">
            	<td><input type="hidden" name="slide_id[<?php echo $slide['id']; ?>]" value="<?php echo $slide['id']; ?>" /><input type="text" name="slide_order[<?php echo $slide['id']; ?>]" value="<?php echo $slide['order']; ?>" maxlength="3" style="width:30px;"/></td>
				<td><img src="../images/slider/<?php echo $slider['name'].'/'.$slide['img_name'] ?>" width="320" height="180" /></td>
                <!--<td><?php echo $slide['img_name'] ?></td>-->
                <td><input type="text" name="slide_cap[<?php echo $slide['id']; ?>]" maxlength="512" value="<?php echo htmlspecialchars($slide['caption']); ?>" /></td>
                <td><input type="text" name="slide_url[<?php echo $slide['id']; ?>]" maxlength="512" style="width:300px;" value="<?php echo htmlspecialchars($slide['url']); ?>" /></td>
                <td width="10%" style="text-align:center;"><input type="checkbox" name="new_tab[<?php echo $slide['id']; ?>]" id="tab_<?php echo $slide['id']; ?>" value="<?php echo $slide['id']; ?>" <?php if($slide['new_tab']==1){echo 'checked';} ?> /><label for="tab_<?php echo $slide['id']; ?>"></td>
                <td width="10%" style="text-align:center;"><input type="checkbox" name="slide_published[<?php echo $slide['id']; ?>]" id="pub_<?php echo $slide['id']; ?>" value="<?php echo $slide['id']; ?>" <?php if($slide['published']==1){echo 'checked';} ?> /><label for="pub_<?php echo $slide['id']; ?>"></td>
				<?php //if(check_permission("Sliders","delete_slide")){?>
				<td width="10%" style="text-align:center;" id="form"><input type="checkbox" name="slide[]" value="<?php echo $slide['id']; ?>" id="<?php echo $slide['id']; ?>" /><label for="<?php echo $slide['id']; ?>"></label></td>
				<?php //} ?>
			</tr>
	<?php
        }
	}else{?>
			<tr>
				<td colspan="5" style="text-align:center; font-size:24px;">[No Slides!]</td>
			</tr>
    <?php
	}
	?>
    	<tr>
        	<td style="text-align:center;"><input name="save" type="submit" value="Save Slides" class="btn green" /></td>
            <td colspan="5"></td>
            <td style="text-align:center;"><?php //if(check_permission("Sliders","delete_slide")){?><input name="del" type="submit" value="Delete" class="btn red" /><?php //} ?></td>
        </tr>
    </table>
</form>
<br>
<br>
<?php
	require_once("includes/end_cpanel.php");
?>