<?php
/* RedixCMS 4.0
здесь в классе можно определять любые функции, которых ещё нет в основном классе или в классе работы с БД, эти функции попадут в основной класс

функции prestart() и adm_prestart() - не удалять, иначе не будет работать сайт.
*/

class user_main 
{
	function prestart() // функция запускается сразу после создания класса и проверки авторизации пользователя
	{
	}
	
	function adm_prestart() // функция запускается сразу после создания класса и проверки авторизации пользователя в админке
	{
	}

	function ShowGeoIp(){
		$url = 'http://194.85.91.253:8090/geo/geo.html'; // url на XML сервер <<IpGeobase>>
		if (!$_SESSION['location']['city']) // проверяем наличие сессии
		{ // если сессии нет, то начинаем выполнять поиск
			$ip = $this->get_user_ip();
			$cl = curl_init(); // Устанавливаем cURL
			$query = '<ipquery><fields><all/></fields><ip-list><ip>'.$ip.'</ip></ip-list></ipquery>'; //  формируем запрос
			curl_setopt($cl, CURLOPT_URL, $url); //  выполняем запрос на сервер по адресу $url
			curl_setopt($cl, CURLOPT_RETURNTRANSFER,1); // указываем что ответ сервера нужно записать в переменную
			curl_setopt($cl, CURLOPT_TIMEOUT, 2); // таймаут соединения - 2 секунды
			curl_setopt($cl, CURLOPT_POST, 1); // указываем метод выполнения скрипта - POST
			curl_setopt($cl, CURLOPT_POSTFIELDS, $query); //  выпосляем запрос
			$result = curl_exec($cl); // записываем в переменную
			curl_close($cl); //  закрываем соединение
			preg_match_all("|<region>(.*?)</region>|", $result, $region); //  узнаём регион
			preg_match_all("|<city>(.*?)</city>|", $result, $city); //  узнаём город
			preg_match_all("|<district>(.*?)</district>|", $result, $district); //  узнаём округ
			preg_match_all("|<lat>(.*?)</lat>|", $result, $lat); //  узнаём широта
			preg_match_all("|<lng>(.*?)</lng>|", $result, $lng); //  узнаём долгота
			$_SESSION['location'] = array(); //  определяем сессию 'location' массивом
			$_SESSION['location']['region'] = $region[1][0]; //  записываем в массив сессии регион
			$_SESSION['location']['city'] = $city[1][0];  //  записываем в массив сессии город
			$_SESSION['location']['district'] = $district[1][0]; //  записываем в массив сессии округ
			$_SESSION['location']['lat'] = $lat[1][0]; //  записываем в массив сессии широту
			$_SESSION['location']['lng'] = $lng[1][0]; //  записываем в массив сессии долготу
		}
		return $_SESSION['location'];  //  возвращаем результат
	}

	function upk($price=0) // user price koefficient
	{
		$koef = ($_SESSION['user']['koef']?$_SESSION['user']['koef']:1);
		return $price*$koef;
	}


	function icq_online($uin=0, $cachttl=300)
	{
		$uin = str_replace("-","",$uin);
		$uin = str_replace(" ","",$uin);
		$uin = str_replace(",","",$uin);
		$uin = str_replace(".","",$uin);
		if(!$uin) return 0;

		if(!file_exists(DOCUMENT_ROOT."/_cache/icq_stat") || !is_dir(DOCUMENT_ROOT."/_cache/icq_stat")) mkdir(DOCUMENT_ROOT."/_cache/icq_stat", 0777);
		
		$cach_file = DOCUMENT_ROOT."/_cache/icq_stat/".$uin.".txt";
		$cach_file_time = filemtime($cach_file);
		if((time()-$cach_file_time)<=$cachttl)
		{
			$stat = file($cach_file);
			return trim($stat[0]);
		}
		
		$file = @file_get_contents('http://online.mirabilis.com/scripts/online.dll?icq='.$uin.'&img=5');
		$md5 = md5($file);
		
		if($md5=='501aa29a5565a264b1257b66bcbf82ea') $stat = 1;
		else $stat = 0;
		
		if(file_exists($cach_file)) unlink($cach_file);

		$f = fopen($cach_file,"w");
		fwrite($f,$stat);
		fclose($f);

		return $stat;
	}
	function skype_online($uin='',$cachttl=300)
	{
		if(!$uin) return 0;

		if(!file_exists(DOCUMENT_ROOT."/_cache/skype_stat") || !is_dir(DOCUMENT_ROOT."/_cache/skype_stat")) mkdir(DOCUMENT_ROOT."/_cache/skype_stat", 0777);
		$cach_file = DOCUMENT_ROOT."/_cache/skype_stat/".$uin.".txt";
		$cach_file_time = filemtime($cach_file);
		if((time()-$cach_file_time)<=$cachttl)
		{
			$stat = file($cach_file);
			return trim($stat[0]);
		}

		$statusuri = "http://mystatus.skype.com/%s.xml";
		$str_status_xml =  @file_get_contents(sprintf($statusuri,$uin));
		
		$lang = 'en';
		$match = array();
		$pattern = "~xml:lang=\"".strtolower($lang)."\">(.*)</~";
		preg_match($pattern,$str_status_xml, $match);
		$stat = $match[1];

		if(file_exists($cach_file)) unlink($cach_file);

		$f = fopen($cach_file,"w");
		fwrite($f,$stat);
		fclose($f);

		return $stat;
	}

	function listofakcii($id=0, $type='')
	{
		$return = array();
		$id = intval($id);
		if(!$id) return $return;
		if(!in_array($type, array("cars","services"))) return $return;

//		$fields = array("cars"=>"cars","services"=>"services");

		$sql = "SELECT * FROM `#__akcii` WHERE `".$type."` LIKE '%".addslashes($id)."%'";
		$res = $this->query($sql);
		while($row = $this->fetch_assoc($res))
		{
			$ids = split(";",$row[$type]);
			if(in_array($id, $ids))
			{
				$sql = "SELECT `url` FROM `#__sitemap` WHERE `com_id`=36 && `record_id`=".$row['id']." && `public`='1'";
				$url = $this->fetch_assoc($this->query($sql));
				if($url){$row['url'] = $url['url'];$return[] = $row;}
			}
		}

		return $return;
	}
}
?>