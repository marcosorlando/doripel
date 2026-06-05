<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_DEPOSITIONS;

if (!APP_DEPOSITIONS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo \json_encode($jSON);

    exit;
}

\usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Depositions';
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

    // SELECIONA AÇÃO
    switch ($Case) {
        // GERENCIA
        case 'manager':
            $DepositionId = $PostData['depositions_id'];

            $Image = (empty($_FILES['depositions_image']) ? null : $_FILES['depositions_image']);
            unset($PostData['depositions_id'], $PostData['depositions_image']);

            $Read->fullRead(
                'SELECT depositions_image FROM '.DB_DEPOSITIONS.' WHERE depositions_id = :id',
                'id='.$DepositionId
            );

            if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['depositions_image'])) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Favor envie uma FOTO nas medidas de 300X300px!',
                    E_USER_ERROR
                );
            } elseif (\in_array('', $PostData)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Para atualizar o destaque, favor preencha todos os campos!',
                    E_USER_ERROR
                );
                $jSON['error'] = true;
            } else {
                $PostData['depositions_date'] = \date('Y-m-d H:i:s');

                if (!empty($Image)) {
                    if (
                        $Read->getResult() && !empty($Read->getResult()[0]['depositions_image']) && \file_exists(
                            '../../uploads/depositions/'.$Read->getResult()[0]['depositions_image']
                        ) && !\is_dir('../../uploads/depositions/'.$Read->getResult()[0]['depositions_image'])
                    ) {
                        \unlink('../../uploads/depositions/'.$Read->getResult()[0]['depositions_image']);
                    }

                    $Upload = new Upload('../../uploads/');
                    $Upload->image($Image, Check::name($PostData['depositions_name']), 500, 'depositions');
                    $PostData['depositions_image'] = $Upload->getResult();
                }

                $Update->exeUpdate(DB_DEPOSITIONS, $PostData, 'WHERE depositions_id = :id', 'id='.$DepositionId);
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>Tudo certo %s</b>: O depoimento foi atualizado com sucesso e será exibido no site.',
                        $_SESSION['userLogin']['user_name']
                    )
                );
            }

            break;

            // DELETA
        case 'delete':
            $DepositionId = $PostData['del_id'];
            $Read->fullRead(
                'SELECT depositions_image FROM '.DB_DEPOSITIONS.' WHERE depositions_id = :id',
                'id='.$DepositionId
            );
            if ($Read->getResult()) {
                $DepositionImage = (empty($Read->getResult()[0]['depositions_image']) ? null : $Read->getResult(
                )[0]['depositions_image']);
                if (
                    $DepositionImage && \file_exists('../../uploads/depositions/'.$DepositionImage) && !\is_dir(
                        '../../uploads/depositions/'.$DepositionImage
                    )
                ) {
                    \unlink('../../uploads/depositions/'.$DepositionImage);
                }
            }

            $Delete->exeDelete(DB_DEPOSITIONS, 'WHERE depositions_id = :id', 'id='.$DepositionId);
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
