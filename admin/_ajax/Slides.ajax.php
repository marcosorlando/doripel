<?php

use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../_app/Config.inc.php';

$NivelAcess = LEVEL_WC_SLIDES;

if (!APP_SLIDE || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    echo json_encode([
        'trigger' => Check::ajaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_WARNING),
        'error' => true,
    ]);
    exit;
}

usleep(50000);

$jSON = null;
$CallBack = 'Slides';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];

if (isset($PostData['callback'], $PostData['callback_action']) && $PostData['callback'] === $CallBack) {
    $Case = (string) $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read();
    $Update = new Update();
    $Delete = new Delete();

    switch ($Case) {
        case 'manager':
            $SlideId = filter_var($PostData['slide_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$SlideId) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Destaque inválido para atualização!', E_USER_WARNING);
                $jSON['error'] = true;
                break;
            }

            $Image = (
                isset($_FILES['slide_image']['error']) &&
                $_FILES['slide_image']['error'] === UPLOAD_ERR_OK
            ) ? $_FILES['slide_image'] : null;
            $MobileImage = (
                isset($_FILES['mobile_image']['error']) &&
                $_FILES['mobile_image']['error'] === UPLOAD_ERR_OK
            ) ? $_FILES['mobile_image'] : null;

            $SlideEnd = (!empty($PostData['slide_end']) ? (string) $PostData['slide_end'] : null);
            unset($PostData['slide_id'], $PostData['slide_end'], $PostData['slide_image'], $PostData['mobile_image']);

            $Read->fullRead('SELECT slide_image, mobile_image FROM ' . DB_SLIDES . ' WHERE slide_id = :id', "id={$SlideId}");
            $CurrentSlide = $Read->getResult()[0] ?? [];

            if (empty($Image) && empty($CurrentSlide['slide_image'])) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Favor envie uma imagem de destaque nas medidas de ' . SLIDE_W . 'x' . SLIDE_H . 'px!', E_USER_WARNING);
                $jSON['error'] = true;
                break;
            }

            $RequiredFields = ['slide_title', 'slide_desc', 'slide_opacity', 'slide_start'];
            foreach ($RequiredFields as $Field) {
                if (trim((string) ($PostData[$Field] ?? '')) === '') {
                    $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o destaque, favor preencha todos os campos obrigatórios!', E_USER_WARNING);
                    $jSON['error'] = true;
                    break 2;
                }
            }

            foreach (['slide_image', 'mobile_image'] as $FileInput) {
                if (isset($_FILES[$FileInput]['error']) && !in_array($_FILES[$FileInput]['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE], true)) {
                    $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Não foi possível receber o arquivo enviado!', E_USER_WARNING);
                    $jSON['error'] = true;
                    break 2;
                }
            }

            $PostData['slide_date'] = date('Y-m-d H:i:s');
            $PostData['slide_start'] = Check::data((string) $PostData['slide_start']);
            $PostData['slide_end'] = (!empty($SlideEnd) ? Check::data($SlideEnd) : null);

            $PostData['show_title'] = (!empty($PostData['show_title']) ? $PostData['show_title'] : '0');
            $PostData['show_desc'] = (!empty($PostData['show_desc']) ? $PostData['show_desc'] : '0');
            $PostData['slide_purchase'] = (!empty($PostData['slide_purchase']) ? $PostData['slide_purchase'] : '0');
            $PostData['slide_information'] = (!empty($PostData['slide_information']) ? $PostData['slide_information'] : '0');
            $PostData['slide_status'] = (!empty($PostData['slide_status']) ? $PostData['slide_status'] : '0');

            if (!empty($Image)) {
                $Upload = new Upload('../../uploads/');
                $Upload->image($Image, Check::name((string) $PostData['slide_title']), SLIDE_W, 'slides');
                $UploadedImage = $Upload->getResult();

                if (!is_string($UploadedImage) || $UploadedImage === '') {
                    $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Não foi possível enviar a imagem principal do destaque!', E_USER_WARNING);
                    $jSON['error'] = true;
                    break;
                }

                $PostData['slide_image'] = $UploadedImage;

                if (!empty($CurrentSlide['slide_image']) && file_exists("../../uploads/{$CurrentSlide['slide_image']}") && !is_dir("../../uploads/{$CurrentSlide['slide_image']}")) {
                    unlink("../../uploads/{$CurrentSlide['slide_image']}");
                }
            }

            if (!empty($MobileImage)) {
                $Upload = new Upload('../../uploads/');
                $Upload->image($MobileImage, Check::name((string) $PostData['slide_title']), 640, 'slides');
                $UploadedMobileImage = $Upload->getResult();

                if (!is_string($UploadedMobileImage) || $UploadedMobileImage === '') {
                    $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Não foi possível enviar a imagem mobile do destaque!', E_USER_WARNING);
                    $jSON['error'] = true;
                    break;
                }

                $PostData['mobile_image'] = $UploadedMobileImage;

                if (!empty($CurrentSlide['mobile_image']) && file_exists("../../uploads/{$CurrentSlide['mobile_image']}") && !is_dir("../../uploads/{$CurrentSlide['mobile_image']}")) {
                    unlink("../../uploads/{$CurrentSlide['mobile_image']}");
                }
            }

            $Update->exeUpdate(DB_SLIDES, $PostData, 'WHERE slide_id = :id', "id={$SlideId}");
            $jSON['trigger'] = Check::ajaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O conteúdo em destaque foi atualizado com sucesso. E será exibido no intervalo de datas cadastradas!");
            break;

        case 'delete':
            $SlideId = filter_var($PostData['del_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$SlideId) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> Destaque inválido para exclusão!', E_USER_WARNING);
                $jSON['error'] = true;
                break;
            }

            $Read->fullRead('SELECT slide_image, mobile_image FROM ' . DB_SLIDES . ' WHERE slide_id = :id', "id={$SlideId}");
            if ($Read->getResult()) {
                foreach (['slide_image', 'mobile_image'] as $ImageField) {
                    $SlideImage = $Read->getResult()[0][$ImageField] ?? null;
                    if ($SlideImage && file_exists("../../uploads/{$SlideImage}") && !is_dir("../../uploads/{$SlideImage}")) {
                        unlink("../../uploads/{$SlideImage}");
                    }
                }
            }

            $Delete->exeDelete(DB_SLIDES, 'WHERE slide_id = :id', "id={$SlideId}");
            $jSON['success'] = true;
            break;
    }

    if ($jSON) {
        echo json_encode($jSON);
        exit;
    }

    echo json_encode([
        'trigger' => Check::ajaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_WARNING),
        'error' => true,
    ]);
    exit;
}

exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
