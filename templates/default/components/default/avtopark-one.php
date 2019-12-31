<?//echo "<pre>";print_r($this->page_info);echo "</pre>";?>
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
		<a href="<?=$img['imgurl']?>?w=900" target="_blank" title="<?=$img['title']?>" rel="shadowbox[qgal]"><img src="<?=$img['imgurl']?>?w=340" width="340" height="255" /></a>
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

<!-- кнопка вызова формы "Заказать лимузин" -->
<div style="width:100%; text-align: center; height: 26px;">
    <a href="#myModal" role="button" class="btn btn-my " style="float: none;" data-toggle="modal" data-lim_id="<?php echo $this->page_info["info"]["name"];?>" onclick="getLimId(this);return false"></a>
    <? if($_SERVER['REMOTE_ADDR'] == '85.90.211.225'){ ?>
<!--        <a href="#myModal" role="button" class="btn btn-success" style="float: none;" data-toggle="modal" data-lim_id="<?php //echo $this->page_info["info"]["name"];?>" onclick="getLimId(this);return false">Заказать</a>-->
    <?}?>
</div>
<!--  -->
<script>
var First = true;
function getLimId(MenuTitl) {
    $('#myModalLabel').find('.send-lim-name').text($(MenuTitl).data('lim_id'));
    $('#contact-form-lim_name').val($(MenuTitl).data('lim_id'));
}
</script>

<?
echo $this->page_info['info']['text'];

echo $this->core_modul('assocakcii','cars');
?>
<div><a href="/avtopark">&laquo;&nbsp;Все лимузины</a></div>
