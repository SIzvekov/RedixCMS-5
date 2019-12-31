<?/*echo "<pre>";print_r($this->page_info);echo "</pre>"*/;?>

<?php 
//List of pages' ids where images appear above the text on page
$pages = array('27');
//check if we should display images first
echo in_array($this->page_info['id'], $pages)? '' : $this->page_info['info']['text'];

echo in_array($this->page_info['id'], $pages)? " <div id='page_text' class='simpletxt' > ".$this->page_info['info']['text']."</div>" : '';

//Display images
$num = 0;
$colperstr = 2;
$totalnum = sizeof($this->page_info['info']['images']);

foreach($this->page_info['info']['images'] as $img){
	if(!$num)
	{
		echo '<div class="row centeredrow"><ul class="list centeredlist">';
	}

?>
<li>
	<div class="item-holder services">
		<a href="<?=$img['imgurl']?>?w=900" target="_blank" rel="shadowbox[qgal]" title="<?=$img['title']?>"><img src="<?=$img['imgurl']?>?w=340" width="340" height="255"/></a>
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
		<a href="/<?=$info['url']?>"><img src="<?=$info['info']['mainimg'][0]['url']?>?w=340" width="340" height="255" alt="<?=$info['info']['name']?>" /></a>
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
}

}

echo $this->core_modul('assocakcii','services');
//End display images

//Сheck if we should display text now
?>

<div class="seo-zone" style="" id="seo-zone"></div>
<script>

// var area_h = $(".area").height();
// var page_text_h = $("#page_text").height();
// $(".seo-zone").height( page_text_h );

</script>

<?php $url = $_SERVER['REQUEST_URI'];
$url = strpos($url, '/', strlen($url) - 1) !== false ? substr($url, 0, strlen($url) - 1) : $url;
$parts = explode('/', $url);
array_pop($parts);
if($parts[count($parts) - 1] == 'ukrasheniya') {
	$parts = array('', 'praisi', 'ukrasheniya');
}
$url = implode('/', $parts); 
?>
<!--<?php echo $url ?>-->
<div><a href="/praisi">&laquo;&nbsp;Все услуги</a></div>
