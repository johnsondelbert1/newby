<?php
require_once("../includes/session.php");
require_once("../includes/functions.php");

if(isset($_POST['commentData'])){
	if(check_permission("Blog","post_comment")){
		$query="SELECT * FROM `blog` WHERE `id` = {$_POST['blogId']}";
		$result=mysqli_query( $connection, $query);
		if(mysqli_num_rows($result)==1){
			$blog_post = mysqli_fetch_array($result);
			if($blog_post['comments_allowed'] == 1){
				$date=date("Y/m/d H:i:s", time());
				$commentContent = mysqli_real_escape_string($connection, htmlspecialchars($_POST['commentData']));
				$commentContent = trim($commentContent, ' \t\n\r\0\x0B');
				if($commentContent!=''){
					$query = "INSERT INTO `blog_comments` (`blog_id`, `content`, `poster_id`, `date_posted`) VALUES ({$_POST['blogId']}, '{$commentContent}', '{$_SESSION['user_id']}', '{$date}')";
					$result=mysqli_query( $connection, $query);

					$last_inserted_comment = mysqli_insert_id($connection);
					$query = "SELECT `content` FROM `blog_comments` WHERE `bc_id` = {$last_inserted_comment}";
					$result=mysqli_query( $connection, $query);
					$commentBody = mysqli_fetch_array($result);
					?>
					<li>
						<b><a href="profile.php?user=<?php echo urlencode($user_info['username']); ?>"><?php echo $user_info['username']; ?></a>, <?php echo date("g:i A", strtotime($date)). ' on '.date("M jS 'y", strtotime($date)); ?></b><span style="float:right;" class="del-blog-comment" id="del-comment-<?php echo $last_inserted_comment; ?>" onclick="delComment(<?php echo $last_inserted_comment; ?>);"><img src="<?php echo $GLOBALS['HOST']; ?>/../images/icon-delete.png" width="16" alt="Delete Comment"></span>
						<br />
						<?php
						echo $commentBody['content']; ?>
					</li>
					<?php
				}else{
					echo "empty";
				}
			}else{
				echo 'disabled';
			}
		}else{
			echo 'deleted';
		}
	}else{
		echo "permission";
	}
}
?>