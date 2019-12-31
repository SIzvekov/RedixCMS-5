<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?=$this->core_modul("htmlhead");?>
</head>
<body>
	<div id="gotop" style="display: none;">
		<a href="#">&#8657; Наверх</a>
	</div>
	<?echo $this->core_modul('blank', 'onlineChat')?>
	<?echo $this->thishomepage?$this->core_modul('blank', 'blank_HAT2'):$this->core_modul('blank', 'blank_HATinner')?>
			<div id="main">
				<div class="m0">
					<div class="m1">
						<div id="sidebar">
						<a href="#calc" id="calc_bottom">КАЛЬКУЛЯТОР >></a>
							<?=$this->core_modul('menu', array('mainmenu','mainmenu'))?>
							<!--noindex--><div id="avtorizblock"><?=$this->core_modul('blank', 'avtoriz')?></div><!--/noindex-->
							<div class="social_button">
								
								<script type="text/javascript" src="//yandex.st/share/share.js"
								charset="utf-8"></script>
								<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus"></div>
								
							</div>
							<?=$this->core_modul('spec')?>
							<div class="ad"><?=$this->core_modul('text', 'banner')?></div>
							<?=$this->core_modul('blank', 'blank_qcontacts')?>
						</div>
						<div id="content">
							<div class="c1">
								<div class="png-box">
									<div class="lt"></div>
									<div class="t"></div>
									<div class="rt"></div>
									<div class="c">
										<div class="c2">
											<div class="l"></div>
											<div class="content">
												<?=$this->core_modul('mainpage')?>
												<div class="area">
													<?php echo $this->thishomepage? '':$this->core_modul('pathway').'<br>'?>
													<?=COMPONENT_TEXT;?>
												</div>
											</div>
											<div class="r"></div>
										</div>
									</div>
									<div class="lb"></div>
									<div class="b"></div>
									<div class="rb"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="footer">
		<div class="holder">
			<div class="frame">
				<strong class="info">Разработка сайта: <a href="http://rrwd.ru/" target="_blank">REKORA &amp; REDIXGROUP 2012</a><br /><a href="http://www.artena.ru/promotion/" target="_blank" title="Продвижение сайтов">Продвижение сайтов</a><br /><a href="/" title="Лимузины в Екатеринбурге">Лимузины в Екатеринбурге</a></strong>
				<!--noindex--><strong class="logo2"><a rel="nofollow" href="http://rrwd.ru/" target="_blank"></a></strong>
				<strong class="logo3"><a rel="nofollow" href="http://redixgroup.ru/" target="_blank"></a></strong><!--/noindex-->
				<div class="counter">
					<!--noindex-->
					<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t45.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet' "+
"border='0' width='31' height='31'><\/a>")
//--></script><!--/LiveInternet-->
					<!--/noindex-->
				</div>
			</div>							
		</div>
	</div>
	<!--noindex-->
	<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter17949775 = new Ya.Metrika({id:17949775, enableAll: true, webvisor:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/17949775" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
	<!--/noindex-->
<?=$this->core_modul('blank', 'newton')?>
</body>
</html>
