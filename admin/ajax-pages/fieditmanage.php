<?
$comid = intval($_GET['comid']);
$sql = "SELECT * FROM `#h_components` WHERE `id`=".$comid;
$res = $this->query($sql);
$comp_row = $this->fetch_assoc($res);
$cur_params = $this->adm_get_param($comp_row['config']); // текущие параметры компонента
$delfield = intval($_GET['delfield']);
$addfieldto = intval($_GET['addfieldto']);
$move2bm = intval($_GET['move2bm']);

$this->core_get_mui($comid);

if(isset($_GET['getfromparent']))
{
	$sql = "SELECT * FROM `#h_components_listedittable` WHERE `pid`=0 && `com_id`=".intval($comp_row['pid']);
	$res = $this->query($sql);
	while($row = $this->fetch_assoc($res))
	{
		$row_id = $row['id'];
		unset($row['id']);
		$row['com_id'] = $comp_row['id'];
		$sql = "INSERT INTO `#h_components_listedittable` SET ";
		$sql_arr = array();
		foreach($row as $k=>$v)
		{
			$sql_arr[] = "`".$k."`='".addslashes($v)."'";
		}
		$sql .= join(",",$sql_arr);
		$this->query($sql);
		$new_pid = $this->insert_id();

		$sql = "SELECT * FROM `#h_components_listedittable` WHERE `pid`=".$row_id." && `com_id`=".intval($comp_row['pid']);
		$res1 = $this->query($sql);
		while($row1 = $this->fetch_assoc($res1))
		{
			$row1['com_id'] = $comp_row['id'];
			$row1['pid'] = $new_pid;
			unset($row1['id']);

			$sql = "INSERT INTO `#h_components_listedittable` SET ";
			$sql_arr = array();
			foreach($row1 as $k=>$v)
			{
				$sql_arr[] = "`".$k."`='".addslashes($v)."'";
			}
			$sql .= join(",",$sql_arr);
			$this->query($sql);
		}
	}
	echo "export complete";
}

if(sizeof($_POST['addeditbookmark']) && $_POST['addeditbookmark']['name'])
{
	$sql = "INSERT INTO `#h_components_listedittable` SET
	`com_id`=".$comid.",
	`mui_title`='admc_bm-".addslashes($_POST['addeditbookmark']['name'])."',
	`sort`=".intval($_POST['addeditbookmark']['sort'])."";
	$res = $this->query($sql);

	if($res)
	{
		$this->adm_add_sys_mes($this->core_echomui('admc_edbmadd'),"ok");
		$_POST = array();
	}
	else $this->adm_add_sys_mes($this->core_echomui('admc_edbmadderr'),"err");
}

if($addfieldto)
{
	$sql = "SELECT MAX(`sort`) as `sort` FROM `#h_components_listedittable` WHERE `com_id`=".$comid." && `pid`=".$addfieldto."";
	$sort = $this->fetch_assoc($this->query($sql));
	$sort = $sort['sort']+1;

	$sql = "INSERT INTO `#h_components_listedittable` SET
	`com_id`=".$comid.",
	`pid`=".$addfieldto.",
	`sort`=".$sort."";
	$res = $this->query($sql);
}

if($delfield)
{
	$this->can_delete = 1;
	$res = $this->adm_del_row($delfield, "#h_components_listedittable", "", "", "pid",0);
	
	if($res) $this->adm_add_sys_mes($this->core_echomui('admc_fielddel'),"del");
	else $this->adm_add_sys_mes($this->core_echomui('admc_fielddelerr'),"err");
}

if($move2bm)
{
	$sql = "UPDATE `#h_components_listedittable` SET `pid`=".$move2bm."	WHERE `id`=".intval($_GET['curid'])." && `com_id`=".$comid;
	$res = $this->query($sql);
}

if(sizeof($_POST['listtableparams']))
{
	$fieldid = intval($_POST['listtableparams']['fieldid']);
	$sql = "UPDATE `#h_components_listedittable` SET `params`='".addslashes($_POST['listtableparams']['param'])."' WHERE `com_id`=".intval($comp_row['id'])." && `id`=".$fieldid;
	$res = $this->query($sql);
	if($res) $this->adm_add_sys_mes($this->core_echomui('admc_paramedited'),"ok");
	else $this->adm_add_sys_mes($this->core_echomui('admc_parameditederr'),"err");
}


$sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($comp_row['id'])." ORDER BY `sort` ASC";
$infa = $this->core_get_tree($sql);
$rows = $this->core_get_tree_keys(0, array(), $infa, 0, 1);

$alldbfnames = array();
$ar1 = $this->mysql_get_fields("#".$cur_params['tbl']);
foreach($ar1 as $item)
{
	if($item['Extra']!="auto_increment") $alldbfnames[$item['Field']] = $item['Field'];
}


$alltypes = array();
$alltypes_db = array();
$ar = $this->mysql_get_fields("#h_components_listedittable");
$typesarr = array();
foreach($ar as $item)
{
	if($item['Field']=='type') $typesarr = $item;
	if($item['Field']=='db_fieldtype') $typesarr_db = $item;
}

$typesarr['Type'] = trim(trim(preg_replace("/^enum/i", "", $typesarr['Type']), ")"),"(");
$typesarr['Type'] = split(",", $typesarr['Type']);
foreach($typesarr['Type'] as $item)
{
	$item = trim($item, "'");
	$alltypes[$item] = $item;
}

$typesarr_db['Type'] = trim(trim(preg_replace("/^enum/i", "", $typesarr_db['Type']), ")"),"(");
$typesarr_db['Type'] = split(",", $typesarr_db['Type']);
foreach($typesarr_db['Type'] as $item)
{
	$item = trim($item, "'");
	$alltypes_db[$item] = $item;
}

$allbookmarks = array();
foreach($rows as $row)
{
	if($row['this_space']) unset($alldbfnames[$row['db_fname']]);
	else $allbookmarks[$row['id']] = $this->core_echomui($row['mui_title']);
}
$alldbfnames['_self'] = $this->core_echomui('self_var');

$this->adm_show_sys_mes();

$dbkey = substr(md5("h_components_listedittable"),0,5);
?>
<?foreach($rows as $row){$ind=$row['id'];


$chmuicode = "document.getElementById('edit-input-mui_title-".$ind.$dbkey."').style.display='inline';document.getElementById('edit-text-mui_title-".$ind.$dbkey."').style.display='none';document.getElementById('edit-field-mui_title-".$ind.$dbkey."').value='".$comp_row['adm_title']."-f-'+document.getElementById('edit-field-db_fname-".$ind.$dbkey."').value;document.getElementById('edit-input-tooltip-".$ind.$dbkey."').style.display='inline';document.getElementById('edit-text-tooltip-".$ind.$dbkey."').style.display='none';document.getElementById('edit-field-tooltip-".$ind.$dbkey."').value='".$comp_row['adm_title']."-t-'+document.getElementById('edit-field-db_fname-".$ind.$dbkey."').value;"
?>
	<?if(!$row['this_space']){
	$allbmwithoutcur = array_diff($allbookmarks,array($row['id']=>$this->core_echomui($row['mui_title'])));
	
	if($tableopen) echo "</table><br>";
	?>
	<div style="font-weight:bold;font-size:15px;">
		<a href="" onclick="realdel = confirm('<?=$this->core_echomui('act_del')?>?');if(realdel){loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>&delfield=<?=$row['id']?>','fieldeditdiv');}return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/del.png" border='0' align='absmiddle' title="<?=$this->core_echomui('act_del')?>"></a>
		<?=$this->core_echomui($row['mui_title'])?> 
		(mui: <?=$this->adm_show_editinput("mui_title", $row['mui_title'], $row['id'], "width:100px;height:18px;","h_components_listedittable", "editform","",1)?>)
		<?=$this->core_echomui('bmsorttitle');?>: <?=$this->adm_show_editinput("sort", $row['sort'], $ind, "width:20px;height:18px;","h_components_listedittable", "editform","",0)?>
		<a href="" onclick="loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>&addfieldto=<?=$row['id']?>','fieldeditdiv');return false;" style="font-weight:normal;font-size:11px;text-decoration:none"><?=$this->core_echomui('addfieldtitle');?></a>
	</div>
	<table class='f_table'>
	<th><?=$this->core_echomui('admc_tbhat_field')?></th>
	<th><?=$this->core_echomui('admc_tbhat_type')?></th>
	<th><?=$this->core_echomui('admc_tbhat_typedb')?></th>
	<th><?=$this->core_echomui('admc_tbhat_mui')?></th>
	<th><?=$this->core_echomui('admc_tbhat_tooltipmui')?></th>
	<th><?=$this->core_echomui('admc_tbhat_params')?></th>
	<th><?=$this->core_echomui('admc_tbhat_sort')?>&nbsp;<a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=savex&field=sort&formfieldname=tbedsort&dbtable=h_components_listedittable','','editform');return false;"><img src='/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/filesave.png' border=0></a></th>
	<th><?=$this->core_echomui('admc_tbhat_req')?></th>
	<th><?=$this->core_echomui('admc_tbhat_public')?></th>
	<th><?=$this->core_echomui('admc_tbhat_useinsql')?></th>
	<th><?=$this->core_echomui('admc_tbhat_act')?></th>
	<?$tableopen = 1;}else{if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";?>
	<tr class='<?=$zebra_class?>' <?echo ($this->classes_switch($zebra_class,'f_hover'))?>>
		<td>
			<?=$this->adm_show_editselect("db_fname", $row['db_fname'], $ind, array_merge(array($row['db_fname']=>$row['db_fname']),$alldbfnames), "width:100px;height:18px;","h_components_listedittable", "editform","","",0,$chmuicode)?>
		</td>
		<td><?=$this->adm_show_editselect("type", $row['type'], $ind, $alltypes, "width:100px;height:18px;","h_components_listedittable", "editform","","",0)?></td>
		<td><?=$this->adm_show_editselect("db_fieldtype", $row['db_fieldtype'], $ind, $alltypes_db, "width:100px;height:18px;","h_components_listedittable", "editform","","",0)?></td>
		<td><?=$this->adm_show_editinput("mui_title", $row['mui_title'], $ind, "width:100px;height:18px;","h_components_listedittable", "editform","",0)?></td>
		<td><?=$this->adm_show_editinput("tooltip", $row['tooltip'], $ind, "width:100px;height:18px;","h_components_listedittable", "editform","",0)?></td>
		<td>
			<span class="simpleeditfield" onmouseover="this.className='simpleeditfield_h'" onmouseout="this.className='simpleeditfield'" onclick="document.getElementById('fieldparamdiv1').style.display='block';loadXMLDoc('/ajax-index.php?page=fieldeditparam&isadm=1&comid=<?=$comid?>&fieldid=<?=$row['id']?>&ep=1','fieldparamdiv1');"><?echo $row['params'] ? substr($row['params'],0,50).(strlen($row['params'])>50?"...":"") : "<i>не заданы</i>";?></span>
		</td>
		<td><?=$this->adm_show_input("tbedsort[".$row['id']."]", $row['sort'], "", "width:50px;height:15px;text-align:center;", "")?></td>
		<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listedittable&field=req');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_<?=$row['req']?>.png" border="0" id="swh_components_listedittablereq-<?=$row['id']?>"></a></td>
		<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listedittable&field=public');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_<?=$row['public']?>.png" border="0" id="swh_components_listedittablepublic-<?=$row['id']?>"></a></td>
		<td><a href="" onclick="loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=<?=$row['id']?>&dbtable=h_components_listedittable&field=useinquery');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/ticks/tick_<?=$row['useinquery']?>.png" border="0" id="swh_components_listedittableuseinquery-<?=$row['id']?>"></a></td>
		<td>
			<div id="move2bmdiv-<?=$row['id']?>" style="padding:2px;display:none;height:18px;position:absolute;background:#fff;border:1px solid #cfcfcf;right:6px">
				<?=$this->core_echomui('act_move1')?>: <?=$this->adm_show_select("edfmoveto", "", $allbmwithoutcur, "", "id='move2bm-".$row['id']."'")?>
				<a href="" onclick="document.getElementById('move2bmdiv-<?=$row['id']?>').style.display='none';return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/cancel.png" border='0' align='absmiddle'></a>&nbsp;
				<a href="" onclick="loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>&curid=<?=$row['id']?>&move2bm='+document.getElementById('move2bm-<?=$row['id']?>').value,'fieldeditdiv');return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/save.png" border='0' align='absmiddle'></a>
			</div>
			<a href="" onclick="realdel = confirm('<?=$this->core_echomui('admc_tdmanage_delconf')?>');if(realdel){loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>&delfield=<?=$row['id']?>','fieldeditdiv');}return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/del.png" border='0' align='absmiddle' title="<?=$this->core_echomui('act_del')?>"></a>
			<a href="" onclick="document.getElementById('move2bmdiv-<?=$row['id']?>').style.display='inline';return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/move.png" border='0' align='absmiddle' title="<?=$this->core_echomui('act_move')?>"></a>		
		</td>
	</tr>
	<?}?>
<?}?><?//echo "<pre>";print_r($rows);echo "</pre>";?>
</table>
<br><strong><?=$this->core_echomui('addbmtitle');?></strong>:<br>
mui: admc_bm-<?=$this->adm_show_input("addeditbookmark[name]", $_POST['addeditbookmark']['name'], "", "width:100px;","onkeyup=\"if(this.value) document.getElementById('fieldeditdiv-saveimg1').style.display='inline';else document.getElementById('fieldeditdiv-saveimg1').style.display='none';\"")?>, 
<?=$this->core_echomui('bmsorttitle');?>: <?=$this->adm_show_input("addeditbookmark[sort]", $_POST['addeditbookmark']['sort'], "", "width:20px;")?> 
<a href="" onclick="loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>','fieldeditdiv','editform');return false;" id="fieldeditdiv-saveimg1" style="display:<?echo $_POST['addeditbookmark']['name']?"inline":"none"?>;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/save.png" border='0' align='absmiddle'></a>

<div id="fieldparamdiv1" style="display:none;position:absolute;top: 0px;left:50%;border:1px solid #cfcfcf;background:#fff;width:500px;padding:5px;margin-left:-250px;"></div>
<br><a href="" onclick="loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>','fieldeditdiv');return false;">reload</a>
<?if($comp_row['pid']){?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="" onclick="if(confirm('Are you sure you want to proceed export?')) loadXMLDoc('/ajax-index.php?page=fieditmanage&isadm=1&comid=<?=$comid?>&getfromparent','fieldeditdiv');return false;">get content from parent component</a><?}?>