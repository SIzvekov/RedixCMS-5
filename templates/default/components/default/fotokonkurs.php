<?
$deadLine = '2015-12-05 00:00:00';
$time = strtotime($deadLine);
$FKactive = (time()<$time);

if(!$FKactive){
	$was = 1;
	$was_text = "Голосование завершено";
}elseif($_SESSION['user']['id'])
{
	$sql = "SELECT * FROM `#__fotokonkurs_votes` WHERE (`user_id`=".intval($_SESSION['user']['id'])." || `user_ip`='".addslashes($this->get_user_ip())."') && `date`='".date('Y-m-d', time())."'";
	$was = $this->num_rows($this->query($sql));
	$was_text = "Вы уже проголосовали сегодня";
}else{
	$was = 0;
}

echo '<div class="simpletxt">'.$this->page_info['info']['description'].'</div>';
?>
<script type="text/javascript" src="/templates/default/js/highslide/highslide-with-gallery.js"></script>
<script type="text/javascript" src="/templates/default/js/highslide/highslide.config.js"></script>
<link rel="stylesheet" type="text/css" href="/templates/default/js/highslide/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '/templates/default/js/highslide/graphics/';
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.wrapperClassName = 'white borderless';
	hs.fadeInOut = true;
	hs.dimmingOpacity = .75;

	/* Add the controlbar */
	if (hs.addSlideshow) hs.addSlideshow({
		/* slideshowGroup: 'group1',*/
		interval: 5000,
		repeat: false,
		useControls: true,
		fixedControls: 'fit',
		overlayOptions: {
			opacity: .6,
			position: 'bottom center',
			hideOnMouseOut: true
		}
	});
</script>
<div class="highslide-gallery">
<div class="fk_wraper">
	<div class="fk_title">Участницы</div>
	<?
	$i = 0;
	foreach($this->page_info['info']['images'] as $item){
		$i++;
		?>
	<div class="fk_item">
		<?if($item['firstpalce']){?><div class="firstplace"><img src="/templates/default/images/firstplace.png" alt="" title=""/></div><?}?>
		<div class="fk_img">
			<a href="<?=$item['imgurl']?>?w=700" class="highslide" onclick="return hs.expand(this)"><img src="<?=$item['imgurl']?>?w=250" alt="" title="" /></a>
			<div class="highslide-caption fk_info" id='caption-votebtnd-<?=$i?>' style="overflow:hidden;">
				<div style="float:left;">
					<div class="fk_name" style="font-size:13px;font-weight:bold;"><?=$item['title']?></div>
					<div class="fk_score">Голосов: <span id="fkscore<?=$item['id']?>"><?=$item['score']?></span></div>
				</div>
				<div class="vote" style="float:right" id="fk_vote-<?=$item['id']?>"><div id='votebtnd-<?=$i?>'>
					<?if(!$was){?>
					<img src="/templates/default/images/voteaxtrabtn.png" alt="" title="" id="fk_vote_btn-<?=$item['id']?>" onclick="hs.close(this);fk_vote(<?=$item['id']?>);" style="cursor:pointer;"/>
					<?}else{?>
					<i><small><?=$was_text?></small></i>
					<?}?>
				</div></div>
			</div>
		
		</div>
		<div class="fk_info">
			<div class="fk_name"><?=$item['title']?></div>
			<div class="fk_score">Голосов: <span id="fkscore<?=$item['id']?>"><?=$item['score']?></span></div>
			<div class="vote" id="fk_vote-<?=$item['id']?>"><div id='votebtn-<?=$i?>'>
				<?if(!$was){?>
				<input type="button" value="проголосовать" onclick="fk_vote(<?=$item['id']?>);" id="fk_vote_btn-<?=$item['id']?>"/>
				<?}else{?>
				<i><small><?=$was_text?></small></i>
				<?}?>
			</div></div>
		</div>
	</div>
	<?}?>
</div>
</div>

<div id="fk_loginform_block">
	<div class="fk_login_close" onclick="close_fk_avt_form()">x</div>
	<div id="fk_loginform"></div>
</div>
