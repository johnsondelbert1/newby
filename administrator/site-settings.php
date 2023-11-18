<?php
require_once("../includes/functions.php");
?>
<?php

$output_dir_banner = "../images/banner/";
$output_dir_bg = "../images/bg/";
$output_dir_logo = "../images/logo/";
$output_dir_icon = "../images/favicon/";

if(isset($_GET['delete'])){
	if($_GET['delete']=='banner'){
		foreach (scandir($output_dir_banner) as $item) {
			if ($item == '.' || $item == '..') continue;
			unlink($output_dir_banner.DIRECTORY_SEPARATOR.$item);
		}
		$success = 'Banner image deleted';
	}
	if($_GET['delete']=='bg'){
		foreach (scandir($output_dir_bg) as $item) {
			if ($item == '.' || $item == '..') continue;
			unlink($output_dir_bg.DIRECTORY_SEPARATOR.$item);
		}
		$success = 'Background image deleted';
	}
	if($_GET['delete']=='logo'){
		foreach (scandir($output_dir_logo) as $item) {
			if ($item == '.' || $item == '..') continue;
			unlink($output_dir_logo.DIRECTORY_SEPARATOR.$item);
		}
		$success = 'Logo image deleted';
	}
	if($_GET['delete']=='favicon'){
		foreach (scandir($output_dir_icon) as $item) {
			if ($item == '.' || $item == '..') continue;
			unlink($output_dir_icon.DIRECTORY_SEPARATOR.$item);
		}
		$success = 'Favicon image deleted';
	}
}
if(isset($_POST['chng_info'])){
	if(check_permission("Website","edit_site_settings")){
		if(isset($_POST['published'])){
			$published = 1;
		}else{
			$published = 0;
		}
		$site_name = mysqli_real_escape_string($connection, $_POST['site_name']);
		$copyright_text = mysqli_real_escape_string($connection, $_POST['copyright_text']);
		$metadata = strip_tags(mysqli_real_escape_string($connection, $_POST['metadata']), "<meta>");
		$site_description = mysqli_real_escape_string($connection, $_POST['site_description']);
		
		$footer_content = mysqli_real_escape_string($connection, $_POST['foot_content']);
		$address_line1 = mysqli_real_escape_string($connection, $_POST['address_line1']);
		$address_line2 = mysqli_real_escape_string($connection, $_POST['address_line2']);
		$address_city = mysqli_real_escape_string($connection, $_POST['address_city']);
		$address_stateregion = mysqli_real_escape_string($connection, $_POST['address_stateregion']);
		$address_zip = mysqli_real_escape_string($connection, $_POST['address_zip']);
		$address_country = mysqli_real_escape_string($connection, $_POST['address_country']);
		$contact_phone = mysqli_real_escape_string($connection, $_POST['contact_phone']);
		
		if($site_name!=""){
			if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$query="UPDATE `site_info` SET 
					`name` = '{$site_name}', `contact_email` = '{$_POST['email']}', `address_line1` = '{$address_line1}', `address_line2` = '{$address_line2}', `address_city` = '{$address_city}', 
					`address_stateregion` = '{$address_stateregion}', `address_zip` = '{$address_zip}', `address_country` = '{$address_country}', `contact_phone` = '{$contact_phone}', 
					`timezone` = '{$_POST['tz']}', `published` = {$published}, `copyright_text` = '{$copyright_text}', `default_rank` = {$_POST['rank']}, `site_description` = '{$site_description}', 
					`homepage` = {$_POST['homepage']}, `meta_tags` = '{$metadata}', `footer_content` = '{$footer_content}'";
				$result=mysqli_query($connection, $query);
				confirm_query($result);
				$success = "Site Info has been updated!";
			}else{
				$error = "Not a valid email address!";
			}
		}else{
			$error = "Site Name cannot be blank!";
		}
	}else{
		$error="You do not have permission to edit Site Information.";
	}
}

if(isset($_POST['bg_submit'])){
	if(check_permission("Website","upload_favicon_banner")){
		if(isset($_POST['bg_fixed'])){
			$bg_fixed = 1;
		}else{
			$bg_fixed = 0;
		}
		if(isset($_POST['use_bg_color'])){
			$use_bg_color = 1;
		}else{
			$use_bg_color = 0;
		}
		
		$query="UPDATE `site_layout` SET 
			`bg_repeat` = '{$_POST['bg_repeat']}', `bg_position` = '{$_POST['bg_position']}', `bg_size`='{$_POST['bg_size']}', `bg_fixed` = {$bg_fixed}, `use_bg_color` = {$use_bg_color}";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		$success = "Background settings has been updated!";
	}else{
		$error="You do not have permission to edit the background image.";
	}
}

if(isset($_POST['cust_css_js'])){
	if(check_permission("Website","edit_site_colors")){
		
		$css = strip_tags(mysqli_real_escape_string($connection, $_POST['custom_css']),"<link>");
		$js = strip_tags(mysqli_real_escape_string($connection, $_POST['custom_js']), "<script>");
		
		/*$query="UPDATE `site_layout` SET 
			`menu_color` = '{$_POST['menu_color']}', `contentbg_color` = '{$_POST['contentbg_color']}', `sitebg_color` = '{$_POST['sitebg_color']}', `accent_color` = '{$_POST['accent_color']}', `text_color` = '{$_POST['text_color']}', `custom_css` = '{$css}'";*/
		$query="UPDATE `site_layout` SET `custom_css` = '{$css}'";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		$query="UPDATE `site_info` SET `custom_js` = '{$js}'";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		$success = "Custom CSS/JS has been updated!";
	}else{
		$error="You do not have permission to edit styles.";
	}
}

if(isset($_POST['socialnet'])){
	if(check_permission("Website","edit_socnet")){
		
		$urls = array();
		$enabled = array();
		foreach($GLOBALS['soc_networks'] as $network){
			if(isset($_POST[$network.'_enabled'])){$enabled[$network.'_enabled'] = 1;}else{$enabled[$network.'_enabled'] = 0;}
			
			$preurl = mysqli_real_escape_string($connection, $_POST[$network.'_url']);
			if($preurl!=''){
				if(!preg_match("~^(?:f|ht)tps?://~i", $preurl)){
					$url = substr_replace($preurl, 'http://', 0, 0);
				}else{
					$url = $preurl;
				}
			}else{
				$url = '';
			}
			$urls[$network.'_url'] = $url;
		}
		
		$query="UPDATE `site_info` SET ";
		$i = 0;
		foreach($GLOBALS['soc_networks'] as $network){
			$query.="`".$GLOBALS['soc_networks'][$i]."_url` = '".$urls[$network.'_url']."', `".$GLOBALS['soc_networks'][$i]."_enabled` = ".$enabled[$network.'_enabled'];
			if(($i+1)  != count($GLOBALS['soc_networks'])){
				$query.=", ";
			}
			$i++;
		}
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		$success = "Social Networks has been updated!";
		
		$query="SELECT * FROM `site_info` WHERE `id` = 1";
		$result=mysqli_query( $connection, $query);
		$GLOBALS['site_info']=mysqli_fetch_array($result);
	}else{
		$error="You do not have permission to edit Social Networks.";
	}
}
if(isset($_POST['uploadbanner'])){
	foreach (scandir($output_dir_banner) as $item) {
		if ($item == '.' || $item == '..') continue;
		unlink($output_dir_banner.DIRECTORY_SEPARATOR.$item);
	}
	$message = upload($_FILES, $output_dir_banner, 2097152, array('.jpeg','.jpg','.gif','.png','.JPEG','.JPG','.GIF','.PNG','.ico','.ICO'));
}
if(isset($_POST['uploadbg'])){
	foreach (scandir($output_dir_bg) as $item) {
		if ($item == '.' || $item == '..') continue;
		unlink($output_dir_bg.DIRECTORY_SEPARATOR.$item);
	}
	$message = upload($_FILES, $output_dir_bg, 4194304, array('.jpeg','.jpg','.gif','.png','.JPEG','.JPG','.GIF','.PNG','.ico','.ICO'));
}
if(isset($_POST['uploadlogo'])){
	foreach (scandir($output_dir_logo) as $item) {
		if ($item == '.' || $item == '..') continue;
		unlink($output_dir_logo.DIRECTORY_SEPARATOR.$item);
	}
	$message = upload($_FILES, $output_dir_logo, 2097152, array('.jpeg','.jpg','.gif','.png','.JPEG','.JPG','.GIF','.PNG','.ico','.ICO'));
}
if(isset($_POST['chng_logo_url'])){
	$logo_url = strip_tags(mysqli_real_escape_string($connection, $_POST['logo_url']));
	$query="UPDATE `site_info` SET 
		`logo_url` = '{$logo_url}'";
	$result=mysqli_query($connection, $query);
	confirm_query($result);
	$success = "Logo URL has been updated!";
}
if(isset($_POST['uploadfavicon'])){
	foreach (scandir($output_dir_icon) as $item) {
		if ($item == '.' || $item == '..') continue;
		unlink($output_dir_icon.DIRECTORY_SEPARATOR.$item);
	}
	$message = upload($_FILES, $output_dir_icon, 128000, array('.jpeg','.jpg','.gif','.png','.JPEG','.JPG','.GIF','.PNG','.ico','.ICO'));
}
if(isset($_POST['chngganalytics'])){
	if(check_permission("Website","edit_google_analytics")){
		$analytics_code = strip_tags(mysqli_real_escape_string($connection, $_POST['analyticscode']), "<script>");
		if(isset($_POST['analyticsenabled'])){
			$analytics_enabled = 1;
		}else{
			$analytics_enabled = 0;
		}
		$query="UPDATE `site_info` SET 
			`g_analytics_enabled` = '{$analytics_enabled}', `g_analytics_code` = '{$analytics_code}'";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		$success = "Google Analytics has been updated!";
	}else{
		$error="You do not have permission to edit Google Analytics settings!";
	}
}

$query="SELECT * FROM  `site_info`";
$result=mysqli_query($connection, $query);
$site=mysqli_fetch_array($result);

$query="SELECT * FROM  `site_layout`";
$result=mysqli_query($connection, $query);
$layout=mysqli_fetch_array($result);

$query="SELECT * FROM `ranks`";
$rankresult=mysqli_query($connection, $query);

$query="SELECT * FROM `pages` ORDER BY `position` ASC";
$result_pages=mysqli_query($connection, $query);
?>
<?php
	$pgsettings = array(
		"title" => "Settings",
		"icon" => "icon-cog"
	);
	require_once("includes/begin_cpanel.php");
?>
<script type="text/javascript" src="../tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	
	tinymce.init({
		selector: "#foot_content",
		theme: "modern",
		skin: 'light',
		width: '100%',
		plugins: [
			"advlist autolink lists link image charmap print preview anchor",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media save directionality",
			"emoticons template paste textcolor"
		],
		toolbar1: "insertfile undo redo | bold italic | bullist numlist outdent indent | link image",
		image_advtab: true,
		templates: [
			{title: 'Footer Template', content: '<b>Phone: </b>(208)-555-5555<br><b>Address: </b>1234 Secondgen Lane, Coeur d\' Alene, ID 83814'},
		]
	});
	
  	var placeholder = 'This is a line \nthis should be a new line';
	$('#analytics').attr('value', placeholder);
	
	$('#analytics').focus(function(){
		if($(this).val() === placeholder){
			$(this).attr('value', '');
		}
	});
	
	$('#analytics').blur(function(){
		if($(this).val() ===''){
			$(this).attr('value', placeholder);
		}    
	});

</script>
<div id="TabbedPanels1" class="TabbedPanels">
            <ul class="TabbedPanelsTabGroup">
            	<?php if(check_permission("Website","edit_site_settings")){ ?>
                	<li class="TabbedPanelsTab" tabindex="0">Settings</li>
                <?php } ?>
                <?php if(check_permission("Website","edit_site_colors")){ ?>
                <li class="TabbedPanelsTab" tabindex="0">Custom CSS/JS</li>
                <?php } ?>
                <?php if(check_permission("Website","upload_favicon_banner")){ ?>
                <li class="TabbedPanelsTab" tabindex="0">Imagery</li>
                <?php } ?>
                <?php if(check_permission("Website","edit_socnet")){ ?>
                <li class="TabbedPanelsTab" tabindex="0">Social Networks</li>
                <?php } ?>
                <?php if(check_permission("Website","edit_google_analytics")){ ?>
                <li class="TabbedPanelsTab" tabindex="0">Google Analytics</li>
                <?php } ?>
                <li class="TabbedPanelsTab" tabindex="0">About</li>
            </ul>
            <div class="TabbedPanelsContentGroup">
                
<?php if(check_permission("Website","edit_site_settings")){ ?>
<div class="TabbedPanelsContent">
<h1 style="margin:-4px -4px 5px -4px; padding:5px;">Site Data</h1>
<form method="post" action="site-settings.php?tab=0">
<table width="75%" border="0"  style="margin-left:auto; margin-right:auto;">
  <tr>
    <td>
        <h2>Site Name</h2>
        <input name="site_name" type="text" value="<?php echo $site['name']; ?>" maxlength="1024" style="width:250px;" />
    </td>
    <td>
    	<h2>Website Timezone</h2>
        <select name="tz">
			<?php foreach(DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $timezone){ ?>
            <option value="<?php echo $timezone; ?>"<?php if($site['timezone'] == $timezone){echo " selected";} ?>><?php echo $timezone; ?></option>
            <?php } ?>
        </select>
     </td>
  </tr>
  <tr>
  	<td>
        <h2>Website Contact Email</h2>
        <input name="email" type="email" value="<?php echo $site['contact_email']; ?>" maxlength="128" style="width:250px;" />
    </td>
  	<td>
        <h2>New User Default Rank</h2>
        <select name="rank">
            <?php
            while($rank=mysqli_fetch_array($rankresult)){?>
                    <option value="<?php echo $rank['id'];?>"<?php if($site['default_rank']==$rank['id']){echo " selected";} ?>><?php echo $rank['name']?></option>
            <?php
            }?>
        </select>
      </td>
  </tr>
  <tr>
  	<td>
        <h2>Website Published</h2>
        <input name="published" type="checkbox"<?php if($site['published']==1){echo " checked";} ?> id="pub" /><label for="pub"></label>
    </td>
  	<td>
    	<h2>Homepage</h2>
        <select name="homepage">
            <?php
            while($homepages=mysqli_fetch_array($result_pages)){?>
                    <option value="<?php echo $homepages['id'];?>"<?php if($site['homepage']==$homepages['id']){echo " selected";} ?>><?php echo $homepages['name']?></option>
            <?php
            }?>
        </select>
    </td>
  </tr>
  <tr>
  	<td>
        <h2>Copyright Text</h2>
        <input name="copyright_text" type="text" value="<?php echo $site['copyright_text']; ?>" maxlength="256" style="width:400px;" />
    </td>
  	<td>
    	<h2>Address</h2>
    	<label for="address_line1">Line 1</label><input name="address_line1" id="address_line1" type="text" value="<?php echo $site['address_line1']; ?>" maxlength="255" style="width:400px;" /><br/>
    	<label for="address_line2">Line 2</label><input name="address_line2" id="address_line2" type="text" value="<?php echo $site['address_line2']; ?>" maxlength="255" style="width:400px;" /><br/>
    	<label for="address_city">City</label><input name="address_city" id="address_city" type="text" value="<?php echo $site['address_city']; ?>" maxlength="255" style="width:250px;" />
    	<label for="address_stateregion">State / Region</label><input name="address_stateregion" id="address_stateregion" type="text" value="<?php echo $site['address_stateregion']; ?>" maxlength="255" style="width:115px;" /><br/>
    	<label for="address_zip">Zip</label><input name="address_zip" id="address_zip" type="text" value="<?php echo $site['address_zip']; ?>" maxlength="10" style="width:100px;" />
    	<label for="address_country">Country</label><input name="address_country" id="address_country" type="text" value="<?php echo $site['address_country']; ?>" maxlength="64" style="width:265px;" />
    </td>
  </tr>
  <tr>
  	<td>
        <h2>Contact Phone</h2>
        <input name="contact_phone" type="text" value="<?php echo $site['contact_phone']; ?>" maxlength="16" style="width:400px;" />
    </td>
  	<td>
    </td>
  </tr>
  <tr>
  	<td colspan="2">
    	<h2>Metadata</h2>
    	<label for="site_description">Brief Site Description</label><input name="site_description" id="site_description" type="text" value="<?php echo $site['site_description']; ?>" maxlength="255" style="width:500px;" /><br/>
        <label for="metadata">Custom Meta Tags</label><textarea name="metadata" id="metadata" rows="15" cols="80" class="materialize-textarea"><?php echo $site['meta_tags']; ?></textarea><br>
    </td>
  </tr>
  <tr>
  	<td colspan="2">
    	<h2>Website Footer Content</h2>
        <textarea name="foot_content" id="foot_content" rows="15" cols="80" class="materialize-textarea"><?php echo $site['footer_content']; ?></textarea><br>
    </td>
  </tr>
  <tr>
  	<td colspan="2"><input name="chng_info" type="submit" class="btn green" value="Change Website Info" /></td>
  </tr>
</table>
</form>
</div>           
<?php } ?>
<?php if(check_permission("Website","edit_site_colors")){ ?>
<div class="TabbedPanelsContent">
<h1 style="margin:-4px -4px 5px -4px; padding:5px;">Custom CSS/JS</h1>
<div class="row">
<div class="input-field col s12">
	<form method="post" action="site-settings?tab=1">
		<h2>Custom CSS</h2>
		<textarea class="materialize-textarea" name="custom_css" id="custom_css" rows="15" cols="80"><?php echo $layout['custom_css']; ?></textarea>
		<h2>Custom Javascript</h2>
	    <textarea class="materialize-textarea" name="custom_js" id="custom_js" rows="15" cols="80"><?php echo $site['custom_js']; ?></textarea><br>
		<input name="cust_css_js" type="submit" class="btn green" value="Save Custom CSS/JS" />
	</form>
</div>
</div>
</form>
</div>
<?php } ?>             
<?php if(check_permission("Website","upload_favicon_banner")){ ?>
<div class="TabbedPanelsContent">
<h1 style="margin:-4px -4px 5px -4px; padding:5px;">Website Imagery</h1><br>
<h2>Upload Banner</h2>
<form method="post" enctype="multipart/form-data" action="site-settings.php?tab=2">
	<input type="file" name="file" id="file" accept="image/*" /><br>
    *Recommended image size is 1510 pixels high by 800 pixels wide. Max filesize 2MB.
	<input name="uploadbanner" type="submit" class="btn green" value="Upload selected banner" />
</form><br>
<?php
	if($banner != false){
		?><img src="../images/banner/<?php echo $banner; ?>" width="500" /><?php
	}else{?>
		<div style="font-size:20px; width:500px; height:200px; border:5px dashed #B1B1B1; text-align:center; line-height:200px; vertical-align:middle; margin-left:auto; margin-right:auto;">There is currently no banner image.</div>
    <?php
	}
?>
<br><br>
<a href="site-settings.php?tab=2&delete=banner">[Delete Banner]</a><br><br>
<h2>Upload Background Image</h2>
<form method="post" enctype="multipart/form-data" action="site-settings.php?tab=2">
	<input type="file" name="file" id="file" accept="image/*" /><br>
    Max filesize 4MB.
	<input name="uploadbg" type="submit" class="btn green" value="Upload selected background" />
</form><br>
<?php
	if($bg != false){
		?><img src="../images/bg/<?php echo $bg; ?>" width="500" /><br><br><?php
	}else{?>
		<div style="font-size:20px; width:500px; height:200px; border:5px dashed #B1B1B1; text-align:center; line-height:200px; vertical-align:middle; margin-left:auto; margin-right:auto;">There is currently no background image.</div>
    <?php
	}
?>
<form method="post" action="site-settings?tab=2">
	<h3>Background Repeat</h3>
	<select name="bg_repeat">
    	<option value="repeat"<?php if($layout['bg_repeat']=='repeat'){echo " selected";} ?>>Tile</option>
        <option value="repeat-x"<?php if($layout['bg_repeat']=='repeat-x'){echo " selected";} ?>>Horizontally</option>
        <option value="repeat-y"<?php if($layout['bg_repeat']=='repeat-y'){echo " selected";} ?>>Vertically</option>
        <option value="no-repeat"<?php if($layout['bg_repeat']=='no-repeat'){echo " selected";} ?>>No repeat</option>
    </select>
	<h3>Background Position</h3>
	<select name="bg_position">
        <option value="left top"<?php if($layout['bg_position']=='left top'){echo " selected";} ?>>left top</option>
        <option value="left center"<?php if($layout['bg_position']=='left center'){echo " selected";} ?>>left center</option>
        <option value="left bottom"<?php if($layout['bg_position']=='left bottom'){echo " selected";} ?>>left bottom</option>
        <option value="right top"<?php if($layout['bg_position']=='right top'){echo " selected";} ?>>right top</option>
        <option value="right center"<?php if($layout['bg_position']=='right center'){echo " selected";} ?>>right center</option>
        <option value="right bottom"<?php if($layout['bg_position']=='right bottom'){echo " selected";} ?>>right bottom</option>
        <option value="center top"<?php if($layout['bg_position']=='center top'){echo " selected";} ?>>center top</option>
        <option value="center center"<?php if($layout['bg_position']=='center center'){echo " selected";} ?>>center center</option>
        <option value="center bottom"<?php if($layout['bg_position']=='center bottom'){echo " selected";} ?>>center bottom</option>
    </select>
    <h3>Background Size</h3>
	<select name="bg_size">
    	<option value="auto"<?php if($layout['bg_size']=='auto'){echo " selected";} ?>>Original image size</option>
        <option value="cover"<?php if($layout['bg_size']=='cover'){echo " selected";} ?>>Fill</option>
    </select>
    <h3>Background fixed</h3>
    <input name="bg_fixed" type="checkbox"<?php if($layout['bg_fixed']==1){echo " checked";} ?> id="bg_fixed" /><label for="bg_fixed"></label>
    <h3>Use background color</h3>
    <input name="use_bg_color" type="checkbox"<?php if($layout['use_bg_color']==1){echo " checked";} ?> id="use_bg_color" /><label for="use_bg_color"></label><br/><br/>
    <input name="bg_submit" type="submit" class="btn green" value="Save background settings" />
</form>
<br/><br/>
<a href="site-settings?tab=2&delete=bg">[Delete Background]</a>
<br><br/><br/><h2>Upload Logo</h2>
<form method="post" enctype="multipart/form-data" action="site-settings.php?tab=2">
	<input type="file" name="file" id="file" accept="image/*" /><br>
    *Recommended image size is 100 pixels high and maximum 600 pixels wide. Max filesize 2MB.
	<input name="uploadlogo" type="submit" class="btn green" value="Upload selected logo" />
</form><br/>
<?php
	if($logo != false){
		?><img src="../images/logo/<?php echo $logo; ?>" height="150" /><?php
	}else{?>
		<div style="font-size:20px; width:350px; height:100px; border:5px dashed #B1B1B1; text-align:center; line-height:100px; vertical-align:middle; margin-left:auto; margin-right:auto;">There is currently no logo image.</div>
    <?php
	}
?>
<br><br>
<a href="site-settings?tab=2&delete=logo">[Delete Logo]</a>
<!-- <form method="post" action="site-settings.php?tab=2">
    <h2>Logo URL</h2>
    <input name="logo_url" type="text" value="<?php echo $site['logo_url']; ?>" maxlength="256" placeholder="http://" style="width:300px;" /><input name="chng_logo_url" type="submit" class="btn green" value="Change Logo URL" />
</form><br><br> -->
<br><h2>Upload Favicon</h2>
<form method="post" enctype="multipart/form-data" action="site-settings.php?tab=2">
	<input type="file" name="file" id="file" accept="image/*" />
	<input name="uploadfavicon" type="submit" class="btn green" value="Upload selected favicon (128KB max)" />
</form><br/>
<?php
	if($favicon != false){
		?><img src="../images/favicon/<?php echo $favicon; ?>" /><?php
	}else{?>
		<div style="font-size:20px; width:32px; height:32px; border:2px dashed #B1B1B1; text-align:center; vertical-align:middle;"></div>
    <?php
	}
?><br/>
<a href="site-settings?tab=2&delete=favicon">[Delete Favicon]</a>
</div>
<?php } ?>    
<?php if(check_permission("Website","edit_socnet")){ ?>
<div class="TabbedPanelsContent">
<h1 style="margin:-4px -4px 5px -4px; padding:5px;">Social Networks</h1>
<form method="post" enctype="multipart/form-data" action="site-settings.php?tab=3">
	<?php 
	$i = 0;
	foreach($GLOBALS['soc_networks'] as $network){ ?>
    	<input type="text" maxlength="256" style="width:400px;" id="<?php echo $GLOBALS['soc_networks_names'][$i]; ?>" placeholder="http://" value="<?php echo $GLOBALS['site_info'][$network.'_url']; ?>" name="<?php echo $network; ?>_url" /><label for="<?php echo $GLOBALS['soc_networks_names'][$i]; ?>>"><input name="<?php echo $network; ?>_enabled" id="<?php echo $network; ?>_enabled" type="checkbox"<?php if($GLOBALS['site_info'][$network.'_enabled']){echo  "checked";} ?> /><label for="<?php echo $network; ?>_enabled">Enabled</label><br><br>
    <?php 
		$i++;
	} ?>
    <input name="socialnet" type="submit" value="Save" class="green btn" /><br><br>
</form>
</div>
<?php } ?>             
  <?php if(check_permission("Website","edit_google_analytics")){ ?>
  <div class="TabbedPanelsContent">
  <h1 style="margin:-4px -4px 5px -4px; padding:5px;">Google Analytics</h1>
  <form method="post" action="site-settings.php?tab=4">
    <input name="analyticsenabled" id="analyticsenabled" type="checkbox"<?php if($site['g_analytics_enabled']){echo  "checked";} ?> /><label for="analyticsenabled">Enabled</label><br>
  	Google Analytics Code:<br>
	<textarea name="analyticscode" id="analytics" style="height:200px;" rows="15" cols="80" ><?php echo $site['g_analytics_code']; ?></textarea><br>
    <input name="chngganalytics" type="submit" class="btn green" value="Change Google Analytics Settings" />
  </form>
  </div>
  <?php } ?>
  <div class="TabbedPanelsContent">
      <div style="width:100%; text-align:center;">
          <h1 style="margin:-4px -4px 5px -4px; padding:5px;">About IlluminateCMS</h1><br>
            <img src="images/logo.png" alt="Logo" /><br><br>
            <h3>IlluminateCMS</h3>
            by<br>
            <a href="http://secondgenerationdesign.com" target="_blank"><strong>Second Gen Design</strong></a><br><br>
            Website Version: <?php echo $site_version; ?><br>
            Database Version: <?php echo $GLOBALS['site_info']['version']; ?><br><br>
            (Backwards compatable to database version <?php echo $db_compatability; ?>)
      </div>
  </div>
<script type="text/javascript">
	var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");

	function changeTab(id){
		TabbedPanels1.showPanel(id);
		return false;
	};
	<?php if(isset($_GET['tab'])){?>changeTab(<?php echo $_GET['tab'];?>);<?php } ?>
</script>
<?php require_once("includes/end_cpanel.php"); ?>