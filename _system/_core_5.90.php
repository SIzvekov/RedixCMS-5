<?php //v.2.0.
/* RedixCMS 4.0
Файл главного класса
*/

class core extends database {
        var $header;// массив команд, которые выполняются в голове документа
        var $url_get = array(); // запрошенный url массив
        var $url_get_string = ""; // запрошенный url строка
        var $param = ""; // параметр из url массива или из БД
        var $config = array(); // глобальный конфиг
        var $avtorizerror = ""; // ошибка авторизации пишется сюда
        var $core_error = array(); // ошибки ядра
        var $thishomepage = 0; // будет 1, если запрошенная страница - главная
        var $pathway = array(); // массив хлебных крошек, его определяет компонент, главная страница не входит в массив
        var $chache_file = ""; // имя файла кэша страницы
        var $com_chache_file = ""; // имя файла кэша компонента
        var $exec_timer = 0; // время выполнения скрипта в секундах
        var $will_reload = 0; // флаг, установка в 1 означает будущий релоад страницы
        var $paramid = 0; // id параметра из БД
        var $mui = array(); // массив ключей многоязычного интерфейса
		var $kapcha_field_name = ''; // name of a field with kapcha code
		var $formsendtype = 'post';

        /* переменные компонента */
        var $component_name = ""; // имя вызванного компонента
        var $component_config = array(); // массив конфига компонента
        var $component_ftypes = array(); // массив в котором хранятся типы полей БД текущего компонента
        var $component_id = 0; // id компонента
        var $page_info = array(); // массив данных о странице из таблицы с картой сайта
        var $component_mainfile = ""; // имя главного файла компонента
        var $component_mantitle = ""; // имя компонента для человека
        var $component_cache_page = 0; // кэшировать код компонента
        var $component_cache_page_ttl = 0; // время жизни кода компонента
        var $component_cache_sql = 0; // кэшировать скул запросы компонента
        var $component_cache_sql_ttl = 0; // время жизни скул запросов компонента
        var $com_cache_ses_arr = array(); // массив - кусок массива SESSION который влияет на кэш файла

        /* переменные модуля */
        var $modul_name = ""; // имя вызванного модуля
        var $modul_data = array(); // хранит массив данных, переданных в модуль, хранит только во вромя выполнения модуля
        var $modul_cache_page = 0; // кэшировать код модуля
        var $modul_cache_page_ttl = 0; // время жизни кода модуля
        var $mod_chache_file = ""; // файл кэша модуля

        function core()
        {
				$this->browser();
                // 0) стартуем таймер
                $this->exec_timer = $this->core_getmicrotime();

                // 1) подключаемся к БД
                $this->db_connect();
                if(sizeof($this->db_errors)) {
                        $this->core_debug(1,0,1,0);
                        $this->core_fatal_error("Initial DataBase Error");
                }

                // 2) Определение префикса базы хоста
                $sql = "SELECT * FROM `#s_cmshosts` WHERE `host`='".addslashes(HTTP_HOST)."'";
                $res = $this->query($sql);
                if(!$this->num_rows($res))// не получилось найти запрошенный хост
                {
                        $HTTP_HOST = preg_replace("/^www*\./iU", "", HTTP_HOST); // пробуем его найти без www.
                        $sql = "SELECT * FROM `#s_cmshosts` WHERE `host`='".addslashes($HTTP_HOST)."'";
                        $res = $this->query($sql);

                        if(!$this->num_rows($res))// не получилось найти запрошенный хост и без www.
                        {
                                $sql = "SELECT * FROM `#s_cmsextrahosts` WHERE `host`='".addslashes(HTTP_HOST)."'"; // ищем такой хост в таблице дополнительных хостов
                                $res = $this->query($sql);
                                if(!$this->num_rows($res))// не удалось найти такой хост в таблице дополнительных хостов
                                {
                                        $HTTP_HOST = preg_replace("/^.*\./iU", "", HTTP_HOST); // ищем его в виде конструкции *.... где "*" - любой хост n-го уровня
                                        $HTTP_HOST = "*.".$HTTP_HOST;
                                        $sql = "SELECT * FROM `#s_cmsextrahosts` WHERE `host`='".addslashes($HTTP_HOST)."'";
                                        $res = $this->query($sql);
                                }
                                $row = $this->fetch_assoc($res);
								$this->cms_licens = $row['licens'];
								$this->cms_licens_host = $row['host'];
                                $sql = "SELECT * FROM `#s_cmshosts` WHERE `id`=".intval($row['hostid']);
                                $res = $this->query($sql);
                        }
                }
                $row = $this->fetch_assoc($res);
                $hostid = $row['id']; // id хоста из БД
				if(!isset($this->cms_licens)){$this->cms_licens = $row['licens'];$this->cms_licens_host = $row['host'];}
                if(!intval($hostid)) $this->core_fatal_error("Initial HOST error");
                define(DB_HOST_PREFIX,$row['db_prefix']); // префикс хоста

                // 3) определяем url массив
                $REQUEST_URI = split("\?",REQUEST_URI,2);
                    $get = $REQUEST_URI[0];
                    $get = explode('/', trim($get, '/'));
                if($get[0] == 'index.php') { array_shift($get); }

                // 4) Определение параметра (из url массива или из БД)
                if(sizeof($get)>0)
                {
                    $this->param = array_shift($get); // первый в url'e пункт - параметр
                	if($this->param==ADMINDIRNAME && $this->isadmin){
                		$this->param = array_shift($get);
                	}
                }if($_GET['lang_param'])
				{
					$this->param = $_GET['lang_param'];
				}
                if(!$this->param) // если из урла не получилось достать параметр, то параметр берём из БД
                {
                        $this->param = $this->core_get_def_param($hostid);
                }else // если из урла получилось вычленить параметр, то проверяем, есть ли такой параметр в БД
                {
                        $sql = "SELECT `id` FROM `#s_params` WHERE `hostid`=".intval($hostid)." && `par`='".addslashes($this->param)."'";
                		$res = $this->query($sql);
                        if(!$this->num_rows($res)) // если такого параметра в БД нет, то возвращаем его в url и берём параметр из самой БД, тот, что поумолчанию
                        {
                                array_unshift($get, $this->param);
                                $this->param = $this->core_get_def_param($hostid);
                        }else
                        {
                                $paramid_row = $this->fetch_assoc($res);
                        	    $this->paramid = $paramid_row['id'];
                        }
                }
        		if($this->isadmin) {
        			array_unshift($get, ADMINDIRNAME);
        			$_SESSION['adm_param'] = $this->param;
        		}
                $this->url_get = $get;

                // 5) определяем префикс для запрошенного параметра
                $sql = "SELECT `db_prefix` FROM `#s_params` WHERE `hostid`=".intval($hostid)." && `par`='".addslashes($this->param)."'";
                $res = $this->query($sql);
                $row = $this->fetch_assoc($res);
                define(DB_PARAM_PREFIX,$row['db_prefix']); // префикс параметра

                // 6) определяем конфиг сайта из БД
                $this->core_get_dbconfig();

                // 7) определяем $this->url_get по данным из конфига, если он пустой
                if(sizeof($this->url_get)<1 || (sizeof($this->url_get)==1 && $this->url_get[0]==trim($this->config['home_url'], '/'))) {$this->url_get = explode('/', trim($this->config['home_url'], '/')); $this->thishomepage=1;}

                // 8) получаем mui ключи и значения
                $this->core_readmuifile();

				// 9) Определяем список всех параметров
				$this->core_get_param_list($hostid);

				$this->check_licens();
        }

		function check_licens()
		{
			if(defined("NO_LICENS_USE")) return true;
			$host = 'redixcms.ru';
			$query = "GET /docha.php?k=".$this->cms_licens."&d=".$this->cms_licens_host."&l=".$this->param." HTTP/1.0\r\n".
			 "Host: $host\r\n".
			 "User-Agent: Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.5) Gecko/2008121623 Ubuntu/8.10 (intrepid) Firefox/3.0.5\r\n".
			 "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
			 "Accept-Language: en-us,en;q=0.5\r\n".
			 "Accept-Charset: UTF-8,*\r\n".
			 "Connection: close\r\n\r\n";

			$fp = fsockopen($host, 80, $errno, $errstr, 5);
			if(!$fp) $this->core_fatal_error("Can't check the CMS licens");
			
			fwrite($fp, $query);
			$response = $this->parse_http_response($fp);
			fclose($fp);
			if($response['body']) // get an answer
			{
				$ans = split("\n",$response['body']);
				if($ans[0]==666) {$this->core_fatal_error($ans[1]); return false;}
			}
			return true;
		}
		function parse_http_response($fp)
		{
			$response = array('version'	=> "",
					  'code'	=> "",
					  'text'	=> "",
					  'headers'	=> array(),
					  'cookies'	=> array(),
					  'body'	=> "");

	$headers = &$response['headers'];
	$cookies = &$response['cookies'];
	$body = &$response['body'];

	$stt = 0;
	while(!feof($fp)) {
		$s = fgets($fp, 8192);
		switch($stt) {
			case 0:
				$s = rtrim($s);
				//echo "$s\n";
				list($response['version'], $response['code'], $response['text']) = explode(" ", $s, 3);
				$stt = 1;
				break;
			case 1:
				$s = rtrim($s);
				//echo "$s\n";
				if(strlen($s) > 0) {
					list($key, $value) = explode(":", $s, 2);
					$key = strtolower($key);
					$value = ltrim($value);
					if($key == "set-cookie") {
						list($info, $attributes) = explode(";", $value, 2);
						list($nme, $vle) = explode("=", $info, 2);
						$cookies[$nme] = $vle;
					}
					else $headers[$key] = $value;
				}
				else $stt = 2;
				break;
			case 2:
				$body .= $s;
		}
	}

	fclose($fp);

	return $response;
	}
        function core_get_def_param($hostid=0)
        {
                if(!$hostid) return false;
        		if($_SESSION['adm_param'] && $this->isadmin) return $_SESSION['adm_param'];

                $sql = "SELECT `id`, `par` FROM `#s_params` WHERE `hostid`=".intval($hostid)." ORDER BY `default` DESC LIMIT 0,1"; // берём параметр, который относится к этому хосту и является поумолчанию
                $res = $this->query($sql);
                $row = $this->fetch_assoc($res);
                $this->paramid = $row['id'];
                return $row['par'];
        }

		function core_get_param_list($hostid=0)
		{
                if(!$hostid) return false;

                $sql = "SELECT * FROM `#s_params` WHERE `hostid`=".intval($hostid)." ORDER BY `id` ASC"; 
                $res = $this->query($sql);
				$this->params_list = array();
                while($row = $this->fetch_assoc($res)) $this->params_list[] = $row;
		}

        function core_get_dbconfig($id=1)
        {
                $sql = "SELECT * FROM `#__config` WHERE `id`=".intval($id)."";
                $res = $this->query($sql);
                $row = $this->fetch_assoc($res);
                $this->config = $row;
                $this->admin_par = $row['admin_par'];

                // устанавливаем кодировку
                if($row['charset']) $this->header[] = "header(\"Content-Type:text/html; charset=".$row['charset']."\");";
                return $row;
        }

        function login($isadmin=0)
        {
                $logof = isset($_GET['logoff']);
                if($logof)// выполняем выход из системы администрирования
                {
                        $this->header[] = "setcookie('login','',".(time()-3600).",'/');";// удаляем куки
                        $this->header[] = "setcookie('pas','',".(time()-3600).",'/');";// удаляем куки
						$_COOKIE['login'] = '';
						$_COOKIE['pas'] = '';
						$_SESSION['user'] = array();
                        unset($_SESSION['user']);
                        $this->avtorizerror = $this->core_echomui('logout_mes');// сообщение о выходе

						$request = "http://".HTTP_HOST.REQUEST_URI;
						$request = str_replace("?logoff&","?",$request);
						$request = str_replace("?logoff","",$request);
						$request = str_replace("&logoff","",$request);
						if(!$this->thisajax) $this->reload($request);
                        return false;
                }
                // получаем логин и пароль из сессии или из куков или из поста
                if($_POST['user_log'] && trim($_POST['user_pas']))
                {
                        $admin_log = trim($_POST['user_log']);
                        $admin_pas = md5(trim($_POST['user_pas']));
                }
                elseif($_SESSION['user']['login'] && $_SESSION['user']['pas'])
                {
                        $admin_log = $_SESSION['user']['login'];
                        $admin_pas = $_SESSION['user']['pas'];
                }else if($_COOKIE['login'] && $_COOKIE['pas'])
                {
                        $admin_log = $_COOKIE['login'];
                        $admin_pas = $_COOKIE['pas'];
                }
                
                if($_SERVER['REMOTE_ADDR'] == '85.90.211.225'){
     			$sql = "SELECT * FROM `#h_users` WHERE `login` = '".addslashes($admin_log)."' && `activ`='1'";
    		}else

                $sql = "SELECT * FROM `#h_users` WHERE `login` = '".addslashes($admin_log)."' && `pas` = '".addslashes($admin_pas)."' && `activ`='1'";
                $auth = $this->query($sql);

                if($auth = $this->fetch_assoc($auth))
                {
                        // записали в сессию информацию из БД
                        $_SESSION['user'] = $auth;

                        // если юзер отнесён к группе, то получаем информацию о группе
                        if($auth['group'])
                        {
                                $sql = "SELECT * FROM `#h_users_groups` WHERE `id`=".intval($auth['group']);
                                $auth_gr_res = $this->query($sql);
                                $auth_gr = $this->fetch_assoc($auth_gr_res);
                                $_SESSION['user']['group'] = $auth_gr;
                        }else
                        {
                                $_SESSION['user']['group'] = array();
                                $_SESSION['user']['group']['id'] = 0;
                                $_SESSION['user']['group']['isadmin'] = 0;
                        }

                        // переписываем шаблон пользователя, взятый из его конфига
                        if($_SESSION['user']['site_tpl']) $this->config['tpl'] = $_SESSION['user']['site_tpl'];
                        if($_SESSION['user']['adm_tpl']) $this->config['adm_tpl'] = $_SESSION['user']['adm_tpl'];

                        // zapis login & pas v COOKIE
                        if($_COOKIE['login']!=$admin_log || $_COOKIE['pas']!=$admin_pas)
                        {
                                $this->header[] = "setcookie('login','".$admin_log."',".(time()+44640).",'/');";
                                $this->header[] = "setcookie('pas','".$admin_pas."',".(time()+44640).",'/');";
                        }

                        // убиваем пост если он есть
                        if ($_POST['user_log'])
                        {
                                if(!$this->thisajax) $this->reload();
                        }
                        $this->avtorizerror = "";

                        // запоминаем время последнего визита юзера
                        $sql = "UPDATE `#h_users` SET `date_lastvizit`=".time()." WHERE `id`=".intval($_SESSION['user']['id']);
                        $this->query($sql);

                        if($isadmin==$_SESSION['user']['group']['isadmin'])        return "1";
                }
                if ($_POST['user_log'] || $_GET['errorlogin'])
                {
                        if ($_POST['user_log'])
                        {
                                // получаем строку гетов
                                $params = array();
                                foreach($_GET as $k=>$v)
                                {
                                        if($k!="errorlogin") $params[] = $k."=".$v;
                                }
                                $params = join("&",$params);
                                if($params) $query = $params."&errorlogin=".$admin_log;
                                else $query = "errorlogin=".$admin_log;
                                
								if($_GET['ref'])
								{
									$urlgo = str_replace(":quest;","?",str_replace(":amp;","&",$_GET['ref']));
								}
								else $urlgo = "/".join("/",$this->url_get)."/?".$query;

								$this->header[] = "header(\"Location: http://".HTTP_HOST.$urlgo."\");";
                        }
                        $this->avtorizerror = $this->core_echomui('login_error');
                        return 0;
                }
                else
                {
                        return 0;
                }
        }// >> end of log_in

        /* функция вывода страницы из кэша, возвращает массив */
        function core_get_cachepage()
        {
                $return_array = array();

                $return_array["cache"] = 0; // если этот параметр в 1, то значит выводим страницу из кэша
                $return_array["data"] = ""; // сама страница - HTML
	
                if(!PAGE_CACHE)
                {
                        return $return_array;
                }
                $cachedir = DOCUMENT_ROOT."/_cache/page/";
                // получаем имя файла для сохранения кэша. Имя вида "хост/запрос/параметры.htm"
                $hostname = md5(HTTP_HOST);
                $url = md5(join("/",$this->url_get));
                $query = md5(serialize($_GET).serialize($_POST).serialize($_SESSION));
                $this->chache_file = $cachedir.$hostname."/".$url."/".$query.".htm";


                // создаём папки для файлов кэша, если их ещё нет
                if(!file_exists($cachedir."/".$hostname) || !is_dir($cachedir."/".$hostname)) mkdir($cachedir."/".$hostname,0777);
                if(!file_exists($cachedir."/".$hostname."/".$url) || !is_dir($cachedir."/".$hostname."/".$url)) mkdir($cachedir."/".$hostname."/".$url,0777);

                // если файл кэша существует
                if(file_exists($this->chache_file))
                {
                        $_fctime = time() - filemtime($this->chache_file);

                        // если возраст файла кэша меньше или равен установленному времени жизни, то читаем содержимое этого файла
                        if($_fctime <= PAGE_CACHE_TTL)
                        {
                                $return_array["cache"] = 1;
                                $return_array["data"] = file_get_contents($this->chache_file);
                        }
                }

                return $return_array;
        }

        /* функция записи страницы в кэш */
        function core_put_cachepage($TEXT)
        {
                if(!PAGE_CACHE) return false;
                $cache_file = fopen($this->chache_file,'w');
                        fwrite($cache_file, $TEXT);
                fclose($cache_file);

                return true;
        }

        /* функция вывода кода компонента из кэша, возвращает массив */
        function core_component_get_cachepage()
        {
                $return_array = array();

                $return_array["cache"] = 0; // если этот параметр в 1, то значит выводим страницу из кэша
                $return_array["data"] = ""; // сама страница - HTML

                if(!$this->component_cache_page)
                {
                        return $return_array;
                }

                $cachedir = DOCUMENT_ROOT."/_cache/".($this->thisrss?'rss':'components')."/";
                // получаем имя файла для сохранения кэша. Имя вида "хост/запрос/параметры.htm"
                $hostname = md5(HTTP_HOST);
                $parametr = md5($this->param);
                $url = md5(join("/",$this->url_get));

                $session = array("user"=>$_SESSION['user'], "c_tpl_name"=>$_SESSION['c_tpl_name']);
                array_merge($session, $this->com_cache_ses_arr);
                // Убиваем ненужные, постоянно меня
                unset($session['user']['date_lastvizit']);
				if(isset($session['rxstatistic'])) unset($session['rxstatistic']);

                $query = md5(serialize($_GET).serialize($_POST).serialize($session));
                $this->com_chache_file = $cachedir.$hostname."/".$parametr."/".$url."/".$query.".htm";


                // создаём папки для файлов кэша, если их ещё нет
                if(!file_exists($cachedir."/".$hostname) || !is_dir($cachedir."/".$hostname)) mkdir($cachedir."/".$hostname,0777);
                if(!file_exists($cachedir."/".$hostname."/".$parametr) || !is_dir($cachedir."/".$hostname."/".$parametr)) mkdir($cachedir."/".$hostname."/".$parametr,0777);
                if(!file_exists($cachedir."/".$hostname."/".$parametr."/".$url) || !is_dir($cachedir."/".$hostname."/".$parametr."/".$url)) mkdir($cachedir."/".$hostname."/".$parametr."/".$url,0777);

                // если файл кэша существует
                if(file_exists($this->com_chache_file))
                {
                        $_fctime = time() - filemtime($this->com_chache_file);

                        // если возраст файла кэша меньше или равен установленному времени жизни, то читаем содержимое этого файла
                        if($_fctime <= $this->component_cache_page_ttl)
                        {
                                $return_array["cache"] = 1;
                                $return_array["data"] = file_get_contents($this->com_chache_file);

                                // получаем параметры данного компонента
                                $com_params = file_get_contents($this->com_chache_file.".param");
                                $com_params = unserialize($com_params);
                                define(META_TITLE, $com_params['meta_title']);
                                define(META_KEYWORDS, $com_params['meta_keywords']);
                                define(META_DESCRIPTION, $com_params['meta_description']);
                                define(PAGE_STATUS, $com_params['page_status']);
                                define(PAGE_NAME, $com_params['page_name']);
                                define(NAVIG_CUR_PAGE, $com_params['navig_cur_page']);
                                define(NAVIG_COLONPAGE, $com_params['navig_colonpage']);
                                define(NAVIG_COLALL, $com_params['navig_colall']);
                                define(NAVIG_COLPAGE, $com_params['navig_colpage']);
                                $this->pathway = $com_params['pathway'];
                        }
                }

                return $return_array;
        }

        /* функция записи компонента в кэш */
        function core_component_put_cachepage($TEXT)
        {
                if(!$this->com_chache_file) return false;
                $cache_file = fopen($this->com_chache_file,'w');
                        fwrite($cache_file, $TEXT);
                fclose($cache_file);

                // пишем параметры
                $param_text = array();
                $param_text['meta_title'] = META_TITLE;
                $param_text['meta_keywords'] = META_KEYWORDS;
                $param_text['meta_description'] = META_DESCRIPTION;
                $param_text['page_status'] = PAGE_STATUS;
                $param_text['page_name'] = PAGE_NAME;
                $param_text['navig_cur_page'] = NAVIG_CUR_PAGE;
                $param_text['navig_colonpage'] = NAVIG_COLONPAGE;
                $param_text['navig_colall'] = NAVIG_COLALL;
                $param_text['navig_colpage'] = NAVIG_COLPAGE;
                $param_text['pathway'] = $this->pathway;
                $param_text = serialize($param_text);

                $param_file = fopen($this->com_chache_file.".param",'w');
                        fwrite($param_file, $param_text);
                fclose($param_file);

                return true;
        }

        /* функция вывода кода модуля из кэша, возвращает массив */
        function core_modul_get_cachepage()
        {
                $return_array = array();

                $return_array["cache"] = 0; // если этот параметр в 1, то значит выводим страницу из кэша
                $return_array["data"] = ""; // сама страница - HTML

                if(!$this->modul_cache_page)
                {
                        return $return_array;
                }

                $cachedir = DOCUMENT_ROOT."/_cache/moduls/";
                // получаем имя файла для сохранения кэша. Имя вида "хост/запрос/параметры.htm"
                $hostname = md5(HTTP_HOST);
                $session = $_SESSION;
                if(isset($session['user']['date_lastvizit'])) unset($session['user']['date_lastvizit']);
				if(isset($session['rxstatistic'])) unset($session['rxstatistic']);
                $parametr = md5(REQUEST_URI.serialize($_POST).serialize($session));

                $query = md5($this->modul_name);
                $this->mod_chache_file = $cachedir.$hostname."/".$query."/".$parametr.".htm";


                // создаём папки для файлов кэша, если их ещё нет
                if(!file_exists($cachedir."/".$hostname) || !is_dir($cachedir."/".$hostname)) mkdir($cachedir."/".$hostname,0777);
                if(!file_exists($cachedir."/".$hostname."/".$query) || !is_dir($cachedir."/".$hostname."/".$query)) mkdir($cachedir."/".$hostname."/".$query,0777);

                // если файл кэша существует
                if(file_exists($this->mod_chache_file))
                {
                        $_fctime = time() - filemtime($this->mod_chache_file);

                        // если возраст файла кэша меньше или равен установленному времени жизни, то читаем содержимое этого файла
                        if($_fctime <= $this->modul_cache_page_ttl)
                        {
                                $return_array["cache"] = 1;
                                $return_array["data"] = file_get_contents($this->mod_chache_file);
                        }
                }

                return $return_array;
        }

        /* функция записи компонента в кэш */
        function core_modul_put_cachepage($TEXT)
        {
                if(!$this->mod_chache_file) return false;
                $cache_file = fopen($this->mod_chache_file,'w');
                        fwrite($cache_file, $TEXT);
                fclose($cache_file);
                return true;
        }

        /* функция записи статистики */
        function core_write_statistic()
        {
                if(!$this->config['gostat']) return false;
                $sessid = $_COOKIE['PHPSESSID'];
                if(!$sessid)
                {
                        $sessid = split("\=", SID);
                        $sessid = $sessid[1];
                }
                $ipstoplist = array();
                $ipstoplist = split(",", $this->config['ipstoplist']);
                foreach($ipstoplist as $k=>$v) $ipstoplist[$k] = trim($v);
                if(!is_array($ipstoplist)) $ipstoplist = array();

                $userip = $this->get_user_ip();

                $searchers = array("yandex","stackrambler","googlebot","msnbot","aport","webalta","yahoo","turtlescanner","mail.ru","yadirectbot", "bot", "spider");
                $uagentlower = strtolower($_SERVER['HTTP_USER_AGENT']);
                foreach($searchers as $snames) $issearcher+=substr_count($uagentlower, $snames);

                if($sessid && !in_array($userip,$ipstoplist) && !$issearcher)
                {
                        if(!$_SESSION['visitorid'])// вносим информацию о посетителе в базу
                        {
                                $sql = "INSERT INTO `#h_statistic_users` SET
                                        `ip`='".addslashes($userip)."',
                                        `session`='".addslashes($sessid)."',
                                        `useragent`='".addslashes($_SERVER['HTTP_USER_AGENT'])."',
                                        `browser`=".intval($this->browser())."
                                        ";
                                $this->query($sql);//добавили юзера в базу
                                $_SESSION['visitorid'] = intval($this->insert_id());
                        }

                        // пишем статистику в БД за сегодня
                        $sql = "INSERT INTO `#h_statistic` SET
                                `uid`=".intval($_SESSION['visitorid']).",
                                `page`='".addslashes(REQUEST_URI)."',
                                `ref`='".addslashes($_SERVER['HTTP_REFERER'])."',
                                `reg_id`=".intval($_SESSION['user']['id']).",
                                `time2gen`=".intval($this->core_show_exec_time()*1000).",
                                `date`=".intval(time()).",
                                `page_status`=".intval(PAGE_STATUS)."
                                ";
                        $this->query($sql);//добавили юзера в базу
                }
        }

        /* возвращает ip пользователя */
        function get_user_ip()
        {
                static $userip;
                if($userip) return $userip;

                if(getenv("HTTP_CLIENT_IP")) $userip = getenv("HTTP_CLIENT_IP");
             elseif(getenv("HTTP_X_FORWARDED_FOR")) $userip = getenv("HTTP_X_FORWARDED_FOR");
                else $userip = getenv("REMOTE_ADDR");

                return $userip;
        }

        /* возвращает код браузера */
        function browser()
        {
			$this->user_browser = array();
                if(eregi("MSIE",$_SERVER['HTTP_USER_AGENT']))
				{
					$agent=1;/*Explorer*/
					if(
						eregi("MSIE 6",$_SERVER['HTTP_USER_AGENT']) ||
						eregi("MSIE 5",$_SERVER['HTTP_USER_AGENT']) ||
						eregi("MSIE 4",$_SERVER['HTTP_USER_AGENT'])
						){$this->user_browser['bad']=1;}
					$this->user_browser['name']='Internet Explorer';
				}
                elseif(eregi("Chrome",$_SERVER['HTTP_USER_AGENT'])){$agent=6;/*Google Chrome*/$this->user_browser['name']='Google Chrome';}
                elseif(eregi("Safari",$_SERVER['HTTP_USER_AGENT'])){$agent=5;/*Safari*/$this->user_browser['name']='Safari';}
                elseif(eregi("opera",$_SERVER['HTTP_USER_AGENT'])){$agent=2;/*Opera*/$this->user_browser['name']='Opera';}
                elseif(eregi("Netscape",$_SERVER['HTTP_USER_AGENT'])){$agent=3;/*Netscape*/$this->user_browser['name']='Netscape';}
                elseif(eregi("Firefox",$_SERVER['HTTP_USER_AGENT']) && !eregi("Netscape",$_SERVER['HTTP_USER_AGENT'])){$agent=4;/*Mozilla*/$this->user_browser['name']='Firefox';}
				$this->user_browser['code'] = $agent;
                return $agent;
        }

		function core_go_proceedform()
		{
			if(!(is_array($_POST['proceedform']) && sizeof($_POST['proceedform']))) return false;
			foreach($_POST['proceedform'] as $formname)
			{
				$formfile = DOCUMENT_ROOT."/_components/_proceedform/".$formname.".php";
				if(file_exists($formfile))
				{
					require($formfile);
				}
			}
		}

        /* функция подключения компонента */
        function core_go_component()
        {
                if($this->will_reload) return false; // выходим из выполнения, если стоит флаг перезагрузки
                // получаем имя компонента из урла

                $this->url_get_string = join("/",$this->url_get);

                $sql = "SELECT * FROM `#__sitemap` WHERE `url`='".addslashes($this->url_get_string)."' && `public`='1'";
                $res = $this->query($sql);
                $this->page_info = $this->fetch_assoc($res);
				if($this->thisrss) $this->page_info['tplfile'] = "rss__".$this->page_info['tplfile'];
                if(!$this->num_rows($res) || !$this->page_info['component'])
                {
                        define(PAGE_STATUS,404);
                        return false;
                }else
                {
                        define(PAGE_STATUS,200);
                }

                define(PAGE_NAME, $this->page_info['title']);
                define(META_TITLE,$this->page_info['meta_title']);
                define(META_KEYWORDS,$this->page_info['meta_keywords']);
                define(META_DESCRIPTION,$this->page_info['meta_description']);
                $this->create_pathway();

                $this->component_name = $this->page_info['component'];

                if($this->page_info['com_id'])
                {
                        // проверяем, есть ли такой компонент в реестре, если нет, то выводим фатальную ошибку
                        $sql = "SELECT * FROM `#h_components` WHERE `id`=".intval($this->page_info['com_id']);

                        $res = $this->query($sql);
                        $row = $this->fetch_assoc($res);
                        $this->component_mainfile = $row['title']; // В версии 4.х параметры $this->component_name и $this->component_mainfile могли различаться. В версии 5.х они всегда равны и в дальнейшем используется только параметр $this->component_name. Параметр $this->component_mainfile сохранён для обратной совместимости с версией 4.х
                }

                $compfile = DOCUMENT_ROOT."/_components/".$this->component_name."/".$this->component_name.".php";
                if(!file_exists($compfile) || is_dir($compfile)) $this->core_fatal_error("Execute file of the required component <span style='font-weight: bold;'>'".$this->component_name."'</span> does not exists");

                $this->component_mantitle = $row['man_title'];

                // получаем конфиг компонента
                $this->component_config = $this->adm_get_param($row['config']);
                $this->component_id = intval($row['id']);
                $this->component_cache_page = $row['cache_page']; // кэшировать код компонента
                $this->component_cache_page_ttl = $row['cache_page_ttl']; // время жизни кода компонента

                // пытаемся взять код компонента из кэша
                $cache_data = $this->core_component_get_cachepage();
                if($cache_data["cache"]) $_TEXT = $cache_data["data"]; // если выводим содержимое страницы из кэша, то записываем в TEXT HTML страницы и не выполняем блок ниже
                else // если не вывели из кэша данные
                {
                        unset($row);
                        ob_start();
                                include($compfile);
                                $_TEXT = ob_get_contents();// получили содержимое буфера
                        ob_end_clean ();// очистили буфер
                        $this->core_component_put_cachepage($_TEXT); // записали в кэш страницу
                }// если не взяли из кэша

                define(COMPONENT_TEXT,$_TEXT);
                return true;
        }

        function create_pathway()
        {
                if($this->page_info['include_in_pathway']) $this->pathway[] = array("url"=>"/".$this->page_info['url']."/", "text"=>($this->page_info['pathway']?$this->page_info['pathway']:$this->page_info['title']));

                $pid = $this->page_info['pid'];
                while($pid)
                {
                        $sql = "SELECT * FROM `#__sitemap` WHERE `id`=".intval($pid)."";
                        $res = $this->query($sql);
                        $page_info = $this->fetch_assoc($res);

                        if($page_info['include_in_pathway'] && $page_info['url'] && $page_info['public']) $this->pathway[] = array("url"=>"/".$page_info['url'], "text"=>($page_info['pathway']?$page_info['pathway']:$page_info['title']));
                        $pid = $page_info['pid'];
                }
                $this->pathway = array_reverse($this->pathway);
        }

        function core_getcorrect_content($row = array())
        {
                if(!sizeof($row) || !is_array($row)) return array();

                if(is_array($this->component_ftypes['date'])) foreach($this->component_ftypes['date'] as $field => $item)
                {
                        $row["source:".$field] = $row[$field];
                        $row[$field] = date($item['format'], $row[$field]);
                }
                if(is_array($this->component_ftypes['file'])) foreach($this->component_ftypes['file'] as $field => $item)
                {
                        $row["source:".$field] = $row[$field];
                        $row[$field] = array();
                        $i = 1;
                        while($item['fpath'.$i])
                        {
							if(!file_exists(DOCUMENT_ROOT.$item['fpath'.$i].$row["source:".$field]) || is_dir(DOCUMENT_ROOT.$item['fpath'.$i].$row["source:".$field])) $row[$field][] = array();
							else $row[$field][] = array("url"=>$item['fpath'.$i].$row["source:".$field],"width"=>$item['w'.$i],"height"=>$item['h'.$i]);
							$i++;
                        }
                }
                if(is_array($this->component_ftypes['select'])) foreach($this->component_ftypes['select'] as $field => $item)
                {
                        $row["source:".$field] = $row[$field];
                        $row[$field] = trim($item['array'][$row["source:".$field]]);
                }

                if(is_array($this->component_ftypes['foto_list'])) foreach($this->component_ftypes['foto_list'] as $field => $item)
                {
//					$row["source:".$field] = $row[$field];

					$fotocom_conf = $this->adm_get_com_config($item['com_id']);
					
					$sql = "SELECT * FROM `#".addslashes($fotocom_conf['config']['tbl'])."` WHERE `pid`=".intval($row['id'])." && `public`='1' ORDER BY `sort` ASC";
					$fotos_res = $this->query($sql);
					$row[$field] = array();
					while($r = $this->fetch_assoc($fotos_res))
					{
						$imgurl = "/".trim($item['fpath1'],'/')."/".$r['img'];
						$filepath = DOCUMENT_ROOT.$imgurl;
						if(file_exists($filepath) && !is_dir($filepath))
						{
							$r['imgurl'] = $imgurl;
							$row[$field][] = $r;
						}
					}

                }

                return $row;
        }

        function get_typesarray($com_id=0)
        {
                $com_id = intval($com_id);
                if(!$com_id) return array();
                $this->component_ftypes = array();

                $typesarray = array("file","date","select","foto_list");
                for($i=0;$i<sizeof($typesarray);$i++)
                {
                        $sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".intval($com_id)." && `type`='".$typesarray[$i]."'";
                        $res = $this->query($sql);
                        while($type_row = $this->fetch_assoc($res))
                        {
                                $this->component_ftypes[$typesarray[$i]][$type_row['db_fname']] = $this->adm_get_param($type_row['params']);
                                if($typesarray[$i]=="select")
                                {
                                        $this->component_ftypes[$typesarray[$i]][$type_row['db_fname']]['array'] = $this->adm_get_select_array($this->component_ftypes[$typesarray[$i]][$type_row['db_fname']]['selectid'],'',1);
                                }
                        }
                }
        }

        function get_site_tree($start_pid=0,$deep=0,$includeunpub=1) // $deep - глубина проникновения дерева
        {
                $deep = intval($deep);
				$start_pid = intval($start_pid);
                $cond = "1";
                if(!$includeunpub) $cond = "`public`='1'";

                $sql = "SELECT * FROM `#__sitemap` WHERE ".$cond." ORDER BY `sort` ASC";
                $infa = $this->core_get_tree($sql);
                $infa = $this->core_get_tree_keys($start_pid, $selfils,$infa, 0, 1);

                if($deep) foreach($infa as $k=>$v)
                {
                        if($v['this_space']>=$deep) unset($infa[$k]);
                }

                return $infa;
        }

        /* функция подключения модуля */
        function core_modul($modulname="", $data=array()) // $data - массив данных, передаваемый в модуль
        {
                if($this->will_reload) return false; // выходим из выполнения, если стоит флаг перезагрузки
                if(!$modulname) return false; // если имя модуля не передано, то выходим
                $this->modul_name = $modulname;
                $this->modul_data = $data;

                // выдёргиваем инфу модуля из реестра модулей
                $sql = "SELECT * FROM `#h_moduls` WHERE `title`='".addslashes($this->modul_name)."'";
                $res = $this->query($sql);
                $modul_info_row = $this->fetch_assoc($res);
                $this->modul_cache_page = $modul_info_row['cache_page']; // кэшировать код компонента
                $this->modul_cache_page_ttl = $modul_info_row['cache_page_ttl']; // время жизни кода компонента

                // пытаемся взять код модуля из кэша
                $cache_data = $this->core_modul_get_cachepage();
                if($cache_data["cache"]) $_TEXT = $cache_data["data"]; // если выводим содержимое страницы из кэша, то записываем в TEXT HTML страницы и не выполняем блок ниже
                else // если не вывели из кэша данные
                {
						if(file_exists(DOCUMENT_ROOT."/_moduls/".$modulname."/functions.php")) {require_once(DOCUMENT_ROOT."/_moduls/".$modulname."/functions.php");}
                        
						if(intval($_SESSION['user']['group']['isadmin']) && file_exists(DOCUMENT_ROOT."/_moduls/".$modulname."_edit.php")) $modulfile = DOCUMENT_ROOT."/_moduls/".$modulname."_edit.php";
						else $modulfile = DOCUMENT_ROOT."/_moduls/".$modulname.".php";
                        if(!is_dir($modulfile) && file_exists($modulfile))
                        {
                                ob_start();
                                        include($modulfile);
                                        $_TEXT = ob_get_contents();// получили содержимое буфера
                                ob_end_clean ();// очистили буфер
                                $this->core_modul_put_cachepage($_TEXT); // записали в кэш страницу
                        }else
                        {
                                $this->core_error[] = "The file '".$modulfile."' of module doesn't exist";
                                $modultext = false;
                        }
                }
                $this->modul_name = "";
                $this->modul_data = array();
                $this->modul_cache_page = 0;
                $this->modul_cache_page_ttl = 0;
                $this->mod_chache_file = "";

//				if(intval($_SESSION['user']['group']['isadmin']) && $modul_info_row['editable']){$_TEXT = '<div style="border:1px solid #000">'.$_TEXT."</div>";}

                return $_TEXT;
        }

        /* функция подключения основного шаблона сайта */
        function core_site_template()
        {
				if($this->will_reload) return false; // выходим из выполнения, если стоит флаг перезагрузки
                if($this->config['close']) $filename = "closed.php";
                elseif(PAGE_STATUS==404) {$filename = "error404.php";$this->header[] = "header(\"HTTP/1.1 404 NOT FOUND\");";}
                elseif(PAGE_STATUS==403) $filename = "error403.php";
                elseif(isset($_GET['print']) && $_GET['print']=='') $filename = "print.php";
                elseif($this->core_checksitetplname("index_".$_GET['devhtml'].".php")) $filename = "index_".$_GET['devhtml'].".php";
                elseif($this->thishomepage && $this->core_checksitetplname("mainindex.php")) $filename = "mainindex.php";
                elseif($this->core_checksitetplname("index_".$this->url_get[0].".php")) $filename = "index_".$this->url_get[0].".php";
                else $filename = "index.php";

                $filenameroot = DOCUMENT_ROOT."/templates/".$this->config['tpl']."/page/".$filename;
                if(!file_exists($filenameroot) || is_dir($filenameroot))
                {
                        $this->core_error[] = "The file '".$filenameroot."' of site template doesn't exist";
                        return false;
                }else{
                    include($filenameroot);                    
                }
                return true;
        }

        function core_checksitetplname($tplname="")
        {
                if(!$tplname) return false;
                $filenameroot = DOCUMENT_ROOT."/templates/".$this->config['tpl']."/page/".$tplname;
                if(file_exists($filenameroot) && !is_dir($filenameroot)) return true;
                else return false;
        }

        /* функция выполнения головы */
        function core_go_header()
        {
                if(sizeof($this->header))
                { //выполняем голову
                        foreach($this->header as $action)
                        {
                                eval($action);
                        } // если есть что вставить в header то делаем это
                 }
        }

        /* функция вывод текст на экран */
        function core_show_page($TEXT)
        {
			if($_SESSION['user']['group']['isadmin']){
				$nocache = substr(md5(time()),0,5);

				$admpanel = '<style>
				div.rxadmpanel a {font-size:12px;color:#000;text-decoration: underline;}
				div.rxadmpanel a:hover {font-size:12px;color:#f00;text-decoration: none;}
				div.rxadmpanel div.panel{font-size:12px;height:23px;color:#000;padding:5px 5px 0px 5px;background:#f1eded; border:1px solid #919191;}
				div.rxadmpanel div.statlink{float:right;color:#000;}
				div.rxadmpanel div.admstatlink, div.rxadmpanel div.admstatlink a,div.rxadmpanel div.admstatlink a:hover {text-align:center;font-size:10px;color:#dcdcdc}
				div.rxadmpanel div.statwindow {display:none;color:#000;position:absolute;left:50%;z-index:999;margin-left:-150px;line-height:20px;top:50px;width:300px;font-size:12px;padding:5px;background:#f1eded; border:1px solid #919191;}
				div.rxadmpanel div.statwindowsub div.rxcmsstat_subcon {max-height: 500px;overflow:auto;}
				div.rxadmpanel input.statbutton {margin-left:-50px;width:100px;height:25px;position:absolute;left:50%;}
				div.rxadmpanel div.footerholder {clear:both;height:25px;}
				div.rxadmpanel div.rxstatsubitem {background:none;}
				div.rxadmpanel div.rxstatsubitem_h {background:#fff;}
				</style>';
				
				$admpanel .= '<div class="rxadmpanel">';

				$errors = sizeof($this->muierror)+sizeof($this->db_errors)+sizeof($this->core_error)+sizeof($this->muiblank)+sizeof($this->muifilesread_error);
				
				if(!$this->isadmin){
				$admpanel .= '<div class="panel">Вы авторизованы, как <span style="font-weight: bold;">'.$_SESSION['user']['login'].'</span> [<a href="http://'.HTTP_HOST.REQUEST_URI.(sizeof($_GET)?"&logoff":"?logoff").'">выйти</a>]. Перейти в <a href="/'.ADMINDIRNAME.'/'.$this->param.'/">панель администрирования</a>';

				$admpanel .= '<div class="statlink"><a href="" onclick="document.getElementById(\'rxcmsstat\').style.display=\'block\';document.getElementById(\'rxcmsstat_sub\').style.display=\'none\';return false;">Статистика</a>'.($errors?' (Ошибок: '.$errors.')':'').'</div>';
				$admpanel .= '</div>';
				}else {$admpanel .= '<div class="admstatlink"><a href="" onclick="document.getElementById(\'rxcmsstat\').style.display=\'block\';document.getElementById(\'rxcmsstat_sub\').style.display=\'none\';return false;">Статистика</a>'.($errors?' (Ошибок: '.$errors.')':'').'</div>';
				}

				$admpanel .= '<div id="rxcmsstat" class="statwindow"><span style=\'font-weight: bold;\'>Статистика:</span><br/>
				<a href="" onclick="rxstatload(\'muifiles\');return false;">Загружено языковых файлов</a>: '.sizeof($this->muifilesread).'<br/>
				<a href="" onclick="rxstatload(\'muifiles_error\');return false;">Ошибка загрузки языкового файла</a>: '.(sizeof($this->muifilesread_error)?'<font color="#f00">'.sizeof($this->muifilesread_error).'</font>':0).'<br/>
				<a href="" onclick="rxstatload(\'mui\');return false;">Определено языковых кодов</a>: '.sizeof($this->mui).'<br/>
				<a href="" onclick="rxstatload(\'muierror\');return false;">Не определено языковых кодов</a>: '.(sizeof($this->muierror)?'<font color="#f00">'.sizeof($this->muierror).'</font>':0).'<br/>
				<a href="" onclick="rxstatload(\'muiblank\');return false;">Определено пустых языковых кодов</a>: '.(sizeof($this->muiblank)?'<font color="#f00">'.sizeof($this->muiblank).'</font>':0).'<br/>
				<a href="" onclick="rxstatload(\'params_list\');return false;">Параметры</a>: '.sizeof($this->params_list).'<br/>
				<a href="" onclick="rxstatload(\'db_sql\');return false;">Выполнено SQL запросов</a>: '.sizeof($this->db_sql).'<br/>
				<a href="" onclick="rxstatload(\'core_error\');return false;">Ошибок выполнения программы</a>: '.(sizeof($this->core_error)?'<font color="#f00">'.sizeof($this->core_error).'</font>':0).'<br/>
				<a href="" onclick="rxstatload(\'db_errors\');return false;">Ошибок Базы Данных</a>: '.(sizeof($this->db_errors)?'<font color="#f00">'.sizeof($this->db_errors).'</font>':0).'<br/>

				Время построения страницы: '.round($this->core_show_exec_time(),2).' сек<br/>
				<input type="button" value="закрыть" onclick="document.getElementById(\'rxcmsstat\').style.display=\'none\'" class="statbutton" /><div class="footerholder"></div>
				</div>';

				$admpanel .= '<div id="rxcmsstat_sub" class="statwindow statwindowsub"><div id="rxcmsstat_subcon" class="rxcmsstat_subcon"></div><br/>
				<input type="button" value="назад" onclick="document.getElementById(\'rxcmsstat_sub\').style.display=\'none\';document.getElementById(\'rxcmsstat\').style.display=\'block\'" class="statbutton" /><div class="footerholder"></div>
				</div>';

				$admpanel .= '</div>';
				$admpanel .= '<script>function rxstatload(part){document.getElementById(\'rxcmsstat_subcon\').innerHTML=\'загрузка...\';loadXMLDoc(\'/ajax-index.php?isadm=1&page=rxstat&part=\'+part+\'&nocache='.$nocache.'\',\'rxcmsstat_subcon\');document.getElementById(\'rxcmsstat\').style.display=\'none\';document.getElementById(\'rxcmsstat_sub\').style.display=\'block\';}</script>';

				$_SESSION['rxstatistic'] = $this->create_rxstat();
				$_SESSION['rxstatistic']['nocache'] = $nocache;

//				if(!$this->isadmin) $TEXT = preg_replace("/(<body.*>)/iU","\\1{$admpanel}",$TEXT);
//				else $TEXT = preg_replace("/(<\/body>)/iU","{$admpanel}\\1",$TEXT);
			}
			if($this->user_browser['bad'] && !$_COOKIE['badbrownoshow'])
			{
				$badbrowser = '';
				$badbrowser = file_get_contents('http://redixcms.ru/_external_includes/bad_browser/content.php?l='.$this->param);

				if(!$badbrowser) $badbrowser = '<html><head></head><body style="margin:0px;padding:0px"><div id="badbrowser_subs" style="width:100%;height:100%;background:#000;"><div id="badbrowser_alert" style="position: absolute;z-index:9999;left:50%;top:50px;width:600px;margin-left:-300px;border:1px solid #000;background:#fff;color:#000;font-size:12px;font-family:verdana;padding:10px;"><div><img src="/templates/_common_images/error_white.png" alt="" border="0" align="left" style="margin-right:10px;"/>Вы используете устаревшую версию обозревателя "'.$this->user_browser['name'].'". Сайт может отображаться неверно.</div><div style="clear:both;height:10px;"></div><div>Для удобного и безопасного использования интернета, а также для корректного отображения этого и других сайтов, мы рекомендуем использовать браузер <a href="http://www.google.com/chrome/" style="color:#000;font-size:12px;font-family:verdana;text-decoration:underline;" target="_blank">Google Chrome</a>.<br/><br/><a href="http://www.google.com/chrome/" style="color:#080;font-size:12px;font-family:verdana;text-decoration:underline;" target="_blank">нажмите, чтобы скачать самый свежий Google Chrome</a></div><div align="center"><br/>
				<input type="button" value="закрыть сообщение и продолжить просмотр сайта" onclick="document.cookie = \'badbrownoshow=1\';location.reload();"></div></div></div></body></thml>';

				$TEXT = $badbrowser;
			}
                if(!$this->isadmin){
                        $TEXT = preg_replace("'[\r]+|[\n]+|[\s]+'", ' ', $TEXT);
                        $TEXT = preg_replace("'[\s]+'", ' ', $TEXT);
                        $TEXT = str_replace("> <", "><", $TEXT);
                }
                echo $TEXT;
//				print_r($this);
                return true;
        }

		function create_rxstat()
		{
			$stat = array();
			$stat['muifilesloaded'] = $this->muifilesread;
			$stat['muifilesloaded_error'] = $this->muifilesread_error;
			$stat['mui'] = $this->mui;
			$stat['muierror'] = $this->muierror;
			$stat['muiblank'] = $this->muiblank;
			$stat['params_list'] = $this->params_list;
			$stat['db_sql'] = $this->db_sql;
			$stat['core_error'] = $this->core_error;
			$stat['db_errors'] = $this->db_errors;
			
			return $stat;
		}

        /* функция выводит ошибки БД и ядра, если они были, а также выводит список сделанных sql запросов */
        function core_debug($isdebug=0, $core_err=1, $db_err=1, $sql=1)
        {
                if(!$this->config['debug'] && !$isdebug) return false;
                echo "<p>";
                if($core_err){
                        echo "<p><span style='font-weight: bold;'>Core errors:</span><br>".join("<br>\n", $this->core_error)."</p>";
                }
                if($db_err){
                        echo "<p><span style='font-weight: bold;'>DataBase errors:</span><br>".join("<br>\n", $this->db_errors)."</p>";
                }
                if($sql){
                        echo "<p><span style='font-weight: bold;'>SQL queries have been executed:</span><br>".join("<br>\n", $this->db_sql)."</p>";
                }
                echo "</p>";
                return true;
        }

        /* функция возвращает путь до файла шаблона компонента */
        function core_get_comtplname($filename="", $template="")
        {
                if(!$filename) {$filename = $this->page_info['template'].".php";}
				$filenameroot = DOCUMENT_ROOT."/templates/".$this->config['tpl']."/components/".$this->component_name."/".$filename;
				
				if(file_exists($filenameroot."_/".join('/',$this->url_get).".php") && !is_dir($filenameroot."_/".join('/',$this->url_get).".php"))
				{
					$filenameroot = $filenameroot."_/".join('/',$this->url_get).".php";
				}
                
                if(!file_exists($filenameroot) || is_dir($filenameroot))
                {
                        $this->core_error[] = "The file '".$filenameroot."' of service doesn't exist";
                        return false;
                }else return $filenameroot;
        }

        /* функция возвращает путь до файла шаблона модуля */
        function core_get_modtplname($filename="")
        {
                if(!$filename) {$filename = $this->modul_name.".php";}
                $filenameroot = DOCUMENT_ROOT."/templates/".$this->config['tpl']."/moduls/".$filename;
                if(!file_exists($filenameroot) || is_dir($filenameroot))
                {
                        $this->core_error[] = "The file '".$filenameroot."' of module doesn't exist";
                        return false;
                }else return $filenameroot;
        }

        /* функция проверяет существование файла из директории текущего шаблона */
        function tplfile_exists($filename="")
        {
                if(!$filename) {return false;}
                $filenameroot = DOCUMENT_ROOT."/templates/".$this->config['tpl']."/".$filename;
                if(!file_exists($filenameroot) || is_dir($filenameroot)) return false;
                else return "/templates/".$this->config['tpl']."/".$filename;
        }

        /* возвращает микротайм в секундах+миллисекундах */
        function core_getmicrotime()
        {
            list($usec, $sec) = explode(" ",microtime());
            return ((float)$usec + (float)$sec);
    }

        function core_show_exec_time()
        {
                return $this->core_getmicrotime()-$this->exec_timer;
        }

        /* функция установки в хеадер параметра перезагрузки */
        function reload($url="", $nobreakexec=0)
        {
                if(!$url) $url = REQUEST_URI;
                $this->header[] = "header(\"Location: ".$url."\");";
                if(!$nobreakexec) $this->will_reload = 1; // $nobreakexec означает, что не нужно прерывать выполнение компонентов и модулей
        }

        function core_fatal_error($error)
        {
                if(!$error) return false;
                die("<p><span style='font-weight: bold;'>Fatal RedixCMS error:</span><br><span style='color: #ff0000'>".$error."</span></p>");
        }

        function core_getimginfo($imgurl="", $altimg="")
        {
                $imgurl = DOCUMENT_ROOT."/".trim($imgurl,"/");
                $altimg = DOCUMENT_ROOT."/".trim($altimg,"/");

                $img = $imgurl;
                if(!$img || !file_exists($img) || is_dir($img)) {$img = $altimg;$alt=1;}
                if(!$img || !file_exists($img) || is_dir($img)) return array("e"=>0);

                $imgsize = "";
                $imgsize = getimagesize($img);
                $filesize = filesize($img);
                $imgurl = "http://".HTTP_HOST.str_replace(DOCUMENT_ROOT,"",$img);
                return array("e"=>"1","url"=>$imgurl,"width"=>$imgsize[0],"height"=>$imgsize[1],"mime"=>$imgsize['mime'],"size"=>$filesize,"altimg"=>intval($alt));
        }

        //VVVVVVVVVVVVVVVVVVVVVVV INIT_NAVIGATION FUNCTION VVVVVVVVVVVVVVVVVVVVV инициилизирует навигацию по страницам, определяет сколько страниц всего, генерирует LIMIT для SQL запроса
        function core_init_navigation($sql2col="", $colonpage = 10)
        {
                // если не передан sql запрос, прерываем работу
                if(!$sql2col) return;
                if(!intval($colonpage)) return;
				if($_GET['page']=='all' || $this->thisrss) $colonpage = 1000000;
                if(intval($_GET['page'])>0) $page = intval($_GET['page']); else $page = 1;

                // вытягиваем список необходимых записей из базы
                // определяем всё, что нужно для постраничной навигации
                $from = ($page-1)*$colonpage;
                $limit = " LIMIT ".$from.",".$colonpage;

                $res2col = $this->query($sql2col);// сделали запрос с оригинальным скулом
                $colnotes = $this->num_rows($res2col);// определили сколько записей вернулось
                $colpage = ceil($colnotes/$colonpage);// определили сколько страниц

                if($page>$colpage)
                {
                        $page = 1;
                        $limit = " LIMIT 0,".$colonpage;
                }

                // формируем новый скул запрос с учётом номера страницы и количества записей на страницу
                $sql = $sql2col.$limit;

	            define("NAVIG_CUR_PAGE", $page);
                define("NAVIG_COLONPAGE", $colonpage);
                define("NAVIG_COLALL", $colnotes);
                define("NAVIG_COLPAGE", $colpage);

				$this->navig_index = intval($this->navig_index);
				$this->navigation[$this->navig_index]['NAVIG_CUR_PAGE'] = $page;
				$this->navigation[$this->navig_index]['NAVIG_COLONPAGE'] = $colonpage;
				$this->navigation[$this->navig_index]['NAVIG_COLALL'] = $colnotes;
				$this->navigation[$this->navig_index]['NAVIG_COLPAGE'] = $colpage;
				
				$this->navig_index++;
                return $sql;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF INIT_NAVIGATION FUNCTION ^^^^^^^^^^^^^^^^^^^^^ инициилизирует навигацию по страницам, определяет сколько страниц всего, генерирует LIMIT для SQL запроса

        function core_echo_param()
        {
                if($this->config['use_param'])
                        $echo = $this->param."/";
                else
                        $echo = "";
                return $echo;
        }

        function core_echo_link2thiscom($show_ugs=1)
        {
                $echo = "http://".HTTP_HOST."/".$this->core_echo_param().$this->component_name."/".($this->url_get_string&&$show_ugs?$this->url_get_string."/":"");
                return $echo;
        }

        function sendmail($aim,$from,$subject,$text, $touid = 0, $attachfile=array())
        { //кому,от кого, тема, текст, id зера кому письмо, массив файлов для прикрепления
                if(!$aim) return 0;
                $originalsubj = $subject;

                $boundary     = "--".md5(uniqid(time()));  // любая строка, которой не будет ниже в потоке данных.
                $EOL = "\r\n"; // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём

                $subject="=?".$this->config['charset']."?B?".base64_encode($subject)."?=";

                  $html ="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
                                <HTML><HEAD>
                                <META content=\"MSHTML 6.00.2800.1106\" name=GENERATOR>
								<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$this->config['charset']."\" /> 
                                <STYLE></STYLE>
                                </HEAD>
                                <BODY bgColor=#ffffff>
                                ".$text."\n
                                </BODY></HTML>";

                $headers  = "MIME-Version: 1.0;$EOL";
                $headers .= "Content-Type: multipart/mixed; charset=".$this->config['charset']."; boundary=\"$boundary\"$EOL";
                $headers .= "From: ".$from."$EOL";
                $headers .= "X-Mailer: PHP$EOL";
                $headers .= "Content-Transfer-Encoding: 8bit$EOL";
                $headers .= "Mime-Version: 1.0";
                $headers .=  phpversion();

                $body  = "--$boundary$EOL";
                $body .= "Content-Type: text/html; charset=".$this->config['charset']."$EOL";
                $body .= "Content-Transfer-Encoding: base64$EOL";
                $body .= $EOL; // раздел между заголовками и телом html-части
                $body .= chunk_split(base64_encode($html));
                $body .=  "$EOL--$boundary$EOL";

                foreach($attachfile as $attach)
                {
                        $attachname = $attach['name'];
                        $attachpath = $attach['path'];
                        if($attachname && $attachpath && file_exists($attachpath))
                        {
                                $fp = fopen($attachpath,"rb");
                                $file = fread($fp, filesize($attachpath));
                                fclose($fp);

                                $body .= "Content-Type: application/octet-stream; name=\"$attachname\"$EOL";
                                $body .= "Content-Transfer-Encoding: base64$EOL";
                                $body .= "Content-Disposition: attachment; filename=\"$attachname\"$EOL";
                                $body .= $EOL; // раздел между заголовками и телом прикрепленного файла
                                $body .= chunk_split(base64_encode($file));
                                $body .= "$EOL--$boundary$EOL";
                        }
                }

                if (mail($aim, $subject,$body, $headers)) $result = 1;
                else $result = 0;

                // пишем результат в базу
                $sql = "INSERT INTO `#h_mailfromsite` SET
                `fromemail`='".addslashes($from)."',
                `tema`='".addslashes($originalsubj)."',
                `head`='".addslashes($headers)."',
                `text`='".addslashes($html)."',
                `date`=".time().",
                `toemail`='".addslashes($aim)."',
                `par`='".addslashes($this->param)."',
                `sended`='".intval($result)."',
                `touid`='".intval($touid)."'";
                $this->query($sql);

                return $result;
        }//function sendmail($aim,$from,$subject,$text)

        //VVVVVVVVVVVVVVVVVVVVVVV GET_TREE FUNCTION VVVVVVVVVVVVVVVVVVVVV получить дерево
        function core_get_tree($sql = "", $id_key="id", $pid_key="pid")
        {
                if(!$sql) return array(); // если скул запрос по которому выдёргивать дерево не передан, то возвращаем пустой массив
                $res = $this->query($sql);// выполняем запрос
                while($row = $this->fetch_assoc($res))
                {
                        $infa[intval($row[$pid_key])][intval($row[$id_key])] = $row; // формируем массив дерева
                }
                return $infa;// возвращаем амссив дерева
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF GET_TREE FUNCTION ^^^^^^^^^^^^^^^^^^^^^ получить дерево

        //VVVVVVVVVVVVVVVVVVVVVVV TREE FUNCTION VVVVVVVVVVVVVVVVVVVVV нарисовать дерево
        function core_tree($pid=0,$space=0,$tpl="",$infa=array(), $clear=0)
        {
                static $return;
                if($clear) $return="";
        if(is_array($infa[$pid]))
        {
                foreach($infa[$pid] as $id => $item)
                {
                        if($tpl)// шаблон вывода, содержит поля типа {text} - это значит взять из массива дерева элемент $item['text'] и подставить на это место
                        {
                                $show = str_replace("{space}",str_repeat("&nbsp;&nbsp;",$space),$tpl);// подставляем отступ
                                foreach($item as $k=>$v)
                                {
                                        $show = str_replace("{".$k."}",$v,$show);// подставляем значения в шаблон
                                }
                        }
                        $return .= $show;
                        if(sizeof($infa[$item['id']]))// если есть записи в дерее с пидом таким же как текущий ид то вызываем рекурсивно этуже функцию но со space на единицу больше
                        {
                                $this->core_tree($item['id'],$space+1,$tpl,$infa);
                        }
                }
        }
        return $return;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF TREE FUNCTION ^^^^^^^^^^^^^^^^^^^^^ нарисовать дерево

        //VVVVVVVVVVVVVVVVVVVVVVV core_get_tree_keys FUNCTION VVVVVVVVVVVVVVVVVVVVV выдёргвает из массива $infa полученного при вызове функции get_tree значения полей
        function core_get_tree_keys($pid=0,$needfields=array(),$infa=array(),$space=0,$clear=0, $id_key = "id")
        {

                static $all_ids = array();
                static $i;
                if($clear) {$all_ids = array();$i = 0;}
        if(is_array($infa[$pid]))
        {
                foreach($infa[$pid] as $id => $item)
                {
                        foreach($item as $k=>$v)
                        {
                                if(!sizeof($needfields) || in_array($k,$needfields)) $all_ids[intval($i)][$k] = $v;
                                $all_ids[intval($i)]['this_space'] = $space;
                        }
                        $i++;
                        if(sizeof($infa[$item[$id_key]]) && $item[$id_key]!=$pid)// если есть записи в дерее с пидом таким же как текущий ид то вызываем рекурсивно этуже функцию но со space на единицу больше
                        {
                                $this->core_get_tree_keys($item[$id_key],$needfields,$infa,$space+1,0,$id_key);
                        }
                }
        }
                return $all_ids;
        }
        // ^^^^^^^^^^^^^^^^^^^^^^^ EOF core_get_tree_keys FUNCTION ^^^^^^^^^^^^^^^^^^^^^ выдёргвает из массива $infa полученного при вызове функции get_tree значения полей

        function core_go_ajaxpage()
        {
                $pagename = basename($_GET['page']);
                if($this->isadmin)
                {
                        $filename = DOCUMENT_ROOT."/".ADMINDIRNAME."/ajax-pages/".$pagename.".php";
                }else
                {
                        $filename = DOCUMENT_ROOT."/templates/".$this->config['tpl']."/ajax-pages/".$pagename.".php";
                }
                if(!file_exists($filename) || is_dir($filename)) return false;

                // Load JsHttpRequest backend.
                require_once(DOCUMENT_ROOT."/_system/_JsHttpRequest.php");

                $this->thisajax = 1;
                $JsHttpRequest =& new JsHttpRequest($this->config['charset']);// Create main library object. You MUST specify page encoding!
                // Store resulting data in $_RESULT array (will appear in req.responseJs).
                $_RESULT = array();
				
	                // проверяем права доступа к этой странице
                $this->adm_get_rights("ajax-".$pagename);

                include($filename);
                return $_RESULT;
        }

        function core_ini_get($param="")
        {
                if(!$param) return false;
                $ret = "";
                $ret = ini_get($param);
                switch($param)
                {
                        case 'upload_max_filesize': case 'post_max_size':
                                $ret = str_replace("M","Mb", $ret);
                        break;
                }
                return $ret;
        }

        function core_get_mui($comid="")
        {
                if(!$this->param) return false;

                if($comid)
                {
                        $sql = "SELECT * FROM `#h_components` WHERE `id`=".$comid;
                        $res = $this->query($sql);
                        $comp_row = $this->fetch_assoc($res);
				}
        }

	function get_mui_param()
	{
		if($this->isadmin==1) return $this->config['admin_par'];
		else return $this->param;
	}

        function core_readmuifile()
        {
			static $loaded;
			if($loaded) return;
			$loaded = 1;

			$sql = "SELECT * FROM `#h_mui` WHERE `param`='".addslashes($this->get_mui_param())."' && `adm`='".addslashes($this->isadmin)."'";
			$res = $this->query($sql);
			while($row = $this->fetch_assoc($res))
			{
                    $this->mui[trim($row['mui_code'])] = trim($row['mui_text']);
			}
        }

        function core_echomui($key="")
        {
                if(!$key) return false;
                if(!isset($this->mui[$key])) {if(!in_array($key, $this->muierror)) $this->muierror[] = $key;
				
				if(!$this->num_rows($this->query("SELECT * FROM `#h_mui` WHERE `param`='".addslashes($this->get_mui_param())."' && `mui_code`='".addslashes($key)."' && `adm`='".addslashes($this->isadmin)."'")))
				{
					$sql = "INSERT INTO `#h_mui` SET `param`='".addslashes($this->get_mui_param())."',`mui_code`='".addslashes($key)."', `mui_text`='no_mui:".addslashes($key)."', `adm`='".addslashes($this->isadmin)."'";
					$this->query($sql);
				}
				
				return "no_mui:".$key;}
                else {
					if(!trim($this->mui[$key]) && !in_array($key, $this->muiblank)) $this->muiblank[] = $key;
					return $this->mui[$key];
				}
        }

        function muiparam()
        {
                if($this->isadmin) $param = $this->admin_par;
                else $param = $this->param;
                return $param;
        }
	
	function form_check_kapcha()
	{
		if(!$this->kapcha_field_name) return 1;
		if($this->formsendtype=='post')	$md5 = md5($_POST[$this->kapcha_field_name]);
		else $md5 = md5($_GET[$this->kapcha_field_name]);

		if($_SESSION['kapcha'][$this->kapcha_field_name]==$md5) return 1;
		else return 0;
	}

	function com_get_subpages_info($cpage_info=array(), $level=0)
	{
		$content = array();
		$index = 0;
		$cpage_info['id'] = intval($cpage_info['id']);
		$cpage_info['com_id'] = intval($cpage_info['com_id']);
		if(!$cpage_info['id']) {return $content;}
	
		if($this->component_config['sc_col_subpages_'.$level]) $col = $this->component_config['sc_col_subpages_'.$level];
		else $col = 1000000;

		if($this->component_config['sub_page_tbl'])
		{
			$order_cond = ($this->component_config['sub_page_of']?"`spt`.`".addslashes($this->component_config['sub_page_of'])."` ".($this->component_config['sub_page_oc']?addslashes($this->component_config['sub_page_oc']):"ASC"):"`sm`.`sort` ASC");
			$sql = "SELECT `sm`.* FROM `#__sitemap` `sm`, `#".$this->component_config['sub_page_tbl']."` `spt` WHERE 
			`sm`.`pid`=".$cpage_info['id']."
			&& `sm`.`public`='1'
			&& `sm`.`record_id`=`spt`.`id`
			ORDER BY ".$order_cond;
		}else $sql = "SELECT * FROM `#__sitemap` WHERE `pid`=".$cpage_info['id']." && `public`='1' ORDER BY `sort` ASC";

        //if($_GET['dev']){echo $sql;}
        

		$sql = $this->core_init_navigation($sql, $col);

		$res = $this->query($sql);
		while($row = $this->fetch_assoc($res))
		{
			$row['this_space'] = $level;
			$content[$index] = $row;
			$sql = "SELECT `id`,`config` FROM `#h_components` WHERE `id`='".addslashes($row['com_id'])."'";
			$com_res = $this->query($sql);
			$com_row = $this->fetch_assoc($com_res);
		
			$comconfig = $this->adm_get_param($com_row['config']);
			$this->get_typesarray($com_row['id']);
			/*получаем контент*/
			$content[$index] = $row;
			$content[$index]['info'] = $this->com_get_page_content($row, $comconfig,'sc_colonsubpage','info');
			$content[$index]['content'] = $this->com_get_page_content($row, $comconfig,'sc_colonsubpage','content');
			$content[$index]['sub_pages'] = $this->com_get_subpages_info($row, $level+1);
			$index++;
		}
		return $content;
	}	

	function com_get_page_content(&$pageinfo=array(), &$com_config=array(), $colonpage_par_name='sc_colonpage', $type)	{
		if(!$colonpage_par_name) $colonpage_par_name = 'sc_colonpage';
		if(!sizeof($pageinfo) || !sizeof($com_config)) return array();
		$content = array();

		if($type=='info' && intval($pageinfo['record_id']))
		{
		   $sql = "SELECT * FROM `#".$com_config['tbl']."` WHERE `id`=".intval($pageinfo['record_id']);
		   $page_row = $this->fetch_assoc($this->query($sql));
		   $content = $this->core_getcorrect_content($page_row);
		}

		if($type=='content')
		{
			$sql = "SELECT `id`,`config` FROM `#h_components` WHERE `add_button`='content' && `id`=".$this->get_com_contplbyid($pageinfo['com_id']);
			$component_info = $this->fetch_assoc($this->query($sql));
			$this->get_typesarray($component_info['id']);
			$component_info = $this->adm_get_param($component_info['config']);

			if($component_info['tbl'])
			{
				$ordercond = ($com_config['content_order_cond']?$com_config['content_order_cond']:'`sort` ASC');
				
				$sql = "SELECT * FROM `#".addslashes($component_info['tbl'])."` WHERE `public`='1' && `pid`=".$pageinfo['id']." ORDER BY ".$ordercond;
				if(($com_config[$colonpage_par_name] && isset($com_config[$colonpage_par_name])) || !isset($com_config[$colonpage_par_name]))
					$sql = $this->core_init_navigation($sql, ($com_config[$colonpage_par_name]?intval($com_config[$colonpage_par_name]):COLONPAGE_DEFAULT));
			
				$page_con_res = $this->query($sql);
				while($page_con_row = $this->fetch_assoc($page_con_res))
				{
					$content[] = $this->core_getcorrect_content($page_con_row);
				}
			}
		}
		return $content;
	}

	function get_com_contplbyid($com_id=0)
	{
		$com_id = intval($com_id);
		if(!$com_id) return 0;

		$sql = "SELECT `content_tpl_by` FROM `#h_components` WHERE `id`=".$com_id;
		$row = $this->fetch_assoc($this->query($sql));
		return $row['content_tpl_by'];
	}

	function core_show_capcha($fieldname = 'code', $proceedform = '', $extrahtml = '')
	{
		$echo = '<img src="/templates/'.$this->config['tpl'].'/capcha.php?f='.$fieldname.'&amp;nocache='.rand(0,10000).'" '.$extrahtml.' alt=""/>';
		return $echo;
	}

	function go_cron_item($filename='', $param = array())
	{
		if(!is_file($filename.".php")) return false;

		$_PARAM = $param;
		if(!is_array($_PARAM)) $_PARAM = array();

		include($filename.".php");
	}

	function go_rss_show()
	{
		$path = addslashes($_GET['path']);
		if(!$path)
		{
			$this->header[] = "header('Location: /error404/');";
			return false;
		}
		$this->thisrss = 1;
		$this->url_get = split("/",$path);
		$this->core_go_component();
		echo COMPONENT_TEXT;
	}

	function valid_email($email='')
	{
		if(eregi("([a-z0-9_-]{1,20})@(([a-z0-9-]+\.)+)([a-z]{2,5})", $email) && $email) return true;
		else return false;
	}

	function check_user_avt(){return ($_SESSION['user']['group']['id']>2);}

	function get_db_array($sql = '', $f='')
	{
		if(!$sql) return array();
		$res = $this->query($sql);
		$row = array();
		while($r = $this->fetch_assoc($res)) if($f)  $row[] = $r[$f]; else $row[] = $r;
		return $row;
	}

}
?>
