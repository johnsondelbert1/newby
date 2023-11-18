<?php require_once('includes/dbconnect.php'); ?>
<?php
	if(isset($_POST['editprov'])){
		$postData = $_POST;
		unset($postData['editprov']);
		$postCount = count($postData);
		$i=0;

		$sql = "UPDATE `provided` SET ";

		foreach($postData as $postName => $postVal){
			$i++;
			$sql .="`".$postName."` = :".$postName;
			if($i!=$postCount){
				$sql.=', ';
			}
		}
		$sql .=" WHERE `id` = ".urldecode($_GET['id']);

		$stmt = $db->prepare($sql);

		foreach($postData as $postName => $postVal){
			$stmt->bindParam(':'.$postName, $_POST[$postName], PDO::PARAM_STR);
		}

		if($stmt->execute()){
			$success = 'Provided Items Edited';
		}else{
			$error = 'Sorry, an error has occured.<br/>';
			foreach($stmt->errorInfo() as $execError){
				$error .= $execError.'<br/>';
			}
		}
	}

	if(isset($_GET['action'])&&$_GET['action'] == 'del'){
		$sql = "DELETE FROM `provided` WHERE `id` =  :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':id', urldecode($_GET['id']), PDO::PARAM_INT);   
		if($stmt->execute()){
			header('Location: provided.php?success='.urlencode('Provided Items Deleted'));
		}else{
			$error = 'Sorry, an error has occured.<br/>';
			foreach($stmt->errorInfo() as $execError){
				$error .= $execError.'<br/>';
			}
		}
	}

	$query = "SELECT * FROM `provided` WHERE `id` = ".urldecode($_GET['id']);
	$result = $db->query($query);
	$num_rows = $result->rowCount();
	$prov = $result->fetch(PDO::FETCH_ASSOC);

	if($num_rows==1){
		$query = "SELECT * FROM `customers` WHERE `CustomerID` = {$prov['CustomerID']}";
		$result = $db->query($query);
		$num_rows_cust = $result->rowCount();
		$cust = $result->fetch(PDO::FETCH_ASSOC);
	}
?>
<html>
	<head>
		<title>Editing Items Provided</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<script type="text/javascript">
			function confDel(id){
				var r = confirm("Are you sure you want to delete these Provided Items?");
				if (r == true) {
				    window.location.assign("<?php echo basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING']; ?>&action=del")
				}
			}
		</script>
		<div id="nav">
			<ul>
				<li><a href="index.php">Customers</a></li>
				<li><a href="veterans.php">Veterans</a></li>
				<li><a href="provided.php">Provided</a></li>
			</ul>
		</div>
		<div id="content">
		<?php if($num_rows==1){ ?>
			<h1>Editing Provided Items for "<?php echo $cust['FirstName']." ".$cust['LastName'];?>"</h1>
			<br/><a href="#" onclick="confDel(<?php echo urldecode($_GET['id']); ?>)">- Delete these Provided Items</a><br/><br/><br/>
			<?php if(isset($error)){echo '<div class="error">'.$error.'</div>';} ?>
			<?php if(isset($success)){echo '<div class="success">'.$success.'</div>';} ?>
			<form method="post" class="forms">
				<table class="form">
					<tr>
						<td>Provided</td>
						<td><textarea name="Provided" rows="5" cols="50"><?php if(isset($_POST['Provided'])){echo $_POST['Provided'];}else{echo $prov['Provided'];} ?></textarea></td>
					</tr>
				</table>
				<input type="submit" name="editprov" value="Save Provided Items" />
			</form>
			<h2>Other Provided Items Under This Customer</h2>
			<?php
			$query = "SELECT * FROM `provided` WHERE `CustomerID` = {$prov['CustomerID']}";
			$result = $db->query($query);
			$num_rows_prov = $result->rowCount();

			if($num_rows_prov>1){?>
			<strong>(<?php echo $num_rows_prov; ?>)</strong><br/><br/>
			<table class="result">
				<tr>
					<th>Edit</th>
					<th>Date</th>
					<th>Items</th>
				</tr>
				<?php while($provList = $result->fetch(PDO::FETCH_ASSOC)) {
					if($provList['id']!=urldecode($_GET['id'])){?>
				<tr>
					<td><a href="edit-provided.php?id=<?php echo $provList['id'];?>">Edit</a></td>
					<td>
	    				<?php 
	    				if($provList['Date']!='0000-00-00 00:00:00'){
	    					$dt = strtotime($provList['Date']);
	    					echo date('m/d/Y', $dt); 
	    				}else{
	    					echo 'N/A';
	    				}?>
					</td>
					<td><?php echo $provList['Provided'];?></td>
				</tr>
				<?php 
				}
				} ?>
			</table>
			<?php
			}else{
				echo '<p>[No other items under this customer]</p>';
			}
			?>
			<?php }else{ ?>
			<h1>Provided items by this ID does not exist.</h1>
			<?php } ?>
		</div>
	</body>
</html>