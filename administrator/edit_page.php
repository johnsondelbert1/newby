<?php
require_once("../includes/functions.php");
?>
<?php
if(isset($_GET['action'])&&$_GET['action']=='newpage'){
	if(!check_permission("Pages","add_pages")){
		redirect_to("index.php");
	}
}elseif(isset($_GET['action'])&&$_GET['action']=='edit'){
	if(!check_permission("Pages","edit_pages")){
		redirect_to("index.php");
	}
}

$query="SELECT * FROM `pages`";
$result=mysqli_query( $connection, $query);
$rows=mysqli_num_rows($result);

if(($rows)!=0||(isset($_GET['action'])&&$_GET['action']=='newpage')){
	if((isset($_GET['action'])&&isset($_GET['page']))||isset($_GET['action'])&&$_GET['action']=='newpage'){
		if(isset($_POST['submit'])||isset($_POST['sandb'])||isset($_POST['sandnewp'])){
			
			$content= mysqli_real_escape_string($connection, $_POST['content']);
			$name=mysqli_real_escape_string($connection, $_POST['name']);
				
			$query="SELECT * FROM `pages` WHERE `name`='{$name}'";
			$result = mysqli_query( $connection, $query);
			$samenames = mysqli_num_rows($result);
			
			$query="SELECT * FROM `pages` WHERE `id`='{$_GET['page']}'";
			$result = mysqli_query( $connection, $query);
			$current_page = mysqli_fetch_array($result);
			
			if($samenames==0||$current_page['name']==$_POST['name']){
				//prep POST values
				$id=$_GET['page'];
				$position=$_POST['pgorder'];
				
				$issub=0;
				if($_POST['sub']!='none'){
					$issub=1;
				}
				if($issub==1){$subpg=$_POST['sub'];}else{$subpg=0;}
				
				if(isset($_POST['published'])){$published=1;}else{$published=0;}
				if(isset($_POST['banner'])){$banner=1;}else{$banner=0;}
				if(isset($_POST['horiz_menu'])){$horiz_menu=1;}else{$horiz_menu=0;}
				if(isset($_POST['vert_menu'])){$vert_menu=1;}else{$vert_menu=0;}
				if(isset($_POST['horiz_menu_visible'])){$horiz_menu_visible=1;}else{$horiz_menu_visible=0;}
				if(isset($_POST['vert_menu_visible'])){$vert_menu_visible=1;}else{$vert_menu_visible=0;}
				
				if(isset($_POST['url'])){$url=$_POST['url'];}else{$url="";}
				
				if(isset($_POST['galleries'])&&$_POST['galleries']!=""){
					$galleries=serialize($_POST['galleries']);
				}else{
					$galleries="";
				}
				if(isset($_POST['forms'])&&$_POST['forms']!=""){
					$forms=serialize($_POST['forms']);
				}else{
					$forms="";
				}
				//prep visibility POST array
				if(isset($_POST['visible'])&&$_POST['visible']!=""){
					if($_POST['visible'][0]!=""){
						if(count($_POST['visible'])>1){
							$visible=serialize(array($_POST['visible'][0]));
						}else{
							$visible=serialize($_POST['visible']);
						}
					}else{
						if(count($_POST['visible'])>1){
							$visible=$_POST['visible'];
							array_shift($visible);
							$visible=serialize($visible);
						}else{
							$visible=serialize(array('any'));
						}
						
					}
				}else{
					$visible=serialize(array('any'));
				}
				
				date_default_timezone_set($GLOBALS['site_info']['timezone']);
				$date=date("Y/m/d H:i:s", time());
				
				//Check for another Blog or Forum page type
				if($_POST['pgtype']=="Blog" || $_POST['pgtype']=="Forum"){
					$query="SELECT * FROM `pages` WHERE `type`='{$_POST['pgtype']}'";
					$result_pgtype = mysqli_query( $connection, $query);
					
					if(mysqli_num_rows($result_pgtype)>=1){
						$other_page_name = "";
						while ($pagetype = mysqli_fetch_array($result_pgtype)){
							if($pagetype['id']!=$id){
								$query = "UPDATE `pages` SET `type`='Custom' WHERE id = {$pagetype['id']}";
								mysqli_query( $connection, $query);
								$other_page_name = $pagetype['name'];
							}else{
								$success = "The page was successfully updated.";
							}
						}
						if(!isset($success)){$success = "The page was successfully updated, but there is only one ".$_POST['pgtype']." type page allowed. \"".$other_page_name."'s\" page type was changed to \"Custom\".";}
					}else{
						$success = "The page was successfully updated.";
					}
				}
				
				$query = "UPDATE `pages` SET `content` = '{$content}', `name`='{$name}', `position`={$position}, `published`='{$published}', 
				`issubpage`={$issub}, `parent`={$subpg}, `galleries`='{$galleries}', `doc_folder`='{$_POST['pgdocfolder']}', `forms`='{$forms}', `visible`='{$visible}', `type`='{$_POST['pgtype']}', `target`='{$_POST['target']}', 
				`banner`={$banner}, `slider`={$_POST['slider']}, `url`='{$url}', `lastedited`='{$date}', `editor`={$_SESSION['user_id']}, `horiz_menu`={$horiz_menu}, `vert_menu`={$vert_menu}, 
				`horiz_menu_visible`={$horiz_menu_visible}, `vert_menu_visible`={$vert_menu_visible} WHERE id = {$id}";
				
				$result = mysqli_query( $connection, $query);
				if (mysqli_affected_rows($connection) == 1) {
					if(!isset($success)){$success = "The page was successfully updated.";}
				} else {
					$error = "The page could not be updated.";
					$error .= "<br />" . mysqli_error($connection);
				}
				
				if(isset($_POST['sandb'])){
					redirect_to("page_list.php");
				}elseif(isset($_POST['sandnewp'])){
					redirect_to("edit_page.php?action=newpage");
				}
			}else{
				$error = "There is already a page of the same name, \"".$_POST['name']."\".";
			}
		}elseif(isset($_POST['newpage'])||isset($_POST['newandb'])||isset($_POST['newandnewp'])){	
			
			$content=mysqli_real_escape_string($connection, $_POST['content']);
			$name=mysqli_real_escape_string($connection, $_POST['name']);
			$position=$_POST['pgorder'];
			
			$query="SELECT * FROM `pages` WHERE `name`='{$name}'";
			$result = mysqli_query( $connection, $query);
			$samenames = mysqli_num_rows($result);
			
			if($samenames==0){	
				$issub=0;
				if($_POST['sub']!='none'){
					$issub=1;
				}
				if($issub==1){$subpg=$_POST['sub'];}else{$subpg=0;}
				
				if(isset($_POST['published'])){$published=1;}else{$published=0;}
				if(isset($_POST['banner'])){$banner=1;}else{$banner=0;}
				if(isset($_POST['horiz_menu'])){$horiz_menu=1;}else{$horiz_menu=0;}
				if(isset($_POST['vert_menu'])){$vert_menu=1;}else{$vert_menu=0;}
				if(isset($_POST['horiz_menu_visible'])){$horiz_menu_visible=1;}else{$horiz_menu_visible=0;}
				if(isset($_POST['vert_menu_visible'])){$vert_menu_visible=1;}else{$vert_menu_visible=0;}
				
				if(isset($_POST['url'])){$url=$_POST['url'];}else{$url="";}
				
				if(isset($_POST['galleries'])&&$_POST['galleries']!=""){
					$galleries=serialize($_POST['galleries']);
				}else{
					$galleries="";
				}
				if(isset($_POST['forms'])&&$_POST['forms']!=""){
					$forms=serialize($_POST['forms']);
				}else{
					$forms="";
				}
				//prep visibility POST array
				if(isset($_POST['visible'])&&$_POST['visible']!=""){
					if($_POST['visible'][0]!=""){
						if(count($_POST['visible'])>1){
							$visible=serialize(array($_POST['visible'][0]));
						}else{
							$visible=serialize($_POST['visible']);
						}
					}else{
						if(count($_POST['visible'])>1){
							$visible=$_POST['visible'];
							array_shift($visible);
							$visible=serialize($visible);
						}else{
							$visible=serialize(array('any'));
						}
						
					}
				}else{
					$visible=serialize(array('any'));
				}

				date_default_timezone_set($GLOBALS['site_info']['timezone']);
				$date=date("Y/m/d H:i:s", time());
				
				if($_POST['name']!=""){
					$query="INSERT INTO `pages` 
					(`name`, `content`, `position`, `issubpage`, `parent`, `published`, `galleries`, `doc_folder`, `forms`, `visible`, `type`, `target`, `banner`, `slider`, `url`, `created`, `creator`, `horiz_menu`, `vert_menu`, `horiz_menu_visible`, `vert_menu_visible`) 
					VALUES 
					('{$name}', '{$content}', '{$position}', '{$issub}', '{$subpg}', {$published}, '{$galleries}', '{$_POST['pgdocfolder']}', '{$forms}', '{$visible}', '{$_POST['pgtype']}', '{$_POST['target']}', {$banner}, {$_POST['slider']}, '{$url}', '{$date}', {$_SESSION['user_id']}, {$horiz_menu}, {$vert_menu}, {$horiz_menu_visible}, {$vert_menu_visible})";
					$result = mysqli_query( $connection, $query);
					confirm_query($result);
					if(isset($_POST['newpage'])){
						$query="SELECT * FROM `pages` ORDER BY `id` DESC LIMIT 1";
						$result=mysqli_query( $connection, $query);
						$editedpage=mysqli_fetch_array($result);
						redirect_to("edit_page.php?action=edit&page=".$editedpage['id']);
					}elseif(isset($_POST['newandb'])){
						redirect_to("page_list.php");
					}elseif(isset($_POST['newandnewp'])){
						redirect_to("edit_page.php?action=newpage");
					}
				}else{
					$error = "Page name cannot be blank.";
				}
			}else{
				$error = "There is already a page of the same name, \"".$_POST['name']."\".";
			}
			
		}elseif(isset($_GET['action'])&&$_GET['action']=="delpage"){
			$query="SELECT * FROM `pages` WHERE `id`={$_GET['page']}";
			$getpagequery=mysqli_query( $connection, $query);
			confirm_query($getpagequery);
			$page=mysqli_fetch_array($getpagequery);
			$query="DELETE FROM `pages` WHERE `id`={$page['id']}";
			$result = mysqli_query( $connection, $query);
			if($result){
				redirect_to('page_list?success='.urlencode("Deleted page ".$page["name"]."!"));
			}
		}	
	}else{
		$query="SELECT * FROM `pages` ORDER BY `position` ASC LIMIT 1";
		$result=mysqli_query( $connection, $query);
		$firstpage=mysqli_fetch_array($result);
		redirect_to("edit_page.php?action=edit&page=".$firstpage['id']);
	}
}else{
	redirect_to("edit_page.php?action=newpage");
}
?>

<?php
	$query="SELECT * FROM `pages` ORDER BY `position` ASC";
	$listpagesquery=mysqli_query( $connection, $query);
	confirm_query($listpagesquery);
?>

<?php
	$query="SELECT position FROM `pages` ORDER BY `position` ASC";
	$listidsquery=mysqli_query( $connection, $query);
	confirm_query($listidsquery);
?>

<?php	
if(isset($_GET['page'])){
	$query="SELECT * FROM `pages` WHERE id={$_GET['page']}";
	$getpagequery=mysqli_query( $connection, $query);
	confirm_query($getpagequery);
	$selpage=mysqli_fetch_array($getpagequery);

	function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	    $pgVisibility = array();
	}
	set_error_handler("exception_error_handler");

	if(!$pgVisibility = unserialize($selpage['visible'])){
		$pgVisibility = array();
	}
}
?>

<?php
$title="";
if($_GET['action']=="edit"){
	$title = "Editing Page: ".$selpage['name'];
}elseif($_GET['action']=="newpage"){
	$title = "Adding new Page";
} ?>
<?php
	$pgsettings = array(
		"title" => $title,
		"icon" => "icon-newspaper"
	);
	require_once("includes/begin_cpanel.php");
?>
<script type="text/javascript" src="../tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	tinymce.init({
		selector: "textarea",
		theme: "modern",
		skin: 'light',
		width: '100%',
		plugins: [
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media nonbreaking save table contextmenu directionality",
			"emoticons template paste textcolor responsivefilemanager"
		],
		toolbar1: "insertfile undo redo | fontsizeselect fontselect styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | responsivefilemanager",
		toolbar2: "print preview media | forecolor backcolor",
		contextmenu: "link image inserttable | tableproperties cell row column deletetable",
		image_advtab: true,
		templates: [
			{title: '2 Column Table', content: '<table style="width: 100%;" border="2" width="1798"><tbody><tr><td style="text-align: center;">Column 1</td><td style="text-align: center;">Column 2</td></tr></tbody></table>'},
		],
        theme_advanced_fonts : "Andale Mono=andale mono,times;"+
                "Arial=arial,helvetica,sans-serif;"+
                "Arial Black=arial black,avant garde;"+
                "Book Antiqua=book antiqua,palatino;"+
                "Comic Sans MS=comic sans ms,sans-serif;"+
                "Courier New=courier new,courier;"+
                "Georgia=georgia,palatino;"+
                "Helvetica=helvetica;"+
                "Impact=impact,chicago;"+
                "Symbol=symbol;"+
                "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                "Terminal=terminal,monaco;"+
                "Times New Roman=times new roman,times;"+
                "Trebuchet MS=trebuchet ms,geneva;"+
                "Verdana=verdana,geneva;",
		external_filemanager_path:"../filemanager/",
		filemanager_title:"Link to File" ,
		external_plugins: { "filemanager" : "plugins/responsivefilemanager/plugin.min.js"}
	});
	
	function disable(select){
		 var selectedOption = select.options[select.selectedIndex];
		 
		 if(selectedOption.value == "Link"){
			 document.getElementById('url').disabled = false;
			 document.getElementById('url').readOnly = false;
		 }else{
			document.getElementById('url').disabled = true; 
			document.getElementById('url').readOnly = true; 
		 }
		 
		 
	}
	$(document).ready(function() {
		//jQuery for seven sets of "Select All" checkboxes
		var $gallall = 'gallall';
        $('input[id="'+$gallall+'"]').change(function() {
			var $all_check_status = $('input[id="'+$gallall+'"]').is(':checked');
             $("#gall label").each(function(index, element) {
				var $target = $(this).attr("for");
				if($all_check_status!=$('input[id="'+$target+'"]').is(':checked')){
                	$(this).trigger('click');
				}
            });
        });
		
		var $formall = 'formall';
        $('input[id="'+$formall+'"]').change(function() {
			var $all_check_status = $('input[id="'+$formall+'"]').is(':checked');
             $("#form label").each(function(index, element) {
				var $target = $(this).attr("for");
				if($all_check_status!=$('input[id="'+$target+'"]').is(':checked')){
                	$(this).trigger('click');
				}
            });
        });

        $('select[id="visibilitySelector"]').change(function() {
        	if($(this).find("option:selected").attr('id') == 'selranks'){
        		$('#rankcontainer').css('visibility','visible');
        	}else{
        		$('#rankcontainer').css('visibility','hidden');
        	}
        });
	 });
</script>
<form action="edit_page.php?<?php if(isset($_GET['action'])&&$_GET['action']=="edit"){echo "action=edit&&page=".$selpage['id'];}elseif(isset($_GET['action'])&&$_GET['action']=="newpage"){echo "action=newpage";} ?>" method="post" name="editpage">

<table cellpadding="0" id="sticker">
  <tr>
  <td>
  	<?php if(check_permission("Pages","add_pages")){?>
        <input class="green btn" type= "submit" name="<?php if(isset($_GET['action'])&&$_GET['action']=="edit"){echo "submit";}elseif(isset($_GET['action'])&&$_GET['action']=="newpage"){echo "newpage";} ?>" value="Save" />
            <?php if(check_permission("Pages","delete_pages")&&$_GET['action']=="edit"){?>
        <?php if(isset($_GET['page'])){?><a class="modal-trigger red btn" href="#modal1">Delete</a><?php } ?>
    <?php } ?>
        <input class="grey btn" type= "submit" name="<?php if(isset($_GET['action'])&&$_GET['action']=="edit"){echo "sandb";}elseif(isset($_GET['action'])&&$_GET['action']=="newpage"){echo "newandb";} ?>" value="Save & Close" />
        <input class="grey btn" type= "submit" name="<?php if(isset($_GET['action'])&&$_GET['action']=="edit"){echo "sandnewp";}elseif(isset($_GET['action'])&&$_GET['action']=="newpage"){echo "newandnewp";} ?>" value="Save & New" />
	<?php } ?>
    <a class="grey btn" href="page_list.php">Close</a>
    <?php if(isset($_GET['action'])&&$_GET['action']!="newpage"){ ?>
            <td width="10%"><div class="input-field col s6"><a href="page_list_simple.php" onclick="window.open('page_list_simple.php', 'newwindow', 'width=700, height=500'); return false;">(List Page URLs)</a></div></td>
            <?php } ?>
    </td>
  </tr>
</table>

<table>
<tr>
<td width="50%">
<div class="input-field col s6">
        	<input id="title" type="text" name="name" value="<?php if(isset($_GET['page'])){echo $selpage['name'];} ?>" class="validate" />
            <label for="title">Title</label>
            </div>
            </td>
            <?php if(isset($_GET['action'])&&$_GET['action']!="newpage"){ ?>
            <td width="40%"><label for="title">Page URL</label><div class="input-field col s6"><a href="<?php echo $GLOBALS['HOST'].'/page/'.urlencode($selpage['name']); ?>" onclick="window.open('<?php echo $GLOBALS['HOST'];?>/page/<?php echo urlencode($selpage['name']);?>', 'newwindow', 'width=1017, height=500'); return false;"><?php echo $GLOBALS['HOST'].'/page/'.urlencode($selpage['name']); ?></a></div>
            </td>
            <?php } ?>
            <td align="left">
    	<input id="pub" type="checkbox" name="published" <?php if((isset($_GET['page'])&&$selpage['published']==1)||$_GET['action']=="newpage"){echo "checked ";} ?>/>
        <label for="pub">Published</label>
    </td>
</tr>
</table>
<div id="TabbedPanels1" class="TabbedPanels">
            <ul class="TabbedPanelsTabGroup">
                <li class="TabbedPanelsTab" tabindex="0">Content</li>
                <li class="TabbedPanelsTab" tabindex="0">Properties</li>
                <li class="TabbedPanelsTab" tabindex="0">Galleries</li>
                <li class="TabbedPanelsTab" tabindex="0">Forms</li>
            </ul>
            <div class="TabbedPanelsContentGroup">
            <div class="TabbedPanelsContent">
            
            <tr>
  	<td colspan="5">
    	<textarea name="content" id="content" class="text" style="width:860px; height:500px;" ><?php if(isset($_GET['page'])){echo $selpage['content'];} ?></textarea>
    </td>
  </tr>
            </div>
                <div class="TabbedPanelsContent">
                    <h1 style="margin:0;">Page Properties</h1><br>
                    <table width="100%" border="5" cellspacing="5" cellpadding="5" class="editpageform">
                    <tr><!--
                        <td align="left">
                        <div class="input-field col s6">
                                <input id="title" type="text" value="<?php if(isset($_GET['page'])){echo $selpage['name'];} ?>" class="validate" />
                                <label for="title">Title</label>
                                </div>
                    
                        </td>
                        -->
                      </tr>
                      <tr>
                        <td align="left">
                        <label>Page Order:</label>
                            <select name="pgorder"><?php $numpages=mysqli_num_rows($listpagesquery)+1; $count=1; while($numpages>=$count){ ?><option value="<?php echo $count; ?>"<?php if(isset($_GET['page'])&&intval($selpage['position'])==$count){echo " selected=\"selected\"";} ?>><?php echo $count;?></option><?php $count++; }  ?>
                            <?php if(isset($_GET['action'])&&$_GET['action']=="newpage"){?><option value="<?php echo ($count); ?>" selected="selected"><?php echo ($count); ?></option><?php }?></select>
                        </td>
                        
                        <td align="left">
                            <?php
                                $types = array('Custom', 'Blog', 'Link', 'Staff', 'Forum');
                            ?>
                            <label>Page Type:</label>
                            <select name="pgtype" onchange="disable(this)">
                            <?php
                            $typecount=0;
                            foreach($types as $type){
                            ?>
                                <option value="<?php echo $type; ?>"<?php if ((isset($selpage['type'])&&$type == $selpage['type'])||$_GET['action']=='newpage'&&$typecount==0){echo ' selected="selected"';} ?>><?php echo $type; ?></option>
                            <?php 
                            $typecount++;
                            } ?>
                            </select>
                        </td>
                        <td align="left">
                            <label>Documents Folder:</label>
                            <select name="pgdocfolder">
                            <option value="">(None)</option>
                            <?php
                            $dirs = array_filter(glob('../uploads/*'), 'is_dir');
                            foreach($dirs as $dir){
								$dir = substr($dir."/", 11);
                            ?>
                                <option value="<?php echo $dir; ?>"<?php if (isset($selpage['doc_folder'])&&$dir == $selpage['doc_folder']){echo ' selected="selected"';} ?>><?php echo $dir; ?></option>
                            <?php 
                            
                            } ?>
                            </select>
                        </td>
                      </tr>
                      <!--<tr>
                        <td align="right">Icon:</td>
                        <td>
                            <input type="text" name="icon" value="<?php if(isset($_GET['page'])){echo $selpage['icon'];} ?>" />
                        </td>
                      </tr>-->
                      <tr>
                        
                        <td align="left">
                            <?php
                            $query="SELECT * FROM `pages` WHERE `issubpage` = 0 ORDER BY `position` ASC";
                            $listpagesquery=mysqli_query( $connection, $query);
                            confirm_query($listpagesquery);
                            ?>
                            <label>Parent Page</label>
                            <select name="sub">
                                <option value="none"<?php if(isset($selpage['issubpage'])&&$selpage['issubpage']==0){echo " selected=\"selected\"";} ?>>(None)</option>
                                <?php
                                while($listpage=mysqli_fetch_array($listpagesquery)){
                                    if(isset($_GET['page'])&&$listpage['id']!=intval($_GET['page'])){?>
                                        <option value="<?php echo $listpage['id'];?>"<?php if(isset($selpage['parent'])&&$selpage['parent']==$listpage['id']){echo " selected=\"selected\"";} ?>><?php echo $listpage['name']?></option>
                                <?php }
                                }?>
                            </select>
                        </td>
                        <td align="left">
	                        <div class="input-field col s6">
	                        <label>External URL - Make sure to add "http://"</label>
	                            <input type="text" name="url" id="url" value="<?php if(isset($_GET['page'])){echo $selpage['url'];}else{echo "http://";} ?>" style="width:90%;" maxlength="1024" <?php if((isset($selpage['type'])&&$selpage['type']!="Link")||$_GET['action']=="newpage"){echo "readonly disabled ";} ?>/>
	                        </div>
                        </td>
                        <td>
                        <label>Visibility:</label>
                            <select name="visible[]" id="visibilitySelector">
                                <option value="any"<?php if(isset($_GET['page'])&&$pgVisibility[0] == 'any'){echo ' selected="selected"';} ?>>Anyone</option>
                                <option value="loggedin"<?php if(isset($_GET['page'])&&$pgVisibility[0] == 'loggedin'){echo ' selected="selected"';} ?>>Logged In</option>
                                <option value="loggedout"<?php if(isset($_GET['page'])&&$pgVisibility[0] == 'loggedout'){echo ' selected="selected"';} ?>>Logged Out</option>
                                <option value="" id="selranks" <?php if(isset($_GET['page'])&&$pgVisibility[0] != 'any'&&$pgVisibility[0] != 'loggedin'&&$pgVisibility[0] != 'loggedout'){echo ' selected="selected"';} ?>>Custom</option>
                            </select>
                            <div id="rankcontainer" style="background-color:#CCCCCC;<?php if(isset($_GET['page'])&&$pgVisibility[0] != 'any'&&$pgVisibility[0] != 'loggedin'&&$pgVisibility[0] != 'loggedout'){echo ' visibility:visible;';}else{echo ' visibility:hidden;';} ?>">
                            	<?php 
		                            $query="SELECT * FROM `ranks`";
		                            $listrankssquery=mysqli_query( $connection, $query);
		                            confirm_query($listrankssquery);

		                            while($listrank = mysqli_fetch_array($listrankssquery)){
                            	?>
                            	<input id="rank-<?php echo $listrank['id']; ?>" type="checkbox" name="visible[]" value="<?php echo $listrank['id']; ?>" <?php if(isset($_GET['page'])&&in_array($listrank['id'], $pgVisibility)){echo "checked ";} ?>/><label for="rank-<?php echo $listrank['id']; ?>"><?php echo $listrank['name']; ?></label><br/>
                            	<?php
                            	}
                            	?>
                            </div>
                        </td>
                      </tr>
                      <tr>
                        <td align="left">
                        <label>Open page in</label>
                            <select name="target">
                                <option value="_self"<?php if ((isset($selpage['target'])&&$selpage['target']=="_self")){echo ' selected="selected"';}?>>Self</option>
                                <option value="_blank"<?php if ((isset($selpage['target'])&&$selpage['target'] == "_blank")){echo ' selected="selected"';} ?>>New Tab</option>
                            </select>
                        </td>
                        <td align="right"><input id="banner" type="checkbox" name="banner" <?php if(isset($_GET['page'])&&$selpage['banner']==1){echo "checked ";} ?>/><label for="banner">Banner</label></td>
                        <td align="left">
                            <?php
                                $query="SELECT * FROM `slider_names`";
                                $listsliderssquery=mysqli_query( $connection, $query);
                                confirm_query($listsliderssquery);
                            ?>
                            <label>Slider</label>
                            <select name="slider">
                                <option value="0"<?php if(isset($selpage['slider'])&&$selpage['slider']==0){echo " selected=\"selected\"";} ?>>(None)</option>
                                <?php
                                while($listslider=mysqli_fetch_array($listsliderssquery)){?>
                                    <option value="<?php echo $listslider['id'];?>"<?php if(isset($selpage['slider'])&&$selpage['slider']==$listslider['id']){echo " selected=\"selected\"";} ?>><?php echo $listslider['name']?></option>
                                <?php
                                }?>
                            </select>
                         </td>
                      </tr>
                      <tr>
                        <td align="right"><b>Show page on:</b></td>
                        <td align="left">
                            <input id="horiz" type="checkbox" name="horiz_menu" <?php if((isset($_GET['page'])&&$selpage['horiz_menu']==1)||$_GET['action']=="newpage"){echo "checked ";} ?>/><label for="horiz">Horiz</label><br>
                            <input id="vert" type="checkbox" name="vert_menu" <?php if(isset($_GET['page'])&&$selpage['vert_menu']==1){echo "checked ";} ?>/><label for="vert">Vert</label>
                        </td>
                        <td align="right"><b>Menus visible on page:</b></td>
                        <td align="left">
                            <input type="checkbox" id="horiz_menu_visible" name="horiz_menu_visible" <?php if((isset($_GET['page'])&&$selpage['horiz_menu_visible']==1)||$_GET['action']=="newpage"){echo "checked ";} ?>/><label for="horiz_menu_visible">Horizontal Menu</label><br>
                            <input type="checkbox" id="vert_menu_visible" name="vert_menu_visible" <?php if((isset($_GET['page'])&&$selpage['vert_menu_visible']==1)||$_GET['action']=="newpage"){echo "checked ";} ?>/><label for="vert_menu_visible">Vertical Menu</label>
                        </td>
                      </tr>
                    </table>
                </div>
                <div class="TabbedPanelsContent">
                <h1 style="margin:0;">Included Galleries</h1><br>
					<?php
                        $query="SELECT * FROM `galleries` ORDER BY `id` ASC";
                        $galleryquery=mysqli_query( $connection, $query);
                        confirm_query($galleryquery);?>
                        <div id="gall">
                          <ul>
                            	<li><input type="checkbox" id="gallall"><label for="gallall"><strong>Select all</strong></label></li>
                        <?php
                        while($gallery=mysqli_fetch_array($galleryquery)){
                            $checked = false;
                            if(isset($selpage['galleries'])&&$selpage['galleries']!=""){
                                $pagegalleries=unserialize($selpage['galleries']);
                                foreach($pagegalleries as $pagegalleryid){
                                    if($pagegalleryid == $gallery['id']){
                                        $checked = true;
                                    }
                                }
                            }
                            
                            ?>
                            <li><input type="checkbox" name="galleries[]" id="<?php echo $gallery['id']; ?>" value="<?php echo $gallery['id']; ?>" <?php if($checked == true){echo "checked";} ?> /><label for="<?php echo $gallery['id']; ?>"><?php echo $gallery['name']; ?></label></li> <?php } ?>
                    	</ul>
                    </div>
                </div>
                <div class="TabbedPanelsContent">
                <h1 style="margin:0;">Included Forms</h1><br>
					<?php
                        $query="SELECT * FROM `forms` ORDER BY `id` ASC";
                        $formquery=mysqli_query( $connection, $query);
                        confirm_query($formquery);
                        ?><div id="form">
                        	<ul>
                            	<li><input type="checkbox" id="formall"><label for="formall"><strong>Select all</strong></label></li>
                        <?php
                        while($form=mysqli_fetch_array($formquery)){
                            $checked = false;
                            if(isset($selpage['forms'])&&$selpage['forms']!=""){
                                $pageforms=unserialize($selpage['forms']);
                                foreach($pageforms as $pageformid){
                                    if($pageformid == $form['id']){
                                        $checked = true;
                                    }
                                }
                            }
                            
                            ?>
                            <li><input type="checkbox" name="forms[]" id="form<?php echo $form['id']; ?>" value="<?php echo $form['id']; ?>" <?php if($checked == true){echo "checked";} ?> /><label for="form<?php echo $form['id']; ?>"><?php echo $form['name']; ?></label></li>
                            <?php } 
                    ?></ul></div>
</div>
            </div>
        </div>
</form>
<div id="modal1" class="modal">
    <div class="modal-content">
		<h4>Are you sure you want to delete?</h4>
		<p>Once you delete this page there will be no way to recover it!</p>
    </div>
    <div class="modal-footer">
	    <div class="row right">
		    <div class="col l12 s12">
			    <a href="#!" class="modal-close waves-effect waves-blue btn blue ">Cancel</a>
			    <a href="edit_page.php?action=delpage&page=<?php echo $_GET['page']; ?>" id="del_button" class="modal-close waves-effect waves-red btn red ">Delete</a>
		    </div>
	    </div>
    </div>
</div>
<script type="text/javascript">
	var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");

	function changeTab(id){
		TabbedPanels1.showPanel(id);
		return false;
	};
	<?php if(isset($_GET['tab'])){?>changeTab(<?php echo $_GET['tab'];?>);<?php } ?>
</script>
<?php
	require_once("includes/end_cpanel.php");
?>