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

	include($this->core_get_modtplname("menu_".$data[1].".php"));
}else echo "<font color='#f00'><span style=\"font-weight: bold;\">Ошибка:</span> меню не найдено.</font>";
?>