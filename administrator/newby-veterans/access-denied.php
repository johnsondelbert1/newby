<?php
require_once('../../includes/functions.php');
echo check_permission("Website","edit_veterans");
if(check_permission("Website","edit_veterans")){
	redirect_to('index.php');
}
?>
<html>
	<head>
		<title>Access Denied</title>
	</head>
	<body>
		<h1>Sorry, you do not have access to the Veterans Database.</h1>
	</body>
</html>