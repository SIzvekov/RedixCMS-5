<?
require_once("_config.php");
error_reporting(0);

list($file,$params) = split("\?",REQUEST_URI,2);

if($file == "/showimg.php"){
	list($file,$params) = split("&",$params,2);
}

if(isset($_GET['path'])) {
	$file = $_GET['path'];
	$img_path = retrieve_img_path(DOCUMENT_ROOT.$_GET['path']);
} else {
	$img_path = retrieve_img_path(DOCUMENT_ROOT.$file);
}
define(IMG_PATH, $img_path);

if(!USE_IMG_RESIZER)
{
	$ext = strtolower(end(split("\.",$file)));
	header("Content-type: image/".$ext);
	@header("Expires: 7 days");
	echo watermark(file_get_contents($img_path), $ext);
	exit;
}

$w = intval($_GET['w'])<=1500?intval($_GET['w']):IMG_MAX_W; // width
$h = intval($_GET['h'])<=1500?intval($_GET['h']):IMG_MAX_H; // height
$q = intval($_GET['q'])&&intval($_GET['q'])<=100?intval($_GET['q']):JPG_DEF_QUALITY; // quality

if(!isset($_GET['s'])) // s = strict
{
	$imginfo = getimagesize($img_path);
	$ind = -1;
	if($w) {$ind = 0;$size = $w;}
	else if($h) {$ind = 1;$size = $h;}
	
	if($ind>=0 && $imginfo[$ind]<=$size)
	{
		$ext = strtolower(end(split("\.",$file)));
		header("Content-type: image/".$ext);
		@header("Expires: 7 days");

		$img_cont = file_get_contents($img_path);
		if(defined("NO_WATERMARK")){
			echo $img_cont;
		}else{
			echo watermark($img_cont, $ext);
		}
		exit;	
	}
}

$cachedir = DOCUMENT_ROOT."/_cache/images/".md5($file);
//echo $cachedir." - ".$file;exit;
$ext = strtolower(end(split("\.",$file)));

if($ext=='png') $q = PNG_DEF_QUALITY;

$cachefile = $cachedir."/".md5("w=".$w."&h=".$h."&q=".$q).".".$ext;

if(file_exists($cachefile))
{
 $img_cont = file_get_contents($cachefile);
}else
{
 $filepath = $img_path;
 if(!$w && !$h && ($q==JPG_DEF_QUALITY || ($ext=='png' && $q == PNG_DEF_QUALITY))) {$img_cont = file_get_contents($filepath);$goresize = 0;}
 else $img_cont = img_resize($filepath, $w, $h, $q);

 if(!$_GET['nocache'] && $goresize)
 {
	if(!is_dir($cachedir))
	 {
		mkdir($cachedir,0777);
		$f = fopen($cachedir."/.file",w);
 		fwrite($f,$file);
 		fclose($f);
	 }
	if(touch($cachedir)||isset($_SERVER['WINDIR']))
	{
		$f = fopen($cachefile,"w");
 		fwrite($f,$img_cont);
 		fclose($f);
	}
 }
}
header("Content-type: image/".$ext);
	@header("Expires: 7 days");
if(defined("NO_WATERMARK")){
	echo $img_cont;
}else{
	echo watermark($img_cont, $ext);
}


// ����������� ��������
/***********************************************************************************
������� img_resize(): ��������� thumbnails
���������:
$src             - ��� ��������� �����
$width, $height  - ������ � ������ ������������� �����������, � ��������
�������������� ���������:
$rgb             - ���� ����, �� ��������� - �����
$quality         - �������� ������������� JPEG, �� ��������� - ������������ (100)
***********************************************************************************/
function img_resize($src, $width, $height, $quality=100, $rgb=0xFFFFFF)
{
	global $goresize;
	if (!file_exists($src)) return false;
	$size = getimagesize($src);
	if ($size === false) return false;

    // ���������� �������� ������ �� MIME-����������, ���������������
    // �������� getimagesize, � �������� ��������������� �������
    // imagecreatefrom-�������.

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc) || (!$width && !$height && $quality==100))
	{
		$img_cont = file_get_contents($src);
		$goresize = 0;
		return $img_cont;
	}else $goresize=1;

    if($width && !$height)
    {
		$ratio       = $width / $size[0];
        if(!$height) {$height = floor($size[1] * $ratio);}
        if($ratio>0) $use_x_ratio = true; else $use_x_ratio = false;

        $new_width   = $use_x_ratio  ? $width  : $size[0];
        $new_height  = $use_x_ratio ? $height : $size[1];
        $new_left    = 0;
        $new_top     = 0;
	}else if(!$width && $height)
    {
		$ratio       = $height / $size[1];
        if(!$width) {$width = floor($size[0] * $ratio);}
        if($ratio>0) $use_x_ratio = true; else $use_x_ratio = false;

		$new_width   = $use_x_ratio  ? $width  : $size[0];
        $new_height  = $use_x_ratio ? $height : $size[1];
        $new_left    = 0;
        $new_top     = 0;
	}else if($height && $width)
    {
		$new_width   = $width;
        $new_height  = $height;
        $new_left    = 0;
        $new_top     = 0;
	}else if($height && $width && ($size[1]>$height || $size[0]>$width))
    {
		// ������� ������������, �� ������� ���� ��������� ������ � ������
        $minih = $size[1] / $height;
        $miniw = $size[0] / $width;
	
		if($minih>$miniw) // ���� �� ������ ���� ��������� � ������� ����� ���, ��� �� ������, �� �� ������ ���� ������ �� ������
        {
			$new_height = $height;
            $new_width = intval($size[0]/$minih);
		}else
        {
			$new_width = $width;
            $new_height = intval($size[1]/$miniw);
		}
	}else if($height && $width && ($height>$size[1] || $width>$size[0]))
    {
		// ������� ������������, �� ������� ���� ��������� ������ � ������
        $minih = $height / $size[1];
        $miniw = $width / $size[0];

        if($minih>$miniw) // ���� �� ������ ���� ��������� � ������� ����� ���, ��� �� ������, �� �� ������ ���� ������ �� ������
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
 imagealphablending($idest, false);
 imagesavealpha($idest,true);
 $transparent = imagecolorallocatealpha($idest, 255, 255, 255, 127);

    imagefilledrectangle($idest, 0, 0,$new_width, $new_height, $transparent);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,$new_width, $new_height, $size[0], $size[1]);

	$icfunc = "Image" . $format;
	if($format=='png')
	{
		$quality = ($quality - 100) / 11.111111;
		$quality = round(abs($quality));
	}else if(!($format=='jpg' || $format=='jpeg')) $quality = '';
    ob_start();
	$icfunc($idest, NULL, $quality);
	$IMG = ob_get_contents();
	ob_end_clean ();
	imagedestroy($isrc);
    imagedestroy($idest);
    return $IMG;

}

function retrieve_img_path($imgpath='')
{
	if($imgpath && file_exists($imgpath)) return $imgpath;
	else return DOCUMENT_ROOT."/templates/_common_images/no_image.png";
}

function watermark($source = '', $img_type = '')
{
	return $source;
	$img_path = split("\/", str_replace(DOCUMENT_ROOT, "", IMG_PATH));
	array_pop($img_path);
	$img_path = join("/",$img_path);

	$watermark_paths = array(
		"/images/cars",
		"/images/cars/main",
		"/images/serv/main",
		"/images/serv",
		);
	if(!in_array($img_path, $watermark_paths) || !is_file(DOCUMENT_ROOT."/templates/default/images/watermark.png"))
	{
		return $source;
	}

	include_once (DOCUMENT_ROOT.'/_system/watermark.class.php');

	$WATERMARK_DIRS = array();
	
	switch ($img_type)
	{
		case 'gif':
			$img_type = IMAGETYPE_GIF;
		break;
		case 'png':
			$img_type = IMAGETYPE_PNG;
		break;
		default:
			$img_type = IMAGETYPE_JPEG;
	}

	if($_GET['w']>=900 || !$_GET['w']) $watermark_filename = 'watermark.png';
	else $watermark_filename = 'watermark.png';

	$watermark_options			= array(
				'watermark' 	=> DOCUMENT_ROOT."/templates/default/images/".$watermark_filename,
				'halign'		=> Watermark::ALIGN_LEFT,
				'valign'		=> Watermark::ALIGN_TOP,
				'hshift'		=> 0,						
				'vshift'		=> 0,						
				'type'			=> $img_type,
				'jpeg-quality'	=> 70,
			);
	ob_start();
	Watermark::output($source, null, $watermark_options);
	$IMG = ob_get_contents();
	ob_end_clean ();
	return $IMG;
}
?>
