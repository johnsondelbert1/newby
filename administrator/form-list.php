<?php
require_once("../includes/functions.php");
?>

<?php
if(!check_permission(array("Forms;add_form","Forms;edit_form","Forms;delete_form",))){
	redirect_to("index.php");
}
if(isset($_POST['newform'])){
	if(check_permission("Forms","add_form")){
		if($formname=mysql_prep($_POST['formname'])!=""){
			$formname=mysql_prep($_POST['formname']);
			$unique_name=randstring();
			$date=date("Y/m/d H:i:s", time());
			
			$query="INSERT INTO `forms` (
				`name`, `u_name`, `creator`, `date_created`
			) VALUES (
				'{$formname}', '{$unique_name}', {$_SESSION['user_id']}, '{$date}')";
			$result=mysqli_query($connection, $query);
			confirm_query($result);
			$query="SELECT * FROM `galleries` WHERE `name` = '{$_POST['formname']}'";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			$formid=mysqli_fetch_array($result);
			$success="Form \"{$_POST['formname']}\" added!";
		}else{
			$error="Form name cannot be blank.";
		}
	}

}elseif(isset($_POST['delforms'])){
	if(check_permission("Forms","delete_form")){
		function del_form($id){
			global $connection;
			
			$query="SELECT * FROM `forms` WHERE `id` = ".$id;
			$result=mysqli_query( $connection, $query);
			$gall = mysqli_fetch_array($result);
			
			$query="DELETE FROM `forms` WHERE `id` = {$id}";
			$result=mysqli_query( $connection, $query);
			confirm_query($result);
			
			$success="Form was deleted!";
		}
		if(isset($_POST['forms'])){
			foreach($_POST['forms'] as $form){
				del_form($form);
			}
		}else{
			$error="No forms selected!";
		}
	}else{
		$error="You do not have permission to delete forms!";
	}
}
?>

<?php
$query="SELECT * FROM `forms`";
$result=mysqli_query( $connection, $query);
?>

<?php
	$pgsettings = array(
		"title" => "Form List",
		"icon" => "icon-pen"
	);
	require_once("includes/begin_cpanel.php");
?>
<script type="text/javascript">
   <!-- jQuery for "Select All" checkboxes -->
    $(document).ready(function() {
		var $checkall = 'formall';
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
<?php if(check_permission("Forms","add_form")){?>
<h1>Add New Form</h1>
<form method="post">
    Name: <input name="formname" type="text" value="<?php if(isset($_POST['galname'])){echo $_POST['galname'];} ?>" maxlength="100" />
    <input name="newform" type="submit" value="Add" class="btn green" />
</form>
<?php } ?>
<h1>Form List</h1>
<form method="post">
    <table width="90%" style="text-align:left;" class="list" border="0" cellspacing="0" id="form">
        <tr>
            <th>
                Name
            </th>
            <?php if(check_permission("Forms","delete_form")){?>
            <th style="text-align:center;">
                <input type="checkbox" id="formall"><label for="formall"></label>
            </th>
            <?php } ?>
        </tr>
    <?php
	if(mysqli_num_rows($result)!=0){
		while($form=mysqli_fetch_array($result)){ ?>
			<tr>
				<td><a href="edit-form.php?formid=<?php echo urlencode($form['id']); ?>"><?php echo $form['name']; ?></a></td>
				<?php if(check_permission("Galleries","delete_gallery")){?>
				<td width="10%" style="text-align:center;"><input type="checkbox" name="forms[]" value="<?php echo $form['id']; ?>" id="<?php echo $form['id']; ?>" /><label for="<?php echo $form['id']; ?>"></label></td>
				<?php } ?>
			</tr>
	<?php
        }
	}else{?>
			<tr>
				<td colspan="2" style="text-align:center; font-size:24px;">[No Forms!]</td>
			</tr>
    <?php
	}
	?>
    	<tr>
        	<td></td>
            <td style="text-align:center;"><?php if(check_permission("Forms","delete_form")){?><input name="delforms" type="submit" value="Delete" class="btn red" /><?php } ?></td>
        </tr>
    </table>
</form>
<?php
	require_once("includes/end_cpanel.php");
?>