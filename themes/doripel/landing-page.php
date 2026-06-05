<?php

use App\Conn\Read;

    if (!$Read){
        $Read = new Read;
    }

    $Read->exeRead(DB_LANDING_PAGES, "WHERE page_name = :nm", "nm={$URL[0]}");
    if (!$Read->getResult()){
        require REQUIRE_PATH . '/404.php';

        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>
<!--<link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700" rel="stylesheet">-->
<link href="<?= INCLUDE_PATH; ?>/lp/fixedform.css" rel="stylesheet">
<script type="application/javascript" src="<?= INCLUDE_PATH; ?>/lp/js/lp.js"></script>

<style type="text/css"> /* BASE */
    #conversion header {display: block;margin: 0;padding: 30px 30px 20px;}

    .none {display: none;}
    .error {
        border: 1px solid #f72c00 !important;
        -webkit-box-shadow: inset 0px 0px 2px #f72c00, 0px 0px 5px #f72c00 !important;
        -moz-box-shadow: inset 0px 0px 2px #f72c00, 0px 0px 5px #f72c00 !important;
        box-shadow: inset 0px 0px 2px #f72c00, 0px 0px 5px #f72c00 !important;
    }
    .sucess{border: 1px solid green !important;
     -webkit-box-shadow: inset 0px 0px 2px #06C431, 0px 0px 5px #06C431 !important;
        -moz-box-shadow: inset 0px 0px 2px #06C431, 0px 0px 5px #06C431 !important;
        box-shadow: inset 0px 0px 2px #06C431, 0px 0px 5px #06C431 !important;
    }

    #conversion header h3 {display: block;font-size: 24px;font-weight: bold;text-align: left;margin: 0 0 10px 0;padding: 0;}

    #conversion header h4 {display: block;font-size: 20px;font-weight: bold;text-align: left;margin: 0 0 10px 0;padding: 0;}

    #conversion header p {display: block;font-size: 16px;line-height: 1.2em;margin: 0 0 10px 0;padding: 0;}

    #conversion section {display: block;margin: 0;padding: 30px 30px 20px;}

    #conversion .social-conversion {padding-bottom: 0 !important;}

    #conversion .social-conversion + section {padding-top: 20px !important;}

    #conversion-modal .modal-content section p, #conversion section p {display: block;font-size: 12px;line-height: 1em;margin: 0;padding: 0 0 20px 0;}

    #conversion-modal .modal-content section p.notice, #conversion section p.notice {font-size: 12px;}

    /* FIELDS */
    #conversion section form div.field, #conversion-modal .modal-content section form div.field {margin: 0;padding: 0 0 12px 0;}

    #conversion section form div.field div, #conversion-modal .modal-content section form div.field div {clear: both;width: 100%;margin: 0 0 5px 0;}

    #conversion-modal .modal-content section form input, #conversion-modal .modal-content section form select, #conversion-modal .modal-content section form textarea, #conversion-modal .modal-content section form .select2-choice, #conversion section form input, #conversion section form button, #conversion section form select, #conversion section form textarea, #conversion section form .select2-choice {display: inline-block;background-color: #FFFFFF;vertical-align: middle;font-size: 18px;line-height: 20px;width: 100%;height: 36px;margin: 0;padding: 5px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;}

    #conversion-modal .modal-content section form label, #conversion section form label {display: block;min-height: 25px;margin: 0;padding: 0 0 5px 0; color: <?=(!empty($form_text_color) ? $form_text_color : '#f1f1f1');?>}

    #conversion-modal .modal-content section form select, #conversion section form select {width: 100%;height: 36px;line-height: 36px;}

    #conversion-modal .modal-content section form textarea, #conversion section form textarea {height: 104px;}

    #conversion-modal .modal-content section form input[type=radio], #conversion-modal .modal-content section form input[type=checkbox], #conversion section form input[type=radio], #conversion section form input[type=checkbox] {width: inherit;height: inherit;margin-top: 0 !important;margin-right: 8px;}

    #conversion-modal .modal-content section form input[type=radio] + label, #conversion-modal .modal-content section form input[type=radio] + label + label, #conversion-modal .modal-content section form input[type=checkbox] + label, #conversion section form input[type=radio] + label, #conversion section form input[type=radio] + label + label, #conversion section form input[type=checkbox] + label {display: block;width: auto;padding: 5px 0 4px 28px;}

    /* SOCIAL PAY */
    #conversion section div#pay-area {padding: 16px 0 0 0;}

    #conversion section div#pay-area p {font-size: 16px;}

    /* CALL TO ACTION */
    #conversion-modal .modal-content section div.actions, #conversion section div.actions {text-align: center;padding: 16px 0 8px 0;}

    #conversion-modal .modal-content section div.actions button.call_button, #conversion-modal .modal-content section div.actions a.call_button, #conversion section div.actions button.call_button, #conversion section div.actions a.call_button {display: block;cursor: pointer;height: auto;text-align: center;text-decoration: none;font-weight: bold;font-size: 20px;line-height: 1.2em;white-space: normal;vertical-align: middle;margin: 2px auto 24px auto;padding: 15px 20px 17px 20px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;}

    #conversion-modal .modal-content section div.actions img, #conversion section div.actions img {width: auto !important;}

    /* ERROR */
    #conversion-modal .modal-content section form label.error, #conversion section form label.error {display: none !important;}

    #conversion-modal .modal-content section form input.error, #conversion-modal .modal-content section form textarea.error, #conversion-modal .modal-content section form .select2-container.error .select2-choice, #conversion-modal .modal-content section form select.error, #conversion section form input.error, #conversion section form textarea.error, #conversion section form .select2-container.error .select2-choice, #conversion section form select.error {background-color: #FFDDDD;}

    #conversion-modal .modal-content section div#error-container, #conversion section div#error-container {display: none;background-color: #FFDDDD;margin: 0 0 24px 0;padding: 16px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;}

    #conversion-modal .modal-content section div#error-container p, #conversion section div#error-container p {color: #463A33;text-align: center;font-size: 16px;margin: 0;padding: 0;}

    /* SELECT 2 */
    .select2-container .select2-choice {background: #FFFFFF;}

    .select2-container .select2-choice .select2-arrow {background: none;border: none;}

    .select2-container .select2-choice .select2-arrow b {background-position: 0 5px;}

    .hidden {display: none;}

    #conversion-modal .modal-content header {display: none;}

    #conversion-modal .modal-content section form input, #conversion-modal .modal-content section form select, #conversion-modal .modal-content section form textarea, #conversion-modal .modal-content section form .select2-choice {border: 1px solid #CCC;}

    /* MEDIA QUERY */
    @media screen and (min-width: 760px) {
        #conversion {-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;}

        #conversion header {-webkit-border-radius: 15px 15px 0px 0px;-moz-border-radius: 15px 15px 0px 0px;border-radius: 15px 15px 0px 0px;}

        #conversion section {-webkit-border-radius: 0px 0px 15px 15px;-moz-border-radius: 0px 0px 15px 15px;border-radius: 0px 0px 15px 15px;}

        #conversion-modal .modal-content {width: 600px;margin: 30px auto;}
    }</style>
<style type="text/css"> /* BASE */
    *, ::before, ::after {margin: 0;padding: 0;outline: none;border: none;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}

    header, footer nav, section, article, figure {display: block;}

    html, body {font-family: 'Ubuntu', Arial, sans-serif;font-size: 16px;line-height: 1.6em;height: 100%;width: 100%;}

    h1 {font-size: 2.15em;line-height: 1.4em;margin-bottom: .5em;}

    h2 {font-size: 1.5em;line-height: 1.4em;margin-bottom: .5em;}

    h3 {font-family: 'Ubuntu', Arial, sans-serif;font-weight: bold;font-size: 1.5em;line-height: 1.4em;margin-bottom: .5em;}

    h4 {font-size: 1.15em;line-height: 1.3em;margin-bottom: .5em;}

    h5 {font-size: 1.2em;line-height: 1.4em;margin-bottom: .5em;}

    p {font-family: 'Ubuntu', Arial, sans-serif;margin-bottom: .5em;}

    img {max-width: 100%;height: auto;}

    em, i {font-style: italic;}

    strong, b {font-weight: bold;}

    ul, ol {margin-bottom: .5em;}

    ul li {list-style: initial;list-style-position: inside;}

    ol li {list-style: initial;list-style-position: inside;list-style-type: decimal;}

    .l-box {padding: 1.8em;}

    #logo img {width: 140px;margin: 0;}

    #header-and-image, #content {word-break: break-word;}

    #second-text-block .l-box {padding-top: 0;}

    /* STYLE */
    body {font-family: 'Ubuntu', sans-serif;}

    #logo, #header-and-image {text-align: center;}

    #header-and-image header {margin-bottom: 30px;}

    #header-and-image header h1 {font-weight: bold;}

    #header-and-image header, #header-and-image figure img {vertical-align: middle;}

    /* FORM */

    #fixed-form #conversion header {padding-bottom: 0;}

    #fixed-form #conversion header h1, #fixed-form #conversion header h2 {font-size: 25px;font-weight: bold;text-transform: uppercase;}

    #fixed-form #conversion header h3 {font-size: 23px;color: <?=(!empty($form_text_color) ? $form_text_color : '#f1f1f1');?>;font-weight: 400;text-transform: uppercase;text-align: center;text-shadow: 2px 2px 5px rgba(0,0,0,0.3);}

    #conversion-modal .modal-content section div.actions input.call_button, #fixed-form #conversion .actions .call_button {border: none;}

    #fixed-form #conversion .actions .call_button:hover {-webkit-transition: all .2s ease-in-out;-moz-transition: all .2s ease-in-out;transition: all .2s ease-in-out;}

    /* COLOR */
    html {background-color: #F1F4F5;}

    #logo {background-color: <?=(!empty($page_header_color) ? $page_header_color : '#FFFFFF');?>;}

    #header-and-image {background-color: #009EAA;}

    #header-and-image h1 {color: #FFFFFF;}

    #header-and-image h2 {color: #FFFFFF;}

    #content {color: #4D4D4D;}

    .color-hack, #fixed-form {background-color: <?=(!empty($form_bg_color) ? $form_bg_color : '#333333');?>;}


    #conversion-modal .modal-content section div.actions button.call_button, #fixed-form #conversion .actions .call_button {background-color: <?= $page_btn_bg_color ?>;color: <?= $page_btn_text_color ?>;}

    #conversion-modal .modal-content section div.actions button.call_button:hover, #fixed-form #conversion .actions .call_button:hover {background-color: <?= $page_btn_bg_color_hover ?>;border-bottom: 5px solid rgba(0,0,0,0.3);}

    #fixed-form #conversion header h1, #fixed-form #conversion header h2 {color: #FFFFFF;}

    #conversion section {color: #FFFFFF;}

    /* MEDIA QUERY */
    @media screen and (min-width: 48em) {
        #second-text-block .l-box {padding-top: 1.8em;}
    }

    @media screen and (min-width: 64em) {
        .container {width: 100%;max-width: 1300px;margin: 0 auto;}

        #logo, #header-and-image {text-align: left;}

        #header-and-image header, #header-and-image figure {display: inline-block;}

        #header-and-image header {width: 50%;padding-right: 30px;margin-bottom: 0;}

        #header-and-image figure {width: 40%;}

        .color-hack, #fixed-form {width: 500px;height: 100%;top: 0;right: 0;position: fixed;overflow-y: auto;display: block;max-height: 100%;}

        #logo, #header-and-image, #content {margin-right: 500px;}
    }</style>
<body style="background: <?= $page_footer_color ?>">
<div class="color-hack"></div>
<div id="logo">
    <div class="container">
        <div class="l-box">
            <img src="<?= $page_logo ? BASE . "/tim.php?src=uploads/landingpages/{$page_logo}&w=&h=60" : INCLUDE_PATH . "/images/logo.png"; ?>" alt="<?= SITE_ADDR_NAME; ?> - logotipo">
        </div>
    </div>
</div>
<section id="header-and-image" style="background: <?= $page_box_color ?> url(<?= BASE . '/uploads/landingpages/' . $page_cover; ?>) no-repeat center center ;">
    <div class="container">
        <div class="pure-u-1">
            <div class="l-box">
                <header>
                    <?= $page_content; ?>
                </header>
                <figure>
                    <?= $page_mockup ? "<img src= " . BASE . "/uploads/landingpages/{$page_mockup} alt='{$page_title}' title='{$page_title}' >" : ''; ?>
                </figure>
            </div>
        </div>
    </div>
</section>
<section id="fixed-form">
    <div id="conversion">
        <header>
            <h3><?= $form_title; ?></h3>
        </header>
        <section>
            <form class="j_landing_page" method="post" action="" enctype="multipart/form-data">
                <div class="callback_return"></div>
                <input type="hidden" name="callback" value="Lp"/>
                <input type="hidden" name="callback_action" value="manage"/>
                <input type="hidden" name="callback_title" value="<?= $page_title; ?>"/>
                <input type="hidden" name="callback_link" value="<?= $page_name; ?>"/>
                <input type="hidden" name="lead_conversion" value="<?= $page_title; ?>"/>
                <input type="hidden" name="page_destino" value="<?= $page_destino; ?>"/>
                <input type="hidden" name="page_mockup" value="<?= $page_mockup; ?>"/>

                <div id="error-container">
                    <p>Preencha corretamente os campos marcados</p>
                </div>
                <div class="field">
                    <label>Nome*</label><input type="text" name="lead_name" value="" class="form-control required" tabindex="1" autofocus required>
                </div>
                <div class="field">
                    <label>Email*</label><input type="email" name="lead_email" value="" class="form-control required" tabindex="2" required>
                </div>
                <div class="field">
                    <label>Cidade*</label><input type="text" name="lead_city" value="" class="form-control  required" tabindex="3" required>
                </div>
                <div class="field">
                    <label>Profissão*</label><input type="text" name="lead_job_title" value="" class="form-control  required" tabindex="4" required>
                </div>
                <!-- security start -->
                <div class="field">
                    <label>Realize a Soma*</label>
                    <input type="number" name="senderHuman" id="senderHuman" class="input-md input-rounded form-control" placeholder="" required>
                    <input type="hidden" name="checkHuman_a" id="checkHuman_a">
                    <input type="hidden" name="checkHuman_b" id="checkHuman_b">
                </div>
                <!-- security end -->
                <div class="actions">
                    <button type="submit" class="call_button"><?= (!empty($page_btn_text) ? $page_btn_text : 'Acessar Conteúdo') ?>
                        <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="<?= INCLUDE_PATH; ?>/lp/ajax-loader.gif"/>
                    </button>
                </div>
                <p class="notice">Prometemos não utilizar suas informações de contato para enviar qualquer tipo de SPAM.</p>
            </form>
        </section>
    </div>
</section>
<section id="content" style="background: <?= $page_bg_color ?>">
    <div class="container pure-g">
        <div id="first-text-block" class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <?= $page_coluna1; ?>
            </div>
        </div>

        <div id="second-text-block" class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <?= $page_coluna2; ?>
            </div>
        </div>
    </div>
</section>
</body>
<script>
    $(function () {
        //SELETOR, EVENTO/EFEITO, CALLBACK, AÇÃO
        $('.j_landing_page').submit(function () {
            var form = $(this);
            var data = $(this).serialize();

            $.ajax({
                url: '<?= INCLUDE_PATH; ?>/lp/ajax/Lp.ajax.php',
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
                                window.setTimeout(function () {
                                    $(window.document.location).attr('href', data.redirect);
                                }, 5000);

                            } else {
                                Trigger(data.trigger);
                            }
                        }
                        if(data.field){
                            $("[name='" + data.field + "']").removeClass('sucess').addClass('error').focus().val('');
                             $("input[name!='" + data.field + "']").removeClass('error');
                        }else{
                            $("input").removeClass('error').addClass('sucess').focus();
                        }
                    });
                }
            });
            return false;
        });
    });
</script>
