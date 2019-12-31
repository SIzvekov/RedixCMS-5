<?php
$table = "#".addslashes($_GET['dbtable']);
// ниже выполняем любые действия, какие хотим а результат вывода будет помещён в req.responseText.
$switcharrowto = $this->adm_switch_row(intval($_GET['id']), $table, "",$_GET['field'],"0,1");

$switchtitleto = ($switcharrowto?$this->core_echomui('adm_switchno'):$this->core_echomui('adm_switchyes'));

$switcharrowto = "/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/ticks/tick_".($_GET['invers']?"invers_":"")."".$switcharrowto.".png";

if($_GET['id'])
{
	$_RESULT = array("switcharrowto"=>$switcharrowto, "switcharrow"=>"sw".$_GET['dbtable'].$_GET['field']."-".$_GET['id'],"switchtitleto"=>$switchtitleto);// - формируем массив с именем и значением для вывода в JS функции, которая работает так: req.responseJS.имя = req.responseJS.значение
}
?>