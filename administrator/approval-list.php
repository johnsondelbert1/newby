<?php
require_once("../includes/functions.php");
if(!check_permission("Users","approve_deny_new_users")){
	redirect_to("index.php");
}

if(isset($_POST['submit'])){
	if(check_permission("Users","approve_deny_new_users")){
		if(isset($_POST['option'])){
			$approved = array();
			$denied = array();
			foreach ($_POST['option'] as $id) {
				if(substr($id, 0, 1) == 'a'){
					array_push($approved, substr($id, 1));
				}elseif(substr($id, 0, 1) == 'd'){
					array_push($denied, substr($id, 1));
				}
			}

			//Update approved users
            $query="UPDATE `users` SET `approved_admin` = 1 WHERE `id` IN (".implode(', ', $approved).")";
            $result=mysqli_query( $connection, $query);

            //Delete denied users
            foreach ($denied as $del_id) {
            	del_acc($del_id);
            }

		}else{
			$error = "No users selected!";
		}
	}else{
		$error="You do not have permission to approve/deny users!";
	}
}

?>
<?php
	$pgsettings = array(
		"title" => "Approve/Deny Accounts",
		"icon" => "icon-users"
	);
	require_once("includes/begin_cpanel.php");
?>

<h1>Approve/Deny Accounts</h1>
<form method="post" action="approval-list">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:center;">
      <tr>
        <th width="20%">User Name</th>
        <th width="40%">Email</th>
        <th width="20%">Created</th>
        <th width="10%">Approve</th>
        <th width="10%">Deny</th>
      </tr>
        <?php
            $query="SELECT * FROM `users` WHERE `approved_admin` = 0 ORDER BY `id` ASC";
            $result=mysqli_query( $connection, $query);
            confirm_query($result);
            if(mysqli_num_rows($result)>=1){
           		while($user=mysqli_fetch_array($result)){

				?>
              <tr>
                <td><?php echo $user['username'];?></td>
                <td><?php echo $user['email'];?></td>
                <td><?php if($user['created']!="0000-00-00 00:00:00"){echo date("m/d/Y h:i A" ,strtotime($user['created']));}else{echo "N/A";} ?></td>
                <td><input type="radio" name="option[<?php echo $user['id']; ?>]" id="a<?php echo $user['id']; ?>" value="a<?php echo $user['id']; ?>" /><label for="a<?php echo $user['id']; ?>"></td>
                <td><input type="radio" name="option[<?php echo $user['id']; ?>]" id="d<?php echo $user['id']; ?>" value="d<?php echo $user['id']; ?>" /><label for="d<?php echo $user['id']; ?>"></td>
              </tr>
            <?php }?>
		      <tr>
		        <th colspan="3"></th>
		        <th width="20%" colspan="2"><input class="green btn" type="submit" name="submit" value="Approve/Deny Accounts" /></th>
		      </tr>
            <?php
            }else{?>
            	<tr><td colspan="5" style="text-align:center;"><strong>[No users]</strong></td></tr>
			<?php
            } ?>
    </table>
</form>
<?php require_once("includes/end_cpanel.php"); ?>