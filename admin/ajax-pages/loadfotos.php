<?
$_GET['param'] = str_replace("'",'"',$_GET['param']);
$params = unserialize($_GET['param']);
$folder = $_GET['dir'];

$fotocom_conf = $this->adm_get_com_config($params['com_id']);

$this->core_readmuifile(DOCUMENT_ROOT.'/'.$this->adm_path.'/_pages/'.$fotocom_conf['adm_title'].'/lang/'.$this->muiparam().'.txt');

$sql = "SELECT `id` FROM `#h_components_listedittable` WHERE `com_id`=".intval($params['com_id'])." && `public`='1' && `pid`=0 ORDER BY `sort` ASC";
$bmid = $this->fetch_assoc($this->query($sql));
$bmid = intval($bmid['id']);

$sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($params['com_id'])." && `public`='1' && `pid`=".$bmid." ORDER BY `sort` ASC";
$fields_res = $this->query($sql);
$foto_fields = array();
while($fields = $this->fetch_assoc($fields_res))
{
	$fields['params'] = $this->adm_get_param($fields['params']);
	$foto_fields[] = $fields;
}
//echo '<pre>';
//print_r($foto_fields);
if(sizeof($_POST['foto-filename']))
{
	foreach($_POST['delfile'] as $file2del)
	{
		$file2del = intval(end(split("\[",$file2del)));
		$file = DOCUMENT_ROOT.'/images/swf_proektov/'.$_POST['swfprv'][$file2del];
		if(is_file($file))
		{
			unlink($file);
			$_POST['swfprv'][$file2del] = '';
		}
	}
	foreach($_POST['foto-filename'] as $k=>$filename)
	{
		$upfields = array();
		foreach($foto_fields as $field)
		{
			if($_FILES['file_swfprv']['type'][$k])
			{
				$t = split('\/',$_FILES['file_swfprv']['type'][$k]);
				if($t[0]=='image')
				{
					$file = DOCUMENT_ROOT.'/images/swf_proektov/'.$_POST['swfprv'][$k];
					if(is_file($file))
					{
						unlink($file);
						$_POST['swfprv'][$file2del] = '';
					}

					$newfname = substr(md5(time()),0,10);
					$ext = strtolower(end(split("\.",$_FILES['file_swfprv']['name'][$k])));
					copy($_FILES['file_swfprv']['tmp_name'][$k], DOCUMENT_ROOT.'/images/swf_proektov/'.$newfname.".".$ext);
					$_POST['swfprv'][$k] = $newfname.".".$ext;
				}
			}

			switch($field['db_fieldtype'])
			{
				case 'int':
					$val = intval($_POST[$field['db_fname']][$k]);
				break;
				case 'txtint':
					$val = "'".intval($_POST[$field['db_fname']][$k])."'";
				break;
				default:
					$val = "'".addslashes($_POST[$field['db_fname']][$k])."'";
			}

			$upfields[] = "`".addslashes($field['db_fname'])."`=".$val;
		}
		$upfields = join(",",$upfields);

		$sql = "UPDATE `#".addslashes($fotocom_conf['config']['tbl'])."` SET
		".$upfields."
		WHERE `".addslashes($params['dir_field_there'])."`='".addslashes($folder)."' && `".addslashes($params['img_field_there'])."`='".addslashes($filename)."'";
		$this->query($sql);
	}
	$this->adm_add_sys_mes($this->core_echomui('admc_record_edited'),"ok");
}
$this->adm_show_sys_mes();

$sql = "SELECT * FROM `#".addslashes($fotocom_conf['config']['tbl'])."` WHERE `".addslashes($params['dir_field_there'])."`='".addslashes($folder)."' ORDER BY `sort` ASC";
$fotos_res = $this->query($sql);
$fotos_info = array();

$acc_ext = array('jpg','jpeg','gif','png','bmp','swf');
											
$filepath = DOCUMENT_ROOT."".$folder;
$dir = scandir($filepath,0);
array_shift($dir);array_shift($dir);
foreach($dir as $item)
{
	if(is_file($filepath."/".$item) && preg_match("/^\./i", $item)) continue;
	if(!is_dir($filepath."/".$item)) 
	{
		$item_ar = split("\.",$item);
		$ext = strtolower(end($item_ar));
		if(in_array($ext, $acc_ext)) $all_files[] = $item;
	}
}
$all_files1 = $all_files;
while($r = $this->fetch_assoc($fotos_res))
{
	if(in_array($r[$params['img_field_there']], $all_files))
	{
		$fotos_info[$r[$params['img_field_there']]] = $r;
		$key = array_search($r[$params['img_field_there']], $all_files);
		unset($all_files1[$key]);
	}else
	{
		$sql = "DELETE FROM `#".addslashes($fotocom_conf['config']['tbl'])."` WHERE `".addslashes($params['dir_field_there'])."`='".addslashes($folder)."' && `".addslashes($params['img_field_there'])."`='".addslashes($r[$params['img_field_there']])."'";
		$this->query($sql);
	}
}

$arr2 = array();

foreach($all_files1 as $file)
{
	$sql = "INSERT INTO `#".addslashes($fotocom_conf['config']['tbl'])."` SET `".addslashes($params['dir_field_there'])."`='".addslashes($folder)."', `".addslashes($params['img_field_there'])."`='".addslashes($file)."', `public`='1'";
	$this->query($sql);

	$arr2[$file] = array('public'=>'1');
}
$fotos_info = array_merge($arr2, $fotos_info);


$i = 0;

$uniq_id = substr(md5(time()),0,5);

echo '<form method="post" id="fotosave'.$uniq_id.'" enctype="multipart/form-data">';
foreach($fotos_info as $file=>$info)
{
	$ext = strtolower(end(split("\.",$file)));

	echo '<div class="fotoitem"><div style="float:left;overflow:hidden;margin-bottom:10px;">';
	if($ext=='swf'){
		echo '<div style="float:left"><object wmode="opaque">';
		echo '<embed src="'.$folder.$file.'" width="150" wmode="opaque" quality="high"/>';
		echo '</object></div>';
	}
	else echo '<img src="/showimg.php?'.$folder.$file.'&w=150" class="preview" /></div>';
	echo '<div style="float:left;overflow:hidden;margin-bottom:10px;margin-left:10px;">';
	foreach($foto_fields as $field)
	{
		$info[$field['db_fname'].'['.$i.']'] = $info[$field['db_fname']];
		$field['db_fname'] = $field['db_fname'].'['.$i.']';

		echo '<div>';
		echo '<div class="fotolabel">'.$this->core_echomui($field['mui_title']).':</div> ';
		echo '<div class="fotoinp">'.$this->get_right_field($field, $info, $field['params']).'</div>';
		echo '<div style="clear:both"></div>';
		echo '</div>';
	}

	echo '</div></div><div style="clear:both"></div>';
	echo '<input type="hidden" name="foto-filename['.$i.']" value="'.$file.'" />';
	$i++;
}
echo sizeof($fotos_info)?'<input type="button" value="'.$this->core_echomui('savebutton').'" onclick="loadXMLDoc(\'/ajax-index.php?isadm=1&page=loadfotos&dir='.$_GET['dir'].'&fui='.$_GET['fui'].'&param='.str_replace('"',"\'",$_GET['param']).'&divid='.$_GET['divid'].'\',\''.$_GET['divid'].'\',\'fotosave'.$uniq_id.'\');">'."<input type=\"button\" value=\"".$this->core_echomui('go_sort_title')."\" onclick=\"if(confirm('".$this->core_echomui('proceedsort')."')){return showdialog('/".$this->adm_path."/sort_items/?".$params['dir_field_here']."=".$folder."&comid=".$params['com_id']."','".$this->core_echomui('go_sort_diti')."','','','','reloadfotolist".$_GET['fui']."');}\" />":$this->core_echomui('nopictures');
echo '</form>';

//print_r($all_files);
//print_r($params);
//loadXMLDoc(\'/ajax-index.php?isadm=1&page=loadfotos&dir='.$_GET['dir'].'&param='.$_GET['param'].'&divid='.$_GET['divid'].'\',\''.$_GET['divid'].'\',\'fotosave'.$uniq_id.'\');
?>
<style>
div.fotoitem{padding:10px 10px 15px 10px;border-bottom:#ccc 1px solid;overflow:hidden;}
div.fotoitem img.preview{float:left;margin:0px 10px 10px 0px;}
div.fotoitem div.fotolabel{width:120px;font-weight:bold;float:left;overflow:hidden;text-align:right;padding-right:10px;}
div.fotoitem div.fotoinp{float:left;overflow:hidden;}
</style>