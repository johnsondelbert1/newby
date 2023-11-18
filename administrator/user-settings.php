<?php
require_once("../includes/functions.php");
confirm_logged_in();
?>
<?php 

if(isset($_POST['submit'])){
	$postFieldPropsArr = array();
	$post = $_POST;
	unset($post['submit'],$post['new_user'],$post['email_activate']);
	
	if(!empty($post)){
		//Get field IDs and put them into array
		$fieldIDs = array();
		foreach ($post['name'] as $fieldID => $fieldVal) {
			array_push($fieldIDs, $fieldID);
		}
		//Get possible field properties and put them into array
		$fieldProps = array();
		foreach ($post as $fieldProp => $fieldVal) {
			array_push($fieldProps, $fieldProp);
		}
		//Build new array using the ID and field prop arrays
		foreach ($fieldIDs as $id) {
			$newField = array();
			foreach ($fieldProps as $key) {
				$newField[$key] = $post[$key][$id];
			}
			$postFieldPropsArr[$id] = $newField;
		}
	}

	//Validation & sanitization
	$errArr = array();
	//Characters
	foreach ($postFieldPropsArr as $id => $props) {
		$postFieldPropsArr[$id]['name'] = trim($props['name']);
		if(preg_match("/[^a-zA-Z0-9-_]/", $postFieldPropsArr[$id]['name'])){
			array_push($errArr, 'Field name `'.$postFieldPropsArr[$id]['name'].'` can only contain characters "a-z", "0-9", "-", and "_".');
		}
		if($postFieldPropsArr[$id]['name'] == ''){
			array_push($errArr, 'Cannot have empty field names.');
		}
	}
	//Duplicate names
	$names = array();
	foreach ($postFieldPropsArr as $id => $props) {
		$postFieldPropsArr[$id]['name'] = trim($props['name']);
		array_push($names, $postFieldPropsArr[$id]['name']);
	}
	if(count($names) !== count(array_unique($names))){
		array_push($errArr, 'Duplicate field names not allowed.');
	}

	if(empty($errArr)){
		//Get IDs of fields to delete from DB
		$query="SELECT `id` FROM `custom_field_users_properties`";
		$result=mysqli_query( $connection, $query);
		if(mysqli_num_rows($result)!=0){
			//Check DB IDs against POST IDs
			$fieldsToDelete = array();
			while ($dbFieldID = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				if(!array_key_exists($dbFieldID['id'], $postFieldPropsArr)){
					array_push($fieldsToDelete, $dbFieldID['id']);
				}
			}

			if(!empty($fieldsToDelete)){
				//Delete columns from `users_custom_fields`
				foreach ($fieldsToDelete as $id) {
					$getNameQuery = "SELECT `name` FROM `custom_field_users_properties` WHERE `id` = ".$id;
					$getNameResult=mysqli_query($connection, $getNameQuery);
					$getName = mysqli_fetch_array($getNameResult, MYSQLI_ASSOC);
					$query = "ALTER TABLE `users_custom_fields` DROP `{$getName['name']}`";
					if($result=mysqli_query($connection, $query)){
						$success = "Custom User Fields has been updated!";
					}else{
						$error = "SQL Error has occurred. Contact Administrator.".mysqli_error($connection);
					}
				}

				$query="DELETE FROM `custom_field_users_properties` WHERE `id` IN (".implode(', ', $fieldsToDelete).")";
				if(!$result=mysqli_query( $connection, $query)){
					$error = "SQL error. Contact Administrator.".mysqli_error($connection);
				}
			}
		}

		//Update existing fields
		$existingFields = $postFieldPropsArr;
		foreach ($existingFields as $key => $value) {
			$flag = strpos($key, 'post_');
			if($flag !== false){
				//Remove any new fields from array to just get existing fields
				unset($existingFields[$key]);
			}
		}
		//Update data in DB
		foreach ($existingFields as $key => $value) {
			//Verify maxchars
			if($value['maxchar']!='' && intval($value['maxchar'])!=0){
				if(intval($value['maxchar'])<=10000){
					$maxchar = intval($value['maxchar']);
				}else{
					$maxchar = 10000;
					$value['maxchar'] = 10000;
				}
			}else{
				$maxchar = 10000;
			}

			$getNameQuery = "SELECT `name` FROM `custom_field_users_properties` WHERE `id` = ".$key;
			$getNameResult=mysqli_query($connection, $getNameQuery);
			$getName = mysqli_fetch_array($getNameResult, MYSQLI_ASSOC);
			echo $value['maxchar'];
			$query="UPDATE `custom_field_users_properties` SET 
				`name` = '{$value['name']}', 
				`type` = '{$value['type']}', 
				`desc` = '{$value['desc']}', 
				`placeholder` = '{$value['placeholder']}', 
				`maxchar` = '{$value['maxchar']}', 
				`validate` = '{$value['validate']}'
				WHERE `id` = {$key}";
			if($result=mysqli_query($connection, $query)){
				$success = "Custom User Fields has been updated!";
			}else{
				$error = "SQL Error has occurred. Contact Administrator.".mysqli_error($connection);
			}
			//Update name & length of column in `users_custom_fields`
			if($value['type']=='text'){
				$query = "ALTER TABLE `users_custom_fields` CHANGE `{$getName['name']}` `{$value['name']}` VARCHAR({$maxchar}) NOT NULL";
			}elseif($value['type']=='textarea'){
				$query = "ALTER TABLE `users_custom_fields` CHANGE `{$getName['name']}` `{$value['name']}` TEXT NOT NULL";
			}
			
			if($result=mysqli_query($connection, $query)){
				$success = "Custom User Fields has been updated!";
			}else{
				$error = "SQL Error has occurred. Contact Administrator.".mysqli_error($connection);
			}
		}

		//Adding new fields
		$newFields = $postFieldPropsArr;
		foreach ($newFields as $key => $value) {
			$flag = strpos($key, 'post_');
			if($flag === false){
				//Remove any existing fields from array to just get new fields
				unset($newFields[$key]);
			}
		}
		//Add new fields in DB
		foreach ($newFields as $key => $value) {
			//Verify maxchars
			if($value['maxchar']!='' && intval($value['maxchar'])!=0){
				if(intval($value['maxchar'])<=10000){
					$maxchar = intval($value['maxchar']);
				}else{
					$maxchar = 10000;
					$value['maxchar'] = 10000;
				}
			}else{
				$maxchar = 10000;
			}

			$query="INSERT INTO `custom_field_users_properties` (`name`, `type`, `desc`, `placeholder`, `maxchar`, `validate`) VALUES (
				'{$value['name']}', 
				'{$value['type']}', 
				'{$value['desc']}', 
				'{$value['placeholder']}', 
				'{$value['maxchar']}', 
				'{$value['validate']}' )";
			if($result=mysqli_query($connection, $query)){
				$success = "Custom User Fields has been updated!";
				$pass = true;
			}else{
				$error = "SQL Error has occurred. Contact Administrator.".mysqli_error($connection);
			}

			//Add columns in `users_custom_fields`
			if($value['type']=='text'){
				$query = "ALTER TABLE `users_custom_fields` ADD `{$value['name']}` VARCHAR({$maxchar}) NOT NULL";
			}elseif($value['type']=='textarea'){
				$query = "ALTER TABLE `users_custom_fields` ADD `{$value['name']}` TEXT NOT NULL";
			}
			
			if($result=mysqli_query($connection, $query)){
				$success = "Custom User Fields has been updated!";
			}else{
				$error = "SQL Error has occurred. Contact Administrator.".mysqli_error($connection);
			}
		}

		$email_activate = (isset($_POST['email_activate']))? 1 : 0;

		$query="UPDATE `site_info` SET 
			`user_creation` = '{$_POST['new_user']}', `require_email_activation` = {$email_activate}
			WHERE `id` = 1";
		if($result=mysqli_query($connection, $query)){
			$success = "Custom User Fields has been updated!";
		}else{
			$error = "SQL Error has occurred. Contact Administrator.".mysqli_error($connection);
		}
	}else{
		$error = $errArr;
	}
}

$query="SELECT * FROM `site_info` WHERE `id` = 1";
$result=mysqli_query( $connection, $query);
$user_settings = mysqli_fetch_array($result);

?>
<?php
	$pgsettings = array(
		"title" => "User Settings",
		"icon" => "icon-users"
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
	var rows;
	$(document).ready(function(){
		rows = $('#fieldtable tr').length;
		$("#anc_add").click(function(){
			var rndID = 'post_'+Math.random().toString(36).substr(2, 5);
    		$('#fieldtable tr').last().after(
											'<tr id="'+rndID+'"><td><input type="hidden" name="id['+rndID+']" value="'+rndID+'" /><input name="name['+rndID+']" type="text" value=""></td>'+
											'<td><select name="type['+rndID+']"><option value="text">Textbox</option><option value="textarea">Text Area</option></select></td>'+
											'<td><input name="desc['+rndID+']" type="text" value=""></td><td><input name="placeholder['+rndID+']" type="text" value=""></td>'+
											'<td><input name="maxchar['+rndID+']" type="text" value=""></td>'+
											'<td><select name="validate['+rndID+']"><option value="none">None</option><option value="email">Email</option><option value="notempty">Cannot Be Blank</option><option value="numbers">Numbers Only</option><option value="phone">Phone</option></select></td>'+
											'<td><span onClick="confirm_del('+rndID+')" class="btn red">Delete</span></td></tr>'
											);
            $('#'+rndID+' select').material_select();
		});
	});
	function confirm_del(rowid){
		var ans = confirm("Are you sure you want to delete this field? Existing user data cannot be recovered if deleted.");
		if (ans==true){
			var row = document.getElementById(rowid);
			row.parentNode.removeChild(row);
		}
	}
</script>
<form method="post" action="user-settings">
<table cellpadding="5" id="sticker">
  <tr>
    <td width="110px"><input name="submit" type="submit" class="btn green" value="Save" /></td>
  <td></td>
  </tr>
</table>
<h2>New User Creation Via</h2>
<input name="new_user" type="radio" value="admin" id="admin"<?php if($user_settings['user_creation']=='admin'){echo ' checked';} ?> /><label for="admin">Admin</label><br/>
<input name="new_user" type="radio" value="approval" id="approval"<?php if($user_settings['user_creation']=='approval'){echo ' checked';} ?> /><label for="approval">Approval</label><br/>
<input name="new_user" type="radio" value="any" id="any"<?php if($user_settings['user_creation']=='any'){echo ' checked';} ?> /><label for="any">Any</label><br/>
<h2>Require Email Activation</h2>
<input name="email_activate" type="checkbox" <?php if($user_settings['require_email_activation']){echo ' checked';} ?> id="email" /><label for="email"></label><br/>
<table width="100%" border="0" cellspacing="10" style="text-align:center;" id="fieldtable">
	<tr>
		<td><h2>Custom User Fields</h2></td>
	</tr>
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
	$query="SELECT * FROM `custom_field_users_properties`";
	$result=mysqli_query( $connection, $query);

    if(mysqli_num_rows($result)!=0||isset($_POST['submit'])){
    	$fieldPropsArr =  array();
    	if(!isset($_POST['submit'])||isset($pass)){
    		//Gets existing DB fields
			while ($dbFieldProps = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$fieldPropsArr[$dbFieldProps['id']] = $dbFieldProps;
			}
		}else{
			$post = $_POST;
			unset($post['submit'],$post['new_user'],$post['email_activate']);
			
			if(!empty($post)){
				//Get field IDs and put them into array
				$fieldIDs = array();
				foreach ($post['name'] as $fieldID => $fieldVal) {
					array_push($fieldIDs, $fieldID);
				}
				//Get possible field properties and put them into array
				$fieldProps = array();
				foreach ($post as $fieldProp => $fieldVal) {
					array_push($fieldProps, $fieldProp);
				}
				//Build new array using the ID and field prop arrays
				foreach ($fieldIDs as $id) {
					$newField = array();
					foreach ($fieldProps as $key) {
						$newField[$key] = $post[$key][$id];
					}
					$fieldPropsArr[$id] = $newField;
				}
			}
		}

		foreach ($fieldPropsArr as $fieldProps) {
			$fieldID = $fieldProps['id'];
	        ?><tr id="<?php echo $fieldID; ?>">
	        <?php if($fieldProps['type'] == 'text' || $fieldProps['type'] == 'textarea'){ ?>
	            <td>
	            	<input type="hidden" name="id[<?php echo $fieldID; ?>]" value="<?php echo $fieldID; ?>" />
	                <input name="name[<?php echo $fieldID; ?>]" type="text" value="<?php if(isset($fieldProps['name']))echo $fieldProps['name']; ?>" maxlength="64" />
	            </td>
	            <td>
	                <select name="type[<?php echo $fieldID; ?>]">
	                    <option value="text"<?php if(isset($fieldProps['type'])&&$fieldProps['type']=="text"){echo " selected";} ?>>Textbox</option>
	                    <option value="textarea"<?php if(isset($fieldProps['type'])&&$fieldProps['type']=="textarea"){echo " selected";} ?>>Text Area</option>
	                </select>
	            </td>
	            <td>
	                <input name="desc[<?php echo $fieldID; ?>]" type="text" value="<?php if(isset($fieldProps['desc']))echo $fieldProps['desc']; ?>" />
	            </td>
	            <td>
	                <input name="placeholder[<?php echo $fieldID; ?>]" type="text" value="<?php if(isset($fieldProps['placeholder']))echo $fieldProps['placeholder']; ?>" maxlength="128" />
	            </td>
	            <td>
	                <input name="maxchar[<?php echo $fieldID; ?>]" type="text" value="<?php if(isset($fieldProps['maxchar'])&&$fieldProps['maxchar'] != 0)echo $fieldProps['maxchar']; ?>" maxlength="11" />
	            </td>
	            <td>
	                <select name="validate[<?php echo $fieldID; ?>]">
	                    <option value="none"<?php if(isset($fieldProps['validate'])&&$fieldProps['validate']=="none"){echo " selected";} ?>>None</option>
	                    <option value="email"<?php if(isset($fieldProps['validate'])&&$fieldProps['validate']=="email"){echo " selected";} ?>>Email</option>
	                    <option value="notempty"<?php if(isset($fieldProps['validate'])&&$fieldProps['validate']=="notempty"){echo " selected";} ?>>Cannot Be Blank</option>
	                    <option value="numbers"<?php if(isset($fieldProps['validate'])&&$fieldProps['validate']=="numbers"){echo " selected";} ?>>Numbers Only</option>
	                    <option value="phone"<?php if(isset($fieldProps['validate'])&&$fieldProps['validate']=="phone"){echo " selected";} ?>>Phone</option>
	                </select>
	            </td>
	            <td>
	                <span onClick="confirm_del(<?php echo $fieldID; ?>)" class="btn red">Delete</span>
	            </td>
	            <?php }elseif($field_types == 'textblock'){ ?>
	            <td colspan="6">
	                <textarea name="fielddesc[<?php echo $fieldID; ?>]" rows="5" style="height:115px;"><?php echo $fieldProps['desc']; ?></textarea>
	                <input type="hidden" name="fieldname[<?php echo $fieldID; ?>]" value="text-block" />
	                <input type="hidden" name="fieldtype[<?php echo $fieldID; ?>]" value="textblock" />
	                <input type="hidden" name="fieldplaceholder[<?php echo $fieldID; ?>]" value="" />
	                <input type="hidden" name="fieldmaxchar[<?php echo $fieldID; ?>]" value="" />
	                <input type="hidden" name="fieldvalidate[<?php echo $fieldID; ?>]" value="None" />
	            </td>
	            <td>
	                <span onClick="confirm_del(<?php echo $fieldID; ?>)" class="btn red">Delete</span>
	            </td>
	            <?php } ?>
	        </tr><?php
		}
	}
?>
</table>
<a href="javascript:void(0);" id='anc_add' class="btn green">New Field</a>
</form>
<?php
	require_once("includes/end_cpanel.php");
?>