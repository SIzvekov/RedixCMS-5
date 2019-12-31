$(function() {
    var calculate = function() {
        if ($('#cars option:selected').val() == '' || $('#order_date').val() == '' || $('#calc_time1').val() == '' || $('#calc_time2').val() == '') {
            return false;
        }
        var data = $('#calculator').serialize();
        $.post('/ajax-index.php?page=calculator', data, function(data) {
            if (data.error) {
                // $('#popup_calc').hide();
                disableCalcWindow();
                $('#calc-dialog').show();
            } else {
                $('#price_arenda').html(data.total);
                $('#price_skidka').html(data.with_discount);
            }
        }, 'json')
        return false;
    };
    $('#close-calc-dialog').click(function() {
        // $('#popup_calc').show();
        enableCalcWindow();
        $('#calc-dialog').hide();
        return false;
    });
    var first = true;
    $('#cars').change(function() {
        var selected = $('#cars option:selected');
        $('#calc_mest').html(selected.attr('data-seats'));
        $('#calc_vybran').html(selected.html());
        $('.calc-order').attr('data-lim_id', selected.html());
        $('#orderModal').find('.send-lim-name').text(selected.attr('data-name'));
        $('#orderModal').find('#contact-form-lim_name').val(selected.attr('data-name'));
        var images = JSON.parse(selected.attr('data-images'));
        $('#calc_foto_top').carousel('pause');
        $('#calc_foto_top .carousel-inner').html('');
        for (var index in images) {
            $('#calc_foto_top .carousel-inner').append('<div class="item">' + '<img src="' + images[index].url + '?w=328" width="328" height="219" alt="' + images[index].title + '">' + '</div>');
        }
        $('#calc_foto_top .item').first().addClass('active');
        $('#calc_foto_top').carousel({
            pause: 'hover',
            interval: 3000
        });
        $('#calc_foto_top').carousel('cycle');
        calculate();
    });
    $('#calc_time1, #calc_time2').inputmask('99:99');
    $('#car_id, #order_date, #calc_time1, #calc_time2, #f_location, #t_location').change(function() {
        calculate();
    });
    $('input[type=text]').keyup(function() {
        $(this).trigger('change');
    });
    $("#datepicker").datepicker({
        minDate: new Date(),
        onSelect: function(dateText, inst) {
            $('#order_date').val(dateText);
            calculate();
        }
    });
    $('.calcbutton').click(function() {
        if ($('#car_id').val() == '' || $('#price_arenda').html() == '' || $('#price_arenda').html() == 0) {
            return false;
        }
        $('#form_from_location').val($('#f_location option:selected').html());
        $('#form_to_location').val($('#t_location option:selected').html());
        $('#form_order_date').val($('#order_date').val());
        $('#form_form_from').val($('#form_from').val());
        $('#form_form_to').val($('#form_to').val());
        $('#form_price').val($('#price_arenda').html());
        $('#form_discount_price').val($('#price_skidka').html());
        $('#calc').click();
        $('#orderModal').modal('show');
        location.hash = "#x";
    });
    $("#form_from").change(function() {
        var timeto = $("#form_from").val();
        $.ajax({
            url: '/ajax-index.php?page=gettimeto&timefrom=' + timeto,
            dataType: 'json',
            beforeSend: function() {},
            success: function(data) {
                var options = "";
                for (var i = 0; i < data.timeto.length; i++) {
                    options += data.timeto[i];
                }
                $("#form_to").html(options);
                calculate();
            },
            error: function() {}
        });
    });
    $("#form_to").change(function() {
        calculate();
    });
    $('#cars').trigger("change");
    $('#form_from').trigger("change");
    $('.ui-state-active').trigger("click");
});

function disableCalcWindow() {
	$('#popup_calc').find('input').prop('disabled', 'true');
	$('#popup_calc').find('select').prop('disabled', 'true');
	return true;
}
function enableCalcWindow(argument) {
	$('#popup_calc').find('input').removeProp('disabled');
	$('#popup_calc').find('select').removeProp('disabled');
	return true;
}