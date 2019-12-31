<div class="akciiassoc">
<div class="akatitle">Эта акция распространяется на автомобили:</div>
<?
foreach($row as $item){
	echo '<a href="/'.$item['url'].'"><div class="akaitem"><div class="akaitemimg"><img src="/images/cars/main/'.$item['mainimg'].'?w=100" /></div><div class="akaitemtit">'.$item['name'].'</div><a href="#myModal" role="button" class="btn btn-my" data-toggle="modal" data-lim_id="'.$item['name'].'" onclick="getLimId(this);return false"></a></div></a>';
}?>
</div>
<script>
var First = true;
function getLimId(MenuTitl) {
    $('#myModalLabel').find('.send-lim-name').text($(MenuTitl).data('lim_id'));
    $('#contact-form-lim_name').val($(MenuTitl).data('lim_id'));
}
</script>