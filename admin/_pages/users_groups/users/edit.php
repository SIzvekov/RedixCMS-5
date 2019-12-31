<div class='headspace'></div><?php //v.1.0.

/* RedixCMS 4.0*/

$sql = "SELECT `id`,`name` FROM `#h_users_groups` WHERE 1 ORDER BY `name`";
$res = $this->query($sql);
while($row = $this->fetch_assoc($res)) $acc_cats[$row['id']] = $row['name'];
$cid = $_SESSION['adm_filter'][$this->adm_get_ses_key($this->pre_way)]['ug_cid'];

if(!$cid) $title = "Все пользователи";
else 
{
	$title = "Пользователи группы '".$acc_cats[$cid]."'";
}

$this->adm_showparttitle("Группы пользователей / ".$title." / {edittext}"); // {edittext} автоматически заменяется на "редактировать" или "добавить" в зависимости от действия

//echo "<pre>";
//print_r($this->config['tpl']);

// получаем запись, если есть id
if($this->id)
{
	$sql = "SELECT * FROM `#h_users` WHERE `id`=".$this->id;
	$res = $this->query($sql);
	$row = $this->fetch_assoc($res);
}
// сохраняем информацию
if($_POST['save'] || $_POST['app'])
{
	$date_reg = $this->adm_gettimefrom("date_reg");
	$date_lastvizit = $this->adm_gettimefrom("date_lastvizit");
	
	if($this->id)
	{
		$newpas = $this->adm_change_pas($_POST['old_pas'],$_POST['new_pas1'],$_POST['new_pas2'], $this->id);
		if($newpas) $_POST['pas'] = $newpas;
	}else
	{
		$_POST['pas'] = $this->adm_getnew_pas($_POST['new_pas1'],$_POST['new_pas2']);
	}
if($_POST['pas'])
{
	if(!intval($_POST['group'])) $_POST['group'] = 3;

	$fields = array(
	"login" => "'".addslashes($_POST['login'])."'",
	"pas" => "'".addslashes($_POST['pas'])."'",
	"email" => "'".addslashes($_POST['email'])."'",
	"family" => "'".addslashes($_POST['family'])."'",
	"name" => "'".addslashes($_POST['name'])."'",
	"otchestvo" => "'".addslashes($_POST['otchestvo'])."'",
	"date_reg" => intval($date_reg),
	"date_lastvizit" => intval($date_lastvizit),
	"group" => intval($_POST['group']),
	"activ" => "'".intval($_POST['activ'])."'",
	"site_tpl" => "'".addslashes($_POST['site_tpl'])."'",
	"adm_tpl" => "'".addslashes($_POST['adm_tpl'])."'",
	"rpascode" => "'".addslashes($_POST['rpascode'])."'",
	"scode" => "'".addslashes($_POST['scode'])."'",
	"phone" => "'".addslashes($_POST['phone'])."'",
	"icq" => "'".addslashes($_POST['icq'])."'",
	"skype" => "'".addslashes($_POST['skype'])."'",
	"region" => "'".addslashes($_POST['region'])."'",
	"city" => "'".addslashes($_POST['city'])."'",
	"street" => "'".addslashes($_POST['street'])."'",
	"koef" => "'".addslashes(str_replace(",",".",trim($_POST['koef'])))."'",
	);
	
	$sql = $this->adm_get_edit_sql("#h_users",$fields);
	$res = $this->query($sql);
	
	if(!$this->id){$this->id = $this->insert_id();$this->adm_add_sys_mes("запись добалена","ok");}
	else{$this->adm_add_sys_mes("запись отредактрована","ok");}
	
	if($_POST['save']) $link = '/'.$this->adm_path.'/'.$this->pre_way.'/';
	elseif($_POST['app']) $link = '/'.$this->way_url_get.'/?id='.$this->id;
	
	$this->reload($link);
}else
{
	$row = $_POST;
	$this->adm_add_sys_mes("Информация не сохранена","err");
}
}

$this->adm_show_sys_mes();

$all_site_tpl = $this->adm_get_sitetpl_list();
$all_adm_tpl = $this->adm_get_admtpl_list();

$regions = array("66"=>"Свердловская", "72"=>"Тюменская", "74"=>"Челябинская", "45"=>"Курганская", "59"=>"Пермская");

$strings = array(
	array("title" => "Логин", "input" => $this->adm_show_input("login",$row['login'],"","width:100%;")),
	array("title" => "Текущий пароль", "input" => $this->adm_show_input("old_pas","","","width:100%;","","password")),
	array("title" => "Новый пароль1", "input" => $this->adm_show_input("new_pas1","","","width:100%;","","password")),
	array("title" => "Новый пароль2", "input" => $this->adm_show_input("new_pas2","","","width:100%;","","password")),
	array("title" => "E-mail", "input" => $this->adm_show_input("email",$row['email'],"","width:100%;")),
//	array("title" => "Фамилия", "input" => $this->adm_show_input("family",$row['family'],"","width:100%;")),
	array("title" => "Имя", "input" => $this->adm_show_input("name",$row['name'],"","width:100%;")),
//	array("title" => "Отчество", "input" => $this->adm_show_input("otchestvo",$row['otchestvo'],"","width:100%;")),
//	array("title" => "Телефон", "input" => $this->adm_show_input("phone",$row['phone'],"","width:100%;")),
//	array("title" => "ICQ", "input" => $this->adm_show_input("icq",$row['icq'],"","width:100%;")),
//	array("title" => "Skype", "input" => $this->adm_show_input("skype",$row['skype'],"","width:100%;")),
//	array("title" => "Область", "input" => $this->adm_show_select("region", $row['region'], $regions, "", "","")),
//	array("title" => "Город", "input" => $this->adm_show_input("city",$row['city'],"","width:100%;")),
//	array("title" => "Адрес", "input" => $this->adm_show_input("street",$row['street'],"","width:100%;")),
	array("title" => "Зарегистрирован", "input" => $this->adm_show_date("date_reg",$row['date_reg'], 1, "d.m.Yгода, в H:i:s")),
	array("title" => "Последний визит", "input" => $this->adm_show_date("date_lastvizit",$row['date_lastvizit'], 0, "d.m.Yгода, в H:i:s")),
	array("title" => "Группа", "input" => $this->adm_show_select("group", $row['group'], $acc_cats, "", "",""),"acc_fname"=>"group","acc_def_input"=>$acc_cats[$row['group']].$this->adm_show_hidden("group", $row['group'])),
	array("title" => "Активен", "input" => $this->adm_show_input("activ","1",$row['activ'],"","","checkbox")),
//	array("title" => "Коэффициент цены", "input" => $this->adm_show_input("koef",$row['koef'],1,"","","")),
//	array("title" => "Шаблон сайта", "input" => $this->adm_show_select("site_tpl", $row['site_tpl'], $all_site_tpl, "", "","")),
//	array("title" => "Шаблон админки", "input" => $this->adm_show_select("adm_tpl", $row['adm_tpl'], $all_adm_tpl, "", "","")),
//	array("title" => "Код вспомнить пароль", "input" => $this->adm_show_input("rpascode",$row['rpascode'],"","width:100%;")),
//	array("title" => "Код активации", "input" => $this->adm_show_input("scode",$row['scode'],"","width:100%;")),
);

$this->hiddenstring[] = $this->adm_show_hidden("pas", $row['pas']);
/*$hiddenstring = array(
	$this->adm_show_hidden("pas", $row['pas']),
);*/

?>
<?echo $this->adm_open_edit_form();?>
<?echo $this->adm_init_bookmarks();?>
<?echo $this->adm_show_edit_content("Содержание", $strings)?>
<?echo $this->adm_close_bookmarks();?>
<?
$save = array("value"=>"сохранить","class"=>"savebutton","alter_class"=>"savebutton_h");
$app = array("value"=>"применить","class"=>"appbutton","alter_class"=>"appbutton_h");
$cancel = array("value"=>"отмена","class"=>"cancelbutton","alter_class"=>"cancelbutton_h");
echo $this->adm_show_edit_toolbar($save, $app, $cancel);
?>
<?echo $this->adm_close_edit_form($hiddenstring);?>