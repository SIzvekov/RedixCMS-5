<?php
$cuid = intval($_GET['cuid']);
$acctype = intval($_GET['acctype']);
$accgid = split(",", $_GET['accgid']);
$fieldname = $_GET['fieldname'];
$get_showfields = $_GET['showfields'];
if($get_showfields) $showfields = split(",", $get_showfields);
else $showfields = array();

$extratharray = array();
$extratdarray = array();

foreach ($accgid as $k=>$v) $accgid[$k] = intval(trim($v));
foreach ($showfields as $k=>$v)
{
	$item = split("\|", $v);
	$extratharray[] = trim($item[1]);
	$extratdarray[] = trim($item[0]);
}

// формируем условие для выбора юзеров
$dopcond = "1";
if($acctype==1) $dopcond = "`group` IN (".join(",",$accgid).")";
else if($acctype==2) $dopcond = "`group` NOT IN (".join(",",$accgid).")";
// получаем список юзеров
$sql = "SELECT * FROM `#h_users` WHERE ".$dopcond;
$res = $this->query($sql);
?>
<table class='f_table'>
<th><?=$this->core_echomui('adm_ajlogin')?><th><?=$this->core_echomui('adm_ajfio')?>
<?	foreach($extratdarray as $v)
	{
		echo "<th>".$this->core_echomui('adm_aj'.$v);
	}?>
<th><?=$this->core_echomui('adm_ajset')?>
<?
while($row = $this->fetch_assoc($res))
{
	$username = array($row['family'], $row['name'], $row['otchestvo']);
	$username = join(" ", $username);

	if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";
	echo "<tr class='".$zebra_class."' ".($this->classes_switch($zebra_class,'f_hover')).">";

	echo "<td><a href='/".$this->adm_path."/users_groups/users/edit/?id=".$row['id']."' style='color:#000' target=_blank>".$row['login']."</a>";
	echo "<td><a href='/".$this->adm_path."/users_groups/users/edit/?id=".$row['id']."' style='color:#000' target=_blank>".$username."</a>";
	foreach($extratdarray as $v)
	{
		if(!isset($row[$v])) continue;
		echo "<td>".$row[$v];
	}
	
	
	echo "<td><a href='#' onclick=\"document.getElementById('hid-".$fieldname."').value='".$row['id']."';document.getElementById('cuserfield').innerHTML='<a href=\'/".$this->adm_path."/users_groups/users/edit/?id=".$row['id']."\' target=_blank>".str_replace('"',"&quot;",$username)." [".$this->core_echomui('adm_ajlogin').": ".$row['login']."]</a>';document.getElementById('chuserfield').innerHTML='<font color=\'008747\'>".$this->core_echomui('adm_ajsetusernotify')."';return false;\">".$this->core_echomui('adm_ajset')."</a>";
	?>
<?}?>
</table>
<a href="#" onclick="document.getElementById('chuserfield').innerHTML='';return false;"><span style='color:#a00;font-weight:bold;'>x</span>&nbsp;<?=$this->core_echomui('adm_ajcloseuserlist')?></a>
<?
$_RESULT = Array();// - формируем массив с именем и значением для вывода в JS функции, которая работает так: req.responseJS.имя = req.responseJS.значение
?>