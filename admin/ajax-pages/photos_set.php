<?
//print_r($_GET);
$tbl = $_GET['tbl'];
$tid = intval($_GET['tid']);
$com_id = intval($_GET['com_id']);
$filepath = trim($_GET['tofolder']);

$this->fmgr_readusernames(DOCUMENT_ROOT.'/'.$filepath."/.usernames_files",'file');

if($_GET['delfile'])
{
	$item = trim($_GET['delfile']);
	unset($this->fmgr_filenames[$item]);

	$this->adm_delete_file(DOCUMENT_ROOT.'/'.$filepath."/".$item, 1);
	
	$f = fopen($filepath."/.usernames_files",'w');
		foreach($this->fmgr_filenames as $k=>$v) fwrite($f,$k."=".$v."\n");
	fclose($f);

	$sql = "DELETE FROM `#".addslashes($tbl)."` WHERE `pid`=".$tid." && `img`='".addslashes($item)."'";
	$this->query($sql);
}

$sql = "SELECT * FROM `#".addslashes($tbl)."` WHERE `pid`=".$tid." ORDER BY `sort` ASC";
$res = $this->query($sql);


/* fields */
$sql = "SELECT `id` FROM `#h_components_listedittable` WHERE `com_id`=".$com_id." && `public`='1' && `pid`=0 ORDER BY `sort` ASC";
$bmid = $this->fetch_assoc($this->query($sql));
$bmid = intval($bmid['id']);

$sql = "SELECT * FROM `#h_components_listedittable` WHERE `com_id`=".$com_id." && `public`='1' && `pid`=".$bmid." ORDER BY `sort` ASC";
$fields_res = $this->query($sql);
$foto_fields = array();
while($fields = $this->fetch_assoc($fields_res))
{
	$fields['params'] = $this->adm_get_param($fields['params']);
	$foto_fields[] = $fields;
}
/************/

?><?=$this->adm_open_edit_form()?><div class="ph_set_allphoto"><table class="ph_set_imgitem"><?
$i=0;	
while($row = $this->fetch_assoc($res))
{
	if(!is_file(DOCUMENT_ROOT.'/'.$filepath."/".$row['img'])) continue;
?>
<tr><td class="imgtd">
<a href="/<?=$filepath?>/<?=$row['img']?>" target="_blank"><div class="ph_set_img"><img src="/showimg.php?/<?=$filepath?>/<?=$row['img']?>&w=150" width="150" alt="<?=$this->fmgr_filenames[$row['img']]?>"/><div class="ph_set_imgname"><?=$this->fmgr_filenames[$row['img']]?></div></div></a>
</td><td>
<div class="ph_set_imgedit">
<a href='' onclick="if(confirm('<?=$this->core_echomui('ph_set_delimgconfirm');?>')) reloadlist('delfile', '<?=$row['img']?>');return false;"><img src="/<?=$this->adm_path?>/template/<?=$this->config['adm_tpl']?>/img/delete_1.gif" border=0 alt="delete"></a>
<br/>
<input type="hidden" name="ph_id[<?=$i?>]" value="<?=$row['id']?>" />
<?
foreach($foto_fields as $field)
	{
		$row[$field['db_fname'].'['.$i.']'] = $row[$field['db_fname']];
		$field['db_fname'] = $field['db_fname'].'['.$i.']';
	//	print_r($field);

		echo '<div style="overflow:hidden;margin-bottom:5px;">';
		echo '<div class="fieldlabel">'.$this->core_echomui($field['mui_title']).':</div> ';
		echo '<div style="">'.$this->get_right_field($field, $row, $field['params']).'</div>';
		echo '</div>';
	}?>
</div>
</td>
</tr>
<?
$i++;
$wasimg = 1;}if(!$wasimg) die($this->core_echomui('ph_set_noimg'));
?></table></div>
<input type="hidden" name="colstr" value="<?=$i?>" />
<br/>
<input type="submit" value="<?=$this->core_echomui('admc_button_save')?>" />
<?echo "<input type=\"button\" value=\"".$this->core_echomui('go_sort_title')."\" onclick=\"if(confirm('".$this->core_echomui('proceedsort')."')){return showdialog('/".$this->adm_path."/sort_items/?imgdir=/".$filepath."/&phid=".$tid."&comid=".$com_id."','".$this->core_echomui('go_sort_diti')."','','','','reloadlist');}\" />"?>
<?=$this->adm_close_edit_form();?>