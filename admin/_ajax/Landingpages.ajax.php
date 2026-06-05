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

if (!APP_LANDING_PAGES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Landingpages';
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
                'SELECT page_logo, page_social_media, page_cover, page_mockup, page_mockup FROM ' . DB_LANDING_PAGES . ' WHERE page_id = :pi',
                'pi=' . $PostData['del_id']
            );

            if ($Read->getResult()) {
                $LogoRemove = '../../uploads/landingpages/' . $Read->getResult()[0]['page_logo'];
                $MockupRemove = '../../uploads/landingpages/' . $Read->getResult()[0]['page_mockup'];
                $SocialRemove = '../../uploads/landingpages/' . $Read->getResult()[0]['page_social_media'];
                $CoverRemove = '../../uploads/landingpages/' . $Read->getResult()[0]['page_cover'];

                if (file_exists($LogoRemove) && !is_dir($LogoRemove)) {
                    unlink($LogoRemove);
                }

                if (file_exists($MockupRemove) && !is_dir($MockupRemove)) {
                    unlink($MockupRemove);
                }

                if (file_exists($SocialRemove) && !is_dir($SocialRemove)) {
                    unlink($SocialRemove);
                }

                if (file_exists($CoverRemove) && !is_dir($CoverRemove)) {
                    unlink($CoverRemove);
                }
            }

            $Delete->exeDelete(DB_LANDING_PAGES, 'WHERE page_id = :id', 'id=' . $PostData['del_id']);

            $jSON['success'] = true;

            break;

        // MANAGER
        case 'manage':
            $PageId = $PostData['page_id'];
            unset($PostData['page_id']);

            $PostData['page_status'] = (empty($PostData['page_status']) ? '0' : '1');
            $PostData['page_name'] = (empty($PostData['page_name']) ? Check::name(
                $PostData['page_title']
            ) : Check::name($PostData['page_name']));

            $Read->exeRead(DB_LANDING_PAGES, 'WHERE page_id= :id', 'id=' . $PageId);
            $ThisPage = $Read->getResult()[0];

            // UPLOAD LOGOTIPO
            if (!empty($_FILES['page_logo'])) {
                $File = $_FILES['page_logo'];

                if (
                    $ThisPage['page_logo'] && file_exists(
                        '../../uploads/landingpages/' . $ThisPage['page_logo']
                    ) && !is_dir('../../uploads/landingpages/' . $ThisPage['page_logo'])
                ) {
                    unlink('../../uploads/landingpages/' . $ThisPage['page_logo']);
                }

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-logo', IMAGE_W);

                if ($Upload->getResult()) {
                    $PostData['page_logo'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR LOGO:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como Logotipo!",
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

            // ENVIAR MOCKUP
            if (!empty($_FILES['page_mockup'])) {
                $File = $_FILES['page_mockup'];

                if (
                    $ThisPage['page_mockup'] && file_exists(
                        '../../uploads/landingpages/' . $ThisPage['page_mockup']
                    ) && !is_dir('../../uploads/landingpages/' . $ThisPage['page_mockup'])
                ) {
                    unlink('../../uploads/landingpages/' . $ThisPage['page_mockup']);
                }

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-mkp', 600);

                if ($Upload->getResult()) {
                    $PostData['page_mockup'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR Mockup:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como mockup!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_mockup']);
            }

            // UPLOAD BACKGROUND-IMAGE - BOX
            if (!empty($_FILES['page_cover'])) {
                $File = $_FILES['page_cover'];

                if (
                    $ThisPage['page_cover'] && file_exists(
                        '../../uploads/landingpages/' . $ThisPage['page_cover']
                    ) && !is_dir('../../uploads/landingpages/' . $ThisPage['page_cover'])
                ) {
                    unlink('../../uploads/landingpages/' . $ThisPage['page_cover']);
                }

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-bg', IMAGE_W);

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

            // UPLOAD SOCIAL-MEDIA-IMAGE - BOX
            if (!empty($_FILES['page_social_media'])) {
                $File = $_FILES['page_social_media'];

                if (
                    $ThisPage['page_social_media'] && file_exists(
                        '../../uploads/landingpages/' . $ThisPage['page_social_media']
                    ) && !is_dir('../../uploads/landingpages/' . $ThisPage['page_social_media'])
                ) {
                    unlink('../../uploads/landingpages/' . $ThisPage['page_social_media']);
                }

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-social', IMAGE_W);

                if ($Upload->getResult()) {
                    $PostData['page_social_media'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como Thumb para Rede Social!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_social_media']);
            }

            $Read->fullRead(
                'SELECT page_name FROM ' . DB_LANDING_PAGES . ' WHERE page_name = :nm AND page_id != :id',
                sprintf('nm=%s&id=%s', $PostData['page_name'], $PageId)
            );

            if ($Read->getResult()) {
                $PostData['page_name'] = sprintf('%s-%s', $PostData['page_name'], $PageId);
            }

            $jSON['name'] = $PostData['page_name'];
            $jSON['view'] = BASE . ('/' . $PostData['page_name']);

            $Update->exeUpdate(DB_LANDING_PAGES, $PostData, 'WHERE page_id = :id', 'id=' . $PageId);
            $jSON['trigger'] = Check::ajaxErro('<b>Página atualizada com sucesso!</b>');

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
