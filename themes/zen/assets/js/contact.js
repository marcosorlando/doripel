$(function () {
    var WcAjax = $("link[rel='base']").attr('href') + "/themes/zen/ajax/Contact.ajax.php";
    
    $('.jwc_contact_close').click(function () {
        $('.wc_contant_sended').fadeOut(200);
        //$('.form-contact').fadeIn(400);
        $('.form-contact').fadeIn(400, function () {
            $('.form-contact input, .form-contact textarea').val("");
        });
        
        return false;
    });
    
    $('.form-contact').submit(function () {
        
        var WcForm = $(this);
        WcForm.find('img').fadeIn(200);
        var ContactData = $(this).serialize();
        
        $.post(WcAjax, ContactData, function (data) {
            WcForm.find('img').fadeOut(200);
            
            if (data.response) {
                $('#response').html(data.response).fadeIn();
            } else {
                $('#response').fadeOut();
            }
            
            if (data.wc_send_mail) {
                $('.jwc_contant_sended_name').text(data.wc_send_mail);
                $('.form-contact').fadeOut(400, function () {
                    $('.wc_contant_sended').fadeIn(400);
                });
            }
        }, 'json');
        return false;
    });
    
    //SELETOR, EVENTO/EFEITO, CALLBACK, AÇÃO
    $('.j_formsubmit').submit(function () {
        var form = $(this);
        var data = $(this).serialize();
        var WcAjax = $("link[rel='base']").attr('href') + "/themes/zen/ajax/Contact.ajax.php";
        
        $.ajax({
            url: WcAjax,
            data: data,
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                form.find('.form_load').fadeIn(500);
            },
            success: function (data) {
                //REMOVE LOAD
                form.find('.form_load').fadeOut('slow', function () {
                    //EXIBE CALLBACKS
                    if (data.trigger) {
                        var CallBackPresent = form.find('.callback_return');
                        if (CallBackPresent.length) {
                            CallBackPresent.html(data.trigger);
                            $('.trigger_ajax').fadeIn('slow');
                        } else {
                            Trigger(data.trigger);
                        }
                    }
                });
            }
        });
        return false;
    });
});
