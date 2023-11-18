<?php require_once("../includes/functions.php"); ?>
<?php
	$query="SELECT * FROM `pages` ORDER BY `name` ASC";
	$listpagesquery=mysqli_query( $connection, $query);
	confirm_query($listpagesquery);
?>
<html>
<head>
<meta charset="utf-8">
<title>Page List</title>
<style type="text/css">
table tr:nth-child(even){
	background-color:#DDDDDD;
}
table tr:nth-child(odd){
	background-color:#BBBBBB;
}
</style>
</head>

<body>
<table cellpadding="3" border="0">
	<tr>
    	<th>Page Name</th>
        <th>Page URL</th>
    </tr>
    <?php
while($listpage=mysqli_fetch_array($listpagesquery)){?>
	<tr>
    	<td style="text-align:right;"><?php echo $listpage['name']?></td>
		<td style="text-align:left;"><a href="<?php echo $GLOBALS['HOST'].'/page/'.urlencode($listpage['name']); ?>" onclick="window.open('../index.php?page=<?php echo urlencode($listpage['name']);?>', 'newwindow', 'width=1017, height=500'); return false;"><?php echo $GLOBALS['HOST'].'/page/'.urlencode($listpage['name']); ?></a></td>
    </tr>
<?php }?>
</table>
</body>
</html>