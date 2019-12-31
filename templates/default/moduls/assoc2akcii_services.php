<div class="akciiassoc">
<div class="akatitle">Эта акция распространяется на услуги:</div>
<?
//	echo '<pre>';print_r($row);echo '</pre>';
foreach($row as $item){
	echo '<a href="/'.$item['url'].'"><div class="akaitem"><div class="akaitemimg"><img src="/images/serv/main/'.$item['mainimg'].'?w=100" /></div><div class="akaitemtit">'.$item['nema'].'</div></div></a>';
}?>
</div>