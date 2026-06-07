<?php

use App\Helpers\Check;
use App\Conn\Create;
use App\Models\Email;
use App\Conn\Read;

    //DEFINE O CALLBACK E RECUPERA O POST
    require_once '../../../_app/Config.inc.php';
    $jSON = null;
    $CallBack = 'Leads';
    $PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];
    //VALIDA AÇÃO
    if (isset($PostData['callback'], $PostData['callback_action']) && $PostData['callback'] === $CallBack){
        //PREPARA OS DADOS
        $Case = $PostData['callback_action'];
        unset($PostData['callback'], $PostData['callback_action']);
        $Read = new Read;
        //ELIMINA CÓDIGOS
        //$PostData = array_map('strip_tags', $PostData);
        //SELECIONA AÇÃO
        switch ($Case) {
            //CAPTURA DE ACORDO COM CALLBACK-ACTION
            case 'news1':
                if (in_array('', $PostData)){
                    $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
                } else {
                    if (!Check::email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)){
                        $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    } else {
                        $LeadData = [
                            'lead_name' => $PostData['name'],
                            'lead_email' => $PostData['email'],
                            'lead_phone' => null,
                            'lead_conversion' => $Case];
                        $Create = new Create;
                        $Create->exeCreate(DB_LEADS, $LeadData);
                        $jSON['trigger'] = Check::ajaxErro("<b>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
                        //$jSON['redirect'] = 'dashboard.php?wc=home';
                    }
                }
                break;
            case 'news2':
                if (in_array('', $PostData)){
                    $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> Informe seu e-mail por favor!', E_USER_NOTICE);
                } else {
                    if (!Check::email($PostData['email']) || !filter_var($PostData['email'], FILTER_VALIDATE_EMAIL)){
                        $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    } else {
                        $LeadData = [
                            'lead_name' => $PostData['name'],
                            'lead_email' => $PostData['email'],
                            'lead_conversion' => $Case
                        ];
                        $Read->fullRead("SELECT lead_email FROM " . DB_LEADS . " WHERE lead_email = :mail", "mail={$LeadData['lead_email']}");
                        if ($Read->getResult()){
                            $jSON['trigger'] = Check::ajaxErro("<b>{$LeadData['lead_name']}</b> Seu e-mail já está registrado em nossa Newsletter!", E_USER_NOTICE);
                        } else {
                            $Create = new Create;
                            $Create->exeCreate(DB_LEADS, $LeadData);
                            $jSON['trigger'] = Check::ajaxErro("<b>Obrigado!</b> Seu e-mail foi registrado com Sucesso!");
                        }
                    }
                }
                break;
            case 'cotacao':
                if (in_array('', $PostData)){
                    $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> Para receber sua cotação, por favor preencha todos os campos!', E_USER_NOTICE);
                } else {
                    if (!Check::email($PostData['lead_email']) || !filter_var($PostData['lead_email'], FILTER_VALIDATE_EMAIL)){
                        $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    } else {
                        $Cotacao = isset($PostData['lead_cotacao']) && is_array($PostData['lead_cotacao'])
                            ? implode(', ', $PostData['lead_cotacao']) : '';
                        $LeadData = [
                            'lead_name' => $PostData['lead_name'],
                            'lead_email' => $PostData['lead_email'],
                            'lead_phone' => $PostData['lead_phone'],
                            'lead_cotacao' => $Cotacao,
                            'lead_message' => $PostData['lead_message'],
                            'lead_conversion' => $Case,
                            'lead_status' => 1];
                        $Create = new Create;
                        $Create->exeCreate(DB_LEADS, $LeadData);
                        //DISPARA OS ALERTAS POR E-MAIL
                        $MailContent = '
                            <table width="550" style="font-family: Tahoma, sans-serif">
                                <tr><td>
                                    <p>Nova cotação recebida de <b> ' . $LeadData['lead_name'] . '</b> gerado pelo formulário do Site</p>
                                    <p>Nome do Remetente: ' . $LeadData['lead_name'] . ' </p>
                                    <p>E-mail para resposta: ' . $LeadData['lead_email'] . ' </p>
                                    <p><b>Produtos de Interesse:</b> ' . $LeadData['lead_cotacao'] . '</p>
                                    <p><b>Mensagem Recebida:</b><br>' . $LeadData['lead_message'] . ' </p>
                                </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Hora = date('H');
                        $Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');
                        $Agradecimento = '
                            <table width="550" style="font-family: Tahoma, sans-serif;">
                                <tr><td>
                                    <p>' . $Saudacao . ' <b>' . $LeadData['lead_name'] . '</b></p>
                                    <p><br>Em breve estaremos respondendo sua solicitação de orçamento. <br><br> Obrigado! </p><br>
                                    <p>Mais que embalagem... Envolvimento!</p>
                                    
                                    <p style="font-size: 1em;">
                                        <img src="' . INCLUDE_PATH . '/images/logo.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" />
                                        <br><br>
                                        ' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                                        Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                                    </p>
                                </td></tr>
                            </table>
                        <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Email = new Email;
                        $Email->EnviarMontando('Cotação originada pelo Site', $MailContent, $LeadData['lead_name'], $LeadData['lead_email'], SITE_ADDR_NAME, SITE_ADDR_EMAIL);
                        if (!$Email->getError()){
                            $Email->EnviarMontando('Confirmação de recebimento', $Agradecimento, SITE_ADDR_NAME, SITE_ADDR_EMAIL, $LeadData['lead_name'], $LeadData['lead_email']);
                            $jSON['trigger'] = Check::ajaxErro("<b>Obrigado!</b> Sua cotação foi recebida com Sucesso!");
                            //header('Location: ' . BASE . '/cotacao#cotacao');
                        } else {
                            $jSON['trigger'] = Check::ajaxErro("Desculpe, não foi possível enviar sua cotação. Entre em contato via por E-mail: " . SITE_ADDR_EMAIL . ". Obrigado!", E_USER_WARNING);
                        }
                    }
                }
                break;
            case 'representative':
                if (in_array('', $PostData)){
                    $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> Para enviar sua solicitação para ser nosso representante, por favor preencha todos os campos!', E_USER_NOTICE);
                } else {
                    if (!Check::email($PostData['rep_email']) || !filter_var($PostData['rep_email'], FILTER_VALIDATE_EMAIL)){
                        $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> E-mail informado não é válido!', E_USER_NOTICE);
                    } else {
                        $LeadData = [
                            'rep_name' => $PostData['rep_name'],
                            'rep_email' => $PostData['rep_email'],
                            'rep_phone' => $PostData['rep_phone'],
                            'rep_cnpj' => $PostData['rep_cnpj'],
                            'rep_cep' => $PostData['rep_cep'],
                            'rep_address' => $PostData['rep_address'],
                            'rep_address_number' => $PostData['rep_address_number'],
                            'rep_city' => $PostData['rep_city'],
                            'rep_uf' => $PostData['rep_uf'],
                            'rep_operation_field' => $PostData['rep_operation_field'],
                            'rep_message' => $PostData['rep_message'],
                            'rep_conversion' => $Case,
                            'rep_status' => 1];
                        $Create = new Create;
                        $Create->exeCreate(DB_REPRESENTATIVES, $LeadData);
                        //DISPARA OS ALERTAS POR E-MAIL
                        $MailContent = '
                            <table width="550" style="font-family: Tahoma, sans-serif">
                                <tr><td>
                                    <p><b> ' . $LeadData['rep_name'] . '</b> deseja ser nosso representante na cidade de   ' . $LeadData['rep_city'] . '</p>
                                    <p>Nome do Remetente: ' . $LeadData['rep_name'] . ' </p>
                                    <p>E-mail para resposta: ' . $LeadData['rep_email'] . ' </p>
                                    <p>Telefone: ' . $LeadData['rep_phone'] . ' </p>
                                    <p>CNPJ: ' . $LeadData['rep_cnpj'] . ' </p>
                                    <p><b>Localidade:</b>' . $LeadData['rep_address'] . ' Nº ' . $LeadData['rep_address_number'] . ' </p>
                                    <p>' . $LeadData['rep_city'] . ' - ' . $LeadData['rep_uf'] . ' </p>
                                    <p><b>Área(s) de atuação:</b> ' . $LeadData['rep_operation_field'] . '</p>
                                    <p><b>Leia o que ele nos enviou:</b><br>' . $LeadData['rep_message'] . ' </p>
                                </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Hora = date('H');
                        $Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');
                        $Agradecimento = '
                            <table width="550" style="font-family: Tahoma, sans-serif;">
                                <tr><td>
                                    <p>' . $Saudacao . ' <b>' . $LeadData['rep_name'] . '</b></p>
                                    <p><br>Em breve estaremos respondendo sua solicitação. <br><br> Obrigado! </p><br>
                                    <p>Mais que embalagem... Envolvimento!</p>
                                    
                                    <p style="font-size: 1em;">
                                        <img src="' . INCLUDE_PATH . '/images/logo/logo_dark.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" />
                                        <br><br>
                                        ' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                                        Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                                    </p>
                                </td></tr>
                            </table>
                            <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                        $Email = new Email;
                        $Email->EnviarMontando('Quero representar a Global Suprimentos', $MailContent, $LeadData['rep_name'], $LeadData['rep_email'], SITE_ADDR_NAME, SITE_ADDR_EMAIL);
                        if (!$Email->getError()){
                            $Email->EnviarMontando('Confirmação de recebimento', $Agradecimento, SITE_ADDR_NAME, SITE_ADDR_EMAIL, $LeadData['rep_name'], $LeadData['rep_email']);
                            $jSON['trigger'] = Check::ajaxErro("<b>Obrigado! {$LeadData['rep_name']}.</b> Sua solicitação foi recebida com Sucesso!");
                        } else {
                            $jSON['trigger'] = Check::ajaxErro("Desculpe, não foi possível enviar sua cotação. Entre em contato via por E-mail: " . SITE_ADDR_EMAIL . ". Obrigado!", E_USER_WARNING);
                        }
                    }
                }
                break;
        }
        //RETORNA O CALLBACK
        if ($jSON){
            echo json_encode($jSON);
        } else {
            $jSON['trigger'] = Check::ajaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_WARNING);
            echo json_encode($jSON);
        }
    } else {
        //ACESSO DIRETO
        exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
