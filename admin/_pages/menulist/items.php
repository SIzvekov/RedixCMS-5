<?php //v.1.0.

/* 
параметры компонента:
tbl=textcontent - таблица БД
trashfname=name - имя поля для сохранения в корзину
colonpage_var=10,20,50 - для фильтра выводить на страницу записей
colonpage_def=20 - для фильтра выводить на страницу записей - значение по умолчанию
*/

//				ЗАГОЛОВОК
$_mid = intval($_SESSION['adm_filter'][$this->ses_key]['mid']);
$this->adm_get_com_config(); // <- получили конфиг компонента из таблицы #__components
$this->adm_showparttitle($this->core_echomui('menulist_items:admc_title'));

//				ФУНКЦИИ СМЕНЫ ФИЛЬТРА И ЧИСЛА ЗАПИСЕЙ НА СТРАНИЦУ
$filterparam = array();
$filterparam[] = array("name"=>"mid","val"=>$_GET['mid']);
$this->adm_set_filter_params($filterparam);

//				ФУНКЦИИ РЕДАКТИРОВАНИЯ
//	1) удаление записи (в т.ч. удаление связанных данных = из бд и файлов)
$this->adm_del_row(0, "#".$this->adm_com_config['config']['tbl'],"", $this->adm_com_config['config']['trashfname']);

//				ФУНКЦИИ ФИЛЬТРА
if($_mid)
{
	$cond = "`mid`=".intval($_mid);
}else
{
	$cond = "1";
}
$sql = "SELECT * FROM `#".$this->adm_com_config['config']['tbl']."` WHERE ".$cond;

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

$allmids = array();
$allmids[0] = "- Все -";
$sql = "SELECT `id`,`name` FROM `#__menulist` WHERE 1 ORDER BY `sort`, `name`";
$res = $this->query($sql);
while($row1 = $this->fetch_assoc($res)) $allmids[$row1['id']] = $row1['name'];


// вывод фильтров:
$filtr1 = $this->adm_showcolonpage_filtr();
$filtr2 = $this->adm_showfiltr("Меню", "mid",$_SESSION['adm_filter'][$this->ses_key]['mid'], $allmids);
echo $filtr1;
echo $filtr2;

//	1) вывод служебных кнопок и фильтров
$this->adm_show_add_button($this->core_echomui('admc_add'),"","h");// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

//	2) вывод шапки таблицы
$this->adm_show_orderhat('','mid='.intval($_mid));

//	3) вывод тех полей таблицы, какие разрешено. Есть три типа полей: информационные, редактирующие и кнопка удаления.
$this->adm_show_table_fields($row, $this->adm_com_config['config']['tbl']);

$this->adm_navigation();

//	4) вывод служебных кнопок и фильтров
$this->adm_show_add_button($this->core_echomui('admc_add'),"","h");// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

echo $filtr2;
echo $filtr1;
?>