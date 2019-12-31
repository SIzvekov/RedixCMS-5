<?
$cid = intval($_GET['cid']);
if(!$_GET['pmedit']) $selname = 'pi-parpunktmenu';
else $selname = 'pid';

if($cid) $dop = " && `id`!=".$cid." ";
$sql = "SELECT * FROM `#__menupunkti` WHERE `mid`=".intval($_GET['mid']).$dop." ORDER BY `sort` ASC ,`name` ASC";
$infa = $this->core_get_tree($sql);
$infa = $this->core_get_tree_keys(0, array('id','pid','name'), $infa, 0, 0);

$selar = array("0"=>"--".$this->core_echomui('root')."--");
foreach($infa as $item)
{
	$selar[$item['id']] = str_repeat("&nbsp;&nbsp;&nbsp;",$item['this_space']).$item['name'];
}
echo $this->adm_show_select($selname, $_GET['ppm'], $selar, "", "","");
?>