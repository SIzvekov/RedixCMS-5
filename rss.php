<?php
if(file_exists("install/index.php")){header("Location: install/index.php");exit;}
/*
RedixCMS 4.0
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
//начали запись в буфер
ob_start();

$core->go_rss_show();

//получили содержимое буфера
$_TEXT = ob_get_contents();
//очистили буфер
ob_end_clean ();

/* ПЯТЫЙ БЛОК: ЗАВЕРШЕНИЕ РАБОТЫ */
//выполняем голову
$core->core_go_header();
//выводим текст
$core->core_show_page($_TEXT);
//пишем статистику
$core->core_write_statistic();
//закрываем коннект к БД
$core->db_close();
//echo $core->core_show_exec_time();
$core->core_debug(0);
?>