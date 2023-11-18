<?php
require_once("../../includes/connection.php");
require_once("../../includes/session.php");
require_once("../../includes/globals.php");

$date=date("Y/m/d H:i:s", time());

//List of existing IDs in DB
$ids_array = array();
$query = "SELECT `cid` FROM `style_colors`";
$result=mysqli_query($connection, $query);
while($row = mysqli_fetch_array($result)){
    array_push($ids_array, $row['cid']);
}

//Parse POST data from edit color page
$params = array();
parse_str($_POST['colorData'], $params);

$color_field = $params['color_field'];
$color_name = $params['color_name'];

//Get IDs that don't exist in DB
$newColors = array_diff(array_keys($params['color_field']), $ids_array);
//Add colors
$oldToNewIds = array();
foreach ($newColors as $value) {
    $name = strip_tags(mysqli_real_escape_string($connection, $color_name[$value]));
    $hex = strip_tags(mysqli_real_escape_string($connection, $color_field[$value]));

    $query="INSERT INTO `style_colors` (c_name, color_hex, date_created, creator) VALUES ('{$name}','{$hex}', '{$date}', {$_SESSION['user_id']})";
    $result=mysqli_query($connection, $query);

    $newId = mysqli_insert_id($connection);

    //Array mapping temporary new IDs to permanent new IDs
    $oldToNewIds[$value] = $newId;

}

//Get IDs that will be deleted from DB
$deletedColors = array_diff($ids_array, array_keys($params['color_field']));
//Delete colors
foreach ($deletedColors as $value) {
    $query="SELECT * FROM `style_colors` WHERE `cid` = '{$value}'";
    $result=mysqli_query($connection, $query);
    $color = mysqli_fetch_array($result);
    if($color['deletable']==1){
        $query="DELETE FROM `style_colors` WHERE `cid` = {$color['cid']}";
        $result=mysqli_query($connection, $query);

        $selectorQuery="SELECT * FROM `css_selectors` WHERE `style_color_id` = '{$color['cid']}'";
        $selectorResult=mysqli_query($connection, $selectorQuery);

        while($selector = mysqli_fetch_array($selectorResult)){
            $editQuery="UPDATE `css_selectors` SET `style_color_id` = 1 WHERE `sid` = {$selector['sid']}";
            $editResult=mysqli_query($connection, $editQuery);
        }
    }
}

foreach ($params['color_name'] as $key => $value) {
	$query="UPDATE `style_colors` SET `c_name` = '{$value}', `color_hex` = '{$params['color_field'][$key]}', `date_edited` = '{$date}', `editor` = '{$_SESSION['user_id']}' WHERE `cid` = '{$key}'";
	$result=mysqli_query($connection, $query);
}

$css = explode(';', $_POST['cssSelectors']);
foreach ($css as $value) {
	$ids = explode('|', $value);

	//Change temporary IDs to new permanent IDs before saving into DB using oldToNewIds map array
	if(strpos($ids[0],'new')!==false){
		$ids[0] = $oldToNewIds[$ids[0]];
	}

	$query="UPDATE `css_selectors` SET 
		`style_color_id` = '{$ids[0]}' WHERE `sid` = '{$ids[1]}'";
	$result=mysqli_query($connection, $query);
}

//Send map array of temp new IDs back to edit-colors.php for processing
if(!empty($oldToNewIds)){
	echo json_encode($oldToNewIds);
}
?>