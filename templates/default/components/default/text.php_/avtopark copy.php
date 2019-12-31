<?if(isset($_GET['one'])){?>
2011 год, 10 мест
Салон: стильный угловой диван из натуральной кожи становиться украшением интерьера. Для удобства пассажиров есть аудиосистема с CD, MP3 и DVD. Кондиционер поддерживает внутри автомобиля комфортную температуру. Спецэффекты: подсветка потолков и бара дополняет элегантный стиль салона, особый дизайн создаёт эффект дискотеки, цветная неоновая и лазерная подсветки создают ощущение праздника. Бар с бокалами и фужерами, панорамное стекло.
Уважаемые клиенты! На данном автомобиле можно украшать только крышу.
<br/><br/>
<?
$num = 0;
$colperstr = 2;
for($i=1;$i<=6;$i++){
	if(!$num)
	{
		echo '<div class="row"><ul class="list">';
	}

?>
<li>
	<div class="item-holder" style="width:350px;">
		<a href="/templates/<?=$this->config['tpl']?>/images/avtopark/inner/0<?=$i?>.jpg" target="_blank" rel="shadowbox[qgal]"><img src="/templates/<?=$this->config['tpl']?>/images/avtopark/inner/0<?=$i?>.jpg?w=340" width="340" height="255" /></a>
	</div>
</li>
<?
	$num++;
	if($num==$colperstr || $i==8)
	{
		echo "</ul></div>";
		$num = 0;
	}
}?>

<a href="/avtopark/">&laquo;&nbsp;Все лимузины</a>



<?}else{?>
<?
$a = array(
"CHRYSLER 300C ЭКСКЛЮЗИВ (2011 Г.В) СУПЕР НОВИНКА!!!!!", 
"LINCOLN TOWN CAR EXCALIBUR PHANTOM «RETRO STYLE» (2009Г) БЕЛЫЙ 6+1 МЕСТ. РЕТРО НОВИНКА.", 
"КАДИЛЛАК ЭСКАЛЕЙД (CADILLAC ESCALADE), (2010 Г.В) БЕЛЫЙ ПЕРЛАМУТР НА 18 ПОСАДОЧНЫХ МЕСТ", 
"ЛИМУЗИН ФОРД ЭКСКЕРШН (FORD EXCURSION), (2010 Г.В) ЧЕРНЫЙ, 16-18 ПОСАДОЧНЫХ МЕСТ", 
"ЛИМУЗИН CHRYSLER (КРАЙСЛЕР) 300C (2010 Г.В) СЛОНОВАЯ КОСТЬ", 
"ЛИМУЗИН ЛИНКОЛЬН ТАУН КАР, 9,5 МЕТРОВ (2010Г.В), БЕЛО-СЕРЕБРИСТЫЙ", 
"ЛИМУЗИН ЛИНКОЛЬН ТАУН КАР 9,5 МЕТРОВ (2005Г.В) БЕЛЫЙ", 
"ЛИМУЗИН ЛИНКОЛЬН ТАУН КАР 9,5 МЕТРОВ (2005Г.В) БЕЛЫЙ", 
);
$num = 0;
$colperstr = 2;
for($i=1;$i<=8;$i++){
	if(!$num)
	{
		echo '<div class="row"><ul class="list">';
	}

?>
<li>
	<div class="item-holder" style="width:350px;">
		<a href="?one"><img src="/templates/<?=$this->config['tpl']?>/images/avtopark/0<?=$i?>.jpg?w=340" width="340" height="255" alt="<?=$a[$i-1]?>" /></a>
		<div class="description" style="background:none;">
			<div class="holder">
				<div class="frame">
					<strong class="title"><a href="?one"><?=$a[$i-1]?></a></strong>
				</div>
			</div>
		</div>
	</div>
</li>
<?
	$num++;
	if($num==$colperstr || $i==8)
	{
		echo "</ul></div>";
		$num = 0;
	}
}?>
<?}?>