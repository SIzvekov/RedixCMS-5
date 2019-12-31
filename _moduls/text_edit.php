<?php //v.1.0.
/* RedixCMS 4.0
Файл модуля.
Состоит из двух частей:
1) получение нужных, недостающих данных
2) подключение шаблона модуля
*/

$sql = "SELECT * FROM `#__textmodulcontent` WHERE `code`='".addslashes($data)."' LIMIT 0,1";
$res = $this->query($sql);
if(!$this->num_rows($res)) $this->core_error[] = "Textmodul '".$data."' doesn't exist";
else{
$row = $this->fetch_assoc($res);
if(!$row['public']){$row['text'] = "&nbsp;";}

$uniqindex = rand(0,1000);
$text = "<script>function edittxtmodul".$uniqindex."(act){
if(act=='over')
{
	document.getElementById('txt".$uniqindex."').style.background='#ebf3fd';
	document.getElementById('editpic".$uniqindex."').style.display='block';
}
else if(act=='out')
{
	document.getElementById('txt".$uniqindex."').style.background='none';
	document.getElementById('editpic".$uniqindex."').style.display='none';
}else if(act=='click')
{
}
}</script>";
$text .= '<div style="padding:0px;margin:0px" id="txt'.$uniqindex.'" onclick="edittxtmodul'.$uniqindex.'(\'click\');" onmouseover="edittxtmodul'.$uniqindex.'(\'over\');" onmouseout="edittxtmodul'.$uniqindex.'(\'out\');"><a href="/'.$this->adm_path.'/'.$this->param.'/textmodul/edit/?id='.$row['id'].'&frontend=1" id="editpic'.$uniqindex.'" onclick="return hs.htmlExpand(this, { objectType: \'iframe\',headingText: \''.$row['name'].'\', contentId: \'highslide-html-8\' },\'\',\'1\' );" style="position:absolute;margin:-10px 0px 0px 0px;padding-right:10px;display:none;"><img src="/'.$this->adm_path.'/template/'.$this->config['adm_tpl'].'/img/edit10px.png" border="0"></a><div style="position:absolute;margin:-18px 0px 0px 15px;font-size:9px;font-family:verdana;background:#fff;padding:0px 3px;color:#868686;white-space:nowrap;z-index:0;">txt: '.$data.'</div>'.preg_replace("/\[cms\:param\]/iU", $this->param, $row['text']).'</div>';
$row['text'] = $text;

include($this->core_get_modtplname());
}
?>