<?php
$time_to = array();
function timeToNumber($time){
	return floatval(substr($time, 0, 2)) + (substr($time, -2) != "00" ? 0.5 : 0);
}
if(isset($_GET['timefrom'])){
	$time_from = timeToNumber($_GET['timefrom']);
	$time_to = array();
	for($i = intval($time_from < 2 ? ($time_from + 24) : ($time_from)); $i < 27; $i++){
		$tmp = str_pad('' . ($i < 24 ? $i : $i - 24), 2, "0", STR_PAD_LEFT) . ":00";
		if(timeToNumber($tmp) > $time_from || ($time_from > 5 && $i >= 24))
			$time_to[] = '<option value="' . $tmp . '">' . $tmp . '</option>';
		if($i == 26) continue;
		$tmp = str_pad('' . ($i < 24 ? $i : $i - 24), 2, "0", STR_PAD_LEFT) . ":30";
		if(timeToNumber($tmp) > $time_from || ($time_from > 5 && $i >= 24))
			$time_to[] = '<option value="' . $tmp . '">' . $tmp . '</option>';
	}
}
echo json_encode(array('timeto' => $time_to));
?>