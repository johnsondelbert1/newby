<?php
require_once('begin.php');

try{
	$connection = mysqli_connect($dbconnection->server,  $dbconnection->username,  $dbconnection->password, $dbconnection->name);
	
	if($dbconnection->firstrun == "false"){
		$_SESSION=array();
		
		if(isset($_COOKIE[session_name()])){
			unset($_SESSION['username']);
			unset($_SESSION['password']);
		}
		
		session_destroy();
		header("Location: ../index.php");
	}
	
}catch (ErrorException $e){
	header("Location: dbconnect.php");
}

if(isset($_POST['submit'])){
	if($_POST['site-name']!=""){
		if(filter_var($_POST['site-email'], FILTER_VALIDATE_EMAIL)) {

			$address_line1 = mysqli_real_escape_string($connection, $_POST['address_line1']);
			$address_line2 = mysqli_real_escape_string($connection, $_POST['address_line2']);
			$address_city = mysqli_real_escape_string($connection, $_POST['address_city']);
			$address_stateregion = mysqli_real_escape_string($connection, $_POST['address_stateregion']);
			$address_zip = mysqli_real_escape_string($connection, $_POST['address_zip']);
			$address_country = mysqli_real_escape_string($connection, $_POST['address_country']);
			$contact_phone = mysqli_real_escape_string($connection, $_POST['contact_phone']);

			$query="UPDATE `site_info` SET 
				`name` = '{$_POST['site-name']}', `contact_email` = '{$_POST['site-email']}', `address_line1` = '{$address_line1}', `address_line2` = '{$address_line2}', `address_city` = '{$address_city}', 
					`address_stateregion` = '{$address_stateregion}', `address_zip` = '{$address_zip}', `address_country` = '{$address_country}', `contact_phone` = '{$contact_phone}', `timezone` = '{$_POST['site-tz']}'";
			$result=mysqli_query($connection, $query);
			
			$info = simplexml_load_file('../../includes/database.xml');
			$info->firstrun = "false";
			$info->asXML('../../includes/database.xml');
			
			header("Location: ../index.php");
		}else{
			$message = "<h3 class=\"error\">Not a valid email address!</h3>";
		}
	}else{
		$message = "<h3 class=\"error\">Site Name cannot be blank!</h3>";
	}
}

$pgtitle = 'Step 3 of 3: Site Setup';
require_once('begin_html.php');
?>
<form method="post">
	Site Name <input type="text" name="site-name" value="<?php if(isset($_POST['site-name'])){echo $_POST['site-name'];}?>" /><br>
    Site Contact Email <input type="text" name="site-email" value="<?php if(isset($_POST['site-email'])){echo $_POST['site-email'];}?>" /><br>
    Website Timezone
        <select name="site-tz">
			<?php foreach(DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $timezone){ ?>
            <option value="<?php echo $timezone; ?>"<?php if(!isset($_POST['site_tz'])&&$timezone == "America/Los_Angeles"){echo 'selected';}elseif(isset($_POST['site_tz'])&&$_POST['site_tz'] == $timezone){echo 'selected';}?>><?php echo $timezone; ?></option>
            <?php } ?>
        </select><br>
    Site Contact Phone<input name="contact_phone" type="text" value="<?php echo $site['contact_phone']; ?>" maxlength="16" style="width:400px;" />
	Address<br/>
	<label for="address_line1">Line 1</label><input name="address_line1" id="address_line1" type="text" value="<?php echo $site['address_line1']; ?>" maxlength="255" style="width:400px;" /><br/>
	<label for="address_line2">Line 2</label><input name="address_line2" id="address_line2" type="text" value="<?php echo $site['address_line2']; ?>" maxlength="255" style="width:400px;" /><br/>
	<label for="address_city">City</label><input name="address_city" id="address_city" type="text" value="<?php echo $site['address_city']; ?>" maxlength="255" style="width:250px;" />
	<label for="address_stateregion">State / Region</label><input name="address_stateregion" id="address_stateregion" type="text" value="<?php echo $site['address_stateregion']; ?>" maxlength="255" style="width:115px;" /><br/>
	<label for="address_zip">Zip</label><input name="address_zip" id="address_zip" type="text" value="<?php echo $site['address_zip']; ?>" maxlength="10" style="width:100px;" /><br/>
	<label for="address_country">Country</label><input name="address_country" id="address_country" type="text" value="<?php echo $site['address_country']; ?>" maxlength="64" style="width:265px;" /><br/>
    <input type="submit" name="submit" value="Submit" />
</form>
</body>
</html>