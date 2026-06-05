<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Email;


//RECUPERA O POST
    $jSON = null;
    $postData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
//VALIDA AÇÃO
    if (isset($postData['callback_action'], $postData['callback']) && $postData['callback'] === 'Contact') {
        //PREPARA OS DADOS
        $case = $postData['callback_action'];
        unset($postData['callback'], $postData['callback_action']);
        require_once __DIR__ . '/../../../vendor/autoload.php';

        //SELECIONA AÇÃO
        switch ($case) {
            //ENVIA EMAILS
            case 'send':
                if (in_array('', $postData)) {
                    $jSON['response'] = Check::ajaxErro(
                        "Para enviar seu contato, favor preencha todos os campos!",
                        E_USER_WARNING
                    );
                } elseif (!Check::Email($postData['email']) || !filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
                    $jSON['response'] = Check::ajaxErro(
                        "Desculpe, mas o e-mail que você informou não tem um formato válido!",
                        E_USER_ERROR
                    );
                } else {
                    $postData = array_map('strip_tags', $postData);
                    $data = date('d/m/Y H:i:s');

                    $MailContent = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta Content-type: text/html; charset='UTF-8' />
<style type='text/css'>body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif;} a:link {text-decoration: none; color: blue;} a:visited { text-decoration: none; } a:hover { text-decoration: none; color: green;} a:active { text-decoration: none; } .moz-forward-container .original-only, blockquote .original-only, .WordSection1 .original-only {width: 0px; height: 0px; overflow: hidden; display: none !important;} #outlook a{padding:0;} p {margin: 4px;} .ExternalClass {width:100%;} .ExternalClass p {margin:0px} .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} table td {border-collapse:collapse; table td {border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;} a {word-wrap:break-word;} .easygoi-rss img {height: auto; width: 100%;} small{font-size:13px; color: #333;}
</style>

</head>
<body>
<span style='visibility: hidden; display: none !important; display:none'> Olá, seja bem-vindo a ZEN, uma agência de marketing digital e desenvolvimento de sites, portais, e-commerces, lojas virtuais, sistemas web. Entre, sinta-se em casa.</span>

<table border='0' width='100%' cellspacing='0' cellpadding='0'>
	<tr bgcolor='#000000' height='150px' align='center' style='background-color: #333; height='150px !important'>
		<td>
			<br>
				<a href='http://zen.ppg.br/' title=\'Zen Agência Web - Marketing Digital\' target=\'_blank\'>
					<img src='http://blog.zen.ppg.br/themes/new/images/zen_agencia_web_logotipo_hw.png'>
				</a>
			<br>
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
				<td style='width: 580px;'>
					<table width='580' cellspacing='0' cellpadding='0' border='0' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
					<tr>
					<td valign='top' style='display: block; width: 580px; mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-line-height-rule:exactly;'>
					
					<table  width='580' cellspacing='0' cellpadding='0' border='0'>
					<tr>
					<td  valign='top' align='left' style='width: 580px;'>
					<table  cellspacing='0' cellpadding='0' border='0' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><tr><td valign='top' style='color: rgb(0, 0, 0); display: block; width: 580px; mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-line-height-rule:exactly;'><br><br>
					
					<h2>Olá prezado(a) Sr.(a) " . $postData['nome'] . "</h2>
					<p>Obrigado pelo contato.<br><br><strong>Em breve estaremos respondendo sua solicitação.</strong>
					
					<br><br/>
					<b>NÓS PROJETAMOS & ENTREGAMOS SOLUÇÕES INOVADORAS</b><br><br>
					<b><em>Somos a Zen Agência Web</em></b><br>
Uma agência especializada em prestar serviços na área do desenvolvimento técnico e criativo de produtos relacionados a Internet. Identificamos oportunidades acionáveis, em seguida, priorizamos por valor, esforço e risco. A seguir <b>HARDWORK</b> começamos a <i>fazer</i> o mais rápido possível. As atividades incluem mapeamento de produto, realização de pesquisas de usuário, planejamento de conteúdo e tecnologia para atingir o objetivo dentro do prazo estimado.
<br>
<small>
Nós amamos encontrar soluções simples para problemas complexos. Focamos nosso trabalho no resultado em cada detalhe, desde a concepção ao código.
</small>
</p>

<br><br>

	<h3>Conteúdos exclusivos sobre Marketing Digital, Design e Desenvolvimento Web em nosso Blog:<a target='_blank' href='http://blog.zen.ppg.br'> ACESSE AGORA!</a></h3><br>

	Até mais!<br><br> Marcos Moreira<br><br></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td><td width='50%' valign='top'></td></tr></table>
	<table class='egoi-footer' border='0' width='100%' cellspacing='0' cellpadding='0' style='background-color: #CCCCCC'>
    <tr>
        <td style='background-color: #ff6600; height: 2px;' colspan='3'></td>
    </tr>
    <tr>
        <td width='50%'></td>
        <td width='650'>
            <table width='650' cellpadding='5' cellspacing='0'>

                 <tr>
                    <td style='text-align: center;'>
                        <table width='100%' >
                            <tr>
                                <td width='650' style='font-style: normal;font-family: Arial, Helvetica, sans-serif; text-align: left; font-size: 13px; color:#333333;'>Esta mensagem foi enviada para <b> " . $postData['email'] . " </b> por <b>contato@zen.ppg.br</b><br>Zen Agência Web, Caxias do Sul - RS - Brasil<br><br>
								<p>" . $data . "</p>															
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
</html>
";


                    $MailContactSite = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<meta Content-type: text/html; charset='UTF-8' />
<style type='text/css'>body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif;} a:link {text-decoration: none; color: blue;} a:visited { text-decoration: none; } a:hover { text-decoration: none; color: green; }a:active { text-decoration: none; } .moz-forward-container .original-only, blockquote .original-only, .WordSection1 .original-only {width: 0px; height: 0px; overflow: hidden; display: none !important;}#outlook a{padding:0;} p {margin: 2px;} .ExternalClass {width:100%;} .ExternalClass p {margin:0px} .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} table td {border-collapse:collapse; table td {border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;} a {word-wrap:break-word;} .easygoi-rss img {height: auto; width: 100%;}
</style>

</head>
<body>
<span style='visibility: hidden; display: none !important; display: none;'> Empresa especializada na locação de trajes para festas e eventos. Locamos Ternos, Vestidos, Sapatos e Trajes Infantis.</span>	

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
					<table  cellspacing='0' cellpadding='0' border='0' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><tr><td valign='top' style='color: rgb(0, 0, 0); display: block; width: 580px; mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-line-height-rule:exactly;'>
					
					<h2>Contato Fale conosco de " . $postData['nome'] . "</h2>
					<p>" . $postData['mensagem'] . "</p><br>
					<p> Telefone de contato: <b>" . $postData['phone'] . "</b></p>
						


<br><br>

	</td></tr></table></td></tr></table></td></tr></table></td></tr></table></td>
	<td width='50%' valign='top'></td></tr></table>	
								
	<table class='egoi-footer' border='0' width='100%' cellspacing='0' cellpadding='0' style='background-color: #CCCCCC'>
    <tr>
        <td style='background-color: #ff6600; height: 2px;' colspan='3'></td>
    </tr>
    <tr>
        <td width='50%'></td>
        <td width='650'>
            <table width='650' cellpadding='5' cellspacing='0'>

                 <tr>
                    <td style='text-align: center;'>
                        <table width='100%' >
                            <tr>
                                <td width='650' style='font-style: normal;font-family: Arial, Helvetica, sans-serif; text-align: left; font-size: 13px; color:#333333;'>Esta mensagem foi enviada via <b> Website: " . BASE . " </b> em <br>$data<br><br>
																						
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

                    $Email->EnviarMontando(
                        $postData['assunto'],
                        $MailContactSite,
                        $postData['nome'],
                        $postData['email'],
                        SITE_ADDR_NAME,
                        MAIL_USER
                    );

                    if (!$Email->getError()) {
                        $Email->EnviarMontando(
                            'Confirmação de Recebimento',
                            $MailContent,
                            SITE_ADDR_NAME,
                            MAIL_USER,
                            $postData['nome'],
                            $postData['email']
                        );
                        $jSON['wc_send_mail'] = $postData['nome'];
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            "Desculpe, não foi possível enviar sua mensagem. Entre em contato via " . SITE_ADDR_EMAIL . ". Obrigado!",
                            E_USER_ERROR
                        );
                    }
                }

                break;

            case 'newsletter':

                // Normalize and validate newsletter email safely
                $email = (string)($postData['newsletter_email'] ?? '');
                $email = trim(strip_tags($email));

                $Read = new Read();
                $Read->linkResult(
                    DB_LEADS,
                    'lead_email',
                    $email,
                    'lead_email',
                );
                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro('Seu e-mail já está cadastrado.', E_USER_NOTICE);
                } elseif ('' === $email) {
                    $jSON['trigger'] = Check::ajaxErro('Informe seu e-mail por favor.', E_USER_NOTICE);
                } elseif (!Check::email($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $jSON['trigger'] = Check::ajaxErro('E-mail informado não é válido.', E_USER_ERROR);
                } else {
                    $LeadData = [
                        'lead_name' => ' ',
                        'lead_email' => $email,
                        'lead_conversion' => $case
                    ];

                    $Create = new Create;
                    $Create->exeCreate(DB_LEADS, $LeadData);

                    $jSON['trigger'] = Check::ajaxErro("E-mail registrado com Sucesso!");
                }
                break;
        }

        //RETORNA O CALLBACK
        if ($jSON) {
            echo json_encode($jSON);
        } else {
            $jSON['trigger'] = Check::ajaxErro(
                '<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
                E_USER_ERROR
            );
            echo json_encode($jSON);
        }
    } else {
        //ACESSO DIRETO
        die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
    }
