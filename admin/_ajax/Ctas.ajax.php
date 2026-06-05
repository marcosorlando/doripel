<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../_app/Config.inc.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$jSON = null;
$NivelAcess = LEVEL_WC_CTAS;

if (!APP_CTAS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_WARNING);
    echo json_encode($jSON);
    exit;
}

usleep(50000);

$CallBack = 'Ctas';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];

if (isset($PostData['callback'], $PostData['callback_action']) && $PostData['callback'] === $CallBack) {
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read();
    $Create = new Create();
    $Update = new Update();
    $Delete = new Delete();

    switch ($Case) {
        case 'manager':
            $CtaId = filter_var($PostData['cta_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$CtaId) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> CTA inválido para atualização!', E_USER_WARNING);
                break;
            }

            $Image = (!empty($_FILES['cta_image']) ? $_FILES['cta_image'] : null);
            unset($PostData['cta_id'], $PostData['cta_image']);

            $Read->fullRead("SELECT cta_image FROM " . DB_CTAS . " WHERE cta_id = :id", "id={$CtaId}");
            $CurrentImage = ($Read->getResult()[0]['cta_image'] ?? null);

            if (empty($Image) && empty($CurrentImage)) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Favor envie uma imagem nas medidas de 500X400px!', E_USER_WARNING);
                break;
            }

            if (in_array('', $PostData, true)) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o CTA, favor preencha todos os campos!', E_USER_WARNING);
                $jSON['error'] = true;
                break;
            }

            $PostData['cta_date'] = date('Y-m-d H:i:s');
            $PostData['cta_status'] = (!empty($PostData['cta_status']) ? 1 : 0);
            $PostData['cta_start'] = Check::data((string) $PostData['cta_start']);
            $PostData['cta_end'] = (!empty($PostData['cta_end']) ? Check::data((string) $PostData['cta_end']) : null);

            if (!empty($Image)) {
                if ($CurrentImage && file_exists("../../uploads/ctas/{$CurrentImage}") && !is_dir("../../uploads/ctas/{$CurrentImage}")) {
                    unlink("../../uploads/ctas/{$CurrentImage}");
                }

                $Upload = new Upload('../../uploads/');
                $Upload->image($Image, Check::name((string) $PostData['cta_title']), AVATAR_W, 'ctas');
                $PostData['cta_image'] = $Upload->getResult();
            }

            $Update->exeUpdate(DB_CTAS, $PostData, "WHERE cta_id = :id", "id={$CtaId}");
            $jSON['trigger'] = Check::ajaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O CTA foi atualizado com sucesso. E será exibido no intervalo de datas cadastradas!");
            break;

        case 'delete':
            $CtaId = filter_var($PostData['del_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$CtaId) {
                $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> CTA inválido para exclusão!', E_USER_WARNING);
                break;
            }

            $Read->fullRead("SELECT cta_image FROM " . DB_CTAS . " WHERE cta_id = :id", "id={$CtaId}");
            if ($Read->getResult()) {
                $CtaImage = (!empty($Read->getResult()[0]['cta_image']) ? $Read->getResult()[0]['cta_image'] : null);
                if ($CtaImage && file_exists("../../uploads/ctas/{$CtaImage}") && !is_dir("../../uploads/ctas/{$CtaImage}")) {
                    unlink("../../uploads/ctas/{$CtaImage}");
                }
            }

            $Delete->exeDelete(DB_CTAS, "WHERE cta_id = :id", "id={$CtaId}");
            $jSON['success'] = true;
            break;
    }

    if ($jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_WARNING);
        echo json_encode($jSON);
    }
} else {
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
