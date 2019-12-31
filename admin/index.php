<?php
/* RedixCMS 4.0
������� ���� �������
*/

								///// ************* ������ ����: ����������

session_cache_limiter('nocache');
session_start(); // �������� ������

								///// ************* ������ ����: �������

// ���������� ���� �������
require_once("../_config.php");

// ���������� ���� ���������� �������
require_once("../_system/_global_functions.php");

// ���������� ���� ���������������� �������. ��� ������� �������� � �������� �����
require_once("../_system/_core_user.php");

// ���������� ���� ������ � ��
require_once("../_system/_db_".DB_TYPE.".php");

// ���������� ���� �������� ������
require_once("../_system/_core_".CMS_VERSION.".php");

// ���������� ���� �������� ������
require_once("_system/_adm_core_".ADM_VERSION.".php");

								///// ************* ������ ����: ����������� ���������� ����������

$core = new adm_core(ADMINDIRNAME, 1); // ���������� �������� ����� ����
$_logged = $core->login(1); // ��������� ����������� ������������
$core->adm_prestart();

								///// ************* ���¨���� ����: ������������ ��������
ob_start(); // ������ ������ � �����

include(DOCUMENT_ROOT.'/'.$core->adm_path.'/template/'.$core->config['adm_tpl'].'/avtorized_'.intval($_logged).'/head.php');// ������ ������
include(DOCUMENT_ROOT.'/'.$core->adm_path.'/template/'.$core->config['adm_tpl'].'/avtorized_'.intval($_logged).'/mainbody.php');// ������ ����
include(DOCUMENT_ROOT.'/'.$core->adm_path.'/template/'.$core->config['adm_tpl'].'/avtorized_'.intval($_logged).'/bottom.php');// ������ ���

$_TEXT = ob_get_contents();// �������� ���������� ������
ob_end_clean ();// �������� �����

								///// ************* ����� ����: ���������� ������

//��������� ������
$core->core_go_header();

// ������� �����
$core->core_show_page($_TEXT);

// ����� ����������
$core->adm_write_statistic();

//��������� ������� � ��
$core->db_close();

$core->core_debug(0);
?>