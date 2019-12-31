<?
//echo "<pre>";
//print_r($this->page_info);
//echo "</pre>";

$tems = array('','Резюме на открытую вакансию','Предложение о сотрудничестве','Задать вопрос');

echo '<div class="padded">'.$this->page_info['info']['text'].'</div>';
?>
<div class="gmap"><!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (начало) -->
<div id="ymaps-map-id_134898246851285032186" style="width: 626px; height: 296px;">&nbsp;</div>
<div style="width: 450px; text-align: right;"><a href="http://api.yandex.ru/maps/tools/constructor/?lang=ru-RU" target="_blank" style="color: #1A3DC1; font: 13px Arial,Helvetica,sans-serif;">Создано с помощью инструментов Яндекс.Карт</a></div>
<script type="text/javascript">function fid_134898246851285032186(ymaps) {var map = new ymaps.Map("ymaps-map-id_134898246851285032186", {center: [60.63094, 56.75814], zoom: 16, type: "yandex#map"});map.controls.add("zoomControl").add("mapTools").add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));map.geoObjects.add(new ymaps.Placemark([60.63094, 56.75814], {balloonContent: '"Голден Лимо" - Прокат и аренда лимузинов.<br/>+7 (343) 213-23-12<br/>+7 (343) 213-12-23'}, {preset: "twirl#orangeDotIcon"}));};</script> <script type="text/javascript" src="http://api-maps.yandex.ru/2.0/?coordorder=longlat&load=package.full&wizard=constructor&lang=ru-RU&onload=fid_134898246851285032186"></script> <!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (конец) --></div>
<h2>&nbsp;</h2>
<br/>
<a name="form"></a>
<?
if($_SESSION['con_form_send']){echo "<div class='goodmes'>".$_SESSION['con_form_send']."</div>";$_SESSION['con_form_send']='';$good=1;}
if(sizeof($this->form_error)) echo "<div class='formerror'><span style=\"font-weight: bold;\">Ошибка:</span><br/>".join("<br/>",$this->form_error)."</div>";
?>
	<div class="ac_box">
		<div class="accordionButton" id="acc1">Отправить сообщение</div>
		<div class="accordionContent">
			<div class="ac_content">
<div class="ac_regform">
 <form method="post" action="#form"><input type="hidden" name="proceedform[]" value="contacts">

  <div class="line">
   <div class="onefirst">
    <div class="label">Ваше имя:</div>
	<div class="inp"><input type="text" name="name" value="<?=$_POST['name']?>" /></div>
   </div>
  </div>

  <div class="line">
   <div class="onesec mrr">
    <div class="label">E-mail:</div>
	<div class="inp"><input type="text" name="mail" value="<?=$_POST['mail']?>" /></div>
   </div>
   <div class="onesec">
    <div class="label">Тема сообщения:</div>
	<div class="inp"><select name="title"><?foreach($tems as $t){?><option value="<?=$t?>"<?echo $t==$_POST['title']?' SELECTED':''?>><?=$t?></option><?}?></select></div>
   </div>
  </div>
 
  <div class="line">
   <div class="onefirst">
    <div class="label">Текст сообщения:</div>
	<div class="inp"><textarea name="text"><?=$_POST['text']?></textarea></div>
   </div>
  </div>

   <div class="line">
   <div class="code">
    <div class="capcha"><?=$this->core_show_capcha('code');?></div>
    <div class="label">Код с картинки:</div>
	<div class="inp"><input type="text" name="code" value="" maxlength="5"/></div>
	<div class="submit"><input type="submit" value="отправить" /></div>
   </div>
  </div>
 </form>
</div>
			</div><!--/ac_content-->
		</div>
		<div class="bottom"></div>
	</div><!--/ac_box--><?if(!$good){?><script>$(function() {hash = location.hash;if(hash=='#form') $( "#acc1" ).click();});</script><?}?>