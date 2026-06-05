<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_SEGMENTS;

if (!APP_SEGMENTS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Segments';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create = new Create();
    // AUTO INSTANCE OBJECT UPDATE
    $Update ??= new Update();
    // AUTO INSTANCE OBJECT DELETE
    $Delete ??= new Delete();

    $Upload ??= new Upload('../../uploads/');

    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $SegId = $PostData['seg_id'];
            $PostData['seg_status'] = (empty($PostData['seg_status']) ? '0' : '1');

            $Read->exeRead(DB_SEG, 'WHERE seg_id = :id', 'id=' . $SegId);

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar o segmento. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Segment = $Read->getResult()[0];
                unset($PostData['seg_id'], $PostData['seg_cover'], $PostData['seg_icon']);

                $PostData['seg_name'] = (empty($PostData['seg_name']) ? Check::name(
                    $PostData['seg_title']
                ) : Check::name($PostData['seg_name']));

                // COVER UPLOAD
                if (!empty($_FILES['seg_cover'])) {
                    $File = $_FILES['seg_cover'];

                    if (
                        $Segment['seg_cover'] && file_exists('../../uploads/' . $Segment['seg_cover']) && !is_dir(
                            '../../uploads/' . $Segment['seg_cover']
                        )
                    ) {
                        unlink('../../uploads/' . $Segment['seg_cover']);
                    }

                    $Upload->image($File, $PostData['seg_name'] . time(), 800);
                    if ($Upload->getResult()) {
                        $PostData['seg_cover'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 800x800px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                // ICONE UPLOAD
                if (!empty($_FILES['seg_icon'])) {
                    $File = $_FILES['seg_icon'];

                    if (
                        $Segment['seg_icon'] && file_exists('../../uploads/' . $Segment['seg_icon']) && !is_dir(
                            '../../uploads/' . $Segment['seg_icon']
                        )
                    ) {
                        unlink('../../uploads/' . $Segment['seg_icon']);
                    }

                    $Upload->image($File, $PostData['seg_name'], 600);
                    if ($Upload->getResult()) {
                        $PostData['seg_icon'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem PNG de 600x600px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                $Read->fullRead(
                    'SELECT seg_id FROM ' . DB_SEG . ' WHERE seg_name = :nm AND seg_id != :id',
                    sprintf('nm=%s&id=%s', $PostData['seg_name'], $SegId)
                );
                if ($Read->getResult()) {
                    $PostData['seg_name'] = $PostData['seg_name'];
                }

                $Update->exeUpdate(DB_SEG, $PostData, 'WHERE seg_id = :id', 'id=' . $SegId);

                $jSON['name'] = $PostData['seg_name'];
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<span><b>SEGMENTO ATUALIZADO:</b> Olá %s. O segmento %s foi atualizado com sucesso!<span>',
                        $_SESSION['userLogin']['user_name'],
                        $PostData['seg_title']
                    )
                );
                $jSON['view'] = BASE . '/segmento/' . $PostData['seg_name'];
            }

            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->fullRead(
                'SELECT seg_title, seg_name FROM ' . DB_SEG . ' WHERE seg_id = :id',
                'id=' . $PostData['seg_id']
            );
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe %s, mas não foi possível identificar o segmento vinculado!",
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Upload = new Upload('../../uploads/');
                $Upload->image($NewImage, $Read->getResult()[0]['seg_title'] . '-' . time(), IMAGE_W);
                if ($Upload->getResult()) {
                    $PostData['segment_id'] = $PostData['seg_id'];
                    $PostData['image'] = $Upload->getResult();
                    unset($PostData['seg_id']);

                    $Create->exeCreate(DB_SEG_IMAGE, $PostData);
                    $jSON['tinyMCE'] = sprintf(
                        "<img title='%s' alt='%s' src='../uploads/%s'/>",
                        $Read->getResult()[0]['seg_title'],
                        $Read->getResult()[0]['seg_title'],
                        $PostData['image']
                    );
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma imagem JPG ou PNG para inserir no segmento!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                }
            }

            break;

        case 'delete':
            $SegId = $PostData['del_id'];

            $Read->exeRead(DB_SEG, 'WHERE seg_id = :id', 'id=' . $SegId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPSS:</b> Desculpe %s. Não foi possível deletar pois o segmento não existe ou foi removido recentemente!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Segment = $Read->getResult()[0];
                $SegCover = '../../uploads/' . $Segment['seg_cover'];

                if (file_exists($SegCover) && !is_dir($SegCover)) {
                    unlink($SegCover);
                }

                $Read->exeRead(DB_SEG_IMAGE, 'WHERE segment_id = :id', 'id=' . $Segment['seg_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $SegImage) {
                        $SegImageIs = '../../uploads/' . $SegImage['image'];
                        if (file_exists($SegImageIs) && !is_dir($SegImageIs)) {
                            unlink($SegImageIs);
                        }
                    }

                    $Delete->exeDelete(DB_SEG_IMAGE, 'WHERE segment_id = :id', 'id=' . $Segment['seg_id']);
                }

                $Read->exeRead(DB_SEG_GALLERY, 'WHERE segment_id = :id', 'id=' . $Segment['seg_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $SegGB) {
                        $SegGBImage = '../../uploads/' . $SegGB['image'];
                        if (file_exists($SegGBImage) && !is_dir($SegGBImage)) {
                            unlink($SegGBImage);
                        }
                    }

                    $Delete->exeDelete(DB_SEG_GALLERY, 'WHERE segment_id = :id', 'id=' . $Segment['seg_id']);
                }

                $Delete->exeDelete(DB_SEG, 'WHERE seg_id = :id', 'id=' . $Segment['seg_id']);
                $jSON['success'] = true;
            }

            break;

        case 'gbremove':
            echo '';
            $Read->fullRead(
                'SELECT image FROM ' . DB_SEG_GALLERY . ' WHERE id = :id',
                'id=' . $PostData['img']
            );
            if ($Read->getResult()) {
                $ImageRemove = '../../uploads/' . $Read->getResult()[0]['image'];
                if (file_exists($ImageRemove) && !is_dir($ImageRemove)) {
                    unlink($ImageRemove);
                }

                $Delete->exeDelete(DB_SEG_GALLERY, 'WHERE id = :id', 'id=' . $PostData['img']);
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
