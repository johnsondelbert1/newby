<?php
require_once("../../includes/functions.php");

if(logged_in()){
	redirect_to("index.php");
}

require_once("../../includes/login_auth.php");
?>
<html>
    <head>
        <title>Login to Veterans Database</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <h1>Login to Veterans Database</h1>
        <?php 
        if(isset($_GET['error'])){
          echo '<span style="color:#E56363; font-weight:bold;">'.urldecode($_GET['error']).'</span><br/><br/>';
        }
        ?>
        <form method="post">
        	<input type="hidden" name="redirect_to" value="index.php"/>
            <input type="hidden" name="redirect_from" value="<?=($_SERVER['PHP_SELF']);?>"/>

                Username:
                <input type="text" name="username" value="<?php if(isset($user)){echo $user;} ?>"/><br/><br/>

                Password:
                <input type="password" name="password" /><br/><br/>

                Remember Me
                <input name="remember" type="checkbox" value=""  id="remember"/><label for="remember"><br/><br/>

                <input type="submit" name="submit" value="Login" />
        </form>
    </body>
</html>