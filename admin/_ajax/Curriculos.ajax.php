<?php

use App\Helpers\Check;

use App\Conn\Delete;
use App\Conn\Read;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = 6;

if (!APP_CV || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Curriculos';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read();
    $Delete = new Delete();

    if ('delete' === $Case) {
        $PostData['id'] = $PostData['del_id'];
        $Read->fullRead('SELECT cv_pdf FROM ' . DB_CV . ' WHERE id = :cv', 'cv=' . $PostData['id']);
        if (
            $Read->getResult() && file_exists('../../uploads/' . $Read->getResult()[0]['cv_pdf']) && !is_dir(
                '../../uploads/' . $Read->getResult()[0]['cv_pdf']
            )
        ) {
            unlink('../../uploads/' . $Read->getResult()[0]['cv_pdf']);
        }

        $Delete->exeDelete(DB_CV, 'WHERE id = :id', 'id=' . $PostData['id']);
        $jSON['success'] = true;
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
