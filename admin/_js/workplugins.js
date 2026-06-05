$(function () {
    //############## TINYMCE
    if ($('.work_mce').length) {
        wc_tinyMCE();
    }

    //############## HIGHLIGHT
    if ($('*[class="brush: php;"]').length) {
        $("head").append('<link rel="stylesheet" href="../assets/highlight.min.css">');
        $.getScript('../assets/highlight.min.js', function () {
            $('*[class="brush: php;"]').each(function (i, block) {
                hljs.highlightBlock(block);
            });
        });
    }

    //############## AIR DATEPICKER
    $('.jwc_datepicker').datepicker({
        language: 'pt-BR',
        autoClose: true
    });

    if ($('.work_mce_basic').length) {
        wc_tinyMCE_basic();
    }

    //############## MASK INPUT
    $(".formDate").mask("99/99/9999");
    $(".formTime").mask("99/99/9999 99:99");
    $(".formCep").mask("99999-999");
    $(".formCpf").mask("999.999.999-99");

    $('.formPhone').focusout(function () {
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if (phone.length > 10) {
            element.mask("(99) 99999-999?9");
        } else {
            element.mask("(99) 9999-9999?9");
        }
    }).trigger('focusout');

});

//FUNCTION TINYMCE
function wc_tinyMCE() {
    tinyMCE.init({
        selector: "textarea.work_mce",
        language: 'pt_BR',
        menubar: false,
        theme: "modern",
        height: 500,
        skin: 'light',
        entity_encoding: "raw",
        theme_advanced_resizing: true,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor media"
        ],
        toolbar: "styleselect | forecolor | backcolor | pastetext | removeformat |  bold | italic | underline | strikethrough | bullist | numlist | alignleft | aligncenter | alignright |  link | unlink | upinsideimage | upinsidevideo | media |  outdent | indent | preview | code | fullscreen",
        content_css: "_css/tinyMCE.css?wc=true",
        style_formats: [
            {title: 'Normal', block: 'p'},
            {title: 'Título 3', block: 'h3'},
            {title: 'Título 4', block: 'h4'},
            {title: 'Check-list h5', block: 'h5'},
            {title: 'Entre Aspas h6', block: 'h6'},
            {title: 'Código', block: 'pre', classes: 'brush: php;'}
        ],
        link_class_list: [
            {title: 'None', value: ''},
            {title: 'Blue CTA', value: 'btn btn_cta_blue'},
            {title: 'Green CTA', value: 'btn btn_cta_green'},
            {title: 'Yellow CTA', value: 'btn btn_cta_yellow'},
            {title: 'Red CTA', value: 'btn btn_cta_red'}
        ],
        setup: function (editor) {
            // Botão de imagem já existente
            editor.addButton('upinsideimage', {
                title: 'Enviar Imagem',
                icon: 'image',
                onclick: function () {
                    $('.workcontrol_imageupload').fadeIn('fast');
                }
            });

            // ✅ Novo botão para vídeo
            editor.addButton('upinsidevideo', {
                title: 'Inserir Vídeo',
                icon: 'media',
                onclick: function () {
                    $('.workcontrol_videoupload').fadeIn('fast');
                }
            });
        },
        link_title: false,
        target_list: false,
        theme_advanced_blockformats: "h1,h2,h3,h4,h5,h6,p,pre",
        media_dimensions: false,
        media_poster: false,
        media_alt_source: false,
        media_embed: true,
        extended_valid_elements: "a[href|target=_blank|rel|class],video[controls|src|width|height|poster|preload|autoplay]",
        imagemanager_insert_template: '<img class="on-contrast-force-gray" src="{$url}" title="{$title}" alt="{$title}" />',
        image_dimensions: false,
        videomanager_insert_template: '<video controls width="100%"><source src="../uploads/{$PostData[\'video\']}" type="{$NewVideo[\'type\']}"></video>',
        relative_urls: false,
        remove_script_host: false,
        paste_as_text: true
    });
}

//FUNCTION TINYMCE BASIC
function wc_tinyMCE_basic() {
    tinyMCE.init({
        selector: "textarea.work_mce_basic",
        language: 'pt_BR',
        menubar: false,
        theme: "modern",
        height: 400,
        skin: 'light',
        entity_encoding: "raw",
        theme_advanced_resizing: true,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor media"
        ],
        toolbar: "styleselect | forecolor | backcolor | pastetext | removeformat |  bold | italic | underline | strikethrough | bullist | numlist |  link | unlink | fullscreen",
        content_css: "_css/tinyMCE.css",
        style_formats: [
            {title: 'Normal', block: 'p'},
            {title: 'Titulo 3', block: 'h3'},
            {title: 'Titulo 4', block: 'h4'},
            {title: 'Titulo 5', block: 'h5'},
            {title: 'Código', block: 'pre', classes: 'brush: php;'}
        ],
        link_class_list: [
            {title: 'None', value: ''},
            {title: 'Blue CTA', value: 'btn btn_cta_blue'},
            {title: 'Green CTA', value: 'btn btn_cta_green'},
            {title: 'Yellow CTA', value: 'btn btn_cta_yellow'},
            {title: 'Red CTA', value: 'btn btn_cta_red'}
        ],
        link_title: false,
        target_list: false,
        theme_advanced_blockformats: "h1,h2,h3,h4,h5,p,pre",
        media_dimensions: false,
        media_poster: false,
        media_alt_source: false,
        media_embed: false,
        extended_valid_elements: "a[href|target=_blank|rel|class]",
        imagemanager_insert_template: '<img src="{$url}" title="{$title}" alt="{$title}" />',
        image_dimensions: false,
        relative_urls: false,
        remove_script_host: false,
        paste_as_text: true
    });
}
