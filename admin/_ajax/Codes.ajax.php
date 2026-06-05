<?php

use App\Helpers\Check;

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_CONFIG_CODES;

if (empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo \json_encode($jSON);

    exit;
}

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Codes';
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
        // STATS
        case 'workcodes':
            if (empty($PostData['code_name']) || empty($PostData['code_script'])) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO:</b> Para cadastrar um WC CODE é preciso informar pelo menos o título e o script. Favor tente novamente!',
                    E_USER_ERROR
                );
            } elseif (empty($PostData['code_id'])) {
                unset($PostData['code_id']);
                $PostData['code_created'] = \date('Y-m-d H:i:s');
                $Create->exeCreate(DB_WC_CODE, $PostData);
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>CADASTRO COM SUCESSO:</b> O seu WC CODE foi cadastrado com sucesso e você já pode ver a alteração em seu site!'
                );
            } else {
                $CodeId = $PostData['code_id'];
                unset($PostData['code_id']);
                $Update->exeUpdate(DB_WC_CODE, $PostData, 'WHERE code_id = :id', 'id='.$CodeId);
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ATUALIZADO COM SUCESSO:</b> O seu WC CODE foi atualizado com sucesso e você já pode ver a alteração em seu site!',
                    E_USER_NOTICE
                );
            }

            break;

        case 'edit':
            $CodeId = $PostData['code_id'];
            $Read->exeRead(DB_WC_CODE, 'WHERE code_id = :id', 'id='.$CodeId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO OBTER WORK CONTROL CODE:</b> Você tentou editar um código que não existe ou foi removido!',
                    E_USER_ERROR
                );
            } else {
                $jSON['data'] = $Read->getResult()[0];
            }

            break;

        case 'delete':
            $CodeDel = $PostData['del_id'];
            $Delete->exeDelete(DB_WC_CODE, 'WHERE code_id = :id', 'id='.$CodeDel);
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
