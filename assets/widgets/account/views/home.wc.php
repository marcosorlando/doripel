<?php

if (empty($_SESSION['userLogin'])) {
    exit('<h1 style="padding: 50px 0; text-align: center; font-size: 3em; font-weight: 300; color: #C63D3A">Acesso Negado!</h1>');
}

echo "<div class='workcontrol_account_view'>";
echo "<p class='wc_account_title'><span>Meus Dados:</span><p>";
echo "<div class='workcontrol_account_home'>";
echo \sprintf('<p><b>Nome: </b>%s %s</p>', $user_name, $user_lastname);
echo \sprintf('<p><b>E-mail: </b>%s</p>', $user_email);
echo \sprintf('<p><b>CPF: </b>%s</p>', $user_document);
echo '<p><b>Sexo: </b>'.(1 == $user_genre ? 'Masculino' : (2 == $user_genre ? 'Feminino' : null)).'</p>';
echo \sprintf('<p><b>Telefone: </b>%s</p>', $user_telephone);
echo \sprintf('<p><b>Celular: </b>%s</p>', $user_cell);
echo '<p><b>Cadastro em '.\date('d/m/Y H\hi', \strtotime((string) $user_registration)).'</b></p>';
echo '</div>';
echo "<div class='wc_spacer'></div>";
echo '</div>';
