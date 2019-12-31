<?
// подключаем файл конфига
require_once("_config.php");

// подключаем файл глобальных функций
require_once("_system/_global_functions.php");

// подключаем файл пользовательских функций. Эти функции попадают в основной класс
require_once("_system/_core_user.php");

// подключаем файл работы с БД
require_once("_system/_db_".DB_TYPE.".php");

// подключаем файл главного класса
require_once("_system/_core_".CMS_VERSION.".php");

// подключаем файл главного класса
require_once(ADMINDIRNAME."/_system/_adm_core_".ADM_VERSION.".php");

$core = new adm_core(ADMINDIRNAME, intval($_GET['isadm'])); // определяем основной класс ядра

header("Content-type: text/xml;");

/*<lastmod><?echo date('Y-m-d',time()-84000);?</lastmod><changefreq>weekly</changefreq>*/// hide this block for now

$param_list = array();
if($core->config['use_param']) foreach($core->params_list as $par) $param_list[] = array($par['par'],$par['db_prefix']);

$row = array();
if(sizeof($param_list)) foreach($param_list as $par)
{
	$dbpref = DB_PREFIX.DB_HOST_PREFIX.$par[1];
	$sql = "SELECT `url` FROM `".$dbpref."sitemap` WHERE `public`='1' ORDER BY `sort` ASC";
	$row[$par[0]] = $core->get_db_array($sql);
}
else
{
	$sql = "SELECT `url` FROM `#__sitemap` WHERE `public`='1' ORDER BY `sort` ASC";
	$row[$core->param] = $core->get_db_array($sql);
}
?>
<<?echo '?';?>xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?if(!$core->config['use_param']){?><url><loc>http://<?=HTTP_HOST?>/</loc><priority>1</priority></url><?}?>
<?foreach($row as $param=>$item) foreach($item as $url){
	if($core->config['home_url']==$url['url'] && !$core->config['use_param']) continue;
	?><url><loc>http://<?=HTTP_HOST?>/<?echo $core->config['use_param']?$param.'/':''?><?=$url['url']?>/</loc><priority>1</priority></url>
<?}?>
</urlset>
<?$core->db_close();?>