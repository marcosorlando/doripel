<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_POSTS;

if (empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Search';
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
        // STATS
        case 'publish':
            $Publish = ['search_publish' => 1];
            $Update->exeUpdate(DB_SEARCH, $Publish, 'WHERE search_id = :search', 'search='.$PostData['key']);
            $jSON['remove_id'] = (string) $PostData['key'];
            $jSON['trigger'] = Check::ajaxErro(
                '<b>Publicada:</b> Esse termo de pesquisa foi publicado com sucesso!'
            );

            break;

            // STATS
        case 'delete':
            $Delete->exeDelete(DB_SEARCH, 'WHERE search_id = :search', 'search='.$PostData['key']);
            $jSON['remove_id'] = (string) $PostData['key'];
            $jSON['trigger'] = Check::ajaxErro(
                '<b>Removida:</b> Esse termo de pesquisa foi removido com sucesso!',
                E_USER_ERROR
            );

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
