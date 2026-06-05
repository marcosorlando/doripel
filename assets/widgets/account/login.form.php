<form name="account_form" class="ajax_no" action="" method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="action" value="wc_login"/>
    <div class="account_form_fields">
        <div class="account_form_callback"></div>
        <div class="flex-box">
            <label>
                <span>E-mail:</span>
                <input name="user_email" type="email" placeholder="Informe seu E-mail:" required/>
            </label>

            <label>
                <span>Senha:</span>
                <input name="user_password" type="password" placeholder="Informe sua Senha:" required/>
            </label>
        </div>
    </div>

    <div class="account_form_actions">
        <button class="btn btn_blue">Iniciar Sessão!</button>
        <img alt="Efetuando Login!" title="Efetuando Login!" src="<?php
        echo BASE; ?>/assets/widgets/account/load.gif"/>
        <div>&nbsp;</div>
        <a title="Recuperar Senha!" href="<?php
        echo $AccountBaseUI; ?>/recuperar#acc">Esqueci Minha Senha!</a>
        <!--<a class="create" title="Cadastre-se no </* SITE_NAME; */?>!" href="</* $AccountBaseUI; */?>/cadastro#acc">Cadastre-se!</a>-->
    </div>
</form>
