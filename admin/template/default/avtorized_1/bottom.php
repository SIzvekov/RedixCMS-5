</table>

<script language="JavaScript">

var dialogopencallback = '';
var dialogclosecallback = '';
function popupwindowshow(act)
{
	if(!(document.getElementById('rxcms_bgpad') && (act=='none'||act=='block'))) return false;
	document.getElementById('rxcms_bgpad').style.display = act;
	document.getElementById('rxcms_dialog').style.display = act;

	if(document.getElementById('dialog_iframe_win') && act=='none')
	{
		document.getElementById('dialog_iframe').removeChild(document.getElementById('dialog_iframe_win'));
		if(dialogclosecallback) window[dialogclosecallback]();
	}
}
function showdialog(url, title,w,h, openclb, closeclb)
{
	dialogopencallback = openclb;
	dialogclosecallback = closeclb;
	if(document.getElementById('dialog_iframe_win')) document.getElementById('dialog_iframe').removeChild(document.getElementById('dialog_iframe_win'));

	document.getElementById('dialog_title').innerHTML = title;
	loadershow('go');

	var iframe = document.createElement("iframe");
	iframe.src = url;
	iframe.name = 'dialogframe';
	iframe.id = 'dialog_iframe_win';
	iframe.onload = function(){loadershow('stop');}
	document.getElementById('dialog_iframe').appendChild(iframe);
	
	if(dialogopencallback) window[dialogopencallback]();
	popupwindowshow('block');

	setwidth = w;
	setheight = h;
	return false;
}

var oldiframesrc;
var newsrc;
function adjustdialogsize()
{
	if(setwidth || setheight)
	{
		if(setwidth) {newwidth = setwidth;newleft = parseInt(parseInt(getClientWidth())/2)-parseInt(newwidth/2);} else {newwidth = parseInt(getClientWidth())-30;newleft = 15;}
		if(setheight) {newheight = setheight;} else {newheight = parseInt(getClientHeight())-30;}
	}else
	{
		var newwidth = parseInt(getClientWidth())-30;
		var newheight = parseInt(getClientHeight())-30;
		var newleft = 15;
	}
	var minwidth = 0;
	var minheight = 0;
	
	if(minwidth && minwidth>newwidth) newwidth = minwidth;
	if(minheight && minheight>newheight) newheight = minheight;

	document.getElementById('rxcms_dialog').style.width = newwidth+'px';
	document.getElementById('rxcms_dialog').style.height = newheight+'px';

	document.getElementById('rxcms_dialog').style.left = newleft+'px';

	document.getElementById('dialog_iframe').style.width = newwidth-10+'px';
	document.getElementById('dialog_iframe').style.height = newheight-40+'px';

	if(document.getElementById('dialog_iframe_win'))
	{
		newsrc = top.frames.dialogframe.document.location;
		if(newsrc!=oldiframesrc) loadershow('go');
		oldiframesrc = newsrc;
	}
	
}
function loadershow(act)
{
	if(!document.getElementById('loaderstat')) return false;
	if(act=='stop') document.getElementById('loaderstat').style.display = 'none';
	if(act=='go') document.getElementById('loaderstat').style.display = 'block';
}
function getClientWidth(){return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;}
function getClientHeight(){return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;}


window.onload = function(){<?if(is_array($core->jsonload)) foreach($core->jsonload as $func) echo $func.";";?>}
</script>

<?
$dialogs = array("sort_items","photos_set");
if(!in_array($core->way, $dialogs)){?>
<div style="clear:both;padding-top:7px;text-align:center;color:#dcdcdc"><a href="http://redixcms.ru/" target="_blank"  style="color:#dcdcdc">redixCMS</a>. <?=$core->core_echomui('cms_core_version')?>: <?=CMS_VERSION?>. <?=$core->core_echomui('cms_adm_version')?>: <?=ADM_VERSION?></div>
<?}?>

<div class="bgpad" id="rxcms_bgpad"></div>
<div class="rxcms_dialog" id="rxcms_dialog"><div class="dialog_hat"><div class="dialog_title" id="dialog_title"></div><div id="loaderstat" class="loaderstat"><?=$core->core_echomui('dialogloadstat')?></div><div class="dialog_closetab"><a href="" onclick="popupwindowshow('none');return false;"><img src="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/img/dialogwindow/close.png" alt="X" /></a></div></div><div class="dialog_iframe" id="dialog_iframe"></div></div>
</body>
</html>