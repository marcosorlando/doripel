<?php

    /*
     * ATENÇÃO: PARA SUA SEGURANÇA NÃO ALTERE ESSE ARQUIVO
     * E SEMPRE LICENCIE SEUS DOMÍNIOS AO COLOCALOS ONLINE!
     */

    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_CONFIG_API;
    if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-key">Zen Control® License</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=config/home">Configurações</a>
			<span class="crumb">/</span>
			Licenciamento de Domínio
		</p>
	</div>
</header>
<div class="dashboard_content">
    <?php
        if (\file_exists('dashboard.json')) {
            $getLicense = \file_get_contents('dashboard.json');
            $License = \json_decode($getLicense);

            echo "<div class='licence_box'>"
                . "<span class='icon-checkbox-checked icon-notext font_green auth'></span>"
                . \sprintf(
                    "<p class='title'>Zen Control® Licenciado por <a title='Conferir Profissional' href='https://pro.workcontrol.com.br/?p=%s' target='_blank'>%s %s</a></p>",
                    $License->user_id,
                    $License->user_name,
                    $License->user_lastname
                )
                . \sprintf(
                    "<p class='icon-warning'>Licença exclusiva para o domínio <b>%s</b>.</p>",
                    $License->license_domain
                )
                . \sprintf("<p class='icon-lock key'><b>IP do SERVIDOR:</b>&nbsp;%s</p>", $License->license_request_ip)
                . \sprintf("<p class='icon-key key'><b>CHAVE:</b>&nbsp;%s</p>", $License->license_hash)
                . "<p class='icon-calendar'>Licença: " . \date(
                    'd/m/Y',
                    \strtotime((string)$License->license_date)
                ) . ' | Autenticação: ' . \date(
                    'd/m/Y H\hi',
                    \strtotime((string)$License->license_auth_date)
                ) . \sprintf(
                    ' | Versão: %s</p>',
                    $License->license_version
                )
                . '</div>';
        }
    ?>

	<div class="wc_api_new">
		<form action="" method="post" enctype="multipart/form-data">
			<div style="text-align: center">
				<img class="form_load none" style="margin: 0 auto 20px auto;"
				     alt="Enviando Requisição!" title="Enviando Requisição!"
				     src="_img/load.gif"/>
			</div>
			<input type="hidden" name="callback" value="Api"/>
			<input type="hidden" name="callback_action" value="license"/>

			<div class="flex_box">
				<label class="label">
					<input style="width: 100%; border: 1px solid #ccc;" type="text" name="user_email" value=""
					       placeholder="Seu e-mail Upinside:" required="required"/>
				</label><label class="label">
					<input style="width: 100%; border: 1px solid #ccc;" type="password" name="user_password" value=""
					       placeholder="Sua senha UpInside:" required="required"/>
				</label>
			</div>

			<div class="license">
				<input type='text' name='licene_key' id='licene_key' value=''
				       placeholder='License Key: bdc807-383f8d-83daafb7-ae87d8' required='required'/>

				<button class='btn btn_green'>APLICAR CHAVE</button>
			</div>


		</form>
		<p class="wc_api_new_info">( ! ) Para gerar uma chave de licenciamento acesse <a
					href="https://download.workcontrol.com.br" target="_blank">download.workcontrol.com.br</a>, utilize
			seu e-mail e senha da UpInside para logar-se e gerar a licença!</p>
	</div>
</div>
