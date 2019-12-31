<?
$action = preg_replace("/[\?\&]+logoff/","",REQUEST_URI);
?>
<form action="<?=$action?>" method="post" name="loginForm" id="loginForm">
<table class="kkr" style="width:100%;height:100%;padding:0px;"><tr><td class="kkr">

<table class="login-table" border="0" cellpadding="0" cellspacing="0" align="center">
	
	<tr><td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"><tr>
			<td class="login-title-l"><img src="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/img/ft-left.png" width="8" height="30" class="png"></td>
			<td class="login-title"><?=$core->core_echomui('adm_avtorization')?>
			<td class="login-title-r"><img src="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/img/ft-right.png" width="8" height="30" class="png"></td>
		</table>

	<tr><td class="login-content">
		<div class="login-error-content"><?=$core->avtorizerror;?></div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td rowspan=2><div class="redixcmslogo"><img src="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/img/redixcms_green_base.png" class="png" width="150" height="60" alt="RedixCMS"></div>
				<td class="login-field-title" width="35%"><?=$core->core_echomui('adm_login')?>
				<td class="login-field-content"><input name="user_log" type="text" id="user_log" class="inputbox" size="15" value="<?=htmlspecialchars($_GET['errorlogin'])?>" tabindex=1 />
				<td rowspan=2><div class="submit"><input class="login-button" value="<?=$core->core_echomui('adm_enter');?>" type="submit" tabindex=3></div>
			<tr>
				<td class="login-field-title"><?=$core->core_echomui('adm_password')?>
				<td class="login-field-content"><input name="user_pas" type="password" class="inputbox" size="15" tabindex=2 />
		</table>

	<tr><td align="center"><br>
		<a href="http://redixcms.ru/" target="_blank" class="login-copy">2007-<?=date("Y",time())?> © RedixCMS - <?=$core->core_echomui('adm_cms')?>.</a>
</table>
</table>
</form>
<script language="javascript" type="text/javascript">
document.loginForm.user_log.select();
document.loginForm.user_log.focus();
</script>