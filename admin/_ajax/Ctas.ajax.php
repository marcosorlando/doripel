<?php
session_start();
require '../../_app/Config.inc.php';
$NivelAcess = LEVEL_WC_CTAS;

if (!APP_CTAS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;
usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Ctas';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] == $CallBack):
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;
    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;
    // AUTO INSTANCE OBJECT UPDATE
    if (empty($Update)):
        $Update = new Update;
    endif;    
    // AUTO INSTANCE OBJECT DELETE
    if (empty($Delete)):
        $Delete = new Delete;
    endif;

    //SELECIONA AÇÃO
    switch ($Case):
        //GERENCIA
        case 'manager':
            $DepositionId = $PostData['cta_id'];
            
            $Image = (!empty($_FILES['cta_image']) ? $_FILES['cta_image'] : null);
            unset($PostData['cta_id'], $PostData['cta_image']);
            
            $Read->FullRead("SELECT cta_image FROM " . DB_CTAS . " WHERE cta_id = :id", "id={$DepositionId}");

            if (empty($Image) && (!$Read->getResult() || !$Read->getResult()[0]['cta_image'])):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Favor envie uma imagem nas medidas de 500X400px!', E_USER_ERROR);
            elseif (in_array('', $PostData)):
                $jSON['trigger'] = AjaxErro('<b class="icon-warning">ERRO AO CADASTRAR:</b> Para atualizar o CTA, favor preencha todos os campos!', E_USER_ERROR);
                $jSON['error'] = true;
            else:
                $PostData['cta_date'] = date('Y-m-d H:i:s');
                $PostData['cta_status'] = (!empty($PostData['cta_status']) ? 1 : 0);
      
                $PostData['cta_start'] = Check::Data($PostData['cta_start']);
                $PostData['cta_end'] = (!empty($PostData['cta_end']) ? Check::Data($PostData['cta_end']) : null);

                if (!empty($Image)):
                    if ($Read->getResult() && !empty($Read->getResult()[0]['cta_image']) && file_exists("../../uploads/ctas/{$Read->getResult()[0]['cta_image']}") && !is_dir("../../uploads/ctas/{$Read->getResult()[0]['cta_image']}")):
                        unlink("../../uploads/ctas/{$Read->getResult()[0]['cta_image']}");
                    endif;
                    $Upload = new Upload('../../uploads/');
                    $Upload->Image($Image, Check::Name($PostData['cta_title']), AVATAR_W, 'ctas');
                    $PostData['cta_image'] = $Upload->getResult();
                endif;
               
                $Update->ExeUpdate(DB_CTAS, $PostData, "WHERE cta_id = :id", "id={$DepositionId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: O CTA foi atualizado com sucesso. E será exibido no intervalo de datas cadastradas!");
            endif;
            break;

        //DELETA
        case 'delete':         
          
            $DepositionId = $PostData['del_id'];
            $Read->FullRead("SELECT cta_image FROM " . DB_CTAS . " WHERE cta_id = :id", "id={$DepositionId}");
            if ($Read->getResult()):
                $DepositionImage = (!empty($Read->getResult()[0]['cta_image']) ? $Read->getResult()[0]['cta_image'] : null);
                if ($DepositionImage && file_exists("../../uploads/ctas/{$DepositionImage}") && !is_dir("../../uploads/ctas/{$DepositionImage}")):
                    unlink("../../uploads/ctas/{$DepositionImage}");
                endif;
            endif;

            $Delete->ExeDelete(DB_CTAS, "WHERE cta_id = :id", "id={$DepositionId}");
            $jSON['success'] = true;
            break;
    endswitch;

    //RETORNA O CALLBACK
    if ($jSON):
        echo json_encode($jSON);
    else:
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
        echo json_encode($jSON);
    endif;
else:
    //ACESSO DIRETO
    die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;
