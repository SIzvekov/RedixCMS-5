<html>
<head>
	<title></title>
	<style type="text/css">
		label {
			display: block;
		}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
</head>
<body>
	<?php
	$locations = array(
		'Верхняя Пышма', 
		'Балтым', 
		'Чистые пруды', 
		'Зеленый бор',
		'Кольцово', 
		'Арамиль', 
		'Большой и Малый Исток', 
		'Торфянник',
		'Березовский', 
		'Новоберезовский', 
		'Горный Щит', 
		'Решеты', 
		'Компрессорный',
		'Калинино', 
		'Большое Седельниково', 
		'Верхнее Дуброво', 
		'Изоплит',
		'Среднеуральск', 
		'Шувакш', 
		'Бородулино', 
		'Патруши', 
		'Шабровский',
		'Первоуральск', 
		'Ревда', 
		'Исеть', 
		'Коптяки', 
		'Солнечный берег(Среднеур)',
		'Сысерть', 
		'Полевской', 
		'Дегтярск', 
		'Бобровский', 
		'Красный', 
		'«Шишки»',
		'Новоуральск ', 
		'Косулино', 
		'Белоярский', 
		'Большие Брусяны',
		'Лосиный', 
		'Монетный',
		'Реж', 
		'Заречный', 
		'Сарапулка',
		'Невьянск', 
		'Кировград', 
		'Нижние Серги',
		'Верхний Тагил', 
		'Богданович', 
		'Асбест',
		'Артемовский', 
		'Алапаевск',
		'Сухой Лог',
		'Камышлов ', 
		'Обуховское', 
		'Никольское',
		'Каменск-Уральский',
		'Нижний Тагил',
		'Касли', 
		'Кыштым',
		'Верхняя Салда ', 
		'Кушва',
		'Красноуральск',
		'Ирбит', 
		'Качканар',
		'Челябинск',
		'Серов'
	); ?>
	<form id="calculator" method="post">
		<input type="hidden" name="car_id" id="car_id" value="">
		<div>
			<label>Дата заказа</label>
			<input type="text" name="order_date" class="datepicker">
		</div>
		<div>
			<label>Время начала заказа</label>
			<input type="text" name="from" class="timepicker">
		</div>
		<div>
			<label>Время окончания заказа</label>
			<input type="text" name="to" class="timepicker">
		</div>
		<div>
			<label>Автомобиль</label>
			<input type="text" class="autocomplete" id="car">
		</div>
		<div>
			<label>Адрес начала поездки</label>
			<select name="location_from">
				<?php foreach($locations as $index => $location): ?>
					<option value="<?php echo $index ?>"><?php echo $location ?></option>
				<?php endforeach ?>
			</select>
		</div>
		<div>
			<label>Адрес окончания поездки</label>
			<select name="location_to">
				<?php foreach($locations as $index => $location): ?>
					<option value="<?php echo $index ?>"><?php echo $location ?></option>
				<?php endforeach ?>
			</select>
		</div>
		<div>
			Итого:
			<span id="result">0</span>
		</div>
		<div>
			Цена со скидкой:
			<span id="result-discount">0</span>
		</div>
		<div>
			Скидка:
			<span id="discount">0</span>
		</div>
		<a href="#" id="calculate-button">Calculate</a>
	</form>

	<script type="text/javascript">
	$(function() {
		var current = null;
		$('.datepicker').datepicker();
		$('.timepicker').timepicker();
		$('.autocomplete').autocomplete({
			source: function(request, response) {
				$.get('/ajax-index.php?page=limo_autocomplete&q=' + request.term, function(data) {
					var results = [];
					for(var index in data) {
						results.push({label: data[index].name, value: data[index].name, id: data[index].id});
					}
					response(results);
				}, 'json');
			},
			focus: function( event, ui ) {
				$('#car_id').val(ui.item.id);
				return false;
			},
			select: function( event, ui ) {
				$('#car_id').val(ui.item.id);
				$('#car').val(ui.item.label);
		 
				return false;
			}
		});

		$('#calculate-button').button().click(function() {
			var data = $('#calculator').serialize();
			$.post('/ajax-index.php?page=calculator', data, function(data) {
				if(data.error) {
					alert(data.error);
				} else {
					$('#result').html(data.total);
					$('#result-discount').html(data.with_discount);
					$('#discount').html(data.discount);
				}
			}, 'json')
			return false;
		});
	});
	</script>
</body>
</html>