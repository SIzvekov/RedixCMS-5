<?
$query = str_replace("pid=".$_GET['pid'],"",$_SERVER['QUERY_STRING']);
$query = preg_replace("/^\&/","",$query);
$query = preg_replace("/\&$/","",$query);

$comid = intval($_GET['comid']);
if($comid)
{
	$this->adm_get_com_config($comid); // <- получили конфиг компонента из таблицы #__components

	$extra = $this->adm_com_config['config']['sort_sqlextra'];
	foreach($_GET as $k=>$v)
	{
		if(preg_match("/^\//i",$v) && $k!='ref') $thisisimgdir = $v;
		$v = "'".addslashes($v)."'";
		$extra = preg_replace("/\{$k\}/iU", "$v", $extra);
	}

	// this condition added in order to work properly when the window was open from <photos_set> file
	if(intval($_GET['phid'])) $extra .= " && `pid`=".intval($_GET['phid']);
	/////////////////////////////////////////////////////////////////////////////////////////////////

	$DB_TBL_NAME = '#'.$this->adm_com_config['config']['tbl'];
	$arrk_title = $this->adm_com_config['config']['sort_fieldtitle'];
	$EXTRA_COND = ' '.$extra;
	$blocksstyle = ($this->adm_com_config['config']['sort_blocksstyle']=='{images}'?'float:left;width:100px;overflow:hidden;margin-right:5px;min-height:100px;':$this->adm_com_config['config']['sort_blocksstyle']);
	$isimgs = intval($this->adm_com_config['config']['sort_blocksstyle']=='{images}');
	if($isimgs)
	{
		$this->fmgr_readusernames(DOCUMENT_ROOT.$thisisimgdir.".usernames_files",'file');
	}
}else{
switch($_GET['part'])
{
	default:
		$DB_TBL_NAME = '#__sitemap';
		$arrk_title = 'title';
		$EXTRA_COND = '';
		$blocksstyle = '';
}
}
//
if($_POST['ids'])
{
	$ids = split(",",$_POST['ids']);
	$sort = 0;
	foreach($ids as $id)
	{
		$sql = "UPDATE `".$DB_TBL_NAME."` SET `sort`=".$sort." WHERE `id`=".intval($id);
		$this->query($sql);
		$sort++;
	}
	$this->reload($_POST['ref']);
}
?>
<script type="text/javascript"> 
$(function() {
	$("#sortable").sortable({
	placeholder: 'ui-state-highlight',
	cursor: 'move',
//	revert: true,
	scrollSensitivity: 40
	});
	$("#sortable").disableSelection();
});

function changesort() {
	var result = $('#sortable').sortable('toArray');
	document.getElementById('ids').value=result;
	document.getElementById('sortform').submit();
}
</script>
<style>
.ui-state-highlight {<?echo $blocksstyle?>}
</style>
<?
$pid = intval($_GET['pid']);
 
$pidcond = ($this->adm_com_config['config']['sort_notree']?'1':"`pid`=".$pid);

$sql = "SELECT * FROM `".$DB_TBL_NAME."` WHERE ".$pidcond." ".$EXTRA_COND." ORDER BY `sort` ASC";
$res = $this->query($sql);
?>
<div class="toolbar">
<?if($pid){
	$sql = "SELECT * FROM `".$DB_TBL_NAME."` WHERE `id`=".$pid."";;
	$res1 = $this->query($sql);
	$parentpage = $this->fetch_assoc($res1)
	?>
 <div class="div_appbutton" style="font-size:14px;margin-top:4px;margin-right:10px;">
	<?=$this->core_echomui('sort_by_parent_page')?> <b><?=$parentpage[$arrk_title]?></b> (<span class="sortsublink"><a href="?pid=<?=$parentpage['pid']?>&<?=$query?>"><?=$this->core_echomui('go_up')?></a>)
 </div>
<?}?>
 <div class="div_appbutton">
  <input type="button" name="" value="<?=$this->core_echomui('save')?>" class="appbutton" onmouseover="this.className='appbutton_h'" onmouseout="this.className='appbutton'" onclick="changesort()">
<?if($_GET['ref']){?><input type="button" name="" value="<?=$this->core_echomui('cancel')?>" class="cancelbutton" onmouseover="this.className='cancelbutton_h'" onmouseout="this.className='cancelbutton'" onclick="location.href='<?=$_GET['ref']?>'"><?}?>
 </div>
</div>
<div id="sortable" style="margin-top:40px;overflow:hidden;">
<?while($row = $this->fetch_assoc($res)){
	$sql = "SELECT `id` FROM `".$DB_TBL_NAME."` WHERE `pid`=".$row['id']." ".$EXTRA_COND." ORDER BY `sort` ASC";
	$subnum = intval($this->num_rows($this->query($sql)));
	?>
	<div style="<?echo $blocksstyle?><?if($isimgs){?>;height:100px;font-size:10px;text-align:center;<?}?>" class="ui-state-default"<?echo $row['public']?'':' style="color:#ccc;"'?> id="<?=$row['id']?>">
	 <?if($isimgs){?><div style="border:0px;widht:100px;height:85px;padding:0px;margin:0 0 3px 0;overflow:hidden;"><img src="/showimg.php?<?=$thisisimgdir?><?=$row[$arrk_title]?>&w=100" alt="" /></div><?}?>
	 <?echo $row['public']&&$row['url']?'<a href="/'.$row['url'].'/" target="_blank" style="text-decoration:none;color:#000;">':''?><?=$isimgs&&$this->fmgr_filenames[$row[$arrk_title]]?$this->fmgr_filenames[$row[$arrk_title]]:$row[$arrk_title];?><?echo $row['public']?'</a>':' ('.$this->core_echomui('no_published').')'?><?echo $subnum&&!$this->adm_com_config['config']['sort_notree']?' <span class="sortsublink"><a href="?pid='.$row['id'].'&'.$query.'">'.$this->core_echomui('sort_sub_pages').' ('.$subnum.')</a></span>':'';?>
	</div> 
<?}?>
</div>
<div style="display:none">
 <form method="post" id="sortform">
  <input name="pid" value="<?=$pid?>">
  <textarea name="ids" id="ids"></textarea>
 </form>
</div>