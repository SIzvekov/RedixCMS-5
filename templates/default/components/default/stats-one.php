<?//echo "<pre>";print_r($this->page_info);echo "</pre>";?>
<?=$this->page_info['info']['text']?>
<?
$num = 0;
$colperstr = 2;
$totalnum = sizeof($this->page_info['info']['images']);
foreach($this->page_info['info']['images'] as $img){
	if(!$num)
	{
		echo '<div class="row"><ul class="list">';
	}

?>
<li>
	<div class="item-holder" style="width:350px;">
		<a href="<?=$img['imgurl']?>" target="_blank" rel="shadowbox[qgal]"><img src="<?=$img['imgurl']?>?w=340" width="340" height="255" /></a>
	</div>
</li>
<?
	$num++;
	if($num==$colperstr || $i==($totalnum-1))
	{
		echo "</ul></div>";
		$num = 0;
	}
}
?>

<div><a href="/stats">&laquo;&nbsp;Все статьи</a></div>