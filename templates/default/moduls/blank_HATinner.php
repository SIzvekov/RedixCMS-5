<?php include_once ($this->core_get_modtplname("order_form".".php")); ?>
<div id="wrapper1">
		<div class="w1">
			<div class="info1">
				<strong style="margin-right: -20px;"><?= $this->core_modul('text','hatcitycode');?></strong>
                <strong><?=$this->config['hattel1']?></strong>
				<strong><?=$this->config['hattel2']?></strong>
			</div>
			<div id="header">
				<div class="holder1">
					<strong class="logo1"><a href="/"><img src="/templates/default/images/logo1.png" alt="Аренда автомобилей — Golden Limo" /></a></strong>
                                        <?php /*if($_SERVER['REMOTE_ADDR'] =='91.124.58.100') { 
                                        $size = getimagesize ("http://www.limo66.ru/swf/gl.swf");
                                        var_dump($size);
                                        } */?>
                                        <?php if($_SERVER['REMOTE_ADDR'] =='91.124.58.100') { ?>
                                                <div class="logo-swf1">
                                                    <a href="/"></a>
                                                    <object type="application/x-shockwave-flash" data="/swf/gl.swf" width="211" height="150">
                                                        <param name="quality" value="high" />
                                                        <param name="wmode" value="transparent" />
                                                        <param name=movie value="/swf/gl.swf">
                                                        <embed type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/flashplayer/" wmode="transparent" src="/swf/gl.swf" width="322" height="151">
                                                    </object> 
                                                </div>
                                        <?php } ?>
				</div>
			</div>