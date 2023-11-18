<?php
//include files if not already included
$flag = FALSE;
foreach(get_included_files() as $index => $string) {
    if (strpos($string, 'functions.php') !== FALSE)
       $flag = TRUE;
}

if($flag == FALSE){
	include("../includes/connection.php");
	include("../includes/functions.php");
}

if(isset($_GET['commlimit'])){
	$limiter = $_GET['commlimit'];
}else{
	$limiter = false;
}

if(isset($_GET['blogid'])){
	$blog_id = intval($_GET['blogid']);
	if($limiter!=false){
		//Get ordered limited number of sorted comments
		$query="SELECT * FROM (SELECT * FROM `blog_comments` WHERE `blog_id` = {$blog_id} ORDER BY `date_posted` ASC LIMIT {$limiter})  X ORDER BY `date_posted` ASC ";
		$result=mysqli_query( $connection, $query);
		$num_comments = mysqli_num_rows($result);
		//Get total number of comments
		$query="SELECT * FROM `blog_comments` WHERE `blog_id` = {$blog_id}";
		$totalresult=mysqli_query( $connection, $query);
		$totalComments = mysqli_num_rows($totalresult);
	}else{
		$query="SELECT * FROM `blog_comments` WHERE `blog_id` = {$blog_id} ORDER BY `date_posted` ASC";
		$result=mysqli_query( $connection, $query);
		$num_comments = mysqli_num_rows($result);
	}

	if($num_comments >= 1){?>

		<ul id="comment-block-<?php echo $blog_id; ?>">
			<?php while ($blog_comment = mysqli_fetch_array($result)) {
				$poster = get_user($blog_comment['poster_id']);?>
			<li>
				<b><a href="profile.php?user=<?php echo urlencode($poster['username']); ?>"><?php echo $poster['username']; ?></a>, <?php echo date("g:i A", strtotime($blog_comment['date_posted'])). ' on '.date("M jS 'y", strtotime($blog_comment['date_posted'])); ?></b><?php if(check_permission("Blog","delete_any_comment")||(isset($user_info)&&$blog_comment['poster_id']==$user_info['id'])){ ?><span style="float:right;" class="del-blog-comment" id="del-comment-<?php echo $blog_comment['bc_id']; ?>" onclick="delComment(<?php echo $blog_comment['bc_id']; ?>);"><img src="<?php echo $GLOBALS['HOST']; ?>/../images/icon-delete.png" width="16" alt="Delete Comment"></span><?php } ?><br>
				<?php echo nl2br($blog_comment['content']); ?>
			</li>
			<?php
			} ?>
		</ul>
	<?php
		if($limiter!=false&&$totalComments>$num_comments){
			echo '<br /><a href="#" class="linkspan" style="color:#039be5;" onclick="viewMore('.$_GET['blogid'].', '.(intval($_GET['commlimit'])+10).')">[View More]</a><span style="float:right;">'.$num_comments.' of '.$totalComments.'</span><br />';
		}
	}else{?>
		<ul id="comment-block-<?php echo $blog_id; ?>">
		</ul>
	<?php
	}
}else{?>
<h1>Missing blog ID</h1>
<?php
}
?>