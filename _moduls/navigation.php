<?php //v.1.0.
/* RedixCMS 4.0
���� ������.
������� �� ���� ������:
1) ��������� ������, ����������� ������
2) ����������� ������� ������
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