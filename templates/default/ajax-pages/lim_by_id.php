<?php

if(isset($_GET['id'])) {
	$limo = $this->fetch_assoc($this->query("SELECT * FROM #__avtopark WHERE id=" . (int)$_GET['id']));
	echo json_encode(array('limo' => $limo));
} else {
	echo json_encode(array('limo' => false));
}