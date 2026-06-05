<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;

    session_start();

    $getPost = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (empty($getPost) || empty($getPost['action'])) {
        exit('Acesso Negado!');
    }

    $strPost = array_map('strip_tags', $getPost);
    $trimPost = array_map('trim', $strPost);
    $POST = array_map('htmlspecialchars', $trimPost);

    $Action = $POST['action'];
    $jSON = null;
    unset($POST['action']);

    usleep(2000);

    require __DIR__ . '/../../../vendor/autoload.php';
    $Read = new Read();
    $Create = new Create();
    $Update = new Update();

    switch ($Action) {
        // COMMENT ID IN SESSION
        case 'setcomment':
            $Read->fullRead('SELECT id, alias_id FROM ' . DB_COMMENTS . ' WHERE id = :id', 'id=' . $POST['id']);
            if (!$Read->getResult()) {
                $jSON['comment'] = false;
            } else {
                $jSON['comment'] = sprintf("<input type='hidden' name='to' value='%s'/>", $POST['to']);
                if ($Read->getResult() && !empty($Read->getResult()[0]['alias_id'])) {
                    $jSON['comment'] .= sprintf(
                        "<input type='hidden' name='alias_id' value='%s'/>",
                        $Read->getResult()[0]['alias_id']
                    );
                } else {
                    $jSON['comment'] .= sprintf(
                        "<input type='hidden' name='alias_id' value='%s'/>",
                        $Read->getResult()[0]['id']
                    );
                }
            }

            break;

        // COMMENT LIKE
        case 'like':
            if (empty($_SESSION['userLogin'])) {
                $jSON['login'] = true;
            } else {
                $Read->fullRead(
                    'SELECT id FROM ' . DB_COMMENTS_LIKES . ' WHERE user_id = :user AND comm_id = :comm',
                    sprintf('user=%s&comm=%s', $_SESSION['userLogin']['user_id'], $POST['id'])
                );
                if ($Read->getResult()) {
                    $jSON['liked'] = true;
                } else {
                    $CreateLike = ['user_id' => $_SESSION['userLogin']['user_id'], 'comm_id' => $POST['id']];
                    $Create->exeCreate(DB_COMMENTS_LIKES, $CreateLike);
                    $jSON['like'] = '<b>EU</b>';
                }
            }

            break;

        // COMMENTS RESPONSE
        case 'response':
            if (empty($_SESSION['userLogin'])) {
                $jSON['login'] = true;
            } else {
                if (!empty($POST['to'])) {
                    $UserResponder = $POST['to'];
                    unset($POST['to']);
                }

                $POST['user_id'] = $_SESSION['userLogin']['user_id'];
                $POST['post_id'] = (!empty($_SESSION['comm']['post_id']) && empty($POST['alias_id']) ? $_SESSION['comm']['post_id'] : null);
                $POST['page_id'] = (!empty($_SESSION['comm']['page_id']) && empty($POST['alias_id']) ? $_SESSION['comm']['page_id'] : null);
                $POST['pdt_id'] = (!empty($_SESSION['comm']['pdt_id']) && empty($POST['alias_id']) ? $_SESSION['comm']['pdt_id'] : null);
                $POST['created'] = date('Y-m-d H:i:s');
                $POST['interact'] = date('Y-m-d H:i:s');
                $POST['status'] = (COMMENT_MODERATE !== 0 ? 2 : 3);
                $POST['likes'] = 0;

                $Create->exeCreate(DB_COMMENTS, $POST);
                $ThisComment = $Create->getResult();

                // SEND E-MAIL TO COMMENT AUTHOR
                if (COMMENT_SEND_EMAIL && !empty($POST['alias_id'])) {
                    $Read->exeRead(DB_COMMENTS, 'WHERE id = :key', 'key=' . $POST['alias_id']);
                    if ($Read->getResult()) {
                        $COMM = $Read->getResult()[0];

                        // GET CONTENT
                        if (!empty($COMM['post_id'])) {
                            $Read->fullRead(
                                'SELECT post_title, post_name FROM ' . DB_POSTS . ' WHERE post_id = :key',
                                'key=' . $COMM['post_id']
                            );
                            $CommTitle = (empty($Read->getResult()[0]['post_title']) ? null : $Read->getResult(
                            )[0]['post_title']);
                            $CommLink = (empty(
                            $Read->getResult()[0]['post_name']
                            ) ? null : BASE . '/artigo/' . $Read->getResult()[0]['post_name']);
                        } elseif (!empty($COMM['page_id'])) {
                            $Read->fullRead(
                                'SELECT page_title, page_name FROM ' . DB_PAGES . ' WHERE page_id = :key',
                                'key=' . $COMM['page_id']
                            );
                            $CommTitle = (empty($Read->getResult()[0]['page_title']) ? null : $Read->getResult(
                            )[0]['page_title']);
                            $CommLink = (empty(
                            $Read->getResult()[0]['page_name']
                            ) ? null : BASE . '/' . $Read->getResult()[0]['page_name']);
                        } elseif (!empty($COMM['pdt_id'])) {
                            $Read->fullRead(
                                'SELECT pdt_title, pdt_name FROM ' . DB_PDT_DORIPEL . ' WHERE pdt_id = :key',
                                'key=' . $COMM['pdt_id']
                            );
                            $CommTitle = (empty($Read->getResult()[0]['pdt_title']) ? null : $Read->getResult(
                            )[0]['pdt_title']);
                            $CommLink = (empty(
                            $Read->getResult()[0]['pdt_name']
                            ) ? null : BASE . '/produto/' . $Read->getResult()[0]['pdt_name']);
                        }

                        // GET AUTHOR COMMENT
                        $Read->fullRead(
                            'SELECT user_id, user_name, user_lastname, user_email FROM ' . DB_USERS . ' WHERE user_id = :key',
                            'key=' . $COMM['user_id']
                        );
                        $CommAuthor = ($Read->getResult() ? $Read->getResult()[0] : null);

                        // SEND E-MAIL TO AUTHOR
                        if (!empty($CommTitle) && !empty($CommLink) && $CommAuthor && $COMM['user_id'] != $_SESSION['userLogin']['user_id']) {
                            require __DIR__ . '/comment.email.php';
                            $BodyMail = "
                            <p>Olá {$CommAuthor['user_name']}, este e-mail rápido é para avisar que <b>{$_SESSION['userLogin']['user_name']} {$_SESSION['userLogin']['user_lastname']}</b> respondeu seu comentário em nosso site!</p>
                            <p>Foi em <a title='Ver {$CommTitle}' target='_blank' href='{$CommLink}'>{$CommTitle}</a> dia " . date(
                                    'd/m/Y H\hi',
                                    strtotime((string)$COMM['created'])
                                ) . ". Responda {$_SESSION['userLogin']['user_name']} no link abaixo...</p>
                            <p><a title='Ver/Responder Comentário' target='_blank' href='{$CommLink}#comment{$ThisComment}'>VER/RESPONDER COMENTÁRIO!</a></p>
                            <p><b>Confira agora mesmo {$CommAuthor['user_name']}. E fique a vontade para responder!</b></p>
                            <p>Obrigado mais uma vez pela interação em nosso site. Sua opinião é muito valiosa para nossa equipe!</p>
                            <p><i>Atenciosamente, " . SITE_NAME . '!</i></p>
                        ';
                            $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                            $SendEmail = new Email();
                            $SendEmail->enviarMontando(
                                'Nova resposta em seu comentário',
                                $Mensagem,
                                SITE_NAME,
                                MAIL_USER,
                                sprintf('%s %s', $CommAuthor['user_name'], $CommAuthor['user_lastname']),
                                $CommAuthor['user_email']
                            );
                        }

                        // SEND E-MAIL TO RESPONDER
                        if (!empty($UserResponder) && $UserResponder != $CommAuthor['user_id'] && $UserResponder != $_SESSION['userLogin']['user_id']) {
                            // GET AUTHOR COMMENT
                            $Read->fullRead(
                                'SELECT user_id, user_name, user_lastname, user_email FROM ' . DB_USERS . ' WHERE user_id = :key',
                                'key=' . $UserResponder
                            );
                            $CommAuthor = ($Read->getResult() ? $Read->getResult()[0] : null);

                            require_once __DIR__ . '/comment.email.php';
                            $BodyMail = "
                            <p>Olá {$CommAuthor['user_name']}, este e-mail rápido é para avisar que <b>{$_SESSION['userLogin']['user_name']} {$_SESSION['userLogin']['user_lastname']}</b> comentou sua resposta em nosso site!</p>
                            <p>Foi em <a title='Ver {$CommTitle}' target='_blank' href='{$CommLink}'>{$CommTitle}</a> dia " . date(
                                    'd/m/Y H\hi',
                                    strtotime((string)$COMM['created'])
                                ) . ". Responda {$_SESSION['userLogin']['user_name']} no link abaixo...</p>
                            <p><a title='Ver/Responder Comentário' target='_blank' href='{$CommLink}#comment{$ThisComment}'>VER/RESPONDER COMENTÁRIO!</a></p>
                            <p><b>Confira agora mesmo {$CommAuthor['user_name']}. E fique a vontade para responder!</b></p>
                            <p>Obrigado mais uma vez pela interação em nosso site. Sua opinião é muito valiosa para nossa equipe!</p>
                            <p><i>Atenciosamente, " . SITE_NAME . '!</i></p>
                        ';
                            $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                            $SendEmail = new Email();
                            $SendEmail->enviarMontando(
                                'Novo comentário em sua resposta',
                                $Mensagem,
                                SITE_NAME,
                                MAIL_USER,
                                sprintf('%s %s', $CommAuthor['user_name'], $CommAuthor['user_lastname']),
                                $CommAuthor['user_email']
                            );
                        }
                    }
                }

                // RETURN COMMENT
                $UserAvatar = (empty($_SESSION['userLogin']['user_thumb']) ? 'admin/_img/no_avatar.jpg' : 'uploads/' . $_SESSION['userLogin']['user_thumb']);
                $CommStars = str_repeat('&starf;', $POST['rank']) . str_repeat('&star;', 5 - $POST['rank']);
                $jSON['response'] = sprintf(
                        "<article class='comments_single comment_response ajax_response' id='comment%s'><div class='comments_single_avatar'><img alt='%s %s' title='%s %s' src='",
                        $ThisComment,
                        $_SESSION['userLogin']['user_name'],
                        $_SESSION['userLogin']['user_lastname'],
                        $_SESSION['userLogin']['user_name'],
                        $_SESSION['userLogin']['user_lastname']
                    ) . BASE . sprintf(
                        "/tim.php?src=%s&w=200&h=200'></div><div class='comments_single_content'><header><h1 class='font_green'><b>√ %s %s</b></h1></header><div class='comments_single_comment'>",
                        $UserAvatar,
                        $_SESSION['userLogin']['user_name'],
                        $_SESSION['userLogin']['user_lastname']
                    ) . nl2br((string)$POST['comment']) . sprintf(
                        "</div><div class='comments_single_ui'><span class='stars'>%s</span><span class='date'>DIA ",
                        $CommStars
                    ) . date('d.m.Y H\hi', strtotime($POST['created'])) . sprintf(
                        "</span></div><div class='comments_single_likes' id='%s'><span><span class='na'>N/A</span></span></div></div></article>",
                        $ThisComment
                    );

                // MODERATE COMMENT TARGET
                if (!empty($POST['alias_id'])) {
                    $UpdateData = ['status' => 3];
                    $Update->exeUpdate(DB_COMMENTS, $UpdateData, 'WHERE id = :alias', 'alias=' . $POST['alias_id']);
                    $jSON['alias'] = true;
                }
            }

            break;

        // ON CHANGE GET USER
        case 'getuser':
            if (empty($POST['email'])) {
                $jSON['trigger'] = Check::ajaxErro('Favor informe seu e-mail para continuar!', E_USER_ERROR);
            } elseif (!Check::email($POST['email']) || !filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) {
                $jSON['trigger'] = Check::ajaxErro('O E-mail informado não tem um formato válido :/', E_USER_ERROR);
            } else {
                $Read->fullRead(
                    'SELECT user_name FROM ' . DB_USERS . ' WHERE user_email = :email',
                    'email=' . $POST['email']
                );
                if (!$Read->getResult()) {
                    $jSON['create'] = true;
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf('Bem-vindo(a) %s. informe sua senha para comentar.', $Read->getResult()[0]['user_name'])
                    );
                    $jSON['login'] = true;
                }
            }

            break;

        // LOGIN USER
        case 'loginuser':
            unset($POST['user_name'], $POST['user_lastname'], $POST['user_code']);
            if (
                empty($POST['user_email']) || !Check::email($POST['user_email']) || !filter_var(
                    $POST['user_email'],
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO LOGAR:</b> Informe seu <b>e-mail</b> para logar e interagir!',
                    E_USER_ERROR
                );
            } elseif (
                empty($POST['user_password']) || strlen($POST['user_password']) < 5 || strlen(
                    $POST['user_password']
                ) > 11
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO LOGAR:</b> Informe sua <b>senha</b> para logar e interagir!',
                    E_USER_ERROR
                );
            } else {
                $PassWord = hash('sha512', $POST['user_password']);
                $Read->exeRead(
                    DB_USERS,
                    'WHERE user_email = :email AND user_password = :pass',
                    sprintf('email=%s&pass=%s', $POST['user_email'], $PassWord)
                );
                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b>ERRO AO LOGAR:</b> E-mail ou senha informados não conferem! Caso tenha esquecido sua senha: <span class='comment_recover_password'>RECUPERAR MINHA SENHA</span>",
                        E_USER_ERROR
                    );
                    $_SESSION['recover']['email'] = $POST['user_email'];
                } else {
                    $_SESSION['userLogin'] = $Read->getResult()[0];
                    $jSON['user'] = true;
                }
            }

            break;

        // CREATE NEW USER
        case 'createuser':
            unset($POST['user_password'], $POST['user_code']);
            if (in_array('', $POST)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>OPPSSS:</b> Informe seu nome e sobrenome para continuar!',
                    E_USER_ERROR
                );
            } elseif (!Check::email($POST['user_email']) || !filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>OPPSSS:</b> O e-mail informado não tem um formato válido!',
                    E_USER_ERROR
                );
            } else {
                $Read->fullRead(
                    'SELECT user_id, user_name FROM ' . DB_USERS . ' WHERE user_email = :email',
                    'email=' . $POST['user_email']
                );
                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf('Bem-vindo(a) %s. informe sua senha para comentar!', $Read->getResult()[0]['user_name'])
                    );
                } else {
                    // CADASTRA USUÁRIO
                    $Password = substr(md5(time()), 0, 6);
                    $POST['user_password'] = hash('sha512', $Password);
                    $POST['user_channel'] = 'Comentários';
                    $POST['user_registration'] = date('Y-m-d H:i:s');
                    $POST['user_level'] = 1;

                    $Create->exeCreate(DB_USERS, $POST);
                    $POST['user_id'] = $Create->getResult();
                    $_SESSION['userLogin'] = $POST;

                    // SEND CREATE ACCOUNT
                    require_once __DIR__ . '/comment.email.php';
                    $BodyMail = "
                    <p style='font-size: 1.3em'>Caro(a) {$POST['user_name']},</p>
                    <p>Antes de mais nada gostaríamos de agradecer pelo seu comentário, sua opinião é muito importante para nós...</p>
                    <p><b>E com isso, dar as boas vindas ao nosso site.</b></p>
                    <p>Uma nova conta foi criada para que você possa ter mais comodidade ao interagir conosco. Segue abaixo os dados de sua conta " . SITE_NAME . "...
                    <p style='font-size: 1.1em'>
                        Login: {$POST['user_email']}<br>
                        Senha: {$Password}
                    </p>";
                    // FOR USER ACCOUNT MANAGER
                    if (ACC_MANAGER !== 0) {
                        $BodyMail .= "
                    <p>Com esses dados você também pode efetuar LOGIN para gerenciar sua conta. Complete seu perfíl em nosso site {$POST['user_name']}...</p>
                    <p><a title='Minha Conta' target='_blank' href='" . BASE . "/conta/login'>ACESSAR MINHA CONTA AGORA!</a></p>
                ";
                    }

                    $BodyMail .= "
                    <p>A partir de agora, sempre que acessar nosso site você pode usar esses dados para identificar-se, e assim ter acesso ao melhor do nosso conteúdo...</p>
                    <p><b>Seja muito bem-vindo(a) {$POST['user_name']}...</b></p>
                    <p><i>Atenciosamente, " . SITE_NAME . '!</i></p>
                ';
                    $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                    $SendEmail = new Email();
                    $SendEmail->enviarMontando(
                        'Seja bem-vindo(a) ' . $POST['user_name'],
                        $Mensagem,
                        SITE_NAME,
                        MAIL_USER,
                        sprintf('%s %s', $POST['user_name'], $POST['user_lastname']),
                        $POST['user_email']
                    );
                    $jSON['user'] = true;
                }
            }

            break;

        // SEND RECOVER
        case 'recoversend':
            $_SESSION['recover']['code'] = substr(md5(time()), 0, 6);
            $jSON['email'] = $_SESSION['recover']['email'];

            $Read->fullRead(
                'SELECT user_email, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_email = :em ',
                'em=' . $_SESSION['recover']['email']
            );
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    "Desculpe. Mas não foi possível enviar o código de acesso!Você pode tentar novamente. <span class='comment_recover_password'>Reenviar Código de acesso!</span>",
                    E_USER_WARNING
                );
            } else {
                $CommAuthor = $Read->getResult()[0];

                // SEND CODE TO LOGIN
                require_once __DIR__ . '/comment.email.php';
                $BodyMail = "
                    <p>Olá {$CommAuthor['user_name']}, você está recebendo esse e-mail pois solicitou um código de acesso a sua conta em nosso site.</p>
                    <p>Caso não tenha solicitado esse código. Por favor nos desculpe pelo incomodo. E apenas ignore este e-mail!</p>
                    <p>Segue seu código de acesso:</p>
                    <p style = 'font-size: 2em;'>{$_SESSION['recover']['code']}</p>
                    <p>Copie esse código e cole no campo de login em nosso site para curtir um comentário, ou enviar o seu!</p>
                    <p><i>Atenciosamente, " . SITE_NAME . '!</i></p>
                    ';
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $SendEmail = new Email();
                $SendEmail->enviarMontando(
                    'Confira seu código de acesso',
                    $Mensagem,
                    SITE_NAME,
                    MAIL_USER,
                    sprintf('%s %s', $CommAuthor['user_name'], $CommAuthor['user_lastname']),
                    $CommAuthor['user_email']
                );
                $jSON['trigger'] = Check::ajaxErro('Um novo código de acesso foi enviado para seu e-mail!');
            }

            break;

        // RECOVER USER
        case 'recoveruser':
            if (empty($POST['user_code'])) {
                $jSON['trigger'] = $jSON['trigger'] = Check::ajaxErro(
                    'Informe o código de acesso para continuar!',
                    E_USER_ERROR
                );
            } elseif (empty($_SESSION['recover']['email']) || empty($_SESSION['recover']['code'])) {
                $jSON['trigger'] = $jSON['trigger'] = Check::ajaxErro(
                    'Não existe um código de acesso para recuperar!',
                    E_USER_ERROR
                );
                $jSON['recover_error'] = true;
            } elseif ($POST['user_code'] != $_SESSION['recover']['code']) {
                $jSON['trigger'] = $jSON['trigger'] = Check::ajaxErro(
                    'O código de acesso não confere. Verifique seu e-mail!',
                    E_USER_ERROR
                );
            } else {
                // READ FROM SESSION
                $Read->exeRead(DB_USERS, 'WHERE user_email = :email ', 'email=' . $_SESSION['recover']['email']);
                if (!$Read->getResult()) {
                    $jSON['trigger'] = $jSON['trigger'] = Check::ajaxErro(
                        'Não foi possível recuperar. Favor tente novamente!',
                        E_USER_ERROR
                    );
                    $jSON['recover_error'] = true;
                } else {
                    $_SESSION['userLogin'] = $Read->getResult()[0];
                    $jSON['user'] = true;
                }
            }

            break;
    }

    echo json_encode($jSON);
