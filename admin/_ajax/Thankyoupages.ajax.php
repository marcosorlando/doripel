<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_THANKYOU_PAGES;

if (!APP_THANKYOU_PAGES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Thankyoupages';
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
        // DELETE
        case 'delete':
            $Read->fullRead(
                'SELECT page_cover, page_logo, page_pdf FROM ' . DB_THANKYOU_PAGES . ' WHERE page_id = :ps',
                'ps=' . $PostData['del_id']
            );

            if ($Read->getResult()) {
                $CoverRemove = '../../uploads/thankyoupages/' . $Read->getResult()[0]['page_cover'];
                $LogoRemove = '../../uploads/thankyoupages/' . $Read->getResult()[0]['page_logo'];
                $MaterialRemove = '../../uploads/thankyoupages/' . $Read->getResult()[0]['page_pdf'];

                if (file_exists($CoverRemove) && !is_dir($CoverRemove)) {
                    unlink($CoverRemove);
                }

                if (file_exists($LogoRemove) && !is_dir($LogoRemove)) {
                    unlink($LogoRemove);
                }

                if (file_exists($MaterialRemove) && !is_dir($MaterialRemove)) {
                    unlink($MaterialRemove);
                }
            }

            $Delete->exeDelete(DB_THANKYOU_PAGES, 'WHERE page_id = :id', 'id=' . $PostData['del_id']);

            $jSON['success'] = true;

            break;

        // MANAGER
        case 'manage':
            $PageId = $PostData['page_id'];
            unset($PostData['page_id']);

            $PostData['page_status'] = (empty($PostData['page_status']) ? '0' : '1');
            $PostData['page_name'] = (empty($PostData['page_name']) ? Check::name(
                    $PostData['page_title']
                ) . '-download' : Check::name($PostData['page_name']));

            $Read->exeRead(DB_THANKYOU_PAGES, 'WHERE page_id= :id', 'id=' . $PageId);

            if ($Read->getResult()) {
                $ThisPage = $Read->getResult()[0];
            }

            // UPLOAD PDF
            if (!empty($_FILES['page_pdf'])) {
                $File = $_FILES['page_pdf'];

                if (
                    $ThisPage['page_pdf'] && file_exists(
                        '../../uploads/thankyoupages/' . $ThisPage['page_pdf']
                    ) && !is_dir('../../uploads/thankyoupages/' . $ThisPage['page_pdf'])
                ) {
                    unlink('../../uploads/thankyoupages/' . $ThisPage['page_pdf']);
                }

                $Upload = new Upload('../../uploads/thankyoupages/');
                $Upload->File($File, $PostData['page_name'] . '-pdf');

                if ($Upload->getResult()) {
                    $PostData['page_pdf'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-file-pdf'>ERRO AO ARQUIVO PDF</b> Olá %s, selecione um arquivo em PDF para inserir no produto!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_pdf']);
            }

            // UPLOAD BACKGROUND-IMAGE
            if (!empty($_FILES['page_cover'])) {
                $File = $_FILES['page_cover'];

                if (
                    $ThisPage['page_cover'] && file_exists(
                        '../../uploads/thankyoupages/' . $ThisPage['page_cover']
                    ) && !is_dir('../../uploads/thankyoupages/' . $ThisPage['page_cover'])
                ) {
                    unlink('../../uploads/thankyoupages/' . $ThisPage['page_cover']);
                }

                $Upload = new Upload('../../uploads/thankyoupages/');
                $Upload->image($File, $PostData['page_name'] . '-bg');

                if ($Upload->getResult()) {
                    $PostData['page_cover'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como capa!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_cover']);
            }

            // ENVIAR LOGO
            if (!empty($_FILES['page_logo'])) {
                $File = $_FILES['page_logo'];

                if (
                    $ThisPage['page_logo'] && file_exists(
                        '../../uploads/thankyoupages/' . $ThisPage['page_logo']
                    ) && !is_dir('../../uploads/thankyoupages/' . $ThisPage['page_logo'])
                ) {
                    unlink('../../uploads/thankyoupages/' . $ThisPage['page_logo']);
                }

                $Upload = new Upload('../../uploads/thankyoupages/');
                $Upload->image($File, $PostData['page_name'] . '-logo');

                if ($Upload->getResult()) {
                    $PostData['page_logo'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR LOGO:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como logotipo!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_logo']);
            }

            $Read->fullRead(
                'SELECT page_name FROM ' . DB_THANKYOU_PAGES . ' WHERE page_name = :nm AND page_id != :id',
                sprintf('nm=%s&id=%s', $PostData['page_name'], $PageId)
            );

            if ($Read->getResult()) {
                $PostData['page_name'] = sprintf('%s-%s', $PostData['page_name'], $PageId);
            }

            $jSON['name'] = $PostData['page_name'];
            $jSON['view'] = BASE . ('/' . $PostData['page_name']);

            $Update->exeUpdate(DB_THANKYOU_PAGES, $PostData, 'WHERE page_id = :id', 'id=' . $PageId);
            $jSON['trigger'] = Check::ajaxErro('<b>Ok!</b> Página atualizada com sucesso.');

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
