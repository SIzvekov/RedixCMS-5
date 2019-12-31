<?
// form settings
$req_fields = array("name"=>"Ваше имя", "mail"=>"Ваш e-mail", "title"=>"Тема письма", "text"=>"Текст письма");

$this->kapcha_field_name = 'code';
$this->form_error = array();
foreach($req_fields as $field=>$label)
{
	if(!$_POST[$field]) $this->form_error[] = 'Не заполнено обязательное поле "'.$label.'"';
}
if(!$this->form_check_kapcha()) $this->form_error[] = 'Код с картинки введён неверно';

if(!sizeof($this->form_error))
{
	$_SESSION['con_form_send'] = "Сообщение успешно отправлено";

$from = "Сайт ".HTTP_HOST."<noreplay@".HTTP_HOST.">";
$to = $this->config['cemail'];
$tema = "Сообщение с сайта";
$text = "Детали:<br>
Имя: ".$_POST['name']."<br>
E-mail: ".$_POST['mail']."<br>
Тема письма: ".$_POST['title']."<br>
Текст письма: ".nl2br($_POST['text'])."";

$this->sendmail($to, $from, $tema, $text);

$this->reload();
}
?>