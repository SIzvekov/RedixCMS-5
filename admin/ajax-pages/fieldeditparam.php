<?
$comid = intval($_GET['comid']);
$fieldid = intval($_GET['fieldid']);
$editparam = intval($_GET['ep']);
if(!$editparam) $editparam = '';

$sql = "SELECT * FROM `#h_components` WHERE `id`=".$comid;
$res = $this->query($sql);
$comp_row = $this->fetch_assoc($res);
$cur_params = $this->adm_get_param($comp_row['config']); // текущие параметры компонента

$sql = "SELECT `type`, `params`,`db_fname` FROM `#".($editparam?"h_components_listedittable":"h_components_listtable")."` WHERE `id`=".$fieldid." && `com_id`=".intval($comp_row['id']);
$res = $this->query($sql);
$row = $this->fetch_assoc($res);

if($editparam)
{
	$params = array();

switch($row['type'])
{
	case 'txt':
		$params['default'] = "";
		$params['style'] = "width:100%";
		$params['extra'] = "";
	break;
	case 'lid':
		$params['default'] = "";
		$params['style'] = "width:100%";
		$params['extra'] = "";
		$params['linkfname'] = "name";
	break;
	case 'checkbox':
		$params['style'] = "";
		$params['extra'] = "";
		$params['default_checked'] = "1";
	break;
	case 'password':
		$params['default'] = "";
		$params['style'] = "width:100%";
		$params['extra'] = "";
	break;
	case 'select':case 'js-select':
		$params['selectid'] = "";
		$params['valsbefor'] = "";
		$params['valsafter'] = "";
		$params['deffromfilter_way'] = "";
		$params['deffromfilter_code'] = "";
		$params['style'] = "";
		$params['extra'] = "";
	break;
	case 'link-select':
		$params['selectid'] = "3";
		$params['valsbefor'] = "";
		$params['valsafter'] = "";
		$params['deffromfilter_way'] = "";
		$params['deffromfilter_code'] = "";
		$params['style'] = "";
		$params['extra'] = "";
	break;
	case 'txtarea':
		$params['w'] = "100%";
		$params['h'] = "100";
		$params['class'] = "";
		$params['extra'] = "";
	break;
	case 'wys':
		$params['w'] = "100%";
		$params['h'] = "300";
		$params['class'] = "";
		$params['extra'] = "";
		$params['wys_type'] = "";
		$params['skin'] = "";
	break;
	case 'date':
		$params['format'] = "d.m.Y";
		$params['default'] = "1";
		$params['style'] = "";
		$params['extra'] = "";
	break;
	case 'file':
		$params['type'] = "img";
		$params['fpath1'] = "/images/photos/small/";
		$params['save2name'] = "_rand_";
		$params['ext'] = "png,jpeg,jpg,gif";
		$params['replace'] = "1";
		$params['resize'] = "1";
		$params['w1'] = "0";
		$params['h1'] = "0";
		$params['newext'] = "";
		$params['islink'] = "1";
		$params['style'] = "";
		$params['extra'] = "";
		$params['cond_resize1'] = "1";
	break;
	case 'hid':
		$params['default'] = "";
	break;
	case 'chuser':
		$params['accgids'] = "";
		$params['acctype'] = "1";
		$params['showfields'] = "";
	break;
	case 'mailfromsite':
		$params['userfname'] = "";
	break;	
	case 'fs_directory':
		$params['fm_dir'] = "images";
	break;
	case 'foto_list':
//		$params['dir_field_here'] = "";
//		$params['dir_field_there'] = "";
//		$params['img_field_there'] = "";
		$params['com_id'] = "";
		$params['fpath1'] = "/images/photos/";
		$params['save2name'] = "_rand_";
		$params['ext'] = "jpg,gif,png,jpeg,bmp";
	break;
	
}
}else
{
	$params = array(
	"linkto"=>"/{adm_path}//content/?cid={thisid}",
	"style"=>"text-align:left;",
	"spacer"=>"|-&nbsp;",
	"shownumber"=>"cid|__",
	);

switch($row['type'])
{
	case 'datetime':
		$params['format'] = "d.m.Y";
	break;
	
	case 'del':
		$params['noactid'] = "";
		$params['dt'] = "";
	break;
	
	case 'edit':
		$params['noactid'] = "";
	break;
	
	case 'linked':
		$params['tbl'] = "__";
		$params['linkfield'] = "id";
		$params['showfield'] = "name";
		$params['defval'] = "-";
	break;
	
	case 'img':
		$params['fpath1'] = "";
		$params['fpath2'] = "";
		$params['w'] = "";
		$params['h'] = "";
	break;
	case 'fromselect':
		$params['selectid'] = "";
	break;
}
}
?>
field: <strong><?=$row['db_fname']?></strong>; type: <strong><?=$row['type']?></strong><br>
<b><?=$this->core_echomui('admc_js_txt1');?></b>:<br>
<?=$this->adm_show_hidden("listtableparams[fieldid]",$fieldid);?>
<?=$this->adm_show_editor("listtableparams[param]", $row['params'], "listtableparams", "150","100%")?>
<br><b><?=$this->core_echomui('admc_js_txt2');?></b>:<br>
<?foreach($params as $par=>$defval){?><div onmouseover="this.style.background='#cfcfcf'" onmouseout="this.style.background='none'"><a href="" onclick="setparam('<?=$par?>','<?=$defval?>','listtableparams');return false;"><?=$par?></a> - <?=str_replace('"','\"',$this->core_echomui('admc_confparams_tooltip_'.$par));?></div><?}?>
<br>
<div align="center">
	<a href="" onclick="loadXMLDoc('/ajax-index.php?page=<?echo $editparam?"fieditmanage":"filistmanage"?>&isadm=1&comid=<?=$comid?>','<?echo $editparam?"fieldeditdiv":"fieldlistdiv"?>','editform');document.getElementById('fieldparamdiv<?=$editparam?>').style.display='none';return false;">Сохранить</a>
	&nbsp;&nbsp;&nbsp;
	<a href="" onclick="document.getElementById('fieldparamdiv<?=$editparam?>').innerHTML='';document.getElementById('fieldparamdiv<?=$editparam?>').style.display='none';return false;">Отмена</a>
</div>