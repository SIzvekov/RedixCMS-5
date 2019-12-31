<?php
$id = intval($_GET['id']);
if($id)
{

	if($_GET['act']=='save')
	{
		$oldsort = $this->fetch_assoc($this->query("SELECT `sort` FROM `#h_adm_menu` WHERE `id`=".$id));
		$need_reload = ($oldsort['sort']!=intval($_POST['linksort'][$id]));

		if($_POST['linkparent'][$id]==$id) $_POST['linkparent'][$id] = 0;

		$oldparent = $this->fetch_assoc($this->query("SELECT `pid` FROM `#h_adm_menu` WHERE `id`=".$id));
		$need_reload = ($oldparent['pid']!=intval($_POST['linkparent'][$id]));

		$sql = "UPDATE `#h_adm_menu` SET `pid`=".intval($_POST['linkparent'][$id]).", `text`='".addslashes($_POST['linktext'][$id])."', `link`='".addslashes($_POST['linklink'][$id])."', `sort`=".intval($_POST['linksort'][$id])." WHERE id=".$id;
		$res = $this->query($sql);
	}elseif($_GET['act']=='del')
	{
		// проверяем, есть ли записи, находящиеся под этой
		$sql = "SELECT COUNT(*) as `count` FROM `#h_adm_menu` WHERE `pid`=".$id;
		$colsub = $this->fetch_assoc($this->query($sql));
		if($colsub['count'])
		{
			$need_reload = 1;

			// получаем пид удаляемой записи
			$sql = "SELECT `pid` FROM `#h_adm_menu` WHERE `id`=".$id;
			$c_pid = $this->fetch_assoc($this->query($sql));

			// Сдвигаем все записи, которые находятся под этой на 1 влево
			$sql = "UPDATE `#h_adm_menu` SET `pid`=".$c_pid['pid']." WHERE `pid`=".$id;
			$this->query($sql);
		}

		$sql = "DELETE FROM `#h_adm_menu` WHERE `id`=".intval($id);
		$res = $this->query($sql);
	}
}
//echo "!".intval($_GET['gid'])."?";
if($_GET['act']=='add' && intval($_GET['gid']))
{
	$sql = "INSERT INTO`#h_adm_menu` SET `pid`=".intval($_POST['addparent']).",`text`='".addslashes($_POST['addlinktext'])."', `link`='".addslashes($_POST['addlinklink'])."', `sort`=".intval($_POST['addlinksort']).", `mid`=".intval($_GET['gid']);
		$res = $this->query($sql);
		$need_reload = 1;
		echo $this->core_echomui('adm_ajwaitdursaving');
}
$_RESULT = array();// - формируем массив с именем и значением для вывода в JS функции, которая работает так: req.responseJS.имя =
if($need_reload)
{
	$_RESULT['reload'] = 1;
}
?>