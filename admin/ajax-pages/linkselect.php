<?
$sid = intval($_GET['sid']);
$cval = $_GET['cval'];
$lsid = intval($_GET['lsid']);

$values = $this->adm_get_select_array($sid, $cval);
$values_plain = array_values($values);
$keys = array_keys($values);

?>
<div class="lsbookmarks<?echo in_array($cval,$keys)?"":"_sel"?>" id="lsbooself-<?=$lsid?>" onclick="this.className='lsbookmarks_sel';document.getElementById('lsboolist-<?=$lsid?>').className='lsbookmarks';document.getElementById('chooseself-<?=$lsid?>').style.display='block';document.getElementById('choosefromlist-<?=$lsid?>').style.display='none';"><?=$this->core_echomui('ls_sel_self')?></div>

<div class="lsbookmarks<?echo in_array($cval,$keys)?"_sel":""?>" id="lsboolist-<?=$lsid?>" onclick="this.className='lsbookmarks_sel';document.getElementById('lsbooself-<?=$lsid?>').className='lsbookmarks';document.getElementById('chooseself-<?=$lsid?>').style.display='none';document.getElementById('choosefromlist-<?=$lsid?>').style.display='block';"><?=$this->core_echomui('ls_sel_fromlist')?></div>

<div style="clear:both"></div>

<div id="chooseself-<?=$lsid?>" style="display:<?echo in_array($cval,$keys)?"none":"block"?>">
<input id="selfinp-<?=$lsid?>" value="<?echo in_array($cval,$keys)?"":$cval?>"/><input type="button" onclick="inpval=document.getElementById('selfinp-<?=$lsid?>').value;document.getElementById('lsfield-<?=$lsid?>').value=inpval;document.getElementById('lstxt-<?=$lsid?>').innerHTML=inpval;document.getElementById('linksel-<?=$lsid?>').style.display='none';" value="ok">
</div>

<div id="choosefromlist-<?=$lsid?>" style="display:<?echo in_array($cval,$keys)?"block":"none"?>;max-height:300px;overflow:auto;">
<?
$was_space = 0;
$k=0;
$next_block_id = '';
//echo '<pre>';print_r($values);echo '</pre>';
foreach($values as $key=>$val){
	if($key)
	{
		$sql = "SELECT `public` FROM `#__sitemap` WHERE `url`='".addslashes($key)."' LIMIT 0,1";
		$is_public = $this->fetch_assoc($this->query($sql));
		if($is_public['public']) $link2page = ($this->config['use_param']?'/'.$this->param:'').'/'.$key.'/'; else $link2page = '';
	}
	$space_next = substr_count($values_plain[$k+1],"--");
	$space = substr_count($val,"--");
	$val = str_replace("--","",$val);
	if($was_space>$space) echo str_repeat("</div>",$was_space-$space);
	if($was_space<$space) echo '<div style="display:none;" id="'.$next_block_id.'">';

	echo '<div class="linksel_normal" onmouseover="this.className=\'linksel_hover\'" onmouseout="this.className=\'linksel_normal\'">';

	if($space_next>$space)
	{
		$next_block_id = substr(md5(microtime()),rand(0,31),5);
		echo '<img src="/'.$this->adm_path.'/template/default/img/treeview/plus.gif" style="float:left;margin-left:'.($space*10).'px;margin-right:2px;margin-top:3px;cursor:pointer;" onclick="if(document.getElementById(\''.$next_block_id.'\').style.display==\'none\'){document.getElementById(\''.$next_block_id.'\').style.display=\'block\';this.src=\'/'.$this->adm_path.'/template/default/img/treeview/minus.gif\';}else{document.getElementById(\''.$next_block_id.'\').style.display=\'none\';this.src=\'/'.$this->adm_path.'/template/default/img/treeview/plus.gif\';}return false;"/>';
	}else
	{
		echo '<img src="/templates/blank.gif" style="float:left;margin-right:2px;margin-left:'.($space*10).'px;margin-top:3px;width:9px;height:9px;"/>';
	}

	if($key==$cval) echo "<b>".$val."</b>";
	else echo '<a href="" onclick="document.getElementById(\'lsfield-'.$lsid.'\').value=\''.$key.'\';document.getElementById(\'lstxt-'.$lsid.'\').innerHTML=\''.str_replace('"','&quot;',$val).'\';document.getElementById(\'linksel-'.$lsid.'\').style.display=\'none\';return false;">'.$val.'</a>';
	echo $link2page?'&nbsp;<a href="'.$link2page.'" target="_blank"><img src="/'.$this->adm_path.'/template/default/img/nw.png" width="9" height="9" /></a>':'';
	echo '</div>';


	$was_space = $space;
	$k++;
}?></div>