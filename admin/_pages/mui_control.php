<?
$this->adm_get_com_config(); // <- ïîëó÷èëè êîíôèã êîìïîíåíòà èç òàáëèöû #__components
$this->adm_showparttitle($this->core_echomui('mui_control:admc_title'));

$_par_id = addslashes($_SESSION['adm_filter'][$this->ses_key]['par_id']);
$filter_group = addslashes($_SESSION['adm_filter'][$this->ses_key]['group']);
$isadm = addslashes($_SESSION['adm_filter'][$this->ses_key]['isadm']);

//				ÔÓÍÊÖÈÈ ÑÌÅÍÛ ÔÈËÜÒÐÀ È ×ÈÑËÀ ÇÀÏÈÑÅÉ ÍÀ ÑÒÐÀÍÈÖÓ
$filterparam = array();
$filterparam[] = array("name"=>"par_id","val"=>$_GET['par_id']);
$filterparam[] = array("name"=>"group","val"=>$_GET['group']);
$filterparam[] = array("name"=>"isadm","val"=>$_GET['isadm']);
$this->adm_set_filter_params($filterparam);

//	0) âûâîä ñèñòåìíûõ ñîîáùåíèé
$this->adm_show_sys_mes();

//echo '<pre>';print_r($this);echo '</pre>';
//echo $_par_id;

echo '<div class="chooselan">'.$this->core_echomui('mui_control:chooselan').':</div>';
foreach($this->params_list as $item){
	if(!$_par_id && $item['default']) $_par_id = $item['par'];

	if($item['par']==$_par_id) echo '<div class="muicontr_params">'.$item['par_name'].'</div>';
	else echo '<a href="?par_id='.$item['par'].'" class="muicontr_params">'.$item['par_name'].'</a>';
}

if($_par_id)
{
	$sql = "SELECT DISTINCT(`group`) FROM `#h_mui` WHERE `param`='".$_par_id."' && `adm`='".intval($isadm)."'";
	$res = $this->query($sql);
	$allgroups = array();
	while($row = $this->fetch_assoc($res))
	{
		$allgroups[$row['group']] = $row['group']?$row['group']:$this->core_echomui('mui_control:nogroup');
	}
	asort($allgroups);
	echo $this->adm_showfiltr($this->core_echomui('mui_control:selectpart'), "isadm",$_SESSION['adm_filter'][$this->ses_key]['isadm'], array("0"=>$this->core_echomui('mui_control:isadm0'),"1"=>$this->core_echomui('mui_control:isadm1')));

	echo $this->adm_showfiltr($this->core_echomui('mui_control:selectgroup'), "group",$_SESSION['adm_filter'][$this->ses_key]['group'], array_merge(array("-allgroups-"=>$this->core_echomui('mui_control:allgroups')),$allgroups));

	$allgroups['_self'] = $this->core_echomui('mui_control:group_self');

	if($filter_group!='-allgroups-' && (in_array($filter_group,$allgroups) || !$filter_group)) $dopcond = "`group`='".$filter_group."'";
	else $dopcond = "1";


	$sql = "SELECT * FROM `#h_mui` WHERE `param`='".$_par_id."' && ".$dopcond." && `adm`='".intval($isadm)."'";
	$res = $this->query($sql);
	echo '<div class="clear"></div><form name="form" id="form" method="post" enctype="multipart/form-data"><div class="muis">';
	$i=1;

	echo $_GET['plaintext']?'<pre>':'';
	while($row = $this->fetch_assoc($res))
	{
		echo !$_GET['plaintext']?"<div class='item".($row['mui_text']=='no_mui:'.$row['mui_code']?" nomui":"")."'><div class='num'>".($i++).". </div><div class='code'>".$row['mui_code']."&nbsp;=&nbsp;</div><div class='text'>".$this->adm_show_editinput('mui_text', $row['mui_text'], $row['id'], "width:auto;height:22px;width:300px;font-size:14px;","h_mui", "form","",1)."</div><div class='group'><span class='gn'>".$this->core_echomui('mui_control:group').":</span> ".$this->adm_show_editselect("group", $row['group'], $row['id'], $allgroups, "width:100px;height:18px;","h_mui", "form","","",0,"")."</div></div>":htmlspecialchars($row['mui_text'])." === \n";
	}
	echo $_GET['plaintext']?'</pre>':'';
	echo '</div></form>';
}

?>
<style>
	a.muicontr_params{float:left;padding:10px;margin:5px;border:1px dotted #ccc;background:#fcfcfc;color:#000;text-decoration:none;}
	a.muicontr_params:hover{float:left;padding:10px;margin:5px;border:1px solid #ccc;background:#e7e7e7;cursor:pointer;color:#000;text-decoration:none;}
	div.muicontr_params{float:left;padding:10px;margin:5px;border:1px solid #ccc;cursor:default;background:#ebffeb;color:#000;text-decoration:none;font-weight:bold;}
	div.chooselan{float:left;padding:10px;margin:6px;border:0px;cursor:default;color:#000;text-decoration:none;font-weight:bold;font-size:14px;}
	div.muis{padding:10px;}
	div.muis div.item{padding:5px;background:#fff;overflow:hidden;}
	div.muis div.item:hover{padding:5px;background:#f0eeee;overflow:hidden;}
	div.muis div.item div.code{font-size:12px;font-weight:bold;text-align:right;width:350px;float:left;}
	div.muis div.item div.text{font-size:14px;float:left;}
	div.muis div.item div.num{color:#cfcfcf;float:left;width:20px;}
	div.muis div.item div.group{float:left;margin-left:10px;}
	div.muis div.nomui{background: #ffebeb;}
	div.muis div.item span.gn{color:#cfcfcf;}
</style>