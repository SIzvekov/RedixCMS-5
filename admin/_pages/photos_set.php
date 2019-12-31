<?
//print_r($_GET);
$tid = intval($_GET['id']); if(!$tid) die("<font color='#f00'>".$this->core_echomui('photogalery_addph_noiderror')."</font>");
$params = unserialize(rawurldecode($_GET['params']));
//print_r($params);
$com_id = intval($params['com_id']); if(!$com_id) die('error: no component id');
$com_config = $this->adm_get_com_config($com_id);
$com_config = $com_config['config'];
//print_r($com_config);

if($_POST['save'] || $_POST['app'])
{

	$fields = $this->get_edit_fields();
	$fields_names = array_keys($fields);
	$post = $_POST;
	$files = $_FILES;
	$colstr = intval($_POST['colstr']);
	
	for($i=0;$i<$colstr;$i++)
	{
		$_POST = array();
		$_FILES = array();
		$_POST['allshowedfields'] = join(";",$fields_names);
		foreach($fields_names as $fname)
		{
			$_POST[$fname] = $post[$fname][$i];

			if(isset($files['file_'.$fname]))
			{
				$_FILES['file_'.$fname]['name'] = $files['file_'.$fname]['name'][$i];
				$_FILES['file_'.$fname]['type'] = $files['file_'.$fname]['type'][$i];
				$_FILES['file_'.$fname]['tmp_name'] = $files['file_'.$fname]['tmp_name'][$i];
				$_FILES['file_'.$fname]['error'] = $files['file_'.$fname]['error'][$i];
				$_FILES['file_'.$fname]['size'] = $files['file_'.$fname]['size'][$i];

				if(in_array($fname."[".$i."]",$post['delfile']))
				{
					if(!is_array($_POST['delfile'])) $_POST['delfile'] = array();
					$_POST['delfile'][] = $fname;
				}
			}
		}
		

		$this->adm_go_upload_file(); // загружаем файлы
		$this->id = $post['ph_id'][$i];
		$fields = $this->get_edit_fields();
		$sql = $this->adm_get_edit_sql("#".$com_config['tbl'],$fields);
		$res = $this->query($sql);
	}

	$this->adm_add_sys_mes($this->core_echomui('admc_record_edited'),"ok");
	$this->reload("");
}
$this->adm_show_sys_mes(); // <- выводим системные сообщения


	echo '<div class="bgpad" id="bgpad" onclick="setvis(\'none\');" style="display:none"></div>';
	echo '<div id="dialog1" class="dialog">';

	$ext = split(",",$params['ext']);
	$extensions = array();
	foreach($ext as $e) {$extensions[] = "*.".trim($e);}
	$extensions = join(",",$extensions);

	$filepath = trim($params['fpath1'],'/');
	if(!is_dir(DOCUMENT_ROOT.'/'.$filepath)) die("<font color='#ff0000'>".$this->core_echomui('adm_showfile_upnodir').": <b>".$filepath."</b></font><br>");

	$save2name = $params['save2name'];

	$filepath_url = trim(trim($filepath),'/');
	$filepath = DOCUMENT_ROOT."/".$filepath_url."/";
	
	$maxfilesize = intval($this->core_ini_get("upload_max_filesize"));
	require_once DOCUMENT_ROOT.'/'.$this->adm_path.'/_pages/filemanager/_system/class.FlashUploader.php';
	IFU_display_js();
	$uploader = new FlashUploader('uploader', 'http://'.HTTP_HOST.'/'.$this->adm_path.'/_pages/filemanager/_system/uploader','http://'.HTTP_HOST.'/'.$this->adm_path.'/_pages/photos_set/upload.php');
	$uploader->set('valid_extensions', $extensions);//*.jpg,*.gif,*.png,*.jpeg,*.bmp,*.pdf,*.doc,*.docx,*.xls,*.xlsx,*.txt,*.avi,*.mp3,*.mp4,*.waw
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
	$uploader->pass_var('save2name', $save2name);
	$uploader->pass_var('tid', $tid);
	$uploader->pass_var('tbl', $com_config['tbl']);
	$uploader->set('callback', 'reloadlist');
	$uploader->set('max_file_size', $maxfilesize);
	echo $this->core_echomui('photogalery_addph_tooltip')."<br/><br/>";
	$uploader->display();
	echo "<br/><small><font color='#cfcfcf'>".$this->core_echomui('max_file_size')." <b>".$maxfilesize." Mb</b></font></small>";
	echo "<br/><small><font color='#cfcfcf'>".$this->core_echomui('photogalery_addph_fileexts').": <b>".$extensions."</b></font></small>";
	echo "<br/><div align='center'><input type='button' value='".$this->core_echomui('photogalery_addph_done')."' onclick=\"setvis('none');\"></div>";
	echo '</div>';

?>
<a href="" style="text-align:center;font-size:150%;font-weight:bold;" onclick="setvis('block',1);return false;"><?=$this->core_echomui('photogalery_addph_button')?></a>
<div id="photos_set"></div>
<?
$this->jsonload[] = 'loadfotosonload()';
$this->jsonload[] = 'reloadlist()';
?>
<script>

function loadfotosonload() {parent.document.getElementById('<?=$_GET['hinp']?>').value=document.body.scrollHeight;setTimeout("loadfotosonload()",100);}
function reloadlist(act, fname)
{
	var dopcond ='';
	if(act && fname) dopcond = '&'+act+'='+fname;

	loadXMLDoc('/ajax-index.php?page=photos_set&isadm=1&tbl=<?=$com_config['tbl']?>&tid=<?=$tid?>&hinp=<?=$_GET['hinp']?>&tofolder=<?=$filepath_url?>&com_id=<?=$com_id?>'+dopcond,'photos_set');
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
		topside=0;
		document.getElementById('dialog'+id).style.top = parseInt(topside)+'px';
	}
}
</script>
<style>
div.dialog{position:absolute;left:50%;top:0px;width:300px;margin-left:-161px;padding:10px;background:#fff;border:1px solid #000;display:none;z-index:1000;}	
div.ph_set_imgedit{}
table.ph_set_imgitem{padding:0px;margin:0px;border-collapse:collapse;width:100%;margin-top:10px;}
table.ph_set_imgitem tr{background:none;}
table.ph_set_imgitem tr:hover{background:#fff;}
table.ph_set_imgitem td{padding-bottom:5px;border-bottom:1px dotted #ccc;padding-top:10px;vertical-align:top;}
table.ph_set_imgitem td.imgtd{width:160px;}
div.ph_set_img{float:left;width:150px;overflow:hidden;margin:0 10px 0 0;}
div.ph_set_imgname{font-size:80%;text-align:center;}
div.fieldlabel{float:left;margin-right:5px;width:150px;text-align:right;overflow:hidden;font-weight:bold;}
</style>