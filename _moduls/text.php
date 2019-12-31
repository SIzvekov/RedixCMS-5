<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/

$sql = "SELECT * FROM `#__textmodulcontent` WHERE `code`='".addslashes($data)."' && `public`='1' LIMIT 0,1";
$res = $this->query($sql);
if(!$this->num_rows($res)) $this->core_error[] = "Textmodul '".$data."' doesn't exist";
else{
$row = $this->fetch_assoc($res);
if($row['public']){
$row['text'] = preg_replace("/\[cms\:param\]/iU", $this->param, $row['text']);
include($this->core_get_modtplname());
}}
?>