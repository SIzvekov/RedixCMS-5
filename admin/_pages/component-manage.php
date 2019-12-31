<?php //v.1.0.

/* 
параметры компонента:
tbl=textcontent - таблица БД
trashfname=name - имя поля для сохранения в корзину
colonpage_var=10,20,50 - для фильтра выводить на страницу записей
colonpage_def=20 - для фильтра выводить на страницу записей - значение по умолчанию
*/

$this->go_com_import($_POST['import_sql']);


//				ЗАГОЛОВОК
$this->adm_get_com_config(); // <- получили конфиг компонента из таблицы #__components
$this->adm_showparttitle($this->core_echomui('component-manage:admc_title'));

//				ФУНКЦИИ СМЕНЫ ФИЛЬТРА И ЧИСЛА ЗАПИСЕЙ НА СТРАНИЦУ
$this->adm_set_filter_params();

//				ФУНКЦИИ РЕДАКТИРОВАНИЯ
//	1) удаление записи (в т.ч. удаление связанных данных = из бд и файлов)
//$this->adm_del_row(0, "#".$this->adm_com_config['config']['tbl'],"", $this->adm_com_config['config']['trashfname'],'pid');
if(intval($_GET['del']))
{
	$delid = intval($_GET['del']);
	$sql = "SELECT `id`,`pid` FROM `#h_components` WHERE 1";
	$infa = $this->core_get_tree($sql);
	$rows = $this->core_get_tree_keys($delid, array(), $infa, 0, 1);
	$rows[]['id'] = $delid;
	echo '<pre>';print_r($rows);echo '</pre>';
	foreach($rows as $delid)
	{
		$delid = $delid['id'];
		$sql = "DELETE FROM `#h_components` WHERE `id`=".$delid;
		$this->query($sql);
		$sql = "DELETE FROM `#h_components_listedittable` WHERE `com_id`=".$delid;
		$this->query($sql);
		$sql = "DELETE FROM `#h_components_listtable` WHERE `com_id`=".$delid;
		$this->query($sql);
	}

	$this->adm_add_sys_mes($this->core_echomui('adm_delrow_del'),"del");
	$request_uri = preg_replace("/([\?\&])del=\d+/i","\\1",REQUEST_URI);
	$request_uri = str_replace("?&","?",trim(trim($request_uri,"&"),"?"));
	$url = "http://".HTTP_HOST.$request_uri;
	$this->reload($url);
}

//				ФУНКЦИИ ФИЛЬТРА
$sql = "SELECT * FROM `#".$this->adm_com_config['config']['tbl']."` WHERE `system`='0'";

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
$this->adm_show_add_button($this->core_echomui('admc_add'),"","h");// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

//	2) вывод шапки таблицы
$this->adm_show_orderhat();

//	3) вывод тех полей таблицы, какие разрешено. Есть три типа полей: информационные, редактирующие и кнопка удаления.
$this->adm_show_table_fields($row, $this->adm_com_config['config']['tbl']);

$this->adm_navigation();

//	4) вывод служебных кнопок и фильтров
$this->adm_show_add_button($this->core_echomui('admc_add'),"","h");// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

echo $filtr1;

$components = array("0"=>"- Выберите -");
foreach($row as $item) $components[$item['id']] = str_repeat("&nbsp;&nbsp;&nbsp;",$item['this_space'])."".$item['man_title'];

$export_sql = $this->go_com_export(intval($_POST['export_comp']));
echo $export_sql?"<textarea style='width:100%;height:100px;'>".$export_sql."</textarea>":'';
?>
<form method="post">
Экспорт компонента: <?=$this->adm_show_select("export_comp", "", $components)?> <input type="submit" value="Экспорт">
</form>
<form method="post">
Импорт компонента: <textarea style='width:100%;height:100px;' name='import_sql'></textarea> <input type="submit" value="Импорт">
</form>