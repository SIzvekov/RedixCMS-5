<?php /* RedixCMS 5.0

//  ��� ��������� �� ���������
NAVIG_CUR_PAGE - ����� ������� ��������
NAVIG_COLONPAGE - ���������� ������� �� ��������
NAVIG_COLALL - ���������� ������� �����
NAVIG_COLPAGE - ���������� ������� �����
*/

/* ��� � ��������� ����� � ���� �������� �� ������ �������. ���� ������ ��� ��� �������� - �������� ���� ����� ���� ����� ������ ����� � ������� sitemap � ��������� ���� `page_text` */

$this->get_typesarray($this->component_id); // ���������� ��������� ��� ������� core_getcorrect_content
$this->page_info['info'] = $this->com_get_page_content($this->page_info, $this->component_config,'','info');
$this->page_info['content'] = $this->com_get_page_content($this->page_info, $this->component_config,'','content');
$this->page_info['sub_pages'] = $this->com_get_subpages_info($this->page_info);

//����������� ������������ ������� (�� ����� url-�����)
include($this->core_get_comtplname($this->page_info['tplfile']));
?>