<?
$this->core_readmuifile(DOCUMENT_ROOT."/".$this->adm_path."/_pages/filemanager/lang/".$this->muiparam().".txt");

$this->base_file_path = "images";
$_SERVER['QUERY_STRING'] = $_GET['path'];
$titpath = $this->fmgr_baseurl($_SERVER['QUERY_STRING']);
$filepath = DOCUMENT_ROOT."/".$this->base_file_path."/".$titpath;
$this->fmgr_readusernames($filepath."/.usernames_dirs",'dir');
$this->fmgr_readusernames($filepath."/.usernames_files",'file');
$dirwithicos = "/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/file_extensions/";
$images_exts = array("jpg","gif","png","jpeg","bmp");

if($_GET['act']=='prop')
{
	$selfiles = split("\n",trim($_POST['selectedfiles']));
	$strings = array();
	if(sizeof($selfiles)>1)
	{
		echo "total";
	}else
	{
		$string = array();
		$wholefilepath = $filepath."/".$selfiles[0];
		$ext = strtolower(end(split("\.",$selfiles[0])));

		$stat = stat($wholefilepath);

		if(!in_array($ext, $images_exts)) 
		{
			if(file_exists(DOCUMENT_ROOT.$dirwithicos.$ext.".png")) $icofile = $dirwithicos.$ext.".png";
			else $icofile = $dirwithicos."default.png";
			$ico = "<img src='".$icofile."' class='ico'/>";
		}
		else echo "<div class='prop_preview'><img src='http://".HTTP_HOST."/".$this->base_file_path."/".trim($titpath,'/')."/".$selfiles[0]."' alt='' title=''></div>";


		if(is_dir($wholefilepath))
		{
			$string['label'] = $this->core_echomui('prop_lb_type_dir');
			$string['val'] = $this->core_echomui('prop_val_type_dir');

			$username = $this->fmgr_showdirname($selfiles[0]);

			$size = dir_size($wholefilepath);
		}
		else
		{
			$string['label'] = $this->core_echomui('prop_lb_type_dir');
			$string['val'] = $ico.$this->core_echomui('prop_lb_type_file')." *.".$ext."";

			$username = $this->fmgr_showfilename($selfiles[0]);

			$size = $stat['size'];
		}
		$strings[] = $string;
////////////////////////////////////////////////////////

		$string['label'] = $this->core_echomui('prop_lb_type_name');
		///// TYPE
		$string['val'] = '';
		$string['val'] .= $this->core_echomui('prop_val_type_file_user').":<br/> <input type='text' class='input_normal' onblur=\"this.className='input_normal'\" onfocus=\"this.className='input_focus'\" value='".$username."' name='filename_user' style='width:305px;'><br/>";
		$string['val'] .= $this->core_echomui('prop_val_type_file_ondisk').":<br/> <input type='text' class='input_normal' onblur=\"this.className='input_normal'\" onfocus=\"this.className='input_focus'\" value='".$selfiles[0]."' name='filename_disk' style='width:305px;'><br/>";
		if(!is_dir($wholefilepath))
			$string['val'] .= $this->core_echomui('prop_val_type_file_link').":<br/><div class='filelink'>http://".HTTP_HOST."<b>/".$this->base_file_path."/".$titpath."/".$selfiles[0]."</b></div><br/>";
		
		$strings[] = $string;
////////////////////////////////////////////////////////
		
		$string['label'] = $this->core_echomui('prop_lb_size');
		$size = showsize($size);
		$string['val'] = $size[0]." ".$this->core_echomui('file_size_'.$size[1]);
		$strings[] = $string;
////////////////////////////////////////////////////////
		
		
		
		/*
		$string['label'] = $this->core_echomui('prop_lb_');
		$string['val'] = '';
		$strings[] = $string;
		*/

	}
	echo '<form method="post" id="fileprop" enctype="multipart/form-data">';
	foreach($strings as $str)
	{
		echo "<div class='prop_string'><div class='prop_label'>".$str['label'].":</div><div class='prop_val'>".$str['val']."</div></div><div style='clear:both;height:10px'></div>";
	}
	echo '<textarea name="selectedfiles" style="display:none">'.trim($_POST['selectedfiles']).'</textarea></form>';
	echo '<input type="submit" value="'.$this->core_echomui('prop_save').'" onclick="return saveprops();" class="btn1"/>&nbsp;&nbsp;<a href="" onclick="setvis(\'none\');return false;">'.$this->core_echomui('cancel').'</a>';
	//
	exit;
}

if($_GET['act']=='del')
{
	$selfiles = split("\n",trim($_POST['selectedfiles']));
	foreach($selfiles as $item)
	{
		$item = trim($item);
		
		if(is_dir($filepath."/".$item)) unset($this->fmgr_dirnames[$item]);
		else unset($this->fmgr_filenames[$item]);

		$this->adm_delete_file($filepath."/".$item, 1);
	}
	
	$f = fopen($filepath."/.usernames_dirs",'w');
		foreach($this->fmgr_dirnames as $k=>$v) fwrite($f,$k."=".$v."\n");
	fclose($f);
	$f = fopen($filepath."/.usernames_files",'w');
		foreach($this->fmgr_filenames as $k=>$v) fwrite($f,$k."=".$v."\n");
	fclose($f);
}//if($_GET['act']=='del')
if($_GET['act']=='saveprop')
{
	$alert = array();
	$selfiles = split("\n",trim($_POST['selectedfiles']));
	if(sizeof($selfiles)==1)
	{
		$item = $selfiles[0];
		$_POST['filename_disk'] = $this->adm_translit($_POST['filename_disk']);

		if(file_exists($filepath."/".$_POST['filename_disk']) && $_POST['filename_disk']!=$item)
		{
			$error = 1;
			$alert[] = $this->core_echomui('file_namedisk_exists');
		}

		if(is_dir($filepath."/".$item) && in_array($_POST['filename_user'],$this->fmgr_dirnames) && $_POST['filename_user']!=$this->fmgr_dirnames[$item])
		{
			$error = 1;
			$alert[] = $this->core_echomui('file_namefmdir_exists');
		}elseif(in_array($_POST['filename_user'],$this->fmgr_filenames) && $_POST['filename_user']!=$this->fmgr_filenames[$item])
		{
			$error = 1;
			$alert[] = $this->core_echomui('file_namefm_exists');
		}
		if(!$error)
		{
			rename($filepath."/".$item, $filepath."/".$_POST['filename_disk']);
			if(is_dir($filepath."/".$_POST['filename_disk']))
			{
				unset($this->fmgr_dirnames[$item]);
				$this->fmgr_dirnames[$_POST['filename_disk']] = $_POST['filename_user'];
				$f = fopen($filepath."/.usernames_dirs",'w');
					foreach($this->fmgr_dirnames as $k=>$v) fwrite($f,$k."=".$v."\n");
				fclose($f);
			}
			else 
			{
				unset($this->fmgr_filenames[$item]);
				$this->fmgr_filenames[$_POST['filename_disk']] = $_POST['filename_user'];
				$f = fopen($filepath."/.usernames_files",'w');
					foreach($this->fmgr_filenames as $k=>$v) fwrite($f,$k."=".$v."\n");
				fclose($f);
			}
		}
		if(sizeof($alert))
		{
			$_RESULT['alert'] = $this->core_echomui('error')."!\n".join("\n", $alert);
		}
	}
}

	$dir = scandir($filepath,0);
	$all_dirs = array();
	$all_files = array();
	array_shift($dir);array_shift($dir);
	foreach($dir as $item)
	{
		if(is_file($filepath."/".$item) && preg_match("/^\./i", $item)) continue;
		if(is_dir($filepath."/".$item)) $all_dirs[] = $item;
		else $all_files[] = $item;
	}

	$parent = split("/",trim($titpath,'/'));
	if(sizeof($parent) && $parent[0])
	{
		array_pop($parent);
		echo "<div class='filepadd'><div class='filepad' ><a href='?./".join('/',$parent)."'><img src='".$dirwithicos."../back.gif'><br/>&laquo;&nbsp;".$this->core_echomui('goback')."</a></div></div>";
	}
	foreach($all_dirs as $item)
	{
		$fileid++;
		if(sizeof($titpath) && $titpath[0]) $l = '/';
		else $l = '';

		echo "<div class='filepadd'><div class='filepad' onmouseover=\"setfileclass(this,'filepad_h');\" onmouseout=\"setfileclass(this,'filepad');\" id='file_".$fileid."' onclick=\"selectfile(this,'".$fileid."', event)\" ondblclick=\"openfile('dir','?./".trim($titpath,'/').$l.$item."')\" oncontextmenu=\"return contextmenu(this,'".$fileid."', event);\"><div class='file'><img src='".$dirwithicos."folder.png'><div class='filename'>".$this->fmgr_showfilename($this->fmgr_showdirname($item))."</div></div></div></div>";
		echo "<input type='hidden' id='file_".$fileid."_name' value='".$item."'>";
	}

	foreach($all_files as $item)
	{
		$fileid++;
		$ext = strtolower(end(split("\.",$item)));

		if(in_array($ext,$images_exts)) $ico = "<img src='http://".HTTP_HOST."/showimg.php?".$this->base_file_path."/".trim($titpath,'/')."/".$item."&w=100' class='prewiev'/>";
		else 
		{
			if(!file_exists(DOCUMENT_ROOT.$dirwithicos.$ext.".png") || !is_file(DOCUMENT_ROOT.$dirwithicos.$ext.".png")) $ext = 'default';
			$ico = "<img src='".$dirwithicos.$ext.".png'/>";
		}

		echo "<div class='filepadd'><div class='filepad' onmouseover=\"setfileclass(this,'filepad_h');\" onmouseout=\"setfileclass(this,'filepad');\" id='file_".$fileid."' onclick=\"selectfile(this,'".$fileid."', event)\" ondblclick=\"openfile('file','http://".HTTP_HOST."/".$this->base_file_path."/".trim($titpath,'/')."/".$item."')\" oncontextmenu=\"return contextmenu(this,'".$fileid."', event);\">".$ico."<div class='filename'>".$this->fmgr_showfilename($item)."</div></div></div>";
		echo "<input type='hidden' id='file_".$fileid."_name' value='".$item."'>";
	}
	if(!sizeof($all_dirs) && !sizeof($all_files)) echo "<div class='emptydir'>".$this->core_echomui('emptydir')."</div>";
	echo '<div class="clear"></div>';


function dir_size($dir) {
$totalsize=0;
if ($dirstream = @opendir($dir)) {
while (false !== ($filename = readdir($dirstream))) {
if ($filename!="." && $filename!="..")
{
if (is_file($dir."/".$filename))
$totalsize+=filesize($dir."/".$filename);

if (is_dir($dir."/".$filename))
$totalsize+=dir_size($dir."/".$filename);
}
}
}
closedir($dirstream);
return $totalsize;
}

function showsize($size=0)
{
	if($size<1024) return array($size,'b');

	$size = round($size/1024,2);
	if($size<1024) return array($size,'kb');

	$size = round($size/1024,2);
	if($size<1024) return array($size,'mb');

	$size = round($size/1024,2);
	if($size<1024) return array($size,'gb');

	$size = round($size/1024,2);
	if($size<1024) return array($size,'tb');


	
}

?>