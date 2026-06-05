<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_SERVICES;

if (!APP_SERVICES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Services';
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

    $Upload = new Upload('../../uploads/');

    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $SvcId = $PostData['svc_id'];
            $PostData['svc_status'] = (empty($PostData['svc_status']) ? '0' : $PostData['svc_status']);

            $Read->exeRead(DB_SVC, 'WHERE svc_id = :id', 'id=' . $SvcId);

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar o serviço. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Service = $Read->getResult()[0];

                // var_dump($PostData);
                unset($PostData['svc_id'], $PostData['svc_cover'], $PostData['image'], $PostData['svc_icon']);

                $PostData['svc_name'] = Check::name($PostData['svc_title']);

                if (!empty($_FILES['svc_cover'])) {
                    $File = $_FILES['svc_cover'];

                    if (
                        $Service['svc_cover'] && file_exists('../../uploads/' . $Service['svc_cover']) && !is_dir(
                            '../../uploads/' . $Service['svc_cover']
                        )
                    ) {
                        unlink('../../uploads/' . $Service['svc_cover']);
                    }

                    $Upload->image($File, sprintf('%s-%s-', $SvcId, $PostData['svc_name']) . time(), 1200);
                    if ($Upload->getResult()) {
                        $PostData['svc_cover'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 1200X628px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                if (!empty($_FILES['svc_icon'])) {
                    $File = $_FILES['svc_icon'];

                    if (
                        $Service['svc_icon'] && file_exists('../../uploads/' . $Service['svc_icon']) && !is_dir(
                            '../../uploads/' . $Service['svc_icon']
                        )
                    ) {
                        unlink('../../uploads/' . $Service['svc_icon']);
                    }

                    $Upload->image($File, sprintf('%s-%s-', $SvcId, $PostData['svc_name']) . time(), 1200);
                    if ($Upload->getResult()) {
                        $PostData['svc_icon'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 1200X628px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                if (!empty($_FILES['image'])) {
                    $File = $_FILES['image'];
                    $gbFile = [];
                    $gbCount = count($File['type']);
                    $gbKeys = array_keys($File);
                    $gbLoop = 0;

                    for ($gb = 0; $gb < $gbCount; ++$gb) {
                        foreach ($gbKeys as $Keys) {
                            $gbFiles[$gb][$Keys] = $File[$Keys][$gb];
                        }
                    }

                    $jSON['gallery'] = null;
                    foreach ($gbFiles as $UploadFile) {
                        ++$gbLoop;
                        $Upload->image(
                            $UploadFile,
                            sprintf('%s-%d-', $SvcId, $gbLoop) . time() . base64_encode(time()),
                            1000
                        );
                        if ($Upload->getResult()) {
                            $gbCreate = ['svc_id' => $SvcId, 'image' => $Upload->getResult()];
                            $Create->exeCreate(DB_SVC_GALLERY, $gbCreate);
                            $jSON['gallery'] .= sprintf(
                                "<img rel='Services' id='%s' alt='Imagem em %s' title='Imagem em %s' src='../uploads/%s'/>",
                                $Create->getResult(),
                                $PostData['svc_title'],
                                $PostData['svc_title'],
                                $Upload->getResult()
                            );
                        }
                    }
                }

                $Read->fullRead(
                    'SELECT svc_id FROM ' . DB_SVC . ' WHERE svc_name = :nm AND svc_id != :id',
                    sprintf('nm=%s&id=%s', $PostData['svc_name'], $SvcId)
                );
                if ($Read->getResult()) {
                    $PostData['svc_name'] = sprintf('%s-%s', $PostData['svc_name'], $SvcId);
                }

                $jSON['name'] = $PostData['svc_name'];
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<span><b>SERVIÇO ATUALIZADO:</b> Olá %s. O serviço %s foi atualizado com sucesso!<span>',
                        $_SESSION['userLogin']['user_name'],
                        $PostData['svc_title']
                    )
                );

                $PostData['svc_status'] = (empty($PostData['svc_status']) ? '0' : '1');

                $Update->exeUpdate(DB_SVC, $PostData, 'WHERE svc_id = :id', 'id=' . $SvcId);
                $jSON['view'] = BASE . '/servico/' . $PostData['svc_name'];
            }

            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->fullRead(
                'SELECT svc_title, svc_name FROM ' . DB_SVC . ' WHERE svc_id = :id',
                'id=' . $PostData['svc_id']
            );
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe %s, mas não foi possível identificar o serviço vinculado!",
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Upload = new Upload('../../uploads/');
                $Upload->image($NewImage, $PostData['svc_id'] . '-' . time(), IMAGE_W);
                if ($Upload->getResult()) {
                    $PostData['svc_id'] = $PostData['svc_id'];
                    $PostData['image'] = $Upload->getResult();
                    unset($PostData['svc_id']);

                    $Create->exeCreate(DB_SVC_IMAGE, $PostData);
                    $jSON['tinyMCE'] = sprintf(
                        "<img title='%s' alt='%s' src='../uploads/%s'/>",
                        $Read->getResult()[0]['svc_title'],
                        $Read->getResult()[0]['svc_title'],
                        $PostData['image']
                    );
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma imagem JPG ou PNG para inserir no serviço!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                }
            }

            break;

        case 'delete':
            $SvcId = $PostData['del_id'];
            $Read->exeRead(DB_SVC, 'WHERE svc_id = :id', 'id=' . $SvcId);
            $Service = $Read->getResult()[0];

            if (!$Service) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPSS:</b> Desculpe %s. Não foi possível deletar pois o serviço não existe ou foi removido recentemente!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $SvcCover = '../../uploads/' . $Service['svc_cover'];

                if (file_exists($SvcCover) && !is_dir($SvcCover)) {
                    unlink($SvcCover);
                }

                $Read->exeRead(DB_SVC_IMAGE, 'WHERE svc_id = :id', 'id=' . $Service['svc_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $SvcImage) {
                        $SvcImageIs = '../../uploads/' . $SvcImage['image'];
                        if (file_exists($SvcImageIs) && !is_dir($SvcImageIs)) {
                            unlink($SvcImageIs);
                        }
                    }

                    $Delete->exeDelete(DB_SVC_IMAGE, 'WHERE svc_id = :id', 'id=' . $Service['svc_id']);
                }

                $Read->exeRead(DB_SVC_GALLERY, 'WHERE svc_id = :id', 'id=' . $Service['svc_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $SvcGallery) {
                        $SvcGalleryImage = '../../uploads/' . $SvcGallery['image'];
                        if (file_exists($SvcGalleryImage) && !is_dir($SvcGalleryImage)) {
                            unlink($SvcGalleryImage);
                        }
                    }

                    $Delete->exeDelete(DB_SVC_GALLERY, 'WHERE svc_id = :id', 'id=' . $Service['svc_id']);
                }

                $Delete->exeDelete(DB_SVC, 'WHERE svc_id = :id', 'id=' . $Service['svc_id']);
                $jSON['success'] = true;
            }

            break;

        case 'gbremove':
            $Read->fullRead('SELECT image FROM ' . DB_SVC_GALLERY . ' WHERE id = :id', 'id=' . $PostData['img']);
            if ($Read->getResult()) {
                $ImageRemove = '../../uploads/' . $Read->getResult()[0]['image'];
                if (file_exists($ImageRemove) && !is_dir($ImageRemove)) {
                    unlink($ImageRemove);
                }

                $Delete->exeDelete(DB_SVC_GALLERY, 'WHERE id = :id', 'id=' . $PostData['img']);
                $jSON['success'] = true;
            }

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
