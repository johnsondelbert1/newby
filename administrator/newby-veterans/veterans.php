<?php require_once('includes/dbconnect.php'); ?>

<?php

	if(isset($_POST['search'])){

		if($_POST['search_method']=='cont'){

			$query = "SELECT * FROM `veterans` WHERE `".$_POST['field']."` LIKE'%".$_POST['query']."%' ORDER BY `ServiceRecordID` DESC";

		}elseif($_POST['search_method']=='ex') {

			$query = "SELECT * FROM `veterans` WHERE `".$_POST['field']."` = '".$_POST['query']."' ORDER BY `ServiceRecordID` DESC";

		}else{

			$query = "SELECT * FROM `veterans` WHERE `".$_POST['field']."` LIKE'%".$_POST['query']."%' ORDER BY `ServiceRecordID` DESC";

		}

	}else{

		$query = "SELECT * FROM `veterans` ORDER BY `ServiceRecordID` DESC";
		$result = $db->query($query);

		$num_rows = $result->rowCount();

		$num_pages = ceil($num_rows/25);

		if(isset($_GET['pg'])&&$_GET['pg']>=1){
			$current_page = $_GET['pg'];
		}else{
			$current_page = 0;
		}
		$query.=" LIMIT ".($current_page * 25).",25";
	}

	$result = $db->query($query);

	$num_rows = $result->rowCount();

?>

<html>

	<head>

		<title>Veterans</title>



		<link rel="stylesheet" type="text/css" href="css/style.css">

	</head>

	<body>

		<div id="nav">

			<ul>

				<li><a href="index.php">Customers</a></li>

				<li><a href="veterans.php" class="selected">Veterans</a></li>

				<li><a href="provided.php">Provided</a></li>

			</ul>

		</div>

		<div id="content">

			<?php

			if(isset($_GET['success'])){

				echo '<div class="success">'.$_GET['success'].'</div>';

			}

			 ?>

			<h3>Search</h3>

			<form method="post">

				<input type="text" name="query" value="<?php if(isset($_POST['query'])){echo $_POST['query'];}?>" />

				<select name="field">

				<?php

				foreach($db->query('SHOW COLUMNS FROM `veterans`') as $row) {?>

					<option value="<?php echo $row['Field']; ?>"<?php if((isset($_POST['field'])&&$_POST['field'] == $row['Field'])||(!isset($_POST['field'])&&$row['Field']=='LastName')){echo 'selected';}?>><?php echo $row['Field']; ?></option>

				<?php } ?>

				</select>

				<input type="radio" id="contains" name="search_method" value="cont" <?php if(!isset($_POST['search_method'])||$_POST['search_method']=='cont'){echo 'checked';} ?> /><label for="contains">Contains</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="exact" name="search_method" value="ex" <?php if(isset($_POST['search_method'])&&$_POST['search_method']=='ex'){echo 'checked';} ?> /><label for="exact">Exact</label>&nbsp;&nbsp;&nbsp;

				<input type="submit" name="search" value="Search" />

			</form>

			<?php if(isset($_POST['search'])){?>

			<a href="<?php basename($_SERVER['PHP_SELF']); ?>">[Clear search]</a><br/><br/>

			<?php } ?>

			<br/><br/><a href="new-veteran.php">+ New Veteran</a><br/><br/>

			<h3>Results</h3>

			<?php if(!isset($_POST['search'])){echo print_page($num_pages, $current_page, "veterans.php");} ?>

			<br/><br/>

			<?php if(isset($_POST['search'])){?>

			<p><?php echo 'Search returned <strong>'.$num_rows.'</strong> results.'; ?></p>

			<?php } ?>

			<table class="result" cellspacing="0">

				<tr>

					<th>Service Record ID</th>

					<th>Name</th>

					<th>Deployed</th>

					<th>Branch</th>

					<th>Where</th>

					<th>Current Status</th>

					<th>Years Service</th>

					<th>Discharge</th>

					<th>Veteran Status Checked</th>

					<th>SS Last Four</th>

				</tr>

			<?php

			while($row = $result->fetch(PDO::FETCH_ASSOC)) {?>

	    		<tr>

	    			<td><a href="new-customer.php?serv_id=<?php echo urlencode($row['ServiceRecordID']); ?>"><?php echo $row['ServiceRecordID']; ?></a></td>

	    			<td><a href="edit-veteran.php?id=<?php echo urlencode($row['ServiceRecordID']); ?>"><?php echo $row['FirstName'].' '.$row['LastName']; ?></a></td>

	    			<td><?php echo ($row['Deployed_Combat']) ? "Yes" : "No"; ?></td>

	    			<td><?php echo $row['Branch_Of_Service']; ?></td>

	    			<td><?php echo $row['Where']; ?></td>

	    			<td><?php echo $row['Current_Military_Status']; ?></td>

	    			<td><?php echo $row['Years_Of_Service']; ?></td>

	    			<td><?php echo $row['Discharge']; ?></td>

	    			<td><?php echo $row['Veteran_Status_Checked']; ?></td>

	    			<td><?php echo $row['SS_Last_Four']; ?></td>

	    		</tr>

			<?php } ?>

			</table>

		</div>

	</body>

</html>