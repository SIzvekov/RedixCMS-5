<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/
$allows = array("cars", "services");
$tables = array("cars"=>"avtopark", "services"=>"services");
$com_ids = array("cars"=>30,"services"=>33);
if(in_array($data,$allows))
{
	$ids = split(";",$this->page_info['info'][$data]);
	//echo '<pre>';print_r($ids);echo '</pre>';
	if(sizeof($ids))
	{
		$sql = "SELECT * FROM `#__".$tables[$data]."` WHERE `id` IN (".join(",",$ids).")";
		$row = $this->get_db_array($sql);
		foreach($row as $k=>$item)
		{
			$sql = "SELECT `url` FROM `#__sitemap` WHERE `com_id`=".$com_ids[$data]." && `record_id`=".$row[$k]['id']." && `public`='1'";
			$url = $this->fetch_assoc($this->query($sql));
			if(!$url) unset($row[$k]);
			else $row[$k]['url'] = $url['url'];
		}

		if(sizeof($row)) include($this->core_get_modtplname("assoc2akcii_".$data.".php"));
	}
}

?>