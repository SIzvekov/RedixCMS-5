<?php
/* RedixCMS 4.0
������� ����, ����������� � ����� ������
*/

								///// ************* ������ ����: ����������

session_cache_limiter('nocache');
session_start(); // �������� ������


								///// ************* ������ ����: �������

// ���������� ���� �������
require_once("_config.php");
// ���������� ���� ���������� �������
require_once("_system/_global_functions.php");

// ���������� ���� ���������������� �������. ��� ������� �������� � �������� �����
require_once("_system/_core_user.php");

// ���������� ���� ������ � ��
require_once("_system/_db_".DB_TYPE.".php");

// ���������� ���� �������� ������
require_once("_system/_core_".CMS_VERSION.".php");

// ���������� ���� �������� ������
require_once(ADMINDIRNAME."/_system/_adm_core_".ADM_VERSION.".php");

								///// ************* ������ ����: ����������� ���������� ����������

$core = new adm_core(ADMINDIRNAME, intval($_GET['isadm'])); // ���������� �������� ����� ����
$core->thisajax = 1;
$core->login(); // ��������� ����������� ������������
$core->prestart();

								///// ************* ���¨���� ����: ������������ ��������
$_RESULT = $core->core_go_ajaxpage();

								///// ************* ����� ����: ���������� ������

//��������� ������� � ��
$core->db_close();
?>