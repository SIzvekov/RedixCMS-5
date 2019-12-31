<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/
$allows = array("cars", "services");

if(in_array($data,$allows))
{
	$row = $this->listofakcii($this->page_info['info']['id'],$data);

	if(sizeof($row))
	{
		include($this->core_get_modtplname());
	}
}
?>