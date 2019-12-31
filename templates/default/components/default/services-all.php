<?//echo "<pre>";print_r($this->page_info['sub_pages']);echo "</pre>";?>
<?
$num = 0;
$colperstr = 3;
$totalnum = sizeof($this->page_info['sub_pages']);

foreach($this->page_info['sub_pages'] as $i=>$info){
	if(!$num)
	{
		echo '<div class="row"><ul class="list services-list">';
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
	<div class="item-holder" style="width:220px;">
		<a href="/<?=$info['url']?>"><img src="<?=$info['info']['mainimg'][0]['url']?>?w=220" width="220" height="255" alt="<?=$info['info']['name']?>" /><div class="bord"></div></a>
		<div class="description" style="background:none;">
			<div class="holder">
				<div class="frame">
					<strong class="title"><a href="/<?=$info['url']?>"><?=$info['info']['nema']?></a></strong>
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
}?>