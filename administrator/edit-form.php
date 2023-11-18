<?php
require_once("../includes/functions.php");
confirm_logged_in();

?>
<?php 
$query="SELECT * FROM `forms` WHERE `id` = ".urldecode($_GET['formid']);
$result=mysqli_query( $connection, $query);
$form = mysqli_fetch_array($result);

if(isset($_POST['submit'])){
	$name=mysql_prep($_POST['formname']);
	$sub_text=mysql_prep($_POST['submit_value']);
	$editor = $_SESSION['user_id'];
	
	if(filter_var($_POST['email_to'],FILTER_VALIDATE_EMAIL)&&filter_var($_POST['email_from'],FILTER_VALIDATE_EMAIL)){
		$field_names=serialize(array_values($_POST['fieldname']));
		$field_types=serialize(array_values($_POST['fieldtype']));
		$field_descs=serialize(array_values($_POST['fielddesc']));
		$field_placeholders=serialize(array_values($_POST['fieldplaceholder']));
		$field_maxchars=serialize(array_values($_POST['fieldmaxchar']));
		$field_validators=serialize(array_values($_POST['fieldvalidate']));
		
		$query="UPDATE `forms` SET 
			`editor` = {$editor}, `last_edited` = '{$date}', `name` = '{$name}', `email_to` = '{$_POST['email_to']}', `email_from` = '{$_POST['email_from']}', `submit_value` = '{$sub_text}', `field_names` = '{$field_names}', `field_types` = '{$field_types}', `field_descs` = '{$field_descs}', `field_placeholders` = '{$field_placeholders}', `field_maxchars` = '{$field_maxchars}', `field_validators` = '{$field_validators}' 
			WHERE `id` = {$_GET['formid']}";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
	
		$success = "Form has been updated!";
	}else{
		$error = "Email not valid";
	}
}
$query="SELECT * FROM `forms` WHERE  `id` =  {$_GET['formid']}";
$result=mysqli_query($connection, $query);
$form=mysqli_fetch_array($result);

$field_names=unserialize($form['field_names']);
$field_types=unserialize($form['field_types']);
$field_descs=unserialize($form['field_descs']);
$field_placeholders=unserialize($form['field_placeholders']);
$field_maxchars=unserialize($form['field_maxchars']);
$field_validators=unserialize($form['field_validators']);

$num_fields = count($field_names);

?>
<?php
	$pgsettings = array(
		"title" => "Editing form: ".$form['name'],
		"icon" => "icon-pen"
	);
	require_once("includes/begin_cpanel.php");
?>
<style type="text/css">
span{
	font-weight:bold;
}
span:hover{
	text-decoration:underline;
	cursor:pointer;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>
	var cnt = <?php echo $num_fields ?>;
	var rows;
	$(document).ready(function(){
		rows = $('#fieldtable tr').length;
		$("#anc_add").click(function(){
    		$('#fieldtable tr').last().after('<tr id="'+cnt+'"><td><input name="fieldname['+cnt+']" type="text" value=""></td><td><select name="fieldtype['+cnt+']"><option value="text">Textbox</option><option value="textarea">Text Area</option></select></td><td><input name="fielddesc['+cnt+']" type="text" value=""></td><td><input name="fieldplaceholder['+cnt+']" type="text" value=""></td><td><input name="fieldmaxchar['+cnt+']" type="text" value=""></td><td><select name="fieldvalidate['+cnt+']"><option value="none">None</option><option value="email">Email</option><option value="notempty">Cannot Be Blank</option><option value="numbers">Numbers Only</option><option value="phone">Phone</option></select></td><td><span onClick="delrow('+cnt+')" class="btn red">Delete</span></td></tr>');
            $('#'+cnt+' select').material_select();
            cnt++;
		});
        $("#txt_add").click(function(){
            $('#fieldtable tr').last().after('<tr id="'+cnt+'"><td colspan="6"><textarea name="fielddesc['+cnt+']" rows="5" style="height:115px;"></textarea><input type="hidden" name="fieldname['+cnt+']" value="text-block" /><input type="hidden" name="fieldtype['+cnt+']" value="textblock" /><input type="hidden" name="fieldplaceholder['+cnt+']" value="" /><input type="hidden" name="fieldmaxchar['+cnt+']" value="" /><input type="hidden" name="fieldvalidate['+cnt+']" value="None" /></td><td><span onClick="delrow('+cnt+')" class="btn red">Delete</span></td></tr>');
            cnt++;
        });
	});
	
	function delrow(rowid){
		var row = document.getElementById(rowid);
		row.parentNode.removeChild(row);
	}
	
</script>
<form method="post" action="edit-form.php?formid=<?php echo $_GET['formid']; ?>">
<table cellpadding="5" id="sticker">
  <tr>
    <td width="110px"><input name="submit" type="submit" class="btn green" value="Save" /></td>
    <td width="110px"><a class="btn red" href="form-list.php">Cancel</a></td>
  <td></td>
  </tr>
</table>
<table width="100%" border="0" style="margin-right:auto; margin-left:auto;">
<tr>
<td width="50%">
    Name: <input name="formname" type="text" value="<?php if(isset($_POST['formname'])){echo $_POST['formname'];}else{echo $form['name'];} ?>" maxlength="128" />
    Data from form to be sent to: <input name="email_to" type="text" value="<?php if(isset($_POST['email_to'])){echo $_POST['email_to'];}else{echo $form['email_to'];} ?>" placeholder="Email" maxlength="128" />
    Sent from: <input name="email_from" type="text" value="<?php if(isset($_POST['email_from'])){echo $_POST['email_from'];}else{echo $form['email_from'];} ?>" placeholder="Email" maxlength="128" />
    Submit Button Text: <input name="submit_value" type="text" value="<?php if(isset($_POST['submit_value'])){echo $_POST['submit_value'];}else{echo $form['submit_value'];} ?>" maxlength="128" />
    <table width="100%" border="0" id="fieldtable">
        <tr>
        	<th>
            	Field Name
            </th>
        	<th>
            	Field Type
            </th>
        	<th>
            	Field Description
            </th>
        	<th>
            	Field Placeholder
            </th>
        	<th>
            	Maximum Characters allowed
            </th>
        	<th>
            	Validation
            </th>
        	<th>
            	Delete Field
            </th>
        </tr>
        <?php 
		$count = 0;
		while ($count<$num_fields){
        ?><tr id="<?php echo $count; ?>">
        <?php if($field_types[$count] == 'text' || $field_types[$count] == 'textarea'){ ?>
            <td>
                <input name="fieldname[<?php echo $count; ?>]" type="text" value="<?php if(isset($field_names[$count]))echo $field_names[$count]; ?>" maxlength="128" />
            </td>
            <td>
                <select name="fieldtype[<?php echo $count; ?>]">
                    <option value="text"<?php if(isset($field_types[$count])&&$field_types[$count]=="text"){echo " selected";} ?>>Textbox</option>
                    <option value="textarea"<?php if(isset($field_types[$count])&&$field_types[$count]=="textarea"){echo " selected";} ?>>Text Area</option>
                </select>
            </td>
            <td>
                <input name="fielddesc[<?php echo $count; ?>]" type="text" value="<?php if(isset($field_descs[$count]))echo $field_descs[$count]; ?>" />
            </td>
            <td>
                <input name="fieldplaceholder[<?php echo $count; ?>]" type="text" value="<?php if(isset($field_placeholders[$count]))echo $field_placeholders[$count]; ?>" maxlength="128" />
            </td>
            <td>
                <input name="fieldmaxchar[<?php echo $count; ?>]" type="text" value="<?php if(isset($field_maxchars[$count]))echo $field_maxchars[$count]; ?>" maxlength="11" />
            </td>
            <td>
                <select name="fieldvalidate[<?php echo $count; ?>]">
                    <option value="none"<?php if(isset($field_validators[$count])&&$field_validators[$count]=="none"){echo " selected";} ?>>None</option>
                    <option value="email"<?php if(isset($field_validators[$count])&&$field_validators[$count]=="email"){echo " selected";} ?>>Email</option>
                    <option value="notempty"<?php if(isset($field_validators[$count])&&$field_validators[$count]=="notempty"){echo " selected";} ?>>Cannot Be Blank</option>
                    <option value="numbers"<?php if(isset($field_validators[$count])&&$field_validators[$count]=="numbers"){echo " selected";} ?>>Numbers Only</option>
                    <option value="phone"<?php if(isset($field_validators[$count])&&$field_validators[$count]=="phone"){echo " selected";} ?>>Phone</option>
                </select>
            </td>
            <td>
                <span onClick="delrow(<?php echo $count; ?>)" class="btn red">Delete</span>
            </td>
            <?php }elseif($field_types[$count] == 'textblock'){ ?>
            <td colspan="6">
                <textarea name="fielddesc[<?php echo $count; ?>]" rows="5" style="height:115px;"><?php echo $field_descs[$count]; ?></textarea>
                <input type="hidden" name="fieldname[<?php echo $count; ?>]" value="text-block" />
                <input type="hidden" name="fieldtype[<?php echo $count; ?>]" value="textblock" />
                <input type="hidden" name="fieldplaceholder[<?php echo $count; ?>]" value="" />
                <input type="hidden" name="fieldmaxchar[<?php echo $count; ?>]" value="" />
                <input type="hidden" name="fieldvalidate[<?php echo $count; ?>]" value="None" />
            </td>
            <td>
                <span onClick="delrow(<?php echo $count; ?>)" class="btn red">Delete</span>
            </td>
            <?php } ?>
        </tr><?php
        $count++;
		}
    ?></table>
    <a href="javascript:void(0);" id='anc_add' class="btn green">New Field</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" id='txt_add' class="btn green">Add Text</a>
</form>
</td>
</tr>
</table>
<?php
	require_once("includes/end_cpanel.php");
?>