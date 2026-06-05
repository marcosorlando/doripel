<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;
    use App\Models\Upload;

    \session_start();

    require __DIR__ . '/../../vendor/autoload.php';

    if (!APP_POSTS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < LEVEL_WC_POSTS) {
        Check::accessBlocked();
    }


// DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $CallBack = 'Posts';
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

        // SELECIONA AÇÃO
        switch ($Case) {
            // DELETE
            case 'delete':
                $PostData['post_id'] = $PostData['del_id'];
                $Read->fullRead(
                    'SELECT post_cover FROM ' . DB_POSTS . ' WHERE post_id = :ps',
                    'ps=' . $PostData['post_id']
                );
                if (
                    $Read->getResult() && \file_exists(
                        '../../uploads/' . $Read->getResult()[0]['post_cover']
                    ) && !\is_dir(
                        '../../uploads/' . $Read->getResult()[0]['post_cover']
                    )
                ) {
                    \unlink('../../uploads/' . $Read->getResult()[0]['post_cover']);
                }

                $Read->fullRead(
                    'SELECT image FROM ' . DB_POSTS_IMAGE . ' WHERE post_id = :ps',
                    'ps=' . $PostData['post_id']
                );
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $PostImage) {
                        $ImageRemove = '../../uploads/' . $PostImage['image'];
                        if (\file_exists($ImageRemove) && !\is_dir($ImageRemove)) {
                            \unlink($ImageRemove);
                        }
                    }
                }

                $Delete->exeDelete(DB_POSTS, 'WHERE post_id = :id', 'id=' . $PostData['post_id']);
                $Delete->exeDelete(DB_POSTS_IMAGE, 'WHERE post_id = :id', 'id=' . $PostData['post_id']);
                $Delete->exeDelete(DB_COMMENTS, 'WHERE post_id = :id', 'id=' . $PostData['post_id']);
                $jSON['success'] = true;

                break;

            case 'manager':
                $PostId = $PostData['post_id'] ?? null;
                if (empty($PostId)) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b>OPSS:</b> Não foi possível identificar o post para atualização.",
                        E_USER_WARNING
                    );
                    echo \json_encode($jSON);

                    return;
                }

                unset($PostData['post_id']);

                $Read->exeRead(DB_POSTS, 'WHERE post_id = :id', 'id=' . $PostId);
                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b>OPSS:</b> O post informado não foi encontrado para atualização.",
                        E_USER_WARNING
                    );
                    echo \json_encode($jSON);

                    return;
                }
                $ThisPost = $Read->getResult()[0];

                $PostData['post_name'] = (empty($PostData['post_name']) ? Check::name(
                    $PostData['post_title']
                ) : Check::name($PostData['post_name']));
                $Read->exeRead(
                    DB_POSTS,
                    'WHERE post_id != :id AND post_name = :name',
                    \sprintf('id=%s&name=%s', $PostId, $PostData['post_name'])
                );
                if ($Read->getResult()) {
                    $PostData['post_name'] = \sprintf('%s-%s', $PostData['post_name'], $PostId);
                }

                $jSON['name'] = $PostData['post_name'];

                if (
                    isset($_FILES['post_cover'])
                    && \is_array($_FILES['post_cover'])
                    && isset($_FILES['post_cover']['error'])
                    && (int)$_FILES['post_cover']['error'] !== \UPLOAD_ERR_NO_FILE
                ) {
                    $File = $_FILES['post_cover'];
                    if ((int)($File['error'] ?? \UPLOAD_ERR_NO_FILE) !== \UPLOAD_ERR_OK || empty($File['tmp_name'])) {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> O arquivo de capa não foi recebido corretamente.",
                            E_USER_WARNING
                        );
                        echo \json_encode($jSON);

                        return;
                    }

                    if (
                        $ThisPost['post_cover'] && \file_exists('../../uploads/' . $ThisPost['post_cover']) && !\is_dir(
                            '../../uploads/' . $ThisPost['post_cover']
                        )
                    ) {
                        \unlink('../../uploads/' . $ThisPost['post_cover']);
                    }

                    $Upload = new Upload('../../uploads/');
                    $Upload->image($File, $PostData['post_name'] . '-' . \time(), IMAGE_W);
                    if ($Upload->getResult()) {
                        $PostData['post_cover'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Selecione uma imagem JPG ou PNG para enviar como capa!",
                            E_USER_WARNING
                        );
                        echo \json_encode($jSON);

                        return;
                    }
                } else {
                    unset($PostData['post_cover']);
                }

                $PostData['post_status'] = (empty($PostData['post_status']) ? '0' : '1');
                $PostData['post_date'] = (empty($PostData['post_date']) ? \date('Y-m-d H:i:s') : Check::Data(
                    $PostData['post_date']
                ));
                if (\array_key_exists('post_month', $ThisPost)) {
                    $PostData['post_month'] = \date('m', \strtotime((string)$PostData['post_date']));
                }
                $PostData['post_category_parent'] = (empty($PostData['post_category_parent']) ? null : \implode(
                    ',',
                    $PostData['post_category_parent']
                ));

                $Update->exeUpdate(DB_POSTS, $PostData, 'WHERE post_id = :id', 'id=' . $PostId);
                $jSON['trigger'] = Check::ajaxErro('<b>TUDO CERTO: </b>O post foi salvo com sucesso!');
                $jSON['view'] = BASE . ('/artigo/' . $PostData['post_name']);

                break;

            case 'sendimage':
                $NewImage = $_FILES['image'];
                $Read->fullRead(
                    'SELECT post_title, post_name FROM ' . DB_POSTS . ' WHERE post_id = :id',
                    'id=' . $PostData['post_id']
                );
                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe %s, mas não foi possível identificar o post vinculado!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                } else {
                    $Upload = new Upload('../../uploads/');
                    $Upload->image($NewImage, $PostData['post_id'] . '-' . \time(), IMAGE_W);
                    if ($Upload->getResult()) {
                        $PostData['image'] = $Upload->getResult();
                        $Create->exeCreate(DB_POSTS_IMAGE, $PostData);
                        $jSON['tinyMCE'] = \sprintf(
                            "<img title='%s' alt='%s' src='../uploads/%s'/>",
                            $Read->getResult()[0]['post_title'],
                            $Read->getResult()[0]['post_title'],
                            $PostData['image']
                        );
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma imagem JPG ou PNG para inserir no post!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                    }
                }

                break;

            case 'sendvideo':
                $NewVideo = $_FILES['video'];

                $Read->fullRead(
                    'SELECT post_title, post_name FROM ' . DB_POSTS . ' WHERE post_id = :id',
                    'id=' . $PostData['post_id']
                );

                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b class='icon-video-camera'>ERRO AO ENVIAR VÍDEO:</b> Desculpe
		    {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o post vinculado!",
                        E_USER_WARNING
                    );
                } else {
                    $Upload = new Upload('../../uploads/');
                    $Upload->media(
                        $NewVideo,
                        Check::name($NewVideo['name']) . '-' . \time()
                    );

                    if ($Upload->getResult()) {
                        $PostData['video'] = $Upload->getResult();

                        $Create->exeCreate('ws_posts_videos', $PostData);

                        $jSON['tinyMCEvideo'] = \sprintf(
                            "<video controls autoplay width='100%%' height='auto'><source src='../uploads/%s' type='%s'></video>",
                            $PostData['video'],
                            $NewVideo['type']
                        );
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                "<b class='icon-video-camera'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma video (.mp4 ou .webm) para inserir no post!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                    }
                }

                break;

            case 'category_add':
                $PostData = \array_map('strip_tags', $PostData);
                $CatId = $PostData['category_id'];
                unset($PostData['category_id']);

                $PostData['category_name'] = Check::name($PostData['category_title']);
                $PostData['category_parent'] = ('' !== $PostData['category_parent'] && '0' !== $PostData['category_parent'] ? $PostData['category_parent'] : null);

                $Read->fullRead(
                    'SELECT category_id FROM ' . DB_CATEGORIES . ' WHERE category_name = :cn AND category_id != :ci',
                    \sprintf('cn=%s&ci=%s', $PostData['category_name'], $CatId)
                );
                if ($Read->getResult()) {
                    $PostData['category_name'] = $PostData['category_name'] . '-' . $CatId;
                }

                $Read->fullRead(
                    'SELECT category_id FROM ' . DB_CATEGORIES . ' WHERE category_parent = :ci',
                    'ci=' . $CatId
                );
                if (
                    $Read->getResult(
                    ) && (isset($PostData['category_parent']) && ('' !== $PostData['category_parent'] && '0' !== $PostData['category_parent']))
                ) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>OPPSSS:</b> Uma categoria PAI (que possui subcategorias) não pode ser atribuida como subcategoria',
                        E_USER_WARNING
                    );
                } else {
                    $Update->exeUpdate(DB_CATEGORIES, $PostData, 'WHERE category_id = :id', 'id=' . $CatId);
                    $jSON['trigger'] = Check::ajaxErro('<b>TUDO CERTO: </b>A categoria foi salva com sucesso!');
                }

                break;

            case 'category_remove':
                $PostData['category_id'] = $PostData['del_id'];
                $Read->fullRead(
                    'SELECT category_title, category_id FROM ' . DB_CATEGORIES . ' WHERE category_parent = :cat',
                    'cat=' . $PostData['category_id']
                );

                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>OPPSSS: </b>Para deletar uma categoria certifique-se que ela não tem subcategoria cadastrada!',
                        E_USER_WARNING
                    );
                } else {
                    $Read->fullRead(
                        'SELECT post_id FROM ' . DB_POSTS . ' WHERE post_category = :cat OR FIND_IN_SET(:cat, post_category_parent)',
                        'cat=' . $PostData['category_id']
                    );
                    if ($Read->getResult()) {
                        $jSON['trigger'] = Check::ajaxErro(
                            \sprintf(
                                '<b>%s POST(S): </b> Não é possível remover categorias quando existem posts cadastrados na mesma!',
                                $Read->getRowCount()
                            ),
                            E_USER_WARNING
                        );
                    } else {
                        $Delete->exeDelete(
                            DB_CATEGORIES,
                            'WHERE category_id = :cat',
                            'cat=' . $PostData['category_id']
                        );
                        $jSON['success'] = true;
                    }
                }

                break;
        }

        // RETORNA O CALLBACK
        if ($jSON) {
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
