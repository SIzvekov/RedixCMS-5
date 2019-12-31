<?php
/* RedixCMS 4.0
����� � ������ ����� ���������� ����� �������, ������� ��� ��� � �������� ������ ��� � ������ ������ � ��, ��� ������� ������� � �������� �����

������� prestart() � adm_prestart() - �� �������, ����� �� ����� �������� ����.
*/

class user_main 
{
	function prestart() // ������� ����������� ����� ����� �������� ������ � �������� ����������� ������������
	{
	}
	
	function adm_prestart() // ������� ����������� ����� ����� �������� ������ � �������� ����������� ������������ � �������
	{
	}

	function ShowGeoIp(){
		$url = 'http://194.85.91.253:8090/geo/geo.html'; // url �� XML ������ <<IpGeobase>>
		if (!$_SESSION['location']['city']) // ��������� ������� ������
		{ // ���� ������ ���, �� �������� ��������� �����
			$ip = $this->get_user_ip();
			$cl = curl_init(); // ������������� cURL
			$query = '<ipquery><fields><all/></fields><ip-list><ip>'.$ip.'</ip></ip-list></ipquery>'; //  ��������� ������
			curl_setopt($cl, CURLOPT_URL, $url); //  ��������� ������ �� ������ �� ������ $url
			curl_setopt($cl, CURLOPT_RETURNTRANSFER,1); // ��������� ��� ����� ������� ����� �������� � ����������
			curl_setopt($cl, CURLOPT_TIMEOUT, 2); // ������� ���������� - 2 �������
			curl_setopt($cl, CURLOPT_POST, 1); // ��������� ����� ���������� ������� - POST
			curl_setopt($cl, CURLOPT_POSTFIELDS, $query); //  ��������� ������
			$result = curl_exec($cl); // ���������� � ����������
			curl_close($cl); //  ��������� ����������
			preg_match_all("|<region>(.*?)</region>|", $result, $region); //  ����� ������
			preg_match_all("|<city>(.*?)</city>|", $result, $city); //  ����� �����
			preg_match_all("|<district>(.*?)</district>|", $result, $district); //  ����� �����
			preg_match_all("|<lat>(.*?)</lat>|", $result, $lat); //  ����� ������
			preg_match_all("|<lng>(.*?)</lng>|", $result, $lng); //  ����� �������
			$_SESSION['location'] = array(); //  ���������� ������ 'location' ��������
			$_SESSION['location']['region'] = $region[1][0]; //  ���������� � ������ ������ ������
			$_SESSION['location']['city'] = $city[1][0];  //  ���������� � ������ ������ �����
			$_SESSION['location']['district'] = $district[1][0]; //  ���������� � ������ ������ �����
			$_SESSION['location']['lat'] = $lat[1][0]; //  ���������� � ������ ������ ������
			$_SESSION['location']['lng'] = $lng[1][0]; //  ���������� � ������ ������ �������
		}
		return $_SESSION['location'];  //  ���������� ���������
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