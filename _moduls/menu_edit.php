<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/

// получаем id этого меню

$sql = "SELECT * FROM `#__menulist` WHERE `code`='".addslashes($data[0])."' && `public`='1'";
$res = $this->query($sql);
$row['menu_info'] = $this->fetch_assoc($res);
$mid = intval($row['menu_info']['id']);

if($mid)
{
	// получаем пункты этого меню
	$sql = "SELECT * FROM `#__menupunkti` WHERE `mid`=".$mid." && `public`='1' ORDER BY `sort`, `name` ASC";
	$infa = $this->core_get_tree($sql);
	$row['menu_punkti'] = array();
	$row['menu_punkti'] = $this->core_get_tree_keys(0,array(),$infa, 0, 1);


$uniqindex = rand(0,1000);
echo "<script>function edittxtmodul".$uniqindex."(act){
if(act=='over')
{
	document.getElementById('txt".$uniqindex."').style.background='none';
	document.getElementById('editpic".$uniqindex."').style.display='block';
}
else if(act=='out')
{
	document.getElementById('txt".$uniqindex."').style.background='none';
	document.getElementById('editpic".$uniqindex."').style.display='none';
}else if(act=='click')
{
}
}</script>";
echo '<div style="padding:0px;margin:0px" id="txt'.$uniqindex.'" onclick="edittxtmodul'.$uniqindex.'(\'click\');" onmouseover="edittxtmodul'.$uniqindex.'(\'over\');" onmouseout="edittxtmodul'.$uniqindex.'(\'out\');"><a href="/'.$this->adm_path.'/'.$this->param.'/menulist/items/?mid='.$mid.'&frontend=1" id="editpic'.$uniqindex.'" onclick="return hs.htmlExpand(this, { objectType: \'iframe\',headingText: \''.$row['menu_info']['name'].'\', contentId: \'highslide-html-8\' },\'\',\'1\' );" style="position:absolute;margin:-10px 0px 0px 0px;padding-right:10px;display:none;"><img src="/'.$this->adm_path.'/template/'.$this->config['adm_tpl'].'/img/edit10px.png" border="0"></a>';
	include($this->core_get_modtplname("menu_".$data[1].".php"));
echo '<div style="clear:both"></div></div>';
}else echo "<font color='#f00'><span style=\"font-weight: bold;\">Ошибка:</span> меню не найдено.</font>";
?>