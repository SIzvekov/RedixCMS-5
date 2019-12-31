<?
return;
if(isset($_GET['logoff']))
{
	setcookie('login', '');
	setcookie('pas', '');
}
//print_r($_SESSION['user']);
//print_r($_COOKIE['login']);
//echo '<!-- '.$this->avtform_fk.' -->';

$suff = $this->avtform_fk?'_fk':'';
?>
<?if(!$_SESSION['user']['id'] || $_SESSION['user']['group']['id']!=3){?>
<div class="avtorizblock">
	<div class="title">Авторизоваться</div>
	<?if($_POST['user_log'] && $_POST['user_pas']){?><div class="errormes">Неверный e-mail или пароль</div><?}?>
	<form mathod="post" action="" id="avtorizform<?=$suff?>" enctype="multipart/form-data" onsubmit="go_avtoriz('<?=$suff?>', '<?=$this->avtform_fk?>');return false;">
		<div class="line">
			<div class="label">E-mail:</div>
			<div class="inp"><input type="text" name="user_log" value="<?=$_POST['user_log']?>"/></div>
		</div>
		<div class="line">
			<div class="label">Пароль:</div>
			<div class="inp"><input type="password" name="user_pas"/></div>
		</div>
		<div class="line">
			<div class="submit">
				<input type="submit" value="войти" />
			</div>
			<div class="links">
				<div><a href="/reg">Зарегистрироваться</a></div>
				<div><a href="/login_restore">Забыли пароль?</a></div>
			</div>
		</div><?if($_GET['dev']||1){?>
		<div class="sociallogin">
			<div class="title">Войти через:</div>
			<?
			$vk_avt_redirekturl = "http://".HTTP_HOST."/vk_avtoriz.php?form=".($this->avtform_fk?'fk':'st')."&fk=".$this->avtform_fk;
			$fb_avt_redirekturl = "http://".HTTP_HOST."/fb_avtoriz.php?form=".($this->avtform_fk?'fk':'st')."&fk=".$this->avtform_fk;
			?>
			<div class="loginfacebook"><a href="<?=$fb_avt_redirekturl?>" target="_blank" onclick="window.open(this.href, this.target, 'width=650,height=300');return false;"><img src="/templates/default/images/fb.png" alt="" title=""/></a></div>
			<div class="loginvk"><a href="<?=$vk_avt_redirekturl?>" target="_blank" onclick="window.open(this.href, this.target, 'width=650,height=300');return false;"><img src="/templates/default/images/vk.png" alt="" title=""/></a></div>
			&nbsp;
		</div>
		<?}?>
	</form>
</div>
<div class="avtorizblockbottomline"></div>
<?}else{?>
<div class="avtorizblock">
	<div class="title">Здравствуйте, <?=$_SESSION['user']['name']?>!</div>
	<a href="?logoff" onclick="logoff();return false;">Выйти</a>
</div>
<div class="avtorizblockbottomline"></div>
<?}?>