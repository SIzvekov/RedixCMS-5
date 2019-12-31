<?php //v.1.0.?>
<title><?= $this->page_info['meta_title'] ?></title>
<meta name="keywords" content="<?= str_replace( '"', "'", $this->page_info['meta_keywords'] ) ?>"/>
<meta name="description" content="<?= str_replace( '"', "'", $this->page_info['meta_description'] ) ?>"/>
<meta http-equiv="Content-Type" content="text/html; charset=<?= $this->config['charset'] ?>"/>
<meta name=viewport content="width=device-width, initial-scale=1" />
<LINK REV="MADE" HREF="http://rrwd.ru/" title="Rekora&Redix webDevelopment"/>
<? if ( $fileurl = $this->tplfile_exists( "css/bootstrap-combined.min.css" ) ) { ?>
	<link href="<?= $fileurl ?>" type="text/css" rel="stylesheet"/>
<? } ?>
<? if ( $fileurl = $this->tplfile_exists( "css/stylesheet.css" ) ) { ?>
	<link href="<?= $fileurl ?>?f=3" type="text/css" rel="stylesheet"/>
<? } ?><? if ( $fileurl = $this->tplfile_exists( "css/stylesheet.ie6.css" ) ) { ?>
	<!--[if IE 6]>
	<link rel="stylesheet" href="<?=$fileurl?>" type="text/css" media="screen"/><![endif]-->
<? } ?><? if ( $fileurl = $this->tplfile_exists( "css/stylesheet.ie7.css" ) ) { ?>
	<!--[if IE 7]>
	<link rel="stylesheet" href="<?=$fileurl?>" type="text/css" media="screen"/><![endif]-->
<? } ?>
<? if ( $fileurl = $this->tplfile_exists( "css/stylesheet.ie8.css" ) ) { ?>
	<!--[if IE 8]>
	<link rel="stylesheet" href="<?=$fileurl?>" type="text/css" media="screen"/><![endif]-->
<? } ?>
<?
	if ( $fileurl = $this->tplfile_exists( "favicon.ico" ) ) {
		?>
		<link rel="icon" href="<?= $fileurl ?>" type="image/x-icon"/>
		<link rel="shortcut icon" href="<?= $fileurl ?>" type="image/x-icon"/>
	<? } ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<?
	if ( $fileurl = $this->tplfile_exists( "js/JsHttpRequest.js" ) ) {
		?>
		<script async type="text/javascript" src="<?= $fileurl ?>"></script>
	<? } ?><?
	if ( $fileurl = $this->tplfile_exists( "js/js.js" ) ) {
		?>
		<script async type="text/javascript" src="<?= $fileurl ?>?_=001"></script>
	<? } ?><?
	////// HIGHSLIDE
	if ( 0 ) {
		?><?
		if ( $fileurl = $this->tplfile_exists( "js/highslide/highslide-full.js" ) ) {
			?>
			<script async type="text/javascript" src="<?= $fileurl ?>"></script>
		<?
		} ?><?
		if ( $fileurl = $this->tplfile_exists( "js/highslide/highslide.config.js" ) ) {
			?>
			<script async type="text/javascript" src="<?= $fileurl ?>"></script>
		<?
		} ?><? if ( $fileurl = $this->tplfile_exists( "css/highslide.css" ) ) {
			?>
			<link href="<?= $fileurl ?>" type="text/css" rel="stylesheet"/>
		<?
		} ?><? if ( $fileurl = $this->tplfile_exists( "css/highslide-ie6.css" ) ) {
			?>
			<!--[if lt IE 7]>
			<link href="<?=$fileurl?>" type="text/css" rel="stylesheet"/>
			<![endif]-->
		<?
		} ?><?
	}
	/////////////////////////HIGHSLIDE
?><?

	///////// IS ADMIN
	if ( intval( $_SESSION['user']['group']['isadmin'] ) ) {
		?>
		<script async language='JavaScript' src='/<?= $this->adm_path ?>/moduls/overlib_mini/overlib_mini.js.php?admtpl=<?= $this->config['adm_tpl'] ?>'></script>
		<script language="Javascript">function tooltip(name, html) {
				name = name.toLowerCase();
				return overlib(html, CAPTION, name)
			}</script>

		<link href="/<?= $this->adm_path ?>/moduls/highslide/highslide.css" rel="stylesheet" type="text/css"/>
		<script language='JavaScript' src='/<?= $this->adm_path ?>/moduls/highslide/highslide-with-html.js'></script>
		<script language='JavaScript' src='/<?= $this->adm_path ?>/moduls/highslide/highslide.config.js'></script>
		<script language='JavaScript' src='/<?= $this->adm_path ?>/moduls/highslide/lang/<?= $this->admin_par ?>.js'></script>

		<script type="text/javascript">
			hs.graphicsDir = '/<?=$this->adm_path?>/moduls/highslide/graphics/';
			hs.outlineType = 'rounded-white';

			hs.wrapperClassName = 'draggable-header';
		</script><?
	}
	///////// IS ADMIN
?>
<!--<script async src="/templates/default/js/ui/jquery.ui.core.js" type="text/javascript"></script> 
<script async src="/templates/default/js/ui/jquery.ui.widget.js" type="text/javascript"></script> 
<script async src="/templates/default/js/ui/jquery.ui.datepicker.js" type="text/javascript"></script> 
<script async src="/templates/default/js/ui/jquery.ui.datepicker-ru.js" type="text/javascript"></script>-->
<script async type="text/javascript" src="/templates/default/js/accordion.js"></script>
<!--<link rel="stylesheet" href="/templates/default/css/jquery-ui.css" type="text/css" media="screen" />-->
<link href="/templates/default/js/shadowbox/shadowbox.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript" src="/templates/default/js/shadowbox/shadowbox.js"></script>
<script async type="text/javascript" src="/templates/default/js/bootstrap-modal.js"></script>
<script async type="text/javascript" src="/templates/default/js/bootstrap-carousel.js"></script>
<script async type="text/javascript" src="/templates/default/js/bootstrap-transition.js"></script>
<script type="text/javascript" src="/templates/default/js/jquery.inputmask.js"></script>
<script type="text/javascript" src="/templates/default/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/templates/default/js/jquery.simulate.js"></script>
<script type="text/javascript" src="/templates/default/js/swfobject.js"></script>

<script type="text/javascript">
	var flashvars = {};
	var params = {
		wmode: "transparent",
		menu: "false",
		quality: "best"
	};
	var attributes = {};
	var useragent=navigator.userAgent;
	if(!navigator.userAgent.match('/Mobile/')){
		swfobject.embedSWF("/swf/gl1.swf", "flashka", "250", "150", "9.0.0", "/swf/gl1.swf", flashvars, params, attributes);
	}

	var params = {
		menu: "false",
		quality: "best"
	};
	swfobject.embedSWF("/images/mpa/GoldenLimoBanner.swf", "mpbanner", "100%", "130", "9.0.0", "/images/mpa/GoldenLimoBanner.swf", flashvars, params, attributes);
</script>

<script type="text/javascript">
	Shadowbox.init();
</script>
<script type="text/javascript">
	$(function () {
		$(document).scroll(function () {
			if ($(document).scrollTop() > 100) {
				$('#gotop').show();
			} else {
				$('#gotop').hide();
			}
		});

		$('#gotop, #gotop a').click(function () {
			$(document).scrollTop(0);
			return false;
		});

		$("a.select-button").on("click", function(){$(this).parent().find('select').simulate('mousedown');});
	});
</script>
<meta name='yandex-verification' content='629ed538caed1a24'/>
<link rel="stylesheet" href="/templates/default/css/jquery-ui.css">
<script src="/templates/default/js/ui/jquery-ui.js" type="text/javascript"></script>
<script async src="/templates/default/js/calculate.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function () {
		$('#inline').datepicker($.datepicker.regional["ru"]);
	});
</script>
<script async src="/templates/default/js/datepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="/templates/default/js/common.js"></script>