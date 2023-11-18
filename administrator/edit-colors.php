<?php
require_once("../includes/functions.php");
?>
<?php
    $pgsettings = array(
        "title" => "Edit Site Colors",
        "icon" => "icon-palette"
    );
    require_once("includes/begin_cpanel.php");
?>
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
                if(data!=''){
                    var newIds = $.parseJSON(data);
                    $.each(newIds, function(index,val){
                        //Update ID & name of color field with permanent ID
                        $('[name="color_field['+index+']"]').attr('id',val).attr('name', 'color_field['+val+']');
                        //Update name of color name field permanent ID
                        $('[name="color_name['+index+']"]').attr('name', 'color_name['+val+']');
                        //Update ID of list sort UL permanent ID
                        $('ul#'+index).attr('id', val);
                    });
                }
                Materialize.toast('Colors updated!', 8000, 'green');
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
    $(document).on("click","button.click-delete",function(){
        list = $(this).parent().find("ul.ui-sortable").children();
        firstColor = $("#connected div").first().find("ul.ui-sortable");
        list.appendTo(firstColor);
        $(this).parent().parent().remove();
        $grid.masonry('layout');
    });
    $(document).on("click","button.click-new-color",function(){
        colorName = $("#new_color_name").val();
        colorHex = $("#new_color_hex").val();
        //random number for unique ID
        randomNum = Math.floor(Math.random() * 9000) + 1000;
        var $newCard = $("<div class='card'><input name='color_field[new"+randomNum+"]' id='new"+randomNum+"' type='text' value='"+colorHex+"' maxlength='7' class='jscolor {hash:true}' /><br><div class='card-color-body'><input name='color_name[new"+randomNum+"]' type='text' value='"+colorName+"' maxlength='32' /><br><ul id='new"+randomNum+"' class='connected list ui-sortable'></ul><button type='button' class='card-color-delete click-delete'>Delete</button></div></div>");
        //Masonry
        $grid.append($newCard).masonry('appended', $newCard);
        //jscolor
        var input = document.getElementById("new"+randomNum);
        var njscolor = new jscolor(input, {hash:true});

        //Resort cards when color added
        $('.sortable').sortable();
        $('.handles').sortable({
            handle: 'span'
        });
        $('.connected').sortable({
            connectWith: '.connected'
        });
    });
    //Resort cards when style dropped
    $(document).on( "sortreceive", ".connected", function( event, ui ) {
        $grid.masonry('layout');
    });
</script>

<input type="button" onclick="sendPost();" name="save_color" class="btn green" value="Save color styles" /><br/><br/>

<label for="new_color_name">Name</label><input type="text" id="new_color_name" name="new_color_name" value="<?php if(isset($_POST['new_color_name'])){echo $_POST['new_color_name'];} ?>" maxlength="32" />
<label for="new_color_hex">Color</label><input type="text" id="new_color_hex" name="new_color_hex" value="<?php if(isset($_POST['new_color_hex'])){echo $_POST['new_color_hex'];} ?>" maxlength="7" class="jscolor {hash:true}" />
<button type="button" name="new_color" class="btn green click-new-color">Create new color</button>
<br/>
<form id="colorData">
<section id="connected" style="overflow-y:auto;">
	<?php
	$query="SELECT * FROM `style_colors`";
	$result=mysqli_query( $connection, $query);
	while($style_color=mysqli_fetch_array($result)){?>
    	<div class="card">
            <input name="color_field[<?php echo $style_color['cid']; ?>]" type="text" value="<?php echo $style_color['color_hex']; ?>" maxlength="7" class="jscolor {hash:true}" /><br>
            <div class="card-color-body">
                <input name="color_name[<?php echo $style_color['cid']; ?>]" type="text" value="<?php echo $style_color['c_name']; ?>" maxlength="32" /><br>
                <ul id="<?php echo $style_color['cid']; ?>" class="connected list">
                <?php
                    $selector_query="SELECT * FROM `css_selectors` WHERE `style_color_id` = {$style_color['cid']}";
                    $selector_result=mysqli_query( $connection, $selector_query);
                    while($css_selector=mysqli_fetch_array($selector_result)){?>
                        <li id="<?php echo $css_selector['sid']; ?>"><?php echo $GLOBALS['color_styles'][$css_selector['s_name']]['disp_name']; ?></li>
                    <?php } ?>
                </ul>
                <?php if($style_color['deletable'] == 1){ ?>
                <button type="button" class="card-color-delete click-delete">
                    Delete
                </button>
                <?php } ?>
            </div>
        </div>
    <?php }?>
</section>
</form>
<?php
    require_once("includes/end_cpanel.php");
?>