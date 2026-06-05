<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_REPRESENTATIVES;

if (!APP_REPRESENTATIVES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Representatives';
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
        // GERENCIA
        case 'manager':

            $repId = (int)$PostData['rep_id'];
            unset($PostData['rep_id']);

            //Popula as Cidades por Estado - Recebido
            if ($PostData['rep_uf']) {
                $jSON['city'] = null;

                $Read->fullRead(
                    'SELECT id, name, uf FROM ' . DB_CITIES . ' WHERE uf = :uf',
                    'uf=' . $PostData['rep_uf']
                );

                if ($Read->getResult()) {
                    $jSON['city'] .= "<option value='0'>- Selecione a cidade -</option> ";

                    foreach ($Read->getResult() as $Cities) {
                        extract($Cities);
                        $selected = (!empty($PostData['rep_city']) && $name == $PostData['rep_city'] ? 'selected' : '');
                        $jSON['city'] .= sprintf("<option value='%s' %s>%s</option>", $name, $selected, $name);
                    }
                }
            }

            $PostData['rep_all_cities'] = isset($PostData['rep_all_cities']) ? (int)$PostData['rep_all_cities'] : 0;
            //Valida se Representante atente todas as cidades do Estado
            if ($PostData['rep_all_cities'] === 1) {
                $jSON['allcities'] = null;
                $PostData['rep_cities'] = null;

                $Read->fullRead('SELECT name FROM ' . DB_CITIES . ' WHERE uf = :uf', 'uf=' . $PostData['rep_uf']);

                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $Cities) {
                        extract($Cities);
                        $jSON['allcities'] .= $name . ', ';
                        $PostData['rep_cities'] .= $name . ', ';
                    }
                }
            } else {
                $jSON['placeholder'] = 'Não esquecer de inserir as cidades separadas por vírgula aqui!';
            }

            //Valida se tem campos vazios - exceto 'rep_all_cities'
            if (
                array_search('', $PostData, true)
                && 'rep_all_cities' != array_search('', $PostData, true)
            ) {
                $jSON['field'] = array_search('', $PostData, true);
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Para atualizar o representante, favor preencha todos os campos!',
                    E_USER_ERROR
                );
                $jSON['error'] = true;
            } else { // Atualiza daddos no Banco

                $Update->exeUpdate(
                    DB_REPRESENTATIVES,
                    $PostData,
                    'WHERE rep_id = :id',
                    'id=' . $repId
                );

                if ($Update->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>Tudo certo</b>: O representante foi atualizado com sucesso.'
                    );
                }
            }
            break;

        // DELETA
        case 'delete':
            $id = $PostData['del_id'];
            $Delete->exeDelete(DB_REPRESENTATIVES, 'WHERE rep_id = :id', 'id=' . $id);
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
