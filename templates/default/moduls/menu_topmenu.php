<style>

</style>

<div class="topmenu"><?php
    //v.1.0.
//echo "<pre>";
//print_r($row);

$requestURI = explode("?", REQUEST_URI,2);
$requestURI = $requestURI[0];

$width = floor(100 / sizeof($row['menu_punkti']));
$leftWidth = 100;
    foreach ($row['menu_punkti'] as $item) {
    	$leftWidth -= $width;
    	if($leftWidth && $leftWidth < $width) $width += $leftWidth;

        $cur_punkt = ($item['link'] == trim($requestURI, "/") || ($this->thishomepage && $this->config['home_url'] == $item['link']));
        ?>
        <div class="newmenuitem" style="width:<?=$width?>%" <? echo $cur_punkt ? 'id="ctopmenu"' : 'onclick="location.href=\'' . (preg_match('/^http/', $item['link']) ? '' : ($this->config['use_param'] ? '/' . $this->param . '/' : '/')) . $item['link'] . '\'"'?>><a <? echo $cur_punkt ? '' : 'href="' . (preg_match('/^http/', $item['link']) ? '' : ($this->config['use_param'] ? '/' . $this->param . '/' : '/')) . $item['link'] . '"'; ?>><?= $item['name'] ?></a></div>
<? } ?>


</div>
