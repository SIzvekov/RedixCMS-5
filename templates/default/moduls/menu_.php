<?php //v.1.0.
//echo "<pre>";
//print_r($row);


foreach($row['menu_punkti'] as $item){
$cur_punkt = ($item['link']==trim(REQUEST_URI,"/") || ($this->thishomepage && $this->config['home_url']==$item['link']));
?>
<a <?echo $cur_punkt?'id="ctopmenu"':'href="'.(preg_match('/^http/',$item['link'])?'':($this->config['use_param']?'/'.$this->param.'/':'/')).$item['link'].'"';?>><?=$item['name']?></a>
<?}?>