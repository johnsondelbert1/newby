<?php
require_once("../includes/functions.php");
confirm_logged_in();
if(!check_permission("Website","cpanel_access")){
	redirect_to("login.php?error=".urlencode('You do not have Cpanel Access'));
}
?>
<?php
$query="SELECT * FROM  `features` WHERE  `id` =  1";
$result=mysqli_query($connection, $query);
$feature=mysqli_fetch_array($result);

$query="SELECT * FROM  `galleries`";
$galleryresult=mysqli_query($connection, $query);
?>
<?php
	$pgsettings = array(
		"title" => $site_info['name']." CPanel",
		"icon" => "icon-dashboard"
	);
	require_once("includes/begin_cpanel.php");
	?>
<h2><a href="../" target="_blank">Back to Website</a></h2>
<?php
if(check_permission("Website","edit_veterans")){
  echo '<h2><a href="newby-veterans/" target="_blank">Veterans Database</a></h2>';
}
?>
<br />
<!--<h2>Social Networks</h2>
<br />
<form method="post" action="control_panel.php">
<table width="50%" border="0">
  <tr>
    <td colspan="2"><h3>Twitter</h3></td>
  </tr>
  <tr>
    <td>Use Twitter feed:</td>
    <td><input name="twitterfeed" type="checkbox" value="" <?php if($feature['twitterfeed']==1){echo "checked";} ?> /></td>
  </tr>
  <tr>
    <td>Twitter Username:</td>
    <td><input name="twitteruser" type="text" value="<?php echo $feature['twitteruser']; ?>" maxlength="100" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input name="updatesocnet" type="submit" value="Update Social Networks" class="submit"/></td>
  </tr>
</table>
</form><br />-->
<?php
	require_once("includes/end_cpanel.php");
?>