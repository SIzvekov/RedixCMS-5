<?php
/* RedixCMS 4.0
Главный файл, запускаемый в самом начале
*/

								///// ************* ПЕРВЫЙ БЛОК: ПОДГОТОВКА

session_cache_limiter('nocache');
session_start(); // стартуем сессию


								///// ************* ВТОРОЙ БЛОК: ИНКЛУДЫ

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

								///// ************* ТРЕТИЙ БЛОК: ОПРЕДЕЛЕНИЕ ГЛОБАЛЬНЫХ ПЕРЕМЕННЫХ

$core = new adm_core(ADMINDIRNAME, intval($_GET['isadm'])); // определяем основной класс ядра
$core->thisajax = 1;
$core->login(); // проверяет авторизацию пользователя
$core->prestart();

								///// ************* ЧЕТВЁРТЫЙ БЛОК: ФОРМИРОВАНИЕ СТРАНИЦЫ
$_RESULT = $core->core_go_ajaxpage();

								///// ************* ПЯТЫЙ БЛОК: ЗАВЕРШЕНИЕ РАБОТЫ

//закрываем коннект к БД
$core->db_close();
?>