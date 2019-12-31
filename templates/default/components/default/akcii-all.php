<?//echo "<pre>";print_r($this->page_info['sub_pages']);echo "</pre>";?>
<?
$num = 0;
$colperstr = 2;
$totalnum = sizeof($this->page_info['sub_pages']);

foreach($this->page_info['sub_pages'] as $i=>$info){
	if(!$num)
	{
		echo '<div class="row"><ul class="list">';
	}

?>
<li style="width:750px;float:left;margin-bottom:10px;">
	<div class="item-holder" style="float:left;overflow:hidden;">
		<div style="float:left;margin-right:10px;">
		<a href="/<?=$info['url']?>"><img src="<?=$info['info']['mainimg'][0]['url']?>?w=200" width="200" alt="<?=$info['info']['name']?>" /></a>
		</div>
		<div class="description" style="background:none;float:left;">
			<div class="holder" style="background:none;">
				<div class="frame" style="width:515px;text-align:left;">
					<strong class="title"><a href="/<?=$info['url']?>" style="font-size:20px; text-decoration: none;"><?=$info['info']['name']?>&nbsp;&raquo;</a></strong>
					<div><?=$info['info']['anons']?></div>
					<a href="/<?=$info['url']?>">подробнее&nbsp;&raquo;</a>
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
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