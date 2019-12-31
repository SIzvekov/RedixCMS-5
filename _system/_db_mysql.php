<?php
/* RedixCMS 4.0
Файл работы с БД
*/

// если нужно кэшировать запросы, то определяем один класс, если не нужно, то другой
if(DB_CACHE){
class database extends user_main {
        //Путь к директории кэш-файлов
        var $CachePath = "/_cache/sql";     //Необходимо ввести полный путь

        //Имя файла с информацией о пиковой нагрузке
        var $PeakFilename='!peak.txt';

        //Флаг, при установке которого ошибки запросов выводятся на экран
        var $Debug=false;

        //Флаг, указывающий, что данные выдаются из кэша
        var $FromCache=array();

        //Дата формирования данных
        var $DataDate=array();

        //Численный код ошибки выполнения последней операции с MySQL
        var $errno=0;

        //Строка ошибки последней операции с MySQL
        var $error='';

        //Информация о пиковой нагрузке
        var $Peak=array(
                        0,    //Время выполнения
                        '',   //Дата выполнения
                        '',   //Запрос
                        '',   //Вызвавший скрипт
                );

        //Номер следующей выдаваемой строки
        var $NextRowNo=array();

        //Массив результатов запроса
        var $ResultData=array();

        var $db_errors = array(); // массив ошибок выполнения запроса

        var $db_sql = array(); // массив выполненных запросов

        function db_connect()
        {
                if (DB_HOST=="") { $this->db_errors[] = "Не указан хост БД\n<br>"; return false; }
                if (DB_LOGIN=="") { $this->db_errors[] = "Не указан логин БД\n<br>"; return false; }
                if (DB_TABLE=="") { $this->db_errors[] = "Не указано имя БД\n<br>"; return false; }
                if (mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD))
                {
                        if (mysql_select_db(DB_TABLE)) {
                                $this->query("SET NAMES 'utf8'");
                                return true;
                        } else { $this->db_errors[] = "Ошибка выбора БД\n<br>"; return false; }
                } else { $this->db_errors[] = "Ошибка подключения к БД\n<br>"; return false; }
    }

        function query($query, $valid=DB_CACHE_TTL){
                static $index=0;
                $nothisquerycache = 0;
                   $query = $this->prefixed($query); // подставляем префиксы
                   $this->db_sql[] = $query; // записали в массив запросов этот запрос

                if ($this->CachePath==''){$this->CachePath=dirname(__FILE__);} // если не определена папка кэшированных запросов, то её определяем

                if (!eregi('^SELECT', $query)){return mysql_query($query);} // если запрос не SELECT, то выполняем просто mysql_query

                $index = md5($query);

                if($this->FromCache[$index])
                  {
                        $this->NextRowNo[$index] = 0;
                          return $index;
                }

                $this->ResultData[$index] = array();
                $this->NextRowNo[$index] = 0;
                $this->DataDate[$index] = 0;
                $filename=DOCUMENT_ROOT.'/'.$this->CachePath.'/'.md5($query).'.txt';

                /* Попытка чтения кэш-файла */
                if ((@$file=fopen($filename, 'r')) && filemtime($filename)>(time()-$valid) && !$nothisquerycache)
                {
                        flock($file, LOCK_SH);
                        $serial=file_get_contents($filename);
                        $this->ResultData[$index]=unserialize($serial);
                        $this->DataDate[$index]=filemtime($_SERVER['DOCUMENT_ROOT'].$filename);
                        $this->FromCache[$index]=true;
                        fclose($file);
                        return $index;
                }
                if($file){fclose($file);}

                /* Если чтение из Кэша не получилось, то Выполнение запроса */
                $time_start=microtime(true);
                @$SQLResult=mysql_query($query);
                $time_end=microtime(true);
                $this->DataDate[$index]=time();
                $time_exec=$time_end-$time_start;

                /* Обработка ошибки запроса */
                if (!$SQLResult){$this->db_errors[] = 'Error from query "'.$query.'": '.mysql_error();} // если произошла ошибка, пишем её в массив

                /* Проверка пиковой нагрузки */
                $peak_filename=DOCUMENT_ROOT.'/'.$this->CachePath.'/'.$this->PeakFilename;
                if (@$file=fopen($peak_filename, 'r'))
                {
                        flock($file, LOCK_SH);
                        $fdata=file($peak_filename);
                        foreach ($fdata as $key=>$value){
                                $this->Peak[$key]=trim($value);
                        }
                }
                $this->Peak[0]=floatval($this->Peak[0]);

                if ($file){fclose($file);}

                if ($time_exec>$this->Peak[0])
                {
                        $this->Peak=array($time_exec,date('r'),$query,$_SERVER['SCRIPT_FILENAME']);
                        $file=fopen($peak_filename, 'w');
                        flock($file, LOCK_EX);
                        fwrite($file, implode("\n", $this->Peak));
                        fclose($file);
                }

                /* Получение названия полей */
                $nf=mysql_num_fields($SQLResult);
                for ($i=0; $i<$nf; $i++){$this->ResultData[$index]['fields'][$i]=mysql_fetch_field($SQLResult, $i);}

                /* Получение данных */
                $nr=mysql_num_rows($SQLResult);
                for ($i=0; $i<$nr; $i++){$this->ResultData[$index]['data'][$i]=mysql_fetch_row($SQLResult);}

                /* Запись кэша */
                $file=fopen($filename, 'w');
                flock($file, LOCK_EX);
                fwrite($file, serialize($this->ResultData[$index]));
                fclose($file);

                return $index;
        }// function query

        function mysql_get_tables()
        {
                $sql = "SHOW TABLES FROM `".addslashes(DB_TABLE)."`";
                $res = $this->query($sql);
                $return = array();
                while($row = $this->fetch_assoc($res)) {$return[] = $this->unprefixed(end($row));}
                return $return;
        }

        function mysql_get_fields($table="")
        {
                if(!$table) return false;

                $sql = "SHOW FIELDS FROM `".addslashes($this->prefixed($table))."`";
                $res = $this->query($sql);
                $return = array();
                while($row = $this->fetch_assoc($res)) {$return[] = $row;}
                return $return;
        }

        function prefixed($query="")
        {
                // имеем 3 кода для 3х типов префиксов:
                // код #s_ означает только системный префикс
                // код #h_ означает системный префикс + префикс хоста
                // код #__ означает системный префикс + префикс хоста + префикс параметра
                if(!$query) return "";
                $query=trim($query);
                if(defined("DB_PREFIX"))
                {
                        $query = str_replace("#s_", DB_PREFIX, $query);
                        if(defined("DB_HOST_PREFIX"))
                        {
                                $query = str_replace("#h_", DB_PREFIX.DB_HOST_PREFIX, $query);
                                if(defined("DB_PARAM_PREFIX"))
                                {
                                        $query = str_replace("#__", DB_PREFIX.DB_HOST_PREFIX.DB_PARAM_PREFIX, $query);
                                }
                        }
                }
                return $query;
        }// function prefixed

        function unprefixed($query="")
        {
                if(!$query) return "";
                $query=trim($query);

                if(defined("DB_PARAM_PREFIX"))
                {
                        $query = str_replace(DB_PREFIX.DB_HOST_PREFIX.DB_PARAM_PREFIX, "#__", $query);
                        if(defined("DB_HOST_PREFIX"))
                        {
                                $query = str_replace(DB_PREFIX.DB_HOST_PREFIX, "#h_", $query);
                                if(defined("DB_PREFIX"))
                                {
                                        $query = str_replace(DB_PREFIX, "#s_", $query);
                                }
                        }
                }
                return $query;
        }

        /*** Количество полей в запросе ***/
        function num_fields($index)
        {
                if(!isset($index)) return false;
                return sizeof($this->ResultData[$index]['fields']);
        }

        /*** Название указанной колонки результата запроса ***/
        function field_name($index, $num)
        {
                if(!isset($index)) return false;
            if (isset($this->ResultData[$index]['fields'][$num])){
                    return $this->ResultData[$index]['fields'][$num]->name;
                }else{
                        return false;
                }
        }

        /*** Информация о колонке из результата запроса в виде объекта ***/
        function fetch_field($index, $num)
        {
                if(!isset($index)) return false;
                if (isset($this->ResultData[$index]['fields'][$num])){
                        return $this->ResultData[$index]['fields'][$num];
                }else{
                        return false;
                }
        }

        /*** Длина указанного поля ***/
        function field_len($index, $num)
        {
                if(!isset($index)) return false;
                if (isset($this->ResultData[$index]['fields'][$num])){
                        return $this->ResultData[$index]['fields'][$num]->max_length;
                }else{
                        return false;
                }
        }

        /*** Тип указанного поля результата запроса ***/
        function field_type($index, $num)
        {
                if(!isset($index)) return false;
                if (isset($this->ResultData[$index]['fields'][$num])){
                        return $this->ResultData[$index]['fields'][$num]->type;
                }else{
                        return false;
                }
        }

        /*** Флаги указанного поля результата запроса ***/
        function field_flags($index, $num)
        {
                if(!isset($index)) return false;
                if (!isset($this->ResultData[$index]['fields'][$num])){
                        return false;
                }
                $result=array();
                if ($this->ResultData[$index]['fields'][$num]->not_null){
                        $result[]='not_null';
                }
                if ($this->ResultData[$index]['fields'][$num]->primary_key){
                        $result[]='primary_key';
                }
                if ($this->ResultData[$index]['fields'][$num]->unique_key){
                        $result[]='unique_key';
                }
                if ($this->ResultData[$index]['fields'][$num]->multiple_key){
                        $result[]='multiple_key';
                }
                if ($this->ResultData[$index]['fields'][$num]->blob){
                        $result[]='blob';
                }
                if ($this->ResultData[$index]['fields'][$num]->unsigned){
                        $result[]='unsigned';
                }
                if ($this->ResultData[$index]['fields'][$num]->zerofill){
                        $result[]='zerofill';
                }
                if ($this->ResultData[$index]['fields'][$num]->binary){
                        $result[]='binary';
                }
                if ($this->ResultData[$index]['fields'][$num]->enum){
                        $result[]='enum';
                }
                if ($this->ResultData[$index]['fields'][$num]->auto_increment){
                        $result[]='auto_increment';
                }
                if ($this->ResultData[$index]['fields'][$num]->timestamp){
                        $result[]='timestamp';
                }
                return implode(' ', $result);
        }

        /* Количество рядов результата запроса */
        function num_rows($index)
        {
                if(!isset($index)) return false;
                return sizeof($this->ResultData[$index]['data']);
        }

        /* Обрабатывает ряд результата запроса и возвращает неассоциативный массив */
        function fetch_row($index)
        {
                if (($this->NextRowNo[$index]+1)>$this->num_rows($index)){
                        return false;
                }
                $this->NextRowNo[$index]++;
                return $this->ResultData[$index]['data'][$this->NextRowNo[$index]-1];
        }

   /* Обрабатывает ряд результата запроса и возвращает ассоциативный массив */
   function fetch_assoc($index)
   {
                if(!isset($index)) return false;
                if (($this->NextRowNo[$index]+1)>$this->num_rows($index)){
                        return false;
                }
                for ($i=0; $i<$this->num_fields($index); $i++)
                {
                        $result[$this->ResultData[$index]['fields'][$i]->name]=
                        $this->ResultData[$index]['data'][$this->NextRowNo[$index]][$i];
                }
                $this->NextRowNo[$index]++;
                return $result;
        }

        function insert_id()
        {
                return mysql_insert_id();
        }

        function db_close()
        {
                @mysql_close();
        }
}// end of class

}//if (DB_CACHE)
else{

class database extends user_main {
        var $db_errors = array(); // массив ошибок выполнения запроса

        var $db_sql = array(); // массив выполненных запросов

        function db_connect()
        {
                if (DB_HOST=="") { $this->db_errors[] = "Не указан хост БД\n<br>"; return false; }
                if (DB_LOGIN=="") { $this->db_errors[] = "Не указан логин БД\n<br>"; return false; }
                if (DB_TABLE=="") { $this->db_errors[] = "Не указано имя БД\n<br>"; return false; }
                if (mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD))
                {
                        if (mysql_select_db(DB_TABLE)) {
                                $this->query("SET NAMES 'utf8'");
                                return true;
                        } else { $this->db_errors[] = "Ошибка выбора БД\n<br>"; return false; }
                } else { $this->db_errors[] = "Ошибка подключения к БД\n<br>"; return false; }
    }

    function query($query='', $multiparam='')
    {
		$wasquery = array();
		if($multiparam)
		{
			$source_query = $query;
			foreach($this->params_list as $param)
			{
				$query = $this->prefixed($source_query, $param['db_prefix']);
				if(in_array($query,$wasquery)) continue;

	            $result = @mysql_query($query);
		        $this->db_sql[] = $query;
				$wasquery[] = $query;
			}
		}else
		{
			$query = $this->prefixed($query);
            $result = @mysql_query($query);
            $this->db_sql[] = $query;
        }
				if ($result) {
            return $result;
        } else { return false; }
    }

        function mysql_get_tables()
        {
                $sql = "SHOW TABLES FROM `".addslashes(DB_TABLE)."`";
                $res = $this->query($sql);
                $return = array();
                while($row = $this->fetch_assoc($res)) {$return[] = $this->unprefixed(end($row));}
                return $return;
        }

        function mysql_get_fields($table="")
        {
                if(!$table) return false;

                $sql = "SHOW FIELDS FROM `".addslashes($this->prefixed($table))."`";
                $res = $this->query($sql);
                $return = array();
                while($row = $this->fetch_assoc($res)) {$return[] = $row;}
                return $return;
        }


        function prefixed($query="",$dbparamprefix='')
        {
                // имеем 3 кода для 3х типов префиксов:
                // код #s_ означает только системный префикс
                // код #h_ означает системный префикс + префикс хоста
                // код #__ означает системный префикс + префикс хоста + префикс параметра
                if(!$query) return "";
                $query=trim($query);
				if(!$dbparamprefix) $dbparamprefix = DB_PARAM_PREFIX;

                if(defined("DB_PREFIX"))
                {
                        $query = str_replace("#s_", DB_PREFIX, $query);
                        if(defined("DB_HOST_PREFIX"))
                        {
                                $query = str_replace("#h_", DB_PREFIX.DB_HOST_PREFIX, $query);
                                if($dbparamprefix)
                                {
                                        $query = str_replace("#__", DB_PREFIX.DB_HOST_PREFIX.$dbparamprefix, $query);
                                }
                        }
                }
                return $query;
        }// function prefixed

        function unprefixed($query="")
        {
                if(!$query) return "";
                $query=trim($query);

                if(defined("DB_PARAM_PREFIX"))
                {
                        $query = str_replace(DB_PREFIX.DB_HOST_PREFIX.DB_PARAM_PREFIX, "#__", $query);
                        if(defined("DB_HOST_PREFIX"))
                        {
                                $query = str_replace(DB_PREFIX.DB_HOST_PREFIX, "#h_", $query);
                                if(defined("DB_PREFIX"))
                                {
                                        $query = str_replace(DB_PREFIX, "#s_", $query);
                                }
                        }
                }
                return $query;
        }

        function fetch_assoc($res)
        {
                if ($res) { return mysql_fetch_assoc($res); } else { return null; }
        }

        function num_rows($res)
        {
                if ($res) { return mysql_num_rows($res); } else { return null; }
        }

        function insert_id()
        {
                return mysql_insert_id();
        }

        function db_close()
        {
                @mysql_close();
        }

}

}// else if (DB_CACHE)
?>