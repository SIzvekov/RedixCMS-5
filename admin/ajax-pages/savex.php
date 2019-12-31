<?php

$table = "#".addslashes($_GET['dbtable']);
$field = addslashes($_GET['field']);
if($_GET['formfieldname']) $formfieldname = $_GET['formfieldname']; else $formfieldname = $field;

// ниже выполняем любые действия, какие хотим а результат вывода будет помещён в req.responseText.
$res = $this->adm_save_x($field, 1, "id", 1, $table, "", $_POST[$formfieldname]);

if($res)
{
	$_RESULT = array("alert"=>$this->core_echomui('adm_ajsaved'));// - формируем массив с именем и значением для вывода в JS функции, которая работает так: req.responseJS.имя = req.responseJS.значение
	if(intval($_GET['reload'])) $_RESULT["reload"]=1;
}
?>