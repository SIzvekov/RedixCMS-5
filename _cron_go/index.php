<?php

 error_reporting(E_ALL);

$_SERVER['DOCUMENT_ROOT'] = '';
$_SERVER['HTTP_HOST'] = '';
$_SERVER['REQUEST_URI'] = '/_cron_go/index.php';

/* ВТОРОЙ БЛОК: ИНКЛУДЫ */
//подключаем файл конфига
require_once("../_config.php");
//подключаем файл глобальных функций
require_once("../_system/_global_functions.php");
//подключаем файл пользовательских функций. Эти функции попадают в основной класс
require_once("../_system/_core_user.php");
//подключаем файл работы с БД
require_once("../_system/_db_".DB_TYPE.".php");
//подключаем файл главного класса
require_once("../_system/_core_".CMS_VERSION.".php");
// подключаем файл главного класса
require_once("../".ADMINDIRNAME."/_system/_adm_core_".ADM_VERSION.".php");
/* ТРЕТИЙ БЛОК: ОПРЕДЕЛЕНИЕ ГЛОБАЛЬНЫХ ПЕРЕМЕННЫХ */
//определяем основной класс ядра
$core = new adm_core(ADMINDIRNAME);

$core->go_cron_item("{filename}", array());

$core->db_close();
?>