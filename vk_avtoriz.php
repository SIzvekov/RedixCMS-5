<?
//$dev = ($_SERVER['REMOTE_ADDR']=='187.184.248.62');
//if(!$dev) die('не работает');

session_cache_limiter('nocache');
session_start();
require_once("_config.php");

$code = $_GET['code'];
if(!$code)
{
	$vk_avt_redirekturl = rawurlencode("http://".HTTP_HOST."/vk_avtoriz.php?form=".$_GET['form']."&fk=".$_GET['fk']);
	$url = "https://oauth.vk.com/authorize?client_id=".VK_APP_CODE."&scope=&redirect_uri=".$vk_avt_redirekturl."&response_type=code&v=".VK_API_VERSION;

	//$content = json_decode(file_get_contents($url), true);
	//print_r($content);
	header("Location: ".$url);
	exit;
	//die('<script>location.href=\''.$url.'\';</script>');
}else{
	$vk_avt_redirekturl = rawurlencode("http://".HTTP_HOST."/vk_avtoriz.php?form=".$_GET['form']."&fk=".$_GET['fk']);
	$url = "https://oauth.vk.com/access_token?client_id=".VK_APP_CODE."&client_secret=".VK_APP_SECRET."&code=".$code."&redirect_uri=".$vk_avt_redirekturl;
	$content = json_decode(file_get_contents($url), true);

	if($content['user_id'])
	{
		$url = "https://api.vk.com/method/users.get?user_id=".$content['user_id']."&v=".VK_API_VERSION."&access_token=".$content['access_token'];
		$response = json_decode(file_get_contents($url), true);
		$name = trim($response['response'][0]['first_name']." ".$response['response'][0]['last_name']);

		$_SESSION['user'] = array(
			'id'=>$content['user_id'],
			'name'=>$name,
			'group'=>array('id'=>3),
			'vk_login' => $content['access_token'], 
			);
	}

	if($_GET['form']=='fk')
	{
		$suff = '_fk';
		$avtform_fk = intval($_GET['fk']);
	}else
	{
		$suff = '';
		$avtform_fk = intval($_GET['fk']);
	}

	die('<script>window.onload = function(){window.opener.go_avtoriz(\''.$suff.'\', \''.$avtform_fk.'\');window.close();}</script>');
}
exit;
?>