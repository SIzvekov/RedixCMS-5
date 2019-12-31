<?
//echo "<pre>";print_r($is);echo "</pre>";
$m = date("n", $is['date']);
$rumonths = array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сетнябрь", "октябрь", "ноябрь", "декабрь");
?>
<div class="block">
	<div class="heading">
		<div class="box-date">
			<em class="date"><?=date("d", $is['date'])?></em>
			<strong><?=$rumonths[$m-1]?></strong>
		</div>
		<span class="h2"><?=$is['name']?></span>
	</div>
	<div class="holder">
		<p><?=str_replace("\n","</p><p>",$is['anons'])?></p>
		<div class="more">
			<!--noindex--><a rel="nofollow" href="/<?=$is['url']?>">подробнее</a><!--/noindex-->
		</div>
	</div>
	<img class="star" src="/templates/<?=$this->config['tpl']?>/images/img-star.png" width="87" height="87" alt="star" />
</div>