<?php

$results = array();
$query = isset($_GET['q']) ? $_GET['q'] : '';

if(strlen($query) > 0) {
	$res = $this->query("SELECT * FROM #__avtopark WHERE name LIKE '%" . mysql_real_escape_string($query) . "%'");
	while(false !== ($row = $this->fetch_assoc($res))) {
		$results[] = $row;
	}
}

echo json_encode($results);