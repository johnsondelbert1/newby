<?php
if($GLOBALS['site_info']['published']==0&&!check_permission("Website","unpublished_access")){
	redirect_to($GLOBALS['HOST']."/under_construction.php");
}

if(isset($_GET['error'])){
	$error=urldecode($_GET['error']);
}
if(isset($_GET['success'])){
	$success=urldecode($_GET['success']);
}
if(isset($_GET['message'])){
	$message=urldecode($_GET['message']);
}

//Re-create folders
foreach ($folders as $folder){
	if(!file_exists($folder)){
		mkdir($folder);
	}
}

$banner = scandir("images/banner/");
if(isset($banner[2])){
	$banner = $banner[2];
}else{
	$banner = false;
}
$favicon = scandir("images/favicon/");
if(isset($favicon[2])){
	$favicon = $favicon[2];
}else{
	$favicon = false;
}
$logo = scandir("images/logo/");
if(isset($logo[2])){
	$logo = $logo[2];
}else{
	$logo = false;
}
$bg = scandir("images/bg/");
if(isset($bg[2])){
	$bg = $bg[2];
}else{
	$bg = false;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <title><?php echo $pgsettings['title']; ?></title>
    
    <META NAME="description" CONTENT="<?php echo $GLOBALS['site_info']['site_description']; ?>">
    <META NAME="robot" CONTENT="index,follow">
    <META NAME="copyright" CONTENT="Source Code Property of Second Generation Design, Copyright © 2011-<?php echo date("Y"); ?>">
    <META NAME="author" CONTENT="2GD - Secondgenerationdesign.com">
    <META NAME="language" CONTENT="English">
    <META NAME="revisit-after" CONTENT="7">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="theme-color" content="<?php echo get_color('android_tab'); ?>">

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
    
    <!-- Start custom meta tags -->
    <?php if($GLOBALS['site_info']['meta_tags']!=""){
		echo $GLOBALS['site_info']['meta_tags'];
	}?>
    <!-- End custom meta tags -->

    <link href="<?php echo $GLOBALS['HOST']; ?>/materialize/css/materialize.css" rel="stylesheet" type="text/css" media="screen,projection"/>
    <link href="<?php echo $GLOBALS['HOST']; ?>/styles/materialize-override.css" rel="stylesheet" type="text/css" media="screen,projection"/>
    <link href="<?php echo $GLOBALS['HOST']; ?>/administrator/styles/fonts.css" rel="stylesheet" type="text/css" />
    <?php if($favicon!=false){ ?><link rel="shortcut icon" href="<?php echo $GLOBALS['HOST']; ?>/images/favicon/<?php echo $favicon; ?>" /><?php } ?>
    <link href="<?php echo $GLOBALS['HOST']; ?>/styles/main.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['HOST']; ?>/styles/animate.css" rel="stylesheet" type="text/css" />
    <!-- <link href="<?php echo $GLOBALS['HOST']; ?>/styles/materialize.css" rel="stylesheet" type="text/css" media="screen,projection"/> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?php echo $GLOBALS['HOST']; ?>/prettyphoto/css/prettyPhoto.css" rel="stylesheet" type="text/css" media="screen" charset="utf-8" />
    <script src="<?php echo $GLOBALS['HOST']; ?>/jscripts/SpryTabbedPanels.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['HOST']; ?>/materialize/js/materialize.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['HOST']; ?>/prettyphoto/js/jquery.prettyPhoto.js" charset="utf-8"></script>
    <script src="<?php echo $GLOBALS['HOST']; ?>/jscripts/autosize.js"></script>
    <style type="text/css">
    .forumtitle, tr.heading,td.heading, th.heading{
			background-color:<?php echo $GLOBALS['site_layout']['sitebg_color']; ?> !important;
			color:<?php echo $GLOBALS['site_layout']['text_color']; ?> !important;
		}

		body{
			<?php if($GLOBALS['site_layout']['use_bg_color']!=1){ ?>
			background-color:#FFFFFF; !important;
			<?php }
			if($bg!=false){?>
			background-image:url(/images/bg/<?php echo $bg; ?>);
			<?php }
			if($GLOBALS['site_layout']['bg_repeat']!=''){ ?>
			background-repeat:<?php echo $GLOBALS['site_layout']['bg_repeat'] ?> !important;
			<?php }
			if($GLOBALS['site_layout']['bg_position']!=''){ ?>
			background-position:<?php echo $GLOBALS['site_layout']['bg_position'] ?> !important;
			<?php }
			if($GLOBALS['site_layout']['bg_size']!=''){ ?>
			background-size:<?php echo $GLOBALS['site_layout']['bg_size'] ?> !important;
			<?php }
			if($GLOBALS['site_layout']['bg_fixed']==1){ ?>
			background-attachment:fixed !important;
			<?php }else{ ?>
			background-attachment:scroll !important;
			<?php } ?>
		}
		.btn-floating{
			z-index: 0;
		}
		<?php
		//Custom Colors

		$query="SELECT DISTINCT `style_color_id` FROM `css_selectors` ORDER BY `style_color_id` ASC";
		$result=mysqli_query( $connection, $query);

		while ($unique_id = mysqli_fetch_array($result)) {
			$css_query="SELECT * FROM `css_selectors` WHERE `style_color_id` = {$unique_id['style_color_id']}";
			$css_result=mysqli_query( $connection, $css_query);

			$color_query="SELECT `color_hex` FROM `style_colors` WHERE `cid` = {$unique_id['style_color_id']}";
			$color_result=mysqli_query( $connection, $color_query);
			$color = mysqli_fetch_array($color_result);

			$bg = array();
			$text = array();

			while ($css_selector = mysqli_fetch_array($css_result)){
				switch ($GLOBALS['color_styles'][$css_selector['s_name']]['type']) {
					case 'bg':
						array_push($bg, $css_selector['s_name']);
						break;
					case 'text':
						array_push($text, $css_selector['s_name']);
						break;
				}
			}

			if(!empty($bg)){
				$items = count($bg);
				$i = 1;
				foreach ($bg as $value) {
					echo $GLOBALS['color_styles'][$value]['selector'];
					if($i < $items){echo ', ';}else{echo "{\r\n";}
					$i++;
				}
				echo "\t".'background-color: '.$color['color_hex'].';'."\r\n}\r\n";
			}
			if(!empty($text)){
				$items = count($text);
				$i = 1;
				foreach ($text as $value) {
					echo $GLOBALS['color_styles'][$value]['selector'];
					if($i < $items){echo ', ';}else{echo "{\r\n";}
					$i++;
				}
				echo "\t".'color: '.$color['color_hex'].';'."\r\n}\r\n";
			}
		}

		?>
		::selection{
			background-color: <?php echo get_color('selection'); ?>;
		}
		::-moz-selection{
			background-color: <?php echo get_color('selection'); ?>;
		}
		::-webkit-input-placeholder {
		   color: <?php echo get_color('input_placeholder'); ?>;
		}
		:-moz-placeholder { /* Firefox 18- */
		   color: <?php echo get_color('input_placeholder'); ?>;  
		}
		::-moz-placeholder {  /* Firefox 19+ */
		   color: <?php echo get_color('input_placeholder'); ?>;  
		}
		:-ms-input-placeholder {  
		   color: <?php echo get_color('input_placeholder'); ?>;  
		}
		/*#contentwrap{
			background-color:<?php echo $GLOBALS['site_layout']['contentbg_color'] ?> !important;
		}*/
/*		.mobile, ul.side-nav, slide-out, .title, .jssorb01 div:hover, .jssorb01 .av:hover, jssora05l, jssora05r{
			background-color: #FFFFFF;
		}*/
		/*.mobile, ul.side-nav, slide-out, .blogtitle{
			background-color:<?php echo $GLOBALS['site_layout']['accent_color'] ?> !important;
		}
		.jssora05l .material-icons, .jssora05r .material-icons{
			color:<?php echo $GLOBALS['site_layout']['menu_color'] ?> !important;
		}
		.mobile i{
			color:<?php echo $GLOBALS['site_layout']['contentbg_color'] ?> !important;
		}
		#banner{
			background-color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		#horiz-menu, #horiz-menu ul, #horiz-menu ul li, #vert-menu ul li, .photo-link, .nav, .captionBlack, .card{
			background-color:<?php echo $GLOBALS['site_layout']['menu_color'] ?> !important;
			color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		#horiz-menu li:hover > a, #horiz-menu li:hover > ul, #horiz-menu ul a:hover, #vert-menu li:hover > a, #vert-menu li:hover > ul, #vert-menu ul a:hover,  .TabbedPanelsTab, .accent, .forum tr:hover, .forumsuser, ul.MenuBarHorizontal a:hover, ul.MenuBarHorizontal a:focus, .forumfooter, ul.side-nav li{
			background-color:<?php echo $GLOBALS['site_layout']['accent_color'] ?> !important;
			color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		*html #horiz-menu li a:hover, #vert-menu li a:hover { /* IE6 only 
			color: <?php echo $GLOBALS['site_layout']['text_color'] ?>;
		}
		#content a, #footer a, #horiz-menu a, #horiz-menu ul a, #vert-menu a, #vert-menu ul a, .blogtitle a, .forum tr a:hover{
			color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		.photo-link:hover{
			background-color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		/*#content, .forum tr, .forumbody, #footerwrap, .blog{
			background-color:<?php echo $GLOBALS['site_layout']['contentbg_color'] ?> !important;
			color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		.forum tr a{
			color:<?php echo $GLOBALS['site_layout']['contentbg_color'] ?> !important;
		}
		.forumtitle{
			border-bottom:2px <?php echo $GLOBALS['site_layout']['text_color'] ?> dashed;
		}
		.icon{
			color:<?php echo $GLOBALS['site_layout']['menu_color'] ?> ;
		}
		 ::-webkit-scrollbar-thumb{
			/*background-color:<?php echo $GLOBALS['site_layout']['accent_color'] ?> !important;
			color:<?php echo $GLOBALS['site_layout']['text_color'] ?> !important;
		}
		::selection {
			background: <?php echo $GLOBALS['site_layout']['accent_color'] ?>; /* WebKit/Blink Browsers 
			}
		::-moz-selection {
			background: <?php echo $GLOBALS['site_layout']['accent_color'] ?>; /* WebKit/Blink Browsers 
			}*/
		#banner{
			<?php if(count($banner)==3){?>background-image:url(images/banner/<?php echo $banner[2];?>);<?php } ?>
			
		}
		/* Start custom CSS */
		<?php if($GLOBALS['site_layout']['custom_css']!=""){
			echo $GLOBALS['site_layout']['custom_css'];
		}?>
		/* End custom CSS */
	</style>
    <!-- Start custom JS -->
    <?php if($GLOBALS['site_info']['custom_js']!=""){
		echo $GLOBALS['site_info']['custom_js'];
	}?>
    
    <!-- End custom JS -->
</head>


<body>
	<?php
    if(isset($pgsettings['use_google_analytics'])&&$pgsettings['use_google_analytics'] == 1){
		if($GLOBALS['site_info']['g_analytics_enabled']==1){
			echo $GLOBALS['site_info']['g_analytics_code'];
		}
	}?>
    
	<div id="wrapper">
    	<?php nav("mobile", $pgsettings['pageselection']); ?>
        <div class="mobile">
            <a href="#" data-activates="slide-out" class="button-collapse" style="color:#6C6C6C;"><i class="mdi-navigation-menu"></i></a>
            <div style="float:right; font-size:32px; padding:10px;">
				<?php
                    foreach($GLOBALS['enabled_soc_networks'] as $network){?>
                        <a href="<?php echo $GLOBALS['site_info'][$network.'_url']; ?>" style="text-decoration:none;" target="_blank"><span class="icon icon-<?php echo $GLOBALS['soc_networks_icons'][array_search($network, $GLOBALS['soc_networks'])]; ?>"></span></a>
                    <?php 
                    }
                ?>
            </div>
        </div>
    	<?php if($logo!=false||!empty($GLOBALS['enabled_soc_networks'])){ ?>
    	<div class="header"><span style="font-size:47px; margin-left:15px;" class="icon icon-facebook2 headericon"></span>
        	<?php if($logo!=false){ ?>
            	<div style="float:left; position:absolute;" id="logo">
                	<?php if($GLOBALS['site_info']['logo_url']!=''){?>
            		<a href="<?php echo $GLOBALS['HOST']; ?>"><img src="<?php echo $GLOBALS['HOST']; ?>/images/logo/<?php echo $logo; ?>" alt="<?php echo $GLOBALS['site_info']['name']; ?> Logo" height="100" /></a>
                    <?php }else{ ?>
                    <img src="<?php echo $GLOBALS['HOST']; ?>/images/logo/<?php echo $logo; ?>" height="100" />
                    <?php } ?>
                </div>
			<?php } ?>
            <div style="float:right; font-size:32px; padding:10px;" id="socnet">
            	<?php
					foreach($GLOBALS['enabled_soc_networks'] as $network){?>
                    	<a href="<?php echo $GLOBALS['site_info'][$network.'_url']; ?>" style="text-decoration:none;" target="_blank"><span class="icon icon-<?php echo $GLOBALS['soc_networks_icons'][array_search($network, $GLOBALS['soc_networks'])]; ?>"></span></a>
					<?php 
					}
				?>
            </div>
        </div>
        <?php } ?>
        <?php
        if(!isset($page['horiz_menu_visible'])||$page['horiz_menu_visible'] == true){
        	$num_horiz_pages = nav("horiz", $pgsettings['pageselection']);
		}
		?>
        <div id="contentwrap">
        
			<?php if(isset($pgsettings['banner'])&&$pgsettings['banner'] == 1){ ?>
    		<?php if($banner!=false){ ?><div class="row"><div class="col l10 s12 offset-l1" style="padding:0 0 !important;"><img src="<?php echo $GLOBALS['HOST']; ?>/images/banner/<?php echo $banner; ?>" width="100%" style="background-color:#C9C9C9;" /></div></div><?php } ?>
			<?php
			}
			?>
			<?php if(isset($pgsettings['slider'])&&$pgsettings['slider'] >= 1){
				slider($pgsettings['slider']);
			}
			?>
            <div class="container">
                <div class="row">
                    <?php
                    if(!isset($page['vert_menu_visible'])||$page['vert_menu_visible'] == true){
                        ?><?php
                            $num_vert_pages = nav("vert", $pgsettings['pageselection']);
                        ?><?php
                    }else{
						$num_vert_pages = 0;
					}
                    ?>
                  
                	<div class="col <?php
					if($num_vert_pages == 0){
									echo 'l12';	
									}
						else{
									echo 'l9';	
									}
					 ?> s12" >
                    <div id="content">
						<div class="card custom">