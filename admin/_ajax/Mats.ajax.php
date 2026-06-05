<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = 6;

if (!APP_MATERIALS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Mats';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read ??= new Read();
    $Create ??= new Create();
    $Update ??= new Update();
    $Delete ??= new Delete();

    // SELECIONA AÇÃO
    switch ($Case) {
        // DELETE
        case 'delete':
            $PostData['mat_id'] = $PostData['del_id'];
            $Read->fullRead(
                'SELECT mat_cover FROM ' . DB_MATERIAIS . ' WHERE mat_id = :ps',
                'ps=' . $PostData['mat_id']
            );
            if (
                $Read->getResult() && file_exists('../../uploads/' . $Read->getResult()[0]['mat_cover']) && !is_dir(
                    '../../uploads/' . $Read->getResult()[0]['mat_cover']
                )
            ) {
                unlink('../../uploads/' . $Read->getResult()[0]['mat_cover']);
            }

            $Delete->exeDelete(DB_MATERIAIS, 'WHERE mat_id = :id', 'id=' . $PostData['mat_id']);

            $jSON['success'] = true;

            break;

        case 'manager':
            $PostId = $PostData['mat_id'];
            unset($PostData['mat_id']);

            $Read->exeRead(DB_MATERIAIS, 'WHERE mat_id = :id', 'id=' . $PostId);
            $ThisPost = $Read->getResult()[0];

            $PostData['mat_name'] = (empty($PostData['mat_name']) ? Check::name($PostData['mat_title']) : Check::name(
                $PostData['mat_name']
            ));
            $Read->exeRead(
                DB_MATERIAIS,
                'WHERE mat_id != :id AND mat_name = :name',
                sprintf('id=%s&name=%s', $PostId, $PostData['mat_name'])
            );
            if ($Read->getResult()) {
                $PostData['mat_name'] = sprintf('%s-%s', $PostData['mat_name'], $PostId);
            }

            $jSON['name'] = $PostData['mat_name'];

            if (!empty($_FILES['mat_cover'])) {
                $File = $_FILES['mat_cover'];

                if (
                    $ThisPost['mat_cover'] && file_exists('../../uploads/' . $ThisPost['mat_cover']) && !is_dir(
                        '../../uploads/' . $ThisPost['mat_cover']
                    )
                ) {
                    unlink('../../uploads/' . $ThisPost['mat_cover']);
                }

                $Upload = new Upload('../../uploads/');
                $Upload->image($File, $PostData['mat_name'] . '-' . time(), 400);
                if ($Upload->getResult()) {
                    $PostData['mat_cover'] = $Upload->getResult();
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
                unset($PostData['mat_cover']);
            }

            $PostData['mat_status'] = (empty($PostData['mat_status']) ? '0' : '1');
            $PostData['mat_date'] = (empty($PostData['mat_date']) ? date('Y-m-d H:i:s') : Check::data(
                $PostData['mat_date']
            ));
            $PostData['mat_category_parent'] = (empty($PostData['mat_category_parent']) ? null : implode(
                ',',
                $PostData['mat_category_parent']
            ));

            $Update->exeUpdate(DB_MATERIAIS, $PostData, 'WHERE mat_id = :id', 'id=' . $PostId);
            $jSON['trigger'] = Check::ajaxErro(
                sprintf(
                    '<b>Ok!</b> O material <b>%s</b> foi atualizado com sucesso!',
                    $PostData['mat_title']
                )
            );
            $jSON['view'] = BASE . ('/materiais#' . $PostData['mat_name']);

            break;

        case 'category_add':
            $PostData = array_map('strip_tags', $PostData);
            $CatId = $PostData['category_id'];
            unset($PostData['category_id']);

            $PostData['category_name'] = Check::name($PostData['category_title']);
            $PostData['category_parent'] = ('' !== $PostData['category_parent'] && '0' !== $PostData['category_parent'] ? $PostData['category_parent'] : null);

            $Read->fullRead(
                'SELECT category_id FROM ' . DB_MATCATEGORIES . ' WHERE category_name = :cn AND category_id != :ci',
                sprintf('cn=%s&ci=%s', $PostData['category_name'], $CatId)
            );
            if ($Read->getResult()) {
                $PostData['category_name'] = $PostData['category_name'] . '-' . $CatId;
            }

            $Read->fullRead(
                'SELECT category_id FROM ' . DB_MATCATEGORIES . ' WHERE category_parent = :ci',
                'ci=' . $CatId
            );
            if (
                $Read->getResult(
                ) && (isset($PostData['category_parent']) && ('' !== $PostData['category_parent'] && '0' !== $PostData['category_parent']))
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPPSSS: </b> %s, uma categoria PAI (que possui subcategorias) não pode ser atribuida como subcategoria',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Update->exeUpdate(DB_MATCATEGORIES, $PostData, 'WHERE category_id = :id', 'id=' . $CatId);
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Ok!</b> A categoria <b>%s</b> foi atualizada com sucesso!',
                        $PostData['category_title']
                    )
                );
            }

            break;

        case 'category_remove':
            $PostData['category_id'] = $PostData['del_id'];
            $Read->fullRead(
                'SELECT category_title, category_id FROM ' . DB_MATCATEGORIES . ' WHERE category_parent = :cat',
                'cat=' . $PostData['category_id']
            );

            if ($Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        "<b>OPPSSS: </b> Olá %s, para deletar uma categoria certifique-se que ela não tem subcategorias cadastradas!",
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Read->fullRead(
                    'SELECT mat_id FROM ' . DB_MATERIAIS . ' WHERE mat_category = :cat OR FIND_IN_SET(:cat, mat_category_parent)',
                    'cat=' . $PostData['category_id']
                );
                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            '<b>%s MATERIAL(S): </b> Olá %s, não é possível remover categorias quando existem materiais cadastrados na mesma!',
                            $Read->getRowCount(),
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                } else {
                    $Delete->exeDelete(DB_MATCATEGORIES, 'WHERE category_id = :cat', 'cat=' . $PostData['category_id']);
                    $jSON['success'] = true;
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
