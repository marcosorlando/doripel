<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_LANDING_PAGES;

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
$CallBack = 'Leads';
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
            $Read->fullRead('SELECT lead_thumb FROM ' . DB_LEADS . ' WHERE lead_id = :id', 'id=' . $PostData['key']);

            if (
                $Read->getResult()[0]['lead_thumb'] &&
                file_exists('../../uploads/leads/' . $Read->getResult()[0]['lead_thumb']) &&
                !is_dir('../../uploads/leads/' . $Read->getResult()[0]['lead_thumb'])
            ) {
                unlink('../../uploads/leads/' . $Read->getResult()[0]['lead_thumb']);
            }

            $Delete->exeDelete(DB_LEADS, 'WHERE lead_id = :id', 'id=' . $PostData['key']);
            $jSON['redirect'] = 'dashboard.php?wc=leads/home';
            $jSON['trigger'] = Check::ajaxErro(
                '<b>Lead Removido:</b> Esse LEAD foi removido com sucesso!'
            );

            break;

        // CAPTURA DE ACORDO COM CALLBACK-ACTION
        case 'manage':

            //Popula as Cidades por Estado - Recebido
            if ($PostData['lead_state']) {
                $jSON['city'] = null;

                $Read->fullRead(
                    'SELECT id, name, uf FROM ' . DB_CITIES . ' WHERE uf = :uf',
                    'uf=' . $PostData['lead_state']
                );

                if ($Read->getResult()) {
                    $jSON['city'] .= "<option value='0'>- Selecione a cidade -</option> ";

                    foreach ($Read->getResult() as $Cities) {
                        extract($Cities);
                        $selected = (!empty($PostData['lead_city']) && $name == $PostData['lead_city'] ? 'selected' : '');
                        $jSON['city'] .= sprintf("<option value='%s' %s>%s</option>", $name, $selected, $name);
                    }
                }
            }

            if (
                in_array('', $PostData, true) &&
                !empty($PostData['lead_thumb']) &&
                !empty($PostData['lead_job_title'])
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<strong>OPPSSS:</strong> Favor preencha todos os campos!',
                    E_USER_NOTICE
                );
            } elseif (
                !Check::email($PostData['lead_email']) || !filter_var($PostData['lead_email'], FILTER_VALIDATE_EMAIL)
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<strong>OPPSSS: </strong>' . $PostData['lead_name'] . ' o e-mail informado não é válido!',
                    E_USER_NOTICE
                );
            } else {
                $LeadId = $PostData['lead_id'];
                unset($PostData['lead_id']);
                $PostData['lead_status'] = (($PostData['lead_status'] ?? '') === '' ? '0' : '1');

                $Read->exeRead(DB_LEADS, 'WHERE lead_id= :id', 'id=' . $LeadId);
                $ThisPage = $Read->getResult()[0];

                /* Upload: lead_thumb */
                if (!empty($_FILES['lead_thumb'])) {
                    $File = $_FILES['lead_thumb'];

                    if (
                        $ThisPage['lead_thumb'] && file_exists(
                            '../../uploads/leads/' . $ThisPage['lead_thumb']
                        ) && !is_dir('../../uploads/leads/' . $ThisPage['lead_thumb'])
                    ) {
                        unlink('../../uploads/leads/' . $ThisPage['lead_thumb']);
                    }

                    $Upload = new Upload('../../uploads/leads/');
                    $Upload->image($File, $PostData['lead_name'], AVATAR_W);

                    if ($Upload->getResult()) {
                        $PostData['lead_thumb'] = $Upload->getResult();
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
                    unset($PostData['lead_thumb']);
                }

                $Update->exeUpdate(DB_LEADS, $PostData, 'WHERE lead_id = :id', 'id=' . $LeadId);
                $jSON['trigger'] = Check::ajaxErro('<b>OK!</b> Lead atualizado com sucesso!');
            }

            break;
    }

    // RETORNA O CALLBACK
    if ([] !== $jSON) {
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
