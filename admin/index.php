<?php
/* RedixCMS 4.0
Главный файл админки
*/

								///// ************* ПЕРВЫЙ БЛОК: ПОДГОТОВКА

session_cache_limiter('nocache');
session_start(); // стартуем сессию

								///// ************* ВТОРОЙ БЛОК: ИНКЛУДЫ

// подключаем файл конфига
require_once("../_config.php");

// подключаем файл глобальных функций
require_once("../_system/_global_functions.php");

// подключаем файл пользовательских функций. Эти функции попадают в основной класс
require_once("../_system/_core_user.php");

// подключаем файл работы с БД
require_once("../_system/_db_".DB_TYPE.".php");

// подключаем файл главного класса
require_once("../_system/_core_".CMS_VERSION.".php");

// подключаем файл главного класса
require_once("_system/_adm_core_".ADM_VERSION.".php");

								///// ************* ТРЕТИЙ БЛОК: ОПРЕДЕЛЕНИЕ ГЛОБАЛЬНЫХ ПЕРЕМЕННЫХ

$core = new adm_core(ADMINDIRNAME, 1); // определяем основной класс ядра
$_logged = $core->login(1); // проверяет авторизацию пользователя
$core->adm_prestart();

								///// ************* ЧЕТВЁРТЫЙ БЛОК: ФОРМИРОВАНИЕ СТРАНИЦЫ
ob_start(); // начали запись в буфер

include(DOCUMENT_ROOT.'/'.$core->adm_path.'/template/'.$core->config['adm_tpl'].'/avtorized_'.intval($_logged).'/head.php');// рисуем голову
include(DOCUMENT_ROOT.'/'.$core->adm_path.'/template/'.$core->config['adm_tpl'].'/avtorized_'.intval($_logged).'/mainbody.php');// вывели тело
include(DOCUMENT_ROOT.'/'.$core->adm_path.'/template/'.$core->config['adm_tpl'].'/avtorized_'.intval($_logged).'/bottom.php');// рисуем низ

$_TEXT = ob_get_contents();// получили содержимое буфера
ob_end_clean ();// очистили буфер

								///// ************* ПЯТЫЙ БЛОК: ЗАВЕРШЕНИЕ РАБОТЫ

//выполняем голову
$core->core_go_header();

// выводим текст
$core->core_show_page($_TEXT);

// пишем статистику
$core->adm_write_statistic();

//закрываем коннект к БД
$core->db_close();

$core->core_debug(0);
?>