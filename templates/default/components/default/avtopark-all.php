<?echo $this->core_modul('text','avttext2')?>
<div id="seo-zone-def-inner"  class="simpletxt" ><?echo $this->core_modul('text','avttext')?></div>
<div class="row"><ul class="list autopark-list">
<?
if($_SERVER['REMOTE_ADDR'] == '37.235.178.180'){
//echo "<pre>";print_r($this->page_info['sub_pages']);echo "</pre>";
}
$num = 0;
$colperstr = 2;
$totalnum = sizeof($this->page_info['sub_pages']);
$j=0;

foreach($this->page_info['sub_pages'] as $i=>$info){
	// echo '<pre>';print_r($info['info']);echo '</pre>';

    $priceArray = array(
        $info['info']['normal_hour'], 
        $info['info']['normal_two_hours'], 
        $info['info']['normal_other_hours'], 
        $info['info']['wedding_hour'], 
        $info['info']['wedding_two_hours'], 
        $info['info']['wedding_other_hours'], 
        $info['info']['wedding_more_hours'], 
        $info['info']['special_price']
    );

    foreach($priceArray as $k=>$v){
        if(!intval($v)) unset($priceArray[$k]);
    }

	$min_price = min($priceArray);
?>

        <li class="bigblock">
            <div class="border-inside">
            <div class="item-holder">
                <a href="/<?=$info['url']?>" class="atitle">
                <div class="auto_title_cont">
                    <div class="auto_title"><?=$info['info']['name']?></div>
                    <div class="place"><span>МЕСТ:</span><?=$info['info']['name_seats']?></div> <!-- place_left -->
                    <div class="place"><span>ЦВЕТ:</span><span class="color"><?=$info['info']['name_color']?></span></div>
                    <div class="price"><span class="prefix">от</span> <span class="price_count"><?=$min_price?> </span><span class="rur">Р</span></div>
                </div>
                </a>
                <div class="apply">
                        <a class="order_container" href="#orderModal" role="button" data-toggle="modal" data-lim_id="<?=$info['info']['name']?>, <?=$info['info']['name_seats']?> мест, <?=$info['info']['name_color']?>"
                           onclick="getLimId(this);return false">
                            <div class="order"></div>
                        </a>
                    </div>
                <div class="images">
                <a href="/<?=$info['url']?>" class="bigimg">
                    <div class="img">
	                    <img src="<?=$info['info']['mainimg'][0]['url']?>?w=340" width="340" height="255" alt="<?=$info['info']['name']?>" />
	                    <div class="bord"></div>
                    </div>
                    <?
                    $onsale = sizeof($this->listofakcii($info['info']['id'],"cars"));
                    if($onsale)
                    {
                            $cl = '';
                            // if($i==($totalnum-1) && $num!=$colperstr) $cl = 'one';
                            echo '<div class="onsale'.$cl.'"></div>';
                    }
                    ?>
                </a>
                <ul class="icons">
                <?
                $sub_images = array_slice($info['info']['images'], 0, 8);
                foreach($sub_images as $img){?>
                    <li>
                    	<span class="#">
                    	<div class="img subimg">
		                    <a href="<?=$img['imgurl']?>?w=900" target="_blank" class="subimg" rel="shadowbox[qgal<?=$info['id']?>]" title="<?=$img['title']?>"><img src="<?=$img['imgurl']?>?w=100" alt="<?=$img['title']?>"/></a>
	                    </div>
                    	</span>
                    </li>
				<?}?>
                </ul>
                </div>
                <div class="rates">
	                <?
	                preg_match_all("/.*(<table.*>.*<\/table>).*/isU", $info['info']['text'], $text);
	                $text = $text[1][0];
	                $text = preg_replace("/<table.*>(.*)/iU", "<table>\\1", $text);
	                // echo '<pre>';print_r($text);echo '</pre>';
	                echo $text;
	                ?>
                </div>
            </div>
            </div>
        </li>


<?
	$j++;
	$num++;
}?>
</ul></div>
<div class="seo-aj-zone" style="margin: 21px 5% 15px;"></div>
<div class="seo-zone" style=""></div>
<div id="seo-zone" style=""></div>
<script>
var First = true;
function getLimId(MenuTitl) {
    $('#orderModalLabel').find('.send-lim-name').text($(MenuTitl).data('lim_id'));
    $('#orderModal').find('#contact-form-lim_name').val($(MenuTitl).data('lim_id'));
}
$(function() {
    $.ajax({
        url: '/ajax-index.php?page=avtopark-avttext3',
        complete: function(data){
            if(data.responseText){
                $(".seo-aj-zone").html(data.responseText);
            }else{
                $(".seo-aj-zone").remove();
            }
        }
    });
});

</script>
