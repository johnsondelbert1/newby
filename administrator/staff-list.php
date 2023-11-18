<?php
require_once("../includes/functions.php");
?>

<?php
//if(!check_permission(array("Forms;add_form","Forms;edit_form","Forms;delete_form",))){
	//redirect_to("index.php");
//}
if(isset($_POST['new'])){
	//if(check_permission("Forms","add_form")){
		if($newname=mysql_prep($_POST['newname'])!=""){
			$query="SELECT * FROM `staff`";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			$num_staff=mysqli_num_rows($result) + 1;
			
			$newname=mysql_prep($_POST['newname']);
			$date=date("Y/m/d H:i:s", time());
			
			$query="INSERT INTO `staff` (
				`name`, `creator`, `date_created`, `order`
			) VALUES (
				'{$newname}', {$_SESSION['user_id']}, '{$date}', {$num_staff})";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			
			$query="SELECT * FROM `staff` WHERE `name` = '{$newname}'";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			$id=mysqli_fetch_array($result);
			
			mkdir("../images/staff/".$id['id']."/");
			$success="Staff Member \"{$newname}\" added!";
		}else{
			$error="Staff member name cannot be blank.";
		}
	//}

}elseif(isset($_POST['delforms'])){
	//if(check_permission("Forms","delete_form")){
		function del($id){
			global $connection;
			
			$query="SELECT * FROM `staff` WHERE `id` = ".$id;
			$result=mysqli_query( $connection, $query);
			$staff = mysqli_fetch_array($result);
			
			$query="DELETE FROM `staff` WHERE `id` = {$id}";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			
			// Specify the target directory and add forward slash
			$dir = "../images/staff/".$id; 
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..') continue;
				unlink($dir.DIRECTORY_SEPARATOR.$item);
			}
			rmdir($dir);
			
			$success="Staff member was deleted!";
		}
		if(isset($_POST['staff'])){
			foreach($_POST['staff'] as $staff){
				del($staff);
			}
		}else{
			$error="No staff members selected!";
		}
	/*}else{
		$error="You do not have permission to delete staff members!";
	}*/
}
?>

<?php
$query="SELECT * FROM `staff` ORDER BY `order`";
$result=mysqli_query( $connection, $query);
?>

<?php
	$pgsettings = array(
		"title" => "Staff Member List",
		"icon" => "icon-profile"
	);
	require_once("includes/begin_cpanel.php");
?>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>-->
<script type="text/javascript">
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'staffall';
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
<?php //if(check_permission("Forms","add_form")){?>
<h1>Add New Staff Member</h1>
<form method="post">
    <label for="newname">Staff Member Name: </label><input name="newname" id="newname" type="text" value="<?php if(isset($_POST['galname'])){echo $_POST['galname'];} ?>" maxlength="100" />
    <input name="new" type="submit" value="Add" class="btn green" />
</form>
<?php //} ?>
<h1>Staff List</h1>
<form method="post">
    <table width="90%" style="text-align:left;" class="list" border="0" cellspacing="0" id="form">
        <tr>
            <th width="10%">
                Order
            </th>
            <th width="10%">
                Picture
            </th>
            <th>
                Name
            </th>
            <th>
                Role
            </th>
            <?php //if(check_permission("Forms","delete_form")){?>
            <th style="text-align:center;">
                <input type="checkbox" id="staffall">
                <label for="staffall"></label>
            </th>
            <?php //} ?>
        </tr>
    <?php
	if(mysqli_num_rows($result)!=0){
		while($staff=mysqli_fetch_array($result)){
			$profile_pic = false;
			$dir = "../images/staff/".$staff['id']."/";
			if(!file_exists($dir)){
				mkdir($dir);
			}
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..' || $item == 'Thumbs.db') continue;
				$profile_pic = $item;
			}
			?>
			<tr style="height:125px;">
            <td><?php echo $staff['order']; ?></td>
				<td><?php if($profile_pic != false){ ?><img src="<?php echo $dir.$item; ?>" width="120" height="120" /><?php }else{echo "[No Picture]";} ?></td>
                <td><a href="edit-staff.php?id=<?php echo urlencode($staff['id']); ?>"><?php echo $staff['name']; ?></a></td>
                <td><?php if($staff['role']!=""){echo $staff['role'];}else{echo '[N/A]';} ?></td>
				<?php //if(check_permission("Galleries","delete_gallery")){?>
				<td width="10%" style="text-align:center;"><input type="checkbox" name="staff[]" value="<?php echo $staff['id']; ?>" id="<?php echo $staff['id']; ?>" /><label for="<?php echo $staff['id']; ?>"></label></td>
				<?php //} ?>
			</tr>
	<?php
        }
	}else{?>
			<tr>
				<td colspan="5" style="text-align:center; font-size:24px;">[No Staff!]</td>
			</tr>
    <?php
	}
	?>
    	<tr>
        	<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align:center;"><?php //if(check_permission("Forms","delete_form")){?><input name="delforms" type="submit" value="Delete" class="btn red" /><?php //} ?></td>
        </tr>
    </table>
</form>
<?php
	require_once("includes/end_cpanel.php");
?>