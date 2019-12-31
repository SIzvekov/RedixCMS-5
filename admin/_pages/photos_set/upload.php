<?
//copy($temp_name, $filepath.'/'.$filename);
	@extract($_GET);
	
	$filename	= $_FILES['Filedata']['name'];
	$temp_name	= $_FILES['Filedata']['tmp_name'];
	$error		= $_FILES['Filedata']['error'];
	$size		= $_FILES['Filedata']['size'];


	$tid = intval($tid);
//	if(!$tid) exit;
	

	$dir = "../../..";
	$dir1 = "../..";
	// подключаем файл конфига
	require_once($dir."/_config.php");

	// подключаем файл глобальных функций
	require_once($dir."/_system/_global_functions.php");

	// подключаем файл пользовательских функций. Эти функции попадают в основной класс
	require_once($dir."/_system/_core_user.php");

	// подключаем файл работы с БД
	require_once($dir."/_system/_db_".DB_TYPE.".php");

	// подключаем файл главного класса
	require_once($dir."/_system/_core_".CMS_VERSION.".php");

	// подключаем файл главного класса
	require_once($dir1."/_system/_adm_core_".ADM_VERSION.".php");

	$core = new adm_core(ADMINDIRNAME, 1); // определяем основной класс ядра
	$_logged = $core->login(1); // проверяет авторизацию пользователя


	/* NOTE: Some server setups might need you to use an absolute path to your "dropbox" folder
	(as opposed to the relative one I've used below).  Check your server configuration to get
	the absolute path to your web directory*/

	if(!$error){

	if($tofolder) $filepath = $tofolder;
	else $filepath = $_SERVER['DOCUMENT_ROOT'];

	$fmgr_filenames = fmgr_readusernames($filepath."/.usernames_files",'file');
	

	$filearr = split("\.",$filename);
	$ext = end($filearr);
	array_pop($filearr);
	$init_name = join(".",$filearr);
	if($save2name=='_rand_') $filename = substr(md5(time()),rand(0,22),10); else $filename = join(".",$filearr);

	$destfile = adm_translit($filename.".".$ext);
	$copy_num = 0;
	while(file_exists($filepath."/".$destfile) && is_file($filepath."/".$destfile)) 
	{
		$copy_num++;
		$destfile = adm_translit($filename." (".$copy_num.").".$ext);
	}

	$newbasename = $init_name.".".$ext;
	$copy_num = 0;

	while(in_array($newbasename, $fmgr_filenames))
	{
		$copy_num++;
		$newbasename = $init_name." (".$copy_num.").".$ext;
	}

	copy($temp_name, $filepath."/".$destfile);
	$f = fopen($filepath."/.usernames_files",'a+');
		fwrite($f,$destfile."=".$newbasename."\n");
	fclose($f);


	$sql = "INSERT INTO `#".addslashes($tbl)."` SET `pid`=".$tid.", `img`='".addslashes($destfile)."',`public`='1'";
	$core->query($sql);

























	}



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


function fmgr_readusernames($what='',$type='file')
{
	if(!$what || !file_exists($what) || !is_file($what)) return array();

	if($type=='file')
	{
		$fmgr_filenames = array();
		$fmgr_filenames = adm_get_param(file_get_contents($what));
		return $fmgr_filenames;
	}
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
                    $params[trim($par[0])]=trim($par[1]);
                }
                return $params;
        }

?>