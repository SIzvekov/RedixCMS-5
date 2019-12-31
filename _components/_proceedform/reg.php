<?
// form settings
$req_fields = array("login"=>"Ваш e-mail (логин)", "pas"=>"Пароль", "pas1"=>"Пароль ещё раз", "code"=>"Код с картинки");
$_POST['login'] = trim($_POST['login']);
$_POST['pas'] = trim($_POST['pas']);
$_POST['pas1'] = trim($_POST['pas1']);
$_POST['name'] = trim($_POST['name']);


$this->kapcha_field_name = 'code';
$this->form_error = array();
foreach($req_fields as $field=>$label)
{
	if(!$_POST[$field]) $this->form_error[] = 'Не заполнено обязательное поле "'.$label.'"';
}
if(!$this->form_check_kapcha()) $this->form_error[] = 'Код с картинки введён неверно';
if(!$this->valid_email($_POST['login'])) $this->form_error[] = 'Проверьте правильность ввода e-mail';
else
{
	$sql = "SELECT `id` FROM `#h_users` WHERE `login`='".addslashes($_POST['login'])."' && `group`=3";
	$is = intval($this->num_rows($this->query($sql)));
}
if($is) $this->form_error[] = 'Пользователь с e-mail <span style="font-weight: bold;">'.$_POST['login'].'</span> уже зарегистрирован';

if($_POST['pas']!=$_POST['pas1']) $this->form_error[] = 'Пароль и подтверждение пароля не совпадают';

if(!sizeof($this->form_error))
{
	$_SESSION['con_form_send'] = "Вы успешно зарегистрированы и авторизованы на сайте.";

	$sql = "INSERT INTO `#h_users` SET 
	`login` = '".addslashes($_POST['login'])."',
	`pas` = '".addslashes(md5($_POST['pas']))."',
	`email` = '".addslashes($_POST['login'])."',
	`name` = '".addslashes($_POST['name'])."',
	`date_reg` = ".time().",
	`group` = 3,
	`activ` = '1'";
	$this->query($sql);

/*
$from = "Сайт ".HTTP_HOST."<noreplay@".HTTP_HOST.">";
$to = $this->config['cemail'];
$tema = "Сообщение с сайта";
$text = "Детали:<br>
Имя: ".$_POST['name']."<br>
E-mail: ".$_POST['mail']."<br>
Тема письма: ".$_POST['title']."<br>
Текст письма: ".nl2br($_POST['text'])."";

$this->sendmail($to, $from, $tema, $text);
*/

$_POST['user_log'] = $_POST['login'];
$_POST['user_pas'] = $_POST['pas'];
$this->login();

$this->reload();
}
?>