<?php // v.2.0.
/* RedixCMS 4.0
Файл главного класса админки
*/

class adm_core extends core
{
        var $adm_path=""; //имя папки, в которой лежит админка
        var $way_url_get = ""; // запрошенный урл, без квери стринг и с $adm_path
        var $way = ""; // запрошенный урл, без квери стринг и без $adm_path
        var $pre_way = ""; // запрошенный урл, без квери стринг и без $adm_path на уровень выше
        var $way_ar = ""; // массив запрошенного урла, без квери стринг и без $adm_path

        var $can_view = 0; // текущей юзер может смотреть страницу
        var $can_edit = 0; // текущей юзер может редактировать запись
        var $can_delete = 0; // текущей юзер может удалять запись
        var $can_add = 0; // текущей юзер может добавлять запись
        var $can_edit_fields = array(); // массив полей БД, которые админ не может редактировать или добавлять. Те поля, которых нет в списке, по-умолчанию доступны для редакрирования и просмотра

        var $id = ""; // id записи, которую сейчас редактируем. Берётся либо из поста, либо из гета
        var $hat_ar = array(); // массив ячеек таблицы с инфой
        var $ses_key = ""; // ключ текущего раздела
        var $thisajax = 0; // флаг, установка его в 1, означает что работает Ajax и не надо некоторые действия выполнять
        var $parttitle = ""; // имя раздела
                var $hiddenstring = array(); // массив скрытых полей
        var $adm_com_config = array();

        var $adm_bookmarks = array(); // массив закладок для редактирования

        var $req_edit_strings = array(); // массив имён полей, обязательных для заполнения

        function adm_core($adm_path="", $isadmin=0)
        {
                $this->isadmin = intval($isadmin);
        	    $this->core(); // запускаем функцию из главного класса, которая определяет конфиг , префиксы и т.п.
        	    $this->adm_path = $adm_path; // сохраняем в классе имя папки, в которой лежит админка
                $this->way_url_get = join("/",$this->url_get); // соединяем урл в одну строку

                $url_get = $this->url_get;
                array_shift($url_get);
                $this->way_ar = $url_get;
                $this->way = join("/",$url_get); // соединяем урл в одну строку

                array_pop($url_get);
                $this->pre_way = join("/", $url_get); // соединяем урл в одну строку;

                $this->ses_key = $this->adm_get_ses_key($this->way);// ключ страницы для записи в сессию

                if(sizeof($_POST) && !($_POST['save'] || $_POST['app'])) $_POST['app']=1; // обрабатываем событие - нажатие на кнопку "сохранить" в редакторе
                return true;
        }

        function adm_get_ses_key($str="")
        {
                return md5($str);
        }

        function adm_mainmenu()
        {
                $mid = intval($_SESSION['user']['group']['id']);
                $infa = $this->core_get_tree("SELECT * FROM `#h_adm_menu` WHERE `mid`=".$mid." ORDER BY `sort` ASC");
                $_echo_menu = $this->adm_show_main_menu(0,0,$infa);
                include(DOCUMENT_ROOT.'/'.$this->adm_path.'/moduls/mmenu/mod_mmenu.php');
        }

        //VVVVVVVVVVVVVVVVVVVVVVV adm_show_main_menu FUNCTION VVVVVVVVVVVVVVVVVVVVV нарисовать дерево главного меню (сформировать яваскрипт код для вывода меню)
        function adm_show_main_menu($pid=0,$space=0,$infa=array(), $accadmparts = array())
        {
                if(!is_array($infa)) return "";
                $echo = "";
                foreach($infa[$pid] as $id => $item)
                {
                        foreach($item as $k=>$v) if(!$item[$k]) $item[$k] = "null"; elseif($k=="img") $item[$k] = "'<img src=\"".$v."\">'"; elseif($k!='id' && $k!='pid') $item[$k] = "'".$v."'";
                        $link = str_replace("{adm_path}",$this->adm_path,$item['link']);

                        $linkpath = split("\?",$item['link']);
                        $linkpath = "_pages/".preg_replace("/^'\/\{adm_path\}\/(.*)\/'*$/i","\\1",$linkpath[0]);
                        //echo "!".$item['link']."?";
                        if($item['link'] == "null" || (file_exists(DOCUMENT_ROOT."/".$this->adm_path."/".$linkpath.".php") && (!sizeof($accadmparts) || in_array($linkpath, $accadmparts))))
                        {
                                $img = str_replace("{cadmtemplate}",$this->config['adm_tpl'],$item['img']);
                                $img = str_replace("{adm_path}",$this->adm_path,$img);
                                $echo .= "[".$img.",".$item['text'].",".$link.",".$item['target'].",".$item['status'];
                                if(sizeof($infa[$item['id']]))// если есть подпункты меню вызываем эту функцию рекурсивно
                                {
                                        $echo .= ",\n";
                                        $echo .= $this->adm_show_main_menu($item['id'],$space+1,$infa);
                                }
                                $echo .= "],";
                        }//else echo "[null,null,null,null,null";
                        if($item['link']=="'split'") $echo .= "_cmSplit,";
                }
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_show_main_menu FUNCTION ^^^^^^^^^^^^^^^^^^^^^ нарисовать дерево главного меню (сформировать яваскрипт код для вывода меню)


        /* инклудит файл текущей страницы */
        function adm_showway()
        {
                $this->id = max(intval($_GET['id']),intval($_POST['id'])); // id записи, которую редактируем сейчас

                if(!$this->way) $this->way = $_SESSION['user']['group']['adm_index_page']; // если строка урла пустая, то это будет главная страница - index

                // запоминаем, что открыли страницу редактирования/добавления
                $is_edit = preg_match("/\/edit$/i",$this->way);

                // проверяем права доступа к этой странице
                $getway = preg_replace("/\/edit$/i","",$this->way); // убрали раздел "edit"
                $this->adm_get_rights($getway);

                // получаем массив полей, которыми админ не может управлят
                $sql = "SELECT * FROM `#h_users_rights` WHERE `pid`=".intval($row['id']);
                $res = $this->query($sql);
                while($row = $this->fetch_assoc($res)) $this->can_edit_fields[$row['way']] = $row;


                $file = DOCUMENT_ROOT."/".$this->adm_path."/_pages/".$this->way.".php"; // url файла, который нужно запустить сейчас
                $error_text = $this->core_echomui('adm_access2part_deny').".";
                if(file_exists($file) && !is_dir($file))//если существует файл
                {
                        // проверяем уровень доступа до этой странице, если доступа на просмотр нет, значит не инклудим рабочий файл
                        if($this->can_view)
                        {
                                // если не редактируем или редактируем, и есть права на это
                                if($is_edit && !$this->can_edit && $this->id) return array("text"=>$error_text, "error"=>1);

                                // если добавляем запись, но нет прав, то выдаём ошибку
                                if($is_edit && !$this->can_add && !$this->id) return array("text"=>$error_text, "error"=>1);

                                ob_start();
                                        include($file);
                                $_TEXT = ob_get_contents();// получили содержимое буфера
                                ob_end_clean ();// очистили буфер
                                return array("text"=>$_TEXT,"error"=>0);
                        }
                }

                return array("text"=>$error_text, "error"=>1);
        }

	function adm_get_rights($getway='')
	{
		$sql = "SELECT * FROM `#h_users_rights` WHERE `way`='".addslashes($getway)."' && `gid`=".intval($_SESSION['user']['group']['id']);
        $res = $this->query($sql);
		$row = $this->fetch_assoc($res);
        $this->can_view = $row['view']; // текущей юзер может смотреть страницу
        $this->can_edit = $row['edit']; // текущей юзер может редактировать запись
        $this->can_delete = $row['delete']; // текущей юзер может удалять запись
        $this->can_add = $row['add']; // текущей юзер может добавлять запись
	}

        //VVVVVVVVVVVVVVVVVVVVVVV SHOWPARTTITLE FUNCTION VVVVVVVVVVVVVVVVVVVVV выводит титле раздела в админке
        function adm_showparttitle($title="", $separ=" / ", $linkarray = array())
        {
                $title = split(" / ",$title);
                if(!sizeof($linkarray)) $linkarray = $this->way_ar;

                $link = $this->adm_path."/";
                foreach($title as $k=>$item)
                {
                        $link .= $linkarray[$k]."/";

                        $item = trim($item);

                        // заменяем конструкцию {edittext} на "редактировать" или "добавить"
                        $edittext = ($this->id?$this->core_echomui('adm_itemedit'):$this->core_echomui('adm_itemadd'));
                        $item = str_replace("{edittext}", $edittext, $item);

                        if($k < (sizeof($title)-1)) $title[$k] = "<a href='/".$link."' class='parttitle'>".$item."</a>";
                        else  $title[$k] = $item;
                }
                $title = join($separ,$title);
                echo "<div class=\"parttitle\">".$title."</div>";
                $this->parttitle = $title;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SHOWPARTTITLE FUNCTION ^^^^^^^^^^^^^^^^^^^^^ выводит титле раздела в админке

        //VVVVVVVVVVVVVVVVVVVVVVV ADD_SYS_MES FUNCTION VVVVVVVVVVVVVVVVVVVVV добавить в список системное сообщение
        function adm_add_sys_mes($mes = "", $mestype="")
        {
                if($mes)
                {
                        $_SESSION['adm_sys_mes'][] = array('text'=>$mes, "type"=>$mestype);
                }
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADD_SYS_MES FUNCTION ^^^^^^^^^^^^^^^^^^^^^ добавить в список системное сообщение

        //VVVVVVVVVVVVVVVVVVVVVVV SHOW_SYS_MES FUNCTION VVVVVVVVVVVVVVVVVVVVV показать системные сообщение
        function adm_show_sys_mes()
        {
                if(is_array($_SESSION['adm_sys_mes']) && sizeof($_SESSION['adm_sys_mes']))
                {
						echo '<script>$(function() {setTimeout(hidesysmes,5000);});function hidesysmes(){var selectedEffect = "highlight";var options = {};$( "#sysmes" ).hide( selectedEffect, options, 1000);}</script>';
						echo '<div id="sysmes">';
                        foreach($_SESSION['adm_sys_mes'] as $item)
                        {
                                echo "<div class=\"sys_mes".($item['type']?"_".$item['type']:"")."\">".$item['text']."</div>";
                        }
						echo '</div>';
                        if(!$this->will_reload) $_SESSION['adm_sys_mes'] = array();
                }
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SHOW_SYS_MES FUNCTION ^^^^^^^^^^^^^^^^^^^^^ показать системные сообщение

        function adm_show_add_button($message="", $dopclass="", $alterclass="", $location="edit")
        {
                if($this->can_add)
                {
                        if(!$message) $message = $this->core_echomui('adm_buttonadd');

                        $c_class = "addbutton".($dopclass?"_".$dopclass:"");
                        if($alterclass) $switchclass = " onmouseover=\"this.className='addbutton_".$alterclass."'\" onmouseout=\"this.className='".$c_class."'\"";

                        echo "<div><input type='button' value='".$message."'".$switchclass." class='".$c_class."' onclick=\"location.href='".$location."'\"></div>";
                }
        }

        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_EDIT_TOOLBAR FUNCTION VVVVVVVVVVVVVVVVVVVVV выводит основной тулбар при редактировании
        function adm_show_edit_toolbar($save=array("value"=>""), $app=array("value"=>""), $cancel=array("value"=>""))
        {
                if(is_array($save) && !$save['value']) $save['value'] = $this->core_echomui('adm_buttonsave');
                if(is_array($app) && !$app['value']) $app['value'] = $this->core_echomui('adm_buttonapplay');
                if(is_array($cancel) && !$cancel['value']) $cancel['value'] = $this->core_echomui('adm_buttoncancel');
                $echo ="";
				$echo .="<div class='toolbar' id='toolbar'>";
                if($save['value'])
                {
                        $c_class = ($save['class']?$save['class']:"savebutton");
                        if($save['alter_class']) $switchclass = " onmouseover=\"this.className='".$save['alter_class']."'\" onmouseout=\"this.className='".$c_class."'\"";
                        $echo .= "<div class=\"div_".$c_class."\"><input type='submit' name='save' value='".$save['value']."' class='".$c_class."' ".$switchclass." onclick=\"".$save['script']."return checkform('editform');\"></div>";
                }
                if($app['value'])
                {
                        $c_class = ($app['class']?$app['class']:"appbutton");
                        if($app['alter_class']) $switchclass = " onmouseover=\"this.className='".$app['alter_class']."'\" onmouseout=\"this.className='".$c_class."'\"";
                        $echo .= "<div class=\"div_".$c_class."\"><input type='submit' name='app' value='".$app['value']."' class='".$c_class."' ".$switchclass." onclick=\"".$app['script']."return checkform('editform');\"></div>";
                }
                if($cancel['value'])
                {
                        $c_class = ($cancel['class']?$cancel['class']:"cancelbutton");
                        if($cancel['alter_class']) $switchclass = " onmouseover=\"this.className='".$cancel['alter_class']."'\" onmouseout=\"this.className='".$c_class."'\"";

                        $location = $this->way_ar;
                        array_pop($location);
                        $location = join("/", $location);
                		if($location) $location = $location."/";
                        $echo .= "<div class=\"div_".$c_class."\"><input type='button' name='cancel' value='".$cancel['value']."' class='".$c_class."' ".$switchclass." onclick=\"".$cancel['script']."location.href='/".$this->adm_path."/".$location."'\"></div>";
                }
				$echo .="<div class='clear'></div></div>";
//                if($cancel) $echo .= "<td><input type='button' name='cancel' value='".$cancel."' class='cancel_button' onclick=\"location.href='".$this->prev_way."'\">";
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_EDIT_TOOLBAR FUNCTION ^^^^^^^^^^^^^^^^^^^^^ выводит основной тулбар при редактировании

        //VVVVVVVVVVVVVVVVVVVVVVV INIT_NAVIGATION FUNCTION VVVVVVVVVVVVVVVVVVVVV инициилизирует навигацию по страницам, определяет сколько страниц всего, генерирует LIMIT для SQL запроса
        function adm_init_navigation($sql2col="", $colonpage = 10)
        {
                // если не передан sql запрос, прерываем работу
                if(!$sql2col) return;

                // если не установлено сколько выводить на страницу и есть это в конфиге, то берём данные из конфига
                if(!$_SESSION['navig'][$this->ses_key]['colonpage'] && intval($this->adm_com_config['config']['colonpage_def'])) $_SESSION['navig'][$this->ses_key]['colonpage'] = intval($this->adm_com_config['config']['colonpage_def']);
                // определяем количество записей выводимых на страницу
                if(intval($_SESSION['navig'][$this->ses_key]['colonpage'])) $colonpage = intval($_SESSION['navig'][$this->ses_key]['colonpage']);
                // если не известно по сколько записей выводить на страницу, прерываем работу
                if(!intval($colonpage)) return;

                if(intval($_GET['page'])>0) $page = intval($_GET['page']); else $page = intval($_SESSION['navig'][$this->ses_key]['page']);
                if(!$page) $page = 1;
                if($page>$_SESSION['navig'][$this->ses_key]['colpage'] && $_SESSION['navig'][$this->ses_key]['colpage']) $page = $_SESSION['navig'][$this->ses_key]['colpage'];

                $_SESSION['navig'][$this->ses_key]['page'] = $page;

                // вытягиваем список необходимых записей из базы
                // определяем всё, что нужно для постраничной навигации
                $from = ($page-1)*$colonpage;
                $limit = " LIMIT ".$from.",".$colonpage;

                $res2col = $this->query($sql2col);// сделали запрос с оригинальным скулом
                $colnotes = $this->num_rows($res2col);// определили сколько записей вернулось
                $colpage = ceil($colnotes/$colonpage);// определили сколько страниц

                // формируем новый скул запрос с учётом номера страницы и количества записей на страницу
                $sql = $sql2col.$limit;

                $_SESSION['navig'][$this->ses_key]['colpage'] = $colpage;
                $_SESSION['navig'][$this->ses_key]['colnotes'] = $colnotes;
                $_SESSION['navig'][$this->ses_key]['colonpage'] = $colonpage;
                return $sql;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF INIT_NAVIGATION FUNCTION ^^^^^^^^^^^^^^^^^^^^^ инициилизирует навигацию по страницам, определяет сколько страниц всего, генерирует LIMIT для SQL запроса

        //VVVVVVVVVVVVVVVVVVVVVVV NAVIGATION FUNCTION VVVVVVVVVVVVVVVVVVVVV выводит навигацию по страницам
        function adm_navigation($link="",$tpl = "navig.php")
        {/*
                в переменной link передаём ссылку по которой будет осуществляться переход,
                на место [pagenum] в ссылке будет подставляться номер страницы на которую надо перейти
        */
                if(!$link) $link = "/".$this->adm_path."/".$this->way."/?page=[pagenum]";

                $curpage = intval($_SESSION['navig'][$this->ses_key]['page']);
                $colpage = intval($_SESSION['navig'][$this->ses_key]['colpage']);
                if(!$curpage)        $curpage = 1;
                if(!$colpage || $colpage == 1)        return;
                if(!$link){echo "<font color='#ff0000'>can't build navigation</font>";return false;}

                $dir = DOCUMENT_ROOT.'/'.$this->adm_path.'/template/'.$this->config['adm_tpl'].'/avtorized_1/extrahtml/';
                if(file_exists($dir.$tpl)) include($dir.$tpl);
                else echo "<font color='#ff0000'>can't build navigation, template doesn't exist</font>";return false;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF NAVIGATION FUNCTION ^^^^^^^^^^^^^^^^^^^^^ выводит навигацию по страницам

        function adm_set_filter_params($filterpararr=array())
        {
                $colonpage = intval($_GET['colonpage']);
                $order = $_GET['order'];
                $odd = $_GET['odd'];

                if($colonpage) {$_SESSION['navig'][$this->ses_key]['colonpage'] = $colonpage;$reload = 1;}
                if($order) {$_SESSION['order'][$this->ses_key]['order'] = $order;$reload = 1;}
                if($odd) {$_SESSION['order'][$this->ses_key]['odd'] = $odd;$reload = 1;}
                if(sizeof($filterpararr))
                {
                        foreach($filterpararr as $item)
                        {
                                if(!isset($item['name']) || !isset($item['val'])) continue;
                                $_SESSION['adm_filter'][$this->ses_key][$item['name']]=$item['val'];
                                $reload = 1;
                        }
                }
                if($reload) $this->reload("http://".HTTP_HOST."/".$this->adm_path."/".$this->way."/");
        }


        //VVVVVVVVVVVVVVVVVVVVVVV GET_ORDER FUNCTION VVVVVVVVVVVVVVVVVVVVV генерируем условие для выборки из БД с учётом сортировки по какому-то полю
        function adm_get_order($def_order="id", $def_odd='asc', $dopcode="")
        /*
                $order - поле по которому делать сортировку,
                $odd - направление сортировки - 0 - по возрастанию, 1 - по убыванию,
                $def_order - если не указано $order или указан неверный, которого нет в массиве $hat_ar, то используем это поле для сортировки как по умолчанию
                $def_odd - направление сортировки по-умолчанию
        */
        {
                // получаем массив ключей из массива шапки, по которым можно делать сортировку
                foreach($this->hat_ar as $item)
                {
                        $k = $item['k'];
                        $v = $item['v'];
                        $nosort = intval($item['nosort']);
                        $edit = intval($item['edit']);
                        $del = intval($item['del']);

                        // если нельзя редактировать записи на странице, то не добавляем их в массив
                        if($edit && !$this->can_edit) continue;

                        // если нельзя удалять записи, а поле с ключём "%delete" то не выводим это поле
                        if($del && !$this->can_delete) continue;

                        // если вначале ключа стоит "%", то не делаем этот ключ ссылкой, значит не добавляем в массив
                        if($nosort) continue;

                        $hat_ar[] = $k;
                }
                // получаем направление сортировки
                $odd = $_SESSION['order'][$this->ses_key]['odd'];
                if(!$odd) {$odd = $def_odd;$_SESSION['order'][$this->ses_key]['odd'] = $def_odd;}

                // получаем поле сортировки
                $order = $_SESSION['order'][$this->ses_key]['order'];
                if(!$order || !in_array($order, $hat_ar)) {$order = $def_order;$_SESSION['order'][$this->ses_key]['order'] = $def_order;}

                $order_cond = " ORDER BY `".$order."` ".$odd.$dopcode;

                return $order_cond;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF GET_ORDER FUNCTION ^^^^^^^^^^^^^^^^^^^^^ генерируем условие для выборки из БД с учётом сортировки по какому-то полю

        function adm_show_table_fields($row=array(), $dbtable="")
        {
                $hat_ar = $this->hat_ar;
                // стартовый индекс
                $i = ($_SESSION['navig'][$this->ses_key]['page']-1)*$_SESSION['navig'][$this->ses_key]['colonpage'];
                if(!is_array($row)) $row = array();
                foreach($row as $item)
                {
                        $i++;
						$itemid = $item['id'];
                        if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";
                        echo "<tr class='".$zebra_class."' ".($this->classes_switch($zebra_class,'f_hover')).">";
                        foreach($hat_ar as $hat_item)
                        {
                                $k = $hat_item['k'];
                                $v = $hat_item['v'];
                                $nosort = intval($hat_item['nosort']);
                                $edit = intval($hat_item['edit']);
                                $del = intval($hat_item['del']);
                                $type = ($hat_item['type']?$hat_item['type']:"text");
                                $hat_params = $hat_item['params'];

                                // если нельзя редактировать записи на странице, то не выводим эти поля
                                if($edit && !$this->can_edit) continue;

                                // если нельзя удалять записи, а поле с ключём "%delete" то не выводим это поле
                                if($del && !$this->can_delete) continue;

                                switch($type)
                                {
									case "plaintext":
										$item[$k] = $this->adm_show_editinput($k, $item[$k], $itemid, "width:auto;height:18px;",$this->adm_com_config['config']['tbl'], "form","",1);
									break;
                                        case "index":
                                                $item[$k] = $i;
                                        break;
                                        case "switch":
                                                $title = ($item[$k]?$this->core_echomui('adm_switchno'):$this->core_echomui('adm_switchyes'));
                                                $item[$k] = "<a href=\"/".$this->adm_path."/".$this->way."/?".$k."=".$itemid."\" onclick=\"document.getElementById('sw".$this->adm_com_config['config']['tbl'].$k."-".$itemid."').src='/templates/_common_images/loader.gif';loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=".$itemid."&dbtable=".$this->adm_com_config['config']['tbl']."&field=".$k."&alt=".$alt."');return false;\"><img src=\"/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/ticks/tick_".$item[$k].".png\" border=\"0\" title=\"".$title."\" id=\"sw".$this->adm_com_config['config']['tbl'].$k."-".$itemid."\"></a>";
                                        break;

                                        case "datetime":
                                                if($item[$k]) $item[$k] = date($hat_params['format'], $item[$k]);
                                                else $item[$k] = "-";
                                        break;

                                        case "field":
                                                $item[$k] = "<input type='text' name='".$k."[".$itemid."]' value='".$item[$k]."' ".($this->adm_inputclass("width:50px;text-align:center;height:15px")).">";
                                        break;

                                        case "img":
                                                if($hat_params['fpath2'])
                                                {
                                                        $linkstart = "<a href=\"http://".HTTP_HOST."/images/".$hat_params['fpath2']."/".$item[$k]."\" target=_blank>";
                                                        $linkend = "</a>";
                                                }else
                                                {
                                                        $linkstart = "";
                                                        $linkend = "";
                                                }
												if(is_file(DOCUMENT_ROOT."/images/".$hat_params['fpath1']."/".$item[$k]))
												{
												    $item[$k] = $linkstart."<img src=\"http://".HTTP_HOST."/images/".$hat_params['fpath1']."/".$item[$k].'?'.($hat_params['w']?'w='.$hat_params['w']:'').($hat_params['h']?'&h='.$hat_params['w']:'')."\" ".($hat_params['w']?"width='".$hat_params['w']."'":"")." ".($hat_params['h']?"height='".$hat_params['h']."'":"")." border=0 />".$linkend;
												}else $item[$k] = '-';
                                        break;

                                        case "lid":
                                                $item[$k] = "<a href=\"http://".HTTP_HOST."/".($this->adm_com_config['url']?$this->adm_com_config['url']."/":"").$item[$k]."/\" target=_blank>".$item[$k]."</a>";
                                        break;

                                        case "del":
                                                // определяем можно ли удалять эту запись
                                                if($hat_params['noactid'] && !is_array($hat_params['noactid']))
                                                {
                                                        $noactid = split(",", $hat_params['noactid']);
                                                        $hat_params['noactid'] = array();
                                                        foreach($noactid as $v) $hat_params['noactid'][] = intval(trim($v));
                                                }
                                                if(!is_array($hat_params['noactid'])) $hat_params['noactid'] = array();
                                                if(in_array($itemid, $hat_params['noactid'])) $actid = 0;
                                                else $actid = $itemid;

                                                if(!isset($hat_params['dt'])) $hat_params['dt'] = $this->core_echomui('adm_itemdelquestion');
                                                else $hat_params['dt'] = $this->core_echomui($hat_params['dt']);

                                                // определяем сообщение перед удалением
                                                $item[$k] = $this->adm_show_delbutton($actid,$hat_params['dt']);
                                        break;

                                        case "edit":
                                                if($hat_params['noactid'] && !is_array($hat_params['noactid']))
                                                {
                                                        $noactid = split(",", $hat_params['noactid']);
                                                        $hat_params['noactid'] = array();
                                                        foreach($noactid as $v) $hat_params['noactid'][] = intval(trim($v));
                                                }
                                                if(!is_array($hat_params['noactid'])) $hat_params['noactid'] = array();
                                                if(in_array($itemid, $hat_params['noactid'])) $actid = 0;
                                                else $actid = $itemid;

                                                $item[$k] = $this->adm_show_editbutton($actid);
                                        break;

                                        case "linked":
                                                $sql = "SELECT `".addslashes(trim($hat_params['linkfield']))."`, `".addslashes(trim($hat_params['showfield']))."` FROM `#".addslashes(trim($hat_params['tbl']))."` WHERE `".addslashes(trim($hat_params['linkfield']))."`='".addslashes($item[$k])."'";
                                                $linked_res = $this->query($sql);

                                                if($this->num_rows($linked_res))
                                                {
                                                        $linked_row = $this->fetch_assoc($linked_res);
                                                        $item[$k] = $linked_row[trim($hat_params['showfield'])];
                                                }else
                                                {
                                                        $item[$k] = trim($hat_params['defval']);
                                                }
                                        break;

                                        case 'fromselect':
                                                $values = $this->adm_get_select_array($hat_params['selectid']);
												$item[$k] = $this->adm_show_editselect($k, $values[$item[$k]], $itemid, $values, "", $this->adm_com_config['config']['tbl'], "form", "", "", 1, "",$hat_params['selectid']);
                                        break;
                                }
                                if($hat_params['linkto'])
                                {
                                        $hat_params['linkto'] = str_replace("{thisid}", $itemid, $hat_params['linkto']);
                                        $hat_params['linkto'] = str_replace("{adm_path}", $this->adm_path, $hat_params['linkto']);

                                        $item_keys = array_keys($item);
                                        foreach($item_keys as $key)        $hat_params['linkto'] = str_replace("{".$key."}", $item[$key], $hat_params['linkto']);

                                        $linkstart = "<a href=\"".$hat_params['linkto']."\">";
                                        $linkend = "</a>";
                                }else
                                {
                                        $linkstart = "";
                                        $linkend = "";
                                }

                                echo "<td calss=\"td-".(++$tdi)."\"".($hat_params['style']?" style=\"".$hat_params['style']."\"":"").">".(($item['this_space'] && $hat_params['spacer'])?str_repeat($hat_params['spacer'],$item['this_space']):"").$linkstart.$item[$k].$linkend;
                                if($hat_params['shownumber'])
                                {
                                        list($fname, $tbl) = split("\|", $hat_params['shownumber'], 2);
                                        if($fname && $tbl)
                                        {
                                                $sql = "SELECT `".addslashes(trim($fname))."` FROM `#".addslashes(trim($tbl))."` WHERE `".addslashes(trim($fname))."`=".intval($itemid);
                                                echo "&nbsp;(".intval($this->num_rows($this->query($sql))).")";
                                        }
                                }
                        }
                }

                echo "</table></form>";

                if(!sizeof($row)) echo "<div class='nostrings'>".$this->core_echomui('adm_noitems')."</div>";
        }


        //VVVVVVVVVVVVVVVVVVVVVVV SHOW_DELBUTTON FUNCTION VVVVVVVVVVVVVVVVVVVVV показывает кнопку удаления
        function adm_show_delbutton($detele="", $mes="")
        {
                if(!$mes) $mes = $this->core_echomui('adm_itemdelquestion');
                $echo = "";
                if(!$detele)
                {
                        $echo = "<img src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/delete_0.gif' border='0' title='".$this->core_echomui('adm_itemdelcanttooltip')."'>";
                }else{
                        if($mes) $message = " onclick=\"return confirm('".$mes."');\"";
                        $echo = "<a href='/".$this->adm_path."/".$this->way."/?del=".$detele."'".$message.">
                        <img src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/delete_1.gif' border='0' title='".$this->core_echomui('adm_itemdeltooltip')."'></a>";
                }
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SHOW_DELBUTTON FUNCTION ^^^^^^^^^^^^^^^^^^^^^ показывает кнопку удаления

        //VVVVVVVVVVVVVVVVVVVVVVV SHOW_EDITBUTTON FUNCTION VVVVVVVVVVVVVVVVVVVVV показывает кнопку редактирования
        function adm_show_editbutton($edit="")
        {
                $echo = "";
                if(!$edit)
                {
                        $echo = "<img src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/edit_0.gif' border='0' title='".$this->core_echomui('adm_itemeditcanttooltip')."'>";
                }else{
                        $echo = "<a href='/".$this->adm_path."/".$this->way."/edit/?id=".$edit."'>
                        <img src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/edit_1.gif' border='0' title='".$this->core_echomui('adm_itemedittooltip')."'></a>";
                }
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SHOW_EDITBUTTON FUNCTION ^^^^^^^^^^^^^^^^^^^^^ показывает кнопку редактирования

        //VVVVVVVVVVVVVVVVVVVVVVV adm_show_userchecklist FUNCTION VVVVVVVVVVVVVVVVVVVVV
        function adm_show_userchecklist($c_uid=0, $userid_fname="", $acctype=0, $accgid = array(), $showfields = array())
        {
                /*
                $c_uid - id текущего юзера
                $userid_fname - имя поля таблицы БД в котором хранится id ользователя
                $acctype - тип доступа к просмотру списка юзеров для выбора пользователя.
                        0 - показывать все группы;
                        1 - показывать только пользователей из групп указаннх в $accgid;
                        2 - показывать пользователей из групп НЕ указаннх в $accgid;
                $accgid - массив id прупп пользователей
                $showfields - имена полей БД таблицы с юзерами, которые показывать в списке юзеров. Содержимое массива должно быть представлено как: fieldname|rusname,fieldname2|rusname2. fieldname - имя поля в БД, rusname - заголовок таблицы
                */
                $echo = "";

                // получаем имя текущего юзера
                $echo .= "<div id='cuserfield' style='float:left'>";
                if($c_uid)
                {
                        $sql = "SELECT * FROM `#h_users` WHERE `id`=".intval($c_uid);
                        $row = $this->fetch_assoc($this->query($sql));
                        $username = array($row['family'], $row['name'], $row['otchestvo']);
                        $echo .= "<a href='/".$this->adm_path."/users_groups/users/edit/?id=".$c_uid."' target=_blank title='".$this->core_echomui('adm_userchecklist_showuser')."'>".join(" ", $username)." [".$this->core_echomui('adm_userchecklist_login').": ".$row['login']."]</a>";
                }else
                {
                        $echo .= "<i>".$this->core_echomui('adm_userchecklist_usernotset')."</i>";
                }
                $echo .= "</div>&nbsp;";
                if($userid_fname) $echo .= $this->adm_show_hidden($userid_fname, $c_uid, "0", "hid-".$userid_fname);
                $accgidar = join(",",$accgid);

                $echo .= " <a href='#' style='color:#000' onclick=\"loadXMLDoc('/ajax-index.php?isadm=1&page=chuser&cuid=".intval($c_uid)."&acctype=".intval($acctype)."&accgid=".$accgidar."&fieldname=".$userid_fname."&showfields=".
urlencode(join(",",$showfields))."', 'chuserfield');return false;\">".$this->core_echomui('adm_userchecklist_setuser')."</a><div id='chuserfield'></div>";
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_show_userchecklist FUNCTION ^^^^^^^^^^^^^^^^^^^^^

        //VVVVVVVVVVVVVVVVVVVVVVV adm_show_usermailto FUNCTION VVVVVVVVVVVVVVVVVVVVV
        function adm_show_mailfromsite($uid=0) // если надо показать письма у которых uid = 0 нужно этот параметр передать как "all"
        {
                if(!$uid) return "<i>".$this->core_echomui('adm_mailfromsite_usernotset')."</i>";
                $uid = intval($uid);
                $echo = "";
                $sql = "SELECT * FROM `#h_mailfromsite` WHERE `touid`=".$uid." ORDER BY `date` DESC";
                $res = $this->query($sql);
                if(!$this->num_rows($res)) $echo .= $this->core_echomui('adm_mailfromsite_nomails');
                else{
                $echo .= "<table class='f_table'><th>".$this->core_echomui('adm_mailfromsite_from')."<th>".$this->core_echomui('adm_mailfromsite_to')."<th>".$this->core_echomui('adm_mailfromsite_thema')."<th>".$this->core_echomui('adm_mailfromsite_date')."";
                while($row = $this->fetch_assoc($res))
                {
                        if($zebra_class == "zebra_white") $zebra_class = "zebra_grey"; else $zebra_class = "zebra_white";
                        $echo .= "<tr onclick=\"if(document.getElementById('sendmail-".$row['id']."').style.display=='none') document.getElementById('sendmail-".$row['id']."').style.display='block'; else document.getElementById('sendmail-".$row['id']."').style.display='none'\" style='cursor:pointer' class='".$zebra_class."' ".($this->classes_switch($zebra_class,'f_hover')).">";
                        $echo .= "<td>".str_replace("<","&lt;",$row['fromemail']);
                        $echo .= "<div id=\"sendmail-".$row['id']."\" style='display:none;position:absolute;background:#fff;text-align:left;border:1px solid #000;padding:5px;'>".$row['text']."</div>";

                        $echo .= "</td>";
                        $echo .= "<td>".$row['toemail'].(!$row['sended']?"<span style='color:#f00;font-weight:bold'> (".$this->core_echomui('adm_mailfromsite_senderror').")</span>":"")."</td>";
                        $echo .= "<td>".$row['tema']."</td>";

                        if(date("d", $row['date'])==date("d", time()) && date("m", $row['date'])==date("m", time()) && date("Y", $row['date'])==date("Y", time()))
                                $echo .= "<td>".date("H:i", $row['date'])."</td>";
                        else
                                $echo .= "<td>".date("d.m.Y H:i", $row['date'])."</td>";

                        $echo .= "</tr>";
                }
                $echo .= "</table>";
                }
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_show_userchecklist FUNCTION ^^^^^^^^^^^^^^^^^^^^^



        function adm_showsaveico($table="", $thisorder="", $filename="filesave.png")
        {
                if(!$table) return false;
                $echo = "";
                if($_SESSION['order'][$this->ses_key]['order']==$thisorder) $needreload = "&reload=1"; // если стоит сортировка по этому полю, то после сохранения, нужно обновить страницу
                $echo = "</a>&nbsp;<input type='image' name='save' value='".$this->core_echomui('adm_showsaveico_save')."' src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/".$filename."' onclick=\"loadXMLDoc('/ajax-index.php?isadm=1&page=savex&field=".$thisorder."&dbtable=".$table.$needreload."','','form');return false;\">";
                return $echo;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV SAVE_X FUNCTION VVVVVVVVVVVVVVVVVVVVV обновить информацию в конкретном поле в большом количестве записей
        function adm_save_x($up_field = "", $isintup = 0, $cond_field = "", $isintcon = 0, $table = "", $dopcond = "", $datas = array())
        {
                if(!$this->can_edit && !$this->thisajax) return false;
                if(!$up_field || !$cond_field || !$table || !sizeof($datas)) return false;

                foreach ($datas as $cond_val=>$up_val)
                {
                        if($isintup) $up_val = intval($up_val);
                        if($isintcon) $cond_val = intval($cond_val);

                        $sql = "UPDATE `".$table."` SET `".$up_field."`='".$up_val."' WHERE `".$cond_field."`='".$cond_val."'";
                        $res = $this->query($sql);

                        if(!$res) return false;
                }

                if(!$this->thisajax)
                {
                        $this->adm_add_sys_mes($this->core_echomui('adm_savex_savemes'));
                        $this->reload();
                }
                return true;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SAVE_X FUNCTION ^^^^^^^^^^^^^^^^^^^^^ обновить информацию в конкретном поле в большом количестве записей

        //VVVVVVVVVVVVVVVVVVVVVVV INPUTCLASS FUNCTION VVVVVVVVVVVVVVVVVVVVV присваивает классы для инпута
        function adm_inputclass($style="")
        {
                if($style)
                {
                        $style = "style=\"".$style."\"";
                }
                return " class=\"input_normal\" onblur=\"this.className='input_normal'\" onfocus=\"this.className='input_focus'\" ".$style;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF INPUTCLASS FUNCTION ^^^^^^^^^^^^^^^^^^^^^ присваивает классы для инпута

        //VVVVVVVVVVVVVVVVVVVVVVV SWITCH_ROW FUNCTION VVVVVVVVVVVVVVVVVVVVV изменит содержание поля БД по на следующее по сиску
        function adm_switch_row($id=0, $table = "", $dopcond = "", $field = "", $stats="")
        /*
                id - записи в БД.
                table - таблица БД с которой работаем.
                dopcond - дополнительное условие выборки
                field - поле, содержание которого надо изменить
                stats - строка статусов вида "1,2,3" (через запятую)
                1. Выдёргиваем из table запись с id и условием dopcond
                2. проверяем содержание поля field
                3. изменяем содержание поля fielв на следующий статус
        */
        {
                if(!$this->can_edit && !$this->thisajax) return "exit";
                if(!$id || !$table || !$field) return false;// если не передан id или имя таблицы или поле БД возвращаем ложь
                $stats = split(",",$stats);// поделили строку stats по запятой
                //1
                $sql = "SELECT `".$field."` FROM `".$table."` WHERE `id`=".$id." ".$dopcond;
                $res = $this->query($sql);
                $cur_stat = $this->fetch_assoc($res);
                $cur_stat = $cur_stat[$field];

                //2
                $key = array_search($cur_stat,$stats);
                if($key===false) return false;

                //3
                // если текущий статус является последним в списке статусов
                if($key == (sizeof($stats)-1)) $key = 0;
                else $key++;

                $new_stat = $stats[$key];
                $sql = "UPDATE `".$table."` SET `".$field."`='".$new_stat."' WHERE `id`=".$id." ".$dopcond;
                $res = $this->query($sql);
                if($res && !$this->thisajax)
                {
                        $this->adm_add_sys_mes($this->core_echomui('adm_switchrow_savemes'));
                        $request_uri = preg_replace("/([\?\&])".$field."=\d+/i","\\1",REQUEST_URI);
                        $request_uri = str_replace("?&","?",trim(trim($request_uri,"&"),"?"));
                        $url = "http://".HTTP_HOST.$request_uri;
                        $this->reload($url);
                        return $new_stat;
                }elseif($res) return $new_stat;
                else return false;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SWITCH_ROW FUNCTION ^^^^^^^^^^^^^^^^^^^^^ изменит содержание поля БД по на следующее по сиску


        function classes_switch($class1,$class2)
        {
                $echo = "";
                $echo = "class=\"".$class1."\" onmouseover=\"this.className='".$class2."'\" onmouseout=\"this.className='".$class1."'\"";
                return $echo;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV SHOW_ORDERHAT FUNCTION VVVVVVVVVVVVVVVVVVVVV рисует заголовок таблицы контента
        function adm_show_orderhat($baseway="",$sortquery=""){
                $hat_ar = $this->hat_ar;
                if(!$baseway) $baseway = "/".$this->adm_path."/".$this->way."/?";

                echo "<form name='form' id='form' method='post' enctype='multipart/form-data'><table class='f_table'>";

                foreach($hat_ar as $item)
                {
                        $k = $item['k'];
                        $v = $item['v'];
                        $nosort = intval($item['nosort']);
                        $edit = intval($item['edit']);
                        $del = intval($item['del']);
                        $type = $item['type'];
                        $hat_params = $item['params'];
                        if(!is_array($hat_params)) $hat_params = array();

                        // если нельзя редактировать записи на странице, то не выводим эти поля
                        if($edit && !$this->can_edit) continue;

                        // если нельзя удалять записи, а поле с ключём "%delete" то не выводим это поле
                        if($del && !$this->can_delete) continue;
                        if($type=="field") $v = $v.$this->adm_showsaveico($this->adm_com_config['config']['tbl'],$k);
						if($k=='sort') $v = $v."<a href='/".$this->adm_path."/sort_items/?comid=".$this->adm_com_config['id']."&".$sortquery."&ref=".$baseway."' class='hatsortlink'>sort</a>";
                ?>
                <th><nobr><?if(!$nosort){?><a href="<?=$baseway?>order=<?=$k?>&odd=<?if($_SESSION['order'][$this->ses_key]['odd']=="desc" || $_SESSION['order'][$this->ses_key]['order']!=$k){?>asc<?}else{?>desc<?}?><?echo SID?"&".SID:""?>"><?}?><?=$v?></a><?if ($_SESSION['order'][$this->ses_key]['order']==$k){?><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/<?if($_SESSION['order'][$this->ses_key]['odd']=="desc"){?>s_desc.png<?}else{?>s_asc.png<?}?>" border=0><?}?></nobr>
<?                }
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SHOW_ORDERHAT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ рисует заголовок таблицы контента

        //VVVVVVVVVVVVVVVVVVVVVVV GET_STR FUNCTION VVVVVVVVVVVVVVVVVVVVV взять из базы строку
        function get_srt($sql)
        {
                if(!$sql) return false;
                if($this->adm_com_config['config']['pidfield'])
                {
                        $infa = $this->core_get_tree($sql);
                        $row = $this->core_get_tree_keys(0, array(), $infa, 0, 1);
                }else
                {
                        $res = $this->query($sql);
                        $row = array();
                        while($rrow = $this->fetch_assoc($res))
                        {
                                $row[] = $rrow;
                        }
                }

                return $row;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF GET_STR FUNCTION ^^^^^^^^^^^^^^^^^^^^^ присваивает слассы для инпута

        //VVVVVVVVVVVVVVVVVVVVVVV DEL_ROW FUNCTION VVVVVVVVVVVVVVVVVVVVV удалит запись из БД
        function adm_del_row($id=0, $table = "", $dopcond = "", $trashname="", $rekursfield="",$needheaderaction=1)/*
        $table - таблица БД из которой надо удалить
        $dopcond - дополнительные условия по которым удалять
        $trashname - имя поля БД в котором хранится имя записи, чтобы поместить его в корзину. Если пусто, то в корзину не поместится
        $rekursfield - поле, в котором хранится pid записи. Если задано это поле, то удаление проходит рекурсивно
        */
        {
                if(!$this->can_delete) return false;
                $id = intval($id);
                if(!$id)
                {
                        if(intval($_GET['del'])) $id = intval($_GET['del']);
                        elseif(is_array($_POST['del'])) $id = join(",", $_POST['del']);
                }
                if(!$id || !$table) return false;// если не передан id или имя таблицы возвращаем ложь
                $ids = split(",",$id);// поделили строку id по запятой, если передан один ид, то в массиве будет 1 значение
                foreach($ids as $k=>$item)// для каждоко переданного ида
                {
                        $ids[$k] = intval($item);// делаем интвал, чтобы не было ошибок
                }
                $id = join(",",$ids);// лепим назад в строку через ","
                if(!$id) return false;// если не передан id возвращаем ложь


                /*                Еслм пользуемся корзиной        */
                if($this->config['use_trash'] && $trashname)
                {
                        // забираем записи, которые будем сейчас удалять и помещаем их в корзину
                        $sql = "SELECT * FROM `".$table."` WHERE `id` IN (".$id.") ".$dopcond;
                        $res = $this->query($sql);

                        //получаем парттитле
                        $parttitle = preg_replace("/<.*>/iU","",$this->parttitle);

                        while($row = $this->fetch_assoc($res))// для каждой полученной записи
                        {
                                $data = serialize($row); // массив данных, помещаемых в корзинуы
                                $sql = "INSERT INTO `#__trash` SET
                                        `wasid`=".intval($row['id']).",
                                        `aid`=".intval($_SESSION['user']['id']).",
                                        `date`=".time().",
                                        `parttitle`='".addslashes($parttitle)."',
                                        `name`='".addslashes($row[$trashname])."',
                                        `table`='".addslashes($table)."',
                                        `data`='".addslashes($data)."'
                                        ";
                                $this->query($sql);
                        }
                }else
				{
					$filestodelete = array();
					$sql = "SELECT `db_fname`,`params` FROM `#h_components_listedittable` WHERE `com_id`=".intval($this->adm_com_config['id'])." && `type`='file'";
					$res = $this->query($sql);
					while($editblinfo = $this->fetch_assoc($res))
					{
						$editblinfo['params'] = $this->adm_get_param($editblinfo['params']);
						$filestodelete[] = $editblinfo; // FileFields
					}
					if(sizeof($filestodelete))
					{
						foreach($filestodelete as $delitem)
						{
							$sql = "SELECT `".$delitem['db_fname']."` FROM `#".$this->adm_com_config['config']['tbl']."` WHERE `id` IN (".$id.") ".$dopcond;
							$res_con = $this->query($sql);
							while($row_con = $this->fetch_assoc($res_con))
							{
								$i=1;
								while($delitem['params']['fpath'.$i])
								{
									$file2del = DOCUMENT_ROOT.$delitem['params']['fpath'.$i].$row_con[$delitem['db_fname']];
									if(file_exists($file2del)) unlink($file2del);
									$i++;
								}
							}
						}
					}
			    }
			
                // sql запрос на удаление
                $del_sql = "DELETE FROM `".$table."` WHERE `id` IN (".$id.") ".$dopcond;
                // удаляем
                $res = $this->query($del_sql);


                // удаляем подпункты
                if($rekursfield)
                {
                        $sql = "SELECT * FROM `".$table."` WHERE `".$rekursfield."` IN (".$id.") ".$dopcond;
                        $res = $this->query($sql);
                        while($row = $this->fetch_assoc($res))// для каждой полученной записи
                        {
                                $this->adm_del_row($row['id'], $table, $dopcond, $trashname, $rekursfield,0);
                        }
                }

                if($needheaderaction)
                {
                        //релодим страницу, но без параметра гета del
                        $this->adm_add_sys_mes($this->core_echomui('adm_delrow_del'),"del");
                        $request_uri = preg_replace("/([\?\&])del=\d+/i","\\1",REQUEST_URI);
                        $request_uri = str_replace("?&","?",trim(trim($request_uri,"&"),"?"));
                        $url = "http://".HTTP_HOST.$request_uri;
                        $this->reload($url);
                }
                // если удалилось, то возвращаем истину, если нето, то ложь
                if($res) return true;
                else return false;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF DEL_ROW FUNCTION ^^^^^^^^^^^^^^^^^^^^^ удалит запись из БД


        function adm_delfromsitemap($did=0)
        {
                static $numdeleted;
                $component_info = '';

                $did = intval($did);
                if(!$did) return false;

                // get information about the page
                $sql = "SELECT `id`,`pid`,`template`,`record_id`,`com_id` FROM `#__sitemap` WHERE `id`=".$did;
                $row = $this->fetch_assoc($this->query($sql));
                //////////////////////////////////////////////

                // get information about component of the page, if we haven't get it yet
                if($row['com_id'])
                {
                        $sql = "SELECT `id`,`config` FROM `#h_components` WHERE `id`=".intval($row['com_id']);
                        $component_info = $this->fetch_assoc($this->query($sql));
                        $component_info['config'] = $this->adm_get_param($component_info['config']);

                        if($component_info['config']['tbl'])
                        {
                                $sql = "SELECT `db_fname`,`params` FROM `#h_components_listedittable` WHERE `com_id`=".intval($component_info['id'])." && `type`='file'";
                                $res = $this->query($sql);
                                while($editblinfo = $this->fetch_assoc($res))
                                {
                                        $editblinfo['params'] = $this->adm_get_param($editblinfo['params']);
                                        $component_info['ff'][] = $editblinfo; // FileFields
                                }
                        }

////////////////////////////////////////////
					$sql = "SELECT `id`,`config` FROM `#h_components` WHERE `add_button`='content' && `id`=".$this->get_com_contplbyid($row['com_id']);
                    $comp_row = $this->fetch_assoc($this->query($sql));
                    $comp_row['config'] = $this->adm_get_param($comp_row['config']);

					$filestodelete = array();
					$sql = "SELECT `db_fname`,`params` FROM `#h_components_listedittable` WHERE `com_id`=".intval($comp_row['id'])." && `type`='file'";
					$res = $this->query($sql);
					while($editblinfo = $this->fetch_assoc($res))
					{
						$editblinfo['params'] = $this->adm_get_param($editblinfo['params']);
						$filestodelete[] = $editblinfo; // FileFields
					}
					if(sizeof($filestodelete))
					{
						foreach($filestodelete as $delitem)
						{
							$sql = "SELECT `".$delitem['db_fname']."` FROM `#".$comp_row['config']['tbl']."` WHERE `pid`=".$row['id']."";
							$res_con = $this->query($sql);
							while($row_con = $this->fetch_assoc($res_con))
							{
								$i=1;
								while($delitem['params']['fpath'.$i])
								{
									$file2del = DOCUMENT_ROOT.$delitem['params']['fpath'.$i].$row_con[$delitem['db_fname']];
									if(file_exists($file2del)) unlink($file2del);
									$i++;
								}
							}
							$sql = "DELETE FROM `#".$comp_row['config']['tbl']."` WHERE `pid`=".$row['id']."";
	                        $res = $this->query($sql);
						}
					}
				}

                // get page record if it is
                if($component_info['config']['tbl'] && $row['record_id'])
                {
                        // get list of a fields with file
                        $i=0;
                        $filefields = array();
                        $filespathways = array();

                        while($component_info['ff'][$i])
                        {
                                $fname = $component_info['ff'][$i]['db_fname'];
                                $filefields[] = $fname;

                                $j = 1;
                                while($component_info['ff'][$i]['params']['fpath'.$j])
                                {
                                        $filespathways[$fname][] = $component_info['ff'][$i]['params']['fpath'.$j];
                                        $j++;
                                }
                                $i++;
                        }

                        $sql = "SELECT `".join("`,`", $filefields)."` FROM `#".addslashes($component_info['config']['tbl'])."` WHERE `id`=".intval($row['record_id']);
                        $res = $this->query($sql);
                        $record_row = $this->fetch_assoc($res);

                        foreach($record_row as $fname=>$val)
                        {
                                foreach($filespathways[$fname] as $path)
                                {
                                        $file = DOCUMENT_ROOT.$path.$val;
                                        if(file_exists($file)) unlink($file);
                                }
                        }

                        $sql = "DELETE FROM `#".addslashes($component_info['config']['tbl'])."` WHERE `id`=".intval($row['record_id']);
                        $res = $this->query($sql);
                }
                //////////////////////////////////////////////

                // delete the page
                $this->query("DELETE FROM `#__sitemap` WHERE `id`=".$did);
                //////////////////////////////////////////////

				// delete menu alias
				$this->query("DELETE FROM `#__menupunkti` WHERE `page_id`=".$did);
				//////////////////////////////////////////////

                // get and delete all subpages
                $sql = "SELECT `id` FROM `#__sitemap` WHERE `pid`=".$did;
                $res = $this->query($sql);
                while($delsubpages = $this->fetch_assoc($res))
                {
                        $this->adm_delfromsitemap($delsubpages['id']);
                }
                //////////////////////////////////////////////

                return true;
        }

        function adm_showfiltr($title="", $selname="", $cparam="", $params=array())
        {
                if(!$selname) return false;
                $echo="";
                $echo.="<div class=\"filtr\">".($title?$title.": ":"");
                $echo.=$this->adm_show_select($selname, $cparam, $params, "", "onchange=\"location.href='?".$selname."='+this.value\"","");
                $echo.="</div>";
                return $echo;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV adm_SELECTCLASS FUNCTION VVVVVVVVVVVVVVVVVVVVV присваивает классы для select'a
        function adm_selectclass($style="")
        {
                if($style)
                {
                        $style = "style=\"".$style."\"";
                }
                return " class=\"filtr_select\" ".$style;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_SELECTCLASS FUNCTION ^^^^^^^^^^^^^^^^^^^^^ присваивает классы для select'a

        /* пишет статистику посещений админки */
        function adm_write_statistic()
        {
                return true;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV adm_OPEN_EDIT_FORM FUNCTION VVVVVVVVVVVVVVVVVVVVV открывает форму редактирования содержимого
        function adm_open_edit_form($action="")
        {
                return "<form name='editform' action='".$action."' id='editform' method='post' enctype='multipart/form-data'>";
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF OPEN_EDIT_FORM FUNCTION ^^^^^^^^^^^^^^^^^^^^^ открывает форму редактирования содержимого

        //VVVVVVVVVVVVVVVVVVVVVVV CLOSE_EDIT_FORM FUNCTION VVVVVVVVVVVVVVVVVVVVV закрывает форму редактирования содержимого
        function adm_close_edit_form()
        {
                        $string = $this->hiddenstring;
                $echo = "";
                foreach($string as $str)
                {
                        $echo .= $str."\n";
                }
                $echo .= $this->adm_show_hidden("id", $this->id);
                if(isset($this->show_edit_strings_1))
                        $echo .= "<input type='hidden' name='allshowedfields' value='".str_replace("[]","",join(";",$this->show_edit_strings_1))."'>\n";
                else
                        $echo .= "<input type='hidden' name='allshowedfields' value='".str_replace("[]","",join(";",$this->show_edit_strings))."'>\n";

                if(!$this->nocloseform) $echo .= "</form>";
                $echo .= "<script language='JavaScript'>";
                $echo .= "function checkform(form){";
                foreach($this->req_edit_strings as $name=>$title)
                {
                        $echo .= "if(!document.forms[form].elements['".$name."'].value){document.forms[form].elements['".$name."'].focus();alert('".str_replace("'","\\'",$this->core_echomui('adm_closeeditform_unfilled'))." \'".$title."\''); document.forms[form].elements['".$name."'].className='input_error';return false;}";
                        $echo .= "else{document.forms[form].elements['".$name."'].className='input_normal';}";
                }
                $echo .= "return true;}";
                $echo .= "</script>";
                $echo .= "<img src=\"/".$this->adm_path."/template/blank.gif\" width=\"800\" border=0 height=\"1\">";
                $this->webfxtab_modules = 1;
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF CLOSE_EDIT_FORM FUNCTION ^^^^^^^^^^^^^^^^^^^^^ закрывает форму редактирования содержимого

        function adm_showall4edit($save, $app, $cancel){
                echo $this->adm_open_edit_form();
                echo $this->adm_init_bookmarks();
                foreach($this->adm_bookmarks as $title=>$array) echo $this->adm_show_edit_content($title, $array);
                echo $this->adm_close_bookmarks();
                if($save || $app || $cancel) echo $this->adm_show_edit_toolbar($save, $app, $cancel);
                echo $this->adm_close_edit_form();
        }

        //VVVVVVVVVVVVVVVVVVVVVVV adm_INIT_BOOKMARKS FUNCTION VVVVVVVVVVVVVVVVVVVVV инициализирует закладки
        function adm_init_bookmarks()
        {
                $ind = md5($this->way);

                $echo = "";
                $echo .= "<table width='100%'><tr><td>";
                $echo .= "<div style='width: 100%;'>";
                $echo .= "<script type='text/javascript' src='/".$this->adm_path."/moduls/bookmarks/tabpane_mini.js.php'></script><div class='tab-pane' id='modules-cpanel-".$ind."'>";
                $echo .= "<script type='text/javascript'>var tabPane1 = new WebFXTabPane( document.getElementById( 'modules-cpanel-".$ind."' ), 1 );</script>";
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_INIT_BOOKMARKS FUNCTION ^^^^^^^^^^^^^^^^^^^^^ инициализирует закладки

        //VVVVVVVVVVVVVVVVVVVVVVV adm_get_editbookmarks FUNCTION VVVVVVVVVVVVVVVVVVVVV
        function adm_get_editbookmarks($datas = array())
        {
                $return = array();
                $this->show_edit_strings_1 = array();
                if(!$this->adm_com_config['id']) return $return;
                $sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($this->adm_com_config['id'])." && `public`='1' && `pid`=0 ORDER BY `sort` ASC";
                $res = $this->query($sql);

                        $this->hiddenstring[] = $this->adm_show_hidden('piid', $pidatas['id'], '', '');
                while($par_row = $this->fetch_assoc($res))
                {
                        $sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($this->adm_com_config['id'])." && `public`='1' && `pid`=".intval($par_row['id'])." ORDER BY `sort` ASC";
                        $res1 = $this->query($sql);
                        $book_return = array();

                        $boo_ind = 0;
                        while($row = $this->fetch_assoc($res1))
                        {
                                $params = $this->adm_get_param($row['params']);

                                $book_return[$boo_ind]['title'] = $this->core_echomui($row['mui_title']);
                                $book_return[$boo_ind]['tooltip'] = $this->core_echomui($row['tooltip']);
                                $book_return[$boo_ind]['acc_fname'] = $row['db_fname'];
                                $book_return[$boo_ind]['acc_def_input'] = $datas[$row['db_fname']];
                                if($row['req']) $book_return[$boo_ind]['req'] = $row['db_fname'];
                                if($row['useinquery']) $this->show_edit_strings_1[] = $row['db_fname'];

                                $book_return[$boo_ind]['input'] = $this->get_right_field($row, $datas, $params);

                                $boo_ind++;
                        }
                        $return[$this->core_echomui($par_row['mui_title'])] = $book_return;
                }
                $this->adm_bookmarks = $return;
                return $return;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_get_editbookmarks FUNCTION ^^^^^^^^^^^^^^^^^^^^^



	function get_right_field($row=array(), $datas=array(), $params=array())
	{
		switch($row['type'])
                               {
                                        case 'txt':
                                                $return = $this->adm_show_input($row['db_fname'], $datas[$row['db_fname']], $params['default'], $params['style'], $params['extra'], "text", $params['disabled']);
                                        break;
                                        case 'lid':
                                                $return = $this->adm_show_lidinput($row['db_fname'], $datas[$row['db_fname']], $params['default'], $params['style'], $params['extra'], $params['linkfname']);
                                        break;
                                        case 'checkbox':
												if($params['default_checked'] && !isset($datas[$row['db_fname']])) $datas[$row['db_fname']] = 1;
                                                $return = $this->adm_show_input($row['db_fname'], "1", $datas[$row['db_fname']], $params['style'], $params['extra'],"checkbox");
                                        break;
                                        case 'password':
                                                $return = $this->adm_show_input($row['db_fname'], $datas[$row['db_fname']], $params['default'], $params['style'], $params['extra'], "password");
                                        break;
                                        case 'select': case 'js-select': case 'link-select':
											$db_fname = preg_replace("/\[.*\]/iU","",$row['db_fname']);
                                                $values = $this->adm_get_select_array($params['selectid'], $datas[$db_fname]);

                                                if($params['valsbefor'])
                                                {
                                                        $valsbefor = split("\|", $params['valsbefor']);
                                                        $values_befor = array();
                                                        foreach($valsbefor as $v)
                                                        {
                                                                $v = split("=", $v);
                                                                $values_befor["'".trim($v[0])."'"] = trim($v[1]);
                                                        }
                                                        if(sizeof($values_befor)) {$values = $values_befor+$values;}
                                                }
                                                if($params['valsafter'])
                                                {
                                                        $valsafter = split("\|", $params['valsafter']);
                                                        $values_after = array();
                                                        foreach($valsafter as $v)
                                                        {
                                                                $v = split("=", $v);
                                                                $values_after["'".trim($v[0])."'"] = trim($v[1]);
                                                        }
                                                        if(sizeof($values_after)) $values = $values+$values_after;
                                                }
                                                $new_values = array();
                                                foreach($values as $k=>$v) $new_values[trim($k, "'")] = $v;
                                                $values = $new_values;

                                                if($row['type']=='js-select') $select_type = "js";
                                                else if($row['type']=='link-select') $select_type = "link";
                                                else $select_type = "";


												if(!$this->id && intval($_SESSION['adm_filter'][$this->adm_get_ses_key($params['deffromfilter_way'])][$params['deffromfilter_code']])) $datas[$db_fname] = intval($_SESSION['adm_filter'][$this->adm_get_ses_key($params['deffromfilter_way'])][$params['deffromfilter_code']]);
                                               
												if($select_type=='link')
												{
													$linkselid = rand(0,1000);
													$return = "<input type='hidden' name='".$row['db_fname']."' id='lsfield-".$linkselid."' value='".$datas[$db_fname]."'>";
													$return .= "<div id='lstxt-".$linkselid."' style='float:left'>".($values[$datas[$db_fname]]?$values[$datas[$db_fname]]:$datas[$db_fname])."</div>&nbsp;<a href='' onclick=\"document.getElementById('linksel-".$linkselid."').style.display='block';loadXMLDoc('/ajax-index.php?page=linkselect&isadm=1&sid=".$params['selectid']."&cval='+document.getElementById('lsfield-".$linkselid."').value+'&lsid=".$linkselid."','linkselcont-".$linkselid."');return false;\">[ V ]</a><div id='linksel-".$linkselid."' class='linkselect' style='width:auto;min-width:400px;'><div class='linkselectclose'><a href='' onclick=\"document.getElementById('linksel-".$linkselid."').style.display='none';return false;\">x</a></div><div id='linkselcont-".$linkselid."'>loading...</div></div>";
												}else
												{
													$return = $this->adm_show_select($row['db_fname'], $datas[$db_fname], $values, $params['style'], $params['extra'], $select_type);
												}
                                        break;
                                        case 'txtarea':
                                                $return = $this->adm_show_editor($row['db_fname'], $datas[$row['db_fname']], "fieldid-".$row['db_fname'], $params['h'], $params['w'], $params['class'],  $params['extra']);
                                        break;
                                        case 'wys':
                                                $return = $this->adm_show_editor($row['db_fname'], $datas[$row['db_fname']], "fieldid-".$row['db_fname'], $params['h'], $params['w'], $params['class'],  $params['extra'], "fckeditor", $params['wys_type'], $params['skin']);
                                        break;
                                        case 'date':
                                                $return = $this->adm_show_date($row['db_fname'], $datas[$row['db_fname']], intval($params['default']), $params['format'], $params['style'], $params['extra'], $params['disabled'], $params['disabled_renew']);
                                        break;
                                        case 'file':
                                                $return = $this->adm_show_file($row['db_fname'], $datas[$row['db_fname']], $params['type'], $params['fpath1'].$datas[$row['db_fname']], $params['fpath2'].$datas[$row['db_fname']], $params['style'], $params['extra'], intval($params['islink']));
                                        break;
                                        case 'hid':
                                                $this->hiddenstring[] = $this->adm_show_hidden($row['db_fname'], $datas[$row['db_fname']], $params['default'], "fieldid-".$row['db_fname']);
                                        break;
                                        case 'chuser':
                                                $accgids = split(",", $params['accgids']);
                                                if(trim($params['showfields']))
                                                {
                                                        $showfields = split(",", $params['showfields']);
                                                        foreach($showfields as $k=>$v)
                                                        {
                                                                $v = split("\|", $v);
                                                                $showfields[$k] = $v[0]."|".$this->core_echomui($v[1]);
                                                        }
                                                }else $showfields = array();
                                                $return = $this->adm_show_userchecklist($datas[$row['db_fname']], $row['db_fname'], $params['acctype'], $accgids, $showfields);
                                        break;
                                        case 'mailfromsite':
                                                $return = $this->adm_show_mailfromsite($datas[$params['userfname']]);
                                        break;

										case 'fs_directory':
											$uniq_id = substr(md5(time()),rand(0,25),5);

											$sd = trim($datas[$row['db_fname']],".");
											if(isset($params['fm_dir'])) $file_manager_dir = ($params['fm_dir']?$params['fm_dir']."/":'');
											else $file_manager_dir = 'images/';

											$sd_whole = DOCUMENT_ROOT.'/'.$sd;
											if($sd && file_exists($sd_whole) && is_dir($sd_whole)) $selected_dir = $sd;
											else $selected_dir = '<i>'.$this->core_echomui('fs_directory_nochoosedir').'</i>';
											$return = "<span id='fs_directory_txt-".$uniq_id."'>".$selected_dir."</span><br/><a href='' onclick=\"loadfilemanager_".$uniq_id."();this.style.display='none';return false;\">".$this->core_echomui('fs_directory_choosefolder')."</a><span id='osnfldtxt-".$uniq_id."' style='display:none;'><a href='' onclick='setseldir_".$uniq_id."();return false;'>".$this->core_echomui('fs_directory_setthisdir')." <span style='font-weight: bold;'><span id='selfldname-".$uniq_id."'></span></span></a></span><input type='hidden' name='".$row['db_fname']."' value='".$datas[$row['db_fname']]."' id='fs_directory_inp-".$uniq_id."'/><div id='fmgr-".$uniq_id."'></div>";
											$sd2fmgr = str_replace($file_manager_dir,"",$sd);

?>
<script language="JavaScript">
function loadfilemanager_<?=$uniq_id?>()
{
	var iframe = document.createElement("iframe");
	iframe.src = "/<?=$this->adm_path?>/filemanager/?<?echo $sd2fmgr?'.'.$sd2fmgr:''?>"
	iframe.name = 'filemanager<?=$uniq_id?>';
	iframe.style.width = '100%';
	iframe.style.height = '300px';
	iframe.style.border = '0px';
	iframe.id = 'filemanager_iframe-<?=$uniq_id?>';
	document.getElementById('fmgr-<?=$uniq_id?>').appendChild(iframe);

	if(document.getElementById('fotos4<?=$row['db_fname']?>')) document.getElementById('fotos4<?=$row['db_fname']?>').innerHTML = '...';

	setInterval(checkselected_<?=$uniq_id?>,500);
}

function checkselected_<?=$uniq_id?>()
{
	var selected = window.filemanager<?=$uniq_id?>.document.getElementById('selectedfiles').value.split("\n");
	if(selected[0] && selected[0]!=document.getElementById('fs_directory_inp-<?=$uniq_id?>').value)
	{
		document.getElementById('osnfldtxt-<?=$uniq_id?>').style.display='block';
		document.getElementById('selfldname-<?=$uniq_id?>').innerHTML = selected[0];
	}
	else document.getElementById('osnfldtxt-<?=$uniq_id?>').style.display='none';
}
function setseldir_<?=$uniq_id?>()
{
	var osnfotodirname = window.filemanager<?=$uniq_id?>.location.href.split("?");
	osnfotodirname = osnfotodirname[1]+'/'+document.getElementById('selfldname-<?=$uniq_id?>').innerHTML;
	
	osnfotodirname = osnfotodirname.split('//').join('/');
	osnfotodirname = osnfotodirname.split('./').join('/');
	osnfotodirname = '<?echo $file_manager_dir?'/'.$file_manager_dir:''?>'+osnfotodirname+'/'
	osnfotodirname = osnfotodirname.split('//').join('/');

	document.getElementById('fs_directory_txt-<?=$uniq_id?>').innerHTML = osnfotodirname;
	document.getElementById('fs_directory_inp-<?=$uniq_id?>').value = osnfotodirname;
	
}
</script>
<?
										break;
										case 'foto_list':
											$uniq_id = substr(md5(time()),rand(0,25),5);
											
											$id = $this->id;
											$pp = serialize($params);

											$return = "<input type='hidden' id='if_photos_h_".$uniq_id."'><iframe style='border:0px;margin:0px;padding:0px;width:100%;height:50px;' id='if_photos_".$uniq_id."' name='if_photos_".$uniq_id."' src='/".$this->adm_path."/photos_set/?id=".$id."&dbp=".$this->param."&params=".$pp."&hinp=if_photos_h_".$uniq_id."' onload=\"setInterval(function() {document.getElementById('if_photos_".$uniq_id."').style.height = document.getElementById('if_photos_h_".$uniq_id."').value+'px';}, 100);\"></iframe>";

										break;
                                        default:
                                                $return = '<font color="#ff0000">Error: field type to editing not specify</font>';
                                }

								return $return;
	}




        function adm_get_select_array($selectid=0, $curval='', $type=0) // type - тип получения массива: 0 - с разделителем. 1 - без разделителя
        {
                if(!$selectid) return array();
                $values = array();
                $sql = "SELECT * FROM `#h_components_selects` WHERE `id`=".intval($selectid);
                $select_res = $this->query($sql);
                $select_row = $this->fetch_assoc($select_res);
                if($select_row['type']=='sim') $values = $this->adm_get_param($select_row['sim_values']); // простой селект
                else // сложный селект
                {
                        $selfils = split(",", $select_row['dif_fields']);
                        if($selfils[0]=="*")
                        {
                                $selfils1 = "*";
                                $ar = $this->mysql_get_fields("#".addslashes($select_row['dif_tblname']));
                                foreach($ar as $k=>$v) $selfils[$k] = trim($v['Field']);
                        }
                        else
                        {
                                $selfils1 = array();
                                foreach($selfils as $k=>$v)
                                {
                                        $selfils1[] = "`".addslashes(trim($v))."`";
                                        $selfils[$k] = trim($v);
                                }
                                $selfils1 = join(",", $selfils1);
                        }
                        $select_row['dif_cond'] = str_replace("{curval}", $curval, $select_row['dif_cond']);
                        $select_row['dif_cond'] = str_replace("{thisid}", $this->id, $select_row['dif_cond']);

                        $sql = "SELECT ".$selfils1." FROM `#".addslashes($select_row['dif_tblname'])."` WHERE ".$select_row['dif_cond']."".($select_row['dif_sort']?" ORDER BY ".addslashes($select_row['dif_sort']):"")."";
                       	$infa = $this->core_get_tree($sql);
                        $infa = $this->core_get_tree_keys($select_row['dif_startpid'], $selfils,$infa, $select_row['dif_startspace'], 1);
                        $values = array();
                        $values = $this->adm_get_param($select_row['dif_valsbefor']);
                        foreach($infa as $item)
                        {
                                if(!$type) $gospace = str_repeat($select_row['dif_spacer'], $item['this_space']); else $gospace = "";

                                $thisitemvalue = '';
                                $thisitemvalue .= $gospace.($item['this_space']?"":"");
                                
                                $dif_valfield = split(" ", $select_row['dif_valfield']);
                                foreach($dif_valfield as $f)
                                {
                                        if(isset($item[$f])) $thisitemvalue .= $item[$f];
                                        else $thisitemvalue .= $f;
                                        $thisitemvalue .= " ";
                                }
                                $thisitemvalue = trim($thisitemvalue);

                                $values[strval($item[$select_row['dif_keyfield']])] = $thisitemvalue;
                        }
                        $values_after = $this->adm_get_param($select_row['dif_valsafter']);
                        $values = $values+$values_after;
                }
                return $values;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV SHOW_EDIT_CONTENT FUNCTION VVVVVVVVVVVVVVVVVVVVV формирует контент для редактирования
        function adm_show_edit_content($title="", $strings = array())
        {
                static $i;
                static $j;
                $i++;
                $tut = 0;
                $ind = $_SERVER['PATH_INFO'].$i;
                $echo = "";
                $echo .= "<div class='tab-page' id='".$ind."'><h2 class='tab'>".($title ? $title : "untitled")."</h2><script type='text/javascript'>tabPane1.addTabPage( document.getElementById( '".$ind."' ) );</script>";
                $echo .= "<table class='edittablemain' callpadding=0 cellspacing=0>";
                foreach($strings as $string)
                {
                        // $string - массив, содержит информацию о текущей строке:
                        /* параметры массива $string
                        title - заголовок (подпись)
                        input - поле ввода данных, либо это просто текст, если не разрешается редактировать данное поле. Если этого параметра нет, то выводится только title в ячейке с collspan=2
                        req - имя поля формы, обязательного для заполнения, соответствующего данной строке.
                        tooltip - текст подсказки к данному полю.
                        acc_fname - имя поля, для получения уровня доступа этого юзера к просмотру/редактированию поля. Значение этого поля должно быть в таблице "_users_rights" в поле "way". Если этого параметра нет в массиве, то права доступа полные.
                        acc_def_input - текст, выводимый вместь input, если прав на редактирование нет.
                        */


                        if(isset($string['acc_fname']) && is_array($this->can_edit_fields[$string['acc_fname']]))
                        {
                                // проверяем, можно ли просматривать это поле этому юзверю, если нет, то пропускаем эту строку
                                if(!$this->can_edit_fields[$string['acc_fname']]['view'])
                                        continue;

                                // проверяем, если действие - добавление записи (нет id), а добавлять запись нельзя, то не выводим строку
                                if(!$this->id && !$this->can_edit_fields[$string['acc_fname']]['add'])
                                        continue;

                                // проверяем, если действие - редактирование записи, а редактировать нельзя, то подменяем input на acc_def_input
                                if($this->id && !$this->can_edit_fields[$string['acc_fname']]['edit'] && isset($string['acc_def_input']))
                                {
                                        $string['input'] = $string['acc_def_input'].$this->adm_show_hidden($string['acc_fname'], $string['acc_def_input']);
                                }
                        }

                        $tut = 1;

                        $j++;
                        $echo .= "<tr id='tr".$j."'>";
                        if(isset($string['title'])) $echo .= "<td id='1td".$j."'".(!isset($string['input'])?" colspan='2' class='td_lable_alone'":" class='td_lable'").">";
                        $echo .= "\n";
                        $echo .=  $string['title'];
                        if($string['req'])
                        {
                                $echo .= " <span class='req_star'>*</span> ";
                                $this->req_edit_strings[$string['req']] = $string['title'];
                        }

                        if($string['tooltip'])
                        {
                                $echo .= $this->adm_show_help_pic($string['title'], $string['tooltip']);
                        }

                        if($string['input'])
                        {
                                if(isset($string['title'])) $echo .= "</td>";
                                $echo .=  "<td id='2td".$j."' class='td_cont'>";
                                if(is_array($string['input']))
                                {
                                        foreach($string['input'] as $s) $echo .=  $s."<br>";
                                }else $echo .=  $string['input'];
                        }
                        $echo .=  "</td></tr>";
                }
                $echo .=  "</table>";
                $echo .=  "</div>";
                if($tut) return $echo;
                else return "";
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF SHOW_EDIT_CONTENT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ формирует контент для редактирования

        function adm_show_help_pic($title="", $text="", $img="b_help.png")
        {
                if(!$text) return;
                $echo = "";
                $echo = "<img src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/".$img."' ".$this->adm_addtooltip($title, $text)." >";
                return $echo;
        }

        function adm_addtooltip($title="", $text="")
        {
                if(!$text) return;
                $echo = "";
                $echo = " style='cursor:default' onmouseover=\"tooltip('".str_replace("'","\'",str_replace('"',"'",$title))."','".str_replace("'","\'",str_replace('"',"'",$text))."');\" onmouseout=\"return nd();\"";
                return $echo;
        }


        //VVVVVVVVVVVVVVVVVVVVVVV adm_SHOW_HIDDEN FUNCTION VVVVVVVVVVVVVVVVVVVVV скрытый input
        function adm_show_hidden($name="", $value="", $default="", $id="")
        {
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
                $echo = "<input type='hidden' name='".$name."' value='".($value ? $value : $default)."'".($id?" id='".$id."'":"")." />";
                $this->show_edit_strings[] = $name;
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF adm_SHOW_HIDDEN FUNCTION ^^^^^^^^^^^^^^^^^^^^^ скрытый input



        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_INPUT FUNCTION VVVVVVVVVVVVVVVVVVVVV простой input
        function adm_show_input($name="", $value="", $cdef="", $style="", $extra="", $type="text", $disabled=0)
        {
                // $cdef - если тип [$type] = checkbox или radio, то это текущее значение, если совпадает с value то нужно установить в checkeg
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}

                $acctypes = array("text", "password", "checkbox", "radio");
                if(!in_array($type,$acctypes)) $type = "text";

                $chf = array("checkbox", "radio");
				if($disabled && $type == "text")
				{
					$echo = "<span>".strval($value?$value:$cdef)."</span>";
				}
                else
				{
					$echo = "<input type='".$type."' name='".$name."' value=\"".str_replace('"','&quot;',$value)."\"".((in_array($type, $chf)&&$cdef==$value)?" checked":"")." ".$this->adm_inputclass($style)." ".$extra.">";
					$this->show_edit_strings[] = $name;
                }
				return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_INPUT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ простой input

        function adm_show_editinput($name="", $value="", $id="", $style="", $tbl2save="", $formname="", $extratext="", $showpencil=1,$extracode='',$usedbkey=1,$goajaxsave=1)
        {
			if(!$this->can_edit) {return $value;return;}
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
                if(!$id) {return "<font color='#ff0000'>Error: input's id don't specify</font>";}
                if(!$tbl2save) {return "<font color='#ff0000'>Error: tbl2save don't specify</font>";}
                if($usedbkey) $dbkey = "-".$id.substr(md5($tbl2save),0,5);
                                else $dbkey = "";
                $onclick = " class=\"simpleeditfield\" onmouseover=\"this.className='simpleeditfield_h'\" onmouseout=\"this.className='simpleeditfield'\" onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='inline';document.getElementById('edit-text-".$name.$dbkey."').style.display='none';return false;\"";

                $echo = "";

                $echo .= "<span id=\"edit-text-".$name.$dbkey."\"".(!$showpencil&&$value!=''?"".$onclick:"")."><span id=\"edit-textt-".$name.$dbkey."\">".$value."</span>";

                if($showpencil || $value=='')
                {
                        $echo .= "&nbsp;<a href=''".$onclick.">";
                        $echo .= "<img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/edit10px.png\" border='0' align='absmiddle'></a>";
                }

                $echo .= "</span>";

                $echo .= "<span id=\"edit-input-".$name.$dbkey."\" style=\"display:none;\">".$this->adm_show_input("edit-input-".$name.$dbkey, $value, "", $style, $extracode." id=\"edit-field-".$name.$dbkey."\"")."&nbsp;";
                $echo .= "<a href='' onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='none';document.getElementById('edit-text-".$name.$dbkey."').style.display='inline';document.getElementById('edit-field-".$name.$dbkey."').value = document.getElementById('edit-textt-".$name.$dbkey."').innerHTML;return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/cancel.png\" border='0' align='absmiddle'></a>&nbsp;";
                $echo .= "<a href='' onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='none';document.getElementById('edit-text-".$name.$dbkey."').style.display='inline';";

                                if($goajaxsave){
                                $echo .= "loadXMLDoc('/ajax-index.php?isadm=1&page=safefield&id=".$id."&dbtable=".$tbl2save."&field=".$name."&in=edit-input-".$name.$dbkey."','edit-textt-".$name.$dbkey."','".$formname."');";
                                }

                                $echo .= "document.getElementById('edit-textt-".$name.$dbkey."').innerHTML=document.getElementById('edit-field-".$name.$dbkey."').value;return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>".($extratext?"<br>".$extratext:"")."</span>";
                return $echo;
        }

        function adm_show_editselect($name="", $value="", $id="", $options=array(), $style="", $tbl2save="", $formname="", $type="", $extratext="", $showpencil=1, $onchange="", $selectid=0)
        {
			if(!$this->can_edit) {return $value;return;}
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
                if(!$id) {return "<font color='#ff0000'>Error: input's id don't specify</font>";}
                if(!$tbl2save) {return "<font color='#ff0000'>Error: tbl2save don't specify</font>";}
                $dbkey = "-".$id.substr(md5($tbl2save),0,5);

                $onclick = " class=\"simpleeditfield\" onmouseover=\"this.className='simpleeditfield_h'\" onmouseout=\"this.className='simpleeditfield'\" onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='inline';document.getElementById('edit-text-".$name.$dbkey."').style.display='none';return false;\"";
                $echo = "";
                $extracode="onchange=\"if(this.value=='_self') document.getElementById('edit-input_self-".$name.$dbkey."').style.display='inline'; else document.getElementById('edit-input_self-".$name.$dbkey."').style.display='none';".$onchange."\"";

                $echo .= "<span id=\"edit-text-".$name.$dbkey."\"".(!$showpencil&&$value!=''?"".$onclick:"")."><span id=\"edit-textt-".$name.$dbkey."\">".$value."</span>";

                if($showpencil || $value=='')
                {
                        $echo .= "&nbsp;<a href=''".$onclick.">";
                        $echo .= "<img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/edit10px.png\" border='0' align='absmiddle'></a>";
                }

                $echo .= "</span>";

                $echo .= "<span id=\"edit-input-".$name.$dbkey."\" style=\"display:none;\">".$this->adm_show_select("edit-input-".$name.$dbkey, $value, $options, $style, $extracode." id=\"edit-field-".$name.$dbkey."\"", $type)."&nbsp;";
                $echo .= "<a href='' onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='none';document.getElementById('edit-text-".$name.$dbkey."').style.display='inline';document.getElementById('edit-field-".$name.$dbkey."').value = document.getElementById('edit-textt-".$name.$dbkey."').innerHTML;document.getElementById('edit-input_self-".$name.$dbkey."').value='';document.getElementById('edit-input_self-".$name.$dbkey."').style.display='none';return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/cancel.png\" border='0' align='absmiddle'></a>&nbsp;";
                $echo .= "<a href='' onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='none';document.getElementById('edit-text-".$name.$dbkey."').style.display='inline';loadXMLDoc('/ajax-index.php?isadm=1&page=safefield&id=".$id."&dbtable=".$tbl2save."&field=".$name."&in=edit-input-".$name.$dbkey."&selectid=".$selectid."','edit-textt-".$name.$dbkey."','".$formname."');document.getElementById('edit-textt-".$name.$dbkey."').innerHTML='...';return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>".($extratext?"<br>".$extratext:"")."<br>";
                $echo .= $this->adm_show_input("edit-input_self-".$name.$dbkey, '', "", 'display:none', " id=\"edit-input_self-".$name.$dbkey."\"");
                $echo .= "</span>";
                return $echo;
        }

        function adm_show_editeditor($name="", $value="", $id="", $h="",$w="", $tbl2save="", $formname="", $class="",$extra="",$type="textarea",$wys_type="Default", $skin="default", $extratext="", $showpencil=1)
        {
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
                if(!$id) {return "<font color='#ff0000'>Error: input's id don't specify</font>";}
                if(!$tbl2save) {return "<font color='#ff0000'>Error: tbl2save don't specify</font>";}
                if($this->thisajax) $type = "textarea";
                $dbkey = "-".$id.substr(md5($tbl2save),0,5);
                $onclick = " class=\"simpleeditfield\" onmouseover=\"this.className='simpleeditfield_h'\" onmouseout=\"this.className='simpleeditfield'\" onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='inline';document.getElementById('edit-text-".$name.$dbkey."').style.display='none';return false;\"";
                $echo = "";

                $echo .= "<span id=\"edit-text-".$name.$dbkey."\"".(!$showpencil&&$value!=''?"".$onclick:"")."><span id=\"edit-textt-".$name.$dbkey."\">".$value."</span>";

                if($showpencil || $value=='')
                {
                        $echo .= "&nbsp;<a href=''".$onclick.">";
                        $echo .= "<img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/edit10px.png\" border='0' align='absmiddle'></a>";
                }

                $echo .= "</span>";

                $echo .= "<span id=\"edit-input-".$name.$dbkey."\" style=\"display:none;position:absolute;\">".$this->adm_show_editor("edit-input-".$name.$dbkey, $value, "edit-field-".$name.$dbkey, $h,$w, $class,$extra,$type,$wys_type, $skin)."&nbsp;";
                $echo .= "<a href='' onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='none';document.getElementById('edit-text-".$name.$dbkey."').style.display='inline';document.getElementById('edit-field-".$name.$dbkey."').value = document.getElementById('edit-textt-".$name.$dbkey."').innerHTML;return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/cancel.png\" border='0' align='absmiddle'></a>&nbsp;";
                $echo .= "<a href='' onclick=\"document.getElementById('edit-input-".$name.$dbkey."').style.display='none';document.getElementById('edit-text-".$name.$dbkey."').style.display='inline';loadXMLDoc('/ajax-index.php?isadm=1&page=safefield&id=".$id."&dbtable=".$tbl2save."&field=".$name."&in=edit-input-".$name.$dbkey."','edit-textt-".$name.$dbkey."','".$formname."');document.getElementById('edit-textt-".$name.$dbkey."').innerHTML=document.getElementById('edit-field-".$name.$dbkey."').value;return false;\"><img src=\"http://".HTTP_HOST."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/tree_edit/save.png\" border='0' align='absmiddle'></a>".($extratext?"<br>".$extratext:"")."</span>";
                return $echo;
        }

        function adm_go_upload_file()
        {
                if(!$this->adm_com_config['id']) return false;
                $sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($this->adm_com_config['id'])." && `public`='1' && `pid`!=0 && `type`='file'";
                $res = $this->query($sql);
                while($row = $this->fetch_assoc($res))
                {
                        $params = $this->adm_get_param($row['params']);
                        $accfileext = split(",", $params['ext']);
						
						IF($params['newext']=='newext') $params['newext'] = '';

                        if($params['fpath1'])
                        {
                                $this->cond_resize = intval($params['cond_resize1']);
                                $this->setwatermark = intval($params['watermark1']);
                                $upfile = $this->adm_upload_file($row['db_fname'], $params['save2name'], $accfileext, $params['fpath1'], intval($params['replace']), intval($params['resize']), intval($params['w1']), intval($params['h1']), trim($params['newext']));

                                $newupfile = split("\.", $upfile);array_pop($newupfile);$newupfile=join(".",$newupfile);
                                $index = 2;
                                while(isset($params['fpath'.$index]))
                                {
                                        $this->cond_resize = intval($params['cond_resize'.$index]);
                                        $this->setwatermark = intval($params['watermark'.$index]);
                                        $this->adm_upload_file($row['db_fname'], $newupfile, $accfileext, $params['fpath'.$index], intval($params['replace']), intval($params['resize']), intval($params['w'.$index]), intval($params['h'.$index]), trim($params['newext']));
                                        $index++;
                                }
                                if($upfile && isset($_POST[$row['db_fname']])) $_POST[$row['db_fname']] = $upfile;
                        }
                }
                return true;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_INPUT FUNCTION VVVVVVVVVVVVVVVVVVVVV простой input
        function adm_show_file($name="", $value="", $type="img", $fpathind1 = 0, $fpathind2 = 0,$style="",$extra="", $islink=1)
        {
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
                $echo = "";

                if(isset($this->adm_file_pathway[$fpathind1])) $thepath1 = $this->adm_file_pathway[$fpathind1]; // такая конструкция применена для совместимости с предыдущей версией
                elseif($fpathind1) $thepath1 = $fpathind1;

                if(isset($this->adm_file_pathway[$fpathind2])) $thepath2 = $this->adm_file_pathway[$fpathind2]; // такая конструкция применена для совместимости с предыдущей версией
                elseif($fpathind2) $thepath2 = $fpathind2;

                if(!$type) $type = "file";

                if(file_exists(DOCUMENT_ROOT.$thepath1) && !is_dir(DOCUMENT_ROOT.$thepath1))
                {
                        $imgsize = getimagesize(DOCUMENT_ROOT.$thepath1);
                        $pathinfo = pathinfo(DOCUMENT_ROOT.$thepath1);
                        $filesize = filesize(DOCUMENT_ROOT.$thepath1);

                        if($type == "img" && in_array($pathinfo['extension'], array("jpg","jpeg","gif","png")))
                        {
                                if($islink) $echo .= "<a href='http://".HTTP_HOST.$thepath2."' target=_blank>";
                                $echo .= "<img src='http://".HTTP_HOST.$thepath1."' border=0 width='".$imgsize['0']."'></a> <br>".$this->adm_show_input("delfile[]", $name, "" ,"","","checkbox")." ".$this->core_echomui('adm_showfile_delimg').""."<br>";
                        }else if($pathinfo['extension']=='swf')
                        {
                                $echo .= '<div><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" id="" align="middle" wmode="opaque">';
                                $echo .= '<param name="allowScriptAccess" value="sameDomain" />';
                                $echo .= '<param name="allowFullScreen" value="false" />';
                                $echo .= '<param name="wmode" value="opaque" />';
                                $echo .= '<param name="movie" value="http://'.HTTP_HOST.$thepath1.'" />';
                                $echo .= '<param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />';
                                $echo .= '<embed src="http://'.HTTP_HOST.$thepath1.'" wmode="opaque" quality="high" bgcolor="#ffffff" name="" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />';
                                $echo .= '</object><div>'.$this->adm_show_input("delfile[]", $name, "" ,"","","checkbox").' '.$this->core_echomui('adm_showfile_delimg').'<br>';
                        }else
                        {
                                $fextfile = "/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/file_extensions/".$pathinfo['extension'].".png";
                                if(!file_exists(DOCUMENT_ROOT.$fextfile) || is_dir(DOCUMENT_ROOT.$fextfile)) $fextfile = "/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/file_extensions/default.png";
                                $echo .= "<a href='http://".HTTP_HOST.$thepath1."' target=_blank><img src='http://".HTTP_HOST.$fextfile."' border=0 align='absmiddle' class='png'>".$pathinfo['basename']." (".round(($filesize/1024),2)." Kb)</a> ".$this->adm_show_input("delfile[]", $name, "" ,"","","checkbox")." ".$this->core_echomui('adm_showfile_delfile')."<br>";
                        }
                }else $echo .= "<em>".$this->core_echomui('adm_showfile_notuploaded')."</em><br>";
                if($this->core_ini_get("file_uploads"))
                {
                        $echo .= "<small><font color='#cfcfcf'>".$this->core_echomui('adm_showfile_choosefile')."<span style='font-weight: bold;'>".$this->core_ini_get("upload_max_filesize")."</span>:</font></small><br><input type='file' name='file_".$name."' ".$this->adm_inputclass($style)." ".$extra."><br>";
                }else $echo .= "<font color='#ff0000'>".$this->core_echomui('adm_showfile_upnotallowed')."</font>";

                $dir1 = split("/", DOCUMENT_ROOT.$thepath1);array_pop($dir1);$dir1 = join("/", $dir1);
                $dir2 = split("/", DOCUMENT_ROOT.$thepath2);array_pop($dir2);$dir2 = join("/", $dir2);

                if(!file_exists($dir1) || (file_exists($dir1) && !is_dir($dir1))) $echo .= "<font color='#ff0000'>".$this->core_echomui('adm_showfile_upnodir').": <span style='font-weight: bold;'>".$dir1."</span></font><br>";
                if($thepath2) if(!file_exists($dir2) || (file_exists($dir2) && !is_dir($dir2))) $echo .= "<font color='#ff0000'>".$this->core_echomui('adm_showfile_upnodir').": <span style='font-weight: bold;'>".$dir2."</span></font><br>";

                $echo .= $this->adm_show_hidden($name, $value);
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_INPUT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ простой input

        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_LIDINPUT FUNCTION VVVVVVVVVVVVVVVVVVVVV простой input
        function adm_show_lidinput($name="", $value="", $cdef="", $style="", $extra="", $linkfname="")
        {
                if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
                $curind = substr(md5($name),0,5);

                                $linkarr = split("/",$value);
                                $value = end($linkarr);
                                array_pop($linkarr);

                                $echo = "/";
                                if(sizeof($linkarr))
                                {
                                        $prelink = join("/",$linkarr);
                                        $echo .= $this->adm_show_editinput("prelidof".$name, $prelink, $this->id, "", "#__sitemap", "", "", 0,'',0,0)."/";
                                }
                $echo .= "<span id='liddiv-".$curind."' onclick=\"this.style.display='none'; document.forms['editform'].".$name.".style.display='inline';document.forms['editform'].".$name.".focus();document.getElementById('autotranslit-".$curind."').checked=false;\" title=\"".$this->core_echomui('adm_lidedit')."\" onmouseover=\"this.style.padding='0px';this.style.border='1px dashed #cfcfcf';\" onmouseout=\"this.style.padding='1px';this.style.border='0px';\" style=\"padding:1px;\">".$value."</span>";
                $echo .= "<input type='text' name='".$name."' style='display:none;' value='".$value."' ".$this->adm_inputclass($style)." ".$extra.">";
                                $echo .= "/";
                $echo .= "<br><input type='checkbox' id='autotranslit-".$curind."' value='1' onclick=\"if(this.checked){document.getElementById('liddiv-".$curind."').style.display='inline'; document.forms['editform'].".$name.".style.display='none';}else{document.getElementById('liddiv-".$curind."').style.display='none'; document.forms['editform'].".$name.".style.display='inline';}\"><label for='autotranslit-".$curind."'>".$this->core_echomui('adm_autotranslit')."</label>";

                                $echo .= "<span style='color:#f00;display:none' id='lidwarmes-".$curind."'><br>&nbsp;".$this->core_echomui('adm_lidwar')."&nbsp;<a href='#' onclick=\"setdefault".$curind."();return false;\">".$this->core_echomui('adm_lidwarretdef')."</a></span>";

                                $echo .= "<br><input type='checkbox' id='addprelid-".$curind."' name='addprelid[".$name."]' value='1'><label for='addprelid-".$curind."'>".$this->core_echomui('adm_addprelid')."</label>";

                $echo .= "<script language=\"JavaScript\">
                        var lettervar = new Array();
						lettervar['A'] = lettervar['a'] = 'a';
						lettervar['B'] = lettervar['b'] = 'b';
						lettervar['C'] = lettervar['c'] = 'c';
						lettervar['D'] = lettervar['d'] = 'd';
						lettervar['E'] = lettervar['e'] = 'e';
						lettervar['F'] = lettervar['f'] = 'f';
						lettervar['G'] = lettervar['g'] = 'g';
						lettervar['H'] = lettervar['h'] = 'h';
						lettervar['I'] = lettervar['i'] = 'i';
						lettervar['J'] = lettervar['j'] = 'j';
						lettervar['K'] = lettervar['k'] = 'k';
						lettervar['L'] = lettervar['l'] = 'l';
						lettervar['M'] = lettervar['m'] = 'm';
						lettervar['N'] = lettervar['n'] = 'n';
						lettervar['O'] = lettervar['o'] = 'o';
						lettervar['P'] = lettervar['p'] = 'p';
						lettervar['Q'] = lettervar['q'] = 'q';
						lettervar['R'] = lettervar['r'] = 'r';
						lettervar['S'] = lettervar['s'] = 's';
						lettervar['T'] = lettervar['t'] = 't';
						lettervar['U'] = lettervar['u'] = 'u';
						lettervar['V'] = lettervar['v'] = 'v';
						lettervar['W'] = lettervar['w'] = 'w';
						lettervar['X'] = lettervar['x'] = 'x';
						lettervar['Y'] = lettervar['y'] = 'y';
						lettervar['Z'] = lettervar['z'] = 'z';
                        lettervar['А'] = lettervar['а'] = 'a';
                        lettervar['Б'] = lettervar['б'] = 'b';
                        lettervar['В'] = lettervar['в'] = 'v';
                        lettervar['Г'] = lettervar['г'] = 'g';
                        lettervar['Д'] = lettervar['д'] = 'd';
                        lettervar['Э'] = lettervar['э'] = lettervar['Е'] = lettervar['е'] = 'e';
                        lettervar['Ё'] = lettervar['ё'] = 'yo';
                        lettervar['Ж'] = lettervar['ж'] = 'j';
                        lettervar['З'] = lettervar['з'] = 'z';
                        lettervar['Й'] = lettervar['й'] = lettervar['И'] = lettervar['и'] = 'i';
                        lettervar['К'] = lettervar['к'] = 'k';
                        lettervar['Л'] = lettervar['л'] = 'l';
                        lettervar['М'] = lettervar['м'] = 'm';
                        lettervar['Н'] = lettervar['н'] = 'n';
                        lettervar['О'] = lettervar['о'] = 'o';
                        lettervar['П'] = lettervar['п'] = 'p';
                        lettervar['Р'] = lettervar['р'] = 'r';
                        lettervar['С'] = lettervar['с'] = 's';
                        lettervar['Т'] = lettervar['т'] = 't';
                        lettervar['У'] = lettervar['у'] = 'u';
                        lettervar['Ф'] = lettervar['ф'] = 'f';
                        lettervar['Х'] = lettervar['х'] = 'h';
                        lettervar['Ц'] = lettervar['ц'] = 'c';
                        lettervar['Ч'] = lettervar['ч'] = 'ch';
                        lettervar['Щ'] = lettervar['щ'] = lettervar['Ш'] = lettervar['ш'] = 'sh';
                        lettervar['Ь'] = lettervar['ь'] = lettervar['Ъ'] = lettervar['ъ'] = '';
                        lettervar['Ы'] = lettervar['ы'] = 'i';
                        lettervar['Ю'] = lettervar['ю'] = 'yu';
                        lettervar['Я'] = lettervar['я'] = 'ya';
                        lettervar[' '] = '_';
                        lettervar['/'] = lettervar['\"'] = lettervar['\\\'] = lettervar['?'] = lettervar['*'] = lettervar['^'] = lettervar['&'] = lettervar[':'] = lettervar['>'] = lettervar['<'] = lettervar['|'] = lettervar['#'] = lettervar[\"'\"] = lettervar['$'] = lettervar['%'] = lettervar['~'] = lettervar['`'] = lettervar['@'] = lettervar[';'] = lettervar['='] = lettervar[')'] = lettervar['('] = lettervar['«'] = lettervar['»'] = '';

                                                function setdefault".$curind."()
                                                {
                                                        document.getElementById('liddiv-".$curind."').innerHTML='".$value."';document.forms['editform'].".$name.".value='".$value."';document.getElementById('autotranslit-".$curind."').checked=false;document.getElementById('liddiv-".$curind."').style.display='none';document.forms['editform'].".$name.".style.display='inline';

                                                        if(".($prelink?"1":"0").")
                                                        {
                                                                document.getElementById('edit-input-prelidof".$name."').style.display='none';
                                                                document.getElementById('edit-text-prelidof".$name."').style.display='inline';

                                                                document.getElementById('edit-text-prelidof".$name."').innerHTML='".$prelink."';
                                                                document.getElementById('edit-field-prelidof".$name."').value='".$prelink."';
                                                        }
                                                }
                        function setlid".$curind."() {
                                if(document.getElementById('autotranslit-".$curind."').checked)
                                {
                                        var value = document.forms['editform'].".$linkfname.".value.split('');
                                        var newvalue = '';
                                        for(i=0;i<value.length;i++) {if(lettervar[value[i]]!=undefined) newvalue = newvalue+''+lettervar[value[i]]; else newvalue = newvalue+''+value[i];}
                                        document.getElementById('liddiv-".$curind."').innerHTML=newvalue;
                                        document.forms['editform'].".$name.".value=newvalue;
                                }

                                var deflid = '".$value."';
                                var defprelid = '".$prelink."';

                                if((deflid && document.forms['editform'].".$name.".value != deflid) || (defprelid && document.getElementById('edit-field-prelidof".$name."').value != defprelid))
                                                                {
                                                                        document.getElementById('lidwarmes-".$curind."').style.display='inline';
                                                                }
                                else
                                                                {
                                                                        document.getElementById('lidwarmes-".$curind."').style.display='none';
                                                                }
                                setTimeout(\"setlid".$curind."()\", 3000);
                        }
                        var value = document.forms['editform'].".$linkfname.".value.split('');
                        var newvalue = '';
                        for(i=0;i<value.length;i++) {if(lettervar[value[i]]!=undefined) newvalue = newvalue+''+lettervar[value[i]]; else newvalue = newvalue+''+value[i];}
                        if(newvalue=='".$value."') document.getElementById('autotranslit-".$curind."').checked=true;
                        else
                        {
                                document.getElementById('liddiv-".$curind."').style.display='none';
                                document.forms['editform'].".$name.".style.display='inline';
                        }
                        setlid".$curind."();
                </script>";
                $this->show_edit_strings[] = $name;
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_LIDINPUT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ простой input

			//VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_LIDINPUT_ORIGINAL FUNCTION VVVVVVVVVVVVVVVVVVVVV простой input
	function adm_show_lidinput_original($name="", $value="", $cdef="", $style="", $extra="", $linkfname="")
	{
		if(!$name) {return "<font color='#ff0000'>Error: input's name don't specify</font>";}
		$curind = substr(md5($name),0,5);
		
		$echo = "<span id='liddiv-".$curind."' onclick=\"this.style.display='none'; document.forms['editform'].".$name.".style.display='inline';document.forms['editform'].".$name.".focus();document.getElementById('autotranslit-".$curind."').checked=false;\" title=\"".$this->core_echomui('adm_lidedit')."\" onmouseover=\"this.style.padding='0px';this.style.border='1px dashed #cfcfcf';\" onmouseout=\"this.style.padding='1px';this.style.border='0px';\" style=\"padding:1px;\">".$value."</span>";
		$echo .= "<input type='text' name='".$name."' style='display:none;' value='".$value."' ".$this->adm_inputclass($style)." ".$extra.">";
		$echo .= "<br><input type='checkbox' id='autotranslit-".$curind."' value='1' onclick=\"if(this.checked){document.getElementById('liddiv-".$curind."').style.display='inline'; document.forms['editform'].".$name.".style.display='none';}else{document.getElementById('liddiv-".$curind."').style.display='none'; document.forms['editform'].".$name.".style.display='inline';}\"><label for='autotranslit-".$curind."'>".$this->core_echomui('adm_autotranslit')."</label>";
		$echo .= "<br><span style='color:#f00;display:none' id='lidwarmes-".$curind."'>&nbsp;".$this->core_echomui('adm_lidwar')."&nbsp;<a href='#' onclick=\"document.getElementById('liddiv-".$curind."').innerHTML='".$value."';document.forms['editform'].".$name.".value='".$value."';document.getElementById('autotranslit-".$curind."').checked=false;document.getElementById('liddiv-".$curind."').style.display='none';document.forms['editform'].".$name.".style.display='inline';return false;\">".$this->core_echomui('adm_lidwarretdef')."</a></span>";
		
		$echo .= "<script language=\"JavaScript\">
			var lettervar = new Array();
			lettervar['A'] = lettervar['a'] = 'a';
			lettervar['B'] = lettervar['b'] = 'b';
			lettervar['C'] = lettervar['c'] = 'c';
			lettervar['D'] = lettervar['d'] = 'd';
			lettervar['E'] = lettervar['e'] = 'e';
			lettervar['F'] = lettervar['f'] = 'f';
			lettervar['G'] = lettervar['g'] = 'g';
			lettervar['H'] = lettervar['h'] = 'h';
			lettervar['I'] = lettervar['i'] = 'i';
			lettervar['J'] = lettervar['j'] = 'j';
			lettervar['K'] = lettervar['k'] = 'k';
			lettervar['L'] = lettervar['l'] = 'l';
			lettervar['M'] = lettervar['m'] = 'm';
			lettervar['N'] = lettervar['n'] = 'n';
			lettervar['O'] = lettervar['o'] = 'o';
			lettervar['P'] = lettervar['p'] = 'p';
			lettervar['Q'] = lettervar['q'] = 'q';
			lettervar['R'] = lettervar['r'] = 'r';
			lettervar['S'] = lettervar['s'] = 's';
			lettervar['T'] = lettervar['t'] = 't';
			lettervar['U'] = lettervar['u'] = 'u';
			lettervar['V'] = lettervar['v'] = 'v';
			lettervar['W'] = lettervar['w'] = 'w';
			lettervar['X'] = lettervar['x'] = 'x';
			lettervar['Y'] = lettervar['y'] = 'y';
			lettervar['Z'] = lettervar['z'] = 'z';
			lettervar['А'] = lettervar['а'] = 'a';
			lettervar['Б'] = lettervar['б'] = 'b';
			lettervar['В'] = lettervar['в'] = 'v';
			lettervar['Г'] = lettervar['г'] = 'g';
			lettervar['Д'] = lettervar['д'] = 'd';
			lettervar['Э'] = lettervar['э'] = lettervar['Е'] = lettervar['е'] = 'e';
			lettervar['Ё'] = lettervar['ё'] = 'yo';
			lettervar['Ж'] = lettervar['ж'] = 'j';
			lettervar['З'] = lettervar['з'] = 'z';
			lettervar['Й'] = lettervar['й'] = lettervar['И'] = lettervar['и'] = 'i';
			lettervar['К'] = lettervar['к'] = 'k';
			lettervar['Л'] = lettervar['л'] = 'l';
			lettervar['М'] = lettervar['м'] = 'm';
			lettervar['Н'] = lettervar['н'] = 'n';
			lettervar['О'] = lettervar['о'] = 'o';
			lettervar['П'] = lettervar['п'] = 'p';
			lettervar['Р'] = lettervar['р'] = 'r';
			lettervar['С'] = lettervar['с'] = 's';
			lettervar['Т'] = lettervar['т'] = 't';
			lettervar['У'] = lettervar['у'] = 'u';
			lettervar['Ф'] = lettervar['ф'] = 'f';
			lettervar['Х'] = lettervar['х'] = 'h';
			lettervar['Ц'] = lettervar['ц'] = 'c';
			lettervar['Ч'] = lettervar['ч'] = 'ch';
			lettervar['Щ'] = lettervar['щ'] = lettervar['Ш'] = lettervar['ш'] = 'sh';
			lettervar['Ь'] = lettervar['ь'] = lettervar['Ъ'] = lettervar['ъ'] = '';
			lettervar['Ы'] = lettervar['ы'] = 'i';
			lettervar['Ю'] = lettervar['ю'] = 'yu';
			lettervar['Я'] = lettervar['я'] = 'ya';
			lettervar[' '] = '_';
			lettervar['.'] = '.';
			lettervar[','] = ',';
			lettervar['-'] = '-';
			lettervar['0'] = '0';
			lettervar['1'] = '1';
			lettervar['2'] = '2';
			lettervar['3'] = '3';
			lettervar['4'] = '4';
			lettervar['5'] = '5';
			lettervar['6'] = '6';
			lettervar['7'] = '7';
			lettervar['8'] = '8';
			lettervar['9'] = '9';
			
			function setlid".$curind."() {
				if(document.getElementById('autotranslit-".$curind."').checked)
				{
					var value = document.forms['editform'].".$linkfname.".value.split('');
					var newvalue = '';
					for(i=0;i<value.length;i++) {if(lettervar[value[i]]!=undefined) newvalue = newvalue+''+lettervar[value[i]]; else newvalue = newvalue+'';}
					document.getElementById('liddiv-".$curind."').innerHTML=newvalue;
					document.forms['editform'].".$name.".value=newvalue;
				}
				
				var deflid = '".$value."';
				if(deflid && document.forms['editform'].".$name.".value != deflid) document.getElementById('lidwarmes-".$curind."').style.display='inline';
				else document.getElementById('lidwarmes-".$curind."').style.display='none';
				setTimeout(\"setlid".$curind."()\", 300);
			} 
			var value = document.forms['editform'].".$linkfname.".value.split('');
			var newvalue = '';
			for(i=0;i<value.length;i++) {if(lettervar[value[i]]!=undefined) newvalue = newvalue+''+lettervar[value[i]]; else newvalue = newvalue+''+value[i];}
			if(newvalue=='".$value."') document.getElementById('autotranslit-".$curind."').checked=true;
			else
			{
				document.getElementById('liddiv-".$curind."').style.display='none';
				document.forms['editform'].".$name.".style.display='inline';
			}
			setlid".$curind."();
		</script>";
		$this->show_edit_strings[] = $name;
		return $echo;
	}
	// ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_LIDINPUT_ORIGINAL FUNCTION ^^^^^^^^^^^^^^^^^^^^^ простой input

        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_EDITOR FUNCTION VVVVVVVVVVVVVVVVVVVVV выводит редактор
        function adm_show_editor($name="",$text="",$id="",$h="",$w="",$class="",$extra="",$type="textarea",$wys_type="Default", $skin="default")
        {
                static $init_editor = array();
                $echo = "";

                $_SESSION['c_tpl_name'] = $this->config['tpl']; // запоминаем текущий шаблон, чтобы потом там искать css файл
                $genid = rand(0,100); // случайное число

                if($type && is_dir(DOCUMENT_ROOT."/editor/".$type) && file_exists(DOCUMENT_ROOT."/editor/".$type."/_run/_initial.php") && file_exists(DOCUMENT_ROOT."/editor/".$type."/_run/_cfg.php"))
                {
                        ob_start(); // начали запись в буфер
                                if(!$init_editor[$type])
                                {
                                        include(DOCUMENT_ROOT."/editor/".$type."/_run/_initial.php");
                                        $init_editor['fckeditor'] = 1;
                                }
                        include(DOCUMENT_ROOT."/editor/".$type."/_run/_cfg.php");
                        $echo .= ob_get_contents();// получили содержимое буфера
                        ob_end_clean ();// очистили буфер
                }else
                {
                        $echo .= "<textarea name='".$name."' id='".$id."' style='width:".$w.";height:".$h.";' ".$this->adm_inputclass()." ".$extra.">".$text."</textarea>";
                }
                $this->show_edit_strings[] = $name;
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_EDITOR FUNCTION ^^^^^^^^^^^^^^^^^^^^^ выводит редактор

        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_SELECT FUNCTION VVVVVVVVVVVVVVVVVVVVV input select
        function adm_show_select($name="", $cvalue="", $options=array(), $style="", $extra="",$type="")
        {
                static $init_sel;

                if(!$name) {return "<font color='#ff0000'>Error: select's name don't specify</font>";}
                if(!is_array($options)) $options = array();

				$is_array = 0;
				if(preg_match("/\[.*\]/iU",$name)) // if the name of the field is like "name[]", it means it is an array
				{
					$cvalue = split("\;",$cvalue);
					$is_array = 1;
				}

                if(!$init_sel && $type=="js")
                {
                        $echo .= "<script language='JavaScript' src='/".$this->adm_path."/moduls/js_select/select.js' type='text/javascript'></script>";
                        $init_sel = 1;
                }

                switch($type)
                {
                        case 'js':
                                $echo .= "<input type='hidden' name='".$name."' id='".$name."' value='".$cvalue."'>";
                                $echo .= "<table id='".$name."_select' border=0 cellpadding=0 cellspacing=0 class='mainselect' onclick=\"document.getElementById('".$name."_options').style.width=document.getElementById('".$name."_select').offsetWidth;showsubmenu('".$name."_options')\" onmousemove='cltimeout()' onmouseout=\"starttimeout('".$name."_options')\">";
                                $echo .= "<tr><td class='textselect'><nobr><div id='".$name."_maintext'>".(in_array($cvalue,array_keys($options)) ? $options[$cvalue] : "")."</div></td>";
                                $echo .= "<td align=right class='picselect'><img src='/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/select_pic.jpg'></td>";
                                $echo .= "</tr></table>";
                                $echo .= "<table border=0 cellpadding=0 cellspacing=0 class='itemsselect' style='display:none;' id='".$name."_options' onmouseout=\"starttimeout('".$name."_options')\" onmousemove=\"cltimeout()\">";
                                $i=0;
                                foreach($options as $val=>$text)
                                {
                                        $i++;
                                        if($val && substr_count($val,"|__this_opt_group"))
                                                $echo .= "<tr><td id='".$name."_".$i."' class='selectoptgroup'>".$text."</td></tr>";
                                        else
                                                $echo .= "<tr><td id='".$name."_".$i."' class='".(($cvalue==$val) ? "strselectselected" : "strselectnormal")."' onmouseover=\"overstr('".$name."','".$i."')\" onclick=\"checkstr('".$name."','".$i."')\"><div id='".$name."_opttext_".$i."' onclick=\"checkstr('".$name."','".$i."')\">".$text."</div><input type='hidden' id='".$name."_optvalue_".$i."' value='".$val."'></td></tr>";
                                }
                                $echo .= "</table>";
                        break;

                        default:
                                $echo .= "<select name='".$name."' ".$this->adm_selectclass($style)." ".$extra.">";
                                foreach($options as $val=>$text)
                                {
                                        if($val && substr_count($val,"|__this_opt_group"))
                                                $echo .= "<optgroup label='".$text."'></optgroup>";
                                        else
                                                $echo .= "<option value='".str_replace("'","&#039;",$val)."'".(((!is_array($cvalue) && $cvalue==$val) || (is_array($cvalue) && in_array($val,$cvalue))) ? " selected" : "").">".$text."</option>";
                                }
                                $echo .= "</select>";
                }
                $this->show_edit_strings[] = $name;
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_SELECT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ input select


        //VVVVVVVVVVVVVVVVVVVVVVV ADM_SHOW_DATE FUNCTION VVVVVVVVVVVVVVVVVVVVV
        function adm_show_date($name="", $time=0, $setctime=0, $format="d.m.Y", $style="", $extra="", $disabled=0, $disabled_renew=0)
        {
                /*
                $name - имя поля времени
                $time - время, которое впихнуть в форму
                $setctime - если установлено в 1 и при этом $time=0 то $time становится = time()
                $format - в каком формате выводить поля. Доступны параметры: d - день, m - месяц, y - год в формате 20xx, Y - год в формате xxxx, h - часы, i - минуты, s - секунды
                $style
                $extra
                */
                if(!$name) {return "<font color='#ff0000'>Error: data field's name don't specify</font>";}
                $time = intval($time);
                if(!$time && $setctime) $time = time();

				if(!$disabled){
                $echo = $format;
                $echo = str_replace("d","$%d%$",$echo);
                $echo = str_replace("m","$%m%$",$echo);
                $echo = str_replace("y","$%y%$",$echo);
                $echo = str_replace("Y","$%Y%$",$echo);
                $echo = str_replace("h","$%h%$",$echo);
                $echo = str_replace("H","$%H%$",$echo);
                $echo = str_replace("i","$%i%$",$echo);
                $echo = str_replace("s","$%s%$",$echo);

                $echo = str_replace("$%d%$",$this->adm_show_input($name."_day", ($time?date("d", $time):""),"","width:20px;","maxlength=2"),$echo);
                $echo = str_replace("$%m%$",$this->adm_show_input($name."_month", ($time?date("m", $time):""),"","width:20px;","maxlength=2"),$echo);
                $echo = str_replace("$%y%$","20".$this->adm_show_input($name."_year", ($time?date("y", $time):""),"","width:20px;","maxlength=2"),$echo);
                $echo = str_replace("$%Y%$",$this->adm_show_input($name."_fullyear", ($time?date("Y", $time):""),"","width:40px;","maxlength=4"),$echo);
                $echo = str_replace("$%h%$",$this->adm_show_input($name."_hour", ($time?date("h", $time):""),"","width:20px;","maxlength=2"),$echo);
                $echo = str_replace("$%H%$",$this->adm_show_input($name."_hour", ($time?date("H", $time):""),"","width:20px;","maxlength=2"),$echo);
                $echo = str_replace("$%i%$",$this->adm_show_input($name."_min", ($time?date("i", $time):""),"","width:20px;","maxlength=2"),$echo);
                $echo = str_replace("$%s%$",$this->adm_show_input($name."_sec", ($time?date("s", $time):""),"","width:20px;","maxlength=2"),$echo);
				}else{
					$echo = $time?date($format, $time):'-';
					if($disabled_renew) $time = time();
				}
				$echo .= $this->adm_show_hidden($name, $time);
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_SHOW_DATE FUNCTION ^^^^^^^^^^^^^^^^^^^^^


        //VVVVVVVVVVVVVVVVVVVVVVV ADM_GET_EDIT_SQL FUNCTION VVVVVVVVVVVVVVVVVVVVV получить скул запрос для обновления или добавления записи
        function adm_get_edit_sql($table="",$fields=array())
        {
                if(!$table) return false;
                $e_fields = split(";",$_POST['allshowedfields']);
                foreach($fields as $f=>$val)
                {
                        if(in_array($f, $e_fields))
                        $newfields[] = "`".$f."`=".$val;
                }
                if(is_array($newfields)) $newfields = join(", ",$newfields);
                else return false;
                if(intval($this->id))
                {
                        $sql = "UPDATE `".$table."` SET ".$newfields." WHERE `id`=".intval($this->id);
                }else
                {
                        $sql = "INSERT INTO `".$table."` SET ".$newfields;
                }
                return $sql;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_GET_EDIT_SQL FUNCTION ^^^^^^^^^^^^^^^^^^^^^ получить скул запрос для обновления или добавления записи

		function pre_pageconfigsafe(){
			if(!$_POST['template']) $_POST['template'] = '';
			if(!$_POST['component']) $_POST['component'] = 'default';
			if($this->id)
			{
				$sql = "SELECT `template`,`record_id` FROM `#__sitemap` WHERE `id`=".$this->id;
				$cur_recinfo = $this->fetch_assoc($this->query($sql));
				if($cur_recinfo['template']!=$_POST['template'])
				{
					if($cur_recinfo['template'])
					{
						$sql = "SELECT `config` FROM `#h_components` WHERE `title`='".$cur_recinfo['template']."'";
						$comconf = $this->fetch_assoc($this->query($sql));
						$comtbl = $this->adm_get_param($comconf['config']);
						if($comtbl['tbl'] && $cur_recinfo['record_id'])
						{
							$sql = "DELETE FROM `#".$comtbl['tbl']."` WHERE `id`=".intval($cur_recinfo['record_id']);
							$this->query($sql);
						}
					}
				}
			}
			if($_POST['template'] && (!$this->id || ($this->id && $cur_recinfo['template']!=$_POST['template'])))
			{
				$sql = "SELECT `config` FROM `#h_components` WHERE `title`='".$_POST['template']."'";
				$comconf = $this->fetch_assoc($this->query($sql));
				$comtbl = $this->adm_get_param($comconf['config']);
				if($comtbl['tbl'])
				{
					$sql = "INSERT INTO `#".$comtbl['tbl']."` SET `id`=''";
					$this->query($sql);
					$_POST['record_id'] = $this->insert_id();
				}
			}

			if(!$this->id){
				$sql = "SELECT MAX(`sort`) as `maxsort` FROM `#__sitemap` WHERE `pid`=".intval($_POST['id']);
				$maxsort = $this->fetch_assoc($this->query($sql));
				$_POST['sort'] = $maxsort['maxsort']+1;
			}
		}

        function get_edit_fields(){
                $return = array();
                if(!$this->adm_com_config['id']) return $return;
                $sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($this->adm_com_config['id'])." && `public`='1' && `pid`!=0";
                $res = $this->query($sql);
                while($row = $this->fetch_assoc($res))
                {
                        switch($row['type'])
                        {
                                case 'date':
                                        $_POST[$row['db_fname']] = $this->adm_gettimefrom($row['db_fname']);
                                break;
                                case 'lid':
                                      if(intval($_POST['addprelid'][$row['db_fname']]))
                                      {
                                       $sql = "SELECT `".$row['db_fname']."` FROM `#".$this->adm_com_config['config']['tbl']."` WHERE `id`=".intval($_POST['pid']);
                                       $rres = $this->query($sql);
                                       $rrow = $this->fetch_assoc($rres);
                                       if($rrow[$row['db_fname']]) $_POST[$row['db_fname']] = $rrow[$row['db_fname']]."/".$_POST[$row['db_fname']];
                                      }
                                    elseif($_POST["edit-input-prelidof".$row['db_fname']])
                                    {
                                     $_POST[$row['db_fname']] = trim($_POST["edit-input-prelidof".$row['db_fname']],"/")."/".$_POST[$row['db_fname']];
                                    }
                                    $_POST[$row['db_fname']] = trim($_POST[$row['db_fname']],"/");
                                break;
                        }

						$row['db_fname'] = preg_replace("/\[.*\]/iU","",$row['db_fname']); // if field name was like "name[something]" make it like "name"
						if(!is_array($_POST[$row['db_fname']])) // if this variable of the POST array is not an array itself, then
						{
							$_POST[$row['db_fname']] = array($_POST[$row['db_fname']]);
						}
						
						$val = array();
						foreach($_POST[$row['db_fname']] as $k=>$v)
						{
							switch($row['db_fieldtype'])
							{
                                case 'txtint':
									$val[] = intval($_POST[$row['db_fname']][$k]);
                                break;
                                case 'int':
                                    $val[] = intval($_POST[$row['db_fname']][$k]);
                                break;
                                case 'flt':
                                   $val[] = floatval($_POST[$row['db_fname']][$k]);
                                break;
								case 'nl2br':
									$val[] = addslashes(nl2br($_POST[$row['db_fname']][$k]));
								break;
                                default:
                                    $val[] = addslashes($_POST[$row['db_fname']][$k]);
							}
							$return[$row['db_fname']] = "'".join(";", $val)."'";
						}
                }
				$this->edit_fields = $return;
//                echo '<pre>';print_r($this->edit_fields);echo '</pre>';	exit;
//		integrate type of field "multiple select" and work with array of datas
                return $return;
        }

        //VVVVVVVVVVVVVVVVVVVVVVV ADM_CLOSE_BOOKMARKS FUNCTION VVVVVVVVVVVVVVVVVVVVV закрывает закладки
        function adm_close_bookmarks()
        {
                $echo = "";
                $echo .= "</div></table>";
                return $echo;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_CLOSE_BOOKMARKS FUNCTION ^^^^^^^^^^^^^^^^^^^^^ закрывает закладки




        /// upimg
        var $validmymes = array();                 // массив допустимых типов файлов для загрузки
        var $invalidmymes = array();        // массив не допустимых типов файлов для загрузки
        var $upfile;                                         // массив информации загружаемого файла
        var $fextension;                                 // расширение загружаемого файла
        var $filesize;                                        // размер загружаемого файла
        var $file_error_no = 200;                // код ошибки, если 200 - значит ошибки не было и всё хорошо
        var $error;                                         // текст ошибки или служебного сообщения
        var $fbasename;                                 // имя картинки загружаемой (то имя с которым картинка к нам пришла)
        var $need_resize = 0;                        // нужно ли пережимать картинку при загрузке
        var $new_width = 0;                         // если нужно пережимать картинку, то она станет с этой шириной (высота считается автоматически - пропорционально если не задана фиксированная высота в $new_height)
        var $new_height = 0;                         // если нужно пережимать картинку, то она станет с этой высотой
        var $cond_resize = 0;                        // условие пережимания картинки [-1 0 1] - если '-1' - пережимать если истинный размер картинки меньше, чем указанный в $new_width, т.е. сделать картинку не менне заданного размера; '0' - безусловное пережимание, т.е. сделать картинку заданного размера; '1' - пережимать если истинный размер картинки больше, чем указанный в $new_width, т.е. сделать картинку не более заданного размера.

        function adm_upload_file($field_name="", $newfname="", $validmymes=array(), $topath="", $replace=1, $need_resize=0, $new_width=0, $new_height=0, $toext="")
        {
                // проверяем, не нужно ли удалить файл
                if(is_array($_POST['delfile']) && sizeof($_POST['delfile']) && in_array($field_name,$_POST['delfile']))
                {
                        $this->adm_delete_file(DOCUMENT_ROOT."/".trim($topath,"/")."/".$_POST[$field_name]);
//                        $_POST[$field_name] = "";
                }
                if(!$field_name || !$topath) return $_POST[$field_name];
                if(!sizeof($_FILES[$field_name]) && sizeof($_FILES["file_".$field_name])) $prev = "file_";
                if(!sizeof($_FILES[$prev.$field_name]) || $_FILES[$prev.$field_name]['error']) return $_POST[$field_name];
                if($newfname=="_rand_") $newfname = substr(md5(time()),rand(0,22),10);

                $this->validmymes = $validmymes;


                $this->need_resize = $need_resize;
                if(intval($new_width)) $this->new_width = intval($new_width);
                if(intval($new_height)) $this->new_height = intval($new_height);
                $fname = $this->uploadfile(DOCUMENT_ROOT."/".trim($topath,"/")."/", $newfname, $prev.$field_name, $toext,$replace);
//                echo "!".DOCUMENT_ROOT."/".trim($topath,"/")."/"."?".$newfname."!";

                if($this->file_error_no==200)
                {
                        $this->adm_add_sys_mes($this->core_echomui('adm_uploadfile_ok').": '".$fname."'","ok");
                }else if($this->file_error_no==4)
                {
                        $this->adm_add_sys_mes($this->file_error,"war");
                }else
                {
                        $this->adm_add_sys_mes($this->core_echomui('adm_uploadfile_err')." ".$this->file_error,"err");
                }
                if($fname)
                {
                        if(!is_dir(DOCUMENT_ROOT."/".trim($topath,"/")."/".$_POST[$field_name]) && $_POST[$field_name]!=$fname) $this->adm_delete_file(DOCUMENT_ROOT."/".trim($topath,"/")."/".$_POST[$field_name]);
                        return $fname;
                }
                else return $_POST[$field_name];
        }

        function init_upimg($fname="")
        {
                if(sizeof($_FILES[$fname]) && $_FILES[$fname]['error']!=4)
                {
                        $this->upfile = $_FILES[$fname];
                        $info = pathinfo($this->upfile['name']);
                        $this->fextension = strtolower($info['extension']);
                        $this->filesize = $_FILES[$fname]['size'];
                        $this->fbasename = $_FILES[$fname]['name'];
                        $this->file_error_no = 200;
                        return true;
                }else
                {
                        $this->file_error_no = 1;
                        $this->error = 'runtime error';
                        return false;
                }
        }// >> end of function image()

        function validmymes()// проверка валидности mime типа (точнее проверяем не сам mime а просто расширение файла)
        {
                if(sizeof($this->validmymes))
                {
                        foreach($this->validmymes as $ext)
                        {
                                if(substr_count($this->fextension, $ext)) return true;
                        }
                        $this->file_error_no = 2;
                        $this->file_error = $this->core_echomui('adm_uploadfile_unsupfiletype');
                        return false;
                }

                if(sizeof($this->invalidmymes))
                {
                        foreach($this->invalidmymes as $ext)
                        {
                                if(substr_count($this->fextension, $ext))
                                {
                                        $this->file_error_no = 2;
                                        $this->file_error = $this->core_echomui('adm_uploadfile_unsupfiletype');
                                        return false;
                                }
                        }
                        return true;
                }
                if(!sizeof($this->validmymes) && !sizeof($this->invalidmymes)) return true;

                $this->file_error_no = 2;
                $this->file_error = $this->core_echomui('adm_uploadfile_unsupfiletype');
                return false;
        }// >> end of function validmymes()

        // загрузка картинки
        function uploadfile($topath="", $tofile="", $file_key_name="", $toextension="", $replace=1)
        {
                if(!$file_key_name || !$topath || !file_exists($topath))
                {
                        $this->file_error_no = 3;
                        $this->error = $this->core_echomui('adm_uploadfile_filesaveerror');
                        return false;// если не указали куда сохранять картинку
                }

                // инициализация загрузки
                if(!$this->init_upimg($file_key_name)) return false;

                if(!$tofile) {$tofile = $this->adm_translit($this->fbasename,1,0);}
                elseif(!$toextension){$tofile = $tofile.".".$this->fextension; }
                elseif($toextension=='_noext_'){$tofile = $tofile;}
                else {$tofile = $tofile.".".$toextension;}

                while(file_exists($topath.$tofile) && !$replace)
                {
                        $this->file_error_no = 4;
                        $this->error = $this->core_echomui('adm_uploadfile_savedascopy')." 'copy_".$tofile."'";
                        $tofile = "copy_".$tofile;
                }

                if($this->validmymes())// если тип файла допустимый, то загружаем его
                {
                        if($this->need_resize && ($this->new_width || $this->new_height))
                        {
                                $res = $this->img_resize($this->upfile['tmp_name'],$topath.$tofile,$this->new_width,$this->new_height);
                        }else
                        {
                                $trymove = 1;
                                $res = copy($this->upfile['tmp_name'],$topath.$tofile);
                        }

                        if(!$res)
                        {
                                $res = copy($this->upfile['tmp_name'],$topath.$tofile);
                        }

                        if($res)
                        {
                                if($this->file_error_no != 4) $this->error = $this->core_echomui('adm_uploadfile_savesuc');
                                $this->file_error_no = 200;
                                return $tofile;
                        }
                }
        }
        // пережимание картинки
        /***********************************************************************************
        Функция img_resize(): генерация thumbnails
        Параметры:
          $src             - имя исходного файла
          $dest            - имя генерируемого файла
          $width, $height  - ширина и высота генерируемого изображения, в пикселях
        Необязательные параметры:
          $rgb             - цвет фона, по умолчанию - белый
          $quality         - качество генерируемого JPEG, по умолчанию - максимальное (100)
        ***********************************************************************************/
        function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
        {
                 if (!file_exists($src)) return false;

                  $size = getimagesize($src);

                if ($size === false) return false;

        // Определяем исходный формат по MIME-информации, предоставленной
          // функцией getimagesize, и выбираем соответствующую формату
          // imagecreatefrom-функцию.

                $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
                  $icfunc = "imagecreatefrom" . $format;
                  if (!function_exists($icfunc)) return false;

                if($width && !$height)
                {
                          $ratio       = $width / $size[0];
                        if(!$height) {$height = floor($size[1] * $ratio);}
                          if($ratio>0 && (!$this->cond_resize || ($this->cond_resize == 1 && $size[0]>$width) || ($this->cond_resize == -1 && $size[0]<$width))) $use_x_ratio = true; else $use_x_ratio = false;

                        $new_width   = $use_x_ratio  ? $width  : $size[0];
                          $new_height  = $use_x_ratio ? $height : $size[1];
                          $new_left    = 0;
                          $new_top     = 0;
                }else if(!$width && $height)
                {
                          $ratio       = $height / $size[1];
                        if(!$width) {$width = floor($size[0] * $ratio);}
                          if($ratio>0 && (!$this->cond_resize || ($this->cond_resize == 1 && $size[1]>$height) || ($this->cond_resize == -1 && $size[1]<$height))) $use_x_ratio = true; else $use_x_ratio = false;

                        $new_width   = $use_x_ratio  ? $width  : $size[0];
                          $new_height  = $use_x_ratio ? $height : $size[1];
                          $new_left    = 0;
                          $new_top     = 0;
                }else if($height && $width && !$this->cond_resize)
                {
                        $new_width   = $width;
                          $new_height  = $height;
                          $new_left    = 0;
                          $new_top     = 0;
                }else if($height && $width && $this->cond_resize==1 && ($size[1]>$height || $size[0]>$width))
                {
                        // олучаем коэффициенты, на сколько надо уменьшить ширину и высоту
                        $minih = $size[1] / $height;
                        $miniw = $size[0] / $width;

                        if($minih>$miniw) // если по высоте надо уменьшить в большее число раз, чем по ширине, то за основу берём размер по высоте
                        {
                                $new_height = $height;
                                $new_width = intval($size[0]/$minih);
                        }else
                        {
                                $new_width = $width;
                                $new_height = intval($size[1]/$miniw);
                        }
                }else if($height && $width && $this->cond_resize==-1 && ($height>$size[1] || $width>$size[0]))
                {
                        // олучаем коэффициенты, на сколько надо уменьшить ширину и высоту
                        $minih = $height / $size[1];
                        $miniw = $width / $size[0];

                        if($minih>$miniw) // если по высоте надо уменьшить в большее число раз, чем по ширине, то за основу берём размер по высоте
                        {
                                $new_height = $height;
                                $new_width = intval($minih*$size[0]);
                        }else
                        {
                                $new_width = $width;
                                $new_height = intval($miniw*$size[1]);
                        }
                }
                else
                {
                        $new_width   = $size[0];
                          $new_height  = $size[1];
                          $new_left    = 0;
                          $new_top     = 0;
                }

                $isrc = $icfunc($src);
                  $idest = imagecreatetruecolor($new_width, $new_height);

                  imagefill($idest, 0, 0, $rgb);
                  imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
                  $new_width, $new_height, $size[0], $size[1]);

                if($this->setwatermark)
                {
                        $watermark = new watermark();
                        # создаем объекты-изображения используя исходные файлы (main.jpg и watermark.png)
                        $main_img_obj = $idest;
                        $watermark_img_obj = imagecreatefrompng( DOCUMENT_ROOT."/images/watermark/watermark.150x150.png");
                        # создаем изображение с водяным знаком - значение прозрачности альфа-канала   водяного знака установим в 66%
                        $idest = $watermark->create_watermark( $main_img_obj,  $watermark_img_obj, 15 );

                //                $idest = $this->create_watermark($idest, "www.laguna-design.ru", DOCUMENT_ROOT."/templates/default/capcha/arial.ttf", 0, 0, 255);
                }
                  ImageJPEG($idest, $dest, $quality);

                  imagedestroy($isrc);
                  imagedestroy($idest);
                  return true;

        }


        // удаление файла
        function adm_delete_file($src="", $dirdel=0)
        {
			$src = str_replace("//","/",$src);
                $src = preg_replace("/\/$/","",$src);
                if(!$src || !file_exists($src)) return $this->core_echomui('adm_deletefile_notfound');
                else if(is_dir($src))
                {
                        if($dirdel)
                        {
                                $d = opendir($src);
                                readdir($d);readdir($d); // прошли директории . и ..
                                while($file = readdir($d))
                                {
                                    $this->adm_delete_file($src."/".$file, 1);
                                }
                                closedir($d);
                                rmdir($src);
                        }
                }else 
			{
				$this->delete_img_cache($src);
				unlink($src);
			}
        }

	function delete_img_cache($src='')
	{
		if(!is_file($src)) return false;
		$src = str_replace("//","/",$src);
		$file = str_replace(DOCUMENT_ROOT,"",$src);
			
		$cachedir = DOCUMENT_ROOT."/_cache/images/".md5($file);
		$this->adm_delete_file($cachedir, 1);
	}


        //VVVVVVVVVVVVVVVVVVVVVVV ADM_TRANSLIT FUNCTION VVVVVVVVVVVVVVVVVVVVV берёт строку string и переводит в транслит
        function adm_translit($string = "",$tolover=0,$isurl=1)
        {
                // массив того, что переводить и во что переводить
                $translit = array(
                "а" => "a","б" => "b","в" => "v","г" => "g","д" => "d","е" => "e","ё" => "yo","ж" => "j","з" => "z","и" => "i",
                "й" => "i","к" => "k","л" => "l","м" => "m","н" => "n","о" => "o","п" => "p","р" => "r","с" => "s","т" => "t",
                "у" => "u","ф" => "f","х" => "h","ц" => "c","ч" => "ch","ш" => "sh","щ" => "sh","ъ" => "","ы" => "i","ь" => "",
                "э" => "e","ю" => "yu","я" => "ya"," "=>"_");

                //
                if($tolover)
                {
                        $string = strtolower($string);
                        $bigchars = array(
                        "А" => "a","Б" => "b","В" => "v","Г" => "g","Д" => "d","Е" => "e","Ё" => "yo","Ж" => "j","З" => "z","И" => "i",
                        "Й" => "i","К" => "k","Л" => "l","М" => "m","Н" => "n","О" => "o","П" => "p","Р" => "r","С" => "s","Т" => "t",
                        "У" => "u","Ф" => "f","Х" => "h","Ц" => "c","Ч" => "ch","Ш" => "sh","Щ" => "sh","Ъ" => "","Ы" => "i","Ь" => "",
                        "Э" => "e","Ю" => "yu","Я" => "ya");
                        $translit = array_merge($translit,$bigchars);
                }else
                {
                        $bigchars = array(
                        "А" => "A","Б" => "B","В" => "V","Г" => "G","Д" => "D","Е" => "E","Ё" => "YO","Ж" => "J","З" => "Z","И" => "I",
                        "Й" => "I","К" => "K","Л" => "L","М" => "M","Н" => "N","О" => "O","П" => "P","Р" => "R","С" => "S","Т" => "T",
                        "У" => "U","Ф" => "F","Х" => "H","Ц" => "C","Ч" => "CH","Ш" => "SH","Щ" => "SH","Ъ" => "","Ы" => "I","Ь" => "",
                        "Э" => "E","Ю" => "YU","Я" => "YA");
                        $translit = array_merge($translit,$bigchars);
                }

                if($isurl)
                {
                        $translit["/"] = "";
                        $translit["\\"] = "";
                        $translit["?"] = "";
                        $translit[":"] = "";
                        $translit["*"] = "";
                        $translit[">"] = "";
                        $translit["<"] = "";
                        $translit["|"] = "";
                        $translit["#"] = "";
                        $translit["№"] = "";
                        $translit["$"] = "";
                        $translit["%"] = "";
                        $translit["~"] = "";
                        $translit["`"] = "";
                        $translit["!"] = "";
                        $translit["@"] = "";
                        $translit["^"] = "";
                        $translit["&"] = "";
                        $translit[";"] = "";
                        $translit["="] = "";
                        $translit["("] = "";
                        $translit[")"] = "";
                        $string = str_replace("'","",$string);
                        $string = str_replace('"','',$string);
                }
                return strtr($string, $translit);
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF ADM_TRANSLIT FUNCTION ^^^^^^^^^^^^^^^^^^^^^ берёт строку string и переводит в транслит

        function adm_get_sitetpl_list()
        {
                $dir_root = DOCUMENT_ROOT."/templates";
                if(!file_exists($dir_root) || !is_dir($dir_root)) return array();
                $tpls = array();

                $dir = opendir($dir_root);
                readdir($dir);readdir($dir);
                while($tpl = readdir($dir))
                {
                        if(is_dir($dir_root."/".$tpl)) $tpls[$tpl] = $tpl;
                }

                closedir($dir);

                return $tpls;
        }


        function adm_get_admtpl_list()
        {
                $dir_root = DOCUMENT_ROOT."/".$this->adm_path."/template";
                if(!file_exists($dir_root) || !is_dir($dir_root)) return array();
                $tpls = array();

                $dir = opendir($dir_root);
                readdir($dir);readdir($dir);
                while($tpl = readdir($dir))
                {
                        if(is_dir($dir_root."/".$tpl)) $tpls[$tpl] = $tpl;
                }

                closedir($dir);

                return $tpls;
        }

        function adm_gettimefrom($fname="")
        {
                if(!$fname || !isset($_POST[$fname])) return false;
                $d = intval($_POST[$fname.'_day']);
                $m = intval($_POST[$fname.'_month']);
                $y = isset($_POST[$fname.'_fullyear'])?intval($_POST[$fname.'_fullyear']):intval($_POST[$fname.'_year'])+2000;//
                $h = intval($_POST[$fname.'_hour']);
                $i = intval($_POST[$fname.'_min']);
                $s = intval($_POST[$fname.'_sec']);

                $sum = $d+$m+$y+$h+$i+$s;

                if($sum && $sum!=2000) {return mktime($h,$i,$s,$m,$d,$y);}
                else {return $_POST[$fname];}
        }

        function adm_change_pas($c_pas="", $new_pas1 = "", $new_pas2 = "", $uid=0, $tab_f_name = "pas", $table="#h_users")
        {
                if(!$c_pas || !$uid || !$tab_f_name || !$table) return false;
                $c_pas = trim($c_pas);
                $new_pas1 = trim($new_pas1);
                $new_pas2 = trim($new_pas2);

                if(!$new_pas1 || !$new_pas2) {$this->adm_add_sys_mes($this->core_echomui('adm_changepas_war1'), "war"); return false;}
                if($new_pas1 != $new_pas2) {$this->adm_add_sys_mes($this->core_echomui('adm_changepas_war2'), "war"); return false;}

                $sql = "SELECT `".addslashes($tab_f_name)."` FROM `".addslashes($table)."` WHERE `id`=".intval($uid);
                $res = $this->query($sql);
                $row = $this->fetch_assoc($res);
                if($row[$tab_f_name] != md5($c_pas))  {$this->adm_add_sys_mes($this->core_echomui('adm_changepas_war3'), "war"); return false;}

                $newpas = md5($new_pas1);
                $this->adm_add_sys_mes($this->core_echomui('adm_changepas_ok1'), "ok");
                return $newpas;
        }

        function adm_getnew_pas($new_pas1 = "", $new_pas2 = "")
        {
                $new_pas1 = trim($new_pas1);
                $new_pas2 = trim($new_pas2);

                if(!$new_pas1 || !$new_pas2) {$this->adm_add_sys_mes($this->core_echomui('adm_changepas_war4'), "war"); return false;}
                if($new_pas1 != $new_pas2) {$this->adm_add_sys_mes($this->core_echomui('adm_changepas_war5'), "war"); return false;}
                $newpas = md5($new_pas1);
                $this->adm_add_sys_mes($this->core_echomui('adm_changepas_ok2'), "ok");
                return $newpas;
        }


        function adm_init_com_config($com_title="", $urlonsite="")
        {
                if(!$com_title || !$urlonsite) return false;

                $sql = "SELECT * FROM `#h_components` WHERE `title`='".addslashes($com_title)."' && `url`='".addslashes($urlonsite)."'";
                $res = $this->query($sql);
                $row = $this->fetch_assoc($res);

                $config = array();
                $array = split("\n",$row['config']);
                foreach($array as $v)
                {
                        $getparam = split("=",$v);
                        $config[$getparam[0]] = trim($getparam[1]);
                }
                $row['config'] = $config;
                return $row;
        }

        function adm_get_com_config($com_id=0)
        {
                if(!$com_id){
					if(!$this->way_ar[0]) return false;
		            $way = $this->way_ar;
			        if(end($way)=="edit") array_pop($way);
				    $way = join("/", $way);
					$sql = "SELECT * FROM `#h_components` WHERE `adm_title`='".addslashes($way)."' ORDER BY `sort`";
				}else $sql = "SELECT * FROM `#h_components` WHERE `id`=".intval($com_id);
                $res = $this->query($sql);
                $row = $this->fetch_assoc($res);

                $config = array();
                $array = split("\n",$row['config']);
                foreach($array as $v)
                {
                        $getparam = split("=",$v,2);
                        if(!$getparam[0]) continue;
                        $config[$getparam[0]] = trim($getparam[1]);
                }
                $row['config'] = $config;
                $this->adm_com_config = $row;
                return $row;
        }

        function adm_create_hat_ar()
        {
                $return = array();
                if(!$this->adm_com_config['id']) return $return;
                $sql = "SELECT * FROM `#h_components_listtable` WHERE `com_id`=".intval($this->adm_com_config['id'])." && `public`='1' ORDER BY `sort` ASC";
                $res = $this->query($sql);
                while($row = $this->fetch_assoc($res))
                {
                        $params = $this->adm_get_param($row['params']);

                        $return[] = array(
                                                "k"=>$row['db_fname'],
                                                "v"=>$this->core_echomui("admclist_".$row['mui_title']),
                                                "edit"=>$row['edit'],
                                                "del"=>$row['del'],
                                                "nosort"=>$row['nosort'],
                                                "type"=>$row['type'],
                                                "params"=>$params);
                }
                $this->hat_ar = $return;
                return $return;
        }

        function adm_showcolonpage_filtr()
        {
                $vararr = array();
                $vv = split(",", $this->adm_com_config['config']['colonpage_var']);
                foreach($vv as $v)
                {
                        $v = intval($v);
                        if(!$v) continue;
                        $vararr[$v] = $v;
                }
                if(!$this->adm_com_config['config']['colonpage_var']) $vararr = array("10"=>10,"20"=>20,"50"=>50);
                return $this->adm_showfiltr($this->core_echomui('admc_filter-strok'), "colonpage",$_SESSION['navig'][$this->ses_key]['colonpage'], $vararr);
        }

        function adm_get_param($inparams = "")
        {
                if(!$inparams) return array();

                $params = array();
                $mparams = split("\n", $inparams);
                foreach($mparams as $par)
                {
					$par = trim($par);
					if(!$par) continue;
                    $par = split("=", $par, 2);
					if(!trim($par[1])) $par[1] = trim($par[0]);
                    $params[trim($par[0])]=trim($par[1]);
                }
                return $params;
        }

function adm_getcomids($comids=0)
{
	static $dopcom_ids=array();

	$sql = "SELECT DISTINCT(`pid`) FROM `#__sitemap` WHERE `com_id`=".$comids;
	$r = $this->query($sql);
	$pids = array();
	while($pid = $this->fetch_assoc($r)) $pids[] = $pid['pid'];
				
	$sql = "SELECT DISTINCT(`com_id`) FROM `#__sitemap` WHERE `id` IN (".join(",",$pids).")";
		
	$r = $this->query($sql);
	while($comid = $this->fetch_assoc($r)) {if(in_array($comid['com_id'],$dopcom_ids)) continue; $dopcom_ids[] = $comid['com_id'];$this->adm_getcomids($comid['com_id']);}

	return $dopcom_ids;
}

/////////////////////////////////// WILL FILE MANAGER FUNCTIONS /////////////////////////////////////
function fmgr_baseurl($url = '')
{
	$url = str_replace("\\","/",$url);
	$url = str_replace("//","/",$url);
	$url = str_replace("../","",$url);
	$url = str_replace("./","",$url);
	return $url;
}

function fmgr_makelink($path)
{
	$path = trim($path,"/");
	$path = split("/",$path);
	$link = '';
	$username = '';
	for($i=0;$i<sizeof($path);$i++)
	{
		$link .= $path[$i].'/';
		$dirpath = rtrim(DOCUMENT_ROOT."/".$this->base_file_path."/".trim(str_replace("./","",$link),'/'),'/');
		
		// get description of the current folder
		$dirdescription = file_get_contents($dirpath."/.dirdescription");

		if($i<sizeof($path)-1) $path[$i] = "<a href='?".$link."' title=\"".str_replace('"',"'",$dirdescription)."\">".($username?$username:$path[$i])."</a>";
		elseif($username) $path[$i] = "<span class='fpathlast' title=\"".str_replace('"',"'",$dirdescription)."\">".$username."</span>";

		// get user name of the next folder
		$wholepath = $dirpath."/.usernames_dirs";
		$this->fmgr_readusernames($wholepath);
		$username = $this->fmgr_dirnames[$path[$i+1]];
	}
	
	return join("&nbsp;&raquo;&nbsp;",$path);
}
function fmgr_readusernames($what='',$type='dir')
{
	if(!$what || !file_exists($what) || !is_file($what)) return array();

	if($type=='dir')
	{
		$this->fmgr_dirnames = array();
		$this->fmgr_dirnames = $this->adm_get_param(file_get_contents($what));
		foreach($this->fmgr_dirnames as $k=>$v) $this->fmgr_dirnames[$k] = trim($v);
		return $this->fmgr_dirnames;
	}
	if($type=='file')
	{
		$this->fmgr_filenames = array();
		$this->fmgr_filenames = $this->adm_get_param(file_get_contents($what));
		foreach($this->fmgr_filenames as $k=>$v) $this->fmgr_filenames[$k] = trim($v);
		return $this->fmgr_filenames;
	}
}
function fmgr_showdirname($basename='')
{
	if($basename=='') return $basename;
	$keys = array_keys($this->fmgr_dirnames);
	if(in_array($basename, $keys)) return $this->fmgr_dirnames[$basename];
	else return $basename;
}

function fmgr_showfilename($fname='', $maxlength=0)
{
	$keys = array_keys($this->fmgr_filenames);
	if(in_array($fname, $keys)) $fname = $this->fmgr_filenames[$fname];
	if($maxlength)
	{
		if(strlen($fname)>$maxlength) $fname = substr($fname,0,$maxlength)."&hellip;";
	}
	return $fname;
}
/////////////////////////////////// WAS FILE MANAGER FUNCTIONS /////////////////////////////////////

function go_com_export($comid=0, $level=0, $firstone=1)
{
	static $export_sql;
	static $addfiles = array();
	static $addfolders = array();
	static $getmui = array();
	static $wasaddedselect = array();
	static $oldcomids = array();
	$comspliter = "\n--- rxCMS export import command splitter ---\n";

	$tpl = $this->config['tpl']; // name of template

	if(!$comid) return;

/////////////// STEP 1
	$sql = "SELECT * FROM `#h_components` WHERE `id`=".$comid;
	$row = $this->fetch_assoc($this->query($sql));
	$ar = $this->mysql_get_fields("#h_components");
	$table_fields = array();
	$table_vals = array();
	$oldcomids[$level+1] = $row['id'];

	if(intval($row['content_tpl_by']))
	{
		foreach($oldcomids as $l=>$id)
		{
			if($row['content_tpl_by']==$id) $row['content_tpl_by'] = "{com_id:level_".$l."}";
		}
	}

	if($row['parent_parts'] && $row['parent_parts']!='root')
	{
		$parts = split(",",trim($row['parent_parts']));
		$newparts = array();
		foreach($parts as $oldid)
		{
			$newid = '';
			foreach($oldcomids as $l=>$id)
			{
				if($oldid==$id) $newid = "{com_id:level_".$l."}";
			}
			if($newid) $newparts[] = $newid;
			else $newparts[] = $oldid;
		}
		$row['parent_parts'] = join(",",$newparts);
	}
	

	foreach($ar as $item)
	{
		$table_fields[] = "`".$item['Field']."`";

		if($item['Field']=='pid' && intval($row[$item['Field']])) $table_vals[] = "'{com_id:level_".$level."}'";
		elseif($item['Field']=='pid' && !intval($row[$item['Field']])) $table_vals[] = "'0'";
		elseif($item['Extra']!="auto_increment") $table_vals[] = "'".addslashes($row[$item['Field']])."'";
		else $table_vals[] = "NULL";
	}
	$table_fields = join(",",$table_fields);
	$table_vals = join(",",$table_vals);
	$export_sql .= "com_".($level+1)."::INSERT INTO `#h_components` (".$table_fields.") VALUES (".$table_vals.");".$comspliter;

	$array = split("\n",$row['config']);
	$config = array();
	foreach($array as $v)
	{
		$getparam = split("=",$v,2);
		if(!$getparam[0]) continue;
		$config[$getparam[0]] = trim($getparam[1]);
	}
	if($config['tbl'])
	{
		$export_sql .= "createtable_".$config['tbl']."::".$this->get_dbtable2add("#".$config['tbl']);
	}
	if($row['use_component'])
	{
		$addfiles[] = "/templates/".$tpl."/components/".$row['use_component']."/".$row['tpl_file'];
		$addfiles[] = "/_components/".$row['use_component']."/".$row['use_component'].".php";
	}
	if($row['adm_title'])
	{
		$addfiles[] = "/".$this->adm_path."/_pages/".$row['adm_title'].".php";
		$addfiles[] = "/".$this->adm_path."/_pages/".$row['adm_title']."/edit.php";
	}
/////////////////////////


/////////////// STEP 2
	$sql = "SELECT * FROM `#h_components_listtable` WHERE `com_id`=".$comid;
	$res = $this->query($sql);
	$ar = $this->mysql_get_fields("#h_components_listtable");
	while($row = $this->fetch_assoc($res))
	{
		$table_fields = array();
		$table_vals = array();
		$row['com_id'] = '{com_id:level_'.($level+1).'}';
		if($row['mui_title']) $getmui[] = "admclist_".$row['mui_title'];
	

		foreach($ar as $item)
		{
			$table_fields[] = "`".$item['Field']."`";
	
			if($item['Extra']!="auto_increment") $table_vals[] = "'".addslashes($row[$item['Field']])."'";
			else $table_vals[] = "NULL";
		}
		$table_fields = join(",",$table_fields);
		$table_vals = join(",",$table_vals);
		$export_sql .= "listtable_".($level+1)."::INSERT INTO `#h_components_listtable` (".$table_fields.") VALUES (".$table_vals.");".$comspliter;
	}	
/////////////////////////


/////////////// STEP 3
	$sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".$comid;
	$infa = $this->core_get_tree($sql);
	$rows = $this->core_get_tree_keys(0, array(), $infa, 0, 1);
	$ar = $this->mysql_get_fields("#h_components_listedittable");
	foreach($rows as $row)
	{
		$table_fields = array();
		$table_vals = array();
		$row['com_id'] = '{com_id:level_'.($level+1).'}';
		$row['pid'] = $row['this_space']?'{pid}':'0';

		if($row['this_space'])
		{
			$array = split("\n",$row['params']);
			$config = array();
			foreach($array as $v)
			{
				$getparam = split("=",$v,2);
				if(!$getparam[0]) continue;
				$config[$getparam[0]] = trim($getparam[1]);
			}	
			if(intval($config['selectid']) && !in_array($config['selectid'],$wasaddedselect))
			{
				$sql = "SELECT * FROM `#h_components_selects` WHERE `id`=".intval($config['selectid']);
				$selectrow = $this->fetch_assoc($this->query($sql));
				$selar = $this->mysql_get_fields("#h_components_selects");
				$table_fields1 = array();
				$table_vals1 = array();
				foreach($selar as $item1)
				{
					$table_fields1[] = "`".$item1['Field']."`";
			
					if($item1['Extra']!="auto_increment") $table_vals1[] = "'".addslashes($selectrow[$item1['Field']])."'";
					else $table_vals1[] = "NULL";
				}
				$table_fields1 = join(",",$table_fields1);
				$table_vals1 = join(",",$table_vals1);
				$export_sql .= "addselect_::INSERT INTO `#h_components_selects` (".$table_fields1.") VALUES (".$table_vals1.");".$comspliter;
				$wasaddedselect[] = $config['selectid'];
				$config['selectid'] = "{lastselid}";
				$newparams = '';
				foreach($config as $k=>$v) $newparams .= $k."=".$v."\n";
				$row['params'] = $newparams;
			}

			$i=1;
			while($config['fpath'.$i])
			{
				$addfolders[] = $config['fpath'.$i];
				$i++;
			}
		}

		if($row['mui_title']) $getmui[] = $row['mui_title'];
		foreach($ar as $item)
		{
			$table_fields[] = "`".$item['Field']."`";
	
			if($item['Extra']!="auto_increment") $table_vals[] = "'".addslashes($row[$item['Field']])."'";
			else $table_vals[] = "NULL";
		}
		$table_fields = join(",",$table_fields);
		$table_vals = join(",",$table_vals);
		$export_sql .= "listedittable_".($level+1)."::INSERT INTO `#h_components_listedittable` (".$table_fields.") VALUES (".$table_vals.");".$comspliter;
	}
/////////////////////////





/////////////// LOOP
	$sql = "SELECT `id` FROM `#h_components` WHERE `pid`=".$comid;
	$res = $this->query($sql);
	while($row = $this->fetch_assoc($res))
	{
		$level++;
		$this->go_com_export($row['id'], $level, 0);
	}
/////////////////////////

	if($firstone){
		$getmui = array_unique($getmui);
		foreach($getmui as $muicode)
		{
			$sql = "SELECT * FROM `#h_mui` WHERE `mui_code`='".addslashes($muicode)."'";
			$res = $this->query($sql);
			while($row = $this->fetch_assoc($res))
			{
				$export_sql .= "mui_".$row['mui_code']."::INSERT INTO `#h_mui` (`id`,`group`,`param`,`mui_code`,`mui_text`) VALUE (NULL,'".$row['group']."','".$row['param']."','".$row['mui_code']."','".$row['mui_text']."');".$comspliter;
			}
		}
		$addfiles = array_unique($addfiles);
		$addfolders = array_unique($addfolders);

		foreach($addfiles as $file)
		{
			if(is_file(DOCUMENT_ROOT.$file))
			{
				$filecontent = file_get_contents(DOCUMENT_ROOT.$file);
				$export_sql .= "addfile_".$file."::".$filecontent."".$comspliter;
			}
		}

		foreach($addfolders as $folder)
		{
			$export_sql .= "createfolder_::".$folder."\n--- rxCMS export import command splitter ---\n";
		}
	}
	return htmlspecialchars($export_sql);
}

function get_dbtable2add($tblname='',$ifnotexists=1)
{
	if(!$tblname) return;
	$ar = $this->mysql_get_fields($tblname);
	$addfields = array();
	$pri = '';

	foreach($ar as $item)
	{
		$addfields[] = "`".$item['Field']."` ".$item['Type']."".($item['Null']=='NO'?' NOT NULL':'')."".($item['Extra']=='auto_increment'?' AUTO_INCREMENT':'')."";
		if($item['Key']=='PRI') $pri = "PRIMARY KEY (`".$item['Field']."`)";
	}
	if($pri) $addfields[] = $pri;

	if(!sizeof($addfields))
	{
		$addfields[] = '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$addfields[] = '`pid` INT NOT NULL';
		$addfields[] = '`sort` INT NOT NULL';
		$addfields[] = "`public` ENUM('0','1') NOT NULL";
	}
	$sql = "CREATE TABLE ".($ifnotexists?'IF NOT EXISTS ':'')."`".$tblname."` (".join(',',$addfields).");".$comspliter;
	return $sql;
}

function go_com_import($import_sql='')
{
	if(!$import_sql) return;
	$com_new_id = array();
	$last_listedittable_id = array();
	
	$sqls = split("--- rxCMS export import command splitter ---", $import_sql);
	foreach($sqls as $item)
	{
		$item = trim($item);
		list($compart, $exepart) = split("::",$item,2);
		list($command, $param) = split("_",$compart,2);
//		echo $command." => ".$param." => ".$exepart."<hr>";
	
		$exepart = trim($exepart);
		switch($command)
		{
			case 'com':
				$sql = $this->go_import_changeid($exepart,$com_new_id,'{com_id:level_!numer!}');
				$this->query($sql);
				$com_new_id[$param] = $this->insert_id();
			break;
 
			case 'listtable':
				$sql = $this->go_import_changeid($exepart,$com_new_id,'{com_id:level_!numer!}');
				$this->query($sql);
			break;
 
			case 'listedittable':
				$sql = $this->go_import_changeid($exepart,$com_new_id,'{com_id:level_!numer!}');
				if($last_listedittable_id[$param]) $sql = str_replace('{pid}',$last_listedittable_id[$param],$sql);
				$this->query($sql);
				if(!$last_listedittable_id[$param]) $last_listedittable_id[$param] = $this->insert_id();
			break;

			case 'createtable':
				$sql = $this->go_import_changeid($exepart,$com_new_id,'{com_id:level_!numer!}');
				$sqls = split(";",$sql);
				foreach($sqls as $sql)
				{
					$sql = split("::",$sql,2);
					if(sizeof($sql)==2) $sql = $sql[1];
					else $sql = $sql[0];
					$this->query($sql);
				}
			break;

			case 'mui':
				$sql = "SELECT `id` FROM `#h_mui` WHERE `mui_code`='".addslashes($param)."'";
				if(!$this->num_rows($this->query($sql)))
				{
					$this->query($exepart);
				}
			break;
			
			case 'createfolder':
				$root = trim($exepart,'/');
				$root = split('/',$root);
				$totalfolder = array();
				foreach($root as $folder)
				{
					$totalfolder[] = $folder;
					$folder = DOCUMENT_ROOT.'/'.join('/',$totalfolder);
					if(!is_dir($folder)) mkdir($folder,0777);
				}
			break;

			case 'addfile':
				$file = DOCUMENT_ROOT.$param;
				if(!is_file($file))
				{
					$f = fopen($file,'w');
					fwrite($f,$exepart);
					fclose($f);
				}
			break;
		}
	}
	

}

function go_import_changeid($text='',$arrayofid = array(),$codilo='')
{
	if(!$text || !$codilo || !sizeof($arrayofid)) return $text;
	foreach($arrayofid as $k=>$id)
	{
		$curcodilo = str_replace("!numer!",$k,$codilo);
		$text = str_replace($curcodilo,$id,$text);
	}
	return $text;
}

}// end of class main

?>