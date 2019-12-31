<?
$this->base_file_path = "images";
$titpath = $this->fmgr_baseurl($_SERVER['QUERY_STRING']);
$filepath = DOCUMENT_ROOT."/".$this->base_file_path."/".$titpath;

$this->adm_showparttitle($this->core_echomui('filemanager_title').": ".$this->fmgr_makelink("./".$titpath)); 
$this->fmgr_readusernames($filepath."/.usernames_dirs",'dir');
$this->fmgr_readusernames($filepath."/.usernames_files",'file');

if(!is_dir($filepath) || !file_exists($filepath))
{
	$err = 1;
	$this->adm_add_sys_mes($this->core_echomui('thisisnotdir'),"err");
}
$this->adm_show_sys_mes();
if(!$err)
{
	if(sizeof($_FILES['upfile']))
	{
		$uploaded_col = 0;
		foreach($_FILES['upfile']['error'] as $ind=>$err)
		{
			if($err) continue;
			$filearr = split("\.",$_FILES['upfile']['name'][$ind]);
			$ext = end($filearr);
			array_pop($filearr);
			$_FILES['upfile']['name'][$ind] = join(".",$filearr);

			$destfile = $this->adm_translit($_FILES['upfile']['name'][$ind].".".$ext);
			$copy_num = 0;
			while(file_exists($filepath."/".$destfile) && is_file($filepath."/".$destfile)) 
			{
				$copy_num++;
				$destfile = $this->adm_translit($_FILES['upfile']['name'][$ind]." (".$copy_num.").".$ext);
			}

			$newbasename = $_FILES['upfile']['name'][$ind].".".$ext;
			$copy_num = 0;

			while(in_array($newbasename, $this->fmgr_filenames))
			{
				$copy_num++;
				$newbasename = $_FILES['upfile']['name'][$ind]." (".$copy_num.").".$ext;
			}

			copy($_FILES['upfile']['tmp_name'][$ind], $filepath."/".$destfile);
			$f = fopen($filepath."/.usernames_files",'a+');
				fwrite($f,$destfile."=".$newbasename."\n");
			fclose($f);
			$uploaded_col++;
			$this->fmgr_readusernames($filepath."/.usernames_files",'file');
		}
		$this->adm_add_sys_mes($this->core_echomui('uploadedfilescol').": <b>".$uploaded_col."</b>","ok");
		$this->reload();
	}

	if($_POST['createfolder'])
	{
		if(!$_POST['createfoldername'])
		{
			$this->adm_add_sys_mes($this->core_echomui('createdir_err'),"err");
		}else
		{
			$newdirname = $this->adm_translit($_POST['createfoldername']);
			$copy_num = 0;
			while(file_exists($filepath."/".$newdirname) && is_dir($filepath."/".$newdirname)) 
			{
				$copy_num++;
				$newdirname = $this->adm_translit($_POST['createfoldername'])."_".$copy_num;
			}
			$newdirbasename = $_POST['createfoldername'];
			$copy_num = 0;

			while(in_array($newdirbasename, $this->fmgr_dirnames))
			{
				$copy_num++;
				$newdirbasename = $_POST['createfoldername']." (".$copy_num.")";
			}
			$basenewdirname = $newdirname;
			$newdirname = $filepath."/".$newdirname;
			if(mkdir($newdirname,0777))
			{
				chmod($newdirname,0777);
				$this->adm_add_sys_mes($this->core_echomui('createdir_ok'),"ok");

				$f = fopen($newdirname."/.dirdescription",'w');
					fwrite($f,$_POST['folderdescription']);
				fclose($f);

				$f = fopen($newdirname."/.usernames_files",'w');fwrite($f,'');fclose($f);
				$f = fopen($newdirname."/.usernames_dirs",'w');fwrite($f,'');fclose($f);

				$f = fopen($filepath."/.usernames_dirs",'a+');
					fwrite($f,$basenewdirname."=".$newdirbasename."\n");
				fclose($f);

			}else
			{
				$this->adm_add_sys_mes($this->core_echomui('createdir_err'),"err");
			}
		}
		$this->reload();
	}

	$dirdescription = nl2br(htmlspecialchars(file_get_contents($filepath."/.dirdescription")));
	if($dirdescription) echo "<div class='dirdescr'>".$dirdescription."</div>";

	echo "<div class='dircontpad' id='dircontent' onclick=\"selectfile();\">".$this->core_echomui('loadwait')."...</div>";
	
	$perms = (touch($filepath)||isset($_SERVER['WINDIR']));
	if(!$perms) echo '<div class="uploadfileerror">'.$this->core_echomui('cantuploadfile').'</div>';
	else
	{
		echo '<div class="uploadfile"><input type="button" onclick="setvis(\'block\',2);" name="uploadfiles" value="'.$this->core_echomui('uplfilesbtn').'" class="btn"/></div>';
/*		
		echo '<div id="dialog2" class="dialog"><form method="post" enctype="multipart/form-data">'.$this->core_echomui('uploadfile').':<br/>
		<input type="file" name="upfile[]" /><br/>
		<span id="upmorefiles"></span>
		<a href="" onclick="addmorefiles(5);return false;">+ '.$this->core_echomui('upmorefiles').'</a><br/><br/>
		<input type="submit" name="uploadfiles" value="'.$this->core_echomui('uplfilesbtn').'" class="btn1"/>&nbsp;&nbsp;<a href="" onclick="setvis(\'none\',2);return false;">'.$this->core_echomui('cancel').'</a>
		</form></div>';
*/

		
		echo '<div id="dialog2" class="dialog">';
		$maxfilesize = intval($this->core_ini_get("upload_max_filesize"));
		require_once DOCUMENT_ROOT.'/'.$this->adm_path.'/_pages/filemanager/_system/class.FlashUploader.php';
		IFU_display_js();
		$uploader = new FlashUploader('uploader', 'http://'.HTTP_HOST.'/'.$this->adm_path.'/_pages/filemanager/_system/uploader', 'http://'.HTTP_HOST.'/'.$this->adm_path.'/_pages/filemanager/_system/upload.php');
		$uploader->set('valid_extensions', '*.*');//*.jpg,*.gif,*.png,*.jpeg,*.bmp,*.pdf,*.doc,*.docx,*.xls,*.xlsx,*.txt,*.avi,*.mp3,*.mp4,*.waw
		$uploader->set('click_text', $this->core_echomui('flaupl_click_text'));
		$uploader->set('uploading_text', $this->core_echomui('flaupl_uploading_text'));
		$uploader->set('complete_text', $this->core_echomui('flaupl_complete_text'));
		$uploader->set('pending_text', $this->core_echomui('flaupl_pending_text'));
		$uploader->set('pend_text', $this->core_echomui('flaupl_pending_text'));
		$uploader->set('max_text', $this->core_echomui('flaupl_max_text'));
		$uploader->set('valid_text', $this->core_echomui('flaupl_valid_text'));
		$uploader->set('size_failure_text', $this->core_echomui('flaupl_size_failure_text'));
		$uploader->set('progress_text', $this->core_echomui('flaupl_progress_text'));
		$uploader->set('set_width', '300');
		$uploader->pass_var('tofolder', $filepath);
		$uploader->set('callback', 'reloadlist');
		$uploader->set('max_file_size', $maxfilesize);
		$uploader->display();
		echo "<br/><small><font color='#cfcfcf'>".$this->core_echomui('max_file_size')." <b>".$maxfilesize." Mb</b></font></small>";
		echo '</div>';


		echo '<div class="uploadfile"><input type="button" onclick="setvis(\'block\',1);document.getElementById(\'newdirname\').value=\'\';document.getElementById(\'folderdescription\').value=\'\';document.getElementById(\'newdirname\').focus();" name="uploadfiles" value="'.$this->core_echomui('createfolder').'" class="btn"/></div>
		
		<div id="dialog1" class="dialog"><form method="post" id="createfolderform" enctype="multipart/form-data"><input type="hidden" name="createfolder" value="1"/>'.$this->core_echomui('createfoldername').':<br/><input type="text" name="createfoldername" class="input_normal" maxlength="50" onblur="this.className=\'input_normal\'" onfocus="this.className=\'input_focus\'" id="newdirname" style="width:300px;"/><br/>
		'.$this->core_echomui('createfolderdescr').':<br/>
		<textarea name="folderdescription" id="folderdescription" style="width:100%;height:30px;" class="input_normal" onblur="this.className=\'input_normal\'" onfocus="this.className=\'input_focus\'"  ></textarea>
		<input type="submit" value="'.$this->core_echomui('createfolder').'" onclick="return gocreatefolder(document.getElementById(\'newdirname\').value);" class="btn1"/>&nbsp;&nbsp;<a href="" onclick="setvis(\'none\',1);return false;">'.$this->core_echomui('cancel').'</a>
		</form></div>';

		echo '<div class="uploadfile" id="delbutton" style="display:none"><input type="button" onclick="deletesel()" name="delfiles" value="'.$this->core_echomui('delselected').'" class="btn"/></div>';

	}
	echo '<div class="bgpad" id="bgpad" onclick="setvis(\'none\');" style="display:none"></div>';

}
?>
<style type="text/css">
body{-moz-user-select: none}
div.dircontpad{background:#fff;margin:10px;border-top:1px solid #f3f3f3;border-left:1px solid #f3f3f3;border-right:1px solid #ccc;border-bottom:1px solid #ccc;}
div.filepadd{float:left;width:124px;height:144px;padding:0px;margin:0px;}
div.filepad{overflow:hidden;margin:2px 12px 12px 2px;border:0px;height:120px;width:100px;text-align:center;padding:5px;}
div.filepad_h{overflow:hidden;margin:1px 11px 11px 1px;border:1px solid #e9e9e9;height:120px;width:100px;text-align:center;padding:5px;background:#f6f6f6;}
div.filepad_sel{margin:0px 10px 10px 0px;border:2px solid #335ea8;min-height:120px;z-index:1;position:absolute;width:100px;text-align:center;padding:5px;background:#edf4ff;}
div.filename{width:100px;overflow:hidden;/*white-space:nowrap;*/cursor:default;}
div.filepad img, div.filepad_h img, div.filepad_sel img{border:0px;margin:0px;padding:0px;margin-bottom:5px;max-height:100px;max-width:100px;}
div.emptydir{color:#0f0f0f;margin:20px;float:left;}
div.uploadfileerror{color:#a00;margin-left:10px;}
div.uploadfile{color:#000;margin-left:10px;border:1px solid #ccc;background:#fafafa;padding:5px;float:left;}
input.btn{margin:15px 5px;border:1px solid #ddd;background:#ececec;color:#000;padding:10px;cursor:pointer;}
input.btn1{margin:5px 0px;border:1px solid #ddd;background:#ececec;color:#000;padding:3px 10px;cursor:pointer;}
div.bgpad{position: fixed; z-index: 100;top: 0; left: 0; height: 100%; width: 100%; background-color: #000022;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=25);-moz-opacity: 0.25;-khtml-opacity: 0.25;opacity: 0.7;display: none;}
div.dialog{position:absolute;left:50%;top:0px;width:300px;margin-left:-161px;padding:10px;background:#fff;border:1px solid #000;display:none;z-index:1000;}
div.dirdescr{padding:0px 10px;}
div.contextmenu{position:absolute;z-index:2;left:0px;top:0px;border:1px solid #000;background:#fff;padding:0px;}
div.contextmenu div.item {padding:5px 20px;background:#fff;color:#000;cursor:pointer;}
div.contextmenu div.item_hover {padding:5px 20px;background:#ccc;color:#fff;cursor:pointer;}
div.prop_string{width:400px;padding:0px;margin:0px;}
div.prop_preview img{width:400px;border:1px solid #000;margin:-1px -1px 9px -1px;}
div.prop_string div.prop_label{font-size:12px;float:left;width:65px;margin-right:5px;text-align:right;font-weight:bold;}
div.prop_string div.prop_val{font-size:12px;float:left;width:330px;}
div.prop_string div.prop_val img.ico{float:left;margin-right:2px;width:20px;}
div.prop_string div.prop_val div.filelink{overflow:auto  ;width: 330px;height:35px;white-space:nowrap;}
</style>
<!--[if IE]>
<style>
img.prewiev{border:0px;margin:0px;padding:0px;margin-bottom:5px;width:100px;}
</style>
<![endif]--> 
<script type="text/javascript">

	

function addmorefiles(colfields)
{
	if(!colfields) colfields = 1;
	obj = document.getElementById('upmorefiles');
	if(!obj) return false;
	for(i=1;i<=colfields;i++)
	{
		var input = document.createElement("input");
		input.type = 'file';
		input.name = 'upfile[]';
		obj.appendChild(input);
		var input = document.createElement("br");
		obj.appendChild(input);
	}
}
function setvis(act,id)
{
	document.getElementById('bgpad').style.display=act;
	if(document.getElementById('dialog'+id) && id) document.getElementById('dialog'+id).style.display=act;

	if(act=='none')
	{
		i = 1;
		while(document.getElementById('dialog'+i))
		{
			document.getElementById('dialog'+i).style.display=act;
			i++;
		}
	}
	if(act=='block')
	{
		topside = parseInt(getClientHeight()/2-100);
		if(topside<100) topside=100;
		document.getElementById('dialog'+id).style.top = parseInt(topside)+'px';
	}
}
function getClientHeight(){return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;}
function getBodyScrollTop(){return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);}
function openfile(type, url)
{
	if(type=='dir') target='_self';
	else target='_blank';
	window.open(url,target);
}
var lastindclicked=1;
function selectfile(fileobj, fileid, event)
{
	destroycontextmenu();
	fileid = parseInt(fileid);
	noneedsel = 0;

	find = 1;
	if(!keystat(event,'ctrl'))
	{
		while(document.getElementById('file_'+find))
		{
			document.getElementById('file_'+find).className='filepad';
			find++;
		}
	}
	else if(fileobj.className=='filepad_sel') {fileobj.className='filepad';noneedsel=1;}
	
	if(keystat(event,'shift'))
	{
		start = 0;
		end = 0;
		if(fileid>parseInt(lastindclicked)) {start = lastindclicked;end = fileid;}
		else {start = fileid;end = lastindclicked;}
		
		for(i=start;i<=end;i++)
		{
			document.getElementById('file_'+i).className='filepad_sel';
		}
	}else lastindclicked = fileid;

	if(fileobj && !noneedsel) fileobj.className='filepad_sel';
	
}
function setfileclass(fileobj, setclass)
{
	if(!fileobj) return false;
	if(fileobj.className!='filepad_sel') fileobj.className=setclass;
}

function keystat(e,what){
	switch(what)
	{
		case 'ctrl':
			ret = e.ctrlKey;
		break;
		case 'shift':
			ret = e.shiftKey;
		break;
		case 'alt':
			ret = e.altKey;
		break;
		case 'meta':
			ret = e.metaKey;
		break;
		case 'ctrla':
			ret = (e.ctrlKey);
		break;
		default:
			ret = false;
	}
	return ret;
}
function selectall()
{
	find = 1;
	while(document.getElementById('file_'+find))
	{
		document.getElementById('file_'+find).className='filepad_sel';
		find++;
	}
}
function blockkeypress()
{
	if(document.getElementById('bgpad').style.display=='none') ret = true;
	else ret = false;
	return ret;
}

function gocreatefolder(newdirname)
{
	if(!newdirname) {alert('<?=$this->core_echomui('needdirname');?>');return false;}
	else{return true;}
}

//document.onmousedown=function(){if(blockkeypress()) return false;}
document.onkeypress=function(){if(blockkeypress()) return false;}
document.onkeydown=function(){if(blockkeypress()) return false;}

function callkeydownhandler(evnt) {
   ev = (evnt) ? evnt : event;

	if(ev.ctrlKey && ev.keyCode=='65' && blockkeypress()) selectall();
	if(ev.keyCode=='116' || (ev.keyCode=='82' && ev.ctrlKey)) location.reload();
	if(ev.keyCode=='46' && blockkeypress()) {<?if($perms) {?>deletesel();<?}else{?><?}?>}

   return false;
}
if (window.document.addEventListener) 
{
	if(blockkeypress()) window.document.addEventListener("keydown", callkeydownhandler, false);
	window.document.addEventListener("mousemove", updateCoords, false);
}
else 
{
	if(blockkeypress()) window.document.attachEvent("onkeydown", callkeydownhandler, false);
	window.document.attachEvent("mousemove", updateCoords, false);
}
<?
$this->jsonload[] = 'reloadlist()';
$this->jsonload[] = 'setInterval(catchselectedfiles, 100)';
?>
window.onSelectStart = function(){return false;}

function reloadlist()
{
	loadXMLDoc('/ajax-index.php?isadm=1&page=filemanager&path=<?=$titpath?>','dircontent');
}
function catchselectedfiles()
{
	elements = document.getElementsByClassName('filepad_sel');
	var selectedfiles = '';
	for(i=0;i<elements.length;i++)
	{
		fname = document.getElementById(elements[i].id+'_name').value;
		selectedfiles += fname+'\n';
	}
	document.getElementById('selectedfiles').value = selectedfiles;
	if(selectedfiles) document.getElementById('delbutton').style.display='block';
	else document.getElementById('delbutton').style.display='none';
}
function deletesel()
{
	if(!confirm('<?=$this->core_echomui('deletequestion')?>')) return false;
	loadXMLDoc('/ajax-index.php?isadm=1&page=filemanager&path=<?=$titpath?>&act=del','dircontent','selected');
}


var mouseX = 0;
var mouseY = 0;

function contextmenu(fileobj, fileid, event)
{
	if(fileobj.className!='filepad_sel') selectfile(fileobj, fileid, event);
	else {destroycontextmenu();}

	var menuitems_text = new Array;
	var menuitems_act = new Array;
	var j=0;
	
	<?if($perms) {?>
	menuitems_text[j] = '<?=$this->core_echomui('contextmenu_delete')?>';
	menuitems_act[j] = 'deletesel()';
	j++;
	<?}?>
	menuitems_text[j] = '<?=$this->core_echomui('contextmenu_properties')?>';
	menuitems_act[j] = "showprop()";
	j++;

	var menu = document.createElement("div");
	menu.className = 'contextmenu';
	menu.id = 'contextmenu';
	menu.style.top = mouseY+'px';
	menu.style.left = mouseX+'px';
	
	for(i=0;i<menuitems_text.length;i++)
	{
		menu.innerHTML += '<div class="item" onclick="'+menuitems_act[i]+';destroycontextmenu();" onmouseover="this.className=\'item_hover\'" onmouseout="this.className=\'item\'" oncontextmenu=\"return false;\">'+menuitems_text[i]+'</div>';
	}
	document.body.appendChild(menu);

	return false;
}

function showprop()
{
	<?if($perms) $id=3;else $id=1;?>
	if(document.getElementById('dialog<?=$id?>'))
	{
		document.body.removeChild(document.getElementById('dialog<?=$id?>'));
	}
	
	var prop = document.createElement("div");
	prop.id = 'dialog<?=$id?>';
	prop.className = 'dialog';
	prop.style.width = '400px';
	prop.style.marginTop = '-100px';
	prop.style.marginLeft = '-200px';
	prop.innerHTML = 'loading...';
	document.body.appendChild(prop);
	setvis('block',<?=$id?>);

	loadXMLDoc('/ajax-index.php?isadm=1&page=filemanager&path=<?=$titpath?>&act=prop','dialog<?=$id?>','selected');
}

function updateCoords(evnt)
{
	e = (evnt) ? evnt : window.event;
	if (e.pageX || e.pageY)
	{
		mouseX = e.pageX;
		mouseY = e.pageY;
	}
	else if (e.clientX || e.clientY)
	{
		mouseX = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
		mouseY = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
	}
}
function destroycontextmenu()
{
	if(document.getElementById('contextmenu'))
	{
		document.body.removeChild(document.getElementById('contextmenu'));
	}
}

function saveprops()
{
	loadXMLDoc('/ajax-index.php?isadm=1&page=filemanager&path=<?=$titpath?>&act=saveprop','dircontent','fileprop');
	setvis('none');
}
</script>
<form id="selected" name="selected" method="post" enctype="multipart/form-data"><textarea id="selectedfiles" name="selectedfiles" style="display:none"></textarea></form>