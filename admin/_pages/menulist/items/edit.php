<div class='headspace'></div><?php //v.1.0.
/* RedixCMS 4.0*/
$this->adm_get_com_config(); // <- получили конфиг компонента из таблицы #__components
if(!$this->id && intval($this->adm_com_config['config']['defid'])) $this->id = intval($this->adm_com_config['config']['defid']);
$this->adm_showparttitle($this->core_echomui('menulist_items:admc_title')." / {edittext}"); // {edittext} автоматически заменяется на "редактировать" или "добавить" в зависимости от действия

// получаем запись, если есть id
if($this->id)
{
	$sql = "SELECT * FROM `#".$this->adm_com_config['config']['tbl']."` WHERE `id`=".$this->id;
	$res = $this->query($sql);
	$row = $this->fetch_assoc($res);
}

// сохраняем информацию
if($_POST['save'] || $_POST['app'])
{
	$this->adm_go_upload_file(); // загружаем файлы
	
	if($_POST['link'])
	{
		$sql = "SELECT `id` FROM `#__sitemap` WHERE `url`='".addslashes($_POST['link'])."'";
		$page_id = $this->fetch_assoc($this->query($sql));
		$_POST['page_id'] = $page_id['id'];
	}

	$fields = $this->get_edit_fields();
	
	$sql = $this->adm_get_edit_sql("#".$this->adm_com_config['config']['tbl'],$fields);
	$res = $this->query($sql);
	
	if(!$this->id){$this->id = $this->insert_id();$this->adm_add_sys_mes($this->core_echomui('admc_record_add'),"ok");}
	else{$this->adm_add_sys_mes($this->core_echomui('admc_record_edited'),"ok");}
	
	if($_POST['save']) $link = '/'.$this->adm_path.'/'.$this->pre_way.'/';
	elseif($_POST['app']) $link = '/'.$this->way_url_get.'/?id='.$this->id;
	
	$this->reload($link);
}

$this->adm_show_sys_mes(); // <- выводим системные сообщения

$this->adm_get_editbookmarks($row); // <- получаем массив закладок с полями для редактирования
$hiddenstring = array(); // <- массив для вывода скрытых полей

if($this->adm_com_config['config']['editbut-save']==1)
	$save = array("value"=>$this->core_echomui('admc_button_save'),"class"=>"savebutton","alter_class"=>"savebutton_h");

if($this->adm_com_config['config']['editbut-apply']==1)
	$app = array("value"=>$this->core_echomui('admc_button_applay'),"class"=>"appbutton","alter_class"=>"appbutton_h");

if($this->adm_com_config['config']['editbut-cancel']==1)
	$cancel = array("value"=>$this->core_echomui('admc_button_cancel'),"class"=>"cancelbutton","alter_class"=>"cancelbutton_h");

$this->adm_showall4edit($save, $app, $cancel);
$this->jsonload[] = "showmenupunkti(document.getElementsByName('mid')[0].value);";
?>
<script language="JavaScript">

document.getElementsByName('mid')[0].onchange = function(){
	showmenupunkti(document.getElementsByName('mid')[0].value);
}
function showmenupunkti(val)
{
	loadXMLDoc('/ajax-index.php?isadm=1&page=menupunkti&mid='+val+'&ppm=<?=intval($row['pid'])?><?echo $this->id?'&cid='.$this->id:'';?>&pmedit=1','2td2');
}
</script>