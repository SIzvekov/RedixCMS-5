<?php //v.2.0.
/* RedixCMS 4.0
���� ������.
������� �� ���� ������:
1) ��������� ������, ����������� ������
2) ����������� ������� ������
*/
if(!$this->page_info['meta_title']) $this->page_info['meta_title'] = $this->config['title'];
if(!$this->page_info['meta_title']) $this->page_info['meta_title'] = $this->page_info['title'];

if(!$this->page_info['meta_keywords']) $this->page_info['meta_keywords'] = $this->config['keywords'];
if(!$this->page_info['meta_description']) $this->page_info['meta_description'] = $this->config['description'];

if($this->config['title_autofill']) $this->page_info['meta_title'] .= $this->config['title_autofill'];

include($this->core_get_modtplname());
?>