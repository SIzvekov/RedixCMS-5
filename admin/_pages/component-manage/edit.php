<div class='headspace'></div><?php //v.1.0.
/* RedixCMS 4.0*/
$this->adm_get_com_config(); // <- получили конфиг компонента из таблицы #__components
if(!$this->id && intval($this->adm_com_config['config']['defid'])) $this->id = intval($this->adm_com_config['config']['defid']);
$this->adm_showparttitle($this->core_echomui('component-manage:admc_title')." / {edittext}"); // {edittext} автоматически заменяется на "редактировать" или "добавить" в зависимости от 

// получаем запись, если есть id
if($this->id)
{
	$sql = "SELECT * FROM `#".$this->adm_com_config['config']['tbl']."` WHERE `id`=".$this->id;
	$res = $this->query($sql);
	$row = $this->fetch_assoc($res);
	
	if($row['system'])
	{
		$link = '/'.$this->adm_path.'/'.$this->pre_way.'/';
		$this->reload($link);
	}
}

// сохраняем информацию
if($_POST['save'] || $_POST['app'])
{
//		$ar = $this->mysql_get_tables();
//		$ar = $this->mysql_get_fields($ar[0]);

	// Создаём БД если надо
	if($_POST['create_config_tbl'])
	{
		
		$params = $this->adm_get_param($_POST['config']);
		
		// Получаем старый Конфиг, если запись не новая
		if($this->id)
		{
			$sql = "SELECT `config` FROM `#h_components` WHERE `id`=".$this->id;
			$res = $this->query($sql);
			$row = $this->fetch_assoc($res);
			$old_params = $this->adm_get_param($row['config']);
			
			if($old_params['tbl'] && $old_params['tbl']!=$params['tbl']) // если в старом конфиге был параметр tbl и он не равен новому параметру tbl, удаляем старую таблицу
			{
				$del_old_sql = "DROP TABLE IF EXISTS `#".addslashes($old_params['tbl'])."`";
			}
		}
		
		if($params['tbl'])
		{
			// получаем список таблиц
			$all_tables = $this->mysql_get_tables();
			
			switch($_POST['create_config_tbl_type'])
			{
				case '1':
					if(!in_array("#".addslashes($params['tbl']), $all_tables)) // таблица не существует
					{
						$sql = "CREATE TABLE `#".addslashes($params['tbl'])."` (`id` INT NOT NULL AUTO_INCREMENT ,`pid` SMALLINT NOT NULL , `sort` SMALLINT DEFAULT  '0' NOT NULL ,
 `public` ENUM(  '0',  '1' ) DEFAULT  '0' NOT NULL , PRIMARY KEY ( `id` )) COMMENT = 'com_adm_title:".addslashes($_POST['adm_title'])."'";
						$this->query($sql,1);
						$this->adm_add_sys_mes($this->core_echomui('admc_newtable_created'),"ok");
					}else $this->adm_add_sys_mes($this->core_echomui('admc_newtable_notcreated_ex'),"war");
					
					if($del_old_sql)
					{
						$this->query($del_old_sql,1);
						$this->adm_add_sys_mes($this->core_echomui('admc_oldtable_del'),"ok");
					}
				break;
				
				case '2':
					$sql = "DROP TABLE IF EXISTS `#".addslashes($params['tbl'])."`";
					$this->query($sql,1);
					$sql = "CREATE TABLE `#".addslashes($params['tbl'])."` (`id` INT NOT NULL AUTO_INCREMENT ,`pid` SMALLINT NOT NULL , `sort` SMALLINT DEFAULT  '0' NOT NULL ,
 `public` ENUM(  '0',  '1' ) DEFAULT  '0' NOT NULL , PRIMARY KEY ( `id` )) COMMENT = 'com_adm_title:".addslashes($_POST['adm_title'])."'";
					$this->query($sql,1);
					$this->adm_add_sys_mes($this->core_echomui('admc_newtable_created'),"ok");
					
					if($del_old_sql)
					{
						$this->query($del_old_sql,1);
						$this->adm_add_sys_mes($this->core_echomui('admc_oldtable_del'),"ok");
					}
				break;
				
				case '3':
					if(!in_array("#".addslashes($params['tbl']), $all_tables)) // таблица не существует
					{
						$sql = "ALTER TABLE `#".addslashes($old_params['tbl'])."` RENAME `#".addslashes($params['tbl'])."`";
						$this->query($sql,1);
						$this->adm_add_sys_mes($this->core_echomui('admc_newtable_renamed'),"ok");
					}else $this->adm_add_sys_mes($this->core_echomui('admc_newtable_norenamed_ex'),"err");
				break;
			}
		}
		
		if($_POST['create_config_tbl']==2)
		{
			$sql = "DROP TABLE IF EXISTS `#".addslashes($params['tbl'])."`";
			$this->query($sql,1);
			$sql = "DROP TABLE IF EXISTS `#".addslashes($old_params['tbl'])."`";
			$this->query($sql,1);
			
			$this->adm_add_sys_mes($this->core_echomui('admc_oldtable_del'),"ok");
		}
	}
	
	$this->adm_go_upload_file(); // загружаем файлы
	
	$fields = $this->get_edit_fields();
	
	$sql = $this->adm_get_edit_sql("#".$this->adm_com_config['config']['tbl'],$fields);
	$res = $this->query($sql);
	
	if(!$this->id){$this->id = $this->insert_id();$this->adm_add_sys_mes($this->core_echomui('admc_record_add'),"ok");$thisfirstadd=1;}
	else{$this->adm_add_sys_mes($this->core_echomui('admc_record_edited'),"ok");}
	
	if($_POST['save']) $link = '/'.$this->adm_path.'/'.$this->pre_way.'/';
	elseif($_POST['app']) $link = '/'.$this->way_url_get.'/?id='.$this->id;
	
	$this->reload($link);

	// Добавляем поля в таблицу списка полей
	if($this->id && $thisfirstadd)
	{
		$sql = "INSERT INTO `#h_components_listtable` SET `com_id`='".$this->id."', `db_fname`='', `mui_title`='index', `type`='index', `edit`='0', `del`='0', `nosort`='1', `sort`='0', `public`='1'";$this->query($sql);

		$sql = "INSERT INTO `#h_components_listtable` SET `com_id`='".$this->id."', `db_fname`='sort', `mui_title`='sort', `type`='field', `edit`='1', `del`='0', `nosort`='0', `sort`='96', `public`='1'";$this->query($sql);

		$sql = "INSERT INTO `#h_components_listtable` SET `com_id`='".$this->id."', `db_fname`='public', `mui_title`='public', `type`='switch', `edit`='1', `del`='0', `nosort`='0', `sort`='97', `public`='1'";$this->query($sql);

		$sql = "INSERT INTO `#h_components_listtable` SET `com_id`='".$this->id."', `db_fname`='', `mui_title`='edit', `type`='edit', `edit`='1', `del`='0', `nosort`='1', `sort`='98', `public`='1'";$this->query($sql);

		$sql = "INSERT INTO `#h_components_listtable` SET `com_id`='".$this->id."', `db_fname`='', `mui_title`='delete', `type`='del', `edit`='0', `del`='1', `nosort`='1', `sort`='99', `public`='1'";$this->query($sql);


		$sql = "INSERT INTO `#h_components_listedittable` SET `com_id`='".$this->id."', `mui_title`='admc_bm-content', `sort`=1, `public`='1'";$this->query($sql);
	}
}

$this->adm_show_sys_mes(); // <- выводим системные сообщения

$this->adm_get_editbookmarks($row); // <- получаем массив закладок с полями для редактирования

$cur_params = $this->adm_get_param($row['config']); // текущие параметры компонента

if($this->id && $cur_params['tbl'])
{
	$this->adm_bookmarks['База Данных'] = array(
	array("input"=>"<div id='dbdiv'>загрузка...</div>"),
	);
	
	$this->adm_bookmarks['Табл. записей'] = array(
	array("input"=>"<div id='fieldlistdiv'>загрузка...</div>"),
	);
	$this->adm_bookmarks['Табл. редактирования'] = array(
	array("input"=>"<div id='fieldeditdiv'>загрузка...</div>"),
	);
}

$hiddenstring = array(); // <- массив для вывода скрытых полей

if($this->adm_com_config['config']['editbut-save']==1)
	$save = array("value"=>$this->core_echomui('admc_button_save'),"class"=>"savebutton","alter_class"=>"savebutton_h");

if($this->adm_com_config['config']['editbut-apply']==1)
	$app = array("value"=>$this->core_echomui('admc_button_applay'),"class"=>"appbutton","alter_class"=>"appbutton_h");

if($this->adm_com_config['config']['editbut-cancel']==1)
	$cancel = array("value"=>$this->core_echomui('admc_button_cancel'),"class"=>"cancelbutton","alter_class"=>"cancelbutton_h");

$this->adm_showall4edit($save, $app, $cancel);?>
<script language="JavaScript">
window.onload = init();

function init()
{
	insertpreloadtext();
	<?if($this->id){?>dbfill();<?}?>
}

function insertpreloadtext()
{
	var obj=document.getElementById('2td5');
	if(!obj) {alert('<?=$this->core_echomui('admc_js_error1');?>');return false;}
	
	obj.innerHTML += "<br><span style=\"font-weight: bold;\"><?=$this->core_echomui('admc_js_txt1');?></span>:<br>";
	<?$params = array(
	"tbl"=>"__",
	"trashfname"=>"name",
	"colonpage_var"=>"10,20,50",
	"colonpage_def"=>"20",
	"editbut-save"=>"1",
	"editbut-apply"=>"1",
	"editbut-cancel"=>"1",
	"defid"=>"",
	"pidfield"=>"pid",
	"sc_colonpage"=>"10",
	"sort_fieldtitle"=>"name",
	"sort_blocksstyle"=>"",
	"sort_sqlextra"=>"",
	"sort_notree"=>"1",
	"sub_page_tbl"=>"__",
	"sub_page_of"=>"date",
	"sub_page_oc"=>"DESC",
	);
	foreach($params as $par=>$defval){?>obj.innerHTML += "<div onmouseover=\"this.style.background='#cfcfcf'\" onmouseout=\"this.style.background='none'\"><a href=\"\" onclick=\"setparam('<?=$par?>','<?=$defval?>');return false;\"><?=$par?></a> - <?=str_replace('"','\"',$this->core_echomui('admc_confparams_tooltip_'.$par));?></div>";<?}?>
	<?
	$tbl_c_arr = array(
	"1"=>$this->core_echomui('admc_conf_createtable_type1'),
	"2"=>$this->core_echomui('admc_conf_createtable_type2'),
	);
	
	if($this->id && $cur_params['tbl']) {$tbl_c_arr[3] = $this->core_echomui('admc_conf_createtable_type3');$askfordel = 1;}
	?>
	obj.innerHTML += "<br><span style=\"font-weight: bold;\"><?=$this->core_echomui('admc_conf_createtable');?></span><br>";
	obj.innerHTML += "<?=str_replace('"','\"',$this->adm_show_input("create_config_tbl", "0", "0", "", "id='create_config_tbl-0' onclick=\"checkstat(this);\"","radio"));?> <label for='create_config_tbl-0'><?=$this->core_echomui('admc_conf_createtable-0');?></label><br>";
	obj.innerHTML += "<?=str_replace('"','\"',$this->adm_show_input("create_config_tbl", "1", "0", "", "id='create_config_tbl-1' onclick=\"checkstat(this);\"","radio"));?> <label for='create_config_tbl-1'><?=$this->core_echomui('admc_conf_createtable-1');?></label>&nbsp;<?=str_replace('"','\"',$this->adm_show_select("create_config_tbl_type", "3", $tbl_c_arr, "", "id='create_config_tbl_type' disabled onchange=\"deltabask(this)\""));?><br>";
	<?if($this->id && $cur_params['tbl']){?>obj.innerHTML += "<?=str_replace('"','\"',$this->adm_show_input("create_config_tbl", "2", "0", "", "id='create_config_tbl-2' onclick=\"checkstat(this);\"","radio"));?> <label for='create_config_tbl-2'><?=$this->core_echomui('admc_conf_createtable-2');?></label><br>";<?}?>
}
function checkstat(obj)
{
	if(obj.value!=1) document.getElementById('create_config_tbl_type').disabled=true;
	else document.getElementById('create_config_tbl_type').disabled=false;
	
	if(obj.id=='create_config_tbl-2')
	{
		con = confirm('<?=$this->core_echomui('admc_create_config_tbl-2-confirm')?>');
		if(!con) document.getElementById('create_config_tbl-0').checked=true;
	}
}

function deltabask(obj)
{

	if(obj.value=='2')
	{
		con=confirm('<?=$this->core_echomui('admc_conf_createtable_type2_confirm')?>');
		if(!con) obj.value=1;
	}

	<?if($askfordel){?>if(obj.value=='1' || obj.value=='2')
	{	
		con=confirm('<?=$this->core_echomui('admc_conf_createtable_type12_confirm1')?>');
		if(!con) obj.value=3;
	}<?}?>
}

function setparam(par, defval, objid)
{
	if(!objid) objid='fieldid-config';
	if(document.getElementById(objid).value) document.getElementById(objid).value+="\n";
	document.getElementById(objid).value+=par+"="+defval;
}
<?if($this->id && $cur_params['tbl']){
$ar = $this->mysql_get_fields("#".$cur_params['tbl']);

if($ar[0]['Extra']=='auto_increment') $auto_increment = array_shift($ar);
else $auto_increment = array();
?>
function dbfill()
{
	var obj=document.getElementById('dbdiv');
	if(!obj) {return false;}
	
	loadXMLDoc('/ajax-index.php?page=dbmanage&isadm=1&comid=<?=$this->id?>','dbdiv');
	loadXMLDoc('/ajax-index.php?page=filistmanage&isadm=1&comid=<?=$this->id?>','fieldlistdiv');
	loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$this->id?>','fieldeditdiv');
}
<?}?>
</script>