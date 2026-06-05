<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

// Upload is a global helper class (non-namespaced)

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_ALBUMS;

$jSON = [];
if (
    !isset($_SESSION['userLogin']['user_level'])
    || (int) $_SESSION['userLogin']['user_level'] < (int) $NivelAcess
) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo \json_encode($jSON);

    exit;
}

\usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$CallBack = 'Albums';
$PostData = \filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

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

    $Upload = new Upload('../../uploads/albuns/');

    // var_dump($PostData);
    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $AlbId = $PostData['album_id'];
            $PostData['album_status'] = ((isset($PostData['album_status']) && '' !== $PostData['album_status']) ? $PostData['album_status'] : '0');
            $Read->exeRead(DB_ALBUMS, 'WHERE album_id = :id', 'id='.$AlbId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar o album. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } elseif (
                (isset($PostData['album_start']) && '' !== $PostData['album_start']) && (!Check::Data(
                    $PostData['album_start']
                ) || !Check::Data($PostData['album_end']))
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas a(s) data(s) de divulgação foi informada com erro de calendário. Veja isso!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Album = $Read->getResult()[0];
                // var_dump($Album);
                unset($PostData['album_id'], $PostData['album_cover'], $PostData['image']);

                $PostData['album_name'] = ('' === $PostData['album_name'] ? Check::name(
                    $PostData['album_title']
                ) : Check::name($PostData['album_name']));

                // ENVIO DA CAPA DO ALBUM
                if (isset($_FILES['album_cover']) && '' !== (string) ($_FILES['album_cover']['name'] ?? '')) {
                    $File = $_FILES['album_cover'];

                    if (
                        $Album['album_cover'] && \file_exists(
                            '../../uploads/albuns/'.$Album['album_cover']
                        ) && !\is_dir('../../uploads/albuns/'.$Album['album_cover'])
                    ) {
                        \unlink('../../uploads/albuns/'.$Album['album_cover']);
                    }

                    $Upload->image($File, \sprintf('%s-%s-', $AlbId, $PostData['album_name']).\time(), 1000);
                    if ($Upload->getResult()) {
                        $PostData['album_cover'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 1200x628px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo \json_encode($jSON);

                        return;
                    }
                }

                // ENVIO DAS IMAGENS DO ALBUM
                if (
                    isset($_FILES['image']) && \is_array(
                        $_FILES['image']
                    ) && (0 === (int) ($_FILES['image']['error'][0] ?? 1))
                ) {
                    $File = $_FILES['image'];
                    $gbFile = [];
                    $gbFiles = [];
                    $gbCount = \count($File['type']);
                    $gbKeys = \array_keys($File);
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
                            \sprintf('%s-%d-', $AlbId, $gbLoop).\time().\base64_encode(\time()),
                            1000
                        );
                        if ($Upload->getResult()) {
                            $gbCreate = ['album_id' => $AlbId, 'image' => $Upload->getResult()];
                            $Create->exeCreate(DB_ALBUMS_IMAGE, $gbCreate);
                            $jSON['gallery'] .= \sprintf(
                                "<img rel='Albums' id='%s' alt='Imagem em %s' title='Imagem em %s' src='../tim.php?src=uploads/albuns/%s&w=1200&h=628'/>",
                                $Create->getResult(),
                                $PostData['album_title'],
                                $PostData['album_title'],
                                $Upload->getResult()
                            );
                            //                              ../tim.php?src=uploads/albuns/{$Upload->getResult()}&w=1200&h=628
                            //                          ../uploads/albuns/{$Upload->getResult()}
                        }
                    }
                }

                if (isset($PostData['album_subcategory'])) {
                    $Read->fullRead(
                        'SELECT presential_cat_parent FROM '.DB_PRESENTIAL_CATEGORIES.' WHERE presential_cat_id = :id',
                        'id='.$PostData['album_subcategory']
                    );
                    $PostData['album_category'] = ($Read->getResult() ? $Read->getResult(
                    )[0]['presential_cat_parent'] : null);
                }

                $Read->fullRead(
                    'SELECT album_id FROM '.DB_ALBUMS.' WHERE album_name = :nm AND album_id != :id',
                    \sprintf('nm=%s&id=%s', $PostData['album_name'], $AlbId)
                );
                if ($Read->getResult()) {
                    $PostData['album_name'] = \sprintf('%s-%s', $PostData['album_name'], $AlbId);
                }

                $PostData['album_start'] = (!empty($PostData['album_start']) && Check::Data(
                    $PostData['album_start']
                ) ? Check::Data($PostData['album_start']) : null);
                $PostData['album_end'] = (!empty($PostData['album_end']) && Check::Data(
                    $PostData['album_end']
                ) ? Check::Data($PostData['album_end']) : null);

                $PostData['album_status'] = (empty($PostData['album_status']) ? '0' : '1');

                $Update->exeUpdate(DB_ALBUMS, $PostData, 'WHERE album_id = :id', 'id='.$AlbId);

                $jSON['name'] = $PostData['album_name'];
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        "<span class='icon-checkmark'><b>ÁLBUM ATUALIZADO:</b> Olá %s. O álbum %s foi atualizado com sucesso!<span>",
                        $_SESSION['userLogin']['user_name'],
                        $PostData['album_title']
                    )
                );
            }

            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->fullRead(
                'SELECT album_title, album_name FROM '.DB_ALBUMS.' WHERE album_id = :id',
                'id='.$PostData['album_id']
            );
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe %s, mas não foi possível identificar o álbum vinculado!",
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Upload = new Upload('../../uploads/albuns/');
                $Upload->image($NewImage, $PostData['album_id'].'-'.\time(), IMAGE_W);
                if ($Upload->getResult()) {
                    $PostData['album_id'] = $PostData['album_id'];
                    $PostData['image'] = $Upload->getResult();
                    unset($PostData['album_id']);

                    $Create->exeCreate(DB_ALBUMS_IMAGE, $PostData);
                    $jSON['tinyMCE'] = \sprintf(
                        "<img title='%s' alt='%s' src='../uploads/albuns/%s'/>",
                        $Read->getResult()[0]['album_title'],
                        $Read->getResult()[0]['album_title'],
                        $PostData['image']
                    );
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma imagem JPG ou PNG para inserir no álbum!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                }
            }

            break;

        case 'removeimage':
            $Read->fullRead('SELECT image FROM '.DB_PDT_GALLERY.' WHERE id = :id', 'id='.$PostData['img']);
            if ($Read->getResult()) {
                $ImageRemove = '../../uploads/albuns/'.$Read->getResult()[0]['image'];
                if (\file_exists($ImageRemove) && !\is_dir($ImageRemove)) {
                    \unlink($ImageRemove);
                }

                $Delete->exeDelete(DB_ALBUMS_IMAGE, 'WHERE id = :id', 'id='.$PostData['img']);
                $jSON['success'] = true;
            }

            break;

        case 'delete':
            $AlbId = $PostData['del_id'];
            //            $Read->fullRead("SELECT album_id FROM " . DB_ORDERS_ITEMS . " WHERE album_id = :id", "id={$AlbId}");
            //            $AlbOrder = $Read->getResult();

            $Read->exeRead(DB_ALBUMS, 'WHERE album_id = :id', 'id='.$AlbId);

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    \sprintf(
                        '<b>OPSS:</b> Desculpe %s. Não foi possível deletar pois o produto não existe ou foi removido recentemente!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Album = $Read->getResult()[0];
                $AlbCover = '../../uploads/'.$Album['album_cover'];

                if (\file_exists($AlbCover) && !\is_dir($AlbCover)) {
                    \unlink($AlbCover);
                }

                $Read->exeRead(DB_ALBUMS_IMAGE, 'WHERE album_id = :id', 'id='.$Album['album_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $AlbImage) {
                        $AlbImageIs = '../../uploads/albuns/'.$AlbImage['image'];
                        if (\file_exists($AlbImageIs) && !\is_dir($AlbImageIs)) {
                            \unlink($AlbImageIs);
                        }
                    }

                    $Delete->exeDelete(DB_ALBUMS_IMAGE, 'WHERE album_id = :id', 'id='.$Album['album_id']);
                }

                //                $Read->exeRead(DB_PDT_GALLERY, "WHERE product_id = :id", "id={$Album['album_id']}");
                //                if ($Read->getResult()):
                //                    foreach ($Read->getResult() as $AlbGB):
                //                        $AlbGBImage = "../../uploads/{$AlbGB['image']}";
                //                        if (file_exists($AlbGBImage) && !is_dir($AlbGBImage)):
                //                            unlink($AlbGBImage);
                //                        endif;
                //                    endforeach;
                //                    $Delete->exeDelete(DB_PDT_GALLERY, "WHERE product_id = :id", "id={$Album['album_id']}");
                //                endif;

                $Delete->exeDelete(DB_ALBUMS, 'WHERE album_id = :id', 'id='.$Album['album_id']);

                $jSON['success'] = true;
            }

            break;
    }

    // RETORNA O CALLBACK
    if ([] !== $jSON) {
        echo \json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo \json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
