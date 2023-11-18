<?php require_once('includes/dbconnect.php'); ?>

<?php

	if(isset($_POST['search'])){

		if($_POST['search_method']=='cont'){

			$query = "SELECT * FROM `provided` WHERE `".$_POST['field']."` LIKE'%".$_POST['query']."%' ORDER BY `Date` DESC";

		}elseif($_POST['search_method']=='ex') {

			$query = "SELECT * FROM `provided` WHERE `".$_POST['field']."` = '".$_POST['query']."' ORDER BY `Date` DESC";

		}else{

			$query = "SELECT * FROM `provided` WHERE `".$_POST['field']."` LIKE'%".$_POST['query']."%' ORDER BY `Date` DESC";

		}

	}else{
		if(!isset($_POST['datesearch'])){
			$query = "SELECT * FROM `provided` ORDER BY `Date` DESC";
		}

	}



	if(isset($_POST['datesearch'])){

		$datefrom = strtotime($_POST['datefrom']);

		$datefrom = date('Y/m/d', $datefrom);

		if($_POST['dateto']!=''){

			$dateto = strtotime($_POST['dateto']);

			$dateto = date('Y/m/d', $dateto);

		}else{

			$dateto = date('Y/m/d', time());

		}



		$query = "SELECT * FROM `provided` WHERE `Date` between '".$datefrom."' and '".$dateto."' ORDER BY `Date` DESC";

	}else{
		if(!isset($_POST['search'])){
			$query = "SELECT * FROM `provided` ORDER BY `Date` DESC";
		}
	}

	if($result = $db->query($query)){
		if(!isset($_POST['search'])&&!isset($_POST['datesearch'])){
			$num_rows = $result->rowCount();

			$num_pages = ceil($num_rows/50);

			if(isset($_GET['pg'])&&$_GET['pg']>=1){
				$current_page = $_GET['pg'];
			}else{
				$current_page = 0;
			}
			$query.=" LIMIT ".($current_page * 50).",50";
		}
		$result = $db->query($query);

		$num_rows = $result->rowCount();

	}else{

		die('SQL Error.');

	}

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

				<li><a href="veterans.php">Veterans</a></li>

				<li><a href="provided.php" class="selected">Provided</a></li>

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

				foreach($db->query('SHOW COLUMNS FROM `provided`') as $row) {?>

					<option value="<?php echo $row['Field']; ?>"<?php if(isset($_POST['field'])&&$_POST['field'] == $row['Field']){echo 'selected';}?>><?php echo $row['Field']; ?></option>

				<?php } ?>

				</select>

				<input type="radio" id="contains" name="search_method" value="cont" <?php if(!isset($_POST['search_method'])||$_POST['search_method']=='cont'){echo 'checked';} ?> /><label for="contains">Contains</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="exact" name="search_method" value="ex" <?php if(isset($_POST['search_method'])&&$_POST['search_method']=='ex'){echo 'checked';} ?> /><label for="exact">Exact</label>&nbsp;&nbsp;&nbsp;

				<input type="submit" name="search" value="Search" />

			</form>

			<?php if(isset($_POST['search'])){?>

			<a href="<?php basename($_SERVER['PHP_SELF']); ?>">[Clear search]</a><br/><br/>

			<?php } ?>



			<h3>Date Search</h3>

			<form method="post">

				<?php 

				$date = date("m/d/Y", time());

				$fromDate = date("m/d/Y", strtotime('-1 month'));

				?>

				<input type="text" name="datefrom" placeholder="<?php echo $fromDate; ?>" value="<?php if(isset($_POST['datefrom'])){echo $_POST['datefrom'];}?>" /> to 

				<input type="text" name="dateto" placeholder="<?php echo $date; ?>" value="<?php if(isset($_POST['dateto'])){echo $_POST['dateto'];}?>" />

				<input type="submit" name="datesearch" value="Search" />

			</form>

			<?php if(isset($_POST['datesearch'])){?>

			<a href="<?php basename($_SERVER['PHP_SELF']); ?>">[Clear date search]</a><br/><br/>

			<?php } ?>



			<br/><br/><a href="new-provided.php">+ New Provided</a><br/><br/>

			<h3>Results</h3>

			<?php if(!isset($_POST['search'])&&!isset($_POST['datesearch'])){echo print_page($num_pages, $current_page, "provided.php");} ?>

			<br/><br/>

			<p><?php echo '<strong>'.$num_rows.'</strong> results.'; ?></p>

			<table class="result" cellspacing="0">

				<tr>

					<th>ID</th>

					<th>Customer ID</th>

					<th>Date</th>

					<th>Provided</th>

				</tr>

			<?php

			while($row = $result->fetch(PDO::FETCH_ASSOC)) {?>

	    		<tr>

	    			<td><?php echo $row['id']; ?></a></td>

	    			<td><a href="edit-customer.php?id=<?php echo urlencode($row['CustomerID']); ?>"><?php echo $row['CustomerID']; ?></td>

	    			<td>

	    				<?php 

	    				if($row['Date']!='0000-00-00 00:00:00'){

	    					$dt = strtotime($row['Date']);

	    					echo date('m/d/Y', $dt); 

	    				}else{

	    					echo 'N/A';

	    				}?>

	    			</td>

	    			<td><a href="edit-provided.php?id=<?php echo urlencode($row['id']); ?>"><?php echo $row['Provided']; ?></a></td>

	    		</tr>

			<?php } ?>

			</table>

		</div>

	</body>

</html>