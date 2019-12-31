<?php

error_reporting(1);

class LocationsRegistry {
	private static $locations = array(
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
	);

	private static $locationTimes = array(
		0.5, 0.5, 0.5, 0.5,
		0.5, 0.5, 0.5, 0.5, 
		0.5, 0.5, 0.5, 0.5, 
		0.5, 0.5, 0.5, 0.5, 
		0.5, 0.5, 0.5, 0.5, 
		0.5, 0.5, 1, 1, 
		1, 1, 1, 1,
		1, 1, 1, 1, 
		1, 1, 1, 1, 
		1, 1, 1, 1, 
		1, 1, 1.5, 1.5, 
		1.5, 1.5, 1.5, 1.5, 
		2, 2, 2, 2.5, 2.5,
		2.5, 2.5, 2.5, 2.5, 
		2.5, 3, 3, 3.5, 
		4, 4, 4, 6.5
	);

	public function getLocations()
	{
		return self::$locations;
	}

	public static function getLocationByIndex($index)
	{
		return isset(self::$locations[$index]) ? self::$locations[$index] : self::$locations[0];
	}

	public static function getLocationTime($index)
	{
		return isset(self::$locationTimes[$index]) ? self::$locationTimes[$index] : self::$locationTimes[0];	
	}
}

class SpecialDayType{
	const SEPTEMBER_26_2014 = 1;
	const SEPTEMBER_27_2014 = 2;
	public static function getSpecialDayType($date)
	{
		if(date('Y', $date) == 2014 && date('m', $date) == 9 && date('d', $date) == 26)
			return self::SEPTEMBER_26_2014;
		if(date('Y', $date) == 2014 && date('m', $date) == 9 && date('d', $date) == 27)
			return self::SEPTEMBER_27_2014;
	}
}

class LocationRegistry
{
	private static $locations = array(
	);

	public static function getLocations()
	{
		if(empty(self::$locations)) {
			self::loadLocations();
		}
		return self::$locations;
	}

	private static function loadLocations()
	{
		global $core;
		self::$locations = array();
		$result = $core->query("SELECT * FROM #__locations");
		if(!$result) {
			return;
		}
		while(false !== ($row = $core->fetch_assoc($result))) {
			self::$locations[] = $row;
		}
	}

	public static function getLocationById($id)
	{
		$locations = self::getLocations();
		foreach($locations as $location) {
			if($location['id'] == $id) {
				return $location;
			}
		}
		return false;
	}

	public static function getLocationTime($id)
	{
		$location = self::getLocationById($id);
		if($location) {
			return $location['hours'];
		}
		return 0;
	}
}

class CarsRegistry
{
	private static $cars = array(

	);
	private static $special_day_cars = array(
		SpecialDayType::SEPTEMBER_26_2014 => array(
			10 => array('wedding_hour' => 2500, 'wedding_two_hours' => 2500, 'wedding_other_hours' => 2500, 'wedding_more_hours' => 2500),
			5 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
			6 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
			9 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
			19 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
		),
		SpecialDayType::SEPTEMBER_27_2014 => array(
			4 => array('wedding_hour' => 2700, 'wedding_two_hours' => 2700, 'wedding_other_hours' => 2700, 'wedding_more_hours' => 2700),
			9 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
			19 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
			1 => array('wedding_hour' => 2000, 'wedding_two_hours' => 2000, 'wedding_other_hours' => 2000, 'wedding_more_hours' => 2000),
		)
	);
	
	private static function correctPrices($special_day, $car){
		if(in_array($car['id'], array_keys(self::$special_day_cars[$special_day]))){
			foreach(self::$special_day_cars[$special_day][$car['id']] as $key=>$price){
				$car[$key] = $price;
			}
		}
		return $car;
	}

	public static function getCars()
	{
		if(empty(self::$cars)) {
			self::loadCars();
		}

		return self::$cars;
	}

	public static function getCar($id)
	{
		$cars = self::getCars();
		foreach($cars as $car) {
			if($id == $car['id']) {
				return $car;
			}
		}

		return false;
	}
	
	private static function loadCars()
	{
		global $core;
		self::$cars = array();
		$result = $core->query("SELECT * FROM #__avtopark");
		if(!$result) {
			return;
		}
		while(false !== ($row = $core->fetch_assoc($result))) {
			self::$cars[] = $row;
		}
	}

	public static function getCarPrice($time, $total, $carId)
	{
		if($total == 0.5) $total = 1;
		else $total = floor($total);
		$car = self::getCar($carId);
		if($time->special_date) $car = self::correctPrices($time->special_date, $car);

		if(!$car) {
			return 0;
		}

		if($time->getDayType() == DayType::TYPE_WEDDING && ($total >= 3 || $total < 0)) {
			$prefix = 'wedding';
			if($total == 3 || $total < 0) {
				$selector = "{$prefix}_hour";
			} elseif($total == 4) {
				$selector = "{$prefix}_two_hours";
			} elseif($total == 5 || $total == 6) {
				$selector = "{$prefix}_other_hours";
			} else {
				$selector = "{$prefix}_more_hours";
			}
		} elseif($time->getDayType() == DayType::TYPE_WEDDING_PLUS && $total >= 3) {
			$selector = "special_price";
		} else {
			$prefix = 'normal';
			if($total == 1 || $total < 0) {
				$selector = "{$prefix}_hour";
			} elseif($total == 2) {
				$selector = "{$prefix}_two_hours";
			} else {
				$selector = "{$prefix}_other_hours";
			}
		}
/*		if($_SERVER['REMOTE_ADDR'] == '37.235.177.59' && $total >= 3){
			echo $selector." ".$car[$selector];
			print_r($time);
		}*/
		$return = isset($car[$selector]) ? (int)$car[$selector] : 0;
		//  Если это октябрь, меняем почасовую ставку (если больше 3-х часов) для Chrysler 300C
		if(in_array($car['id'], array(1,9,19)) && $selector == "normal_other_hours" && $time->isOctober()){
			$return = 1700;
		}
		return $return;
	}
}

class Day
{
	public $type;
	public $date;

	public function __construct($date, $delta = 0)
	{
		$date = strtotime($date);
		$this->date = $date;
		$this->type = DayType::getDayType(date('w', $date), $delta);
	}

	public function getK($hour)
	{
		
		/*if($hour->getType() == TimeType::TYPE_DAY || $hour->getType() == TimeType::TYPE_EVENING) {
			return 0;
		}
		if($hour->getDayType() == DayType::TYPE_WEDDING)
			return 500;
		*/
		if($hour->getType() == TimeType::TYPE_NIGHT){
			return 500;
		}
		return 0;
	}

	public function getType()
	{
		return $this->type;
	}
	
	static public function countHours($hours){
		$num = 0;
		foreach($hours as $hour){
			$num += $hour->half ? 0.5 : 1;
		}
		return $num;
	}
}

class Hour 
{
	private $type;
	private $day;
	public $half;
	public $special_date = 0;
	public $location_time; //  это время подачи
	private $is_october = false;

	public function __construct($h, $day, $half)
	{
		$this->day = $day;
		$this->type = TimeType::getTimeType($h);
		$this->half = $half;
		$this->special_date = SpecialDayType::getSpecialDayType($this->day->date);
	}

	public function getPrice($totalHours, $carId)
	{
		return ($this->half ? 0.5 : 1) * (CarsRegistry::getCarPrice($this, $totalHours, $carId)) + (($this->half ? 0.5 : 1) - $this->location_time) * ($this->day->getK($this));
	}

	public function getDayType()
	{
		if(
			self::check_in_range('2014-10-01', '2015-04-14', date('Y-m-d', $this->day->date)) || 
			(date('Y', $this->day->date) == 2014 && date('m', $this->day->date) == 9 && date('d', $this->day->date) == 30 && $this->type == TimeType::TYPE_NIGHT)
		){
//			echo "Yes";
			$this->day->type = DayType::TYPE_NORMAL;
			$this->is_october = true;
		}
		if($this->type == TimeType::TYPE_EVENING || $this->type == TimeType::TYPE_NIGHT) {
			return DayType::TYPE_NORMAL;
		}

		return $this->day->getType();
	}
	
	static function check_in_range($start_date, $end_date, $date_from_user)
	{
		// Convert to timestamp
		$start_ts = strtotime($start_date);
		$end_ts = strtotime($end_date);
		$user_ts = strtotime($date_from_user);

		// Check that user date is between start & end
		return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
	}

	public function getType()
	{
		return $this->type;
	}
	
	static function timeToNumber($time){
		return floatval(substr($time, 0, 2)) + (substr($time, -2) != "00" ? 0.5 : 0);
	}

	//  уже октябрь 2014 или позже
	public function isOctober(){
		return $this->is_october;
	}
	
	public function setLocationTime($location_time){
		if(($this->half && $location_time >= 0.5) || (!$this->half && $location_time == 0.5)){
			$this->location_time = 0.5;
		}
		else if($location_time >= 1){
			$this->location_time = 1;
		}
		return $location_time - $this->location_time;
	}
}


class DiscountCalculator 
{
	public static function calculate($total, $delta)
	{
		$total = floor($total);
		if($total == 5) {
			return 5;
		}
		if($total == 6 && $delta < 45) {
			return 10;
		}
		if($total >= 7 && $delta < 45) {
			return 15;
		}
		if($total == 6 && $delta >= 45) {
			return 15;
		}
		if($total >= 7 && $delta >= 45) {
			return 20;
		}
	}
}


class Calculator
{
	private static $day;
	private static $deltaDays;

	public static function calculate($params)
	{

		self::$deltaDays = self::calculateDeltaDays($params['order_date']);

		if(self::$deltaDays < 0) {
			return array('error' => 'Не валидная дата');
		}
		self::$day = new Day($params['order_date'], self::$deltaDays);
		$deliveryTime = LocationRegistry::getLocationTime($params['location_from']);
		$endTime = LocationRegistry::getLocationTime($params['location_to']);
//		$from = date('H', strtotime($params['from']));
//		$to = date('H', strtotime($params['to']));
		$from = Hour::timeToNumber($params['from']);
		$to = Hour::timeToNumber($params['to']);
		$hours = self::getHours($from, $to);
		$total = Day::countHours($hours);

		$hours = self::getHours($from - /*ceil*/($deliveryTime), $to + /*ceil*/($endTime), $deliveryTime, $endTime);
		
		if($_SERVER['REMOTE_ADDR'] == '37.235.177.59'){
//			print_r($hours);
		}
		$evening = true;
		foreach($hours as $hour) {
			if($hour->getType() != TimeType::TYPE_EVENING && $hour->getType() != TimeType::TYPE_NIGHT) {
				$evening = false;
				break;
			}
		}

		if((self::$day->getType() == DayType::TYPE_WEDDING || self::$day->getType() == DayType::TYPE_WEDDING_PLUS) && !$evening) {
			if($total < 3) {
				return array('error' => 'Минимальный срок заказа 3 часа');
			}
		}

		$sum = 0;

		foreach($hours as $hour) {
			$sum += $hour->getPrice($total, $params['car_id']);
		}

		//  Отключаем пока скидки
		$discount = 0;
		//  $discount = DiscountCalculator::calculate($total, self::$deltaDays);
		
		$sumWithDiscount = ceil($sum - (($discount / 100) * $sum));

		return array('total' => $sum, 'with_discount' => $sumWithDiscount, 'discount' => $discount);
	}

	/**
	* Calculate delta days
	*/
	public static function calculateDeltaDays($orderDate)
	{
		$diff = strtotime($orderDate) - time();
		return ceil($diff / 60 / 60 / 24);
	}

	public static function getHours($start, $end, $deliveryTime, $endTime)
	{
		$half_start = false;
		$half_end = false;
		if(ceil($start) > $start) {
			$half_start = true;
			$start = ceil($start) - 1;
		}
		if(floor($end) < $end) {
			$half_end = true;
			$end = ceil($end);
		}
		$output = array();
		if($start > $end) {
			$end = $end + 24;
		}
		for($i = $start + 1; $i <= $end; $i++) {
			$hour = $i <= 24 ? $i : $i - 24;
			$half = false;
			if($i == $start + 1 && $half_start) $half = true;
			if($i == $end && $half_end) $half = true;
			$output[] = new Hour($hour, self::$day, $half);
		}
		$location_time = $deliveryTime;
		if($location_time)
		for($i = 0; $i < sizeof($output); $i++) {
			$location_time = $output[$i]->setLocationTime($location_time);
			if($location_time == 0) break;
		}
		$location_time = $endTime;
		if($location_time)
		for($i = sizeof($output) - 1; $i >= 0; $i--) {
			$location_time = $output[$i]->setLocationTime($location_time);
			if($location_time == 0) break;
		}

		return $output;
	}
}

class DayType
{
	const TYPE_NORMAL = 1;
	const TYPE_WEDDING = 2;
	CONST TYPE_WEDDING_PLUS = 3;

	private static $types = array(
		1 => array(0, 1, 2, 3, 4),
		2 => array(5, 6),
	);

	public static function getDayType($dayOfWeek, $delta)
	{
		foreach(self::$types as $k => $type) {
			if(in_array($dayOfWeek, $type)) {
				$type = $k;
/*				if($type == self::TYPE_WEDDING && $delta <= 7) {
					return self::TYPE_WEDDING_PLUS;
				}*/

				return $type;
			}
		}

		return self::TYPE_NORMAL;
	}
}

class TimeType
{
	const TYPE_DAY = 1;
	const TYPE_EVENING = 2;
	const TYPE_NIGHT = 3;

	private static $ranges = array(
		1 => array('from' => 8, 'to' => 20),
		2 => array('from' => 21, 'to' => 24),
		3 => array('from' => 1, 'to' => 7),
	);

	public static function getTimeType($hour) 
	{
		foreach(self::$ranges as $rangeType => $range) {
			if($hour >= $range['from'] && $hour <= $range['to']) {
				return $rangeType;
			}
		}

		return self::TYPE_DAY;
	}
}


class CalculatorTest
{
	public function assertEqual($first, $second, $message)
	{
		if($first != $second) {
			echo $message . "...FAIL {$first} != {$second} . <br>\n";
			return;
		}
		echo $message . "...OK<br>\n";
	}
	public function testTimeType()
	{
		$evening = TimeType::getTimeType(20);
		$day = TimeType::getTimeType(19);
		$night = TimeType::getTimeType(0);
		$this->assertEqual($evening, TimeType::TYPE_EVENING, "Should be evening");
		$this->assertEqual($day, TimeType::TYPE_DAY, "should be day");
		$this->assertEqual($night, TimeType::TYPE_NIGHT, "should be night");
	}

	public function testDayType()
	{
		$normal = DayType::getDayType(0);
		$wedding = DayType::getDayType(6);
		$this->assertEqual($normal, DayType::TYPE_NORMAL, "Should be normal");
		$this->assertEqual($wedding, DayType::TYPE_WEDDING, "Should be wedding");
	}

	public function testDeltaDays()
	{
		$date = date('d.m.Y', strtotime('+10 days'));
		$delta = Calculator::calculateDeltaDays($date);
		$this->assertEqual($delta, 10, "Delta days should be 10");
	}

	public function testHours()
	{
		$hours = Calculator::getHours('11:00', '16:00');
		$this->assertEqual(count($hours), 5, "Hours should be 5");
		$hours = Calculator::getHours('18:00', '16:00');
		$this->assertEqual(count($hours), 22, "Hours should be 22");
	}

	public function testDiscount()
	{
		$discount = DiscountCalculator::calculate(5, 0);
		$this->assertEqual($discount, 5, "Discount should be 5");
		$discount = DiscountCalculator::calculate(6, 44);
		$this->assertEqual($discount, 10, "Discount should be 10");
		$discount = DiscountCalculator::calculate(7, 44);
		$this->assertEqual($discount, 15, "Discount should be 15");
		$discount = DiscountCalculator::calculate(6, 45);
		$this->assertEqual($discount, 15, "Discount should be 15");
		$discount = DiscountCalculator::calculate(7, 45);
		$this->assertEqual($discount, 20, "Discount should be 20");
	}

	public function run() 
	{
		$this->testTimeType();
		$this->testDayType();
		$this->testDeltaDays();
		$this->testHours();
		$this->testDiscount();
	}
}

if(isset($_GET['test'])) {
	$test = new CalculatorTest();
	$test->run();
	exit;
}

if($_POST) {
	echo json_encode(Calculator::calculate($_POST));
}

if(isset($_GET['install'])) {
	global $core;

	$core->query('DELETE FROM #__sitemap WHERE com_id=46');
	$core->query('DELETE FROM #__locations WHERE 1');

	$locations = LocationsRegistry::getLocations();
	foreach($locations as $index => $location) {
		$name = $location;
		$time = LocationRegistry::getLocationTime($index);
		$core->query("INSERT INTO #__locations(name, hours) VALUES('{$name}', '{$time}')");

		$record_id = $core->insert_id();
		$core->query("INSERT INTO #__sitemap(url, pid, title, public, type, tplfile, record_id, com_id) VALUES('lokacii/{$index}', 141, '{$location}', 1, 'page', 'text.php', {$record_id}, 46)");
	}
}