<?php 
	if(isset($_GET['error'])){
		header("Location: ../administrator/setup/setuplogin.php?error=".$_GET['error']);
	}else{
		header("Location: ../administrator/setup/setuplogin.php");
	}
?>