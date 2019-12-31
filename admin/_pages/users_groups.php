<?php //v.1.0.

/* RedixCMS 4.0
Файл управления компонентом text - это компонент поумолчанию
*/

//				ЗАГОЛОВОК
$this->adm_com_config['config']['tbl'] = "h_users_groups";
$this->adm_showparttitle("Группы пользователей");

//				ФУНКЦИИ СМЕНЫ ФИЛЬТРА И ЧИСЛА ЗАПИСЕЙ НА СТРАНИЦУ
$this->adm_set_filter_params();

//				ФУНКЦИИ РЕДАКТИРОВАНИЯ
//	1) смена статуса галочек (публиковать/непубликовать и т.п.)
$this->adm_switch_row(intval($_GET['isadmin']),"#h_users_groups","","isadmin","0,1");

//	3) удаление записи (в т.ч. удаление связанных данных = из бд и файлов)
$this->adm_del_row(0, "#h_users_groups");

//				ФУНКЦИИ ФИЛЬТРА
$sql = "SELECT * FROM `#h_users_groups` WHERE 1";

//	1) определение фильтров и списков фильтров (список категорий, список городов и т.п.)
//	2) формируем массив заголовков таблицы
	$this->hat_ar = array(// каждый элемент - массив - это колонка. Поля: 
	//k - ключ, должен совпадать с именем поля БД, если содержимое этого поля учавствует в сортировке, или тут выводится будет инфа из БД
	//v - текст зоголовка колонки таблицы
	//type - тип поля, используется для вывода содержимого из БД. Типы могут быть: index - порядковый номер строки; switch - переключатель 1,0 (например для поля public); field - поле input, например для поля sort; edit - кнопка "редактировать"; del - кнопка "удалить"
	//params - массив параметров. Используемые параметры:
		// table - который указывает, с какой таблицей работать [поля типа field и switch]
		// noactid - массив id с которыми нельзя делать действия (удалять, редактировать) [поле типа del, edit]
		// dt - текст сообщения перед удалиение, если не нужно это сообщение, нужно определить этот параметр пустым. Если параметр не определён, по-умолчанию используется "Удалить?" [поле типа del]
	array("k"=>"index","v"=>"#","type"=>"index", "nosort"=>1),
	array("k"=>"name","v"=>"Название группы (пользователей)"),
	array("k"=>"isadmin","v"=>"Администраторы", "edit"=>1, "type"=>"switch","params"=>array("table"=>"h_users_groups","field"=>"isadmin")),
	array("k"=>"edit","v"=>"Редактировать", "nosort"=>1, "edit"=>1, "type"=>"edit"),
	array("k"=>"delete","v"=>"Удалить", "nosort"=>1, "del"=>1,"type"=>"del"),
	);

//	3) определение порядка сортировки
$sql = $sql.$this->adm_get_order();

//	4) определение числа записей на страницу и текущей страницы - определение LIMIT для sql
$sql = $this->adm_init_navigation($sql);

//				ВЫБОР СПИСКА ИЗ БД
//	1) формирование массива для вывода
$row = $this->get_srt($sql);
foreach($row as $k=>$item)
{
	$sql = "SELECT COUNT(*) as `users` FROM `#h_users` WHERE `group`=".intval($item['id']);
	$u_row = $this->fetch_assoc($this->query($sql));
	$row[$k]['name'] = "<a href='/".$this->adm_path."/".$this->way."/users/?gid=".$row[$k]['id']."'>".$row[$k]['name']." (".$u_row['users'].")</a>";
}
//				ВЫВОД СПИСКА
//	0) вывод системных сообщений
$this->adm_show_sys_mes();

// вывод фильтров:
$filtr1 = $this->adm_showfiltr("Строк на страницу", "colonpage",$_SESSION['navig'][$this->ses_key]['colonpage'],array("10"=>10,"20"=>20,"50"=>50));
echo $filtr1;

//	1) вывод служебных кнопок и фильтров
$this->adm_show_add_button("добавить группу","","h");// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

//	2) вывод шапки таблицы
$this->adm_show_orderhat();

//	3) вывод тех полей таблицы, какие разрешено. Есть три типа полей: информационные, редактирующие и кнопка удаления.
$this->adm_show_table_fields($row, "h_users_groups");

$this->adm_navigation();

//	4) вывод служебных кнопок и фильтров
$this->adm_show_add_button("добавить группу","","h");// возможные параметры : 1 - изменяет надпись на кнопке. 2 - дополнение к классу кнопки, пристыковывается через "_" к основному классу "addbutton". 3 - Альтернативный класс, заменяется на него, в виде "addbutton_"{имя класса}, если навели курсор на кнопку. 4 - локатион, куда переводит кнопка, по-умолчанию это "edit"

echo $filtr1;
?>