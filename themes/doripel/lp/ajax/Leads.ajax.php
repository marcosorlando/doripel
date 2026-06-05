<?php

    //DEFINE O CALLBACK E RECUPERA O POST
    require_once '../../../../_app/Config.inc.php';

    $jSON = null;
    $CallBack = 'Leads';
    $PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    var_dump($PostData);

    die;


    //VALIDA AÇÃO
    if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):

        var_dump($PostData);

    die;

        //PREPARA OS DADOS
        $Case = $PostData['callback_action'];

        $ebook_title = $PostData['callback_title'];
        $ebook_link = $PostData['callback_link'];
        $ebook_mokup = $PostData['page_mockup'];

        $humanA = $PostData['checkHuman_a'];
        $humanB = $PostData['checkHuman_b'];
        $humanCheck = $PostData['senderHuman'];
        $human = ($humanCheck == $humanA + $humanB) ? true : false;
        $Link = $PostData['page_destino'];

        unset($PostData['callback'], $PostData['callback_action'], $PostData['callback_title'], $PostData['callback_link'], $PostData['checkHuman_a'],      $PostData['checkHuman_b'], $PostData['senderHuman'], $PostData['page_destino'], $PostData['page_mockup'], $humanA, $humanB);

        //ELIMINA CÓDIGOS
        $PostData = array_map('strip_tags', $PostData);

        //SELECIONA AÇÃO
        switch ($Case):
            //CAPTURA DE ACORDO COM CALLBACK-ACTION
            case 'manage':

                if (in_array('', $PostData)):

                    $jSON['trigger'] = AjaxErro('<strong>OPPSSS:</strong> Favor preencha todos os campos!', E_USER_NOTICE);

                elseif (!Check::Email($PostData['lead_email']) || !filter_var($PostData['lead_email'], FILTER_VALIDATE_EMAIL)):

                    $jSON['trigger'] = AjaxErro('<strong>OPPSSS:  </strong>' . $PostData['lead_name'] . ' o e-mail informado não é válido!', E_USER_NOTICE);

                elseif ($human == false):

                    $jSON['trigger'] = AjaxErro('<strong>OPPSSS:  </strong>' . $PostData['lead_name'] . ', para receber seu material, realize a soma dos números corretamente', E_USER_WARNING);
                else:

                    $Create = new Create;
                    $Create->ExeCreate(DB_LEADS, $PostData);

                    $BaseDir = BASE;
                    $Path = INCLUDE_PATH;

                    $MailContent = "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta Content-type: text/html; charset='UTF-8' />
<style type='text/css'>body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif;} a:link {text-decoration: none; color: blue;} a:visited { text-decoration: none; } a:hover { text-decoration: none; color: green; } a:active { text-decoration: none; } .moz-forward-container .original-only, blockquote .original-only, .WordSection1 .original-only {width: 0px; height: 0px; overflow: hidden; display: none !important;}#outlook a{padding:0;} p {margin: 0px;} p {margin-bottom:0; margin:0;} .ExternalClass {width:100%;} .ExternalClass p {margin:0px} .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} table td {border-collapse:collapse; table td {border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;} a {word-wrap:break-word;} .easygoi-rss img {height: auto; width: 100%;}
</style>

</head>
<body>
<span style='visibility: hidden; display: none !important; display: none;'>Doripel Móveis.</span>

<table border='0' width='100%' cellspacing='0' cellpadding='0'>
	<tr bgcolor='#eeeeee' height='150px' align='center' style='background-color: #eeeeee; height='150px !important'>
		<td>
			<br>
				<a href='{$BaseDir}' title=\'Doripel Móveis\' target=\'_blank\'>
					<img src='{$Path}/images/logo.png' alt='Doripel Móvies - Logo'>
				</a>
			<br>
		</td>
	</tr>
</table>	

<table border='0' width='100%' cellspacing='0' cellpadding='0'>
	<tr>
		<td width='50%' valign='top' ></td>
		<td valign='top' >
			<table width='580' border='0' cellspacing='0' cellpadding='0' >
			<tr>
				<td  style='width: 580px;'>
					<table width='580'  cellspacing='0' cellpadding='0' border='0' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
					<tr>
					<td valign='top' style='display: block; width: 580px; mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-line-height-rule:exactly;'>
					
					<table  width='580' cellspacing='0' cellpadding='0' border='0'>
					<tr>
					<td  valign='top' align='left' style='width: 580px;'>
					<table  cellspacing='0' cellpadding='0' border='0' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><tr><td valign='top' style='color: rgb(0, 0, 0); display: block; width: 580px; mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-line-height-rule:exactly;'><br><br>
					
					<h2>Olá prezado(a) Sr.(a) " . $PostData['lead_name'] . "</h2>
					<p> Boa leitura, segue abaixo link para visualizar e salvar seu <b>Catálogo:<b></p>
					
                    <a href='{$Link}' style='text-decoration=none'>
                      <img src='{$BaseDir}/uploads/landingpages/{$ebook_mokup}' /><br>					
					    Ler ou Salvar: {$ebook_title}
					    </a>
                    <br><br>

	<h3>Junte-se a nós, acompanhe as novidades:</h3><br>
	<a target='_blank' title='Siga-nos no Facebook ' href='https://www.facebook.com/DoripelMoveis'>
    <img width='40px' height='40px' src='{$Path}/images/facebook-icon.png'/>
    </a>
    
    <a target='_blank' title='Siga-nos no Instagram ' href='https://www.instagram.com/moveis.doripel/'>
    <img width='40px' height='40px' src='{$Path}/images/instagram-icon.png'/>
    </a>
	
	<br>

	Um abraço!<br><br> Equipe Doripel<br><br></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td><td width='50%' valign='top'></td></tr></table>									
	<table class='egoi-footer' border='0' width='100%' cellspacing='0' cellpadding='0' style='background-color: #eeeeee'>
    <tr>
        <td style='background-color: #6699cc; height: 2px;' colspan='3'></td>
    </tr>
    <tr>
        <td width='50%'></td>
        <td width='650'>
            <table width='650' cellpadding='5' cellspacing='0'>

                 <tr>
                    <td style='text-align: center;'>
                        <table width='100%' >
                            <tr>
                                <td width='650' style='font-style: normal;font-family: Arial, Helvetica, sans-serif; text-align: left; font-size: 13px; color:#333333;'>Esta mensagem foi enviada para  " . $PostData['lead_email'] . " por comercial@doripel.com.br <br>Doripel Móveis, Lagoa Vermelha - RS - Brasil<br><br>
                                														
<span style='font-style: normal;font-family: Arial, Helvetica, sans-serif; text-align: left; font-size: 13px; font-weight:bold; color:#666666;'>

<span class='original-only'> </span>
</td>
                            </tr>
                        </table>
                        </td>
                </tr>
                <tr>
                    <td width='650' style='font-style: normal;font-family: Arial, Helvetica, sans-serif; text-align: left; color:#000000; font-size: 13px; height:40px; vertical-align:top;'></td>
                </tr>
            </table>
        </td>
        <td width='50%'></td>
    </tr>

<!-- ////// linha necessária para não quebrar o footer no Mac Mail //// -->     
    <tr>
      <td colspan='3' width='100%'></td>
    </tr> 
</table>
</body>
</html>";

                    $Email = new Email;
                    //EnviarMontando($Assunto, $Mensagem, $RemetenteNome, $RemetenteEmail, $DestinoNome, $DestinoEmail) {
                    $Email->EnviarMontando('E-book: ' . $ebook_title, $MailContent, SITE_ADDR_NAME, MAIL_USER, $PostData['lead_name'], $PostData['lead_email']);

                    $jSON['trigger'] = AjaxErro("<strong>Obrigado!</strong> Um link foi enviado para seu e-mail para salvar seu E-book!");
                    $jSON['redirect'] = $Link;
                    //endif;
                endif;
                break;
        endswitch;

        //RETORNA O CALLBACK
        if ($jSON):
            echo json_encode($jSON);
        else:
            $jSON['trigger'] = AjaxErro('<strong class="icon-warning">OPSS:</strong> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
            echo json_encode($jSON);
        endif;
    else:
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    endif;