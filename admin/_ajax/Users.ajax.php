<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Email;
use App\Models\Upload;

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_USERS;

if ((!APP_USERS) || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo \json_encode($jSON);

    exit;
}

\usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Users';
$PostData = \filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();
    // AUTO INSTANCE OBJECT UPDATE
    $Update ??= new Update();
    // AUTO INSTANCE OBJECT DELETE
    $Delete ??= new Delete();
    $Upload = new Upload('../../uploads/');

    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $UserId = $PostData['user_id'];
            unset($PostData['user_id'], $PostData['user_thumb']);

            $Read->fullRead(
                'SELECT user_id FROM '.DB_USERS.' WHERE user_email = :email AND user_id != :id',
                \sprintf('email=%s&id=%s', $PostData['user_email'], $UserId)
            );
            if ($Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>OPSS:</b> Olá %s. O e-mail <b>%s</b> já está cadastrado na conta de outro usuário!',
                        $_SESSION['userLogin']['user_name'],
                        $PostData['user_email']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Read->fullRead(
                    'SELECT user_id FROM '.DB_USERS.' WHERE user_document = :dc AND user_id != :id',
                    \sprintf('dc=%s&id=%s', $PostData['user_document'], $UserId)
                );
                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            '<b>OPSS:</b> Olá %s. O CPF <b>%s</b> já está cadastrado na conta de outro usuário!',
                            $_SESSION['userLogin']['user_name'],
                            $PostData['user_document']
                        ),
                        E_USER_WARNING
                    );
                } else {
                    if (true != Check::cpf($PostData['user_document'])) {
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                '<b>OPSS:</b> Olá %s. O CPF <b>%s</b> informado não é válido!',
                                $_SESSION['userLogin']['user_name'],
                                $PostData['user_document']
                            ),
                            E_USER_WARNING
                        );
                        echo \json_encode($jSON);

                        return;
                    }

                    if (!empty($_FILES['user_thumb'])) {
                        $UserThumb = $_FILES['user_thumb'];
                        $Read->fullRead('SELECT user_thumb FROM '.DB_USERS.' WHERE user_id = :id', 'id='.$UserId);
                        if (
                            $Read->getResult() && (\file_exists(
                                '../../uploads/'.$Read->getResult()[0]['user_thumb']
                            ) && !\is_dir('../../uploads/'.$Read->getResult()[0]['user_thumb']))
                        ) {
                            \unlink('../../uploads/'.$Read->getResult()[0]['user_thumb']);
                        }

                        $Upload->image(
                            $UserThumb,
                            $UserId.'-'.Check::name(
                                $PostData['user_name'].$PostData['user_lastname']
                            ).'-'.\time(),
                            600
                        );
                        if ($Upload->getResult()) {
                            $PostData['user_thumb'] = $Upload->getResult();
                        } else {
                            $jSON['trigger'] = Check::ajaxErro(
                                \sprintf(
                                    "<b class='icon-image'>ERRO AO ENVIAR FOTO:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como foto!",
                                    $_SESSION['userLogin']['user_name']
                                ),
                                E_USER_WARNING
                            );
                            echo \json_encode($jSON);

                            return;
                        }
                    }

                    if (!empty($PostData['user_password'])) {
                        if (\strlen((string) $PostData['user_password']) >= 5) {
                            $PostData['user_password'] = \hash('sha512', (string) $PostData['user_password']);
                        } else {
                            $jSON['trigger'] = Check::ajaxErro(
                                \sprintf(
                                    '<b>ERRO DE SENHA:</b> Olá %s, a senha deve ter no mínimo 5 caracteres para ser redefinida!',
                                    $_SESSION['userLogin']['user_name']
                                ),
                                E_USER_WARNING
                            );
                            echo \json_encode($jSON);

                            return;
                        }
                    } else {
                        unset($PostData['user_password']);
                    }

                    if ($UserId == $_SESSION['userLogin']['user_id']) {
                        if ($PostData['user_level'] != $_SESSION['userLogin']['user_level']) {
                            $jSON['trigger'] = Check::ajaxErro(
                                \sprintf(
                                    '<b>PERFIL ATUALIZADO COM SUCESSO:</b> Olá %s, seus dados foram atualizados com sucesso! Seu nível de usuário não foi alterado pois não é permitido atualizar o próprio nível de acesso!',
                                    $_SESSION['userLogin']['user_name']
                                )
                            );
                        } else {
                            $jSON['trigger'] = Check::ajaxErro(
                                \sprintf(
                                    '<b>PERFIL ATUALIZADO COM SUCESSO:</b> Olá %s, seus dados foram atualizados com sucesso!',
                                    $_SESSION['userLogin']['user_name']
                                )
                            );
                        }

                        $SesseionRenew = true;
                        unset($PostData['user_level']);
                    } elseif ($PostData['user_level'] > $_SESSION['userLogin']['user_level']) {
                        $PostData['user_level'] = $_SESSION['userLogin']['user_level'];
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                '<b>TUDO CERTO:</b> Olá %s. O usuário %s %s foi atualizado com sucesso! Você não pode criar usuários com nível de acesso maior que o seu. Então o nível gravado foi ',
                                $_SESSION['userLogin']['user_name'],
                                $PostData['user_name'],
                                $PostData['user_lastname']
                            ) . Check::getWcLevel($PostData['user_level']) . '!</p>'
                        );
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                '<b>TUDO CERTO:</b> Olá %s. O usuário %s %s foi atualizado com sucesso!',
                                $_SESSION['userLogin']['user_name'],
                                $PostData['user_name'],
                                $PostData['user_lastname']
                            )
                        );
                    }

                    $PostData['user_datebirth'] = (empty($PostData['user_datebirth']) ? null : Check::nascimento(
                        $PostData['user_datebirth']
                    ));

                    // ATUALIZA USUÁRIO
                    $Update->exeUpdate(DB_USERS, $PostData, 'WHERE user_id = :id', 'id='.$UserId);
                    if (!empty($SesseionRenew)) {
                        $Read->exeRead(DB_USERS, 'WHERE user_id = :id', 'id='.$UserId);
                        if ($Read->getResult()) {
                            $_SESSION['userLogin'] = $Read->getResult()[0];
                        }
                    }
                }
            }

            break;

        case 'delete':
            $UserId = $PostData['del_id'];
            $Read->exeRead(DB_USERS, 'WHERE user_id = :user', 'user='.$UserId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>USUÁRIO NÃO EXISTE:</b> Olá %s, você tentou deletar um usuário que não existe ou já foi removido!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                \extract($Read->getResult()[0]);
                if ($user_id == $_SESSION['userLogin']['user_id']) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            '<b>OPPSSS:</b> Olá %s, por questões de segurança, o sistema não permite que você remova sua própria conta!',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                } elseif ($user_level > $_SESSION['userLogin']['user_level']) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            '<b>PERMISSÃO NEGADA:</b> Desculpe %s, mas %s tem acesso superior ao seu. Você não pode remove-lo!',
                            $_SESSION['userLogin']['user_name'],
                            $user_name
                        ),
                        E_USER_WARNING
                    );
                } else {
                    $Delete->exeDelete(DB_USERS_ADDR, 'WHERE user_id = :user', 'user='.$user_id);

                    // COMMENT CONTROL
                    $Read->fullRead('SELECT id FROM '.DB_COMMENTS.' WHERE user_id = :user', 'user='.$user_id);
                    if ($Read->getResult()) {
                        // RESPONSES REMOVE
                        foreach ($Read->getResult() as $DelId) {
                            $Delete->exeDelete(DB_COMMENTS, 'WHERE alias_id = :in', 'in='.$DelId['id']);
                        }

                        // COMMENT REMOVE
                        $Delete->exeDelete(DB_COMMENTS, 'WHERE user_id = :user', 'user='.$user_id);
                        $Delete->exeDelete(DB_COMMENTS_LIKES, 'WHERE user_id = :user', 'user='.$user_id);
                    }

                    if (\file_exists('../../uploads/'.$user_thumb) && !\is_dir('../../uploads/'.$user_thumb)) {
                        \unlink('../../uploads/'.$user_thumb);
                    }

                    $Delete->exeDelete(DB_USERS, 'WHERE user_id = :user', 'user='.$user_id);
                    $jSON['trigger'] = Check::ajaxErro('USUÁRIO REMOVIDO COM SUCESSO!');
                    $jSON['redirect'] = 'dashboard.php?wc=users/home';
                }
            }

            break;

        case 'addr_add':
            $AddrId = $PostData['addr_id'];
            unset($PostData['addr_id']);

            $Update->exeUpdate(DB_USERS_ADDR, $PostData, 'WHERE addr_id = :addr', 'addr='.$AddrId);
            $jSON['trigger'] = Check::ajaxErro('<b>ENDEREÇO ATUALIZADO COM SUCESSO!</b>');

            break;

        case 'addr_delete':
            $Delete->exeDelete(DB_USERS_ADDR, 'WHERE addr_id = :addr', 'addr='.$PostData['del_id']);
            $jSON['sucess'] = true;

            break;

        case 'block_user':
            // ADD NOTE
            $Read->exeRead(DB_USERS, 'WHERE user_id = :user', 'user='.$PostData['admin_id']);
            $AdminName = $Read->getResult()[0]['user_name'].' '.$Read->getResult()[0]['user_lastname'];
            $NoteBlock = [
                'user_id' => $PostData['user_id'],
                'admin_id' => $PostData['admin_id'],
                'note_text' => '<b class=\'font_red\'>Usuário bloqueado</b> Motivo: '.$PostData['user_blocking_reason'],
                'note_datetime' => \date('Y-m-d H:i:s'),
            ];

            $Create->exeCreate(DB_USERS_NOTES, $NoteBlock);

            // BLOCK USER
            $Block = [
                'user_blocking_reason' => $PostData['user_blocking_reason'],
            ];
            $Update->exeUpdate(DB_USERS, $Block, 'WHERE user_id = :user', 'user='.$PostData['user_id']);

            // SEND NOTIFICATION
            $Read->linkResult(DB_USERS, 'user_id', $PostData['user_id']);
            $Student = $Read->getResult()[0];

            require __DIR__.'/../../_ead/wc_ead.email.php';
            $MailBody = "
                    <p style='font-size: 1.4em;'>Olá {$Student['user_name']},</p>
                    <p>Este e-mail é para informar que sua conta foi <b>bloqueada</b> na nossa Escola Online.</p>
                    <p>Analise o motivo do bloqueio abaixo:</p>
                    <p>{$Block['user_blocking_reason']}</p>
                    <p>Se acredita que sua conta foi bloqueada de forma equivocada, não deixe de responder este e-mail!</p>
                ";

            $MailContent = \str_replace('#mail_body#', $MailBody, $MailContent);
            $Email = new Email();
            $Email->enviarMontando(
                'Sua conta foi suspensa da escola online!',
                $MailContent,
                MAIL_SENDER,
                MAIL_USER,
                \sprintf('%s %s', $Student['user_name'], $Student['user_lastname']),
                $Student['user_email']
            );

            $jSON['redirect'] = 'dashboard.php?wc=teach/students_gerent&id='.$PostData['user_id'];
            $jSON['success'] = true;
            $jSON['clear'] = true;

            break;

        case 'unblock_user':
            // ADD NOTE
            $Read->exeRead(DB_USERS, 'WHERE user_id = :user', 'user='.$PostData['admin_id']);
            $AdminName = $Read->getResult()[0]['user_name'].' '.$Read->getResult()[0]['user_lastname'];
            $NoteBlock = [
                'user_id' => $PostData['user_id'],
                'admin_id' => $PostData['admin_id'],
                'note_text' => '<b class=\'font_green\'>Usuário desbloqueado!</b> Motivo: '.$PostData['user_blocking_reason'],
                'note_datetime' => \date('Y-m-d H:i:s'),
            ];

            $Create->exeCreate(DB_USERS_NOTES, $NoteBlock);

            // BLOCK USER
            $Block = [
                'user_blocking_reason' => null,
            ];
            $Update->exeUpdate(DB_USERS, $Block, 'WHERE user_id = :user', 'user='.$PostData['user_id']);

            // SEND NOTIFICATION
            $Read->linkResult(DB_USERS, 'user_id', $PostData['user_id']);
            $Student = $Read->getResult()[0];

            require __DIR__.'/../_tpl/Mail.email.php';
            $MailBody = "
                    <p style='font-size: 1.4em;'>Olá {$Student['user_name']},</p>
                    <p>Este e-mail é para informar que sua conta foi <b>desbloqueada</b> na nossa Escola Online.</p>
                    <p>Seja bem vindo de volta!</p>
                    <p>Se tiver qualquer problema, não deixe de responder este e-mail!</p>
                ";

            $MailContent = \str_replace('#mail_body#', $MailBody, $MailContent);
            $Email = new Email();
            $Email->enviarMontando(
                'Sua conta foi desbloqueada na escola online!',
                $MailContent,
                MAIL_SENDER,
                MAIL_USER,
                \sprintf('%s %s', $Student['user_name'], $Student['user_lastname']),
                $Student['user_email']
            );

            $jSON['redirect'] = 'dashboard.php?wc=teach/students_gerent&id='.$PostData['user_id'];
            $jSON['success'] = true;
            $jSON['clear'] = true;

            break;

        case 'note_draft':
            $Draft = ['note_status' => 1];
            $Update->exeUpdate(DB_USERS_NOTES, $Draft, 'WHERE note_id = :id', 'id='.$PostData['del_id']);
            $jSON['success'] = true;

            break;

        case 'note_add':
            $Note = [
                'user_id' => $PostData['user_id'],
                'admin_id' => $PostData['admin_id'],
                'note_text' => $PostData['note_text'],
                'note_datetime' => \date('Y-m-d H:i:s'),
            ];

            $Create->exeCreate(DB_USERS_NOTES, $Note);

            // GET NOTES USER
            $Read->exeRead(
                DB_USERS_NOTES,
                'WHERE user_id = :user AND note_status IS NULL ORDER BY note_datetime DESC',
                'user='.$PostData['user_id']
            );
            if ($Read->getResult()) {
                $ContentDiv = '';

                foreach ($Read->getResult() as $Note) {
                    $Read->linkResult(DB_USERS, 'user_id', $Note['admin_id'], 'user_id, user_name, user_lastname');
                    $UserName = $Read->getResult()[0]['user_name'].' '.$Read->getResult()[0]['user_lastname'];
                    $DateNote = \date('d/m/Y H:i', \strtotime((string) $Note['note_datetime']));
                    $ContentDiv .= "<article class='student_gerent_home_anotation' id='".$Note['note_id']."'>
                        <span class='icon-cross icon-notext student_gerent_home_anotation_remove j_delete_action_confirm' callback='Users' callback_action='note_draft' id='".$Note['note_id']."' rel='student_gerent_home_anotation'></span>
                        <div class='student_gerent_home_anotation_content icon-pushpin'>
                            ".\nl2br((string) $Note['note_text'])."
                            <p class='icon-calendar'>".$DateNote.' por '.$UserName.'</p>
                        </div>
                    </article>';
                }
            }

            $jSON['content'] = ['.j_content_note' => $ContentDiv];
            $jSON['success'] = true;
            $jSON['clear'] = true;

            break;

        case 'list_notes_all':
            // GET NOTES USER
            $Read->exeRead(
                DB_USERS_NOTES,
                'WHERE user_id = :user ORDER BY note_datetime DESC',
                'user='.$PostData['user_id']
            );
            if ($Read->getResult()) {
                $ContentDiv = '';

                foreach ($Read->getResult() as $Note) {
                    $Read->linkResult(DB_USERS, 'user_id', $Note['admin_id'], 'user_id, user_name, user_lastname');
                    $UserName = $Read->getResult()[0]['user_name'].' '.$Read->getResult()[0]['user_lastname'];
                    $DateNote = \date('d/m/Y H:i', \strtotime((string) $Note['note_datetime']));
                    $ContentDiv .= "<article class='student_gerent_home_anotation' id='".$Note['note_id']."'>
                        <span class='icon-cross icon-notext student_gerent_home_anotation_remove j_delete_action_confirm' callback='Users' callback_action='note_draft' id='".$Note['note_id']."' rel='student_gerent_home_anotation'></span>
                        <div class='student_gerent_home_anotation_content icon-pushpin'>
                            ".\nl2br((string) $Note['note_text'])."
                            <p class='icon-calendar'>".$DateNote.' por '.$UserName.'</p>
                        </div>
                    </article>';
                }
            }

            $jSON['content'] = ['.j_content_note' => $ContentDiv];
            $jSON['success'] = true;

            break;
    }

    // RETORNA O CALLBACK
    if ($jSON) {
        echo \json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo \json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
