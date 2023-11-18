<?php
require_once("../includes/functions.php");
?>
<?php
if(logged_in()&&!check_permission("Website","cpanel_access")){
	redirect_to("../index.php?error=".urlencode("You do not have permission to access that!"));
}elseif(logged_in()){
	redirect_to("index.php");
}
if(isset($_GET['error'])){
	$message="<h3 class=\"error animate shake\">".urldecode($_GET['error'])."</h3>";
}
if(isset($_GET['success'])){
	$message="<h3 class=\"success\">".urldecode($_GET['success'])."</h3>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
  ================================================== -->
    <meta charset="utf-8">
    <title>Login to <?php echo $GLOBALS['site_info']['name']; ?> CPanel</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="../styles/animate.css" rel="stylesheet" type="text/css" />

    <!-- Mobile Specific Metas
  ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
  ================================================== -->

    <!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
<style>
	@import url(http://fonts.googleapis.com/css?family=Roboto:300);

* { box-sizing:border-box; }

/* basic stylings ------------------------------------------ */
body 				 { background:url(images/blue-polygon.jpg);}
.container 		{ 
  font-family:'Roboto';
  width:600px; 
  margin:30px auto 0; 
  display:block; 
  background:#FFF;
  padding:10px 50px 50px;
}
h2 		 { 
  text-align:center; 
  margin-bottom:50px; 
}
h2 small { 
  font-weight:normal; 
  color:#888; 
  display:block; 
}
.footer 	{ text-align:center; }
.footer a  { color:#53B2C8; }

/* form starting stylings ------------------------------- */
.group 			  { 
  position:relative; 
  margin-bottom:45px; 
}
input 				{
  font-size:18px;
  padding:10px 10px 10px 5px;
  display:block;
  width:300px;
  border:none;
  border-bottom:1px solid #757575;
  
}
input:focus 		{ outline:none; }

/* LABEL ======================================= */
label 				 {
  color:#999; 
  font-size:18px;
  font-weight:normal;
  position:absolute;
  pointer-events:none;
  left:5px;
  top:10px;
  transition:0.2s ease all; 
  -moz-transition:0.2s ease all; 
  -webkit-transition:0.2s ease all;
}

/* active state */
input:focus ~ label, input:valid ~ label 		{
  top:-20px;
  font-size:14px;
  color:#5264AE;
}

/* BOTTOM BARS ================================= */
.bar 	{ position:relative; display:block; width:300px; }
.bar:before, .bar:after 	{
  content:'';
  height:2px; 
  width:0;
  bottom:1px; 
  position:absolute;
  background:#5264AE; 
  transition:0.2s ease all; 
  -moz-transition:0.2s ease all; 
  -webkit-transition:0.2s ease all;
}
.bar:before {
  left:50%;
}
.bar:after {
  right:50%; 
}

/* active state */
input:focus ~ .bar:before, input:focus ~ .bar:after {
  width:50%;
}

/* HIGHLIGHTER ================================== */
.highlight {
  position:absolute;
  height:60%; 
  width:100px; 
  top:25%; 
  left:0;
  pointer-events:none;
  opacity:0.5;
}

/* active state */
input:focus ~ .highlight {
  -webkit-animation:inputHighlighter 0.3s ease;
  -moz-animation:inputHighlighter 0.3s ease;
  animation:inputHighlighter 0.3s ease;
}

/* ANIMATIONS ================ */
@-webkit-keyframes inputHighlighter {
	from { background:#5264AE; }
  to 	{ width:0; background:transparent; }
}
@-moz-keyframes inputHighlighter {
	from { background:#5264AE; }
  to 	{ width:0; background:transparent; }
}
@keyframes inputHighlighter {
	from { background:#5264AE; }
  to 	{ width:0; background:transparent; }
}
</style>
</head>
<body>
<div class="container">
  
  <h2><?php echo $GLOBALS['site_info']['name']; ?><small>Login</small></h2>
  
  <form>
    
    <div class="group">      
      <input type="text" required tabindex="1">
      <span class="highlight"></span>
      <span class="bar"></span>
      <label>Username</label>
    </div>
      
    <div class="group">      
      <input type="text" required tabindex="2">
      <span class="highlight"></span>
      <span class="bar"></span>
      <label>Password</label>
    </div>
       <div class="group">
       <input type="submit" name="submit" value="Sign in" class="button" tabindex="4"/>
</div>
       <div class="group">
       <input name="remember" type="checkbox" value="" tabindex="3"/>Remember Me<br>
                </div>
    
  </form>
      
  
</div>
</body>
</html>