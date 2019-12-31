<?php if(1){?>
<div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button type="button" class="close closeMyModal" data-dismiss="modal" aria-hidden="true">×</button>
        <p id="myModalLabel">Заказать "<span class="send-lim-name"></span>"</p>
    </div>

    <div class="modal-body">
        <form method="POST" id="contact-form" onsubmit="yaCounter17949775.reachGoal('orderlim'); return true;">

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
                    <div class="capchaitem code"><?= $this->core_show_capcha('code1', 'contact1') ?></div>
                    <input type="text" id="contact-form-code" name="code1" class="span2" value="">
                    <div id="cap_err_code"></div>
                </div>
            </div>
            <input type="hidden" id="contact-form-lim_name" name="lim_name">
            <input type="hidden" id="contact-form-action" name="action" value="addZ">
            <div class="modal-footer">
                <button class="btn btn-primary" style ="float:none;">Заказать</button>
                <button class="btn closeMyModal" data-dismiss="modal" aria-hidden="true" style ="float:none;">Закрыть</button>
            </div>

        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#contact-form-phone").inputmask("+7(999)999-99-99");
        $(".closeMyModal").click(function(){
            $("#myModal").find('input[type=text],input[type=tel]').val('');
        });

        $('#contact-form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: '/ajax-index.php?page=order_form',
                data: $(this).serializeArray(),
                success: function(response) {

                    var obj = $.parseJSON(response);
                    if ($('.notice').remove())
                    {
                        $('.modal-body').prepend('<div style="width:99%; padding: 5px 0 5px 0;display:none;" class="' + (obj.status ? 'data_send' : 'error') + ' notice">' + obj.message + '</div>').find('.notice').show('fast').delay(3000).hide('slow', function() {
                            $(this).remove();
                        });
                        if (obj.capcha_status)
                        {
                            $('#cap_err_code').append('<div style="width:99%; padding: 5px 0 5px 0;display:none;" class="' + (obj.status ? 'data_send' : 'error') + ' notice">' + obj.capcha_status + '</div>').find('.notice').show('fast').delay(3000).hide('slow', function() {
                                $(this).remove();
                            });
                        }
                        if (obj.phone_status)
                        {
                            $('#phone_err_code').append('<div style="width:99%; padding: 5px 0 5px 0;display:none;" class="' + (obj.status ? 'data_send' : 'error') + ' notice">' + obj.phone_status + '</div>').find('.notice').show('fast').delay(3000).hide('slow', function() {
                                $(this).remove();
                            });
                        }
                    }
                    if (obj.status == true )
                    {
                        /*console.log(obj,'kjhkjh');*/
                        setTimeout(function(){
                            $("#myModal").modal('hide');
                            $("#myModal").find('input[type=text],input[type=tel]').val('');
                        },3200);
                    }
                }
            });
        });

        $("#contact-form").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 4,
                    maxlength: 16,
                },
                phone: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Это поле обязательно для заполнения",
                    minlength: "Имя должно быть минимум 3 символа",
                    maxlength: "Максимальное число символо - 16",
                },
                phone: {
                    required: "Это поле обязательно для заполнения"
                }

            }

        });
    });

</script>
<?php } // if(0)?>
