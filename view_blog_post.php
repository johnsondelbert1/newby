<?php
require_once("includes/session.php");
require_once("includes/functions.php");

if(isset($_GET['post'])&&$_GET['post']!=''){
	$query="SELECT * FROM `blog` WHERE `id` = {$_GET['post']}";
	$result=mysqli_query( $connection, $query);
	$post=mysqli_fetch_array($result);
}else{
	redirect_to($GLOBALS['HOST']."/blog");
}

$query="SELECT * FROM `pages` WHERE `type` = 'Blog' ";
$result_page_prop=mysqli_query( $connection, $query);
$page_properties = mysqli_fetch_array($result_page_prop);

$pgsettings = array(
	"title" => $post['title'],
	"pageselection" => "blog",
	"nav" => $page_properties['horiz_menu_visible'],
	"banner" => $page_properties['banner'],
	"slider" => $page_properties['slider'],
	"use_google_analytics" => 1,
);

//Default limit number blog comments
$_GET['commlimit']=10;

require_once("includes/begin_html.php");
?>
<script type="text/javascript">
function sendComment(postId){
    $('#comment-sendbtn-'+postId).html('<img src="images/ajax-load.gif" style="margin-left:10px; margin-top:10px;"/>');
    $.post("ajax_processing/post_blog_comment.php",
    {
        commentData: $('#blog-comment-'+postId).val(),
        blogId: postId,
    },
    function(data, status){
        if(status == 'success'){
            switch(data){
                case 'empty':
                    Materialize.toast('Comment cannot be empty.', 8000, 'red');
                    $('#comment-sendbtn-'+postId).html('<i class="material-icons">send</i>');
                    $('#blog-comment-'+postId).val("");
                    $('#blog-comment-'+postId).attr('rows', '1');
                    $('#blog-comment-'+postId).height('auto');
                    break;
                case 'permission':
                    Materialize.toast('You do not have permission to post comments.', 8000, 'red');
                    $('#comment-sendbtn-'+postId).html('<i class="material-icons">send</i>');
                    break;
                case 'disabled':
                    Materialize.toast('Comments have been disabled for this post.', 8000, 'red');
                    $('#comment-sendbtn-'+postId).html('<i class="material-icons">send</i>');
                    break;
                case 'deleted':
                    Materialize.toast('Blog post has been deleted.', 8000, 'red');
                    $('#comment-sendbtn-'+postId).html('<i class="material-icons">send</i>');
                    break;
                default:
                    $("#comment-block-"+postId).append(data);
                    $('#blog-comment-'+postId).val("");
                    $('#blog-comment-'+postId).attr('rows', '1');
                    $('#blog-comment-'+postId).height('auto');
                    $('#comment-sendbtn-'+postId).html('<i class="material-icons">send</i>');
                    break;
            }
        }else{
            Materialize.toast('An error has occured. Try again later.', 8000, 'red');
            $('#comment-sendbtn-'+postId).html('<i class="material-icons">send</i>');
        }
    });
}
function viewMore(postId, numComments){
        $.get("ajax_processing/get_blog_comments.php?blogid="+postId+"&commlimit="+numComments, function(data, status){
            if(status == 'success'){
                $('#comment-wrap-'+postId).html(data);
            }else{
                Materialize.toast('An error has occured. Try again later.', 8000, 'red');
            }
        });
    }
function delComment(postId){
        $.post("ajax_processing/delete_blog_comment.php",
        {
            id: postId,
        },
            function(data, status){
            if(status == 'success'){
                switch(data){
                    case 'invalPerms':
                        Materialize.toast('Insufficient permission.', 8000, 'red');
                        break;
                    case 'notExist':
                        Materialize.toast('Comment does not exist.', 8000, 'red');
                        break;
                    default:
                        $('#del-comment-'+postId).parent().remove();
                        Materialize.toast('Comment deleted!', 8000, 'green');
                        break;
                }
            }else{
                Materialize.toast('An error has occured. Try again later.', 8000, 'red');
            }
        });
    }
</script>
<h1>View Blog Post</h1>
<?php

	$query="SELECT * FROM `users` WHERE `id` = ".$post['poster'];
	$userresult=mysqli_query( $connection, $query);
	$userdata=mysqli_fetch_array($userresult);
	$createdtimestamp = strtotime($post['datecreated']);
	$lasteditedtimestamp = strtotime($post['datecreated']);
	?>
    <table width="200" border="0">
  <tr>
    <td><?php if(check_permission("Blog","edit_blog")||(isset($_SESSION['user_id'])&&$post['poster']==$_SESSION['user_id'])){?><a class="btn red" href="page/<?php echo $GLOBALS['blog_page']; ?>?delpost=<?php echo $post['id']; ?>">Delete</a><?php } ?></td>
    <td><?php if(check_permission("Blog","edit_blog")||(isset($_SESSION['user_id'])&&$post['poster']==$_SESSION['user_id'])){?><a class="btn blue" href="edit_blog_post?post=<?php echo $post['id'] ?>">Edit</a><?php } ?></td>
    <td><a class="btn blue" href="<?php echo $GLOBALS['HOST']."/page/".urlencode($page_properties['name']);?>">Back</a></td>
  </tr>
</table>
<table width="100%" height="100%" class="blog">
    <tr>
        <td colspan="2">
            <table class="blogtitle" width="100%" height="100%">
                <tr>
                    <td width="70%">
                        <?php echo $post['title']; ?>
                    </td>
                    <td>
                        <?php echo date("F j, Y", $createdtimestamp);?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="80%" valign="top">
            <table width="100%" height="100%">
                <tr>
                    <td class="blogname" height="30">
                        Posted by: <a href="profile.php?user=<?php echo urlencode($userdata['username']); ?>"><?php echo $userdata['username']; ?></a> at <?php echo date("g:i A", $createdtimestamp);
                        if($post['lastedited']!="0000-00-00 00:00:00"){
                            $lasteditedtimestamp = strtotime($post['lastedited']);
                                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edited: ".date("F j, Y, g:i A", $lasteditedtimestamp);
                            }?>
                    </td>
                </tr>
                <tr>
                    <td class="blogbody" valign="top">
                        <?php echo $post['content'];?>
                    </td>
                <?php if($post['comments_allowed'] == 1){?>
                <tr>
                    <td>
                        <div class="blog-comments">
                            <div id="comment-wrap-<?php echo $post['id']; ?>">
                                <?php 
                                $_GET['blogid'] = $post['id'];
                                include("ajax_processing/get_blog_comments.php"); ?>
                            </div>
                            <?php if(check_permission("Blog","post_comment")){?>
                            <br />
                            <textarea id="blog-comment-<?php echo $post['id'];?>" maxlength="1000" rows="1" placeholder="Write a comment..." style="width:300px; height:30px; resize:none; border-bottom:1px solid; margin-right:5px;" /></textarea><a onclick="sendComment(<?php echo $post['id'];?>)" id="comment-sendbtn-<?php echo $post['id'];?>" class="btn-floating green" ><i class="material-icons">send</i></a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </td>
        </tr>
        <tr>
        	<td>
				
            </td>
        </tr>
    </tr>
</table>
<?php
	require_once("includes/end_html.php");
?>