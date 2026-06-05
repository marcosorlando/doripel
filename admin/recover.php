<?php

use App\Helpers\Check;

ob_start();
session_start();

require __DIR__ . '/../vendor/autoload.php';

$Cookie = filter_input(INPUT_COOKIE, 'workcontrol', FILTER_VALIDATE_EMAIL);
?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Bem-vindo(a) ao <?php
            echo ADMIN_NAME; ?> - Recuperar Senha!</title>
        <meta name="description" content="<?php
        echo ADMIN_DESC; ?>"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>

        <link rel="shortcut icon" href="_img/favicon.png"/>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet'
              type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Source+Code+Pro:300,500' rel='stylesheet' type='text/css'>
        <link rel="base" href="<?php
        echo BASE; ?>/admin/">

        <link rel="stylesheet" href="../assets/bootcss/fonticon.min.css"/>
        <link rel="stylesheet" href="_css/reset.css"/>
        <link rel="stylesheet" href="_css/workcontrol.css"/>
    </head>
    <body class="login">
    <div class="container login_container">
        <div class="login_box">
            <img class="login_logo" alt="<?php
            echo ADMIN_NAME; ?>" title="<?php
            echo ADMIN_NAME; ?>" src="_img/work_icon.png"/>
            <form class="login_form" name="work_login" action="" method="post" enctype="multipart/form-data">
                <!--<div class="trigger trigger_ajax trigger_info m_botton">
                </div>-->
                <?= Check::ajaxErro(
                    '<span>Informe seu e-mail abaixo. Você receberá um
					link para
					recuperar sua senha!</span>',
                    E_USER_NOTICE
                ); ?>
                <div class="callback_return m_botton"></div>
                <input type="hidden" name="callback" value="Login">
                <input type="hidden" name="callback_action" value="admin_recover">

                <label class="label">
                    <span class="legend">Seu E-mail:</span>
                    <input type="email" name="user_email" value="<?php
                    echo $Cookie ?? ''; ?>" placeholder="E-mail:" required/>
                </label>

                <img class="form_load none" style="float: right; margin-top: 3px; margin-left: 10px;"
                     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                <button class="btn btn_green fl_right icon-key2">Obter Nova Senha!</button>
                <div class="clear"></div>
            </form>
            <p class="login_link"><a href="./">&larrhk; Logar-se!</a></p>
        </div>
    </div>

    <div class="login_bg"></div>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/jquery.form.js"></script>
    <script src="_js/workcontrol.js"></script>
    </body>
    </html>
<?php
ob_end_flush();
