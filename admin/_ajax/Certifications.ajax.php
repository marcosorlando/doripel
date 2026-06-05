<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_CERTIFICATIONS;

if (!APP_CERTIFICATIONS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Certifications';
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
            $CertId = $PostData['cert_id'];
            $PostData['cert_status'] = (empty($PostData['cert_status']) ? '0' : '1');

            $Read->exeRead(DB_CERT, 'WHERE cert_id = :id', 'id=' . $CertId);

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar a certificação. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Certification = $Read->getResult()[0];

                unset($PostData['cert_id'], $PostData['cert_cover']);

                $PostData['cert_name'] = (empty($PostData['cert_name']) ? Check::name(
                    $PostData['cert_title']
                ) : Check::name($PostData['cert_name']));

                // COVER UPLOAD
                if (!empty($_FILES['cert_cover'])) {
                    $File = $_FILES['cert_cover'];

                    if (
                        $Certification['cert_cover'] && file_exists(
                            '../../uploads/' . $Certification['cert_cover']
                        ) && !is_dir('../../uploads/' . $Certification['cert_cover'])
                    ) {
                        unlink('../../uploads/' . $Certification['cert_cover']);
                    }

                    $Upload->image($File, $PostData['cert_name'] . time(), 800);
                    if ($Upload->getResult()) {
                        $PostData['cert_cover'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 800px de largura para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                $Read->fullRead(
                    'SELECT cert_id FROM ' . DB_CERT . ' WHERE cert_name = :nm AND cert_id != :id',
                    sprintf('nm=%s&id=%s', $PostData['cert_name'], $CertId)
                );
                if ($Read->getResult()) {
                    $PostData['cert_name'] .= time();
                }

                $jSON['name'] = $PostData['cert_name'];
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<span><b>CERTIFICAÇAO ATUALIZADA:</b> Olá %s. A certificação %s foi atualizada com sucesso!<span>',
                        $_SESSION['userLogin']['user_name'],
                        $PostData['cert_title']
                    )
                );

                $Update->exeUpdate(DB_CERT, $PostData, 'WHERE cert_id = :id', 'id=' . $CertId);
                $jSON['view'] = BASE . '/certificacoes/' . $PostData['cert_name'];
            }

            break;

        case 'delete':
            $CertId = $PostData['del_id'];

            $Read->exeRead(DB_CERT, 'WHERE cert_id = :id', 'id=' . $CertId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPSS:</b> Desculpe %s. Não foi possível deletar pois a certificação não existe ou foi removida recentemente!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Certification = $Read->getResult()[0];
                $CertCover = '../../uploads/' . $Certification['cert_cover'];

                if (file_exists($CertCover) && !is_dir($CertCover)) {
                    unlink($CertCover);
                }

                $Delete->exeDelete(DB_CERT, 'WHERE cert_id = :id', 'id=' . $Certification['cert_id']);
                $jSON['success'] = true;
            }

            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->fullRead(
                'SELECT cert_title, cert_name FROM ' . DB_CERT . ' WHERE cert_id = :id',
                'id=' . $PostData['cert_id']
            );

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe %s, mas não foi possível identificar a certificação vinculada!",
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Upload = new Upload('../../uploads/');
                $Upload->image($NewImage, $Read->getResult()[0]['cert_title'] . '-' . time(), IMAGE_W);

                if ($Upload->getResult()) {
                    $PostData['certification_id'] = $PostData['cert_id'];
                    $PostData['image'] = $Upload->getResult();
                    unset($PostData['cert_id']);

                    $Create->exeCreate(DB_CERT_IMAGE, $PostData);
                    $jSON['tinyMCE'] = sprintf(
                        "<img title='%s' alt='%s' src='../uploads/%s'/>",
                        $Read->getResult()[0]['cert_title'],
                        $Read->getResult()[0]['cert_title'],
                        $PostData['image']
                    );
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma imagem JPG ou PNG para inserir na Certificação!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                }
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
