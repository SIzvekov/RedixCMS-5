<?
// form settings
$req_fields = array(
"qname"=>"Ваше имя",
"qemail"=>"E-mail",
"city"=>"Телефон",
"question"=>"Ваш отзыв",);//, "from_email"=>"Ваш e-mail"

$this->kapcha_field_name = 'code';

$this->form_error = array();
foreach($req_fields as $field=>$label)
{
	if(!$_POST[$field]) $this->form_error[] = 'Не заполнено обязательное поле "'.$label.'"';
}
if(!$this->form_check_kapcha()) $this->form_error[] = 'Код с картинки введён неверно';

if(!eregi("([a-z0-9_-]{1,20})@(([a-z0-9-]+\.)+)([a-z]{2,5})", $_POST['qemail']) && $_POST['qemail']) $this->form_error[] = "Неверно указан e-mail";


if(!sizeof($this->form_error))
{
	$_SESSION['reg_form_send'] = "Ваш отзыв успешно отправлен.";

	$sql = "INSERT INTO `#__feedback` SET 
	`pid`=19, 
	`question`='".addslashes($_POST['question'])."', 
	`qdate`=".time().", 
	`qname`='".addslashes($_POST['qname'])."', 
	`city`='".addslashes($_POST['city'])."', 
	`telefon`='".addslashes($_POST['telefon'])."', 
	`public`='0', 
	`sort`='0', 
	`qemail`='".addslashes($_POST['qemail'])."'";
	$this->query($sql);

	$from = "Отзыв ".HTTP_HOST."<noreply@".HTTP_HOST.">";
	$to = $this->config['cemail'];
	$tema = "Новый отзыв на сайте ".HTTP_HOST;
	$text = "Здравствуйте.<br>
	На сайте ".HTTP_HOST." добавлен новый отзыв:<br/>
	<br/>
	".nl2br($_POST['question'])."<br/>
	<br/>
	Автор отзыва: ".$_POST['qname']." (e-mail: ".$_POST['qemail'].").<br/>
	Контактный телефон: ".$_POST['city']."<br/>
	Дата отзыва: ".date("d.m.Y H:i",time()-7200)." (МСК)<br>
	<br>
	Дать ответ на отзыв и опубликовать его на сайте вы можете через систему управления сайтом: <a href='http://".HTTP_HOST."/admin/'>http://".HTTP_HOST."/admin/</a>";

	$this->sendmail($to, $from, $tema, $text);
	$this->reload();
}
?>