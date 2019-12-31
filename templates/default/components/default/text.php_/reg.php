<?
//echo '<pre>';print_r($_SESSION['user']);echo '</pre>';
if($_SESSION['con_form_send']){echo "<div class='goodmes'>".$_SESSION['con_form_send']."</div>";$_SESSION['con_form_send']='';$good=1;}else{
if(sizeof($this->form_error)) echo "<div class='formerror'><span style=\"font-weight: bold;\">Ошибка:</span><br/>".join("<br/>",$this->form_error)."</div>";
?>
<div class="regform"><form method="post" action="" id="form-reg"><input type="hidden" name="proceedform[]" value="reg"/>
	<div class="title">Зарегистрироваться</div>
	<div class="line">
		<div class="label">Ваш e-mail (логин) *:</div>
		<div class="input"><input type="text" name="login" value="<?=$_POST['login']?>"/></div>
	</div>
	<div class="line">
		<div class="label">Пароль *:</div>
		<div class="input"><input type="password" name="pas" value=""/></div>
	</div>
	<div class="line">
		<div class="label">Пароль ещё раз *:</div>
		<div class="input"><input type="password" name="pas1" value=""/></div>
	</div>
	<div class="line">
		<div class="label">Ваше имя:</div>
		<div class="input"><input type="text" name="name" value="<?=$_POST['name']?>"/></div>
	</div>
	<div class="line">
		<div class="label">Сотовый телефон:</div>
		<div class="input"><input type="text" name="phone" value="<?=$_POST['phone']?>"/></div>
	</div>
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