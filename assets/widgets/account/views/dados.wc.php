<?php

if (empty($_SESSION['userLogin'])) {
    exit('<h1 style="padding: 50px 0; text-align: center; font-size: 3em; font-weight: 300; color: #C63D3A">Acesso Negado!</h1>');
}
?>

<form name="account_form" autocomplete="off" action="" method="post" enctype="multipart/form-data">
    <div class="account_form_fields">
        <div class="account_form_callback account_form_callback_fixed"></div>

        <label class="file" for="account_user_avatar">
            <b>Minha Foto (<?php
                echo AVATAR_W; ?>x<?php
                echo AVATAR_H; ?>px):</b>
            <input type="file" class="wc_loadimage" id="account_user_avatar" name="user_thumb"/>
        </label>

        <div class="flex-box">
            <label>
                <span>Nome:</span>
                <input name="user_name" value="<?php
                echo $_SESSION['userLogin']['user_name']; ?>" type="text"
                       placeholder="Primeiro Nome:" required/>
            </label>

            <label>
                <span>Sobrenome:</span>
                <input name="user_lastname" value="<?php
                echo $_SESSION['userLogin']['user_lastname']; ?>" type="text"
                       placeholder="Último Nome:" required/>
            </label>
        </div>

        <div class="flex-box">
            <label>
                <span>CPF:</span>
                <?php
                if ($_SESSION['userLogin']['user_document']) { ?>
                    <span class="input"><?php
                        echo $_SESSION['userLogin']['user_document']; ?></span>
                    <?php
                } else { ?>
                    <input class="formCpf" name="user_document" value="" type="text" placeholder="Seu CPF:"/>
                    <?php
                } ?>
            </label>

            <label>
                <span>Gênero:</span>
                <select name="user_genre" required>
                    <option value="" selected disabled>Selecione seu Gênero:</option>
                    <option value="1" <?php
                    echo 1 == $_SESSION['userLogin']['user_genre'] ? 'selected' : ''; ?>>Masculino
                    </option>
                    <option value="2" <?php
                    echo 2 == $_SESSION['userLogin']['user_genre'] ? 'selected' : ''; ?>>Feminino
                    </option>
                </select>
            </label>
        </div>

        <div class="flex-box">
            <label>
                <span>Telefone:</span>
                <input class="formPhone" name="user_telephone" value="<?php
                echo $_SESSION['userLogin']['user_telephone']; ?>"
                       type="text" placeholder="Seu Telefone:"/>
            </label>

            <label>
                <span>Celular:</span>
                <input class="formPhone" name="user_cell" value="<?php
                echo $_SESSION['userLogin']['user_cell']; ?>"
                       type="text" placeholder="Seu Celular:"/>
            </label>
        </div>
        <div class="flex-box">
            <label>
                <span>E-mail:</span>
                <span class="input"><?php
                    echo $_SESSION['userLogin']['user_email']; ?></span>
            </label>
            <label>
                <span>Senha:</span>
                <input name="user_password" type="password" placeholder="Nova senha:"/>
            </label>
        </div>
    </div>

    <input type="hidden" name="action" value="wc_user"/>

    <div class="account_form_actions">
        <button class="btn btn_blue">Atualizar Meus Dados!</button>
        <img alt="Recuperando Senha!" title="Recuperando Senha!" src="<?php
        echo BASE; ?>/assets/widgets/account/load.gif"/>
    </div>
</form>
