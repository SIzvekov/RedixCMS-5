<?php /* RedixCMS 5.0

//  для навигации по страницам
NAVIG_CUR_PAGE - Номер текущей страницы
NAVIG_COLONPAGE - Количество записей на страницу
NAVIG_COLALL - Количество записей всего
NAVIG_COLPAGE - Количество страниц всего
*/

/* Тут я выдёргиваю текст к этой странице из другой таблицы. Этот момент ещё под вопросом - возможно есть смысл этот текст писать сразу в таблицу sitemap в отдельное поле `page_text` */

$this->get_typesarray($this->component_id); // определили параметры для функции core_getcorrect_content
$this->page_info['info'] = $this->com_get_page_content($this->page_info, $this->component_config,'','info');
$this->page_info['content'] = $this->com_get_page_content($this->page_info, $this->component_config,'','content');
$this->page_info['sub_pages'] = $this->com_get_subpages_info($this->page_info);

//подключение стандартного шаблона (по имени url-папки)
include($this->core_get_comtplname($this->page_info['tplfile']));
?>