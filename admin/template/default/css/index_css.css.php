<?
header("Content-Type: text/css; charset=utf-8");
$c_adm_template = "default";
$adm_path = "admin";
?>
/* Основные стили */
body, table{font-size: 8pt;	margin: 0px;padding: 0px;font-family:Verdana, Arial, Helvetica, sans-serif;}
form {padding:0px;	margin:0px;}
a,a.normal {color: #00f;text-decoration: underline;}
a:hover,a.normal:hover {color: #f00;text-decoration: none;}
a.black {color: #000;}
a.boldblack, a.boldblack:hover {color: #000;text-decoration: none;font-weight:bold;}

/* Стиль заголовка страницы */
div.parttitle{font-weight: bold;font-size:15px;margin:10px;}

div.nostrings{text-align: center;}

/* Стили системных сообщений */
div.sys_mes{background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/message.png) left top no-repeat;height: 24px;padding-left:30px;padding-top:3px;}
div.sys_mes_ok{background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/ok.png) left top no-repeat;height: 24px;padding-left:30px;padding-top:3px;color:#008D02;}
div.sys_mes_err{background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/err.png) left top no-repeat;height: 24px;padding-left:30px;padding-top:3px;color:#ff4509;}
div.sys_mes_war{background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/warning.png) left top no-repeat;height: 24px;padding-left:30px;padding-top:3px;color:#ecdb8c;}
div.sys_mes_del{background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/del.png) left top no-repeat;height: 24px;padding-left:30px;padding-top:3px;color:#000;}

div.toolbar
{
	background: #fff;
	padding: 5px;
	position: fixed;
	top: 0px;
	z-index: 100;
	width: 99%;
	border-bottom: #cfcfcf 1px solid;
}

div.headspace
{
	height: 45px;
}

input.addbutton {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/add.png) left no-repeat; border: 1px solid #fff; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}
input.addbutton_h {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/add.png) left no-repeat; border: 1px solid #008D02; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}

div.div_savebutton {float:left;}
input.savebutton {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/save.png) left no-repeat; border: 1px solid #fff; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}
input.savebutton_h {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/save.png) left no-repeat; border: 1px solid #008D02; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}

div.div_appbutton {float:left;}
input.appbutton {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/applay.png) left no-repeat; border: 1px solid #fff; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}
input.appbutton_h {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/applay.png) left no-repeat; border: 1px solid #008D02; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}

div.div_cancelbutton {float:left;}
input.cancelbutton {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/cancel.png) left no-repeat; border: 1px solid #fff; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}
input.cancelbutton_h {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/infoico/small/cancel.png) left no-repeat; border: 1px solid #f00; background-color: #fff;cursor:pointer;height: 26px;padding-left:30px;}

div.div_nextbutton {float:left;}
input.nextbutton {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/move.gif) right no-repeat; border: 1px solid #fff; background-color: #fff;cursor:pointer;height: 26px;padding-right:22px;}
input.nextbutton_h {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/move.gif) right no-repeat; border: 1px solid #008D02; background-color: #fff;cursor:pointer;height: 26px;padding-right:22px;}

div.div_backbutton {float:left;}
input.backbutton {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/back.gif) left no-repeat; border: 1px solid #fff; background-color: #fff;cursor:pointer;height: 26px;padding-left:23px;}
input.backbutton_h {background: url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/back.gif) left no-repeat; border: 1px solid #008D02; background-color: #fff;cursor:pointer;height: 26px;padding-left:23px;}


/* стиль основной таблицы с инфой*/
table.f_table {width:100%;border:1px solid #cfcfcf;border-collapse: collapse;}

table.f_table tr.zebra_white {background:#fff;}
table.f_table tr.zebra_grey {background:#f9f9f9;}
table.f_table tr.f_hover {background:#f1f3f5;}

table.f_table td {border:1px solid #dfdfdf; text-align:center;}
table.f_table th {background: #ccc;color: #fff;}
table.f_table th a {color: #fff;}
table.f_table th a:hover {color: #efefef;}

.input_normal {font-size: 8pt;background-color: #fcfcfe;border: 1px solid #cfcfcf;}
.input_focus {font-size: 8pt;background-color: #ffffff;border: 1px solid #919b9c;}
.input_error {font-size: 8pt;background-color: #fcfcfe;border: 1px solid #f00;}

div.filtr {text-align:right;}

table.navigation_bar{font-size:11px; margin: 10px 0px 10px 0px;}
table.navigation_bar a, table.navigation_bar a:hover{font-size:11px;}
table.navigation_bar td.cpage {background-color: #edeff1;}

table.edittablemain {border:0px;width:100%;}
table.edittablemain tr{background:none;}
table.edittablemain tr:hover{background:#f3f3f3;}
table.edittablemain td.td_lable_alone {font-weight: bold;text-align:center;}
table.edittablemain td.td_lable {width:150px;padding:5px;font-weight: bold;vertical-align: top;text-align: right;text-transform:capitalize;}
table.edittablemain td.td_cont{padding:5px;vertical-align: top;text-align: left;}

table.addconttable {border:0px;width:100%;}
table.addconttable td.td_lable_alone {font-weight: bold;text-align:center;}
table.addconttable td.td_lable {width: 150px;padding-left: 10px;font-weight: bold;vertical-align: top;text-align: right;font-size:11px;}

div.td_lable{width: 15%;padding-left: 10px;font-weight: bold;vertical-align: top;text-align: right;font-size:11px;float:left;margin-bottom:5px;}
div.td_cont{margin-left:5px;margin-bottom:5px;float:left;width: 80%;}

div.addconsteptitle{padding:5px 10px;font-size:15px;border:#cfcfcf 1px solid;margin:2px 0px 0px 0px;background:#efefef;cursor:pointer;}
div.addconsteptitle_nsh{padding:5px 10px;font-size:13px;border:#cfcfcf 1px solid;margin:2px 0px 0px 0px;background:#fafafa;cursor:pointer;}
div.addconsteptitle img{margin-right:7px;float:left;margin-top:3px;}
div.addconsteptitle_nsh img {margin-right:7px;float:left;margin-top:1px;}
div.addconstep {background:#fcfcfc;padding:10px 10px;clear:both;margin-bottom:13px;}

select.filtr_select {font-size:11px;}

div.linkselect
{
	margin-top:-10px;
	min-width: 200px;
	position:absolute;
	display:none;
	background:#fff;
	border:2px solid #ccc;
	padding: 13px 8px 8px 8px;
}

div.linkselect div.linkselectclose
{
	margin: -3px 3px 0px 0px;
	position: absolute;
	top: 0px;
	right: 0px;
}

div.linkselect div.linkselectclose a, div.linkselect div.linkselectclose a:hover
{
	font-size: 15px;
	font-weight: bold;
	color: #a00;
	text-decoration: none;
}

div.linksel_normal, div.linksel_hover
{
	padding: 2px;
	border: 1px solid #fff;
	background: #fff;
}
div.linksel_hover
{
	background: #ececec;
}

div.lsbookmarks
{
	float: left;
	text-align: center;
	width:50%;
	margin:0px -1px 7px -1px;
	padding: 4px 0px 4px 0px;
	border-top: 1px solid #dfdfdf;
	border-left: 1px solid #dfdfdf;
	border-bottom: 2px solid #ccc;
	border-right: 1px solid #dfdfdf;
	color: #666;
	cursor: pointer;
	white-space: nowrap;
}
div.lsbookmarks_sel
{
	float: left;
	text-align: center;
	width:50%;
	margin:0px -2px 7px -2px;
	padding: 4px 0px 4px 0px;
	border-top: 2px solid #ccc;
	border-left: 2px solid #ccc;
	border-bottom: 1px solid #fff;
	border-right: 2px solid #ccc;
	cursor: default;
	white-space: nowrap;
}

.simpleeditfield {background: none;border:none;padding:1px;}
.simpleeditfield_h {background: #bbeebb;border:1px dashed #88dd88;padding:0px;}

div.mpleftbar{float:left;overflow:show;margin-right: 10px;}

div.sitemap
{
	border: 1px solid #ccc;
	background: #fcfcfc;
	float:left;
	width: 600px;
	margin-right: 0px;
	position:relative;
}
div.widgets
{
	border: 0px;
	float:left;
	width: 400px;
}
div.widgets div.widget
{
	float:left;
	width: 80px;
	height: 80px;
	margin: 0px 5px 5px 0px;
	padding: 2px;
	border: 1px solid #fff;
	background: #fff;
	cursor: pointer;
	text-align: center;
	font-size: 11px;
}
div.widgets div.widget_h
{
	float:left;
	width: 80px;
	height: 80px;
	margin: 0px 5px 5px 0px;
	padding: 2px;
	border: 1px solid #aaa;
	background: #fcfcfc;
	cursor: pointer;
	text-align: center;
	font-size: 11px;
}
div.widgets a, div.widgets a:hover
{
	text-decoration:none;
	color: #000;
}
div.widgets img
{
	border: 0px;
	margin: 0px;
	padding: 0px;
}

div.sitemap img
{
	border: 0px;
	margin: 0px;
	padding: 0px;
}

div.smapline
{
	padding: 6px;
}
div.mpline{background: none;margin:0px;border:0px;font-size:12px;}
div.mpline_hover{background: #fff;border: 1px solid #cfcfcf;margin:-1px;font-size:12px;}
div.mpline a, div.mpline a:hover{color:#000;text-decoration:none;}
div.mpline span.pagetypeinfo{display:none;}

div.mpline span.pagetitle,div.mpline_hover span.pagetitle{float:left;margin-right:5px;}
div.mpline span.numcont1, div.mpline_hover span.numcont2 {display:block;float:left;margin-right:5px;}
div.mpline span.numcont2, div.mpline_hover span.numcont1 {display:none;float:left;margin-right:5px;}

div.mpline0, div.mpline_hover0, div.mpline0 a, div.mpline0 a:hover{color:#ccc;text-decoration:none;}

div.smtoolbar a,div.smtoolbar a:hover{border:1px solid #cfcfcf;padding:9px;height:12px;background:#ececec;margin-right:2px;float:left;font-size:10px;color:#000;text-decoration:none;}
div.smtoolbar a:hover{background:#fafafa;}

div.mpline div.smtoolbar,div.mpline0 div.smtoolbar{display:none;}
div.mpline_hover div.smtoolbar, div.mpline_hover0 div.smtoolbar{display:block;right:0px;margin-top:2px;margin-left:19px;height:30px;position:absolute;margin-right:-3px;}
div.smtoolbar_hover img, div.smtoolbar img,div.smtoolbar_hover0 img, div.smtoolbar0 img{border:0px;}


div.smapplusminus
{
	float:left;
	margin-left:5px;
	padding-top:5px;
	margin-right:10px;
	width: 10px;
}
div.smapplusminus a, div.smapplusminus a:hover{color: #000;text-decoration:none;font-weight:bold;}
div.smapplusminus a:hover{color: #00f;}
div.smapplusminus img{width:12px;border:0px;height:12px;}
div.sitemap div.sml-left{float: left;}
div.sitemap div.sml-right{float: left;}
div.clear{clear: both;}
div.addpart{float:left;width: 600px;}
div.addpartitem{float:left;padding:10px 2px 0px 10px;}
div.addpart div.title{font-weight:bold;font-size:16px;}
div.steptit{font-weight:bold;}
div.exitbutton {position:absolute;z-index:1;top:3px;right:5px;}
div.exitbutton img{border:0px;}
div.hat{margin:5px;}
div.hat a{color:#000;}

div.bgpad{position: fixed; z-index: 100;top: 0; left: 0; height: 100%; width: 100%; background-color: #000022;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=25);-moz-opacity: 0.25;-khtml-opacity: 0.25;opacity: 0.7;display: none;}
div.rxcms_dialog{position:fixed;left:15px;top:15px;width:90%;height:90%;margin:0px;padding:0px;background:#fff;border:1px solid #000;display:none;z-index:101;}
div.dialog_hat{background:#cfcfcf url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/dialogwindow/hatbg.jpg);width:100%;height: 30px;}
div.dialog_title{padding: 5px 0px 0px 20px;font-size:13px;font-weight:bold;font-family:Tahoma;float:left;}
div.loaderstat{padding: 8px 0px 0px 22px;float:left;margin-left:15px;height:22px;background:url(/<?=$adm_path?>/template/<?=$c_adm_template?>/img/dialogwindow/loader.gif) no-repeat left center;}
div.dialog_closetab{float:right;height:30px;width:30px;padding:0px;margin:0px;}
div.dialog_closetab img{border:0px;width:30px;height:30px;}
div.dialog_iframe{border:0px;margin:5px;}
div.dialog_iframe iframe{border:0px;height:100%;width:100%;padding:0px;margin:0px;}

#sortable { margin: 0; padding: 0;  }
#sortable div { margin: 4px 0px; padding: 5px; border:1px solid #ccc;background:#fcfcfc;cursor:move;font-size:13px;}
html>body #sortable div { height: 1.5em; line-height: 1.2em; }
span.sortsublink a{color:#888;font-size:11px;text-decoration:underline;}
span.sortsublink a:hover{color:#000;font-size:11px;text-decoration:none;}

div.ui-state-default{overflow:hidden;}

#sysmes{padding:10px;}