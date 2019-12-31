<div class="addpartitem">
<?$sql = "SELECT * FROM `#h_components` WHERE `add_button`='razdel' && `enabled`='1'";
$res = $this->query($sql);
$row = array();
while($roww = $this->fetch_assoc($res)) $row[] = $roww;
$this->can_add=1;

echo sizeof($row)?'<div class="title">'.$this->core_echomui('tpl_addpart').'</div>':"";
foreach($row as $item){
	if($item['limit'])
	{
		$sql = "SELECT `id` FROM `#__sitemap` WHERE `com_id`=".intval($item['id']);
		$used = $this->num_rows($this->query($sql));		
		if($used>=$item['limit']) continue;
	}
	echo "<div>";
	$this->adm_show_add_button($this->core_echomui('add_button_'.$item['id']),"","h","pageconfig/?add&com=".$item['id']);
	echo "</div>";
}
?>
</div>
<div class="addpartitem">
<div class="title">&nbsp;</div>
<?$sql = "SELECT * FROM `#h_components` WHERE `add_button`='content' && `enabled`='1'";
$res = $this->query($sql);
$row = array();
while($roww = $this->fetch_assoc($res)) $row[] = $roww;

foreach($row as $item){
	$this->adm_get_rights($item['adm_title']);
	echo "<div>";
	$this->adm_show_add_button($this->core_echomui('add_button_'.$item['id']),"","h","pageconfig/?add&com=".$item['id']);
	echo "</div>";
}
?>
</div>