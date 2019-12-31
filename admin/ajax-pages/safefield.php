<?
$id = intval($_GET['id']);
$dbtable = addslashes($_GET['dbtable']);
$field = addslashes($_GET['field']);
$v = addslashes($_POST[$_GET['in']]);
$selectid = intval($_GET['selectid']);

if($v=='_self')
{
	$in = str_replace("-input-","-input_self-",$_GET['in']);
	$v = addslashes($_POST[$in]);
}

if($id && $dbtable && $field)
{
	$sql = "UPDATE `#".$dbtable."` SET `".$field."`='".$v."' WHERE `id`=".$id;
	$res = $this->query($sql);
}
if($selectid)
{
	$values = $this->adm_get_select_array($selectid);
}
echo isset($values[$v])?$values[$v]:$v;
if($res)
{
//	$_RESULT = array("alert"=>$this->core_echomui('adm_ajsaved'));
}
?>