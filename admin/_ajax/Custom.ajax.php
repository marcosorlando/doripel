<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';

if (empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < 6) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

// ADMIN
$Admin = $_SESSION['userLogin'];

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Custom';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

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

    // SELECIONA AÇÃO
    switch ($Case) {
        // HELLO BAR CREATE / UPDATE
        case 'hellobar_update':
            // NIVEL DE ACESSO
            if ($Admin['user_level'] < LEVEL_WC_HELLO) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
                    E_USER_ERROR
                );

                break;
            }

            $HelloId = $PostData['hello_id'];
            $HelloRule = $PostData['hello_rule'];
            unset($PostData['hello_id'], $PostData['hello_rule'], $PostData['hello_cover']);

            $Read->exeRead(DB_HELLO, 'WHERE hello_id = :id', 'id=' . $HelloId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPPSSS:</b> Desculpe %s, mas você tentou atualizar um Pop-Up que não existe!',
                        $Admin['user_name']
                    ),
                    E_USER_ERROR
                );

                break;
            }

            extract($Read->getResult()[0]);

            $HelloUpload = (empty($_FILES['hello_cover']) ? null : $_FILES['hello_cover']);
            if (empty($hello_image) && empty($HelloUpload)) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Olá %s, você precisa enviar a imagem do hello bar!',
                        $Admin['user_name']
                    ),
                    E_USER_WARNING
                );

                break;
            }

            if (!empty($HelloUpload)) {
                if (
                    !empty($hello_image) && file_exists('../../uploads/' . $hello_image) && !is_dir(
                        '../../uploads/' . $hello_image
                    )
                ) {
                    unlink('../../uploads/' . $hello_image);
                }

                $Upload = new Upload('../../uploads/');
                $Upload->image($HelloUpload, Check::name($PostData['hello_title']), IMAGE_W);
                if (!$Upload->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>Não foi possível enviar a imagem:</b> Selecione imagens JPG, ou PNG para a sua hello!',
                        E_USER_WARNING
                    );

                    break;
                }

                $PostData['hello_image'] = $Upload->getResult();
            }

            // CAMPOS EM BRANCO
            if (in_array('', $PostData)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>Erro ao cadastrar:</b> Para criar uma hellobar, preencha todos os campos do formulário!',
                    E_USER_WARNING
                );

                break;
            }

            // RETORNAR O RULE PARA O WC_VIEW QUANDO TIVER
            $PostData['hello_status'] = (empty($PostData['hello_status']) ? 0 : 1);
            $PostData['hello_rule'] = (empty($HelloRule) ? null : $HelloRule);
            $PostData['hello_start'] = Check::data($PostData['hello_start']);
            $PostData['hello_end'] = Check::data($PostData['hello_end']);
            $PostData['hello_date'] = date('Y-m-d H:i:s');

            $Update->exeUpdate(DB_HELLO, $PostData, 'WHERE hello_id = :id', 'id=' . $HelloId);
            $jSON['trigger'] = Check::ajaxErro(
                '<b>Hellobar atualizada:</b> Sua hellobar foi atualizada e já pode ser exibida em seu site!'
            );

            break;

        // HELLO BAR DELETE
        case 'hellobar_delete':
            $HelloId = $PostData['del_id'];

            $Read->fullRead('SELECT hello_image FROM ' . DB_HELLO . ' WHERE hello_id = :hello', 'hello=' . $HelloId);
            if ($Read->getResult()) {
                $hello_image = $Read->getResult()[0]['hello_image'];
                if (file_exists('../../uploads/' . $hello_image) && !is_dir('../../uploads/' . $hello_image)) {
                    unlink('../../uploads/' . $hello_image);
                }
            }

            $Delete->exeDelete(DB_HELLO, 'WHERE hello_id = :id', 'id=' . $HelloId);
            $jSON['success'] = true;

            break;
    }

    // RETORNA O CALLBACK
    if ($jSON) {
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
