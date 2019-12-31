<div class="akciiassoc">
<div class="akatitle">Акция!</div>
<?
//echo '<pre>';print_r($row);echo '</pre>';
foreach($row as $item){
	echo '<a href="/'.$item['url'].'"><div class="akaitem"><div class="akaitemimg"><img src="/images/akcii/'.$item['mainimg'].'?w=100" /></div><div class="akaitemtit">'.$item['name'].'</div></div></a>';
}?>
</div>