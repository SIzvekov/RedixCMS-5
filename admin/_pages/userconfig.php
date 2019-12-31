<div class='headspace'></div><?
$this->id = intval($_SESSION['user']['id']);
if(!$this->id) die('error');
// получаем запись, если есть id
$sql = "SELECT * FROM `#h_users` WHERE `id`=".$this->id;
$res = $this->query($sql);
$row = $this->fetch_assoc($res);

// сохраняем информацию
if($_POST['save'])
{
	if(!$_POST['old_pas'] && ($_POST['new_pas1']||$_POST['new_pas2'])) $_POST['old_pas'] = $_POST['pas'];
	if($this->id)
	{
		$newpas = $this->adm_change_pas($_POST['old_pas'],$_POST['new_pas1'],$_POST['new_pas2'], $this->id);
		if($newpas) $_POST['pas'] = $newpas;
	}
if($_POST['pas'])
{
	$fields = array(
	"pas" => "'".addslashes($_POST['pas'])."'",
	"email" => "'".addslashes($_POST['email'])."'",
	);

	$sql = $this->adm_get_edit_sql("#h_users",$fields);
	$res = $this->query($sql);
	if(!$this->id){$this->id = $this->insert_id();$this->adm_add_sys_mes($this->core_echomui('record_added'),"ok");}
	else{$this->adm_add_sys_mes($this->core_echomui('record_edited'),"ok");}
	
	$_SESSION['user']['pas'] = $_POST['pas'];

	$link = '/'.$this->way_url_get.'/';
	
	$this->reload($link);
}else
{
	$row = $_POST;
	$this->adm_add_sys_mes($this->core_echomui('record_unsaved'),"err");
}
}

$this->adm_show_sys_mes();

$strings = array(
	array("title" => $this->core_echomui('password_cur'), "input" => $this->adm_show_input("old_pas","","","width:100%;","","password")),
	array("title" => $this->core_echomui('password_new1'), "input" => $this->adm_show_input("new_pas1","","","width:100%;","","password")),
	array("title" => $this->core_echomui('password_new2'), "input" => $this->adm_show_input("new_pas2","","","width:100%;","","password")),
	array("title" => $this->core_echomui('email'), "input" => $this->adm_show_input("email",$row['email'],"","width:100%;","")),
);
$this->hiddenstring[] = $this->adm_show_hidden("pas", $row['pas']);

?><?echo $this->adm_open_edit_form();?>
<?echo $this->adm_init_bookmarks();?>
<?echo $this->adm_show_edit_content("&nbsp;", $strings)?>
<?echo $this->adm_close_bookmarks();?>
<?
$save = array("value"=>$this->core_echomui('button_save'),"class"=>"savebutton","alter_class"=>"savebutton_h");
echo $this->adm_show_edit_toolbar($save, $app, $cancel);
?><div class="nolongimg"><?echo $this->adm_close_edit_form($hiddenstring);?></div>
<style>div.nolongimg img{width:100px;}</style>