<?
$pageId = intval($_GET['page_id']);
if($pageId){
	$sql = "SELECT `txtUnderAjax` FROM `#__services` WHERE `id`=".$pageId;
	$text = $this->fetch_assoc($this->query($sql));
	echo $text['txtUnderAjax'];
}
?>