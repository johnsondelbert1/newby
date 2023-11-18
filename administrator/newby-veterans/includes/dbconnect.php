<?php
require_once('../../includes/functions.php');
if(!check_permission("Website","edit_veterans")){
	if(!logged_in()){
		redirect_to('login.php');
	}else{
		redirect_to('access-denied.php');
	}
}
function print_page($num_pages, $current_page, $url){
	if($num_pages!=1){
		if($current_page>1){ ?>
			<a href="<?php echo $url; ?>?pg=<?php echo $current_page - 1; ?>"><span style="font-size:24px;">&#8592;</span></a>
		<?php }else{ ?>
        	
        <?php
		}
    	echo ' Page '.$current_page.' of '.$num_pages;
		if($num_pages>1&&$current_page<$num_pages){ ?>
    		<a href="<?php echo $url; ?>?pg=<?php echo $current_page + 1; ?>"><span style="font-size:24px;">&#8594;</span></a>
    	<?php }else{ ?>
        	
        <?php
		}
	}
}
$db = new PDO('mysql:host=localhost;dbname=newbygin_vets;charset=utf8', 'newbygin_cms', 'Hunter44!#');
date_default_timezone_set('America/Los_Angeles');
?>