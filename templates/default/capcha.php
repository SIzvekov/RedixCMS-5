<?php
error_reporting(0);
session_cache_limiter('nocache');
session_start(); // стартуем сессию
// unset($_SESSION['kapcha']);
$session_kod='';

// настройки капчи
require("capcha/config.php");

$im=imagecreate($W,$H);
$bg=imagecolorallocate($im, $BG_COL_R, $BG_COL_G, $BG_COL_B);//цвет фона
$c=imagecolorallocate($im, $BOR_COL_R, $BOR_COL_G, $BOR_COL_B);//цвет бордюра
$tc=imagecolorallocate($im, $TEXT_COL_R, $TEXT_COL_G, $TEXT_COL_B);//цвет кода

for($i=0;$i<$BOR;$i++)
{
	imageline($im, $i, 0, $i, $H, $c);
	imageline($im, $W-1-$i, 0, $W-1-$i, $H, $c);
	imageline($im, 0, $i, $W, $i, $c);
	imageline($im, 0, $H-1-$i, $W, $H-1-$i, $c);
}


$arr_kod=array();
for ($i_zn=0;$i_zn<$COL_NUM;$i_zn++)
{
	$i_rand=$arr_letter[rand(0,sizeof($arr_letter)-1)];
	imagettftext($im, $FONT_SIZE,rand(-$UGOL_RIGHT, $UGOL_LEFT),$arr_x[$i_zn],rand($FIRST_TOP, $SECOND_TOP),$tc,'capcha/'.$FONT_FAMILY, $i_rand);
	$arr_kod[]=$i_rand;
}

// Пишем в сессию код
$arr_kod = join("",$arr_kod);
$_SESSION['kapcha_source'][$_GET['f']] = $arr_kod;
$_SESSION['kapcha'][$_GET['f']] = md5($arr_kod);


header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?>