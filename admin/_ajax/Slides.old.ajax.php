<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_SLIDES;

if (!APP_SLIDE || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Slides';
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
            $SlideId = $PostData['slide_id'];
            $SlideEnd = (empty($PostData['slide_end']) ? null : $PostData['slide_end']);
            $imageMobile = (empty($_FILES['slide_image_mobile']) ? null : $_FILES['slide_image_mobile']);
            $imageTablet = (empty($_FILES['slide_image_tablet']) ? null : $_FILES['slide_image_tablet']);
            $imageDesktop = (empty($_FILES['slide_image_desktop']) ? null : $_FILES['slide_image_desktop']);
            $PostData['slide_link'] = (empty($PostData['slide_link']) ? '#content' : $PostData['slide_link']);
            $PostData['slide_title'] = (empty($PostData['slide_title']) ? 'Banner Nº '
                . $SlideId : $PostData['slide_title']);

            unset($PostData['slide_id'], $PostData['slide_end'], $PostData['slide_image_mobile'], $PostData['slide_image_tablet'], $PostData['slide_image_desktop']);

            $Read->fullRead(
                'SELECT slide_image_mobile, slide_image_tablet, slide_image_desktop FROM ' . DB_SLIDES . ' WHERE slide_id = :id',
                'id=' . $SlideId
            );

            if (empty($imageDesktop) && (!$Read->getResult() || !$Read->getResult()[0]['slide_image_desktop'])) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Favor envie uma imagem de destaque para <b>DESKTOP</b> nas medidas de ' . SLIDE_W . 'x' . SLIDE_H . 'px!',
                    E_USER_ERROR
                );
            } elseif (in_array('', $PostData)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Para atualizar o destaque, favor preencha todos os campos!',
                    E_USER_ERROR
                );
                $jSON['error'] = true;
            } else {
                $PostData['slide_date'] = date('Y-m-d H:i:s');
                $PostData['slide_start'] = Check::Data($PostData['slide_start']);
                $PostData['slide_end'] = (empty($SlideEnd) ? null : Check::Data($SlideEnd));

                $PostData['show_headline'] = (empty($PostData['show_headline']) ? '0' : $PostData['show_headline']);
                $PostData['show_desc'] = (empty($PostData['show_desc']) ? '0' : $PostData['show_desc']);
                $PostData['slide_category'] = (empty($PostData['slide_category']) ? '0' : $PostData['slide_category']);
                $PostData['slide_product'] = (empty($PostData['slide_product']) ? '0' : $PostData['slide_product']);
                $PostData['slide_status'] = (empty($PostData['slide_status']) ? '0' : $PostData['slide_status']);

                if (!empty($imageMobile)) {
                    if (
                        $Read->getResult() && !empty($Read->getResult()[0]['slide_image_mobile']) && file_exists(
                            '../../uploads/' . $Read->getResult()[0]['slide_image_mobile']
                        ) && !is_dir('../../uploads/' . $Read->getResult()[0]['slide_image_mobile'])
                    ) {
                        unlink('../../uploads/' . $Read->getResult()[0]['slide_image_mobile']);
                    }

                    $Upload = new Upload('../../uploads/');
                    $Upload->image(
                        $imageMobile,
                        Check::name($PostData['slide_title']) . '-mobile',
                        SLIDE_W,
                        'slides'
                    );
                    $PostData['slide_image_mobile'] = $Upload->getResult();
                }

                if (!empty($imageTablet)) {
                    if (
                        $Read->getResult() && !empty($Read->getResult()[0]['slide_image_tablet']) && file_exists(
                            '../../uploads/' . $Read->getResult()[0]['slide_image_tablet']
                        ) && !is_dir('../../uploads/' . $Read->getResult()[0]['slide_image_tablet'])
                    ) {
                        unlink('../../uploads/' . $Read->getResult()[0]['slide_image_tablet']);
                    }

                    $Upload = new Upload('../../uploads/');
                    $Upload->image(
                        $imageTablet,
                        Check::name($PostData['slide_title']) . '-tablet',
                        SLIDE_W,
                        'slides'
                    );
                    $PostData['slide_image_tablet'] = $Upload->getResult();
                }

                if (!empty($imageDesktop)) {
                    if (
                        $Read->getResult() && !empty($Read->getResult()[0]['slide_image_desktop']) && file_exists(
                            '../../uploads/' . $Read->getResult()[0]['slide_image_desktop']
                        ) && !is_dir('../../uploads/' . $Read->getResult()[0]['slide_image_desktop'])
                    ) {
                        unlink('../../uploads/' . $Read->getResult()[0]['slide_image_desktop']);
                    }

                    $Upload = new Upload('../../uploads/');
                    $Upload->image(
                        $imageDesktop,
                        Check::name($PostData['slide_title']) . '-desktop',
                        SLIDE_W,
                        'slides'
                    );
                    $PostData['slide_image_desktop'] = $Upload->getResult();
                }

                $Update->exeUpdate(DB_SLIDES, $PostData, 'WHERE slide_id = :id', 'id=' . $SlideId);
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Tudo certo %s</b>: O conteúdo em destaque foi atualizado com sucesso. E sera exibido nas datas cadastradas!',
                        $_SESSION['userLogin']['user_name']
                    )
                );
            }

            break;

        // DELETA
        case 'delete':
            $SlideId = $PostData['del_id'];
            $Read->fullRead(
                'SELECT slide_image_mobile, slide_image_tablet, slide_image_desktop FROM ' . DB_SLIDES . ' WHERE slide_id = :id',
                'id=' . $SlideId
            );
            if ($Read->getResult()) {
                $imageMobile = (empty($Read->getResult()[0]['slide_image_mobile']) ? null : $Read->getResult(
                )[0]['slide_image_mobile']);
                if (
                    $imageMobile && file_exists('../../uploads/' . $imageMobile) && !is_dir(
                        '../../uploads/' . $imageMobile
                    )
                ) {
                    unlink('../../uploads/' . $imageMobile);
                }

                $imageTablet = (empty($Read->getResult()[0]['slide_image_tablet']) ? null : $Read->getResult(
                )[0]['slide_image_tablet']);
                if (
                    $imageTablet && file_exists('../../uploads/' . $imageTablet) && !is_dir(
                        '../../uploads/' . $imageTablet
                    )
                ) {
                    unlink('../../uploads/' . $imageTablet);
                }

                $imageDesktop = (empty($Read->getResult()[0]['slide_image_desktop']) ? null : $Read->getResult(
                )[0]['slide_image_desktop']);
                if (
                    $imageDesktop && file_exists('../../uploads/' . $imageDesktop) && !is_dir(
                        '../../uploads/' . $imageDesktop
                    )
                ) {
                    unlink('../../uploads/' . $imageDesktop);
                }
            }

            $Delete->exeDelete(DB_SLIDES, 'WHERE slide_id = :id', 'id=' . $SlideId);
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
