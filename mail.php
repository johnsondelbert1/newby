<?php 

// THE BELOW LINE STATES THAT IF THE SUBMIT BUTTON
// WAS PUSHED, EXECUTE THE PHP CODE BELOW TO SEND THE 
// MAIL. IF THE BUTTON WAS NOT PRESSED, SKIP TO THE CODE
// BELOW THE "else" STATEMENT (WHICH SHOWS THE FORM INSTEAD). 
if ( isset ( $_POST [ 'buttonPressed' ])){ 

// REPLACE THE LINE BELOW WITH YOUR E-MAIL ADDRESS. 
$to = 'johnsondelbert1@gmail.com' ; 
$subject = 'Estimate Request' ; 

// NOT SUGGESTED TO CHANGE THESE VALUES 
$message = "Year: " . $_POST [ "Year" ] ."<br/ >Make: " . $_POST [ "Make" ] ."<br/ >Model: " . $_POST [ "Model" ] ."<br/ >Other: " . $_POST [ "Other" ]; 
$headers = "MIME-Version: 1.0 \r\nContent-type: text/html; charset=iso-8859-1 \r\nFrom: " . $_POST [ "from" ]." \r\n"; 

mail ( $to , $subject , $message , $headers ); 

// THE TEXT IN QUOTES BELOW IS WHAT WILL BE 
// DISPLAYED TO USERS AFTER SUBMITTING THE
// FORM. 
echo "<p>Your e-mail has been sent! You should receive a reply soon!</p>" ;} 
else { 
?><head>

<style>
p{
	color:#fff;
}
.text{
	width:700px;
	background-color:#666;
	border:2px solid #222;
	border-radius: 4px;
	color:#FFF;
	padding:5px;
}
input.button{
	background-color:#666;
	border:2px solid #222;
	border-radius: 4px;
	color:#333;
	padding:5px;
}
input.button:hover{
	background-color:#999;
}
</style>
</head> 

<form method= "post" action= " <?php echo $_SERVER [ 'PHP_SELF' ] ; ?> " /> 
  <table> 
    <tr> 
      <td><p></p></td> 
      <td><input class="text" autocomplete="off" name= "from" placeholder="Your e-mail address:" type= "text" /></td> 
    </tr> 
    
    <tr> 
      <td><p></p></td> 
      <td><input class="text" autocomplete="off" name= "Year" placeholder="Year" /></td> 
    </tr> 
    
    <tr> 
      <td><p></p></td> 
      <td><textarea class="text" autocomplete="off" name= "Make" placeholder="Make"></textarea></td> 
    </tr> 
    
    <tr> 
      <td><p></p></td> 
      <td><textarea class="text" autocomplete="off" name= "Model" placeholder="Model"></textarea></td> 
    </tr> 
    
    <tr> 
      <td><p></p></td> 
      <td><textarea class="text" autocomplete="off" name= "Other" placeholder="Other Information" cols= "20" rows= "6" ></textarea></td> 
    </tr> 
    
    <tr> 
      <td></td> 
      <td><input class="button" name= "buttonPressed" type= "submit" value= "Send E-mail!" /></td> 
    </tr> 
  </table>	
</form> 

<?php } ?> 