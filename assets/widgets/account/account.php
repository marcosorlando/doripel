<?php

use App\Conn\Read;
use App\Helpers\Check;

$AccountAction = \trim(\strip_tags((string)($URL[1] ?? '')));
$AccountBaseUI = BASE . '/conta';

echo "<link rel='stylesheet' href='" . BASE . "/assets/widgets/account/account.css'/>";
echo "<link rel='stylesheet' href='" . BASE . "/assets/bootcss/fonticon.css'/>";
echo '<script src="' . BASE . '/assets/jquery.form.js"></script>';
echo '<script src="' . BASE . '/assets/widgets/account/account.js"></script>';

// LOGIN
if ('' === $AccountAction || '0' === $AccountAction || 'login' === $AccountAction) {
    // REDIRECT IF LOGIN
    if (!empty($_SESSION['userLogin'])) {
        \header(\sprintf('Location: %s/home', $AccountBaseUI));

        exit;
    }

    echo "<article class='login_box'>";
    echo '<header>';
    echo '<h1>' . SITE_NAME . '!</h1>';
    echo '<p>Informe seu E-mail e senha para logar-se!</p>';
    echo '</header>';

    require __DIR__ . '/login.form.php';
    echo '</article>';
}

// RECOVER
if ('recuperar' === $AccountAction) {
    // REDIRECT IF LOGIN
    if (!empty($_SESSION['userLogin'])) {
        \header(\sprintf('Location: %s/home', $AccountBaseUI));

        exit;
    }

    echo "<article class='login_box'>";
    echo '<header>';
    echo '<h1>Recuperar Senha!</h1>';
    echo '<p>Informe seu e-mail abaixo para recuperar sua senha!</p>';
    echo '</header>';

    require __DIR__ . '/recover.form.php';
    echo '</article>';
}

// CADASTRO
if ('cadastro' === $AccountAction) {
    echo "<article class='login_box recover_pass'>";
    echo '<header>';
    echo '<h1>Cadastre-se!</h1>';
    echo '<p>Informe seus dados para criar sua conta!</p>';
    echo '</header>';

    require __DIR__ . '/create.form.php';
    echo '</article>';
}

// NEWPASS
if ('nova-senha' === $AccountAction) {
    // REDIRECT IF LOGIN
    if (!empty($_SESSION['userLogin'])) {
        \header(\sprintf('Location: %s/home', $AccountBaseUI));

        exit;
    }

    echo "<article class='login_box recover_pass'>";
    echo '<header>';
    echo '<h1>Criar Nova Senha!</h1>';
    echo '<p>Informe e repita uma nova senha abaixo para continuar!</p>';
    echo '</header>';

    $wcRecoverPassword = \filter_input(INPUT_COOKIE, 'wc_recover_passtowd');

    if (empty($URL[2]) || !$wcRecoverPassword) {
        $AccountRecoverError = Check::ajaxErro(
            'Não foi possível obter sua conta. Favor tente novamente!',
            E_USER_WARNING
        );

        require __DIR__ . '/recover.form.php';
    } else {
        $AccountRecoverUser = \explode('pass', (string)$URL[2]);
        $AccountRecoverUserMail = (empty($AccountRecoverUser[0]) ? null : \base64_decode($AccountRecoverUser[0]));
        $AccountRecoverUserPass = (empty($AccountRecoverUser[1]) ? null : $AccountRecoverUser[1]);
        if (empty($AccountRecoverUserMail) || (null === $AccountRecoverUserPass || '' === $AccountRecoverUserPass || '0' === $AccountRecoverUserPass)) {
            $AccountRecoverError = Check::ajaxErro(
                'Não foi possível obter sua conta. Favor tente novamente!',
                E_USER_WARNING
            );

            require __DIR__ . '/recover.form.php';
        } else {
            if (empty($Read)) {
                $Read = new Read();
            }

            $Read->fullRead(
                'SELECT user_id FROM ' . DB_USERS . ' WHERE user_email = :email AND user_password = :pass',
                \sprintf('email=%s&pass=%s', $AccountRecoverUserMail, $AccountRecoverUserPass)
            );
            if (!$Read->getResult()) {
                $AccountRecoverError = Check::ajaxErro(
                    'Não foi possível obter sua conta. Favor tente novamente!',
                    E_USER_WARNING
                );

                require __DIR__ . '/recover.form.php';
            } else {
                $_SESSION['userRecoverId'] = $Read->getResult()[0]['user_id'];

                require __DIR__ . '/newpass.form.php';
            }
        }
    }

    echo '</article>';
}

// DASHBOARD
$AccViews = ['home', 'dados', 'pedidos', 'pedido', 'enderecos', 'contato'];
if (\in_array($AccountAction, $AccViews)) {
    // REDIRECT IF LOGIN
    if (empty($_SESSION['userLogin'])) {
        \header(\sprintf('Location: %s/restrito', $AccountBaseUI));

        exit;
    }

    \extract($_SESSION['userLogin']);

    require __DIR__ . '/account.sidebar.php';
    echo "<article class='account_box'>";

    require \sprintf('views/%s.wc.php', $AccountAction);
    echo '</article>';
}

// LOGOFF
if ('sair' === $AccountAction) {
    echo "<article class='login_box'>";
    echo '<header>';
    echo '<h1>Volte Logo</h1>';
    echo '<p>Sua conta foi desconectada com sucesso!</p>';
    echo '</header>';

    require __DIR__ . '/login.form.php';
    echo '</article>';
    unset($_SESSION['userLogin']);
}

// RESTRICT
if ('restrito' === $AccountAction) {
    echo "<article class='login_box'>";
    echo '<header>';
    echo '<h1>Acesso Restrito!</h1>';
    echo '<p>Antes é preciso logar para acessar sua conta!</p>';
    echo '</header>';

    require __DIR__ . '/login.form.php';
    echo '</article>';
    unset($_SESSION['userLogin']);
}
