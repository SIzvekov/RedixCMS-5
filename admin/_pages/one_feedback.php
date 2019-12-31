<?php //v.1.0.
/* 
параметры компонента:
tbl=textcontent - таблица БД
trashfname=name - имя поля для сохранения в корзину
colonpage_var=10,20,50 - для фильтра выводить на страницу записей
colonpage_def=20 - для фильтра выводить на страницу записей - значение по умолчанию
*/

//				ЗАГОЛОВОК
$_pid = intval($_SESSION['adm_filter'][$this->ses_key]['pid']);
$this->adm_get_com_config(); // <- получили конфиг компонента из таблицы #__components
$file = str_replace("\\","/",__FILE__);
$file = split('/',$file);
$file = end($file);
$file = end(array_reverse(split("\.",$file)));

$this->adm_showparttitle($this->core_echomui($file.':admc_title'));

//				ФУНКЦИИ СМЕНЫ ФИЛЬТРА И ЧИСЛА ЗАПИСЕЙ НА СТРАНИЦУ
$filterparam = array();
$filterparam[] = array("name"=>"pid","val"=>$_GET['pid']);
$this->adm_set_filter_params($filterparam);

//				ФУНКЦИИ РЕДАКТИРОВАНИЯ
//	1) удаление записи (в т.ч. удаление связанных данных = из бд и файлов)
$this->adm_del_row(0, "#".$this->adm_com_config['config']['tbl'],"", $this->adm_com_config['config']['trashfname']);

//				ФУНКЦИИ ФИЛЬТРА
$sql = "SELECT * FROM `#".$this->adm_com_config['config']['tbl']."` WHERE `pid`=".intval($_pid);

//	1) определение фильтров и списков фильтров (список категорий, список городов и т.п.)

//	2) формируем массив заголовков таблицы
	$this->adm_create_hat_ar();

//	3) определение порядка сортировки
$sql = $sql.$this->adm_get_order();

//	4) определение числа записей на страницу и текущей страницы - определение LIMIT для sql
$sql = $this->adm_init_navigation($sql);

//				ВЫБОР СПИСКА ИЗ БД
//	1) формирование массива для вывода
$row = $this->get_srt($sql);

//				ВЫВОД СПИСКА
//	0) вывод системных сообщений
$this->adm_show_sys_mes();

// вывод фильтров:
$filtr1 = $this->adm_showcolonpage_filtr();
echo $filtr1;

//	1) вывод служебных кнопок и фильтров
$this->adm_show_add_button($this->core_echomui('admc_add'),"","h",'/'.$this->adm_path.'/'.$this->way.'/edit/?topid='.$_pid);// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

//	2) вывод шапки таблицы
$this->adm_show_orderhat();

//	3) вывод тех полей таблицы, какие разрешено. Есть три типа полей: информационные, редактирующие и кнопка удаления.
$this->adm_show_table_fields($row, $this->adm_com_config['config']['tbl']);

$this->adm_navigation();

//	4) вывод служебных кнопок и фильтров
$this->adm_show_add_button($this->core_echomui('admc_add'),"","h",'/'.$this->adm_path.'/'.$this->way.'/edit/?topid='.$_pid);// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

echo $filtr1;

//'/'.$this->adm_path.'/'.$this->way.'/edit/
//'/'.$this->adm_path.'/'.$this->way.'/edit/?topid='.$_pid
//'/'.$this->adm_path.'/pageconfig/?add&com='.$this->adm_com_config['id'].'&topid='.$_pid.'&ref=/'.$this->adm_path.'/'.$this->way.'/'
?>