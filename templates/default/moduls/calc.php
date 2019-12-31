<?php
	$time_from = array();
	for($i=6; $i<26; $i++){
		$time_from[] = str_pad('' . ($i < 24 ? $i : $i - 24), 2, "0", STR_PAD_LEFT) . ":00";
		$time_from[] = str_pad('' . ($i < 24 ? $i : $i - 24), 2, "0", STR_PAD_LEFT) . ":30";
	}
	$time_to = array();
	$time_to = $time_from;
	unset($time_to[0]);
	$time_to[] = "02:00";
?>
<!--noindex-->
<div class="modal hide fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button type="button" class="close closeMyModal" data-dismiss="modal" aria-hidden="true">×</button>
        <p id="orderModalLabel">Заказать "<span class="send-lim-name"></span>"</p>
    </div>

    <div class="modal-body">
        <form method="POST" id="order-form" novalidate="novalidate">
			      <input type="hidden" name="from" id="form_form_from" />
            <input type="hidden" name="to" id="form_form_to" />
            <input type="hidden" name="order_date" id="form_order_date" />
            <input type="hidden" name="from_location" id="form_from_location" />
            <input type="hidden" name="to_location" id="form_to_location" />
            <input type="hidden" name="price" id="form_price" />
            <input type="hidden" name="discount_price" id="form_discount_price" />
            <div id="contact-form-container-result" style="display: none;"></div>

            <div class="control-group">
                <div class="controls">
                    <input type="text" id="contact-form-name" name="name" class="span5" placeholder="Ваше имя *">
                    <div class="help-block"></div>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <input type="tel" id="contact-form-phone" name="phone" class="span5" placeholder="Ваш телефон *">
                    <div class="help-block"></div>
                    <div id="phone_err_code"></div>                    
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <div class="capcha_text">Введите код с картинки</div>
                    <div class="capchaitem code"><img src="/templates/default/capcha.php?f=code&amp;nocache=9713" alt=""></div>
                    <input type="text" id="contact-form-code" name="code" class="span2" value="">
                    <div id="cap_err_code"></div>
                </div>
            </div>
            <input type="hidden" id="contact-form-lim_name" name="lim_name">
            <input type="hidden" id="contact-form-action" name="action" value="addZ">
            <div class="modal-footer">
                <button class="btn btn-primary" style="float:none;">Заказать</button>
                <button class="btn closeorderModal" data-dismiss="modal" aria-hidden="true" style="float:none;">Закрыть</button>
            </div>

        </form>
    </div>
</div>

<!-- Модальное окно ошибки -->
<div id="calc-dialog">
  <p class="ph3">ВНИМАНИЕ!</p>
  <div class="dialog-line"></div>
  <p>Бронировать лимузин на пятницу и субботу на время с 07:00 до 20:00 возможно только продолжительностью от <strong>3 часов</strong>.</p>
  <p>Подкорректируйте время, пожалуйста!</p>

  <div class="button" id="close-calc-dialog">ОК</div>
</div>

<!-- Модальное окно calc -->
<a href="#x" class="overlay" id="calc"></a>

<div class="popup">
  <div class="b-popup-content"  id="popup_calc">
    <div class="popup-title">Расчёт стоимости аренды лимузина</div>
    <a class="calc_close" href="#close"><img src="/templates/default/images/close.png" alt=""></a>

    <div class="calc">
      <form class="calcform" id="calculator" onsubmit="yaCounter17949775.reachGoal('calcorder'); return true;">
        <input type="hidden" name="order_date" id="order_date">

        <div class="calc_left">

          <span>Выберите дату:</span>

          <div id="calendar" class="calendar">          
            <div id="datepicker"></div>
          </div>

          
          <label>Время аренды:</label>
		  с <div class="select-outer-half">
          <select name="from" id="form_from">
			<?php foreach($time_from as $from){?>
				<option value="<?=$from?>"><?=$from?>
			<?php }	?>
			</select>
			<a class="select-button"></a>
		  </div>

		  &nbsp;до <div class="select-outer-half">
          <select name="to" id="form_to">
			<?php foreach($time_to as $key=>$to){?>
				<option value="<?=$to?>"><?=$to?>
			<?php }	?>
			</select>
			<a class="select-button"></a>
		  </div>
		  <div class="clear"></div><br/>

<?php/*		  <input id="calc_time1" type="text" name="from"  placeholder="00:00">
          до <input id="calc_time2" type="text" name="to"  placeholder="00:00">*/?>
          <div class="marka_bg">
		  
		  <label>Марка автомобиля:</label>
		  <div class="select-outer">
            <select name="car_id" id="cars">
              <?php foreach($cars as $car): ?>
              <?php $title = isset($car['name_car']) && !empty($car['name_car']) ? $car['name_car'] : $car['name']; ?>
                <option value="<?php echo $car['id'] ?>" data-images='<?php echo json_encode($car['images']) ?>' data-seats="<?php echo $car['name_seats'] ?>" data-name="<?php echo $car['name'] ?>"><?php echo $title ?>, <?php echo $car['name_color'] ?>, <?php echo $car['name_seats'] ?> мест</option>
              <?php endforeach ?>
            </select>
			<a class="select-button"></a>
            <!--<input type="text" class="autocomplete" id="car"  placeholder="Марка автомобиля">-->
          </div>
			</div>
			<label for="calc_adres">Адрес начала поездки:</label>
		  <div class="select-outer">
          <select name="location_from" id="f_location">
            <?php foreach($locations as $index => $location): ?>
            <option value="<?php echo $index ?>"><?php echo $location ?></option>
          <?php endforeach ?>
        </select>
		<a class="select-button"></a>
		</div>
        <label for="calc_adres">Адрес окончания поездки:</label>
		  <div class="select-outer">
        <select name="location_to" id="t_location">
          <?php foreach($locations as $index => $location): ?>
          <option value="<?php echo $index ?>"><?php echo $location ?></option>
        <?php endforeach ?>
      </select>
		<a class="select-button"></a>
		</div>
      <div class="calc_price">
        <div class="calc_price_left">
          Стоимость аренды, руб*:
          <span id="price_arenda">0</span>
        </div>

        <div class="calc_price_right">
          Стоимость с учетом скидки*:
          <span id="price_skidka">0</span>
        </div>
      </div>
    </div>


    <div class="calc_right">
      <div id="calc_foto_top" class="carousel slide">
        <!-- Wrapper for slides -->
        <div class="carousel-inner">
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#calc_foto_top" data-slide="prev">
          &lsaquo;
        </a>
        <a class="right carousel-control" href="#calc_foto_top" data-slide="next">
          &rsaquo;
        </a>
      </div>
      <p><span id="calc_vybran"></span><br clear="both" /></p>
      <p>Посадочных мест................................<span id="calc_mest"></span></p>
      <div id="calc_foto_bottom">
      </div>
      <p id="calc_podr">Подробности по тел. 213-23-12 и 213-12-23</p>           
      <input class="calcbutton" type="button" value="">
      <a href="#myModal" role="button" id="calc-order" class="btn btn-my hide" data-toggle="modal" data-lim_id="Линкольн Навигатор (LINCOLN NAVIGATOR)" onclick="getLimId(this);return false"></a>
    </div>
  </form>

  <div class="calc_footer">
    *<span style="font-weight: bold;">ВНИМАНИЕ! Стоимость ОРИЕНТИРОВОЧНАЯ.</span> Возможны дополнительные СКИДКИ!<br /> Окончательную стоимость можно уточнить в офисе при оформлении заказа.<br />
	При заказе в удаленные районы Екатеринбурга и другие населенные пункты к стоимости заказа<br />
автоматически прибавляется стоимость ВРЕМЕНИ ПОДАЧИ ЛИМУЗИНА туда и обратно.
  </div>

</div>            
</div>

</div>
<!-- // Модальное окно  calc -->  

<script src="/templates/default/js/modalcalc.js" type="text/javascript"></script>

<!--/noindex-->
