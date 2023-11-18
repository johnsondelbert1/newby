<?php
require_once("includes/session.php");
require_once("includes/functions.php");

$pgsettings = array(
	"title" => "Error 404",
	"pageselection" => false,
	"nav" => true,
	"banner" => 1,
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php");
?>

<h1>404! File not found, sorry!</h1>

<?php
require_once("includes/end_html.php");
?>