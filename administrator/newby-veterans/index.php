<?php require_once('includes/dbconnect.php'); ?>

<?php

	if(isset($_POST['search'])){



		//excluded columns to add to results table when searched

		$excluded_columns = array('ServiceRecordID','CustomerID','FirstName','LastName','Mailing_address','City','State_or_Province','ZipCode','Phone_Number','Email_Address','How_Many','Help_Seeking','Notes');



		if($_POST['search_method']=='cont'){

			$query = "SELECT * FROM `customers` WHERE `".$_POST['field']."` LIKE'%".$_POST['query']."%' ORDER BY `CustomerID` DESC";

		}elseif($_POST['search_method']=='ex') {

			$query = "SELECT * FROM `customers` WHERE `".$_POST['field']."` = '".$_POST['query']."' ORDER BY `CustomerID` DESC";

		}else{

			$query = "SELECT * FROM `customers` WHERE `".$_POST['field']."` LIKE'%".$_POST['query']."%' ORDER BY `CustomerID` DESC";

		}

	}else{

		$query = "SELECT * FROM `customers` ORDER BY `CustomerID` DESC";

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

		<title>Customers</title>



		<link rel="stylesheet" type="text/css" href="css/style.css">

	</head>

	<body>

		<div id="nav">

			<ul>

				<li><a href="index.php" class="selected">Customers</a></li>

				<li><a href="veterans.php">Veterans</a></li>

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

				foreach($db->query('SHOW COLUMNS FROM `customers`') as $row) {?>

					<option value="<?php echo $row['Field']; ?>"<?php if((isset($_POST['field'])&&$_POST['field'] == $row['Field'])||(!isset($_POST['field'])&&$row['Field']=='LastName')){echo 'selected';}?>><?php echo $row['Field']; ?></option>

				<?php } ?>

				</select>

				<input type="radio" id="contains" name="search_method" value="cont" <?php if(!isset($_POST['search_method'])||$_POST['search_method']=='cont'){echo 'checked';} ?> /><label for="contains">Contains</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="exact" name="search_method" value="ex" <?php if(isset($_POST['search_method'])&&$_POST['search_method']=='ex'){echo 'checked';} ?> /><label for="exact">Exact</label>&nbsp;&nbsp;&nbsp;

				<input type="submit" name="search" value="Search" />

			</form>

			<?php if(isset($_POST['search'])){?>

			<a href="<?php basename($_SERVER['PHP_SELF']); ?>">[Clear search]</a><br/><br/>

			<?php } ?>

			<br/><br/><a href="new-customer.php">+ New Customer</a><br/><br/>

			<h3>Results</h3>

			<?php if(!isset($_POST['search'])){echo print_page($num_pages, $current_page, "index.php");} ?>

			<br/><br/>

			<?php if(isset($_POST['search'])){?>

			<p><?php echo 'Search returned <strong>'.$num_rows.'</strong> results.'; ?></p>

			<?php } ?>

			<table class="result" cellspacing="0">

				<tr>

					<?php

					if(isset($excluded_columns)&&!array_search($_POST['field'], $excluded_columns)){

						echo '<th>'.$_POST['field'].'</th>';

					}

					?>

					<th>Service Record ID</th>

					<th>Customer ID</th>

					<th>Name</th>

					<th>Address</th>

					<th>Phone</th>

<!-- 					<th>Alt. Phone</th> -->

					<th>Email</th>

					<th>Kids</th>

<!--					<th>Partner Name</th>

 					<th>Birthday</th>

					<th>Marriage Status</th>



					<th>How Many</th>

					<th>Child 1 Age</th>

					<th>Child 2 Age</th>

					<th>Child 3 Age</th>

					<th>Child 4 Age</th>

					<th>Child 5 Age</th>

					<th>Child 6 Age</th>



					<th>How Did You Hear</th> -->



					<th>Help Seeking</th>

					<!-- <th>How Related Vet</th> -->

					<th>Notes</th>

					<th>Created</th>

				</tr>

			<?php

			while($row = $result->fetch(PDO::FETCH_ASSOC)) {?>

	    		<tr>

	    			<?php

					if(isset($excluded_columns)&&!array_search($_POST['field'], $excluded_columns)){

						echo '<td>'.$row[$_POST['field']].'</td>';

					}

					?>

	    			<td><?php echo $row['Service_Record_ID']; ?></td>

	    			<td><a href="new-provided.php?cust_id=<?php echo urlencode($row['CustomerID']); ?>"><?php echo $row['CustomerID']; ?></a></td>

	    			<td><a href="edit-customer.php?id=<?php echo urlencode($row['CustomerID']); ?>"><?php echo $row['FirstName'].' '.$row['LastName']; ?></a></td>

	    			<td><?php if($row['Mailing_address']!=''){echo $row['Mailing_address'].',<br/>';}echo $row['City']; if($row['City']!=''&&$row['State_or_Province']!=''){echo ', ';} echo $row['State_or_Province'].' '.$row['ZipCode'];?></td>

	    			<td><?php echo $row['Phone_Number']; ?></td>

	    			<!-- <td><?php echo $row['Alternate_number']; ?></td> -->

	    			<td><?php echo $row['Email_Address']; ?></td>

	    			<td><?php echo $row['How_Many']; ?></td>

<!--	    			<td><?php echo $row['PartnerName']; ?></td>

	    			<td><?php echo $row['Birthday']; ?></td>

	    			<td><?php echo $row['Marriage_Status']; ?></td>

 	    			<td><?php echo $row['How_Many']; ?></td>

	    			<td><?php echo $row['Child_Age_one']; ?></td>

	    			<td><?php echo $row['Child_Age_Two']; ?></td>

	    			<td><?php echo $row['Child_Age_Three']; ?></td>

	    			<td><?php echo $row['Child_Age_Four']; ?></td>

					<td><?php echo $row['Child_Age_Five']; ?></td>

	    			<td><?php echo $row['Child_Age_Six']; ?></td>

	    			<td><?php echo $row['How_Did_You_Hear']; ?></td> -->

	    			<td><?php echo $row['Help_Seeking']; ?></td>

	    			<!-- <td><?php echo $row['How_Related_Vet']; ?></td> -->

	    			<td><?php echo $row['Notes']; ?></td>

	    			<td>

	    			<?php if($row['date_created'] != '0000-00-00 00:00:00'){echo date('m/d/Y', strtotime($row['date_created']));}else{echo 'N/A';} ?>

	    			</td>

	    		</tr>

			<?php } ?>

			</table>

		</div>

	</body>

</html>