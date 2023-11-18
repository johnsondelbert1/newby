<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $pgtitle; ?></title>
    <link rel="shortcut icon" href="images/favicon.png">
    <link href="../styles/main.css" rel="stylesheet" type="text/css" />
    <link href="../styles/fonts.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
    
    <style type="text/css">
		body{
			background-color:#0E4700;
			color:#FFFFFF;
			padding:10px;
		}
	</style>
</head>

<body>
<?php if(!empty($message)){echo $message;} ?>
<h1><?php echo $pgtitle; ?></h1>