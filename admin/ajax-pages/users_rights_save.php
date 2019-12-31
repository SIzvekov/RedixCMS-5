<?php
$id = intval($_GET['id']);
if($id)
{

	if($_GET['act']=='del')
	{
		$need_reload = 1;

		$sql = "DELETE FROM `#h_users_rights` WHERE `id`=".intval($id)." || `pid`=".intval($id);
		$res = $this->query($sql);
	}
}
if($_GET['act']=='add' && intval($_GET['gid']))
{
	$need_reload = 1;
	if(intval($_GET['aspar']))
		$sql = "INSERT INTO `#h_users_rights` SET `way`='".addslashes($_POST['addrightpar'])."', `gid`=".intval($_GET['gid']).", `pid`=".intval($_POST['addparto']);
	else
		$sql = "INSERT INTO `#h_users_rights` SET `way`='".addslashes($_POST['addrightway'])."', `gid`=".intval($_GET['gid']);
	$res = $this->query($sql);
}

$_RESULT = array("alert"=>$alert);// - формируем массив с именем и значением для вывода в JS функции, которая работает так: req.responseJS.имя =
if($need_reload)
{
	$_RESULT['reload'] = 1;
}
?>