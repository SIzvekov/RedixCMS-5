<?php

if($_GET['dev']){
	
	$users = array(
		'81' => 'Алёна Белоусова',
		'94' => 'Анна Аскарова',
		'91' => 'Ирина Семенова',
		'89' => 'Ирина Шилонцева',
		'86' => 'Мария Галичанина',
		'90' => 'Мария Кайсина',
		'88' => 'Першина Анна',
	);

	if(in_array($_GET['userid'], array_keys($users))) $imgId = $_GET['userid'];
	else $imgId = current(array_keys($users));
	
	// $userIp = '188.68.132.65';
	$sql = "SELECT * FROM `sys_rx_ru_fotokonkurs_votes` `vt`, `sys_rx_users` `u` WHERE `vt`.`img_id`=".$imgId." && `u`.`id`=`vt`.`user_id` order by `vt`.`date`, `vt`.`user_ip` ASC";
	$res = $this->query($sql);
	// echo 'user IP: <span style="font-weight: bold;">'.$userIp.'</span>';
	echo '<form><input type="hidden" name="dev" value="1">';
	echo '<select name="userid">';
	foreach($users as $id=>$name){
		echo '<option value="'.$id.'"'.($id==$imgId?' SELECTED':'').'>'.$name.'</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="показать"></form><br/>';
	echo '<a name="general"></a>';
	echo '<a href="#general">Общая таблица</a><br/>';
	echo '<a href="#users">По пользователям</a><br/>';
	echo '<a href="#ips">По IP</a><br/>';
	echo '<table border="1" cellpadding="7" cellspacing="0">';
	echo '<th>#</th>';
	echo '<th>IP</th>';
	echo '<th>Login</th>';
	echo '<th>Создан</th>';
	echo '<th>Последний Визит</th>';
	echo '<th>Голосовал</th>';
	$i = 1;
	$groups = array('ip'=>array(), 'user'=>array(), 'userIp'=>array());
	while ($row = $this->fetch_assoc($res)) {
		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$row['user_ip'].'</td>';
		echo '<td>'.$row['login'].'</td>';
		echo '<td>'.date("d.m.Y H:i:s", $row['date_reg']).'</td>';
		echo '<td>'.date("d.m.Y H:i:s", $row['date_lastvizit']).'</td>';
		echo '<td>'.date("d.m.Y", strtotime($row['date'])).'</td>';
		echo '</tr>';
		$i++;

		$groups['ip'][$row['user_ip']]++;
		$groups['user'][$row['login']]++;
		$groups['userIp'][$row['login']][] = $row['user_ip']." (".date("d.m.Y", strtotime($row['date'])).")";
		$groups['ipDate'][$row['user_ip']][] = date("d.m.Y", strtotime($row['date']));
	}
	echo '</table>';

	echo '<a name="users"></a>';
	echo '<a href="#general">Общая таблица</a><br/>';
	echo '<a href="#users">По пользователям</a><br/>';
	echo '<a href="#ips">По IP</a><br/>';
	echo '<h3>По пользователям</h3>';
	echo '<table border="1" cellpadding="7" cellspacing="0">';
	echo '<th>#</th>';
	echo '<th>Пользователь</th>';
	echo '<th>Количество голосов</th>';
	echo '<th>Ip (дата) с которых голосовал пользователь</th>';
	
	$i = 1;
	foreach ($groups['user'] as $login => $number) {
		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$login.'</td>';
		echo '<td>'.$number.'</td>';
		echo '<td>'.join("<br/>", $groups['userIp'][$login]).'</td>';
		echo '</tr>';
		$i++;
	}

	echo '</table>';
	
	echo '<a name="ips"></a>';
	echo '<a href="#general">Общая таблица</a><br/>';
	echo '<a href="#users">По пользователям</a><br/>';
	echo '<a href="#ips">По IP</a><br/>';
	echo '<h3>По IP</h3>';
	echo '<table border="1" cellpadding="7" cellspacing="0">';
	echo '<th>#</th>';
	echo '<th>IP</th>';
	echo '<th>Количество голосов</th>';
	echo '<th>Дата голосования</th>';
	
	$i = 1;
	foreach ($groups['ip'] as $ip => $number) {
		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$ip.'</td>';
		echo '<td>'.$number.'</td>';
		echo '<td>'.join("<br/>", $groups['ipDate'][$ip]).'</td>';
		echo '</tr>';
		$i++;
	}

	echo '</table>';
	

	die('---');
}
?>