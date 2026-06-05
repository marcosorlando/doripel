<?php

use App\Conn\Create;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Email;
use App\Models\Upload;

\session_start();

$POST = \filter_input_array(INPUT_POST, FILTER_DEFAULT);
if (empty($POST) || empty($POST['action'])) {
    exit('Acesso Negado!');
}

$strPost = \array_map('strip_tags', $POST);
$Action = $POST['action'];
$jSON = null;
unset($POST['action'], $POST['user_level']);

\usleep(2000);

require __DIR__.'/../../../vendor/autoload.php';
$Read ??= new Read();
$Create ??= new Create();
$Update ??= new Update();

switch ($Action) {
    // LOGIN
    case 'wc_login':
        if (\in_array('', $POST)) {
            $jSON['trigger'] = Check::ajaxErro('Favor informe seu E-mail e Senha para logar!', E_USER_WARNING);
        } elseif (!Check::Email($POST['user_email']) || !\filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $jSON['trigger'] = Check::ajaxErro('O E-mail informado não tem um formato válido!', E_USER_WARNING);
        } elseif (\strlen($POST['user_password']) < 5) {
            $jSON['trigger'] = Check::ajaxErro('Sua senha deve conter no mínimo 5 caracteres!', E_USER_WARNING);
        } else {
            $Password = \hash('sha512', $POST['user_password']);
            $Read->exeRead(
                DB_USERS,
                'WHERE user_email = :email AND user_password = :pass',
                \sprintf('email=%s&pass=%s', $POST['user_email'], $Password)
            );
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    'Os dados informados não conferem. Informe seu e-mail e senha!',
                    E_USER_WARNING
                );
            } else {
                $_SESSION['userLogin'] = $Read->getResult()[0];
                $jSON['clear'] = true;

                $jSON['redirect'] = 2 === $_SESSION['userLogin']['user_level'] ? SAC_URL : BASE.'/conta/home#acc';

                $LoginUpdate = ['user_login' => \time(), 'user_lastaccess' => \date('Y-m-d H:i:s')];
                $Update->exeUpdate(
                    DB_USERS,
                    $LoginUpdate,
                    'WHERE user_id = :id',
                    'id='.$Read->getResult()[0]['user_id']
                );
            }
        }

        break;

        // CREATE
    case 'wc_create':
        if (\in_array('', $POST)) {
            $jSON['trigger'] = Check::ajaxErro(
                'Favor preencha todos os campos para criar sua nova conta!',
                E_USER_WARNING
            );
        } elseif (!Check::Email($POST['user_email']) || !\filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $jSON['trigger'] = Check::ajaxErro(
                'Oppsss. O e-mail informado não parece ter um formato válido!',
                E_USER_WARNING
            );
        } elseif (\strlen($POST['user_password']) < 5) {
            $jSON['trigger'] = Check::ajaxErro('Oppsss. Sua senha deve ter no mínimo 5 caracteres!', E_USER_WARNING);
        } else {
            $Read->fullRead(
                'SELECT user_email FROM '.DB_USERS.' WHERE user_email = :email',
                'email='.$POST['user_email']
            );
            if ($Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf('Desculpe, mas o e-mail <b>%s</b> já está cadastrado!', $POST['user_email']),
                    E_USER_ERROR
                );
            } else {
                $POST['user_password'] = \hash('sha512', $POST['user_password']);
                $POST['user_registration'] = \date('Y-m-d H:i:s');
                $POST['user_lastupdate'] = \date('Y-m-d H:i:s');
                $POST['user_lastaccess'] = \date('Y-m-d H:i:s');
                $POST['user_channel'] = 'Cadastro';
                $POST['user_level'] = 1;

                $Create->exeCreate(DB_USERS, $POST);
                $POST['user_id'] = $Create->getResult();
                $_SESSION['userLogin'] = $POST;

                $jSON['trigger'] = Check::ajaxErro(
                    'Seja muito bem vindo ao '.SITE_NAME.\sprintf(' %s!', $POST['user_name'])
                );
                $jSON['redirect'] = BASE.'/conta/home#acc';
            }
        }

        break;

        // RECOVER
    case 'wc_recover':
        if (\in_array('', $POST)) {
            $jSON['trigger'] = Check::ajaxErro('Favor informe seu E-mail para continuar!', E_USER_WARNING);
        } elseif (!Check::Email($POST['user_email']) || !\filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $jSON['trigger'] = Check::ajaxErro('O E-mail informado não tem um formato válido!', E_USER_WARNING);
        } else {
            $Read->exeRead(DB_USERS, 'WHERE user_email = :email', 'email='.$POST['user_email']);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    'O e-mail informado não está cadastrado em nosso site!',
                    E_USER_WARNING
                );
            } else {
                $AccountUser = $Read->getResult()[0];
                $HashRecover = \base64_encode((string) $Read->getResult()[0]['user_email']).'pass'.$Read->getResult(
                )[0]['user_password'];
                $LinkRecover = BASE.('/conta/nova-senha/'.$HashRecover);

                \setcookie(
                    'wc_recover_passtowd',
                    \base64_encode((string) $Read->getResult()[0]['user_email']),
                    \time() + 3600,
                    '/'
                );

                // SEND MAIL RECOVER
                // SEND CODE TO LOGIN
                require_once __DIR__.'/account.email.php';
                $BodyMail = "
                    <p>Olá {$AccountUser['user_name']}, você está recebendo esse e-mail pois solicitou uma nova senha em nosso site.</p>
                    <p>Caso não tenha solicitado essa senha. Por favor nos desculpe pelo incomodo. E apenas ignore este e-mail.</p>
                    <p>Caso contrário:</p>
                    <p><a title='Recuperar Minha Senha' href='{$LinkRecover}#acc'>RECUPERAR MINHA SENHA AGORA!</a></p>
                    <p>Ao clicar no link acima você será redirecionado para criar uma nova senha, e assim recuperar seu acesso!</p>
                    <p><i>Atenciosamente, ".SITE_NAME.'!</i></p>
                    ';
                $Mensagem = \str_replace('#mail_body#', $BodyMail, $MailContent);
                $SendEmail = new Email();
                $SendEmail->enviarMontando(
                    \sprintf('Recupere sua senha %s!', $AccountUser['user_name']),
                    $Mensagem,
                    SITE_NAME,
                    MAIL_USER,
                    \sprintf('%s %s', $AccountUser['user_name'], $AccountUser['user_lastname']),
                    $AccountUser['user_email']
                );
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf('Olá %s. Enviamos os dados de acesso para seu e-mail.', $AccountUser['user_name'])
                );
                $jSON['clear'] = true;
            }
        }

        break;

        // RESET
    case 'wc_newpass':
        if (empty($_SESSION['userRecoverId'])) {
            $jSON['redirect'] = BASE.'/conta/recuperar#acc';
        } elseif (\in_array('', $POST)) {
            $jSON['trigger'] = Check::ajaxErro('Favor informe e repita sua nova senha!', E_USER_WARNING);
        } elseif (\strlen($POST['user_password']) < 5) {
            $jSON['trigger'] = Check::ajaxErro('Sua nova senha deve ter no mínimo 5 caracteres!', E_USER_WARNING);
        } elseif ($POST['user_password'] != $POST['user_password_r']) {
            $jSON['trigger'] = Check::ajaxErro('Você informou duas senhas diferentes!', E_USER_WARNING);
        } else {
            $UpdatePassword = [
                'user_password' => \hash('sha512', $POST['user_password']),
                'user_lastupdate' => \date('Y-m-d H:i:s'),
            ];
            $Update->exeUpdate(DB_USERS, $UpdatePassword, 'WHERE user_id = :id', 'id='.$_SESSION['userRecoverId']);
            $Read->exeRead(DB_USERS, 'WHERE user_id = :id', 'id='.$_SESSION['userRecoverId']);
            if ($Read->getResult()) {
                $_SESSION['userLogin'] = $Read->getResult()[0];
                if (!empty($_SESSION['userRecoverId'])) {
                    unset($_SESSION['userRecoverId']);
                }

                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        "Olá %s, sua senha foi alterada! <a href='",
                        $_SESSION['userLogin']['user_name']
                    ).BASE."/conta/home' title='Acessar Minha Conta!'>Acessar Minha Conta!</a>"
                );
                $jSON['clear'] = true;

                \setcookie(
                    'wc_recover_passtowd',
                    \base64_encode((string) $Read->getResult()[0]['user_email']),
                    \time(),
                    '/'
                );
            } else {
                $jSON['trigger'] = Check::ajaxErro('Você informou duas senhas diferentes!', E_USER_WARNING);
            }
        }

        break;

        // USER
    case 'wc_user':
        if (empty($_SESSION['userLogin']['user_id']) || empty($_SESSION['userLogin']['user_email'])) {
            unset($_SESSION['userLogin']);
            $jSON['redirect'] = BASE.'/conta/sair#acc';
        } elseif (empty($POST['user_name']) || empty($POST['user_lastname']) || empty($POST['user_genre'])) {
            $jSON['trigger'] = Check::ajaxErro(
                \sprintf(
                    'Opppssss %s, você deve preencher os campos obrigatórios (*)!',
                    $_SESSION['userLogin']['user_name']
                ),
                E_USER_WARNING
            );
        } else {
            $UserId = $_SESSION['userLogin']['user_id'];
            $UserEmail = $_SESSION['userLogin']['user_email'];
            unset($POST['user_thumb']);

            if (!empty($_FILES['user_thumb'])) {
                $UserThumb = $_FILES['user_thumb'];
                $Read->fullRead('SELECT user_thumb FROM '.DB_USERS.' WHERE user_id = :id', 'id='.$UserId);
                if (
                    $Read->getResult() && (\file_exists(
                        '../../../uploads/'.$Read->getResult()[0]['user_thumb']
                    ) && !\is_dir('../../../uploads/'.$Read->getResult()[0]['user_thumb']))
                ) {
                    \unlink('../../../uploads/'.$Read->getResult()[0]['user_thumb']);
                }

                $Upload = new Upload('../../../uploads/');
                $Upload->image(
                    $UserThumb,
                    $UserId.'-'.Check::name($POST['user_name'].$POST['user_lastname']),
                    AVATAR_W
                );
                if ($Upload->getResult()) {
                    $POST['user_thumb'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            'Opppssss %s, selecione uma imagem JPG ou PNG para enviar sua foto!',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo \json_encode($jSON);

                    return;
                }
            }

            if (!empty($POST['user_password'])) {
                if (\strlen($POST['user_password']) >= 5) {
                    $POST['user_password'] = \hash('sha512', $POST['user_password']);
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            'Oppsss %s, sua senha deve ter no mínimo 5 caracteres para ser redefinida!',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo \json_encode($jSON);

                    return;
                }
            } else {
                unset($POST['user_password']);
            }

            if (!empty($POST['user_document'])) {
                if (!Check::cpf($POST['user_document'])) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            '<b>Oppsss:</b> %s, o CPF informado não é válido. Favor confira seu CPF para atualizar!',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo \json_encode($jSON);

                    return;
                }

                $Read->fullRead(
                    'SELECT user_document FROM '.DB_USERS.' WHERE user_document = :document AND user_id != :user',
                    \sprintf('document=%s&user=%s', $POST['user_document'], $UserId)
                );
                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            '<b>Oppsss:</b> %s, o CPF informado já está cadastrado em outra conta. Se isso for um erro, favor entre em contato conosco via ',
                            $_SESSION['userLogin']['user_name']
                        ).SITE_ADDR_EMAIL.'!',
                        E_USER_WARNING
                    );
                    echo \json_encode($jSON);

                    return;
                }
            }

            // ATUALIZA USUÁRIO
            $POST['user_lastupdate'] = \date('Y-m-d H:i:s');
            $Update->exeUpdate(DB_USERS, $POST, 'WHERE user_id = :id', 'id='.$UserId);
            $Read->exeRead(DB_USERS, 'WHERE user_id = :id', 'id='.$UserId);
            if ($Read->getResult()) {
                $_SESSION['userLogin'] = $Read->getResult()[0];
            }

            $jSON['trigger'] = Check::ajaxErro(
                \sprintf(
                    '<b>TUDO CERTO:</b> Olá %s, seus dados foram atualizados com sucesso!',
                    $_SESSION['userLogin']['user_name']
                )
            );
        }

        break;
}

echo \json_encode($jSON);
