<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;
    use App\Models\Upload;

    session_start();
    require __DIR__ . '/../../vendor/autoload.php';

    if (
        !APP_PORTFOLIO || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) ||
        $_SESSION['userLogin']['user_level'] < LEVEL_WC_PORTFOLIO
    ) {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
            E_USER_ERROR
        );
        echo json_encode($jSON);
        die;
    }
//DEFINE O CALLBACK E RECUPERA O POST
    $jSON = null;
    $callBack = 'Portfolio';
    $postData = filter_input_array(INPUT_POST) ?? [];

//VALIDA AÇÃO
    if (
        !empty($postData)
        && isset($postData['callback_action'], $postData['callback'])
        && $postData['callback_action'] !== ''
        && $postData['callback'] === $callBack
    ) {
        //PREPARA OS DADOS
        $case = $postData['callback_action'];
        unset($postData['callback'], $postData['callback_action']);

        $Read ??= new Read();
        $Create ??= new Create();
        $Update ??= new Update();
        $Delete ??= new Delete();

        //SELECIONA AÇÃO
        switch ($case) {
            //DELETE
            case 'delete':
                $postData['id'] = $postData['del_id'];
                $Read->fullRead(
                    "SELECT img_970x500, img_450x350, img_350x350 FROM " . DB_PORTFOLIO . " WHERE id = :ps",
                    "ps={$postData['id']}"
                );
                if (
                    $Read->getResult() && file_exists(
                        "../../uploads/portfolio/{$Read->getResult()[0]['img_970x500']}"
                    ) && !is_dir("../../uploads/portfolio/{$Read->getResult()[0]['img_970x500']}")
                ) {
                    unlink("../../uploads/portfolio/{$Read->getResult()[0]['img_970x500']}");
                    unlink("../../uploads/portfolio/{$Read->getResult()[0]['img_450x350']}");
                    unlink("../../uploads/portfolio/{$Read->getResult()[0]['img_350x350']}");
                }
                $Delete->exeDelete(DB_PORTFOLIO, "WHERE id = :id", "id={$postData['id']}");
                $jSON['success'] = true;
                break;

            case 'manager':
                $PostId = filter_var($postData['id'] ?? null, FILTER_VALIDATE_INT);
                if (!$PostId) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>OPPSS:</b> Não foi possível identificar o registro do portfólio para salvar.',
                        E_USER_WARNING
                    );
                    break;
                }
                unset($postData['id']);

                $Read->exeRead(DB_PORTFOLIO, "WHERE id = :id", "id={$PostId}");
                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>OPPSS:</b> Este trampo não foi encontrado. Atualize a página e tente novamente.',
                        E_USER_WARNING
                    );
                    break;
                }
                $ThisPost = $Read->getResult()[0];

                $postData['slug'] = (!empty($postData['slug']) ? Check::name(
                    $postData['slug']
                ) : Check::name($postData['title']));
                $Read->exeRead(
                    DB_PORTFOLIO,
                    "WHERE id != :id AND slug = :slug_value",
                    "id={$PostId}&slug_value={$postData['slug'] }"
                );
                if ($Read->getResult()) {
                    $postData['slug'] = "{$postData['slug'] }-{$PostId}";
                }
                $jSON['name'] = $postData['slug'];

                $File = $_FILES['img_970x500'] ?? null;
                $File_2 = $_FILES['img_450x350'] ?? null;
                $File_3 = $_FILES['img_350x350'] ?? null;

                $hasFile970 = is_array($File) && (int)($File['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
                $hasFile450 = is_array($File_2) && (int)($File_2['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
                $hasFile350 = is_array($File_3) && (int)($File_3['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

                $Upload = new Upload('../../uploads/portfolio/');

                if ($hasFile970) {
                    if (
                        $ThisPost['img_970x500'] && file_exists(
                            "../../uploads/portfolio/{$ThisPost['img_970x500']}"
                        ) && !is_dir("../../uploads/portfolio/{$ThisPost['img_970x500']}")
                    ) {
                        unlink("../../uploads/portfolio/{$ThisPost['img_970x500']}");
                    }

                    $Upload->image($File, $postData['slug'] . '_970X500', 970);
                    if ($Upload->getResult()) {
                        $postData['img_970x500'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!",
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);
                        return;
                    }
                } else {
                    unset($postData['img_970x500']);
                }

                if ($hasFile450) {
                    if (
                        $ThisPost['img_450x350'] && file_exists(
                            "../../uploads/portfolio/{$ThisPost['img_450x350']}"
                        ) && !is_dir("../../uploads/portfolio/{$ThisPost['img_450x350']}")
                    ) {
                        unlink("../../uploads/portfolio/{$ThisPost['img_450x350']}");
                    }

                    $Upload->image($File_2, $postData['slug'] . '_450X350', 450);
                    if ($Upload->getResult()) {
                        $postData['img_450x350'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM DE 450x350px:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!",
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);
                        return;
                    }
                } else {
                    unset($postData['img_450x350']);
                }

                if ($hasFile350) {
                    if (
                        $ThisPost['img_350x350'] && file_exists(
                            "../../uploads/portfolio/{$ThisPost['img_350x350']}"
                        ) && !is_dir("../../uploads/portfolio/{$ThisPost['img_350x350']}")
                    ) {
                        unlink("../../uploads/portfolio/{$ThisPost['img_350x350']}");
                    }

                    $Upload->image($File_3, $postData['slug'] . '_350X350', 350);
                    if ($Upload->getResult()) {
                        $postData['img_350x350'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM DE 350x350px:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar como capa!",
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);
                        return;
                    }
                } else {
                    unset($postData['img_350x350']);
                }

                $postData['status'] = (!empty($postData['status']) ? '1' : '0');
                $postData['deliveryted_at'] = (!empty($postData['deliveryted_at']) ? Check::data(
                    $postData['deliveryted_at']
                ) : date('Y-m-d'));

                $Update->exeUpdate(DB_PORTFOLIO, $postData, "WHERE id = :id", "id={$PostId}");
                if (!$Update->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>OPPSS:</b> Não foi possível salvar o trampo. Verifique os dados e tente novamente.',
                        E_USER_WARNING
                    );
                    break;
                }

                $safeTitle = (string)($postData['title'] ?? ($ThisPost['title'] ?? ''));
                $safeSlug = (string)($postData['slug'] ?? ($ThisPost['slug'] ?? ''));
                $jSON['trigger'] = Check::ajaxErro(
                    "<b>TUDO CERTO:</b> O trampo <b>{$safeTitle}</b> foi atualizado com sucesso!"
                );
                $jSON['view'] = BASE . "/portfolio/{$safeSlug}";
                break;

            case 'sendimage':
                $NewImage = $_FILES['image'] ?? null;
                if (!is_array($NewImage)) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Nenhuma imagem foi recebida para upload.",
                        E_USER_WARNING
                    );
                    break;
                }
                $Read->fullRead(
                    "SELECT title, slug FROM " . DB_PORTFOLIO . " WHERE id = :id",
                    "id={$postData['id']}"
                );
                if (!$Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o post vinculado!",
                        E_USER_WARNING
                    );
                } else {
                    $Upload = new Upload('../../uploads/portfolio/');
                    $Upload->image($NewImage, $postData['id'] . '-' . time(), IMAGE_W);
                    if ($Upload->getResult()) {
                        $postData['image'] = $Upload->getResult();
                        $Create->exeCreate(DB_PORTFOLIO, $postData);
                        $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['title']}' alt='{$Read->getResult()[0]['title']}' src='../uploads/portfolio/{$postData['image']}'/>";
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no post!",
                            E_USER_WARNING
                        );
                    }
                }
                break;

            case 'cat_add':
                $postData = array_map('strip_tags', $postData);
                $CatId = $postData['id'];
                unset($postData['id']);

                $postData['slug'] = Check::name($postData['title']);
                $postData['parent'] = ($postData['parent'] ?: null);

                $Read->fullRead(
                    "SELECT id FROM " . DB_PORTFOLIO_CATEGORIES . " WHERE slug = :cn AND id != :ci",
                    "cn={$postData['slug']}&ci={$CatId}"
                );
                if ($Read->getResult()) {
                    $postData['slug'] = $postData['slug'] . '-' . $CatId;
                }

                $Read->fullRead(
                    "SELECT id FROM " . DB_PORTFOLIO_CATEGORIES . " WHERE parent = :ci",
                    "ci={$CatId}"
                );
                if ($Read->getResult() && !empty($postData['parent'])) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b>OPPSSS: </b> {$_SESSION['userLogin']['user_name']}, uma categoria PAI (que possui subcategorias) não pode ser atribuida como subcategoria",
                        E_USER_WARNING
                    );
                } else {
                    $Update->exeUpdate(DB_PORTFOLIO_CATEGORIES, $postData, "WHERE id = :id", "id={$CatId}");
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b>TUDO CERTO: </b> A categoria <b>{$postData['title']}</b> foi atualizada com sucesso!"
                    );
                }
                break;

            case 'cat_remove':
                $postData['id'] = $postData['del_id'];
                $Read->fullRead(
                    "SELECT title, id FROM " . DB_PORTFOLIO_CATEGORIES . " WHERE parent = :cat",
                    "cat={$postData['id']}"
                );

                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        "<b>OPPSSS: </b> Olá {$_SESSION['userLogin']['user_name']}, para deletar uma categoria certifique-se que ela não tem subcategorias cadastradas!",
                        E_USER_WARNING
                    );
                } else {
                    $Read->fullRead(
                        "SELECT id FROM " . DB_PORTFOLIO . " WHERE category = :cat ",
                        "cat={$postData['id']}"
                    );
                    if ($Read->getResult()) {
                        $jSON['trigger'] = Check::ajaxErro(
                            "<b>{$Read->getRowCount()} PORTFÓLIO: </b> Olá {$_SESSION['userLogin']['user_name']}, não é possível remover categorias quando existem trampos cadastrados na mesma!",
                            E_USER_WARNING
                        );
                    } else {
                        $Delete->exeDelete(
                            DB_PORTFOLIO_CATEGORIES,
                            "WHERE id = :cat",
                            "cat={$postData['id']}"
                        );
                        $jSON['success'] = true;
                    }
                }
                break;
        }


        //RETORNA O CALLBACK
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
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
