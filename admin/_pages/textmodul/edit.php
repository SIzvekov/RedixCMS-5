<div class='headspace'></div><?php //v.1.0.

/* RedixCMS 4.0
Файл управления компонентом textmodul - редактирование текстовых модулей
*/

// получаем запись, если есть id
if($this->id)
{
	$sql = "SELECT * FROM `#__textmodulcontent` WHERE `id`=".$this->id;
	$res = $this->query($sql);
	$row = $this->fetch_assoc($res);
}
// сохраняем информацию
if($_POST['save'] || $_POST['app'])
{
	$fields = array(
	"name" => "'".addslashes($_POST['name'])."'",
	"code" => "'".addslashes($_POST['code'])."'",
	"public" => "'".intval($_POST['public'])."'",
	"text" => "'".addslashes($_POST['text'])."'",
	);
	
	$sql = $this->adm_get_edit_sql("#__textmodulcontent",$fields);
	$res = $this->query($sql);
	
	if(!$this->id){$this->id = $this->insert_id();$this->adm_add_sys_mes("запись добалена","ok");}
	else{$this->adm_add_sys_mes("запись отредактрована","ok");}
	
	if($_POST['save']) $link = '/'.$this->adm_path.'/'.$this->pre_way.'/';
	elseif($_POST['app']) $link = '/'.$this->way_url_get.'/?id='.$this->id;
	
	$this->reload($link);
}

$this->adm_show_sys_mes();

$strings = array(
	array("title" => "Заголовок", "input" => $this->adm_show_input("name",$row['name'],"","width:100%;"), "req" => "name"),
	array("title" => "Код", "input"=>$this->adm_show_input("code",$row['code'],"","width:100%;"), "acc_fname"=>"code", "acc_def_input"=>$row['code']),
	array("title" => "Опубликован", "input" => $this->adm_show_input("public", "1", $row['public'],"","","checkbox")),
	array("title" => "Текст", "input" => $this->adm_show_editor("text",$row['text'],"id","200","100%","","","fckeditor")),
//	array("title" => "Селект", "input" => $this->adm_show_select("select", 3, array("1"=>"1","2"=>"2","3"=>"3"), "", "multiple size=5","")),
//	array("title" => "Дата создания", "input" => $this->adm_show_date("date")),
);


$hiddenstring = array();

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