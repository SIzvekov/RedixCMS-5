<?
//$dev = ($_SERVER['REMOTE_ADDR']=='187.184.248.62');
//if(!$dev) die('В разработке. Скоро будет.');

session_cache_limiter('nocache');
session_start();
require_once("_config.php");

$code = $_GET['code'];
if(!$code)
{
	$fb_avt_redirekturl = "http://".HTTP_HOST."/fb_avtoriz.php?form=".$_GET['form']."%26fk=".$_GET['fk'];
	$url = "https://www.facebook.com/dialog/oauth?client_id=".FB_APP_CODE."&redirect_uri=".$fb_avt_redirekturl;//."&response_type=code";
	header("Location: ".$url);
	exit;
}else{
	$fb_avt_redirekturl = "http://".HTTP_HOST."/fb_avtoriz.php?form=".$_GET['form']."%26fk=".$_GET['fk'];
	$url = "https://graph.facebook.com/oauth/access_token?client_id=".FB_APP_CODE."&client_secret=".FB_APP_SECRET."&code=".$code."&redirect_uri=".$fb_avt_redirekturl;
	
	$c = curl_init ();  
	curl_setopt ($c, CURLOPT_URL, $url);  
	curl_setopt ($c, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt ($c, CURLOPT_FOLLOWLOCATION, 1); 
	$content = curl_exec ($c);  
	curl_close ($c);
	//echo $content;
	$content_arr = explode("&", $content);
	
	$content = array();
	foreach($content_arr as $item)
	{
		list($k, $v) = explode("=", $item, 2);
		$k = trim($k);
		$v = trim($v);
		$content[$k] = $v;
	}
	
	if($content['access_token'])
	{
		$url = "https://graph.facebook.com/me?fields=id,name&access_token=".$content['access_token'];
	
		$c = curl_init ();  
		curl_setopt ($c, CURLOPT_URL, $url);  
		curl_setopt ($c, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt ($c, CURLOPT_FOLLOWLOCATION, 1); 
		$content_user = curl_exec ($c);  
		curl_close ($c);

		$content_user = json_decode($content_user, true);

		$_SESSION['user'] = array(
			'id'=>$content_user['id'],
			'name'=>$content_user['name'],
			'group'=>array('id'=>3),
			'fb_login' => $content['access_token'], 
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
	die('<script> window.onload = function(){window.opener.go_avtoriz(\''.$suff.'\', \''.$avtform_fk.'\');window.close();}</script>');
}
exit;
?>