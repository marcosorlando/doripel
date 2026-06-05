<?php

use App\Conn\Create;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Email;

session_start();

require __DIR__ . '/../../vendor/autoload.php';

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Login';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
if (null === $PostData || false === $PostData) {
    $PostData = [];
}

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // ELIMINA CÓDIGOS
    $PostData = array_map(static fn($v) => is_string($v) ? strip_tags($v) : $v, $PostData);

    // SELECIONA AÇÃO
    switch ($Case) {
        // LOGIN
        case 'admin_login':
            if (in_array('', $PostData, true)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>OPPSSS:</b> Informe seu e-mail e senha para logar!',
                    E_USER_NOTICE
                );
            } elseif (
                !Check::email((string)($PostData['user_email'] ?? '')) || !filter_var(
                    (string)($PostData['user_email'] ?? ''),
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
            } elseif (strlen((string)($PostData['user_password'] ?? '')) < 5) {
                $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> Senha informada não é compatível!', E_USER_NOTICE);
            } else {
                $Read = new Read();
                $Read->fullRead('SELECT user_id FROM ' . DB_USERS . ' WHERE user_level >= :lv', 'lv=6');
                if (null === $Read->getResult() || [] === $Read->getResult()) {
                    $AdminWorkControl = [
                        'user_id' => 1,
                        'user_thumb' => 'mail/work_icon.png',
                        'user_name' => 'Admin',
                        'user_lastname' => 'Work Control',
                        'user_email' => 'admin@workcontrol.com.br',
                        'user_password' => hash('sha512', 'admin'),
                        'user_registration' => date('Y-m-d H:i:s'),
                        'user_level' => 10,
                    ];
                    $Create = new Create();
                    $Create->exeCreate(DB_USERS, $AdminWorkControl);
                }

                $Read->fullRead(
                    'SELECT user_id FROM ' . DB_USERS . ' WHERE user_email = :email',
                    'email=' . $PostData['user_email']
                );
                if (null === $Read->getResult() || [] === $Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>ERRO:</b> E-mail informado não é cadastrado!',
                        E_USER_WARNING
                    );
                } else {
                    // CRIPTOGRAFA A SENHA
                    $PostData['user_password'] = hash('sha512', (string)$PostData['user_password']);

                    $Read->fullRead(
                        'SELECT user_id FROM ' . DB_USERS . ' WHERE user_email = :email AND user_password = :pass',
                        sprintf('email=%s&pass=%s', $PostData['user_email'], $PostData['user_password'])
                    );
                    if (null === $Read->getResult() || [] === $Read->getResult()) {
                        $jSON['trigger'] = Check::ajaxErro('<b>ERRO:</b> E-mail e senha não conferem!', E_USER_ERROR);
                    } else {
                        $Read->exeRead(
                            DB_USERS,
                            'WHERE user_email = :email AND user_password = :pass AND user_level >= :level',
                            sprintf('email=%s&pass=%s&level=6', $PostData['user_email'], $PostData['user_password'])
                        );
                        if (null === $Read->getResult() || [] === $Read->getResult()) {
                            $jSON['trigger'] = Check::ajaxErro(
                                '<b>ERRO:</b> Você não tem permissão para acessar o painel!',
                                E_USER_ERROR
                            );
                        } else {
                            $Remember = (isset($PostData['user_remember']) ? 1 : null);
                            if ($Remember) {
                                setcookie('workcontrol', (string)$PostData['user_email'], time() + 2592000, '/');
                            } else {
                                setcookie('workcontrol', '', 60, '/');
                            }

                            /* if (!EAD_STUDENT_MULTIPLE_LOGIN) {
                                 $wc_ead_login_cookie = hash("sha512", time());
                                 setcookie('wc_ead_login', $wc_ead_login_cookie, time() + 2592000, '/');

                                 $UpdateUserLogin = ['user_lastaccess' => date('Y-m-d H:i:s'), 'user_login' => time(), 'user_login_cookie' => $wc_ead_login_cookie];
                                 $Update = new Update;
                                 $Update->exeUpdate(DB_USERS, $UpdateUserLogin, "WHERE user_id = :user", "user={$Read->getResult()[0]['user_id']}");
                               }
                            */

                            $_SESSION['userLogin'] = $Read->getResult()[0];
                            $jSON['trigger'] = Check::ajaxErro(
                                sprintf(
                                    '<b>Olá %s,</b> Seja bem-vindo(a) de volta!',
                                    $Read->getResult()[0]['user_name']
                                )
                            );
                            $jSON['redirect'] = 'dashboard.php?wc=home';
                        }
                    }
                }
            }

            break;

        case 'admin_recover':
            if (
                isset($PostData['user_email']) && Check::email((string)$PostData['user_email']) && filter_var(
                    (string)$PostData['user_email'],
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $Read = new Read();
                $Read->fullRead(
                    'SELECT user_id, user_name, user_email, user_password FROM ' . DB_USERS . ' WHERE user_email = :email AND user_level >= :level',
                    sprintf('email=%s&level=6', $PostData['user_email'])
                );
                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>OPPSSS:</b> E-mail não cadastrado ou não tem permissão para o painel!',
                        E_USER_WARNING
                    );
                } else {
                    $CodeReset = sprintf(
                        'user_id=%s&user_email=%s&user_password=%s',
                        $Read->getResult()[0]['user_id'],
                        $Read->getResult()[0]['user_email'],
                        $Read->getResult()[0]['user_password']
                    );
                    $CodePass = base64_encode($CodeReset);

                    require __DIR__ . '/../_tpl/Mail.email.php';
                    $BodyMail = "
                    <p style='font-size: 1.5em;'>Olá {$Read->getResult()[0]['user_name']}, recupere sua senha do " . ADMIN_NAME . "!</p>
                    <p>Caso não tenha feito essa solicitação. Por favor ignore esse e-mail e nenhuma ação será tomada quanto aos dados de acesso!</p>
                    <p>Ou para criar uma nova senha de acesso <a title='Criar Nova Senha' href='" . BASE . "/admin/newpass.php?key={$CodePass}'>CLIQUE AQUI!</a>!</p>
                    <p>Você será redirecionado para uma página onde poderá definir uma nova senha de acesso ao painel! Cuide bem dos seus dados.</p>
                    ";
                    $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);

                    $Email = new Email();
                    $Email->enviarMontando(
                        'Recupere sua Senha',
                        $Mensagem,
                        ADMIN_NAME,
                        MAIL_USER,
                        $Read->getResult()[0]['user_name'],
                        $Read->getResult()[0]['user_email']
                    );

                    $_SESSION['trigger_login'] = Check::ajaxErro(
                        sprintf(
                            '<b>SUCESSO:</b> Olá %s, confira o link enviado em seu e-mail para recuperar sua senha!',
                            $Read->getResult()[0]['user_name']
                        )
                    );
                    $jSON['trigger'] = Check::ajaxErro('<b>SUCESSO:</b> O link foi enviado para seu e-mail!');
                    $jSON['redirect'] = './';
                }
            } else {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>OPPSSS:</b> Infor seu e-mail para recuperar a senha!',
                    E_USER_WARNING
                );
            }

            break;

        case 'admin_newpass':
            if (!isset($_SESSION['RecoverPass']) || '' === $_SESSION['RecoverPass']) {
                // Sem código de recuperação válido, nada a fazer aqui;
            } elseif (in_array('', $PostData, true)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>OPPSSS:</b> Para redefinir uma nova senha, você deve informar e repetir a mesma logo abaixo!',
                    E_USER_NOTICE
                );
            } elseif (strlen($PostData['user_password']) < 5) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ALERTA:</b> Informe uma senha com no mínimo 5 caracteres!',
                    E_USER_WARNING
                );
            } elseif ($PostData['user_password'] != $PostData['user_password_re']) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ALERTA:</b> Você deve informar e repetir a mesma senha. Você informou senhas diferentes!',
                    E_USER_WARNING
                );
            } else {
                $DecodeValidate = base64_decode((string)$_SESSION['RecoverPass'], true);
                parse_str($DecodeValidate, $Validate);

                $Read = new Read();
                $Read->fullRead(
                    'SELECT user_name, user_id FROM ' . DB_USERS . ' WHERE user_id = :id AND user_email = :email AND user_password = :pass',
                    sprintf(
                        'id=%s&email=%s&pass=%s',
                        (string)$Validate['user_id'],
                        (string)$Validate['user_email'],
                        (string)$Validate['user_password']
                    )
                );
                if ($Read->getResult()) {
                    $UpdatePass = ['user_password' => hash('sha512', $PostData['user_password'])];
                    $Update = new Update();
                    $Update->exeUpdate(
                        DB_USERS,
                        $UpdatePass,
                        'WHERE user_id = :id',
                        'id=' . $Read->getResult()[0]['user_id']
                    );

                    $_SESSION['trigger_login'] = Check::ajaxErro(
                        sprintf(
                            '<b>INFO:</b> Olá %s, para logar informe seu e-mail e sua NOVA SENHA de acesso!',
                            $Read->getResult()[0]['user_name']
                        )
                    );
                    $jSON['trigger'] = Check::ajaxErro('<b>SUCESSO:</b> Sua senha foi redefinida!');
                    $jSON['redirect'] = './';
                } else {
                    $_SESSION['trigger_login'] = Check::ajaxErro(
                        '<b>OPPSSS:</b> Você tentou recuperar sua senha com um código de acesso expirado!',
                        E_USER_ERROR
                    );
                    $jSON['trigger'] = Check::ajaxErro('<b>ERRO:</b> Não foi possível redefinir!', E_USER_WARNING);
                    $jSON['redirect'] = './';
                }
            }

            break;
    }

    // RETORNA O CALLBACK
    if (null !== $jSON && [] !== $jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
