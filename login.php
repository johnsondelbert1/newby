<?php
require_once("includes/functions.php");

if(logged_in()){
	redirect_to($GLOBALS['HOST']."/index.php");
}

require_once("includes/login_auth.php");

$pgsettings = array(
	"title" => "Login to ".$GLOBALS['site_info']['name'],
	"pageselection" => false,
	"nav" => true,
	"banner" => 1,
	"use_google_analytics" => 1,
);
require_once("includes/begin_html.php");
?>
<h1>Login to <?php echo $GLOBALS['site_info']['name'] ?></h1>
<form method="post" action="login">
	<input type="hidden" name="redirect_to" value="/" />
    <input type="hidden" name="redirect_from" value="<?=($_SERVER['PHP_SELF']);?>"/>
    <div class="row center">
        <div class="col s12 l3">Username:</div>
        <div class="col s12 l3"><input type="text" name="username" value="<?php if(isset($user)){echo $user;} ?>" style="width:300px;"/></div>
      </div>
      <div class="row center">
        <div class="col s12 l3">Password:</div>
        <div class="col s12 l3"><input type="password" name="password" style="width:300px;" /></div>
      </div>
      <div class="row center" >
        <div class="col s10 l3">Remember Me</div>
        <div class="col s2 l1"><input name="remember" type="checkbox" value=""  id="remember"/><label for="remember"></div>
      </div>
      <div class="row center">
        <div class="col s12 l2"><input class="green btn" type="submit" name="submit" value="Login" /><br /><br />
      </div>
      <div class="row">
      <div class="col s12 l12"><a href="forgot_password_request">(Forgot password/Username?)</a></td>
      </div>
</form>

<?php
	require_once("includes/end_html.php");
?>