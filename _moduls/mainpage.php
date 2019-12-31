<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/
$sql = "SELECT * FROM `#__mppictures` WHERE `public`='1' ORDER BY `sort` ASC";
$array = $this->get_db_array($sql);

include($this->core_get_modtplname());
?>