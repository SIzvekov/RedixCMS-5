<?php //v.1.0.?>
<title><?=$this->page_info['meta_title']?></title>
<meta name="keywords" content="<?=str_replace('"',"'",$this->page_info['meta_keywords'])?>" />
<meta name="description" content="<?=str_replace('"',"'",$this->page_info['meta_description'])?>" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$this->config['charset']?>" />
<LINK REV="MADE" HREF="http://rrwd.ru/" title="Rekora&Redix webDevelopment" />
<?if($fileurl = $this->tplfile_exists("css/bootstrap-combined.min.css")){?>
<link href="<?=$fileurl?>" type="text/css" rel="stylesheet" />
<?}?>
<?if($fileurl = $this->tplfile_exists("css/stylesheet.css")){?>
<link href="<?=$fileurl?>" type="text/css" rel="stylesheet" />
<?}?><?if($fileurl = $this->tplfile_exists("css/stylesheet.ie6.css")){?>
<!--[if IE 6]><link rel="stylesheet" href="<?=$fileurl?>" type="text/css" media="screen" /><![endif]-->
<?}?><?if($fileurl = $this->tplfile_exists("css/stylesheet.ie7.css")){?>
<!--[if IE 7]><link rel="stylesheet" href="<?=$fileurl?>" type="text/css" media="screen" /><![endif]-->
<?}?><?
if($fileurl = $this->tplfile_exists("favicon.ico")){?>
<link rel="icon" href="<?=$fileurl?>" type="image/x-icon" />
<link rel="shortcut icon" href="<?=$fileurl?>" type="image/x-icon" />
<?}?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script> 
<?
if($fileurl = $this->tplfile_exists("js/JsHttpRequest.js")){?>
<script type="text/javascript" src="<?=$fileurl?>"></script>
<?}?><?
if($fileurl = $this->tplfile_exists("js/js.js")){?>
<script type="text/javascript" src="<?=$fileurl?>"></script>
<?}?><?
////// HIGHSLIDE
if(0){
?><?
if($fileurl = $this->tplfile_exists("js/highslide/highslide-full.js")){?>
<script type="text/javascript" src="<?=$fileurl?>"></script>
<?}?><?
if($fileurl = $this->tplfile_exists("js/highslide/highslide.config.js")){?>
<script type="text/javascript" src="<?=$fileurl?>"></script>
<?}?><?if($fileurl = $this->tplfile_exists("css/highslide.css")){?>
<link href="<?=$fileurl?>" type="text/css" rel="stylesheet" />
<?}?><?if($fileurl = $this->tplfile_exists("css/highslide-ie6.css")){?>
<!--[if lt IE 7]>
<link href="<?=$fileurl?>" type="text/css" rel="stylesheet" />
<![endif]-->
<?}?><?
}
/////////////////////////HIGHSLIDE
?><?

///////// IS ADMIN
if(intval($_SESSION['user']['group']['isadmin'])){?>
<script language='JavaScript' src='/<?=$this->adm_path?>/moduls/overlib_mini/overlib_mini.js.php?admtpl=<?=$this->config['adm_tpl']?>'></script>
<script language="Javascript">function tooltip(name, html) {name = name.toLowerCase();return overlib(html, CAPTION, name)}</script>

<link href="/<?=$this->adm_path?>/moduls/highslide/highslide.css" rel="stylesheet" type="text/css" />
<script language='JavaScript' src='/<?=$this->adm_path?>/moduls/highslide/highslide-with-html.js'></script>
<script language='JavaScript' src='/<?=$this->adm_path?>/moduls/highslide/highslide.config.js'></script>
<script language='JavaScript' src='/<?=$this->adm_path?>/moduls/highslide/lang/<?=$this->admin_par?>.js'></script>

<script type="text/javascript">
hs.graphicsDir = '/<?=$this->adm_path?>/moduls/highslide/graphics/';
hs.outlineType = 'rounded-white';

hs.wrapperClassName = 'draggable-header';
</script><?
}
///////// IS ADMIN
?>
<!--<script type="text/javascript" src="/templates/default/js/jquery-1.6.min.js"></script>-->
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script> -->
<script src="/templates/default/js/ui/jquery.ui.core.js" type="text/javascript"></script> 
<script src="/templates/default/js/ui/jquery.ui.widget.js" type="text/javascript"></script> 
<script src="/templates/default/js/ui/jquery.ui.datepicker.js" type="text/javascript"></script> 
<script src="/templates/default/js/ui/jquery.ui.datepicker-ru.js" type="text/javascript"></script>
<script type="text/javascript" src="/templates/default/js/accordion.js"></script>
<link rel="stylesheet" href="/templates/default/css/jquery-ui.css" type="text/css" media="screen" />
<link href="/templates/default/js/shadowbox/shadowbox.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/templates/default/js/shadowbox/shadowbox.js"></script>
<script type="text/javascript" src="/templates/default/js/bootstrap-modal.js"></script>
<script type="text/javascript" src="/templates/default/js/jquery.inputmask.js"></script>
<script type="text/javascript" src="/templates/default/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/templates/default/js/swfobject.js"></script>

<script type="text/javascript">
  var flashvars = {};
	var params = {
	  wmode: "transparent",
	  menu: "false",
	  quality: "best"
	};
	var attributes = {};
	swfobject.embedSWF("/swf/gl1.swf", "flashka", "250", "150", "9.0.0", "/swf/gl1.swf", flashvars, params, attributes);
</script>
<script type="text/javascript">
Shadowbox.init();
</script>
<script type="text/javascript">
	$(function() {
		$(document).scroll(function() {
			if($(document).scrollTop() > 100) {
				$('#gotop').show();
			} else {
				$('#gotop').hide();
			}
		});

		$('#gotop a').click(function() {
			$(document).scrollTop(0);
			return false;
		});
	});
</script>
<meta name='yandex-verification' content='629ed538caed1a24' />



	
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
    <script src="jquery-ui-i18n.js"></script>
    <link rel="stylesheet" href="/templates/default/css/jquery-ui.css">
    <style type="text/css">
	    label {margin-right:12px; }
        input {width: 200px; text-align: left; margin-right: 10px}
        #wrapper > * {float: left}
    </style>  
    <script type="text/javascript">
$(function() {
	
    $('#inline').datepicker($.datepicker.regional["ru"]);
			
});
    </script>  

	
	
    <script type="text/javascript">	
	/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
	/* Written by Andrew Stromnov (stromnov@gmail.com). */
	(function( factory ) {
		if ( typeof define === "function" && define.amd ) {
	
			/* AMD. Register as an anonymous module. */
			define([ "../datepicker" ], factory );
		} else {
	
			/* Browser globals */
			factory( jQuery.datepicker );
		}
	}(function( datepicker ) {
	
	datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3C;Пред',
		nextText: 'След&#x3E;',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
		'Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Нед',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	datepicker.setDefaults(datepicker.regional['ru']);
	
	return datepicker.regional['ru'];
	
	}));
    </script>  	
