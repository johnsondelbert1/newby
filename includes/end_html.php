            	</div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="footerwrap">
        <div id="footer">
            <div id="footer-content" style="float:left;">
                <?php echo $GLOBALS['site_info']['contact_phone'];?><br/>
                <?php echo $GLOBALS['site_info']['address_line1'];if($GLOBALS['site_info']['address_line2']!=''){echo ', '.$GLOBALS['site_info']['address_line2'];}?><br/>
                <?php echo $GLOBALS['site_info']['address_city'];if($GLOBALS['site_info']['address_stateregion']!=''){echo ', '.$GLOBALS['site_info']['address_stateregion'];} echo ' '.$GLOBALS['site_info']['address_zip'];?><br/>
                <?php echo $GLOBALS['site_info']['address_country'];?><br/>
                <?php echo '<a href="mailto:'.$GLOBALS['site_info']['contact_email'].'">'.$GLOBALS['site_info']['contact_email'].'</a>';?>
                
            </div>
            <div id="footer-content" style="float:left;">
                <?php if($GLOBALS['site_info']['footer_content']!=""){echo $GLOBALS['site_info']['footer_content']."<br>";} ?>
            </div>
            <div style="text-align:right; font-size:14px; float:right;" id="login-status">
                <?php check_login(); ?><br/><br/><br/>
                <?php echo $GLOBALS['site_info']['name']; ?><?php if($GLOBALS['site_info']['copyright_text']!=''){echo ' '.$GLOBALS['site_info']['copyright_text'];} ?>, Copyright © <?php echo date("Y"); ?>.
            </div>
        </div>
    </div>
    <!-- Website designed by Second Gen Design -->
    <script type="text/javascript">
    <?php
    //Display messages
    if(isset($error)&&!is_array($error)){
        echo "Materialize.toast('".$error."', 8000, 'red');";
    }elseif(isset($error)&&is_array($error)&&!empty($error)){
        foreach ($error as $value) {
            echo "Materialize.toast('".$value."', 8000, 'red');";
        }
    } 
    ?>
    <?php
    if(isset($success)&&!is_array($success)){
        echo "Materialize.toast('".$success."', 8000, 'green');";
    }elseif(isset($success)&&is_array($success)&&!empty($success)){
        foreach ($success as $value) {
            echo "Materialize.toast('".$value."', 8000, 'green');";
        }
    } 
    ?>
    <?php
    if(isset($message)&&!is_array($message)){
        echo "Materialize.toast('".$message."', 8000, 'yellow');";
    }elseif(isset($message)&&is_array($message)&&!empty($message)){
        foreach ($message as $value) {
            echo "Materialize.toast('".$value."', 8000, 'yellow');";
        }
    } 
    ?>
    	var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
    </script>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function(){
            $("a[rel^='prettyPhoto']").prettyPhoto();

            autosize(document.querySelectorAll('textarea'));

            // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
            $('.modal-trigger').leanModal();

            $('.tooltipped').tooltip({delay: 50});
        });
    </script>
        <!--Import jQuery before materialize.js-->
      <!--<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>-->
      
       <script type="text/javascript">
    //custom JS code
	
	// Initialize collapse button
  $('.button-collapse').sideNav({
      menuWidth: 300, // Default is 240
      edge: 'left', // Choose the horizontal origin
      closeOnClick: true // Closes side-nav on <a> clicks, useful for Angular/Meteor
    }
  );
  </script>
    </body>
</html>