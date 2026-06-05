<?php

use App\Conn\Read;
use App\Helpers\Check;

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_REPORTS;

if (empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Reports';
$PostData = \filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    if ('get_report' === $Case) {
        $ReportStart = \date(
            'Y-m-d',
            \strtotime('' !== (string) (string) (string) $PostData['start_date'] && '0' !== (string) $PostData['start_date'] ? Check::Data($PostData['start_date']) : \date('Y-m-01 H:i:s'))
        );
        $ReportEnd = \date(
            'Y-m-d',
            \strtotime('' !== (string) (string) (string) $PostData['end_date'] && '0' !== (string) $PostData['end_date'] ? Check::Data($PostData['end_date']) : \date('Y-m-d H:i:s'))
        );
        $_SESSION['wc_report_date'] = [$ReportStart, $ReportEnd];
        $jSON['redirect'] = 'dashboard.php?wc='.$PostData['report_back'];
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
