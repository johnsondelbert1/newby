<?php
require_once("includes/functions.php");
if($GLOBALS['site_info']['published']==1||check_permission("Website","unpublished_access")){
	redirect_to($GLOBALS['HOST']."/index.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Coming Soon</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link href="styles/under_construction.css" rel="stylesheet" type="text/css" />
	<link href="styles/960.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script> 
	<script type="text/javascript" src="js/cufon-yui.js"></script>
	<script type="text/javascript" src="js/Clarendon_LT_Std_700.font.js"></script>
	<script type="text/javascript">
		Cufon.replace('h1,h3', {fontFamily: 'Clarendon LT Std', hover:true})
	</script>
</head>
<body>
  <div style="position: absolute; top: 0; right: 0; width: 100px; text-align:right; margin:10px;">
    <a href="administrator/login.php" style="color:#FFFFFF;">[Sign In]</a>
  </div>
	<div id="shim"></div>
	<div id="content">
		<div class="logo_box">
		<?php
			$logo = scandir("images/logo/");
			if(isset($logo[2])){
				$logo = $logo[2];
				?><img src="<?php echo $GLOBALS['HOST']; ?>/images/logo/<?php echo $logo; ?>" alt="<?php echo $GLOBALS['site_info']['name']; ?> Logo" width="350" /><?php
			}
		?>
	    </div>          
		<div class="main_box">
			<h2>Our website is coming soon.<br/><span>In the mean time connect with us with the information below</span></h2>
			
			<ul class="info">
				<?php if($GLOBALS['site_info']['contact_phone'] != ''){?>
				<li>
					<h3>P</h3>
					<p><?php echo $GLOBALS['site_info']['contact_phone'];?></p>
				</li>
				<?php } ?>
				<?php if($GLOBALS['site_info']['contact_email'] != ''){?>
				<li>
					<h3>A</h3>
						<p>
					    <?php echo $GLOBALS['site_info']['address_line1'];if($GLOBALS['site_info']['address_line2']!=''){echo ', '.$GLOBALS['site_info']['address_line2'];}?><br/>
		                <?php echo $GLOBALS['site_info']['address_city'];if($GLOBALS['site_info']['address_stateregion']!=''){echo ', '.$GLOBALS['site_info']['address_stateregion'];} echo ' '.$GLOBALS['site_info']['address_zip'];?><br/>
		                <?php echo $GLOBALS['site_info']['address_country'];?>
		                </p>
				</li>
				<?php } ?>
				<?php if($GLOBALS['site_info']['contact_email'] != ''){?>
				<li>
					<h3>E</h3>
					<p><?php echo $GLOBALS['site_info']['contact_email'];?></p>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>

</body>
</html>