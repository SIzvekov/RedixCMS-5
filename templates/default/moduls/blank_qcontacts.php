<div class="block qcont">
	<div itemscope itemtype="http://schema.org/Organization">
		 <span style="font-size:15px"; itemprop="name">Golden Limo</span>
		  <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<address>
									<span itemprop="postalCode"><?=$this->config['cnt_zip']?></span>
									<span itemprop="streetAddress"><?=$this->config['cnt_address']?></span>
									<span><?=$this->config['cnt_office']?></span>
									<span class="mark"><?=$this->config['cnt_tooltip']?></span>
								</address>
								</div>
								<ul class="list">
									<li>
										<?if($this->config['cnt_skype']){?><img src="/templates/<?=$this->config['tpl']?>/images/ico-skype.png" width="16" height="15" alt="skype" />
										<?=$this->config['cnt_skype']?><?}else echo "&nbsp;";?>
									</li>
									<li>
										<?if($this->config['cnt_icq']){?><img src="/templates/<?=$this->config['tpl']?>/images/ico-icq.png" width="16" height="15" alt="icq" />
										<?=$this->config['cnt_icq']?><?}else echo "&nbsp;";?>
									</li>
								</ul>
								<dl class="phone">
									<dt style="width:46px; height:1px"><?= $this->core_modul('text','footercitycode');?></dt>
									<dd>
										<span itemprop="telephone"><strong><?=$this->config['hattel1']?></strong></span>
										<span itemprop="telephone"><strong><?=$this->config['hattel2']?></strong></span>
									</div>
									<br>
										<div class="all">
											<!--noindex--><a rel="nofollow" href="/contacts">все контакты</a><!--/noindex-->
										</div>
									</dd>
								</dl>
								<img class="star" src="/templates/<?=$this->config['tpl']?>/images/img-star.png" width="87" height="87" alt="star" />
							</div>
