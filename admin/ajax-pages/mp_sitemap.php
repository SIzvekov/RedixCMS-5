<?
$delete = intval($_GET['delete']);
if($delete)
{
        $this->adm_delfromsitemap($delete);
		$_RESULT['reloadblocks'] = 1;
}

$move = intval($_GET['move']);
$dir = $_GET['dir'];
if($move)
{
        $sql = "SELECT `pid` FROM `#__sitemap` WHERE `id`=".$move."";
        $res = $this->query($sql);
        $row = $this->fetch_assoc($res);

        $sql = "SELECT `id`,`sort` FROM `#__sitemap` WHERE `pid`=".intval($row['pid'])." ORDER BY `sort` ASC";
        $res = $this->query($sql);
        $sort = 0;
        $strings = array();

        while($row_all = $this->fetch_assoc($res))
        {
                $sort++;
                $sql = "UPDATE `#__sitemap` SET `sort`=".intval($sort)." WHERE `id`=".intval($row_all['id'])."";
                $this->query($sql);
                $strings[$sort] = $row_all['id'];
                if($row_all['id']==$move)
                {
                        $cursort = $sort;
                }
        }
        if($dir=="up" && $cursort!=1)
        {
                $sql = "UPDATE `#__sitemap` SET `sort`=".intval($cursort)." WHERE `id`=".intval($strings[$cursort-1])."";
                $res = $this->query($sql);
                $sql = "UPDATE `#__sitemap` SET `sort`=".intval($cursort-1)." WHERE `id`=".intval($move)."";
                $res = $this->query($sql);
        }
        if($dir=="down" && $cursort!=$sort)
        {
                $sql = "UPDATE `#__sitemap` SET `sort`=".intval($cursort)." WHERE `id`=".intval($strings[$cursort+1])."";
                $res = $this->query($sql);
                $sql = "UPDATE `#__sitemap` SET `sort`=".intval($cursort+1)." WHERE `id`=".intval($move)."";
                $res = $this->query($sql);
        }
}



$ar = $this->mysql_get_fields("#__sitemap");
foreach($ar as $k=>$v) $selfils[$k] = trim($v['Field']);

$subitempid = intval($_GET['subitempid']);
$startspace = intval($_GET['startspace']);

$sql = "SELECT * FROM `#__sitemap` WHERE `pid`=".$subitempid." && `type`='page' ORDER BY `sort` ASC";
$infa = $this->core_get_tree($sql);
$infa = $this->core_get_tree_keys($subitempid, $selfils,$infa, $startspace, 1);

$showitems=array();
foreach($_COOKIE['admmpsub'] as $item=>$val)
{
	$item = str_replace("item","",$item);
	if(intval($val))
	{
		$showitems[]=$item;
	}
}


$adm_files = array();
$comtits = array();
$i=0;

foreach($infa as $item){
      $i++;
        $sql = "SELECT `adm_title`, `man_title`, `nodelete` FROM `#h_components` WHERE `id`=".intval($item['com_id'])."";
        $row = $this->fetch_assoc($this->query($sql));
        $adm_files[$item['template']] = $row['adm_title'];
        $comtits[$item['template']] = $row['man_title'];

		$pubind = '';//($item['public']?'':0);
        ?>
<div class="mpline<?=$pubind?>" onmouseover="this.className='mpline_hover<?=$pubind?>'" onmouseout="this.className='mpline<?=$pubind?>'" style="padding-left:<?=$item['this_space']*20?>px;">
<div class="smapplusminus">
<?if($this->num_rows($this->query("SELECT `id` FROM `#__sitemap` WHERE `type`='page' && `pid`=".intval($item['id'])))){?>
<a href="" onclick="showsubitems(<?=$item['id']?>,<?=($item['this_space']+1)?>,this);return false;"><img src='/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/expander_<?echo in_array($item['id'],$showitems)?"minus":"plus"?>.gif' alt='<?echo in_array($item['id'],$showitems)?"-":"+"?>'/></a>
<?$estsubitems = 1;}else{$estsubitems = 0;?><img src='/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/blank.gif'/><?}?></div>
<div class="smapline" id="smapline-<?=$item['id']?>">


<?
if($item['com_id'])
	{
		$sql = "SELECT `id`,`adm_title`,`config` FROM `#h_components` WHERE `parent_parts`=".intval($item['com_id']);
		$rrr = $this->query($sql);
		$com_param = array();
		$numsubcontent = 0;
		$numsubcontent_up = 0;
		$con_edit_info = array();
		while($com_row = $this->fetch_assoc($rrr))
		{
			$com_param = $this->adm_get_param($com_row['config']);
			if($com_param['tbl'])
			{
				$sql = "SELECT `id` FROM `#".$com_param['tbl']."` WHERE `pid`=".$item['id'];
				$com_con = $this->query($sql);
				$numsubcontent = $this->num_rows($com_con);
				$numsubcontent_up = $this->num_rows($this->query($sql." && `public`='0'"));

				$sql = "SELECT `adm_title` FROM `#h_components` WHERE `add_button`='content' && `id`=".$this->get_com_contplbyid($item['com_id']);
				$con_edit_info = $this->fetch_assoc($this->query($sql));
			}
		}
	}	

	/*
	return hs.htmlExpand(this, { objectType: \'iframe\',headingText: \'\', contentId: \'highslide-html-8\' } )
	*/
?>
		<span class="pagetitle"><?echo $item['tplfile']?'<a href="/'.($this->config['use_param']?$this->param.'/':'').$item['url'].'/" title="'.$this->core_echomui('tpl_openpageinnw').'" target="_blank">':''?><?=$item['title']?><?echo $item['url']?'</a>':''?></span><?echo $con_edit_info['adm_title']?' <span class="numcont1">(<font color="#f00">'.$numsubcontent_up.'</font>/'.$numsubcontent.')</span><span class="numcont2"><a href="/'.$this->adm_path.'/'.$con_edit_info['adm_title'].'/?pid='.$item['id'].'" onclick="return showdialog(this.href,\''.$item['title'].'\');">('.$this->core_echomui('content_inside').' '.$numsubcontent_up.'/'.$numsubcontent.')</a></span>':''?>
		<?if($comtits[$item['template']]){?><span class="pagetypeinfo">| <?=$this->core_echomui('tpl_pagetype')?>: <?=$comtits[$item['template']]?></span><?}?>
		<div class="clear"></div>

	<div class="smtoolbar">
	<?if($item['tplfile']){?>
         <?// PUBLIC?>
        <?echo "<a href=\"/".$this->adm_path."/".$this->way."/?public=".$item['id']."\" onclick=\"loadXMLDoc('/ajax-index.php?isadm=1&page=switch&id=".$item['id']."&dbtable=__sitemap&field=public');return false;\"><img src=\"/".$this->adm_path."/template/".$this->config['adm_tpl']."/img/ticks/tick_".$item['public'].".png\" border=\"0\" id=\"sw__sitemappublic-".$item['id']."\" title=\"".$this->core_echomui('tpl_pagepub'.$item['public'])."\" alt=\"".$this->core_echomui('tpl_pagepub'.$item['public'])."\" style=\"margin:-2px;\"></a>"?>
<?}?>
		
		<?// EDIT?>
        <a href="/<?=$this->adm_path?>/pageconfig/?id=<?=$item['id']?>"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/edit10px.png" alt="<?=$this->core_echomui('tpl_editpage')?>" title="<?=$this->core_echomui('tpl_editpage')?>" style="margin:-2px;"/> <?=$this->core_echomui('editbutton')?></a>

		<?// DELETE?><?if(!$row['nodelete']){?>
        <a href="" onclick="if(confirm('<?=$this->core_echomui('adm_itemdelquestion')?>')){deletepagefromtree(<?=$item['id']?>);} return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/del.png" alt="<?=$this->core_echomui('tpl_delpage')?>" title="<?=$this->core_echomui('tpl_delpage')?>" style="margin:-2px;"/></a>
		<?}?>
		<?// MOVE DOWN?>
        <?if(isset($infa[$i]['this_space']) && $infa[$i]['this_space']==$item['this_space']){?>
        <a href="" onclick="movepage(<?=$item['id']?>,'down');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/arr_down.png" alt="<?=$this->core_echomui('tpl_movepagedown')?>" title="<?=$this->core_echomui('tpl_movepagedown')?>" style="margin:-2px;"/></a>
        <?}?>
		<?// MOVE UP?>
        <?if(isset($infa[$i-2]['this_space']) && $infa[$i-2]['this_space']==$item['this_space']){?>
        <a href="" onclick="movepage(<?=$item['id']?>,'up');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/tree_edit/arr_up.png" alt="<?=$this->core_echomui('tpl_movepageup')?>" title="<?=$this->core_echomui('tpl_movepageup')?>" style="margin:-2px;"/></a>
        <?}?>
	</div>
</div>

</div>
<div class="clear"></div>
</div>

<?if($estsubitems){
	if(in_array($item['id'],$showitems)) $_RESULT['mpgoload'][] = $item['id'].':'.($item['this_space']+1);
	?>
<div id="subitems-<?=$item['id']?>" style="display:none">loading...</div>
<?}?>
<?}?>