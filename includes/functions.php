<?php
require_once("session.php");
require_once("globals.php");

//global DB variables

//Site Info
$query="SELECT * FROM `site_info` WHERE `id` = 1";
$result=mysqli_query( $connection, $query);
$GLOBALS['site_info']=mysqli_fetch_array($result);

$GLOBALS['HOST']=$GLOBALS['HOST'];

//Layout
$query="SELECT * FROM `site_layout` WHERE `id` = 1";
$result=mysqli_query( $connection, $query);
$GLOBALS['site_layout']=mysqli_fetch_array($result);

//Social Networks
$GLOBALS['soc_networks'] = array('facebook','googleplus','twitter','instagram','linkedin');
$GLOBALS['soc_networks_names'] = array('Facebook','Google +','Twitter','Instagram','LinkedIn');
$GLOBALS['soc_networks_icons'] = array('facebook2','googleplus3','twitter2','instagram','linkedin');
$GLOBALS['enabled_soc_networks'] = array();
foreach($GLOBALS['soc_networks'] as $network){
	if($GLOBALS['site_info'][$network.'_enabled'] == 1){
		array_push($GLOBALS['enabled_soc_networks'], $network);
	}
}

//Date with timezone
date_default_timezone_set($GLOBALS['site_info']['timezone']);
$GLOBALS['date']=date("Y-m-d H:i:s", time());

//Get Blog page name
$query="SELECT * FROM `pages` WHERE `type` = 'Blog'";
$result=mysqli_query( $connection, $query);
$pg=mysqli_fetch_array($result);
$GLOBALS['blog_page'] = $pg['name'];

//Get Forum page name
$query="SELECT * FROM `pages` WHERE `type` = 'Forum'";
$result=mysqli_query( $connection, $query);
$pg=mysqli_fetch_array($result);
$GLOBALS['forum_page'] = $pg['name'];

//Folders to be re-created if missing
$folders = array('images/banner/', 'images/bg/', 'images/favicon/', 'images/logo/', 'blog_galleries/', 'galleries/');

//Site color styling
$GLOBALS['color_styles'] = array(
	"website_bg" => array(
		"disp_name" => 'Website Background',
		"selector" => 'body',
		"type" => 'bg',
	),
	"content_bg" => array(
		"disp_name" => 'Content Background',
		"selector" => '#content .custom',
		"type" => 'bg',
	),
	"content_text" => array(
		"disp_name" => 'Content Text',
		"selector" => '#content .custom',
		"type" => 'text',
	),
	"content_link" => array(
		"disp_name" => 'Content Link',
		"selector" => '#content .custom a',
		"type" => 'text',
	),
	"menu_bg" => array(
		"disp_name" => 'Menu Background',
		"selector" => '#horiz-menu, #horiz-menu ul, #horiz-menu ul li, #vert-menu, #vert-menu li, #vert-menu ul li, .nav, .side-nav, .side-nav .collapsible-header, .side-nav li:hover, .side-nav li.active',
		"type" => 'bg',
	),
	"menu_bg_hover" => array(
		"disp_name" => 'Menu BG Hover',
		"selector" => '#horiz-menu li:hover > a, #horiz-menu li:hover, #horiz-menu ul a:hover, #vert-menu li:hover > a, #vert-menu li:hover > ul, #vert-menu ul a:hover, .side-nav .collapsible-body',
		"type" => 'bg',
	),
	"menu_text" => array(
		"disp_name" => 'Menu Text',
		"selector" => '#horiz-menu a, #vert-menu a, .side-nav a, .side-nav .mdi-navigation-arrow-drop-down, .mobile-login',
		"type" => 'text',
	),
	"menu_text_hover" => array(
		"disp_name" => 'Menu Text Hover',
		"selector" => '#horiz-menu li:hover > a, #horiz-menu li:hover, #horiz-menu ul a:hover, #vert-menu li:hover > a, #vert-menu li:hover > ul, #vert-menu ul a:hover',
		"type" => 'text',
	),
	"icon" => array(
		"disp_name" => 'Social Network Icons',
		"selector" => '.icon, .mobile i',
		"type" => 'text',
	),
	"footer_bg" => array(
		"disp_name" => 'Footer Background',
		"selector" => '#footerwrap',
		"type" => 'bg',
	),
	"footer_text" => array(
		"disp_name" => 'Footer Text',
		"selector" => '#footer',
		"type" => 'text',
	),
	"footer_link" => array(
		"disp_name" => 'Footer Link Text',
		"selector" => '#footer a',
		"type" => 'text',
	),
	"header_bg" => array(
		"disp_name" => 'Header Background',
		"selector" => '.mobile-logo, .mobile',
		"type" => 'bg',
	),
	"blog_title_bg" => array(
		"disp_name" => 'Blog Title BG',
		"selector" => '#blog .title',
		"type" => 'bg',
	),
	"blog_title_text" => array(
		"disp_name" => 'Blog Title Text',
		"selector" => '#blog .title a',
		"type" => 'text',
	),
	"blog_body_bg" => array(
		"disp_name" => 'Blog Body BG',
		"selector" => '#blog .card',
		"type" => 'bg',
	),
	"blog_body_text" => array(
		"disp_name" => 'Blog Body Text',
		"selector" => '#blog .card',
		"type" => 'text',
	),
	"android_tab" => array(
		"disp_name" => 'Android Tab',
		"selector" => '',
		"type" => '',
	),
	"slider_arrows" => array(
		"disp_name" => 'Slider Arrows',
		"selector" => '.large',
		"type" => 'text',
	),
	"slider_bullets" => array(
		"disp_name" => 'Slider Bullets',
		"selector" => '.jssorb01 div',
		"type" => 'bg',
	),
	"slider_bullet_hover" => array(
		"disp_name" => 'Slider Bullet Hover',
		"selector" => '.jssorb01 div:hover',
		"type" => 'bg',
	),
	"slider_active_bullet" => array(
		"disp_name" => 'Slider Active Bullet',
		"selector" => '.jssorb01 .av',
		"type" => 'bg',
	),
	"slider_cap_bg" => array(
		"disp_name" => 'Slider Caption BG',
		"selector" => '.captionBlack',
		"type" => 'bg',
	),
	"slider_cap_text" => array(
		"disp_name" => 'Slider Caption Text',
		"selector" => '.captionBlack, .captionBlack a',
		"type" => 'text',
	),
	"selection" => array(
		"disp_name" => 'Selection',
		"selector" => '',
		"type" => '',
	),
	"input_textbox_bg" => array(
		"disp_name" => 'Textbox BG',
		"selector" => 'input[type=text], input[type=url], input[type=tel], input[type=number], input[type=color], input[type=email], input[type=password], textarea',
		"type" => 'bg',
	),
	"input_textbox_text" => array(
		"disp_name" => 'Textbox Text',
		"selector" => 'input[type=text], input[type=url], input[type=tel], input[type=number], input[type=color], input[type=email], input[type=password], textarea',
		"type" => 'text',
	),
	"input_placeholder" => array(
		"disp_name" => 'Textbox Placeholder',
		"selector" => '',
		"type" => '',
	),
);
//Add new selectors to DB
foreach ($GLOBALS['color_styles'] as $key => $val){
	$addquery="SELECT * FROM `css_selectors` WHERE `s_name` = '{$key}'";
	$addresult=mysqli_query( $connection, $addquery);
	if(mysqli_num_rows($addresult)==0){
		$insertquery="INSERT INTO `css_selectors` (`s_name`, `style_color_id`) VALUES ('{$key}', '1')";
		$insertresult=mysqli_query( $connection, $insertquery);
	}
}
//Removes non-existing selectors from DB
$removequery="SELECT * FROM `css_selectors`";
$removeresult=mysqli_query( $connection, $removequery);
while($css_selector=mysqli_fetch_array($removeresult)){
	if(!array_key_exists($css_selector['s_name'], $GLOBALS['color_styles'])){
		$delquery = "DELETE FROM `css_selectors` WHERE `s_name` = '{$css_selector['s_name']}'";
		$delresult=mysqli_query( $connection, $delquery);
	}
}

//Ranks & permissions
function get_rank_info(){
	//gets permissions for logged in user
	global $connection;
	$query="SELECT `rank` FROM `users` WHERE `id` = {$_SESSION['user_id']}";
	$result=mysqli_query( $connection, $query);
	$user_rank=mysqli_fetch_array($result);
	$query="SELECT * FROM `ranks` WHERE `id` = {$user_rank['rank']}";
	$result=mysqli_query( $connection, $query);
	if(mysqli_num_rows($result)!=0){
		$rank_permissions=mysqli_fetch_array($result);
		if($rank_permissions['permissions']!=""){
			$user_permissions = unserialize($rank_permissions['permissions']);
		}else{
			$user_permissions=array();
		}
	}else{
		$user_permissions = $blank_permissions;
	}
	return $user_permissions;
}

//Ranks & permissions

function enable_all_perms($blank_permissions){
	$filled_perms = $blank_permissions;
	foreach($blank_permissions as $perm_group_key => $perm_group_perms){
		foreach($perm_group_perms as $perm_key => $perm_properties){
			$filled_perms[$perm_group_key][$perm_key]['value'] = 1;
		}
	}
	return($filled_perms);
}

$blank_permissions = array(
	"Pages" => array(
		"add_pages" => array("value" => 0, "disp_name" => "Add", "description" => "Enables members of this rank to add a new page to the website"),
		"edit_pages" => array("value" => 0, "disp_name" => "Edit", "description" => "Enables members of this rank to edit existing pages on the website, edit the staff, and edit the slider banner."),
		"delete_pages" => array("value" => 0, "disp_name" => "Delete", "description" => "Enables members of this rank to delete pages on the website"),
	),
	"Blog" => array(
		"post_blog" => array("value" => 0, "disp_name" => "Post", "description" => "Enables members of this rank to post a blog."),
		"edit_blog" => array("value" => 0, "disp_name" => "Edit", "description" => "Enables members of this rank to edit existing bl0ogs."),
		"delete_blog" => array("value" => 0, "disp_name" => "Delete", "description" => "Enables members of this rank to delete."),
		"post_comment" => array("value" => 0, "disp_name" => "Post Comments", "description" => "Enables members of this rank to post comments on a blog post."),
		"delete_any_comment" => array("value" => 0, "disp_name" => "Delete Any Comment", "description" => "Enables members of this rank to delete any comment on a blog post."),
	),
	"Forum" => array(
		"add_delete_forum" => array("value" => 0, "disp_name" => "Add & Delete Forums", "description" => "Enables members of this rank to add and delete forums."),
		"edit_forum" => array("value" => 0, "disp_name" => "Edit Forum", "description" => "Enables members of this rank to edit a forum's name and description."),
		"add_thread" => array("value" => 0, "disp_name" => "Post Thread", "description" => "Enables members of this rank to post a thread in the forums."),
		"reply_to_thread" => array("value" => 0, "disp_name" => "Reply to Thread", "description" => "Enables members of this rank to reply to a thread in the forums."),
		"edit_thread" => array("value" => 0, "disp_name" => "Edit Post", "description" => "Enables members of this rank to edit their own post in the forums."),
		"pin_unpin_thread" => array("value" => 0, "disp_name" => "Pin & Unpin Thread", "description" => "Enables members of this rank to pin and unpin threads."),
		"lock_unlock_thread" => array("value" => 0, "disp_name" => "Lock & Unlock Thread", "description" => "Enables members of this rank to lock and unlock threads."),
		"delete_thread" => array("value" => 0, "disp_name" => "Delete Thread", "description" => "Enables members of this rank to delete threads from the forums."),
	),
	"Users" => array(
		"add_users" => array("value" => 0, "disp_name" => "Add Users", "description" => "Enables members of this rank to add users to the website and change those users' ranks."),
		"delete_users" => array("value" => 0, "disp_name" => "Delete Users", "description" => "Enables members of this rank to delete users from the website."),
		"approve_deny_new_users" => array("value" => 0, "disp_name" => "Approve/Deny New Users", "description" => "Enables members of this rank to approve or deny new users."),
		"ban_users" => array("value" => 0, "disp_name" => "Ban Users", "description" => "Enables members of this rank to ban users from logging into the website."),
		"create_rank" => array("value" => 0, "disp_name" => "Create Ranks", "description" => "Enables members of this rank to create ranks on the website."),
		"edit_rank" => array("value" => 0, "disp_name" => "Edit Ranks", "description" => "Enables members of this rank to edit existing ranks on the website."),
		"delete_rank" => array("value" => 0, "disp_name" => "Delete Ranks", "description" => "Enables members of this rank to delete ranks on the website."),
	),
	"Uploading" => array(
		"upload_files" => array("value" => 0, "disp_name" => "Upload Files", "description" => "Enables members of this rank to upload files to the website."),
		"delete_files" => array("value" => 0, "disp_name" => "Delete Files", "description" => "Enables members of this rank to delete files from the website."),
		"rename_files" => array("value" => 0, "disp_name" => "Rename Files", "description" => "Enables members of this rank to rename uploaded files on the website."),
		"create_files" => array("value" => 0, "disp_name" => "Create Files", "description" => "Enables members of this rank to create new files on the website."),
		"edit_files" => array("value" => 0, "disp_name" => "Edit Files", "description" => "Enables members of this rank to edit files on the website."),
		"create_folders" => array("value" => 0, "disp_name" => "Create Folders", "description" => "Enables members of this rank to create folers to put files in."),
		"rename_folders" => array("value" => 0, "disp_name" => "Rename Folders", "description" => "Enables members of this rank to rename folders."),
		"delete_folders" => array("value" => 0, "disp_name" => "Delete Folders", "description" => "Enables members of this rank to delete folders."),
	),
	"Galleries" => array(
		"add_gallery" => array("value" => 0, "disp_name" => "Add Galleries", "description" => "Enables members of this rank to add galleries to the website."),
		"edit_gallery" => array("value" => 0, "disp_name" => "Edit Galleries", "description" => "Enables members of this rank to edit existing galleries."),
		"delete_gallery" => array("value" => 0, "disp_name" => "Delete Galleries", "description" => "Enables members of this rank to delete galleries from the website."),
		"rename_gallery" => array("value" => 0, "disp_name" => "Rename Galleries", "description" => "Enables members of this rank to rename galleries."),
	),
	"Sliders" => array(
		"add_slider" => array("value" => 0, "disp_name" => "Add Slider", "description" => "Enables members of this rank to add sliders to the website."),
		"edit_slider" => array("value" => 0, "disp_name" => "Edit Slider", "description" => "Enables members of this rank to edit existing sliders."),
		"delete_slider" => array("value" => 0, "disp_name" => "Delete Slider", "description" => "Enables members of this rank to delete sliders from the website."),
		"rename_slider" => array("value" => 0, "disp_name" => "Rename Slider", "description" => "Enables members of this rank to rename sliders."),
	),
	"Forms" => array(
		"add_form" => array("value" => 0, "disp_name" => "Add Forms", "description" => "Enables members of this rank to add forms."),
		"edit_form" => array("value" => 0, "disp_name" => "Edit Forms", "description" => "Enables members of this rank to edit existing forms."),
		"delete_form" => array("value" => 0, "disp_name" => "Delete Forms", "description" => "Enables members of this rank to delete forms."),
	),
	"Website" => array(
		"cpanel_access" => array("value" => 0, "disp_name" => "CPanel Access", "description" => "Enables members of this rank to access the admin CPanel."),
		"unpublished_access" => array("value" => 0, "disp_name" => "Unpublished Access", "description" => "Enables members of this rank to access the website when Unpublished."),
		"edit_site_settings" => array("value" => 0, "disp_name" => "Edit Website Information", "description" => "Enables members of this rank to edit the website information."),
		"edit_site_colors" => array("value" => 0, "disp_name" => "Edit Website Colors", "description" => "Enables members of this rank to modify the website theme colors."),
		"edit_user_settings" => array("value" => 0, "disp_name" => "Edit User Settings", "description" => "Enables members of this rank to edit sitewide user settings."),
		"upload_favicon_banner" => array("value" => 0, "disp_name" => "Upload Favicon and Banner", "description" => "Enables members of this rank to upload a favicon and banner to the website."),
		"edit_socnet" => array("value" => 0, "disp_name" => "Edit Social Networks", "description" => "Enables members of this rank to edit the social networks the website is connected to."),
		"edit_google_analytics" => array("value" => 0, "disp_name" => "Edit Google Analytics", "description" => "Enables members of this rank to edit the Google Analytics information."),
		"edit_veterans" => array("value" => 0, "disp_name" => "Edit Veterans", "description" => "Enables members of this rank to edit the Veterans Database."),
	)
);

//Adds all new permissions to admin ranks
$query="SELECT * FROM `ranks` WHERE `admin_rank` = 1";
$result=mysqli_query( $connection, $query);
while($admin_rank=mysqli_fetch_array($result)){
	$admin_permissions = unserialize($admin_rank['permissions']);
	$enabled_perms = enable_all_perms($blank_permissions);
	if($admin_permissions != $enabled_perms){
		$admin_permissions = mysqli_real_escape_string($connection, serialize($enabled_perms));
		$query="UPDATE `ranks` SET
			`permissions` = '{$admin_permissions}' WHERE `id` = {$admin_rank['id']}";
		mysqli_query( $connection, $query);
	}
}

if(logged_in()){
	$permissions = array_replace_recursive($blank_permissions, get_rank_info());
}else{
	$permissions = "";
}

if(isset($_SESSION['user_id'])){
	$query="SELECT * FROM `users` WHERE `id` = {$_SESSION['user_id']}";
	$result=mysqli_query($connection, $query);
	$user_info=mysqli_fetch_array($result);
}

function check_permission($perm_group, $perm = false){
	global $connection;
	global $permissions;
	
	if(logged_in()){
		if (is_array($perm_group)&&$perm==false){
			$return = false;
			foreach ($perm_group as $permission){
				$explode = explode(";", $permission);
				if(isset($permissions[$explode[0]][$explode[1]]['value'])&&$permissions[$explode[0]][$explode[1]]['value']==1){
					$return=true;
				}
				
			}
			return $return;
		}else{
			if(isset($permissions[$perm_group][$perm]['value'])&&$permissions[$perm_group][$perm]['value']==1){
				return true;
			}else{
				return false;
			}
		}
	}else{
		return false;
	}
}

function get_user_permission($u_id, $perm_group, $perm){
	//gets permissions for specified user ID
	global $connection;
	$query="SELECT `rank` FROM `users` WHERE `id` = {$u_id}";
	$result=mysqli_query( $connection, $query);
	$user_rank=mysqli_fetch_array($result);
	$query="SELECT * FROM `ranks` WHERE `id` = {$user_rank['rank']}";
	$result=mysqli_query( $connection, $query);
	if(mysqli_num_rows($result)!=0){
		$rank_permissions=mysqli_fetch_array($result);
		if($rank_permissions['permissions']!=""){
			$user_permissions = unserialize($rank_permissions['permissions']);
			if(isset($user_permissions[$perm_group][$perm]['value'])&&$user_permissions[$perm_group][$perm]['value']==1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function get_page_permission($visibleArr){
	global $user_info;
	$dispPage=false;
	switch ($visibleArr[0]) {
		case 'any':
			$dispPage=true;
			break;
		case 'loggedin':
			if(logged_in()){
				$dispPage=true;
			}
			break;
		case 'loggedout':
			if(!logged_in()){
				$dispPage=true;
			}
			break;
		default:
			if(in_array($user_info['rank'], $visibleArr)){
				$dispPage=true;
			}
			break;
	}
	return $dispPage;
}

function get_color($color){
	global $connection;
	$Query="SELECT `color_hex`, `cid`, `s_name`, `style_color_id` FROM `style_colors` INNER JOIN `css_selectors` ON `cid` = `style_color_id` WHERE `s_name` = '{$color}'";
	$Result=mysqli_query( $connection, $Query);
	$color = mysqli_fetch_array($Result);
	return $color['color_hex'];
}

function get_user($id){
	global $connection;
	$query="SELECT * FROM `users` WHERE `id` = {$id}";
	$result=mysqli_query( $connection, $query);
	return $user_info=mysqli_fetch_array($result);
}

function randstring($length = 16) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }
    return $string;
}

function del_acc($userid){
	global $connection;
	
	$query="SELECT * FROM `users` WHERE `id` = {$userid}";
	$userquery=mysqli_query( $connection, $query);
	confirm_query($userquery);
	$user = mysqli_fetch_array($userquery);
	
	if($user['deletable']!=0){
		$query="DELETE FROM `users` 
				WHERE `id` =  '{$userid}'";
		$result=mysqli_query($connection, $query);
		confirm_query($result);

		$query="DELETE FROM `users_custom_fields` 
				WHERE `uid` =  '{$userid}'";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		return true;
	}else{
		return false;
	}
}

function del_rank($rankid){
	global $connection;
	
	$query="SELECT * FROM `ranks` WHERE `id` = {$rankid}";
	$rankquery=mysqli_query( $connection, $query);
	confirm_query($rankquery);
	$rank= mysqli_fetch_array($rankquery);
	
	if($rank['deletable']!=0){
		$query="DELETE FROM `ranks` 
				WHERE `id` =  '{$rankid}'";
		$result=mysqli_query($connection, $query);
		confirm_query($result);
		return true;
	}else{
		return false;	
	}
}

function mysql_prep( $value ) {
	global $connection;
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysqli_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) { // PHP v4.3.0 or higher
		// undo any magic quote effects so mysql_real_escape_string can do the work
		if( $magic_quotes_active ) { $value = stripslashes( $value ); }
		$value = mysqli_real_escape_string($connection,  $value );
	} else { // before PHP v4.3.0
		// if magic quotes aren't already on then add slashes manually
		if( !$magic_quotes_active ) { $value = addslashes( $value ); }
		// if magic quotes are active, then the slashes already exist
	}
	return $value;
}
function check_login(){ ?>
<style type="text/css">
</style>
<p><?php
	if(logged_in()){?>
        <?php echo "<b>".$_SESSION['username']."</b>";?>
         | <a href="<?php echo $GLOBALS['HOST']; ?>/account-settings">Account Settings</a> | <?php if(check_permission("Website","cpanel_access")){?><a href="<?php echo $GLOBALS['HOST']; ?>/administrator/" target="_blank">CPanel</a> | <?php } ?><a href="<?php echo $GLOBALS['HOST']; ?>/logout">Logout</a>
    <?php }else{ ?>
			<?php if($GLOBALS['site_info']['user_creation'] == 'approval' || $GLOBALS['site_info']['user_creation'] == 'any'){ ?><a href="<?php echo $GLOBALS['HOST']; ?>/register">Register</a> | <?php } ?><a href="<?php echo $GLOBALS['HOST']; ?>/login">Login</a>
	<?php }?>
    	</p>
<?php }

function verify_login($user){
	//check if user can login (activated email, not banned, or approved by admin)
	$login_flag = true;
	$login_msg = array();

	//Check email
	if(($user['activated_email']==0 && $GLOBALS['site_info']['require_email_activation']==1)){
		$login_flag = false;
		array_push($login_msg, 'Email not activated. <a href="resend-activation?email='.urlencode($user['email']).'"> Click Here </a> to re-send activation.');
	}
	//Check admin approval
	if(($user['approved_admin']==0 && $GLOBALS['site_info']['user_creation']=='approval')){
		$login_flag = false;
		array_push($login_msg, "Account not approved by administrator.");
	}
	//Check ban status
	if(($user['banned']==1)){
		$login_flag = false;
		array_push($login_msg, "Account has been banned.");
	}
	//Check unpubpished site permissions
	if($GLOBALS['site_info']['published'] == 0 && !get_user_permission($user['id'], 'Website', 'unpublished_access')){
		$login_flag = false;
		array_push($login_msg, "Website is not accessible to you; it is Under Construction.");
	}

	return array($login_flag, $login_msg);
}

function order_doc_files($dir){
	$dirfiles = scandir("uploads/".$dir);
	if($dirfiles!=false){
		$dirfiles = array_diff($dirfiles, array('.', '..'));
		$chronfiles = array();
		$nonchronfiles = array();
		foreach($dirfiles as $file){
			$filename = pathinfo($file);
			$filename = str_replace('_', ' ', $filename['filename']);
			$filedate = strtotime($filename);
			if($filedate!=false){
				$chronfiles[$file] = $filedate;
			}else{
				array_push($nonchronfiles, $file);
			}
		}
		
		arsort($chronfiles);
		$chronnofiles = true;
		$nonchronnofiles = true;
		if(count($chronfiles)>0){
			$chronnofiles = false;
			?><ul><?php
			foreach($chronfiles as $filename => $filedate){
				$filedate = date('F d, Y', $filedate);
				?>
				<li><a href="<?php echo $GLOBALS['HOST']; ?>/uploads/<?php echo $dir.'/'.$filename; ?>" target="_blank"><?php echo $filedate; ?></a></li>
			<?php
			}
			?></ul><?php
		}
		if(count($nonchronfiles)>0){
			$nonchronnofiles = false;
			?><ul><?php
			foreach($nonchronfiles as $file){
				?>
				<li><a href="<?php echo $GLOBALS['HOST']; ?>/uploads/<?php echo $dir.'/'.$file; ?>" target="_blank"><?php echo $file; ?></a></li>
			<?php
			}
			?></ul><?php
		}
	}
}

function gallery($images_dir, $thumbs_dir, $thumbs_width, $thumbs_height, $gallname = "gall", $num_images = false){
	?>
<div align="center" style="text-align:center; width:100%; padding:4px; margin-bottom:10px;">
    <?php
    /** generate photo gallery **/
	if (file_exists($images_dir)&&file_exists($thumbs_dir)){
		$image_files = get_files($images_dir);
		$count = 0;
		if(count($image_files)) {
			$index = 0;
			foreach($image_files as $index=>$file) {
				if($count === $num_images){
					break;
				}
				$count++;
				$index++;
				$thumbnail_image = $thumbs_dir.$file;
				if(!file_exists($thumbnail_image)) {
					$extension = get_file_extension($thumbnail_image);
					$extension = strtolower($extension);
					if($extension) {
						make_thumb($images_dir.$file,$thumbnail_image,$thumbs_width,$thumbs_height,$extension);
					}
				}?>
				<a href="<?php echo $GLOBALS['HOST']; ?>/<?php echo $images_dir.$file; ?>" rel="prettyPhoto[<?php echo $gallname; ?>]" title="<?php echo $file; ?>" class="photo-link"><img src="<?php echo $GLOBALS['HOST']; ?>/<?php echo $thumbnail_image;?>" style="width:<?php echo $thumbs_width; ?>px; height:<?php echo $thumbs_height; ?>px;" /></a>
			<?php
            }
			?><div class="clear"></div><?php
		}else{?>
			
		<?php
		}
	}else{?>
	<?php
    }
	?>
</div>
<?php
}

function upload($files, $directory ,$maxfilesize, $allowed_file_types = false){
	$filename = $files["file"]["name"];
	$file_basename = substr($filename, 0, strripos($filename, '.')); // get file extention
	$file_ext = substr($filename, strripos($filename, '.')); // get file name
	$filesize = $files["file"]["size"];
 
	if (!empty($file_basename)) {
 		// rename file
		$newfilename = str_replace(" ", "_", $files["file"]["name"]);
		if (((is_array($allowed_file_types)&&in_array($file_ext,$allowed_file_types))||$allowed_file_types == false)) {
			if ($filesize < $maxfilesize) {	
				if (file_exists($directory . $newfilename)) {		
					// file already exists error
					$message = "You have already uploaded this file.</h2>";			
				} else {		
					if(move_uploaded_file($files["file"]["tmp_name"], $directory.$newfilename)){
						$message = "\"".$newfilename."\" was uploaded successfully.";
					}else{
						$message = "\"".$newfilename."\" was not uploaded successfully.";
					}
				}
			}else{
				// file size error
				$mb = ($maxfilesize / 1024)/1024;
				$message = "The file you are trying to upload is too large. (".$mb." MB max)";
			}
		}else{
			// file type error
			$message = "Only these file types are allowed for upload: " . implode(', ',$allowed_file_types);
			unlink($files["file"]["tmp_name"]);
		}
	} else {	
		// file selection error
		$message = "Please select a file to upload.";	
	}
	return $message;
}

function multi_upload($files, $output_dir){
	
	$ret = array();

	$error =$files["myfile"]["error"];
   {
	
		if(!is_array($files["myfile"]['name'])) //single file
		{
			$RandomNum   = time();
			
			$ImageName      = str_replace(' ','-',strtolower($files['myfile']['name']));
			$ImageType      = $files['myfile']['type']; //"image/png", image/jpeg etc.
		 
			$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
			$ImageExt       = str_replace('.','',$ImageExt);
			$ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
			$NewImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;

			move_uploaded_file($files["myfile"]["tmp_name"],$output_dir. $NewImageName);
			 //echo "<br> Error: ".$files["myfile"]["error"];
			 
				 $ret[$fileName]= $output_dir.$NewImageName;
		}
		else
		{
			$fileCount = count($files["myfile"]['name']);
			for($i=0; $i < $fileCount; $i++)
			{
				$RandomNum   = time();
			
				$ImageName      = str_replace(' ','-',strtolower($files['myfile']['name'][$i]));
				$ImageType      = $files['myfile']['type'][$i]; //"image/png", image/jpeg etc.
			 
				$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
				$ImageExt       = str_replace('.','',$ImageExt);
				$ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
				$NewImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
				
				$ret[$NewImageName]= $output_dir.$NewImageName;
				move_uploaded_file($files["myfile"]["tmp_name"][$i],$output_dir.$NewImageName );
			}
		}
	}
	echo json_encode($ret);
}

function print_multi_upload($output_dir, $maxsize, $filetypes, $cpanel = false){
?>
<script type="text/javascript" src="<?php if($cpanel == true){echo "../";} ?>jscripts/plupload.full.min.js"></script>
<p>(If using Internet Explorer, you must have version 10 or above.)</p>
<div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
<br />

<div id="container">
    <a class="btn blue" id="pickfiles" href="javascript:;">Select files</a> 
    <a class="btn green" id="uploadfiles" href="javascript:;">Upload</a>
</div>

<br />
<pre id="console"></pre>


<script type="text/javascript">
// Custom example logic

var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'pickfiles', // you can pass in id...
	container: document.getElementById('container'), // ... or DOM Element itself
	url : '<?php if($cpanel == true){echo "../";} ?>upload.php?dir=<?php if($cpanel==true){echo urlencode(substr($output_dir, 3));}else{echo urlencode($output_dir);} ?>',
	flash_swf_url : '<?php if($cpanel == true){echo "../";} ?>jscripts/Moxie.swf',
	silverlight_xap_url : '<?php if($cpanel == true){echo "../";} ?>jscripts/Moxie.xap',
	
	filters : {
		max_file_size : '<?php echo $maxsize; ?>',
		<?php if($filetypes!=false){?>
		mime_types: [
			{title : "Image files", extensions : "<?php echo $filetypes;?>"},
			{title : "Zip files", extensions : "zip"}
		]
		<?php } ?>
	},

	init: {
		PostInit: function() {
			document.getElementById('filelist').innerHTML = '';

			document.getElementById('uploadfiles').onclick = function() {
				uploader.start();
				return false;
			};
		},

		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {
				document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
			});
		},

		UploadProgress: function(up, file) {
			document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
		},

		Error: function(up, err) {
			document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
		}
	}
});

uploader.init();

</script>

<?php
}

function redirect_to($location=NULL){
	if($location!=NULL){
		header("Location: {$location}");
		exit;
	}
}

function confirm_query($result){
	global $connection;
	if(!$result){
		die("Query failed: ".mysqli_error($connection));	
	}else{
		return true;
	}
}

function nav_button($page){
	?>
<a href="<?php
	echo $GLOBALS['HOST'];?>/page/<?php echo urlencode($page['name']);
	?>" <?php if($page['target']!="_self"){echo 'target="'.$page['target'].'"';} ?>><?php echo $page['name'];?></a>
<?php
}

function nav($position, $pgselection){
	global $connection;
	$logo = scandir("images/logo/");
	if(isset($logo[2])){
		$logo = $logo[2];
	}else{
		$logo = false;
	}

	if($position=="mobile"){?>
        <ul id="slide-out" class="side-nav">
            <div style="height:71px; width:100%;" class="mobile-logo">
            <?php if($logo!=false){ ?>
                <?php if($GLOBALS['site_info']['logo_url']!=''){?>
                <a href="<?php echo $GLOBALS['site_info']['logo_url']; ?>"><img src="<?php echo $GLOBALS['HOST']; ?>/images/logo/<?php echo $logo; ?>" alt="<?php echo $GLOBALS['site_info']['name']; ?> Logo" width="240" /></a>
                <?php }else{ ?>
                <img src="<?php echo $GLOBALS['HOST']; ?>/images/logo/<?php echo $logo; ?>" width="240" />
            <?php } 
            } ?>
        </div>
    <?php
		$query="SELECT * FROM `pages` WHERE `horiz_menu` = 1 OR `vert_menu` = 1 AND `issubpage` = 0 AND `published` = 1 ORDER BY `position` ASC";
		$result=mysqli_query( $connection, $query);
		$numpages=mysqli_num_rows($result);
		
		if($numpages!=0){
		
			$query="SELECT * FROM `pages` WHERE `horiz_menu` = 1 AND `issubpage` = 0 AND `published` = 1 ORDER BY `position` ASC";
			$result=mysqli_query( $connection, $query);
			$numhorizpages=mysqli_num_rows($result);
			
			if($numhorizpages!=0){
			?>
            <ul>
			<?php
			while($page=mysqli_fetch_array($result)){?>
				<li<?php if($pgselection=="true"){if(isset($_GET['page'])&&urlencode($page['name'])==$_GET['page']){echo " class=\"selected\"";}} ?>><?php 	
					
					$query="SELECT * FROM `pages` WHERE `horiz_menu` = 1 AND `issubpage` = 1 AND `published` = 1 AND `parent`={$page['id']} ORDER BY `position` ASC";
					$subpgresult=mysqli_query( $connection, $query);
					confirm_query($subpgresult);
					
					if(mysqli_num_rows($subpgresult)==0){
						nav_button($page);
					}else{?>
						<ul class="collapsible collapsible-accordion">
							<li>
								<span class="collapsible-header" style="padding-left:0px; margin-left:0px;">
                                	<?php nav_button($page); ?>
                                	<i class="mdi-navigation-arrow-drop-down"></i>
                                </span>
                                
                                  <div class="collapsible-body">
                                    <ul>
									<?php while($subpage=mysqli_fetch_array($subpgresult)){?>
                                        <li style="width:100%;"<?php if($pgselection=="true"){if(isset($_GET['page'])&&urlencode($subpage['name'])==$_GET['page']){echo " class=\"selected\"";}} ?>><?php
                                        
                                        nav_button($subpage);
                                        
                                        ?>
                                        </li>
                                    <?php } ?>
								</ul>
							  </div>
							</li>
						  </ul>
					<?php } ?>
					</li>
				<?php
				}
				?>
				</ul>
				<hr/>
			<?php
			
		}
			?>
			<?php
			$query="SELECT * FROM `pages` WHERE `vert_menu` = 1 AND `issubpage` = 0 AND `published` = 1 ORDER BY `position` ASC";
			$result=mysqli_query( $connection, $query);
			$numvertpages=mysqli_num_rows($result);
			
			if($numvertpages!=0){?>
            <ul>
            <?php
			while($page=mysqli_fetch_array($result)){
				if($page['issubpage']==0){ $lastmainpage=$page['id'];?>
				<li<?php if($pgselection=="true"){if(isset($_GET['page'])&&urlencode($page['name'])==$_GET['page']){echo " class=\"selected\"";}} ?>><?php 
					
					$query="SELECT * FROM `pages` WHERE `vert_menu` = 1 AND `issubpage` = 1 AND `published` = 1 AND `parent`={$page['id']} ORDER BY `position` ASC";
					$subpgresult=mysqli_query( $connection, $query);
					confirm_query($subpgresult);
					
					if(mysqli_num_rows($subpgresult)==0){
						nav_button($page);
					}else{?>
					
						<ul class="collapsible collapsible-accordion">
							<li>
								<span class="collapsible-header" style="padding-left:0px; margin-left:0px;">
                                	<?php nav_button($page); ?>
                                	<i class="mdi-navigation-arrow-drop-down"></i>
                                </span>
                                  <div class="collapsible-body">
                                    <ul>
									<?php while($subpage=mysqli_fetch_array($subpgresult)){?>
                                        <li style="width:100%;"<?php if($pgselection=="true"){if(isset($_GET['page'])&&urlencode($subpage['name'])==$_GET['page']){echo " class=\"selected\"";}} ?>><?php
                                        
                                        nav_button($subpage);
                                        
                                        ?>
                                        </li>
                                    <?php } ?>
								</ul>
							  </div>
							</li>
						  </ul>
						  </li>
					<?php } ?>
					</li>
				<?php
				}
				?>
				
			<?php
			}
		?>
        </ul>
        <hr/>
        <?php } 
		}
		?>
        <div class="mobile-login">
        	<?php
        if(logged_in()){?>
        	<ul>
            	<li><span class="mobile-login-username"><?php echo $_SESSION['username'];?></span></li>
	            <li><span class="icon-cog"></span><a href="<?php echo $GLOBALS['HOST']; ?>/account-settings">Settings</a></li>
	            <?php if(check_permission("Website","cpanel_access")){?><li><span class="icon-dashboard"></span><a href="<?php echo $GLOBALS['HOST']; ?>/administrator/" target="_blank">CPanel</a></li><?php } ?>
	            <li><span class="icon-exit"></span>                                       <a href="<?php echo $GLOBALS['HOST']; ?>/logout.php">Logout</a></li>
            </ul>
        <?php }else{ ?>
                <?php if($GLOBALS['site_info']['user_creation'] == 'approval' || $GLOBALS['site_info']['user_creation'] == 'any'){ ?><a href="<?php echo $GLOBALS['HOST']; ?>/register">Register</a> | <?php } ?><a href="<?php echo $GLOBALS['HOST']; ?>/login">Login</a>
        <?php }?>
        	<br/><br/><?php echo $GLOBALS['site_info']['name']; ?><?php if($GLOBALS['site_info']['copyright_text']!=''){echo ' '.$GLOBALS['site_info']['copyright_text'];} ?>, Copyright © <?php echo date("Y"); ?>.
            </div>
        </ul>

	<?php }
	
	if($position=="horiz"||$position=="vert"){
			if($position=="horiz"){
				$query="SELECT * FROM `pages` WHERE `horiz_menu` = 1 AND `issubpage` = 0 AND `published` = 1 ORDER BY `position` ASC";
			}elseif($position=="vert"){
				$query="SELECT * FROM `pages` WHERE `vert_menu` = 1 AND `issubpage` = 0 AND `published` = 1 ORDER BY `position` ASC";
			}
			
			$result=mysqli_query( $connection, $query);
			$numpages=mysqli_num_rows($result);
			
			if($position=="horiz"&&$numpages!=0){ ?>
				<div class="nav">
					<ul id="horiz-menu">
			<?php }elseif($position=="vert"&&$numpages!=0){ ?>
				<div class=" col l3 card" style="padding:0 !important;" id="vert-td"><div style="width:100%;">
					<ul  id="vert-menu">
			<?php }
				//$buttonwidth = $numpages;
				//$buttonwidth = 900 - $buttonwidth;
				//$buttonwidth = $buttonwidth / $numpages + 1;
				if($position=="horiz"){
					if($numpages!=0){
						$buttonwidth = round(100 / $numpages, 4);
					}else{
						$buttonwidth=100;
					}
				}elseif($position=="vert"){
					$buttonwidth = 100;
				}
				
				$pageorder = 0;

				if($numpages!=0){
				while($page=mysqli_fetch_array($result)){
					if($page['issubpage']==0){ $lastmainpage=$page['id'];
						if(get_page_permission(unserialize($page['visible']))){?>
	                        <li style="min-width:<?php echo $buttonwidth; ?>%;"<?php if($pgselection=="true"){if(isset($_GET['page'])&&urlencode($page['name'])==$_GET['page']){echo " class=\"selected\"";}} ?>><a style="min-width:<?php echo $buttonwidth; ?>%;" href="<?php
	                        	echo $GLOBALS['HOST'];?>/page/<?php echo urlencode($page['name']);
	                            ?>" <?php if($page['target']!="_self"){echo "target=\"".$page['target']."\"";} ?>><?php echo $page['name'];?></a><?php 
	                            if($position=="horiz"){
	                                $query="SELECT * FROM `pages` WHERE `horiz_menu` = 1 AND `issubpage` = 1 AND `published` = 1 AND `parent`={$page['id']} ORDER BY `position` ASC";
	                            }elseif($position=="vert"){
	                                $query="SELECT * FROM `pages` WHERE `vert_menu` = 1 AND `issubpage` = 1 AND `published` = 1 AND `parent`={$page['id']} ORDER BY `position` ASC";
	                            }
	                            $subpgresult=mysqli_query( $connection, $query);
	                            confirm_query($subpgresult);
	                            if(mysqli_num_rows($subpgresult)!=0){?>
	                                <ul style="min-width:100%;">
	                                <?php while($subpage=mysqli_fetch_array($subpgresult)){
	                                	if(get_page_permission(unserialize($subpage['visible']))){?>
		                                    <li style="width:100%;"<?php if($pgselection=="true"){if(isset($_GET['page'])&&urlencode($subpage['name'])==$_GET['page']){echo " class=\"selected\"";}} ?>>
		                                        <a href="<?php
		                                        	echo $GLOBALS['HOST'];?>/page/<?php echo urlencode($subpage['name']);?>"
		                                            <?php if($subpage['target']!="_self"){echo "target=\"".$subpage['target']."\"";} ?>><?php echo $subpage['name'];?></a>
		                                    </li>
		                                <?php 
	                                		}
		                                } ?>
	                                </ul>
		                            <?php 
		                            } ?>
	                            </li>
	                        <?php
	                    	}
                        }
                    }
				}
			?>
			</ul>
			<?php if($position=="horiz"&&$numpages!=0){?>
				</div>
			<?php }elseif($position=="vert"&&$numpages!=0){?>
            	</div></div>
			<?php } ?>
		<?php
		
    }
	return $numpages;
}


function echo_page($num_pages, $current_page, $url){
	if($num_pages!=1){
		if($current_page>1){ ?>
			<div class="col s4 l2"><a class="btn-floating red" href="<?php echo $GLOBALS['HOST'].'/'.$url; ?>&pg=<?php echo $current_page - 1; ?>"><i class="material-icons">keyboard_arrow_left</i></a></div>
		<?php }else{ ?>
        	<div class="col s4 l2"><span class="disabled btn-floating gray"><i class="material-icons">keyboard_arrow_left</i></span></div>
        <?php
		}
    	echo '<div class="col s4 l8 center"> Page '.$current_page.' of '.$num_pages.'</div>';
		if($num_pages>1&&$current_page<$num_pages){ ?>
    		<div class="col s4 l2 right" style="text-align:right"><a class="btn-floating red" href="<?php echo $GLOBALS['HOST'].'/'.$url; ?>&pg=<?php echo $current_page + 1; ?>"><i class="material-icons">keyboard_arrow_right</i></a></div>
    	<?php }else{ ?>
        	<div class="col s4 l2 right" style="text-align:right"><span class="disabled gray btn-floating"><i class="material-icons">keyboard_arrow_right</i></span></div>
        <?php
		}
	}
}

function slider($slider_id){
	global $connection;
	$query="SELECT * FROM `slider_names` WHERE `id` = ".$slider_id;
	$result=mysqli_query( $connection, $query);
	if(mysqli_num_rows($result)==1){
		$slider=mysqli_fetch_array($result);
	?>
                        <!-- Caption Style -->
                        <style> 
                            .captionOrange, .captionBlack
                            {
                                font-size: 20px;
                                line-height: 30px;
                                text-align: center;
								border-radius: 2px;
                                                            }
                            .captionOrange
                            {
                                background: #EB5100;
                                background-color: rgba(235, 81, 0, 0.6);
                            }
                            .captionBlack
                            {
                                font-size:16px;
                                
                            }
                            .captionBlack a{
                            	text-decoration: underline;
                            }
                            a.captionOrange, A.captionOrange:active, A.captionOrange:visited
                            {
                                color: #ffffff;
                                text-decoration: none;
                            }
                            a.captionOrange:hover
                            {
                                color: #eb5100;
                                text-decoration: underline;
                                background-color: #eeeeee;
                                background-color: rgba(238, 238, 238, 0.7);
                            }
                            .bricon
                            {
                                background: url(jssor/img/browser-icons.png);
                            }
                        </style>
                        <!-- it works the same with all jquery version from 1.x to 2.x -->
                        <!--<script type="text/javascript" src="jssor/jquery-1.9.1.min.js"></script>-->
                        <!-- use jssor.slider.mini.js (40KB) or jssor.sliderc.mini.js (32KB, with caption, no slideshow) or jssor.sliders.mini.js (28KB, no caption, no slideshow) instead for release -->
                        <!-- jssor.slider.mini.js = jssor.sliderc.mini.js = jssor.sliders.mini.js = (jssor.js + jssor.slider.js) -->
                        <script type="text/javascript" src="<?php echo $GLOBALS['HOST']; ?>/jssor/jssor.js"></script>
                        <script type="text/javascript" src="<?php echo $GLOBALS['HOST']; ?>/jssor/jssor.slider.js"></script>
                        <script>
                            jQuery(document).ready(function ($) {
								
                                //Reference http://www.jssor.com/development/slider-with-slideshow-jquery.html
                                //Reference http://www.jssor.com/development/tool-slideshow-transition-viewer.html
                    
                                var _SlideshowTransitions = [
                                //Fade in R
                                {$Duration: 1200, x: -0.3, $During: { $Left: [0.3, 0.7] }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 }
                                //Fade out L
                                , { $Duration: 1200, x: 0.3, $SlideOut: true, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 }
                                ];
								
								var _CaptionTransitions = [
								//CLIP|LR
								{$Duration: 900, $Clip: 3, $Easing: $JssorEasing$.$EaseInOutCubic },
					
								];
								
                                var options = {
                                    $AutoPlay: true,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
                                    $AutoPlaySteps: 1,                                  //[Optional] Steps to go for each navigation request (this options applys only when slideshow disabled), the default value is 1
                                    $AutoPlayInterval: 4000,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
                                    $PauseOnHover: 3,                               //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, 4 freeze for desktop, 8 freeze for touch device, 12 freeze for desktop and touch device, default value is 1
                    
                                    $ArrowKeyNavigation: true,   			            //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
                                    $SlideDuration: 500,                                //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500
                                    $MinDragOffsetToSlide: 20,                          //[Optional] Minimum drag offset to trigger slide , default value is 20
                                    //$SlideWidth: 1200,                                 //[Optional] Width of every slide in pixels, default value is width of 'slides' container
                                    //$SlideHeight: 600,                                //[Optional] Height of every slide in pixels, default value is height of 'slides' container
                                    $SlideSpacing: 0, 					                //[Optional] Space between each slide in pixels, default value is 0
                                    $DisplayPieces: 1,                                  //[Optional] Number of pieces to display (the slideshow would be disabled if the value is set to greater than 1), the default value is 1
                                    $ParkingPosition: 0,                                //[Optional] The offset position to park slide (this options applys only when slideshow disabled), default value is 0.
                                    $UISearchMode: 1,                                   //[Optional] The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc).
                                    $PlayOrientation: 1,                                //[Optional] Orientation to play slide (for auto play, navigation), 1 horizental, 2 vertical, 5 horizental reverse, 6 vertical reverse, default value is 1
                                    $DragOrientation: 3,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)
                    
                                    $SlideshowOptions: {                                //[Optional] Options to specify and enable slideshow or not
                                        $Class: $JssorSlideshowRunner$,                 //[Required] Class to create instance of slideshow
                                        $Transitions: _SlideshowTransitions,            //[Required] An array of slideshow transitions to play slideshow
                                        $TransitionsOrder: 1,                           //[Optional] The way to choose transition to play slide, 1 Sequence, 0 Random
                                        $ShowLink: true                                    //[Optional] Whether to bring slide link on top of the slider when slideshow is running, default value is false
                                    },
									
									$CaptionSliderOptions: {                            //[Optional] Options which specifies how to animate caption
										$Class: $JssorCaptionSlider$,                   //[Required] Class to create instance to animate caption
										$CaptionTransitions: _CaptionTransitions,       //[Required] An array of caption transitions to play caption, see caption transition section at jssor slideshow transition builder
										$PlayInMode: 1,                                 //[Optional] 0 None (no play), 1 Chain (goes after main slide), 3 Chain Flatten (goes after main slide and flatten all caption animations), default value is 1
										$PlayOutMode: 1,                                 //[Optional] 0 None (no play), 1 Chain (goes before main slide), 3 Chain Flatten (goes before main slide and flatten all caption animations), default value is 1
									},
									
                                    $BulletNavigatorOptions: {                                //[Optional] Options to specify and enable navigator or not
                                        $Class: $JssorBulletNavigator$,                       //[Required] Class to create navigator instance
                                        $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                                        $Lanes: 1,                                      //[Optional] Specify lanes to arrange items, default value is 1
                                        $SpacingX: 10,                                   //[Optional] Horizontal space between each item in pixel, default value is 0
                                        $SpacingY: 10                                    //[Optional] Vertical space between each item in pixel, default value is 0
                                    },
                    
                                    $ArrowNavigatorOptions: {
                                        $Class: $JssorArrowNavigator$,              //[Requried] Class to create arrow navigator instance
                                        $ChanceToShow: 1,                                //[Required] 0 Never, 1 Mouse Over, 2 Always
                                        $AutoCenter: 2                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                                    },
                    
                                    $ThumbnailNavigatorOptions: {
                                        $Class: $JssorThumbnailNavigator$,              //[Required] Class to create thumbnail navigator instance
                                        $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                                        $ActionMode: 0,                                 //[Optional] 0 None, 1 act by click, 2 act by mouse hover, 3 both, default value is 1
                                        $DisableDrag: true                              //[Optional] Disable drag or not, default value is false
                                    }
                                };
                    
                                var jssor_sliderb = new $JssorSlider$("sliderb_container", options);
                                //responsive code begin
                                //you can remove responsive code if you don't want the slider scales while window resizes
								function ScaleSlider() {
									var parentWidth = $('#sliderb_container').parent().width();
									if (parentWidth) {
										jssor_sliderb.$ScaleWidth(parentWidth);
									}
									else
										window.setTimeout(ScaleSlider, 30);
								}
                                ScaleSlider();
                    
                                if (!navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|IEMobile)/)) {
                                    $(window).bind('resize', ScaleSlider);
                                }
                    
                    
                                //if (navigator.userAgent.match(/(iPhone|iPod|iPad)/)) {
                                //    $(window).bind("orientationchange", ScaleSlider);
                                //}
                                //responsive code end
								
                            });
                        </script>
                        <!-- Jssor Slider Begin -->
                        <!-- You can move inline styles to css file or css block. -->
                        <div class="row">
                        <div class="col l10 s12 offset-l1" style="padding:0 0 !important;">
                        <div style="width:100%; margin-left:auto; margin-right:auto;">
                        <div id="sliderb_container" style="position: relative; width: 600px; height: 300px; overflow: hidden; margin-left:auto; margin-right:auto; margin-bottom:20px;">
                    
                            <!-- Loading Screen -->
                            <div u="loading" style="position: absolute; top: 0px; left: 0px;">
                                <div style="filter: alpha(opacity=70); opacity:0.7; position: absolute; display: block;
                                    background-color: #000; top: 0px; left: 0px;width: 100%;height:100%;">
                                </div>
                                <div style="position: absolute; display: block; background: url(../img/loading.gif) no-repeat center center;
                                    top: 0px; left: 0px;width: 100%;height:100%;">
                                </div>
                            </div>
                    
                            <!-- Slides Container -->
                            <div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width: 600px; height: 300px;
                                overflow: hidden;">
                                <?php
									
									$query="SELECT * FROM `slider_images` WHERE `published` = 1 AND `slider_id` = ".$slider_id." ORDER BY `order`";
									$result=mysqli_query( $connection, $query);
									
									if(mysqli_num_rows($result)!=0){
										while($slide=mysqli_fetch_array($result)){?>
                                            <div>
                                                <img u=image src="<?php echo $GLOBALS['HOST']; ?>/images/slider/<?php echo $slider['name'].'/'.$slide['img_name']; ?>" />
                                                <!--<div u="thumb" style="background-color:rgba(0, 0, 0, 0.4); line-height:35px; padding-left:10px; margin-top:60px;"><?php if($slide['url']!=''){?><a href="<?php echo $slide['url'];?>"<?php if($slide['new_tab']==1){ echo 'target="_blank"'; } ?>><?php echo $slide['caption'];?></a><?php }else{echo $slide['caption'];} ?></div>-->
                                                <?php if($slide['caption']!=''){ ?>
                                                <div u=caption t="*" class="captionBlack"  style="position:absolute; left:20px; top: 30px; width:300px; height:30px; font-weight:bold;">
													<?php if($slide['url']!=''){?>
                                                        <a href="<?php echo $slide['url'];?>"<?php if($slide['new_tab']==1){ echo ' target="_blank"'; } ?>><?php echo $slide['caption']; ?></a>
                                                    <?php 
													}else{
														echo $slide['caption'];
													} ?>
                                                </div>
                                                <?php } ?>
                                            </div>
										<?php
                                        }
									}
								?>
                            </div>
                            
                    
                            <!-- ThumbnailNavigator Skin Begin 
                            <div u="thumbnavigator" class="sliderb-T" style="position: absolute; top: 0px; left: 0px; height:45px; width:600px;">
                                <div style="filter: alpha(opacity=40); opacity:0.4; position: absolute; display: block;
                                    background-color: #000000; top: 0px; left: 0px; width: 100%; height: 100%;">
                                </div>
                                <!-- Thumbnail Item Skin Begin
                                <div u="slides">
                                    <div u="prototype" style="POSITION: absolute; WIDTH: 600px; HEIGHT: 45px; TOP: 0; LEFT: 0;">
                                        <thumbnailtemplate style="font-family: verdana; font-weight: normal; POSITION: absolute; WIDTH: 100%; HEIGHT: 100%; TOP: 0; LEFT: 0; color:#fff; line-height: 45px; font-size:20px; padding-left:10px;"></thumbnailtemplate>
                                    </div>
                                </div>
                                <!-- Thumbnail Item Skin End
                            </div>
                            ThumbnailNavigator Skin End -->
                            
                            <!-- Bullet Navigator Skin Begin -->
                            <!-- jssor slider bullet navigator skin 01 -->
                            <style>
                                /*
                                .jssorb01 div           (normal)
                                .jssorb01 div:hover     (normal mouseover)
                                .jssorb01 .av           (active)
                                .jssorb01 .av:hover     (active mouseover)
                                .jssorb01 .dn           (mousedown)
                                */
                                .jssorb01 div, .jssorb01 div:hover, .jssorb01 .av
                                {
                                    
                                    
                                    overflow:hidden;
                                    cursor: pointer;
									border-radius: 2px;
                                }
                                .jssorb01 div {  }
                                /*.jssorb01 div:hover, .jssorb01 .av:hover { background-color: #d3d3d3; }
                                .jssorb01 .av { background-color: #fff; }*/
                                .jssorb01 .dn, .jssorb01 .dn:hover { background-color: #555555; }
                            </style>
                            <!-- bullet navigator container -->
                            <div u="navigator" class="jssorb01" style="position: absolute; bottom: 16px; right: 10px;">
                                <!-- bullet navigator item prototype -->
                                <div u="prototype" style="POSITION: absolute; WIDTH: 12px; HEIGHT: 12px;"></div>
                            </div>
                            <!-- Bullet Navigator Skin End -->
                            
                            <!-- Arrow Navigator Skin Begin -->
                            <style>
                                /* jssor slider arrow navigator skin 05 css */
                                /*
                                .jssora05l              (normal)
                                .jssora05r              (normal)
                                .jssora05l:hover        (normal mouseover)
                                .jssora05r:hover        (normal mouseover)
                                .jssora05ldn            (mousedown)
                                .jssora05rdn            (mousedown)
                                */
                                .jssora05l, .jssora05r, .jssora05ldn, .jssora05rdn
                                {
                                    position: absolute;
                                    cursor: pointer;
                                    display: block;
                                    overflow:hidden;
                                }
                                
                                .jssora05l:hover { background-position: -130px -40px; }
                                .jssora05r:hover { background-position: -190px -40px; }
                                .jssora05ldn { background-position: -250px -40px; }
                                .jssora05rdn { background-position: -310px -40px; }
                            </style>
                            <!-- Arrow Left -->
                          <span u="arrowleft" class="jssora05l" style=" top: 123px; left: 8px;"><i class="large material-icons">keyboard_arrow_left</i>
                            </span>
                            <!-- Arrow Right -->
                            <span u="arrowright" class="jssora05r" style=" top: 123px; right: 8px"><i class="large material-icons">keyboard_arrow_right</i>
                            </span>
                           
                            <!-- Trigger -->
                        </div>
                        </div>
                        </div>
                            </div>
                        <!-- Jssor Slider End -->
<?php
	}
}

/* function:  generates thumbnail */
function make_thumb($src,$dest,$desired_width,$desired_height,$extention) {
  /* read the source image */
  if($extention=="jpg" || $extention=="jpeg"){
  	$source_image = imagecreatefromjpeg($src);
  }elseif($extention=="gif"){
  	$source_image = imagecreatefromgif($src);
  }elseif($extention=="png"){
  	$source_image = imagecreatefrompng($src);
  }
  $width = imagesx($source_image);
  $height = imagesy($source_image);
  /* find the "desired height" of this thumbnail, relative to the desired width  */
  $desired_height = $desired_height;
  /* create a new, "virtual" image */
  $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
  /* copy source image at a resized size */
  imagecopyresampled($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
  /* create the physical thumbnail image to its destination */
  if($extention=="jpg" || $extention=="jpeg"){
  	imagejpeg($virtual_image,$dest);
  }elseif($extention=="gif"){
  	imagegif($virtual_image,$dest);
  }elseif($extention=="png"){
  	imagepng($virtual_image,$dest);
  }
}

/* function:  returns files from dir */
function get_files($images_dir,$exts = array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG')) {
  $files = array();
  if($handle = opendir($images_dir)) {
    while(false !== ($file = readdir($handle))) {
      $extension = strtolower(get_file_extension($file));
      if($extension && in_array($extension,$exts)) {
        $files[] = $file;
      }
    }
    closedir($handle);
  }
  return $files;
}

/* function:  returns a file's extension */
function get_file_extension($file_name) {
  return substr(strrchr($file_name,'.'),1);
}
?>