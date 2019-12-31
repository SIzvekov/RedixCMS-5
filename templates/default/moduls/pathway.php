<?php //v.1.0.?><?
$separator = $data;
$patharay = array();
$ind = 0;

$size = sizeof($this->pathway);
foreach($this->pathway as $item)
{
	$ind++;
	if($item['url'] && $ind!=$size)
		$patharay[] = "<a href=\"".($item['url'] != "/index/"?$item['url']:"/")."\" class='pathwaylink'>".$item['text']."</a>";
	else 
		$patharay[] = "<span class='pathwaytext'>".$item['text']."</span>";
}
echo "<div class='pathway'>".join(" <span class='sep'>".($separator?$separator:"/")."</span> ", $patharay)."</div>";
?>