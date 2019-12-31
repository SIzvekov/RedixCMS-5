<?//echo "<pre>";print_r($this->way_ar[0]);echo "</pre>";
$autopark_like_urls = array(
	'arenda-mikroavtobusov', 
	'arenda-avtomobili-biznes-klassa', 
	'arenda-avtobusov',
	'prokat_avtomobilei_premium-klassa',
	'arenda-vnedorozhnikov',
	'prokat-minivehnov',
	'avtomobili-premium-klassa'
);
$module_pages = array(
	'romanticheskoe_svidanie',
	'devichnik_v_limuzine',
	'den_rojdeniya',
	'vstrecha_iz_roddoma',
	'svadebnye-avtomobili', 
	'transfer'
);

$is_module = false;

if(in_array($this->way_ar[0], $autopark_like_urls)){
	$filename = 'autopark';
}elseif(in_array($this->way_ar[0], $module_pages)){

	$page_info = array(
		'id' => 10,
	    'com_id' => 29
	);

	$def_config = $this->component_config;
	$this->component_config = array();
	$this->page_info['sub_pages'] = $this->com_get_subpages_info($page_info);
	$this->component_config = $def_config;

	$filename = 'autopark';
	$is_module = true;
}else{
	$filename = 'default';
}
include('_services-one/services-one-'.$filename.'.php');
?>