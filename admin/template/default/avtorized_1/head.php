<html>
<head>
<title><?=$core->core_echomui('adm_pagetitle')?><?echo $core->config['title']?"-".$core->config['title']:"";?></title>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$core->config['charset']?>" />
<link href="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/css/index_css.css.php" rel="stylesheet" type="text/css" />
<link href="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/css/theme.css.php" rel="stylesheet" type="text/css" />
<link href="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/css/select.css.php" rel="stylesheet" type="text/css" />
<link href="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/css/accordings.css.php" rel="stylesheet" type="text/css" />
<link href="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/css/jquery.treeview.css.php" rel="stylesheet" type="text/css" />
<script language='JavaScript' src='/<?=$core->adm_path?>/moduls/overlib_mini/overlib_mini.js.php?admtpl=<?=$core->config['adm_tpl']?>'></script>
<script language="Javascript">function tooltip(name, html) {name = name.toLowerCase();return overlib(html, CAPTION, name)}</script>
<script language="JavaScript" src="/<?=$core->adm_path?>/template/JsHttpRequest.js" type="text/javascript"></script>

<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery-1.4.2.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.ui.core.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.ui.widget.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.ui.mouse.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.ui.sortable.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.ui.accordion.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.effects.core.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.effects.highlight.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.cookie.js"></script> 
<script type="text/javascript" src="/<?=$core->adm_path?>/moduls/jquery/jquery.treeview.js"></script> 

</head><body SCROLLING=YES>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<?if(0){?>
<tr>
        <td class="mmenu">
                <table border=0 cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                                <td>
                                        <?$core->adm_mainmenu();?>
                                </td>
                                <td align=right width="200px">
                                        <a href="http://<?=HTTP_HOST?>/" target="_blank" style="color:#000;">открыть сайт</a>
                                        &nbsp;<nobr><strong><?=$_SESSION['user']['name']?> <?=$_SESSION['user']['family']?><a href="/<?=$core->adm_path?>/?logoff" class="black" onclick="return confirm('<?=$core->core_echomui('adm_exitq')?>');"><img src="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/img/cross.gif" border=0 align="absmiddle" title="<?=$core->core_echomui('adm_exit')?>" alt="<?=$core->core_echomui('adm_exit')?>"></a></strong></nobr>
                                </td>
                        </tr>
                </table>
        </td>
</tr><?}?>