<?php
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

if(file_exists("install/index.php")){header("Location: install/index.php");exit;}
/*
RedixCMS 5.0
Главный файл, запускаемый в самом начале
*/
/* ПЕРВЫЙ БЛОК: ПОДГОТОВКА */
session_cache_limiter('nocache');
session_start(); // стартуем сессию

/* УСТАНОВКА ПРИ НЕОБХОДИМОСТИ */
if (file_exists('install.php')) {
	require_once('install.php');
	exit();
}

/* ВТОРОЙ БЛОК: ИНКЛУДЫ */
//подключаем файл конфига
require_once("_config.php");

//change language
if($_SERVER['HTTP_ACCEPT_LANGUAGE'] && !$_SESSION['choosed_lang'] && REQUEST_URI=='/' && sizeof($_LANGS)>1){$lang=strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));if(!in_array($lang,$_LANGS)){$lang=$_LANGS[0];}header("Location: http://".HTTP_HOST.'/'.$lang.'/');$_SESSION['choosed_lang']=$lang;exit;}

//clearing mirrors.
/*if(HTTP_HOST=='...') {header("HTTP/1.1 301 Moved Permanently");header("Location: http://...".REQUEST_URI);exit;}*/

//подключаем файл глобальных функций
require_once("_system/_global_functions.php");
//подключаем файл пользовательских функций. Эти функции попадают в основной класс
require_once("_system/_core_user.php");
//подключаем файл работы с БД
require_once("_system/_db_".DB_TYPE.".php");
//подключаем файл главного класса
require_once("_system/_core_".CMS_VERSION.".php");
// подключаем файл главного класса
require_once(ADMINDIRNAME."/_system/_adm_core_".ADM_VERSION.".php");

/* ТРЕТИЙ БЛОК: ОПРЕДЕЛЕНИЕ ГЛОБАЛЬНЫХ ПЕРЕМЕННЫХ */
//определяем основной класс ядра
$core = new adm_core(ADMINDIRNAME);
//проверяет авторизацию пользователя
$core->login();
$core->prestart();

/* ЧЕТВЁРТЫЙ БЛОК: ФОРМИРОВАНИЕ СТРАНИЦЫ */
$core->core_go_proceedform();
$cache_data = $core->core_get_cachepage();
if($cache_data["cache"]) {
	//если выводим содержимое страницы из кэша, то записываем в TEXT HTML страницы и не выполняем блок ниже
	$_TEXT = $cache_data["data"];
} else {
	//начали запись в буфер
	ob_start();
	//Запуск вызываемого компонента
	$core->core_go_component();
	//Вызов нужного шаблона сайта
	$core->core_site_template();
	//получили содержимое буфера
	$_TEXT = ob_get_contents();
	//очистили буфер
	ob_end_clean ();
	//записали в кэш страницу
	$core->core_put_cachepage($_TEXT);
}

if($_GET['dev'] && 0){
	$sql = "SELECT * FROM `sys_rx_ru_fotokonkurs_votes` WHERE `img_id`=5";
	$res = $core->query($sql);
	$return = array();
	$users_by_ip = array();
	while($row = $core->fetch_assoc($res)){
		$return[$row['date']][$row['user_ip']]++;

		$sql = "SELECT * FROM `sys_rx_ru_fotokonkurs_votes` WHERE `user_ip`='".$row['user_ip']."' GROUP BY `user_id`";
		$u_res = $core->query($sql);

		$users_by_ip[$row['user_ip']]['num'] = $core->num_rows($u_res);
		while($u_row = $core->fetch_assoc($u_res)){
			$sql = "SELECT `date_lastvizit`, `login` FROM `sys_rx_users` WHERE `id`=".$u_row['user_id'];
			$user_res = $core->fetch_assoc($core->query($sql));
			$item = $u_row['user_id'].", ".$user_res['login']." ".($user_res['date_lastvizit']?date("d.m.Y H:i:s", $user_res['date_lastvizit']):'');

			if(in_array($item, $users_by_ip[$row['user_ip']]['uids'])) continue;
			$users_by_ip[$row['user_ip']]['uids'][] = $item;
		}
	}
	asort($users_by_ip);
	echo '<pre>';print_r($users_by_ip);echo '</pre>';
	echo '<pre>';print_r($return);echo '</pre>';
}


/* ПЯТЫЙ БЛОК: ЗАВЕРШЕНИЕ РАБОТЫ */
//выполняем голову
$core->core_go_header();
//выводим текст
$core->core_show_page($_TEXT);
//пишем статистику
$core->core_write_statistic();
//закрываем коннект к БД
$core->db_close();
echo "\n<!--redixCMS time2gen : ".$core->core_show_exec_time()."-->";
$core->core_debug(0);
?>