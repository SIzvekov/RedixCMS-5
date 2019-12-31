<?//echo "<pre>";print_r($this->page_info['sub_pages']);echo "</pre>";?>
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
		<a href="<?=$img['imgurl']?>" target="_blank" rel="shadowbox[qgal]" title="<?=$img['title']?>"><img src="/showimg.php?<?=$img['imgurl']?>&w=340" width="340" height="255"/></a>
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
if($num) echo "</ul></div>";

if(sizeof($this->page_info['sub_pages']))
{

	$num = 0;
	$colperstr = 2;
	$totalnum = sizeof($this->page_info['sub_pages']);

	foreach($this->page_info['sub_pages'] as $i=>$info){
		if(!$num)
		{
			echo '<div class="row"><ul class="list">';
		}

?>
<li>
<?
	$onsale = sizeof($this->listofakcii($info['info']['id'],"services"));
	if($onsale)
	{
		$cl = '';
		if($i==($totalnum-1) && $num!=$colperstr) $cl = 'one';
		echo '<div class="onsale'.$cl.'"></div>';
	}
	?>
	<div class="item-holder" style="width:350px;">
		<a href="/<?=$info['url']?>/"><img src="/showimg.php?<?=$info['info']['mainimg'][0]['url']?>&w=340" width="340" height="255" alt="<?=$info['info']['name']?>" /></a>
		<div class="description" style="background:none;">
			<div class="holder">
				<div class="frame">
					<strong class="title"><a href="/<?=$info['url']?>/"><?=$info['info']['nema']?></a></strong>
				</div>
			</div>
		</div>
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

}

echo $this->core_modul('assocakcii','services');
?>
<div><a href="../">&laquo;&nbsp;Все услуги</a></div>
