<?php

use App\Helpers\Check;
use App\Conn\Create;
//DEFINE O CALLBACK E RECUPERA O POST
require_once '../../../_app/Config.inc.php';

$jSON = null;
$CallBack = 'Leads';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];

//VALIDA AÇÃO
if (isset($PostData['callback'], $PostData['callback_action']) && $PostData['callback'] === $CallBack) {
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    //ELIMINA CÓDIGOS
    $PostData = array_map('strip_tags', $PostData);

    //SELECIONA AÇÃO
    switch ($Case) {
        //CAPTURA DE ACORDO COM CALLBACK-ACTION
        case 'news1':
        case 'news2':
            if (in_array('', $PostData, true)) {
                $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
                break;
            }

            if (!Check::email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)) {
                $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                break;
            }

            $LeadData = [
                'lead_name' => null,
                'lead_email' => $PostData['email'],
                'lead_conversion' => $Case,
            ];

            $Create = new Create();
            $Create->exeCreate(DB_LEADS, $LeadData);

            $jSON['trigger'] = Check::ajaxErro("<b>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
            break;
    }

    //RETORNA O CALLBACK
    if ($jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_WARNING);
        echo json_encode($jSON);
    }
} else {
    //ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
