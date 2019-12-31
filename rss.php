<?php
if(file_exists("install/index.php")){header("Location: install/index.php");exit;}
/*
RedixCMS 4.0
������� ����, ����������� � ����� ������
*/

/* ������ ����: ���������� */
session_cache_limiter('nocache');
session_start(); // �������� ������

/* ��������� ��� ������������� */
if (file_exists('install.php')) {
	require_once('install.php');
	exit();
}

/* ������ ����: ������� */
//���������� ���� �������
require_once("_config.php");
//���������� ���� ���������� �������
require_once("_system/_global_functions.php");
//���������� ���� ���������������� �������. ��� ������� �������� � �������� �����
require_once("_system/_core_user.php");
//���������� ���� ������ � ��
require_once("_system/_db_".DB_TYPE.".php");
//���������� ���� �������� ������
require_once("_system/_core_".CMS_VERSION.".php");
// ���������� ���� �������� ������
require_once(ADMINDIRNAME."/_system/_adm_core_".ADM_VERSION.".php");

/* ������ ����: ����������� ���������� ���������� */
//���������� �������� ����� ����
$core = new adm_core(ADMINDIRNAME);
//��������� ����������� ������������
$core->login();
$core->prestart();

/* ���¨���� ����: ������������ �������� */
//������ ������ � �����
ob_start();

$core->go_rss_show();

//�������� ���������� ������
$_TEXT = ob_get_contents();
//�������� �����
ob_end_clean ();

/* ����� ����: ���������� ������ */
//��������� ������
$core->core_go_header();
//������� �����
$core->core_show_page($_TEXT);
//����� ����������
$core->core_write_statistic();
//��������� ������� � ��
$core->db_close();
//echo $core->core_show_exec_time();
$core->core_debug(0);
?>