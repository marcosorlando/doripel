<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;
    use App\Models\Upload;

    session_start();

    require __DIR__ . '/../../vendor/autoload.php';

    $jSON = [];
    if (!isset($_SESSION['userLogin']['user_level']) || (int)$_SESSION['userLogin']['user_level'] < LEVEL_WC_PARTNERS) {
        echo Check::accessBlocked();
    }

    usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
    $jSON = [];
    $CallBack = 'Partners';
    $PostData = filter_input_array(INPUT_POST) ?? [];

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
            case 'manager':
                $PartnerID = $PostData['partner_id'];

                $Image = (empty($_FILES['partner_image']) ? null : $_FILES['partner_image']);
                unset($PostData['partner_id'], $PostData['partner_image']);

                $Read->fullRead(
                    'SELECT partner_image FROM ' . DB_PARTNERS . ' WHERE partner_id = :id',
                    'id=' . $PartnerID
                );

                if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['partner_image'])) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>ERRO AO CADASTRAR:</b> Favor envie uma FOTO nas medidas de 300 X 200px!',
                        E_USER_ERROR
                    );
                } elseif (in_array('', $PostData, true)) {
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>ERRO AO CADASTRAR:</b> Para atualizar o cliente/parceiro, favor preencha todos os campos!',
                        E_USER_ERROR
                    );
                    $jSON['error'] = true;
                } else {
                    $PostData['partner_date'] = date('Y-m-d H:i:s');

                    if (!empty($Image)) {
                        if (
                            $Read->getResult() && !empty($Read->getResult()[0]['partner_image']) && file_exists(
                                '../../uploads/partners/' . $Read->getResult()[0]['partner_image']
                            ) && !is_dir('../../uploads/partners/' . $Read->getResult()[0]['partner_image'])
                        ) {
                            unlink('../../uploads/partners/' . $Read->getResult()[0]['partner_image']);
                        }

                        $Upload = new Upload('../../uploads/');
                        $Upload->image(
                            $Image,
                            Check::name($PostData['partner_name']),
                            300,
                            'partners'
                        );
                        $PostData['partner_image'] = $Upload->getResult();
                    }

                    $partnerPage = trim((string)($PostData['partner_page'] ?? ''));
                    if ('' !== $partnerPage && !preg_match('/^https?:\/\//i', $partnerPage)) {
                        $partnerPage = 'https://' . ltrim($partnerPage, '/');
                    }

                    if ('' !== $partnerPage && !filter_var($partnerPage, FILTER_VALIDATE_URL)) {
                        $jSON['trigger'] = Check::ajaxErro(
                            '<b>ERRO AO CADASTRAR:</b> Informe uma URL válida para o site do parceiro!',
                            E_USER_ERROR
                        );
                        $jSON['error'] = true;

                        break;
                    }

                    $PostData['partner_page'] = $partnerPage;
                    $Update->exeUpdate(DB_PARTNERS, $PostData, 'WHERE partner_id = :id', 'id=' . $PartnerID);
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            '<b>Tudo certo %s</b>: O parceiro foi atualizado com sucesso. E será exibido no site.',
                            $_SESSION['userLogin']['user_name']
                        )
                    );
                }

                break;

            case 'delete':
                $PartnerID = $PostData['del_id'];
                $Read->fullRead(
                    'SELECT partner_image FROM ' . DB_PARTNERS . ' WHERE partner_id = :id',
                    'id=' . $PartnerID
                );
                if ($Read->getResult()) {
                    $PartnerImage = (empty($Read->getResult()[0]['partner_image']) ? null : $Read->getResult(
                    )[0]['partner_image']);
                    if (
                        $PartnerImage && file_exists('../../uploads/' . $PartnerImage) && !is_dir(
                            '../../uploads/' . $PartnerImage
                        )
                    ) {
                        unlink('../../uploads/' . $PartnerImage);
                    }
                }

                $Delete->exeDelete(DB_PARTNERS, 'WHERE partner_id = :id', 'id=' . $PartnerID);
                $jSON['success'] = true;

                break;
        }

        // RETORNA O CALLBACK
        if ([] !== $jSON) {
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
