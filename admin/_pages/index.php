<?
$params = $this->adm_get_select_array(5);
?>
<div class="hat"><b><a href='http://<?=HTTP_HOST?>/' target='_blank'>http://<?=HTTP_HOST?>/</a></b>
<?if(sizeof($params)>1){?>
<?=$this->core_echomui('tpl_site')?>: <?=$this->adm_show_select("setparam", $this->param, $params, "", "onchange=\"location.href='/".$this->adm_path."/'+this.value+'/'\"");?><br/><br/>
<?}?></div>
<?$this->adm_show_sys_mes();?>
<div class="mpleftbar">
 <a href="" onclick="return showdialog('/<?=$this->adm_path?>/sort_items/','<?=$this->core_echomui('sitemap_sort_dialog_title')?>','','','','loadsitemap');"><?=$this->core_echomui('sitemap_sort_btn_title')?></a>
 <div class="clear"><br/></div>
 <div class="sitemap" id="sitemap"><?=$this->core_echomui('tpl_sitemaploading')?></div>
</div>
<div class="widgets">
<?
$sql = "SELECT * FROM `#h_adm_widgets` WHERE `public`='1' ORDER BY `sort` ASC";
$res = $this->query($sql);
$widgets = array();
while($row = $this->fetch_assoc($res))
{
	$widgets[] = array("id"=>$row['id'],"title"=>$this->core_echomui('widget_name_'.$row['id']),"url"=>$row['url'],"img"=>$row['img']);
}
?>
<?foreach($widgets as $row){?>
	<div class="widget" onmouseover="this.className='widget_h';tooltip('','<?=$row['title']?>');" onmouseout="this.className='widget';return nd();" onclick="return showdialog('<?=str_replace("{adm_path}",$this->adm_path,$row['url'])?>','<?=$row['title']?>');">
		<a href="<?=str_replace("{adm_path}",$this->adm_path,$row['url'])?>" id="widget-<?=$row['id']?>"><?if($row['img'] && file_exists(DOCUMENT_ROOT."/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/widgets/".$row['img'])){?>
		<img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/widgets/<?=$row['img']?>"><br/>
		<?}?><?=$row['title']?></a>
	</div>
<?}?>
</div>

<div style="clear:both"></div>
<div class="addpart" id="addpart">loading...</div>
<div class="exitbutton"><a href="" onclick="showdialog('/<?=$this->adm_path?>/userconfig/', '<?=$this->core_echomui('userconf')?> <?=$_SESSION['user']['login']?>',400,300);return false;"><?=$this->core_echomui('userconflink')?></a>&nbsp;&nbsp;<a href="/?logoff"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/cross.gif" alt="<?=$this->core_echomui('exitfromadm')?>" title="<?=$this->core_echomui('exitfromadm')?>"/></a></div>
<script language="JavaScript">
var setwidth = 0;
var setheight = 0;
<?
$this->jsonload[] = 'loadsitemap()';
$this->jsonload[] = 'setInterval(adjustdialogsize, 100)';
$this->jsonload[] = 'loadaddbuttons()';
?>

function loadsitemap(){loadXMLDoc('/ajax-index.php?page=mp_sitemap&isadm=1','sitemap');}

var needlinehid = 0;
function smaplineact(smid, hover)
{
        var i=1;
        if(hover==1)
        {
                needlinehid = 0;
                for(i=0;i<=<?$max = $this->fetch_assoc($this->query("SELECT MAX(`id`) as `id` FROM `#__sitemap` WHERE 1"));echo $max['id'];?>;i++)
                {
					if(document.getElementById('smapline-'+i))
					{
						document.getElementById('smapline-'+i).style.display='block';
                        document.getElementById('smaplineh-'+i).style.display='none';
					}
                }
                document.getElementById('smapline-'+smid).style.display='none';
                document.getElementById('smaplineh-'+smid).style.display='block';
        }else
        {
                needlinehid = 1;
//                setTimeout(function(){smaplinehid(smid);},1000);
        }
}
function showsubitems(i,space,linkobj)
{
	subitemshow = document.getElementById('subitems-'+i).style.display;
	if(subitemshow=='block')
	{
		document.getElementById('subitems-'+i).style.display='none';
		if(linkobj) linkobj.innerHTML = "<img src='/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/expander_plus.gif' alt='+'/>";
		setCookie('admmpsub[item'+i+']','0',3600,'/');
	}else
	{
		document.getElementById('subitems-'+i).style.display='block';
		if(document.getElementById('subitems-'+i).innerHTML=='loading...')
		{
			loadXMLDoc('/ajax-index.php?page=mp_sitemap&isadm=1&subitempid='+i+'&startspace='+space,'subitems-'+i);
		}
		if(linkobj) linkobj.innerHTML = "<img src='/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/expander_minus.gif' alt='-'/>";
		setCookie('admmpsub[item'+i+']','1',3600,'/');
	}
}
function smaplinehid(smid)
{
        if(!needlinehid) return true;
        document.getElementById('smapline-'+smid).style.display='block';
        document.getElementById('smaplineh-'+smid).style.display='none';
        needlinehid = 0;
}

function deletepagefromtree(pid)
{
        loadXMLDoc('/ajax-index.php?page=mp_sitemap&isadm=1&delete='+pid,'sitemap');
}

function movepage(pid, dir)
{
        loadXMLDoc('/ajax-index.php?page=mp_sitemap&isadm=1&move='+pid+'&dir='+dir,'sitemap');
}

function setCookie (name, value, expires, path, domain, secure) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}
function loadaddbuttons()
{
	loadXMLDoc('/ajax-index.php?page=addpart&isadm=1','addpart');
}

</script>

<?echo file_get_contents("http://redixcms.ru/_reformal/code.php");?>