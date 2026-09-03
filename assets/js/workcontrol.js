$(function () {
    
    //############## GET PROJECT
    BASE = $("link[rel='base']").attr("href");
    
    //############## IFRAME RESET
    function VideoResize() {
        $('.htmlchars iframe').each(function () {
            var url = $(this).attr("src");
            var char = "?";
            if (url.indexOf("?") != -1) {
                var char = "&";
            }
            
            var iw = $(this).width();
            var width = $('.htmlchars').outerWidth();
            var height = (iw * 9) / 16;
            $(this).attr({width: width, height: height}).css({width: width + "px", height: height + "px"});
        });
    }
    
    VideoResize();
    $(window).resize(function () {
        VideoResize();
    });
    
    //############## GOTO CORE
    $('.wc_goto').click(function () {
        var Goto = $($(this).attr("href"));
        if (Goto.length) {
            $('html, body').animate({scrollTop: Goto.offset().top - 110}, 800);
        } else {
            $('html, body').animate({scrollTop: 0}, 800);
        }
        return false;
    });
    
    //############## IMAGE ERROR
    $('img').error(function () {
        var s, w, h, b;
        s = $(this).attr('src');
        w = 500;
        h = 500;
        b = $('link[rel="base"]').attr('href');
        $(this).attr('src', b + '/tim.php?src=admin/_img/no_image.jpg&w=' + w + "&h=" + h);
    });
    
    //############## GET CEP
    $('.wc_getCep').change(function () {
        var cep = $(this).val().replace('-', '').replace('.', '');
        if (cep.length === 8) {
            $.get("https://viacep.com.br/ws/" + cep + "/json", function (data) {
                if (!data.erro) {
                    $('.wc_bairro').val(data.bairro);
                    $('.wc_complemento').val(data.complemento);
                    $('.wc_localidade').val(data.localidade);
                    $('.wc_logradouro').val(data.logradouro);
                    $('.wc_uf').val(data.uf);
                }
            }, 'json');
        }
    });
    
    //############## MASK INPUT
    if ($('.formDate').length || $('.formTime').length || $('.formCep').length || $('.formCpf').length || $('.formPhone').length) {
        $.getScript(BASE + '/assets/maskinput.js', function () {
            $(".formDate").mask("99/99/9999");
            $(".formTime").mask("99/99/9999 99:99");
            $(".formCep").mask("99999-999");
            $(".formCpf").mask("999.999.999-99");
            
            var SPMaskBehavior = function (val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function (val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $('.formPhone').mask(SPMaskBehavior, spOptions);
        });
    }
    
    //############## DATEPICKER
    if ($('.jwc_datepicker').length) {
        $("head").append('<link rel="stylesheet" href=' + BASE + '"/assets/datepicker/datepicker.min.css">');
        $.getScript(BASE + '/assets/datepicker/datepicker.min.js');
        $.getScript(BASE + '/assets/datepicker/datepicker.pt-BR.js', function () {
            $('.jwc_datepicker').datepicker({language: 'pt-BR', autoClose: true});
        });
    }
    
    //############## WC TAB
    $('.wc_tab').click(function () {
        if (!$(this).hasClass('wc_active')) {
            var WcTab = $(this).attr('href');
            
            $('.wc_tab').removeClass('wc_active');
            $(this).addClass('wc_active');
            
            $('.wc_tab_target.wc_active').fadeOut(200, function () {
                $(WcTab).fadeIn(200).addClass('wc_active');
            }).removeClass('wc_active');
        }
        
        if (!$(this).hasClass('wc_active_go')) {
            return false;
        }
    });
    
    //############## WC ACCORDION
    $('.wc_accordion').click(function () {
        $('.wc_accordion_toogle_active').slideUp(200, function () {
            $(this).removeClass('wc_accordion_toogle_active');
        });
        $(this).find('.wc_accordion_toogle:not(.wc_accordion_toogle_active)').slideToggle(200).addClass('wc_accordion_toogle_active');
    });
    
    $('.wc_accordion div').click(function (e) {
        e.stopPropagation();
    });
    
    //############## HIGHLIGHT
    if ($('*[class="brush: php;"]').length) {
        $("head").append('<link rel="stylesheet" href=' + BASE + '"/assets/css/highlight.min.css">');
        $.getScript(BASE + '/assets/highlight.min.js', function () {
            $('*[class="brush: php;"]').each(function (i, block) {
                hljs.highlightBlock(block);
            });
        });
    }
    
    //############## MODAL BOX
    if ($('*[rel*="shadowbox"]').length) {
        $("head").append('<link rel="stylesheet" href=' + BASE + '"/assets/shadowbox/shadowbox.css">');
        $.getScript(BASE + '/assets/shadowbox/shadowbox.js', function () {
            Shadowbox.init();
        });
    }
    
    //############## GET CNPJ + Validando com function validarCNPJ
    $('.wc_getCnpj').on('blur', function () {
        if (!validarCNPJ(this.value)) {
            Trigger("<div class='trigger trigger_ajax trigger_error' >CNPJ Informado é inválido.</div>")
            $(".wc_getCnpj").focus();
            clearCnpjData();
        } else {
            var cnpj = $(this).val().replace('-', '').replace('/', '').replace('.', '').replace('.', '');
            
            if (cnpj.length === 14) {
                $.getJSON("https://www.receitaws.com.br/v1/cnpj/" + cnpj + '/?callback=?', function (data) {
                    
                    if (!data.erro) {
                        $('.wc_nome').val(data.nome);
                        $('.wc_fantasia').val(data.fantasia);
                        $('.wc_telefone').val(data.telefone.substr(0, 14));
                        $('.wc_logradouro').val(data.logradouro);
                        $('.wc_numero').val(data.numero);
                        $('.wc_complemento').val(data.complemento);
                        $('.wc_bairro').val(data.bairro);
                        $('.wc_cep').val(data.cep.replace('.', ''));
                        $('.wc_municipio').val(data.municipio);
                        $('.wc_uf').val(data.uf);
                        $('.wc_country').val('31');
                    } else {
                        Trigger("<div class='trigger trigger_ajax trigger_error' >CNPJ não encontrado na Receita Federal.</div>");
                        $(".wc_getCnpj").focus();
                        clearCnpjData();
                    }
                }, 'json');
            }
        }
    });
    
    //OTIMIZA TITULO DOS PRODUTOS (FACEBOOK/GOOGLE - LIMIT)
    $(".title_optimize").on("keyup click", function () {
        var caracteresDigitados = $(this).val().length;
        
        if (parseInt(caracteresDigitados) <= 65) {
            $(".chars").css('background-color', 'lawngreen').text(caracteresDigitados + ' ✅ Otimizado!');
        } else {
            $(".chars").css('background-color', 'yellow').text(caracteresDigitados + ' ⚠️ Pode Melhorar!');
        }
    });
    
    //CONTADOR DE CARACTERES (SEO - LIMIT)
    $(".subtitle_optimize").on("keyup click", function () {
        //var limite = $(this).attr('maxlength');
        var caracteresDigitados = $(this).val().length;
        //var caracteresRestantes = (parseInt(limite) - parseInt(caracteresDigitados));
        $(".caracteres").text(caracteresDigitados);
        
        if (parseInt(caracteresDigitados) <= 155) {
            $(".caracteres").css('background-color', 'lawngreen').text(caracteresDigitados + ' ✅ Otimizado!');
        } else {
            $(".caracteres").css('background-color', 'yellow').text(caracteresDigitados + ' ⚠️ Pode Melhorar!');
        }
        
    });
    
    //CONTADOR DE CARACTERES (SEO - LIMIT)
    $(".contador").on("keyup click", function () {
        let limite = $(this).attr('maxlength');
        let caracteresDigitados = $(this).val().length;
        
        let caracteresRestantes = (parseInt(limite) - parseInt(caracteresDigitados));
        $(".caracteres").text(caracteresRestantes);
    });
    
});

//############## ON READY
$(document).ready(function () {
    
    //############## IFRAME RESET
    $(".htmlchars p iframe").wrap("<div style='--aspect-ratio: 16/9;'></div>");
    $("iframe#mediaview").wrap("<div style='--aspect-ratio: 16/9;'></div>");
    
    //Coloca asterisco Vermelho nos Inputs required
    $(":not(input[typeof='radio'], input[typeof='checkbox']):required").prev().prepend('<b class="font_red">* </b>');
    
    //Muda a Classe do Pai dos input[type='radio']:: label.label_check (de fa fa-circle para  icon-radio-checked) caso esteja :checked
    $("form input[type='radio']").each(function (index, element) {
        if ($(this).is(':checked')) {
            $(this).parent().removeClass('icon-radio-unchecked').addClass(' icon-radio-checked');
            $(this).parent().siblings().not("span").removeClass(' icon-radio-checked').addClass('icon-radio-unchecked');
        }
    });
    
    //Muda a Classe do Pai dos input[type='checkbox']:: label.label_check (de fa fa-square para icon-checkbox-checked) caso esteja :checked
    /*$("form input[type='checkbox']:not()").each(function (index, element) {
        if ($(this).is(':checked')) {
            $(this).parent().removeClass('icon-checkbox-unchecked').addClass('icon-checkbox-checked');
            //REMOVE A CLASSE DO BOTÃO ON/OFF
            if ($(this).parent().hasClass('switch')) {
                $(this).parent().removeClass('icon-checkbox-checked');
            }
        }
    });*/
    
    //READY: OTIMIZA TITULO DOS PRODUTOS (FACEBOOK/GOOGLE - LIMIT)
    if ($(".title_optimize").length) {
        //let limite = $(".title_optimize").attr('maxlength');
        let caracteresDigitados = $(".title_optimize").val().length;
        //let caracteresRestantes = (parseInt(limite) - parseInt(caracteresDigitados));
        
        var limitChars = $(".chars").attr('maxlength');
        var counterChar = $(".chars").val();
        var resultChars = (parseInt(limitChars) - parseInt(counterChar.length));
        
        if (caracteresDigitados <= 65) {
            $(".chars").css('background-color', 'lawngreen').text(caracteresDigitados + ' ✅ Otimizado!');
        } else {
            $(".chars").css('background-color', 'yellow').text(caracteresDigitados + ' ⚠️ Pode Melhorar!');
        }
    }
    
    //READY: OTIMIZA SEO DOS PRODUTOS (FACEBOOK/GOOGLE - LIMIT)
    if ($(".subtitle_optimize").length) {
        //let limite = $(".subtitle_optimize").attr('maxlength');
        let caracteresDigitados = $(".subtitle_optimize").val().length;
        //let caracteresRestantes = (parseInt(limite) - parseInt(caracteresDigitados));
        
        var limitChars = $(".subtitle_optimize").attr('maxlength');
        var counterChar = $(".subtitle_optimize").val();
        var resultChars = (parseInt(limitChars) - parseInt(counterChar.length));
        
        if (caracteresDigitados <= 151) {
            $(".caracteres").css('background-color', 'lawngreen').text(caracteresDigitados + ' ✅ Otimizado!');
        } else {
            $(".caracteres").css('background-color', 'yellow').text(caracteresDigitados + ' ⚠️ Pode Melhorar!');
        }
    }
});

function validarCNPJ(cnpj) {
    
    cnpj = cnpj.replace(/[^\d]+/g, '');
    
    if (cnpj == '') {
        return false;
    }
    
    if (cnpj.length != 14) {
        return false;
    }
    
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" || cnpj == "44444444444444" || cnpj == "55555555555555" || cnpj == "66666666666666" || cnpj == "77777777777777" || cnpj == "88888888888888" || cnpj == "99999999999999") {
        return false;
    }
    
    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) {
            pos = 9;
        }
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) {
        return false;
    }
    
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) {
            pos = 9;
        }
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1)) {
        return false;
    }
    
    return true;
}
