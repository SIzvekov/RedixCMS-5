<?
$comid = intval($_GET['comid']);
$sql = "SELECT * FROM `#h_components` WHERE `id`=".$comid;
$res = $this->query($sql);
$comp_row = $this->fetch_assoc($res);
$cur_params = $this->adm_get_param($comp_row['config']); // текущие параметры компонента
$delfield = intval($_GET['delfield']);

if(sizeof($_POST['listtableparams']))
{
	$fieldid = intval($_POST['listtableparams']['fieldid']);
	$sql = "UPDATE `#h_components_listtable` SET `params`='".addslashes($_POST['listtableparams']['param'])."' WHERE `com_id`=".intval($comp_row['id'])." && `id`=".$fieldid;
	$res = $this->query($sql);
	if($res) $this->adm_add_sys_mes($this->core_echomui('admc_paramedited'),"ok");
	else $this->adm_add_sys_mes($this->core_echomui('admc_parameditederr'),"err");
}
if(sizeof($_POST['addlistfield']) && $_GET['addfiled'])
{
	if($_POST['addlistfield']['nosort']) $_POST['addlistfield']['nosort'] = 0;
	else $_POST['addlistfield']['nosort'] = 1;

	if(!$_POST['addlistfield']['db_fname']) $_POST['addlistfield']['db_fname'] = '';
	
	$sql = "INSERT INTO `#h_components_listtable` SET
	`com_id`=".$comid.",
	`db_fname`='".addslashes($_POST['addlistfield']['db_fname'])."',
	`mui_title`='".addslashes($_POST['addlistfield']['mui_title'])."',
	`type`='".addslashes($_POST['addlistfield']['type'])."',
	`edit`='".intval($_POST['addlistfield']['edit'])."',
	`del`='".intval($_POST['addlistfield']['del'])."',
	`nosort`='".intval($_POST['addlistfield']['nosort'])."',
	`sort`='".intval($_POST['addlistfield']['sort'])."',
	`public`='".intval($_POST['addlistfield']['public'])."'";
	
	$res = $this->query($sql);
	
	if($res)
	{
		$this->adm_add_sys_mes($this->core_echomui('admc_fieldadded'),"ok");
		$_POST = array();
	}
	else $this->adm_add_sys_mes($this->core_echomui('admc_fieldaddederr'),"err");
	
}

if($delfield)
{
	$sql = "DELETE FROM `#h_components_listtable` WHERE `id`=".$delfield;
	$res = $this->query($sql);
	
	if($res) $this->adm_add_sys_mes($this->core_echomui('admc_fielddel'),"del");
	else $this->adm_add_sys_mes($this->core_echomui('admc_fielddelerr'),"err");
}

$sql = "SELECT * FROM `#h_components_listtable` WHERE `com_id`=".intval($comp_row['id'])." ORDER BY `sort` ASC";
$res = $this->query($sql);

$alldbfnames = array("0"=>" ");
$ar1 = $this->mysql_get_fields("#".$cur_params['tbl']);
foreach($ar1 as $item)
{
	$alldbfnames[$item['Field']] = $item['Field'];
}

$alltypes = array();
$ar = $this->mysql_get_fields("#h_components_listtable");
$typesarr = array();
foreach($ar as $item)
{
	if($item['Field']=='type') $typesarr = $item;
}
$typesarr['Type'] = trim(trim(preg_replace("/^enum/i", "", $typesarr['Type']), ")"),"(");
$typesarr['Type'] = split(",", $typesarr['Type']);
foreach($typesarr['Type'] as $item)
{
	$item = trim($item, "'");
	$alltypes[$item] = $item;
}


$this->adm_show_sys_mes();
?>
<table class='f_table'>
	<th><?=$this->core_echomui('admc_tbhat_field')?></th>
	<th><?=$this->core_echomui('admc_tbhat_mui')?></th>
	<th><?=$this->core_echomui('admc_tbhat_type')?></th>
	<th><?=$this->core_echomui('admc_tbhat_params')?></th>
	<th><?=$this->core_echomui('admc_tbhat_edit')?></th>
	<th><?=$this->core_echomui('admc_tbhat_del')?></th>
	<th><?=$this->core_echomui('admc_tbhat_nosort')?></th>
	<th><?=$this->core_echomui('admc_tbhat_sort')?>&nbsp;<a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=savex&field=sort&formfieldname=tblistsort&dbtable=h_components_listtable','','editform');return false;"><img src='/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/filesave.png' border=0></a></th>
	<th><?=$this->core_echomui('admc_tbhat_public')?></th>
	<th><?=$this->core_echomui('admc_tbhat_act')?></th>
<?while($row = $this->fetch_assoc($res)){
unset($alldbfnames[$row['db_fname']]);

$ind = $row['id'];
	if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";
?>
<tr class='<?=$zebra_class?>' <?echo ($this->classes_switch($zebra_class,'f_hover'))?>>
	<td><?=$this->adm_show_editselect("db_fname", $row['db_fname'], $ind, $alldbfnames, "width:100px;height:18px;","h_components_listtable", "editform","","",0)?>
	<td><?=$this->adm_show_editinput("mui_title", $row['mui_title'], $ind, "width:100px;height:18px;","h_components_listtable", "editform","",0)?></td>
	<td><?=$this->adm_show_editselect("type", $row['type'], $ind, $alltypes, "width:100px;height:18px;","h_components_listtable", "editform","","",0)?></td>
	<td>
		<span class="simpleeditfield" onmouseover="this.className='simpleeditfield_h'" onmouseout="this.className='simpleeditfield'" onclick="document.getElementById('fieldparamdiv').style.display='block';loadXMLDoc('/ajax-index.php?page=fieldeditparam&isadm=1&comid=<?=$comid?>&fieldid=<?=$row['id']?>','fieldparamdiv');"><?echo $row['params'] ? substr($row['params'],0,50).(strlen($row['params'])>50?"...":"") : "<i>не заданы</i>";?></span>
	</td>
	<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listtable&field=edit');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_<?=$row['edit']?>.png" border="0" id="swh_components_listtableedit-<?=$row['id']?>"></a></td>
	<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listtable&field=del');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_<?=$row['del']?>.png" border="0" id="swh_components_listtabledel-<?=$row['id']?>"></a></td>
	<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listtable&field=nosort&invers=1');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_invers_<?=$row['nosort']?>.png" border="0" id="swh_components_listtablenosort-<?=$row['id']?>"></a></td>
	<td><?=$this->adm_show_input("tblistsort[".$row['id']."]", $row['sort'], "", "width:50px;height:15px;text-align:center;", "")?></td>
	<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listtable&field=public');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_<?=$row['public']?>.png" border="0" id="swh_components_listtablepublic-<?=$row['id']?>"></a></td>
	<td><a href="" onclick="realdel = confirm('<?=$this->core_echomui('admc_tdmanage_delconf')?>');if(realdel){loadXMLDoc('/ajax-index.php?page=filistmanage&isadm=1&comid=<?=$comid?>&delfield=<?=$row['id']?>','fieldlistdiv');}return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/del.png" border='0' align='absmiddle'></a></td>
</tr>
<?}?>
<tr><td colspan="10" style="text-align:left;"><strong><?=$this->core_echomui('admc_addfield')?></strong></td></tr>
<tr>
	<td><?=$this->adm_show_select("addlistfield[db_fname]", $_POST['addlistfield']['db_fname'], $alldbfnames, "", "id='add-db_fname' onchange=\"if(this.value==0) val=''; else val=this.value; document.getElementById('add-mui_title').value=val;\"","")?></td>
	<td><?=$this->adm_show_input("addlistfield[mui_title]", $_POST['addlistfield']['mui_title'],"","width:100px;height:18px","id='add-mui_title'")?></td>
	<td><?=$this->adm_show_select("addlistfield[type]", $_POST['addlistfield']['type'], $alltypes, "", "id='add-type'","")?></td>
	<td>-</td>
	<td><?=$this->adm_show_input("addlistfield[edit]", "1", $_POST['addlistfield']['edit'],"","id='add-edit'","checkbox")?></td>
	<td><?=$this->adm_show_input("addlistfield[del]", "1", $_POST['addlistfield']['del'],"","id='add-del'","checkbox")?></td>
	<td><?=$this->adm_show_input("addlistfield[nosort]", "1", $_POST['addlistfield']['nosort'],"","id='add-nosort'","checkbox")?></td>
	<td><?=$this->adm_show_input("addlistfield[sort]", $_POST['addlistfield']['sort'],"","width:50px;height:18px","id='add-mui_title'")?></td>
	<td><?=$this->adm_show_input("addlistfield[public]", "1", $_POST['addlistfield']['public'],"","id='add-public'","checkbox")?></td>
	<td><a href="" onclick="loadXMLDoc('/ajax-index.php?page=filistmanage&isadm=1&comid=<?=$comid?>&addfiled=1','fieldlistdiv','editform');return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/save.png" border='0' align='absmiddle'></a></td>
</tr>
</table>
<div id="fieldparamdiv" style="display:none;position:absolute;top: 0px;left:50%;border:1px solid #cfcfcf;background:#fff;width:500px;padding:5px;margin-left:-250px;"></div>
<a href="" onclick="loadXMLDoc('/ajax-index.php?page=filistmanage&isadm=1&comid=<?=$comid?>','fieldlistdiv');return false;">reload</a>