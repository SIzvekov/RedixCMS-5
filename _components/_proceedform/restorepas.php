<?
// form settings
$req_fields = array("login"=>"Ваш e-mail (логин)", "code"=>"Код с картинки");
$_POST['login'] = trim($_POST['login']);

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
	$sql = "SELECT `id`, `name`, `email` FROM `#h_users` WHERE `login`='".addslashes($_POST['login'])."' && `group`=3";
	$is_res = $this->query($sql);
	$is = intval($this->num_rows($is_res));
}
if(!$is) $this->form_error[] = 'Пользователь с e-mail <span style="font-weight: bold;">'.$_POST['login'].'</span> не найден';
if($_POST['step']==2)
{
	if($_POST['pas']!=$_POST['pas1']) $this->form_error[] = 'Пароль и подтверждение пароля не совпадают';
}


if(!sizeof($this->form_error))
{
	$is_row = $this->fetch_assoc($is_res);

	if($_POST['step']==1)
	{
		$_SESSION['con_form_send'] = "На ваш e-mail отправлено письмо. Для создания нового пароля перейдите по ссылке в письме.";

		$restore_pass_code = md5(microtime().$_POST['login']);
		$restore_pass_code = substr($restore_pass_code, 0, 10);

		$sql = "UPDATE `#h_users` SET 
		`rpascode`='".addslashes($restore_pass_code)."'
		WHERE `id`=".intval($is_row['id']);
		$this->query($sql);

		if($this->valid_email($is_row['email']))
		{
			$link = "http://".HTTP_HOST."/login_restore/?step=2&email=".rawurlencode($_POST['login'])."&code=".$restore_pass_code;
			$from = "Сайт ".HTTP_HOST."<noreplay@".HTTP_HOST.">";
			$to = $is_row['email'];
			$tema = "Восстановление пароля";
			$text = "Здравствуйте".($is_row['name']?', '.$is_row['name']:'').".<br/>
			Для создания нового пароля для вашего аккаунта на сайте ".HTTP_HOST." перейдите по ссылке:<br/>
			<a href=\"".$link."\">".$link."</a><br/>
			<br/>
			====
			GoldenLimo, Екатеринбург.
			";
			$this->sendmail($to, $from, $tema, $text);
		}
	}elseif($_POST['step']==2){
		$_SESSION['con_form_send'] = "Пароль изменён";
		$sql = "UPDATE `#h_users` SET 
		`rpascode`='',
		`pas` = '".addslashes(md5($_POST['pas']))."'
		WHERE `id`=".intval($is_row['id']);
		$this->query($sql);

		$_POST['user_log'] = $_POST['login'];
		$_POST['user_pas'] = $_POST['pas'];
		$this->login();
	}

	$this->reload();
}
?>