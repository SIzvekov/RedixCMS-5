<?php
// echo "<pre>";print_r($this->page_info['record_id']);echo "</pre>";

//List of pages' ids where images appear above the text on page
$pages = array('27');
//check if we should display images first
echo in_array($this->page_info['id'], $pages)? '' : $this->page_info['info']['text'];
echo '<div class="seo-zone-under-raw">'.($this->page_info['info']['txtUnder'] ? $this->page_info['info']['txtUnder'] : '').'</div>';

echo in_array($this->page_info['id'], $pages)? " <div style=\"bottom: 0;left: 0;position: absolute;margin: 21px 5% 15px;\" id='page_text' class='simpletxt' > ".$this->page_info['info']['text']."</div>" : '';

?>
<div class="row"><ul class="list autopark-list">
<?
if($_SERVER['REMOTE_ADDR'] == '37.235.178.180'){
//echo "<pre>";print_r($this->page_info['sub_pages']);echo "</pre>";
}
$num = 0;
$colperstr = 2;
$totalnum = sizeof($this->page_info['sub_pages']);
$j=0;

if($is_module){
    echo '<div style="color:#ac5700; text-align:center;font-weight:bold;font-size:large;padding-top:10px;"><a target="_blank" href="http://www.limo66.ru/avtopark">НАШ АВТОПАРК</a></div>';
}

foreach($this->page_info['sub_pages'] as $i=>$info){
	// echo '<pre>';print_r($info['info']);echo '</pre>';
	$allPrices = array(
            $info['info']['normal_hour'], 
            $info['info']['normal_two_hours'], 
            $info['info']['normal_other_hours'], 
            $info['info']['wedding_hour'], 
            $info['info']['wedding_two_hours'], 
            $info['info']['wedding_other_hours'], 
            $info['info']['wedding_more_hours'], 
            $info['info']['special_price']
        );
    $min_price = 0;
    foreach ($allPrices as $price) {
        if(intval($price)){
            if($min_price) $min_price = min($min_price, $price);
            else $min_price = $price;
        }
    }
    // $min_price = 0;

    if(!$info['info']['name_car']) $info['info']['name_car'] = $info['info']['nema'];
    $modalTitle = $info['info']['name_car'].
    ($info['info']['name_seats'] ? ", ".$info['info']['name_seats']." мест" : '').
    ($info['info']['name_color'] ? ", ".$info['info']['name_color'] : '');
?>

        <li class="bigblock">
            <div class="border-inside">
            <div class="item-holder">
                <a href="/<?=$info['url']?>" class="atitle">
                <div class="auto_title_cont">
                    <div class="auto_title"><?=$info['info']['name_car']?></div>
                    <?if($info['info']['name_seats']){?><div class="place"><span>МЕСТ:</span><?=$info['info']['name_seats']?></div><?}?><!-- place_left -->
                    <?if($info['info']['name_color']){?><div class="place"><span>ЦВЕТ:</span><span class="color"><?=$info['info']['name_color']?></span></div><?}?>
                    <?if($min_price){?><div class="price"><span class="prefix">от</span> <span class="price_count"><?=$min_price?> </span><span class="rur">Р</span></div><?}?>
                </div>
                </a>
                <div class="apply">
                        <a class="order_container" href="#orderModal" role="button" data-toggle="modal" data-lim_id="<?=$modalTitle?>"
                           onclick="getLimId(this);return false">
                            <div class="order"></div>
                        </a>
                    </div>
                <div class="images">
                <a href="/<?=$info['url']?>" class="bigimg">
                    <div class="img">
	                    <img src="<?=$info['info']['mainimg'][0]['url']?>?w=340" width="340" height="255" alt="<?=$info['info']['nema']?>" />
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

<div class="seo-zone" style=""></div>
<div class="seo-zone-under" style="margin: 0px 5% 15px;"></div>
<div class="seo-zone-under-ajax" style="margin: 0px 5% 15px;"></div>

<?
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

?>

<?echo $this->core_modul('assocakcii','services');?>

<script>

var area_h = $(".area").height();
var page_text_h = $("#page_text").height();
$(".seo-zone").height( page_text_h );

var First = true;
function getLimId(MenuTitl) {
    $('#orderModalLabel').find('.send-lim-name').text($(MenuTitl).data('lim_id'));
    $('#orderModal').find('#contact-form-lim_name').val($(MenuTitl).data('lim_id'));
}
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


<script>
$(function() {
    var text = $(".seo-zone-under-raw").html();
    $(".seo-zone-under").html(text);
    $(".seo-zone-under-raw").remove();
    $.ajax({
        url: '/ajax-index.php?page=service-one-undertext&page_id=<?=$this->page_info['record_id']?>',
        complete: function(data){
            if(data.responseText){
                $(".seo-zone-under-ajax").html(data.responseText);
            }else{
                $(".seo-zone-under-ajax").remove();
            }
        }
    });
});
</script>
