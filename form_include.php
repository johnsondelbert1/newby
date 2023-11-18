<?php
require_once("includes/functions.php");
if(isset($_GET['form'])){
	$query="SELECT * FROM `forms` WHERE  `id` =  {$_GET['form']}";
	$result=mysqli_query($connection, $query);
	$form=mysqli_fetch_array($result);
	
	$field_names=unserialize($form['field_names']);
	$field_types=unserialize($form['field_types']);
	$field_descs=unserialize($form['field_descs']);
	$field_placeholders=unserialize($form['field_placeholders']);
	$field_maxchars=unserialize($form['field_maxchars']);
	$field_validators=unserialize($form['field_validators']);
	
	$num_fields = count($field_names);
	$count = 0;

?>
<form method="post">
<table border="1" width="50%">
	<?php while($num_fields>$count){
		$maxchars = intval($field_maxchars[$count]);
	?>
	<tr>
    	<td style="vertical-align:top;">
        <?php echo $field_names[$count]; ?>: 
        <?php if($field_types[$count] == "text"){?>
            <input type="text"<?php if($field_placeholders[$count] != ""){echo ' placeholder="'.$field_placeholders[$count].'"';} ?><?php if($maxchars != ""){echo ' maxlength="'.$maxchars.'"';} ?> />
			<?php }elseif($field_types[$count] == "textarea"){ ?>
            <textarea rows="15" cols="75"<?php if($field_placeholders[$count] != ""||$maxchars != ""){$placeholder = $field_placeholders[$count]; if($maxchars != ""){$placeholder.=' (Max. '.$maxchars.' characters)';} echo ' placeholder="'.$placeholder.'"';} ?><?php if($maxchars != ""){echo ' maxlength="'.$maxchars.'"';} ?>></textarea>
            <?php } ?>
        </td>
    </tr>
    <?php
    $count++;
	} ?>
    <tr>
    	<td>
        	<input type="submit" name="<?php echo $form['name']; ?>" value="<?php echo $form['submit_value']; ?>" />
        </td>
    </tr>
</table>
</form>
<?php
}else{
	echo "Form ID Not Set!";
}
?>