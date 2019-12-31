<div class='headspace'></div><?php //v.1.0.

/* RedixCMS 4.0*/

$this->adm_showparttitle("Группы пользователей / {edittext}"); // {edittext} автоматически заменяется на "редактировать" или "добавить" в зависимости от действия

//echo "<pre>";
//print_r($this->config['tpl']);

// получаем запись, если есть id
if($this->id)
{
	$sql = "SELECT * FROM `#h_users_groups` WHERE `id`=".$this->id;
	$res = $this->query($sql);
	$row = $this->fetch_assoc($res);
}
// сохраняем информацию
if($_POST['save'] || $_POST['app'])
{
	$fields = array(
	"name" => "'".addslashes($_POST['name'])."'",
	"isadmin" => "'".intval($_POST['isadmin'])."'",
	"adm_index_page" => "'".addslashes($_POST['adm_index_page'])."'",
	);
	
	$sql = $this->adm_get_edit_sql("#h_users_groups",$fields);
	$res = $this->query($sql);
	
	if(!$this->id){$this->id = $this->insert_id();$this->adm_add_sys_mes("запись добалена","ok");}
	else{$this->adm_add_sys_mes("запись отредактрована","ok");}
	
	if($_POST['save']) $link = '/'.$this->adm_path.'/'.$this->pre_way.'/';
	elseif($_POST['app']) $link = '/'.$this->way_url_get.'/?id='.$this->id;
	
	$this->reload($link);
}

$this->adm_show_sys_mes();

$strings = array(
	array("title" => "Название", "input" => $this->adm_show_input("name",$row['name'],"","width:100%;"), "req" => "name"),
	array("title" => "Администраторы", "input" => $this->adm_show_input("isadmin", "1", $row['isadmin'],"","","checkbox"),"tooltip"=>"Поставьте галочку, чтобы члены этой группы имели доступ в систему администрирования сайта"),
	array("title" => "Главный url", "input" => $this->adm_show_input("adm_index_page",$row['adm_index_page'],"","width:100%;"), "tooltip" => "url индексной страницы администратора при входе в систему администрирования сайта"),
);


if($this->id)
{
// получаем дерево меню админки
$tpl = "{space}{text}<br>";

$infa = $this->core_get_tree("SELECT * FROM `#h_adm_menu` WHERE `mid`=".$this->id." ORDER BY `sort` ASC");
$tree = $this->core_get_tree_keys(0,array(),$infa, 0, 1);

// формируем массив для выпадающий список для выбора родителя
$acc_cats[0] = "- корень -";
foreach($tree as $tree1)
{
	$acc_cats[$tree1['id']] = str_repeat("&nbsp;&nbsp;",$tree1['this_space']).$tree1['text'];
}


$menu_tree = "<div id='divlinks'>";
$k=0;
$all = sizeof($tree);
foreach($tree as $item)
{
	
	/* стрелки сортировки
	($k!=1?"<a href='#' onclick=\"savesort('".$item['id']."','up');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/arr_up.png\" border='0' align='absmiddle'></a>":"").($k!=$all?"<a href='#' onclick=\"savesort('".$item['id']."','down');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/arr_down.png\" border='0' align='absmiddle'></a>":"").
	*/

	$menu_tree .= "<div id='thisdivlink-".$item['id']."'>";
	
	$k++;
	$item['link_normal'] = $item['link'];
	$item['link'] = str_replace("{adm_path}",$this->adm_path,$item['link']);
	$menu_tree .= "<div id='linkspan-".$item['id']."'>".str_repeat("&nbsp;&nbsp;",$item['this_space'])."<span id='linksort-".$item['id']."'>".$item['sort']."</span>. <span id='linktext-".$item['id']."'> ".(!$item['link']?"<span style=\"font-weight: bold;\">":"").$item['text']."</span></span> <font color='#cfcfcf'><span id='linklink-".$item['id']."'>".$item['link']."</span><a href='#' onclick=\"editlink('".$item['id']."',1);return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/edit10px.png\" border='0' align='absmiddle'></a>
<a href='#' onclick=\"dellink('".$item['id']."');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/del.png\" border='0' align='absmiddle'></a>
</font></div>";
	$menu_tree .= "<div id='editspan-".$item['id']."' style='display:none;'>".$this->adm_show_input("linksort[".$item['id']."]",$item['sort'],"","width:25px;height:18px"," maxlength=3 id='sort-".$item['id']."'").". ".$this->adm_show_input("linktext[".$item['id']."]",$item['text'],"","width:150px;height:18px"," id='text-".$item['id']."'")."&nbsp;".$this->adm_show_input("linklink[".$item['id']."]",$item['link_normal'],"","width:150px;height:18px"," id='link-".$item['id']."'").$this->adm_show_select("linkparent[".$item['id']."]", $item['pid'], $acc_cats, "", "","")." <a href='#' onclick=\"savelink('".$item['id']."');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>
<a href='#' onclick=\"editlink('".$item['id']."',0);return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/cancel.png\" border='0' align='absmiddle'></a>
<a href='#' onclick=\"dellink('".$item['id']."');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/del.png\" border='0' align='absmiddle'></a>
</div>";

$menu_tree .= "</div>";
}
$menu_tree .= "</div><br><span style=\"font-weight: bold;\">Добавить пункт:</span><br><font style='color:#cfcfcf;font-size:9px;'>Сорт.&nbsp;
Текст&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Ссылка&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Родитель</font><br>";
$menu_tree .= $this->adm_show_input("addlinksort","","","width:25px;height:18px"," maxlength=3 id='sort-add'").". ".$this->adm_show_input("addlinktext","","","width:150px;height:18px"," id='text-add'")."&nbsp;".$this->adm_show_input("addlinklink","/{adm_path}/","","width:150px;height:18px"," id='link-add'")." ".
$this->adm_show_select("addparent", "", $acc_cats, "", "","")
." <a href='#' onclick=\"addlink();return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>";

$adm_menu = array(array("input"=>"
".$menu_tree."
"));

// #######################################################################

$adm_rights_tree = "<table class='f_table' style='width:400px;'><tr><th>Страница<th>Смотреть<th>Редактировать<th>Добавлять<th>Удалять<th>Удалить";

// получаем дерево
$tpl = "{space}{way}<br>";

$infa = $this->core_get_tree("SELECT * FROM `#h_users_rights` WHERE `gid`=".$this->id);
$tree = $this->core_get_tree_keys(0,array(),$infa, 0, 1);

// формируем массив для выпадающий список для выбора куда добавить параметр
$acc_ways = array();
foreach($tree as $tree1)
{
	if($tree1['pid']) continue;
	$acc_ways[$tree1['id']] = $tree1['way'];
}


$f_arr = array("view","edit","add","delete");

foreach($tree as $item)
{
	if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";
	$adm_rights_tree .= "<tr class='".$zebra_class."' ".($this->classes_switch($zebra_class,'f_hover'))."><td style='text-align:left;'>";
	$adm_rights_tree .= str_repeat("&nbsp;&nbsp;",$item['this_space']);
	$adm_rights_tree .= $item['way'];
	
	foreach($f_arr as $f)
	{
		$adm_rights_tree .= "<td><a href=\"/".$this->adm_path."/".$this->way."/?".$f."=".$item['id']."\" onclick=\"loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=".$item['id']."&dbtable=h_users_rights&field=".$f."');return false;\"><img src=\"/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/ticks/tick_".$item[$f].".png\" border=\"0\" id=\"swh_users_rights".$f."-".$item['id']."\"></a>";
	}
	
	$adm_rights_tree .= "<td><a href='#' onclick=\"delfromrights('".$item['id']."');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/del.png\" border='0' align='absmiddle'></a>";
}
$adm_rights_tree .= "</table><br><span style=\"font-weight: bold;\">Добавить:</span><br>";

$adm_rights_tree .= "<div id='addrway' style='display:block'><font style='color:#cfcfcf;font-size:9px;'><span style=\"font-weight: bold;\">Путь</span> | <a href='#' style='color:#cfcfcf' onclick=\"ppswitch();return false;\">Параметр</a></font><br>";
$adm_rights_tree .= $this->adm_show_input("addrightway","","","width:250px;height:18px");
$adm_rights_tree .= " <a href='#' onclick=\"addfromrights('0');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>";
$adm_rights_tree .= "</div>";


$adm_rights_tree .= "<div id='addrpar' style='display:none'><font style='color:#cfcfcf;font-size:9px;'><a href='#' style='color:#cfcfcf' onclick=\"ppswitch();return false;\">Путь</a> | <span style=\"font-weight: bold;\">Параметр</span></font><br>";
$adm_rights_tree .= $this->adm_show_input("addrightpar","","","width:100px;height:18px")."&nbsp;";
$adm_rights_tree .= $this->adm_show_select("addparto", "", $acc_ways);
$adm_rights_tree .= " <a href='#' onclick=\"addfromrights('1');return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>";
$adm_rights_tree .= "</div>";

$adm_rights = array(array("input"=>"
".$adm_rights_tree."
"));


}
$hiddenstring = array();

?>
<?echo $this->adm_open_edit_form();?>
<?echo $this->adm_init_bookmarks();?>
<?echo $this->adm_show_edit_content("Содержание", $strings)?>
<?if($this->id) echo $this->adm_show_edit_content("Меню админки", $adm_menu)?>
<?if($this->id) echo $this->adm_show_edit_content("Права доступа", $adm_rights)?>
<?echo $this->adm_close_bookmarks();?>
<?
$save = array("value"=>"сохранить","class"=>"savebutton","alter_class"=>"savebutton_h");
$app = array("value"=>"применить","class"=>"appbutton","alter_class"=>"appbutton_h");
$cancel = array("value"=>"отмена","class"=>"cancelbutton","alter_class"=>"cancelbutton_h");
echo $this->adm_show_edit_toolbar($save, $app, $cancel);
?>
<?echo $this->adm_close_edit_form($hiddenstring);?>
<script language="JavaScript">
function editlink(itid, type)
{
	if(!itid || !document.getElementById('linkspan-'+itid)) return false;
	
	if(type==1)
	{
		document.getElementById('linkspan-'+itid).style.display='none';
		document.getElementById('editspan-'+itid).style.display='block';
	}else{
		document.getElementById('linkspan-'+itid).style.display='block';
		document.getElementById('editspan-'+itid).style.display='none';
	}
}

function dellink(delid)
{
	if(!delid) return false;
	document.getElementById('linkspan-'+delid).style.display='none';
	document.getElementById('editspan-'+delid).style.display='none';
	loadXMLDoc('/ajax-index.php?isadm=1&page=users_groups_save_menu&act=del&id='+delid);
}

function savesort(id, goto)
{
	if(!id) return false;
}

function savelink(id)
{
	if(!id) return false;
	loadXMLDoc('/ajax-index.php?isadm=1&page=users_groups_save_menu&act=save&id='+id,'','editform');

	lnk = document.getElementById('link-'+id).value;
	document.getElementById('linklink-'+id).innerHTML = lnk;
	document.getElementById('linksort-'+id).innerHTML = document.getElementById('sort-'+id).value;
	if(lnk) document.getElementById('linktext-'+id).innerHTML = document.getElementById('text-'+id).value;
	else  document.getElementById('linktext-'+id).innerHTML = '<span style="font-weight: bold;">'+document.getElementById('text-'+id).value+'</span>';
	
	document.getElementById('linkspan-'+id).style.display='block';
	document.getElementById('editspan-'+id).style.display='none';
}

function addlink()
{
	loadXMLDoc('/ajax-index.php?isadm=1&page=users_groups_save_menu&act=add&gid=<?=$this->id?>','divlinks','editform');
}

function addfromrights(aspar)
{
	loadXMLDoc('/ajax-index.php?isadm=1&page=users_rights_save&act=add&gid=<?=$this->id?>&aspar='+aspar,'','editform');
}

function delfromrights(id)
{
	loadXMLDoc('/ajax-index.php?isadm=1&page=users_rights_save&act=del&id='+id);
}

function ppswitch()
{
	if(document.getElementById('addrway').style.display=='block')
	{
		document.getElementById('addrway').style.display='none';
		document.getElementById('addrpar').style.display='block';
	}else
	{
		document.getElementById('addrway').style.display='block';
		document.getElementById('addrpar').style.display='none';
	}
}
</script>