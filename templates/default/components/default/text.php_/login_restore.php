<?
$_GET['step'] = intval($_GET['step']);
if(!$_GET['step']) $_GET['step'] = 1;
switch($_GET['step']){
	case '1':
		$title = 'Шаг 1. Введите ваш e-mail';
		$s = 1;
	break;
	case '2':
		$title = 'Шаг 2. Задайте новый пароль';
		$sql = "SELECT `id`, `email`, `rpascode` FROM `#h_users` WHERE `email`='".addslashes($_GET['email'])."' && `rpascode`='".addslashes($_GET['code'])."'";
		$user = $this->fetch_assoc($this->query($sql));
		if(!$user['id'])
		{
			$this->form_error = array('Код подтверждения не найден. <a href="/login_restore/">Начните с первого шага</a>');
		}
		$s = 2;
		$_POST['login'] = $user['email'];
	break;
}







//echo '<pre>';print_r($_SESSION['user']);echo '</pre>';
if($_SESSION['con_form_send']){echo "<div class='goodmes'>".$_SESSION['con_form_send']."</div>";$_SESSION['con_form_send']='';$good=1;}else{
if(sizeof($this->form_error)) echo "<div class='formerror'><span style=\"font-weight: bold;\">Ошибка:</span><br/>".join("<br/>",$this->form_error)."</div>";
?>
<div class="regform"><form method="post" action="" id="form-reg"><input type="hidden" name="proceedform[]" value="restorepas"/><input type="hidden" name="step" value="<?=$s?>" />
	<div class="title"><?=$title?></div>
	<div class="line">
		<div class="label">Ваш e-mail (логин) *:</div>
		<div class="input"><input type="text" name="login" value="<?=$_POST['login']?>"<?echo ($s==2)?' readonly':''?> /></div>
	</div>
	<?if($s==2){?>
	<div class="line">
		<div class="label">Проверочный код:</div>
		<div class="input"><input type="text" name="rpascode" value="<?=$user['rpascode']?>" readonly /></div>
	</div>
	<div class="line">
		<div class="label">Новый пароль *:</div>
		<div class="input"><input type="password" name="pas" value=""/></div>
	</div>
	<div class="line">
		<div class="label">Новый пароль ещё раз *:</div>
		<div class="input"><input type="password" name="pas1" value=""/></div>
	</div>
	<?}?>
	<div class="line submit">
		<div class="code">
	    <div class="capcha"><?=$this->core_show_capcha('code');?></div>
	    <div class="capchalabel">Код с картинки *:</div>
		<div class="inp"><input type="text" name="code" value="" maxlength="5"></div>
		<div class="button"><input type="submit" value="отправить"></div>
	   </div>
	</div>
	<div class="bottomline"></div>
</form></div>
<?}?>