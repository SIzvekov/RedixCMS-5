 <div class="padded">620027, г. Екатеринбург, ул. Челюскинцев 106, офис 500 (в здании гостиницы "Marins Park Hotel", 5 этаж)<br /> <div>&nbsp;</div> <div>Телефоны:&nbsp;</div> +7 (343) 213-23-12<br />+7 (343) 213-12-23&nbsp;<br /> <div>&nbsp;</div> <div>Адрес электронной почты: <a href="javascript:location.href='mailto:limuzin-ural@mail.ru">limuzin-ural@mail.ru</a></div> <br /> <div class="gmap"><!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту  (начало) --> <script src="http://api-maps.yandex.ru/1.1/?key=APR3aU8BAAAAsv6tcQIAExMSSajfuptfW-9Ej_QyGdgFnR8AAAAAAAAAAABL41zGDqIcodiYikniAoNtP_HkoA==&modules=pmap&wizard=constructor" type="text/javascript"></script> <script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-4438")[0]);
        map.setCenter(new YMaps.GeoPoint(37.300628,44.944299), 16, YMaps.MapType.MAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));

        YMaps.Styles.add("constructor#pmdomPlacemark", {
            iconStyle : {
                href : "http://api-maps.yandex.ru/i/0.3/placemarks/pmdom.png",
                size : new YMaps.Point(28,29),
                offset: new YMaps.Point(-8,-27)
            }
        });

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(37.300757,44.943324), "constructor#pmdomPlacemark", "Отель \"Гранд Круиз\"<br/>Телефон для бронирования: <br/>+7 (918) 05 33 505"));
        
        function createObject (type, point, style, description) {
            var allowObjects = ["Placemark", "Polyline", "Polygon"],
                index = YMaps.jQuery.inArray( type, allowObjects),
                constructor = allowObjects[(index == -1) ? 0 : index];
                description = description || "";
            
            var object = new YMaps[constructor](point, {style: style, hasBalloon : !!description});
            object.description = description;
            
            return object;
        }
    });
</script> <div id="YMapsID-4438" style="width:626px;height:296px">&nbsp;</div> <!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (конец) -->&nbsp;</div></div><br/>
<a name="form"></a>
	<div class="ac_box">
		<div class="accordionButton" id="acc1">Отправить сообщение</div>
		<div class="accordionContent">
			<div class="ac_content">
<div class="ac_regform">
 <form method="post" action="#form"><input type="hidden" name="proceedform[]" value="contacts">

  <div class="line">
   <div class="onefirst">
    <div class="label">Ваше имя:</div>
	<div class="inp"><input type="text" name="name" value="" /></div>
   </div>
  </div>

  <div class="line">
   <div class="onesec mrr">
    <div class="label">E-mail:</div>
	<div class="inp"><input type="text" name="mail" value="" /></div>
   </div>
   <div class="onesec">
    <div class="label">Тема сообщения:</div>
	<div class="inp"><select name="title"><option value="" SELECTED></option><option value="Резюме на открытую вакансию">Резюме на открытую вакансию</option><option value="Предложение о сотрудничестве">Предложение о сотрудничестве</option><option value="Задать вопрос">Задать вопрос</option></select></div>
   </div>
  </div>
 
  <div class="line">
   <div class="onefirst">
    <div class="label">Текст сообщения:</div>
	<div class="inp"><textarea name="text"></textarea></div>
   </div>
  </div>

   <div class="line">
   <div class="code">
    <div class="capcha"><img src="/templates/default/capcha.php?f=code&nocache=6104" /></div>
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
	</div><!--/ac_box--><script>$(function() {hash = location.hash;if(hash=='#form') $( "#acc1" ).click();});</script>