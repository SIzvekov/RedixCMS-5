<?
$comid = intval($_GET['comid']);
$sql = "SELECT * FROM `#h_components` WHERE `id`=".$comid;
$res = $this->query($sql);
$row = $this->fetch_assoc($res);
$cur_params = $this->adm_get_param($row['config']); // текущие параметры компонента

if($cur_params['tbl'])
{
	if($_POST['addfield']['field'])
	{
		if($_POST['addfield']['default']!='') $default = "DEFAULT '".addslashes($_POST['addfield']['default'])."' ";
		else $default="";
		
		if($_POST['addfield']['val']) $values = "( ".$_POST['addfield']['val']." ) ";
		else $values = "";
		
		if($_POST['addfield']['after']) $addafter = " AFTER `".$_POST['addfield']['after']."`";
		else $addafter = "";
		
		$sql = "ALTER TABLE `#".$cur_params['tbl']."` ADD `".addslashes($_POST['addfield']['field'])."` ".$_POST['addfield']['type']." ".$values.$default."NOT NULL".$addafter;
		$res = $this->query($sql,1);
		
		if(!$res) $this->adm_add_sys_mes($this->core_echomui('admc_fieldadderror').":<br><span style=\"font-weight: bold;\">sql:</span> ".$sql."<br>", "err");
		else 
		{
			$this->adm_add_sys_mes($this->core_echomui('admc_fieldadded'), "ok");
			$_POST = array();
		}
		
	}

	if($_POST['editfield']['field'] && $_POST['editfield']['wasfield'])
	{
		if($_POST['editfield']['default']!='') $default = "DEFAULT '".addslashes($_POST['editfield']['default'])."' ";
		else $default="";
		
		if($_POST['editfield']['val']) $values = "( ".$_POST['editfield']['val']." ) ";
		else $values = "";
		
		$sql = "ALTER TABLE `#".$cur_params['tbl']."` CHANGE `".addslashes($_POST['editfield']['wasfield'])."` `".addslashes($_POST['editfield']['field'])."` ".$_POST['editfield']['type'].$values.$default;
		$res = $this->query($sql,1);
		
		if(!$res) $this->adm_add_sys_mes($this->core_echomui('admc_fieldediterror').":<br><span style=\"font-weight: bold;\">sql:</span> ".$sql."<br>", "err");
		else 
		{
			$this->adm_add_sys_mes($this->core_echomui('admc_fieldedited'), "ok");
			$_POST = array();
		}
		
	}



	$ar = $this->mysql_get_fields("#".$cur_params['tbl']);
	
	if($ar[0]['Extra']=='auto_increment') $auto_increment = array_shift($ar);
	else $auto_increment = array();
	
	if($_GET['delfield']) // Удаляем поле
	{
		$sql = "ALTER TABLE `#".$cur_params['tbl']."` DROP `".addslashes($_GET['delfield'])."`";
		$this->query($sql,1);
		$this->adm_add_sys_mes($this->core_echomui('admc_fielddel'), "del");
		$wasdeletedfield = $_GET['delfield'];
	}
	
	$alltypes = array(
"VARCHAR"=>"VARCHAR",
"TINYINT"=>"TINYINT",
"TEXT"=>"TEXT", // no def, no val
"DATE"=>"DATE", // no val, spec def
"SMALLINT"=>"SMALLINT", //no val
"MEDIUMINT"=>"MEDIUMINT", //no val
"INT"=>"INT", //no val
"BIGINT"=>"BIGINT", //no val
"FLOAT"=>"FLOAT", //no val
"DOUBLE"=>"DOUBLE", //no val
"DECIMAL"=>"DECIMAL", //no val
"DATETIME"=>"DATETIME", //no val, spec def
"TIMESTAMP"=>"TIMESTAMP",
"TIME"=>"TIME",
"YEAR"=>"YEAR",
"CHAR"=>"CHAR",
"TINYBLOB"=>"TINYBLOB",
"TINYTEXT"=>"TINYTEXT",
"BLOB"=>"BLOB",
"MEDIUMBLOB"=>"MEDIUMBLOB",
"MEDIUMTEXT"=>"MEDIUMTEXT",
"LONGBLOB"=>"LONGBLOB",
"LONGTEXT"=>"LONGTEXT",
"ENUM"=>"ENUM",
"SET"=>"SET",
"BINARY"=>"BINARY",
"VARBINARY"=>"VARBINARY",
	);
//$_RESULT=array();

$this->adm_show_sys_mes();
?>
<?=$this->core_echomui('admc_tdmanage_table')?>: <span style="font-weight: bold;"><?=$this->prefixed("#".$cur_params['tbl'])?></span><br><br>
<table class='f_table'><th><?=$this->core_echomui('admc_tdmanage_field')?></th><th><?=$this->core_echomui('admc_tdmanage_type')?></th><th><?=$this->core_echomui('admc_tdmanage_default')?></th><th><?=$this->core_echomui('admc_tdmanage_edit')?></th><th><?=$this->core_echomui('admc_tdmanage_del')?></th>
<?if(sizeof($auto_increment)){$afterfield[$auto_increment['Field']]= $auto_increment['Field'];?><tr><td><i><?=$auto_increment['Field']?></td><td><i><?=$auto_increment['Type']?></td><td><i><?=($auto_increment['Default']?$auto_increment['Default']:"&nbsp;")?></td><td colspan=2><i><?=$auto_increment['Extra']?></td></tr><?}?>
<?foreach($ar as $item){
	if($item['Field']==$wasdeletedfield) continue; 
	$afterfield[$item['Field']]= $item['Field'];
	if($item['Field']!="sort" && $item['Field']!="public") $lastafteritem=$item;
	if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";
?><tr class='<?=$zebra_class?>' <?echo ($this->classes_switch($zebra_class,'f_hover'))?>>
	<td><?echo $_GET['editfield']==$item['Field'] ? $this->adm_show_input("editfield[field]", ($_POST['editfield']['field']?$_POST['editfield']['field']:$item['Field']),"","width:100px;height:18px","id='edit-field'").$this->adm_show_hidden("editfield[wasfield]",$item['Field']) : $item['Field']?></td>
	<td><?if($_GET['editfield']==$item['Field'])
	{
		$itemtype = split("\(",$item['Type']);
		$item['Type']=strtoupper(trim($itemtype[0]));
		$item['Val']=trim($itemtype[1],")");
		echo $this->adm_show_select("editfield[type]", ($_POST['editfield']['type']?$_POST['editfield']['type']:$item['Type']), $alltypes, "", "id='edit-type'","");
		echo $this->adm_show_input("editfield[val]",($_POST['editfield']['val']?$_POST['editfield']['val']:$item['Val']),"","width:100px;height:18px","id='edit-val'");
	}else{
		echo $item['Type'];
	}?></td>
	<td><?if($_GET['editfield']==$item['Field'])
	{
		echo $this->adm_show_input("editfield[default]",($_POST['editfield']['default']?$_POST['editfield']['default']:$item['Default']),"","width:100px;height:18px","id='edit-default'");
	}else
	{
		echo ($item['Default']!='')?$item['Default']:"&nbsp;";
	}?></td>
	<td><?if($_GET['editfield']==$item['Field']){?>
		<a href="" onclick="loadXMLDoc('/ajax-index.php?page=dbmanage&isadm=1&comid=<?=$comid?>','dbdiv','editform');return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/save.png" border='0' align='absmiddle'></a>
	<?}else{?>
		<a href="" onclick="loadXMLDoc('/ajax-index.php?page=dbmanage&isadm=1&comid=<?=$comid?>&editfield=<?=$item['Field']?>','dbdiv');return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/edit10px.png" border='0' align='absmiddle'></a>
	<?}?>
	</td>
	<td><a href="" onclick="realdel = confirm('<?=$this->core_echomui('admc_tdmanage_delconf')?>');if(realdel){loadXMLDoc('/ajax-index.php?page=dbmanage&isadm=1&comid=<?=$comid?>&delfield=<?=$item['Field']?>','dbdiv');}return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/del.png" border='0' align='absmiddle'></a></td>
</tr><?}?>
<tr><td colspan="5" style="font-weight:bold;text-align:left;"><?=$this->core_echomui('admc_tdmanage_addtitle')?>:</td></tr>
<tr>
	<td><?=$this->adm_show_input("addfield[field]", $_POST['addfield']['field'],"","width:100px;height:18px","id='add-field'")?></td>
	<td>
		<?=$this->adm_show_select("addfield[type]", $_POST['addfield']['type'], $alltypes, "", "id='add-type'","")?>
		(<?=$this->adm_show_input("addfield[val]",$_POST['addfield']['val'],"","width:100px;height:18px","id='add-val'")?>)
	</td>
	<td><?=$this->adm_show_input("addfield[default]",$_POST['addfield']['default'],"","width:100px;height:18px","id='add-default'")?></td>
	<td colspan="2"><?=$this->core_echomui('admc_addafter')?>: <?=$this->adm_show_select("addfield[after]", ($_POST['addfield']['after']?$_POST['addfield']['after']:$lastafteritem), $afterfield, "", "id='add-after'","")?> <a href="" onclick="loadXMLDoc('/ajax-index.php?page=dbmanage&isadm=1&comid=<?=$comid?>','dbdiv','editform');return false;"><img src="http://<?=HTTP_HOST?>/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/save.png" border='0' align='absmiddle'></a></td>
</tr>
</table>
<?}?>
<!--<a href="" onclick="loadXMLDoc('/ajax-index.php?page=dbmanage&isadm=1&comid=<?=$comid?>','dbdiv');return false;">reload</a>-->