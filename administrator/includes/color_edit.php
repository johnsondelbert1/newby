<script type="text/javascript">
    function saveDragDropNodes()
    {
        var saveString = "";
        var uls = document.getElementById('connected').getElementsByTagName('UL');
        for(var no=0;no<uls.length;no++){    // Looping through all <ul>
            var lis = uls[no].getElementsByTagName('LI');
            for(var no2=0;no2<lis.length;no2++){
                if(saveString.length>0)saveString = saveString + ";";
                saveString = saveString + uls[no].id + '|' + lis[no2].id;
            }
        }
        return saveString;
    }
    function sendPost(){
        selectorArray = saveDragDropNodes();
        $.post("post/color_edit_post.php",
        {
            colorData: $('#colorData').serialize(),
            cssSelectors: selectorArray,
        },
        function(data, status){
            if(status == 'success'){
                alert(data+"Colors updated!");
            }
        });
    }
    function touchHandler(event) {
        var touch = event.changedTouches[0];

        var simulatedEvent = document.createEvent("MouseEvent");
            simulatedEvent.initMouseEvent({
            touchstart: "mousedown",
            touchmove: "mousemove",
            touchend: "mouseup"
        }[event.type], true, true, window, 1,
            touch.screenX, touch.screenY,
            touch.clientX, touch.clientY, false,
            false, false, false, 0, null);

        touch.target.dispatchEvent(simulatedEvent);
        event.preventDefault();
    }

    function init() {
        document.addEventListener("touchstart", touchHandler, true);
        document.addEventListener("touchmove", touchHandler, true);
        document.addEventListener("touchend", touchHandler, true);
        document.addEventListener("touchcancel", touchHandler, true);
    }
</script>

<form method="post" action="site-settings.php?tab=1" id="colorData">
<section id="connected">
	<?php
	$query="SELECT * FROM `style_colors`";
	$result=mysqli_query( $connection, $query);
	while($style_color=mysqli_fetch_array($result)){?>
    	<div class="card" style="overflow:visible;">
        <input name="color_field[<?php echo $style_color['cid']; ?>]" type="text" value="<?php echo $style_color['color_hex']; ?>" maxlength="7" style="margin-left: -10px; margin-right: -10px; margin-top: -10px; width:200px; text-align:center; border: 0;" class="color {hash:true}" /><br>
        <?php if($style_color['deletable'] == 1){ ?>
        <?php } ?>
        <input name="color_name[<?php echo $style_color['cid']; ?>]" type="text" value="<?php echo $style_color['c_name']; ?>" maxlength="32" /><br>
            <ul id="<?php echo $style_color['cid']; ?>" class="connected list">
            <?php
                $selector_query="SELECT * FROM `css_selectors` WHERE `style_color_id` = {$style_color['cid']}";
                $selector_result=mysqli_query( $connection, $selector_query);
                while($css_selector=mysqli_fetch_array($selector_result)){?>
                    <li id="<?php echo $css_selector['sid']; ?>"><?php echo $GLOBALS['color_styles'][$css_selector['s_name']]['disp_name']; ?></li>
                <?php } ?>
            </ul>
            <a href="site-settings.php?tab=1&delcolor=<?php echo $style_color['cid']; ?>">
            <div style="margin-left: -10px; margin-right: -10px; margin-bottom: -10px; width:100%; text-align:center; border: 0; background:#E74C3C; border-radius:0px;">
                Delete
            </div>
            </a>
        </div>
    <?php }?>
</section>
<input type="button" onclick="sendPost();" name="save_color" class="btn green" value="Save color settings" />
</form>
