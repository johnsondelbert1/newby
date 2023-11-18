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
		"title" => $GLOBALS['site_info']['name']." CPanel 404",
		"icon" => "icon-dashboard"
	);
	require_once("includes/begin_cpanel.php");
	?>
<h2>404 File Not found</h2>
<?php
	require_once("includes/end_cpanel.php");
?>