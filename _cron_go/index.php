<?php

 error_reporting(E_ALL);

$_SERVER['DOCUMENT_ROOT'] = '';
$_SERVER['HTTP_HOST'] = '';
$_SERVER['REQUEST_URI'] = '/_cron_go/index.php';

/* ������ ����: ������� */
//���������� ���� �������
require_once("../_config.php");
//���������� ���� ���������� �������
require_once("../_system/_global_functions.php");
//���������� ���� ���������������� �������. ��� ������� �������� � �������� �����
require_once("../_system/_core_user.php");
//���������� ���� ������ � ��
require_once("../_system/_db_".DB_TYPE.".php");
//���������� ���� �������� ������
require_once("../_system/_core_".CMS_VERSION.".php");
// ���������� ���� �������� ������
require_once("../".ADMINDIRNAME."/_system/_adm_core_".ADM_VERSION.".php");
/* ������ ����: ����������� ���������� ���������� */
//���������� �������� ����� ����
$core = new adm_core(ADMINDIRNAME);

$core->go_cron_item("{filename}", array());

$core->db_close();
?>