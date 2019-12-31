<script language="JavaScript" src="/<?=$this->adm_path?>/moduls/mmenu/JSCookMenu_mini.js" type="text/javascript"></script>
<script language="JavaScript" src="/<?=$this->adm_path?>/moduls/mmenu/theme.js.php?adm_path=<?=$this->adm_path?>&ctpl=<?=$this->config['adm_tpl']?>" type="text/javascript"></script>
<div id="myMenuID"></div>
<script language="JavaScript" type="text/javascript">
var myMenu =
[
<?// ['картинка','текст','ссылка на...','окно в котором открывать','текст в статусной строке при наведении']
echo $_echo_menu;
?>
];
cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script>