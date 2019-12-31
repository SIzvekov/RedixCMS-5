<tr>
	<td id="maincontainer">
		<?$text = $core->adm_showway();
		if($text['error']){?>
		<img src="/<?=$core->adm_path?>/template/<?=$core->config['adm_tpl']?>/img/infoico/forbidden.png" height="48" width="48" border="0" align="absmiddle">&nbsp;<?=$text['text']?>
<?}
		else {echo $text['text'];}
		?>
	</td>
</tr>