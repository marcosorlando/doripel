<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_LINKTREE;

$jSON = [];
if (
    !isset($_SESSION['userLogin']['user_level'])
    || (int)$_SESSION['userLogin']['user_level'] < (int)$NivelAcess
) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = [];
$CallBack = 'Linktree';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
if (null === $PostData || false === $PostData) {
    $PostData = [];
}

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

    // ELIMINA CÓDIGOS
    $PostData = array_map(static fn($v) => is_string($v) ? strip_tags($v) : $v, $PostData);

    // SELECIONA AÇÃO
    switch ($Case) {
        // DELETE
        case 'delete':
            $Read->fullRead(
                'SELECT carduser_thumb FROM ' . DB_CARD_USER . ' WHERE carduser_id = :id',
                'id=' . $PostData['del_id']
            );

            if ($Read->getResult() && $Read->getResult()[0]['carduser_thumb'] !== null) {
                extract($Read->getResult()[0]);
                if (
                    $carduser_thumb &&
                    file_exists('../../uploads/linktree/' . $carduser_thumb) &&
                    !is_dir('../../uploads/linktree/' . $carduser_thumb)
                ) {
                    unlink('../../uploads/linktree/' . $carduser_thumb);
                }
            }

            $Delete->exeDelete(DB_CARD_USER, 'WHERE carduser_id = :id', 'id=' . $PostData['del_id']);

            $jSON['trigger'] = Check::ajaxErro(
                '<b>Cartão Removido:</b>Esse CARD foi removido com sucesso!'
            );
            $jSON['redirect'] = 'dashboard.php?wc=linktree/home';

            break;

        // CAPTURA DE ACORDO COM CALLBACK-ACTION
        case 'manage':
            if (in_array('', $PostData) && $PostData['carduser_thumb']) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<strong>OPPSSS:</strong> Favor preencha todos os campos!',
                    E_USER_NOTICE
                );
            } elseif (
                !Check::email($PostData['carduser_email']) || !filter_var(
                    $PostData['carduser_email'],
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<strong>OPPSSS: </strong>' . $PostData['carduser_name'] . ' o e-mail informado não é válido!',
                    E_USER_NOTICE
                );
            } else {
                $CardUserId = $PostData['carduser_id'];
                unset($PostData['carduser_id']);

                $PostData['carduser_status'] = (empty($PostData['carduser_status']) ? '0' : '1');
                $PostData['carduser_url'] = Check::getMailUser($PostData['carduser_email']);

                $Read->exeRead(DB_CARD_USER, 'WHERE carduser_id= :id', 'id=' . $CardUserId);
                $ThisPage = $Read->getResult()[0];

                // UPLOAD AVATAR-IMAGE
                if (!empty($_FILES['carduser_thumb'])) {
                    $File = $_FILES['carduser_thumb'];

                    if (
                        $ThisPage['carduser_thumb'] && file_exists(
                            '../../uploads/linktree/' . $ThisPage['carduser_thumb']
                        ) && !is_dir('../../uploads/linktree/' . $ThisPage['carduser_thumb'])
                    ) {
                        unlink('../../uploads/linktree/' . $ThisPage['carduser_thumb']);
                    }

                    $Upload = new Upload('../../uploads/linktree/');
                    $Upload->image($File, $PostData['carduser_name'], AVATAR_W);

                    if ($Upload->getResult()) {
                        $PostData['carduser_thumb'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR FOTO:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como foto!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                } else {
                    unset($PostData['carduser_thumb']);
                }

                $Update->exeUpdate(DB_CARD_USER, $PostData, 'WHERE carduser_id = :id', 'id=' . $CardUserId);
                $jSON['trigger'] = Check::ajaxErro('<b>OK!</b> Cartão atualizado com sucesso!');
                // $jSON['redirect'] = $Link;
            }

            break;
    }

    // RETORNA O CALLBACK
    if ([] !== $jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<strong>OPSS:</strong> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
