<?
/*
echo "<pre>";
//print_r($_POST);
//print_r($row);
print_r($_COOKIE);
echo "</pre>";
*/
$allowsteps = array(1);
if($row['tpl_file'] || $row['add_button']=='razdel') {$allowsteps[]=2;}
if($row['tpl_file']) {$allowsteps[]=3;$allowsteps[]=4;}
if($row['add_button']=='content')$allowsteps[]=5;


if($row['parent_parts']=='root')
{
	array_shift($allowsteps);
}
echo $this->adm_showparttitle($this->core_echomui('tpl_'.$ACTION.'part')." ".$this->core_echomui('add_button_'.$row['id'])).'<div id="accordion">';

foreach($allowsteps as $k=>$cur_step)
{
	$noshow = (isset($_COOKIE['pcnfstep'.$cur_step])&&!intval($_COOKIE['pcnfstep'.$cur_step])?1:0);//(isset($_GET['edit'])&&$cur_step!=end($allowsteps));
	echo '<h3 onclick="location.href=\'#step'.$k.'\'"><a><span style="font-weight: bold;">'.$this->core_echomui('step')." ".($k+1).":</span> ".$this->core_echomui('add_contetn_step'.$cur_step.'_desc')."</a></h3><div>";


switch($cur_step)
{
	case '1':
		if($row['parent_parts']=='root') $checkacc = 1;
		elseif(!$row['parent_parts']) $nocheck = 1;
		else
		{
			$parts = split(",",$row['parent_parts']);
			$dopcom_ids = array();
			foreach($parts as $k=>$v)
			{
				$parts[$k] = intval($v);
				$dopcom_ids = $this->adm_getcomids($parts[$k]);
			}
			$original_parts = $parts;

			$dopcom_ids = array_unique($dopcom_ids);
			$parts = array_merge($parts,$dopcom_ids);
			$checkacc = sizeof($parts);
		}
		$ar = $this->mysql_get_fields("#__sitemap");
		foreach($ar as $k=>$v) $selfils[$k] = trim($v['Field']);
		
		$dopcond = '';
		if($ACTION=='edit') $dopcond = " && `id`!=".intval($this->id);
		else $dopcond = '';
		$sql = "SELECT * FROM `#__sitemap` WHERE `type`='page'".$dopcond." ORDER BY `sort` ASC";
		$infa = $this->core_get_tree($sql);
		$infa = $this->core_get_tree_keys(0, $selfils,$infa, 1, 1);
		echo '<div class="clear"></div><div class="sitemap1"><ul id="tree">';
		if(in_array(0, $parts) || $nocheck)
		{
			$colstrings++;
			$thispid = 0;
			echo '<a href="" id="selpart-root" onclick="selpart(\'root\',0,0);return false;">';
		}
		else echo '<div id="selpart-root"></div>';
		echo $this->core_echomui('add_root').'</a><br/><div id="doptxt-root"></div>';
		$partids = array("root");
        $actpids = array();
		$blockon = 0;
		$previous_space = -1;
		foreach($infa as $item)
		{
			if($blockon && $blockon > $item['this_space']) $blockon = 0;
			if($blockon && $blockon != $item['this_space']) continue;
			if(!$checkacc || ($checkacc && in_array($item['com_id'], $parts)) || $nocheck)
			{
				$blockon = 0;
				$colstrings++;

				//echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$item['this_space']);
				if($previous_space>=0)
				{
					if($previous_space==$item['this_space']) echo '</li>';
					elseif($previous_space>=$item['this_space']) echo '</ul></li>';
					elseif($previous_space<=$item['this_space']) echo '<ul>';
				}
				echo '<li>';
				if(in_array($item['com_id'],$original_parts) || $nocheck)
				{
					$thispid = $item['id'];
					echo '<a href="" id="selpart-'.$item['id'].'" onclick="selpart(\''.$item['id'].'\',0,'.$item['id'].',\''.$item['url'].'\');return false;">';
					$partids[] = $item['id'];
					$actpids[$item['id']] = array("id"=>$item['id'],"space"=>$item['this_space'],"url"=>$item['url']);
					if(($_POST['pi-pid'] || $_GET['topid']) && ($item['id']==$_POST['pi-pid'] || $item['id']==$_GET['topid'])) $spaceforpreload = $item['this_space'];
				}
				echo $item['title'].'</a><br/><div id="doptxt-'.$item['id'].'"></div>';
				$previous_space = $item['this_space'];
			}else $blockon = $item['this_space'];
		}
		echo "</ul></div>";
	break;
	case '2':
		$parenturl = "http://".HTTP_HOST."/<span id='parenturl'>";
		if(intval($_POST['pi-pid']))
		{
			$sql = "SELECT `url` FROM `#__sitemap` WHERE `id`=".intval($_POST['pi-pid']);
			$pidurl = $this->fetch_assoc($this->query($sql));
			$parenturl .= $pidurl['url']."/";
		}
		$parenturl .= "</span>";
		$strings = array(
			array("title" => $this->core_echomui('form-title'), "input" => $this->adm_show_input("pititle", $_POST['pititle'],"","width:300px;","id='pagetitle' onkeyup=\"if(document.getElementById('sitetreepttl')) if(this.value) document.getElementById('sitetreepttl').innerHTML=this.value; else document.getElementById('sitetreepttl').innerHTML='".$this->core_echomui('add_here')."';\"")),
			array("title" => $this->core_echomui('form-url'), "input" => $parenturl.$this->adm_show_lidinput_original("piurl", $_POST['piurl'], "", "", "", "pititle")),
		);

		if($this->config['use_pathway'])
		{
			$include_in_pathway = intval($_POST['pi-include_in_pathway']);
			if(!isset($_POST['pititle'])) $checked = "checked";
			$strings[] = array("title" => "&nbsp;", "input"=>"&nbsp;");
			$strings[] = array("title" => "", "input"=>$this->core_echomui('title_pathway'));
			$strings[] = array("title" => $this->core_echomui('form-include_in_pathway'), "input"=>$this->adm_show_input('pi-include_in_pathway', "1",$include_in_pathway , '', $checked,"checkbox"));
			$strings[] = array("title" => $this->core_echomui('form-pathway'), "input" => $this->adm_show_input("pi-pathway", $_POST['pi-pathway'],"","width:300px;","id='pathwaytext'")."&nbsp;&laquo;&laquo;&nbsp;<a href='' onclick=\"document.getElementById('pathwaytext').value=document.getElementById('pagetitle').value;return false;\">".$this->core_echomui('textoflinkgettitle')."</a>","tooltip"=>$this->core_echomui('tooltip-pathway'));
		}

		$tdi=1;
		foreach($strings as $str)
		{
			echo "<div class='td_lable' id='s2td1".$tdi."'>".$str['title']."</div>";
			echo "<div class='td_cont' id='s2td2".$tdi."'>".$str['input']."</div>";
			echo "<div class='clear'></div>";
			$tdi++;
		}
	break;
	case '3':
		$strings = array(
			array("title" => "Title", "input" => $this->adm_show_input("pi-meta_title",$_POST['pi-meta_title'],"","width:500px;","id='metatitle'")."&nbsp;&laquo;&laquo;&nbsp;<a href='' onclick=\"document.getElementById('metatitle').value=document.getElementById('pagetitle').value;return false;\">".$this->core_echomui('textoflinkgettitle')."</a>"),
			array("title" => "Keywords", "input" => $this->adm_show_editor("pi-meta_keywords",$_POST['pi-meta_keywords'],"meta_keywords","50","100%")),
			array("title" => "Description", "input" => $this->adm_show_editor("pi-meta_description",$_POST['pi-meta_description'],"meta_description","50","100%")),
		);
		$tdi=1;
		foreach($strings as $str)
		{
			echo "<div class='td_lable' id='s3td1".$tdi."'>".$str['title']."</div>";
			echo "<div class='td_cont' id='s3td2".$tdi."'>".$str['input']."</div>";
			echo "<div class='clear'></div>";
			$tdi++;
		}
	break;

	case '4':
		$menus = $this->adm_get_select_array(6);
		$strings = array(
			array("title"=>$this->core_echomui('selectmenu'),"input"=>$this->adm_show_select("pi-menu", $_POST['pi-menu'], $menus, "", "id='menusel' onchange=\"showhidmenuset(this.value)\"","")),
			array("title"=>$this->core_echomui('selectparentpunkt'),"input"=>"&nbsp;"),
			array("title" => $this->core_echomui('textoflink'), "input" => $this->adm_show_input("pi-linktext",$_POST['pi-linktext'],"","width:300px;","id='linktext'")."&nbsp;&laquo;&laquo;&nbsp;<a href='' onclick=\"document.getElementById('linktext').value=document.getElementById('pagetitle').value;return false;\">".$this->core_echomui('textoflinkgettitle')."</a>"),
			array("title" => $this->core_echomui('linksort'), "input" => $this->adm_show_input("pi-linksort",$_POST['pi-linksort'],"","")),
			array("title"=>$this->core_echomui('openlinkin'),"input"=>$this->adm_show_select("pi-openlinkin", $_POST['pi-openlinkin'], array("_self"=>$this->core_echomui('openlinkin_self'),"_blank"=>$this->core_echomui('openlinkin_blank')), "", "","")),
		);
		
		$tdi=1;
		foreach($strings as $str)
		{
			echo "<div class='td_lable' id='s4td1".$tdi."'>".$str['title']."</div>";
			echo "<div class='td_cont' id='s4td2".$tdi."'>".$str['input']."</div>";
			echo "<div class='clear'></div>";
			$tdi++;
		}
		
	break;
	case '5':
		$edit_file = DOCUMENT_ROOT."/".$this->adm_path."/_pages/".$row['adm_title']."/edit.php";
		if(file_exists($edit_file))
		{
			$this->core_readmuifile(DOCUMENT_ROOT."/".$this->adm_path."/_pages/".$row['adm_title']."/edit/lang/".$this->muiparam().".txt");
			$com_id = $row['id'];
			$this->nocloseform = 1;
			if(intval($_POST['record_id'])) $this->id = intval($_POST['record_id']);
			else $page_row = $_POST;
			$this->go_addpage_cont = 'y';
			require($edit_file);
		}
	break;
}
echo '</div>';
}
echo '</div>';
?>
<script language="JavaScript">

function selpart(part,space,pid,url)
{
	if(!part) return false;
	if(!document.getElementById('selpart-'+part)) return false;

	if(document.getElementById('pagetitle') && document.getElementById('pagetitle').value) setvalue = document.getElementById('pagetitle').value;
	else setvalue = '<?=$this->core_echomui('add_here')?>';

	<?foreach($partids as $pid){?>document.getElementById('selpart-<?=$pid?>').className='normal';document.getElementById('doptxt-<?=$pid?>').innerHTML = '';<?}?>
	document.getElementById('selpart-'+part).className='boldblack';
	document.getElementById('doptxt-'+part).innerHTML = '<div style="float:left;"><div class="" style="width:15px;height:12px;float:left;overflow:hidden;"></div></div><div style="color:#aaa;float:left;border:dashed 1px #ccc;padding:3px;" id="sitetreepttl">'+setvalue+'</div><div style="clear:both;"></div>';
	document.getElementById('sel-pid').value=pid;
	if(document.getElementById('parenturl'))
	{
		if(url) document.getElementById('parenturl').innerHTML = url+'/';
		else document.getElementById('parenturl').innerHTML = '';
	}
}
function showhidmenuset(val)
{
	if(val==0)
	{
		document.getElementById('s4td12').style.display='none';
		document.getElementById('s4td22').style.display='none';
		document.getElementById('s4td13').style.display='none';
		document.getElementById('s4td23').style.display='none';
		document.getElementById('s4td14').style.display='none';
		document.getElementById('s4td24').style.display='none';
		document.getElementById('s4td15').style.display='none';
		document.getElementById('s4td25').style.display='none';
	}else
	{
		document.getElementById('s4td12').style.display='block';
		document.getElementById('s4td22').style.display='block';
		document.getElementById('s4td13').style.display='block';
		document.getElementById('s4td23').style.display='block';
		document.getElementById('s4td14').style.display='block';
		document.getElementById('s4td24').style.display='block';
		document.getElementById('s4td15').style.display='block';
		document.getElementById('s4td25').style.display='block';

		loadXMLDoc('/ajax-index.php?isadm=1&page=menupunkti&mid='+val+'&ppm=<?=$_POST['pi-parpunktmenu']?><?echo $ACTION=='edit'?'&cid='.$_POST['pi-menupunktid']:'';?>','s4td22');
	}
}
function str_repeat(string, num)
{
	var str = '';
	if(!num) num = 1;
	for(i=1;i<=num;i++) {str = str+''+string;}
	return str;
}
<?
$this->jsonload[] = 'pninit()';
?>
function pninit()
{
	document.getElementById('toolbar').style.position='relative';
	document.getElementById('toolbar').style.border='none';
	<?if(isset($_POST['pi-pid'])){?>selpart('<?echo $_POST['pi-pid']?$_POST['pi-pid']:'root'?>',0,'<?=$_POST['pi-pid']?>','<?=$actpids[$_POST['pi-pid']]['url']?>');<?}else if(isset($_GET['topid'])){?>selpart('<?echo $_GET['topid']?$_GET['topid']:'root'?>',0,'<?=$_GET['topid']?>');<?}else
	if(sizeof($actpids)==1){$actpids = end($actpids);?>selpart('<?echo $actpids['id']?$actpids['id']:'root'?>',0,'<?=$actpids['id']?>','<?=$actpids['url']?>');<?}?>
	if(document.getElementById('menusel')) showhidmenuset(document.getElementById('menusel').value);
}

function stepexpand(stepid)
{
	if(!document.getElementById('titstep'+stepid) || !document.getElementById('titeximg'+stepid) || !document.getElementById('contstep'+stepid)) return false;

	if(document.getElementById('titstep'+stepid).className=='addconsteptitle')
	{
		document.getElementById('titstep'+stepid).className='addconsteptitle_nsh';
		document.getElementById('contstep'+stepid).style.display='none';
		document.getElementById('titeximg'+stepid).src = '/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/expander_plus.gif';
		setCookie('pcnfstep'+stepid,'0',3600,'/');
	}else
	{
		document.getElementById('titstep'+stepid).className='addconsteptitle';
		document.getElementById('contstep'+stepid).style.display='block';
		document.getElementById('titeximg'+stepid).src = '/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/expander_minus.gif';
		setCookie('pcnfstep'+stepid,'1',3600,'/');
	}
}
function switchbg(act, obj)
{
	if(!obj) return false;
	if(act=='over') obj.style.backgroundColor = '#efefef';
	else {
		if(obj.className=='addconsteptitle') obj.style.backgroundColor = '#efefef';
		else obj.style.backgroundColor = '#fafafa';
	}
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

</script><script> 
$(function() {
	step = location.hash.split('step');
	step = parseInt(step[1])
	if(!step && location.hash!='step'+step){<?if($page['id'] && (sizeof($allowsteps)==4||sizeof($allowsteps)==5)){echo "step = ".(sizeof($allowsteps)-1).";";}?>}

	$( "#accordion" ).accordion({autoHeight: false,navigation: false, active: step});
});

$(function() {
	$("#tree").treeview({
		collapsed: true,
		animated: "medium",
		unique: true,
	});
	TreeView1.CollapseAll();
})
</script> 