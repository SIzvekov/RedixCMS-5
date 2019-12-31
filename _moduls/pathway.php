<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/

$sql = "SELECT `title`,`pathway` FROM `#__sitemap` WHERE `include_in_pathway`='1' && `url`='".addslashes($this->config['home_url'])."'";
$row = $this->fetch_assoc($this->query($sql));
if(sizeof($row)&&is_array($row))
{
	$mainpage = array('url'=>'/'.$this->config['home_url'].'/', 'text'=>($row['pathway']?$row['pathway']:$row['title']));
	array_unshift($this->pathway, $mainpage);
}

include($this->core_get_modtplname());
?>