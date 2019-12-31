<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/

$sql = "SELECT * FROM `#__akcii` WHERE `inleft`='1' ORDER BY RAND()";
$row = $this->get_db_array($sql);

$k=0;
$is = array();
while($row[$k] && !sizeof($is))
{
	$sql = "SELECT `url` FROM `#__sitemap` WHERE `com_id`=36 && `public`='1' && `record_id`=".intval($row[$k]['id']);
	$url = $this->fetch_assoc($this->query($sql));
	if($url['url']) 
	{
		$row[$k]['url'] = $url['url'];
		$is = $row[$k];
		if($_GET['dev']) 
		{
			echo '---<br/>';
			print_r($url);
			print_r($is);	
		}
	}
	$k++;
}

include($this->core_get_modtplname());
?>