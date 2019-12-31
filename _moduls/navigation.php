<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/

if(is_array($data))
{
	$tpl = $data[0];
	$navig_code = intval($data[1]);
}else 
{
	$tpl = $data;
	$navig_code = 0;
}

include($this->core_get_modtplname($tpl.".php"));
?>