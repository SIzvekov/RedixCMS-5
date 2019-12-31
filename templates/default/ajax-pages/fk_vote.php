<?
$deadLine = '2015-12-05 00:00:00';
$time = strtotime($deadLine);
$FKactive = (time()<$time);

if(!$FKactive){ 
	$_RESULT['fk_callback'] = 1;
	$_RESULT['was'] = 1;
	$_RESULT['wastext'] = 'Голосование завершено';
}else{

if(!intval($_SESSION['user']['id']) || intval($_SESSION['user']['group']['id'])!='3')
{
	$error = true;
	$_RESULT['fk_noauth'] = 1;
}else
{
	$_RESULT['fk_noauth'] = 0;

	$sql = "SELECT * FROM `#__fotokonkurs_votes` WHERE (`user_id`=".intval($_SESSION['user']['id'])." || `user_ip`='".addslashes($this->get_user_ip())."') && `date`='".date('Y-m-d', time())."'";
	$was = $this->num_rows($this->query($sql));
	$was_text = "Вы уже проголосовали сегодня";

	if(!$was)
	{
		$sql = "INSERT INTO `#__fotokonkurs_votes` SET `img_id`=".intval($_GET['p']).", `user_id`=".intval($_SESSION['user']['id']).", `date`=NOW(), `user_ip`='".addslashes($this->get_user_ip())."'";
		$this->query($sql);
		
		$sql = "SELECT `score` FROM `#__fotokonkurs_fotos` WHERE `id`=".intval($_GET['p']);
		$score = $this->fetch_assoc($this->query($sql));
		$score = intval($score['score']) + 1;

		$sql = "UPDATE `#__fotokonkurs_fotos` SET `score`=".$score." WHERE `id`=".intval($_GET['p']);
		$this->query($sql);

		$_RESULT['score'] = $score;
		$_RESULT['was'] = 0;
		$_RESULT['wastext'] = $was_text;
	}else
	{
		$_RESULT['was'] = 1;
		$_RESULT['wastext'] = $was_text;
	}
}

$_RESULT['p'] = intval($_GET['p']);


$_RESULT['fk_callback'] = 1;
}
?>