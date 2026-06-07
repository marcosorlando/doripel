<?php

    use App\Helpers\Check;
    use App\Models\Email;


    $Read->exeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';

        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>
<!-- start page title section -->
<section class="wow fadeIn bg-light-gray padding-35px-tb page-title-small top-space">
	<div class="container">
		<div class="row equalize xs-equalize-auto">
			<div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 display-table">
				<div class="display-table-cell vertical-align-middle text-left xs-text-center">
					<!-- start page title -->
					<h1 class="alt-font text-deep-pink font-weight-600 no-margin-bottom text-uppercase">Fale com a
						Doripel</h1>
					<!-- end page title -->
				</div>
			</div>
		</div>
	</div>
</section>
<!-- end page title section -->
<!-- start help section -->
<!--<section class="wow fadeIn big-section">-->
<!--  <div class="container">-->
<!--    <div class="row">-->
<!--      <div class="col-md-8 col-sm-12 col-xs-12 text-center center-col">-->
<!--        <span class="alt-font text-small text-uppercase">Está em busca de recolocação profissional</span>-->
<!--        <h2 class="alt-font font-weight-700 letter-spacing-minus-1 text-extra-dark-gray">Trabalhe conosco</h2>-->
<!--        <p class="width-75 center-col xs-width-100">Para enviar o seu currículo para Doripel clique no botão a seguir.</p>-->
<!--        <a href="#start-your-project" class="btn btn-large btn-transparent-dark-gray margin-10px-top inner-link">Envie seu currículo</a>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</section>-->
<!-- end help section -->
<!-- start contact section -->
<section class="no-padding">
	<div class="container-fluid">
		<div class="row equalize sm-equalize-auto contact">
			<div class="col-md-6 col-sm-12 no-padding cover-background sm-height-450px xs-height-350px wow fadeInLeft"
			     style="background: url(<?= INCLUDE_PATH; ?>/images/sac-doripel.jpg)"></div>
			<div class="col-md-6 col-sm-12 no-padding col-2-nth wow fadeInRight">
				<!-- start contact item -->
				<div class="col-md-6 col-sm-6 col-xs-12 display-table bg-pink height-350px last-paragraph-no-margin">
					<div class="display-table-cell vertical-align-middle text-center">
						<i class="icon-map text-deep-pink icon-medium margin-25px-bottom"></i>
						<div class=text-white text-uppercase alt-font font-weight-600 margin-5px-bottom
						">Endereço
					</div>
					<p class="text-white width-60 md-width-80 center-col text-medium">Av. Julio Vanzin, 1600 - Área
						Industrial
						Lagoa Vermelha - RS - Brasil</p>
				</div>
			</div>
			<!-- end contact item -->
			<!-- start contact item -->
			<div class="col-md-6 col-sm-6 col-xs-12 display-table bg-black height-350px last-paragraph-no-margin">
				<div class="display-table-cell vertical-align-middle text-center">
					<i class="icon-chat text-pink icon-medium margin-25px-bottom"></i>
					<div class="text-white text-uppercase alt-font font-weight-600 margin-5px-bottom">Faça uma
						chamada
					</div>
					<p class="center-col text-medium no-margin text-extra-light-gray">Telefone</p>
					<p class="center-col text-medium no-margin text-extra-large text-white font-weight-700">
						<a class="contact-link" href="tel:555433586500" title="Clique para LIGAR">(54) 3358-6500</a>
					</p>
				</div>
			</div>
			<!-- end contact item -->
			<!-- start contact item -->
			<div class="col-md-6 col-sm-6 col-xs-12 display-table bg-black height-350px last-paragraph-no-margin">
				<div class="display-table-cell vertical-align-middle text-center">
					<i class="icon-envelope text-pink icon-medium margin-25px-bottom"></i>
					<div class="text-white text-uppercase alt-font font-weight-600 margin-5px-bottom">Nosso E-mail
					</div>
					<p class="center-col text-medium no-margin text-extra-light-gray">
						<a class="contact-link" href="mailto:comercial@doripel.com.br">comercial@doripel.com.br</a>
					</p>
					<p class="center-col text-medium text-extra-light-gray">
						<a class="contact-link"
						   href="mailto:assistencia@doripel.com.br">assistencia@doripel.com.br</a>
					</p>
				</div>
			</div>
			<!-- end contact item -->
			<!-- start contact item -->
			<div class="col-md-6 col-sm-6 col-xs-12 display-table bg-pink height-350px last-paragraph-no-margin">
				<div class="display-table-cell vertical-align-middle text-center text-white">
					<i class="icon-clock text-deep-pink icon-medium margin-25px-bottom"></i>
					<div class=" text-uppercase alt-font font-weight-600 margin-5px-bottom">Horário de
						Trabalho
					</div>
					<p class="center-col text-medium no-margin">Segunda à Sexta - 7:15 às 11:35 | Manhã</p>
					<p class="center-col text-medium no-margin">Segunda à Sexta - 13:15 às 17:40 | Tarde</p>

				</div>
			</div>
			<!-- end contact item -->
		</div>
	</div>
	</div>
</section>
<!-- end contact section -->
<!-- start form section -->
<section class="wow fadeIn" id="start-your-project">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 center-col margin-eight-bottom sm-margin-40px-bottom xs-margin-30px-bottom text-center last-paragraph-no-margin">
				<h5 class="alt-font font-weight-700 text-extra-dark-gray text-uppercase">Fale com a Doripel</h5>
				<p id="formulario">Entre em contato para enviar sugestões ou esclarecer dúvidas.<br>
					Estamos a disposição para ouvir você.
				</p>
			</div>
		</div>
        <?php

            $Contato = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];

            if ($Contato && isset($Contato['action']) && $Contato['action'] == 'contact') {
                unset($Contato['action']);

                if (in_array('', $Contato)) {
                    echo Check::erro("Para enviar seu contato, favor preencha todos os campos!", E_USER_WARNING);
                } elseif (!Check::email($Contato['email']) || !filter_var($Contato['email'], FILTER_VALIDATE_EMAIL)) {
                    echo Check::erro(
                        "Desculpe, mas o e-mail que você informou não tem um formato válido!",
                        E_USER_WARNING
                    );
                } else {
                    $Contato = array_map('strip_tags', $Contato);

                    $MailContent = '
                        <table width="550" style="font-family: Tahoma, sans-serif">
                         <tr><td>
                           <p>Novo contato de <b> ' . $Contato['nome'] . '</b> gerado pelo formulário do Site</p>
                           <p>Esse e-mail deve ser encaminhado para: ' . $Contato['setor'] . ' </p>
                           <p>Assunto: ' . $Contato['assunto'] . ' </p>
                           <p>Nome do Remetente: ' . $Contato['nome'] . ' </p>
                           <p>E-mail para resposta: ' . $Contato['email'] . ' </p>
                           <p><b>MENSAGEM:</b><br>' . $Contato['mensagem'] . ' </p>

                          <p style="font-size: 1em">
                          <img src="' . BASE . '/themes/doripel/images/logo.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" /><br><br>' . SITE_ADDR_NAME . '<br>Telefone:<b> ' . SITE_ADDR_PHONE_A . '</b><br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br> Visite nosso site: <a title=" ' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a><br>
                          </p>
                          </td></tr>
                        </table>
                        <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';
                    $Hora = date('H');
                    $Saudacao = (($Hora > 0) && ($Hora <= 12)) ? 'Bom dia!' : ((($Hora > 12) && ($Hora <= 18)) ? 'Boa tarde! ' : 'Boa noite!');

                    $Agradecimento = '
                        <table width="550" style="font-family: "Tahoma, sans-serif;">
                         <tr><td>
                            <p>' . $Saudacao . ' <b>' . $Contato['nome'] . '</b></p>
                           <p><br>Em breve estaremos respondendo seu e-mail. <br> Obrigado! </p><br><br>
                          
                          <p style="font-size: 1em;">
                          <img src="' . BASE . '/themes/doripel/images/logo.png" alt="Atenciosamente ' . SITE_NAME . '" title="Atenciosamente ' . SITE_NAME . '" />
						<br><br>
                           ' . SITE_ADDR_NAME . '<br>Telefone: ' . SITE_ADDR_PHONE_A . '<br>E-mail: ' . SITE_ADDR_EMAIL . '<br><br>
                           Visite nosso site: <a title="' . SITE_NAME . '" href="' . BASE . '">' . SITE_ADDR_SITE . '</a>
                          </p>
                          </td></tr>
                        </table>
                        <style>body, img{max-width: 550px !important; height: auto !important;} p{margin-botton: 15px 0 !important;}</style>';

                    $Email = new Email;
                    $Email->EnviarMontando(
                        $Contato['assunto'],
                        $MailContent,
                        $Contato['nome'],
                        $Contato['email'],
                        SITE_ADDR_NAME,
                        SITE_ADDR_EMAIL
                    );

                    if (!$Email->getError()) {
                        $_SESSION['sucesso'] = "{$Saudacao} {$Contato['nome']}, sua mensagem foi recebida com sucesso. Obrigado!";
                        $Email->EnviarMontando(
                            'Confirmação de recebimento',
                            $Agradecimento,
                            'Doripel Móveis',
                            'doripel@doripel.com.br',
                            $Contato['nome'],
                            $Contato['email']
                        );
                        header('Location: ' . BASE . '/contato#formulario');
                    } else {
                        echo Check::erro(
                            "Desculpe, não foi possível enviar sua mensagem. Entre em contato via por E-mail: " . SITE_ADDR_EMAIL . ". Obrigado!",
                            E_USER_WARNING
                        );
                    }
                }
            }

            if (!empty($_SESSION['sucesso']) && empty($Contato)) {
                echo Check::erro($_SESSION['sucesso']);
                unset($_SESSION['sucesso']);
            }
        ?>
		<form action="" class="animated fadeIn" method="post" enctype="multipart/form-data" novalidate="true">
			<input type="hidden" name="action" value="contact"/>
			<div class="row">
				<div class="col-md-12">
					<div id="success-project-contact-form" class="no-margin-lr"></div>
				</div>
				<div class="col-md-6">
					<input type="text" name="nome" value="<?= isset($Contato['nome']) ? $Contato['nome'] : ''; ?>"
					       placeholder="Nome*" class="big-input" required>
				</div>
				<div class="col-md-6">
					<input type="text" name="assunto"
					       value="<?= isset($Contato['assunto']) ? $Contato['assunto'] : ''; ?>" placeholder="Assunto*"
					       class="big-input" required>
				</div>
				<div class="col-md-6">
					<input type="email" name="email" value="<?= isset($Contato['email']) ? $Contato['email'] : ''; ?>"
					       placeholder="E-mail*" class="big-input" required>
				</div>
				<div class="col-md-6">
					<div class="select-style big-select">
						<select name="setor" id="setor" class="bg-transparent no-margin-bottom">
							<option value="">Selecione o setor</option>
							<option value="assistencia@doripel.com.br">Assistência Técnica</option>
							<option value="cobranca@doripel.com.br">Cobrança</option>
							<option value="comercial@doripel.com.br">Comercial</option>
							<option value="compras@doripel.com.br">Compras</option>
							<option value="contabilidade@doripel.com.br">Contabilidade</option>
							<option value="dpto.qualidade@doripel.com.br">Depto. de Qualidade</option>
							<option value="faturamento@doripel.com.br">Faturamento</option>
							<option value="financeiro@doripel.com.br">Financeiro</option>
							<option value="pessoal@doripel.com.br">Depto. Pessoal</option>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<textarea name="mensagem" placeholder="Sua mensagem*" rows="6" class="big-textarea"
					          required><?= isset($Contato['mensagem']) ? $Contato['mensagem'] : ''; ?></textarea>
				</div>
				<div class="col-md-12 text-center">
					<button type="submit" class="btn btn-medium btn-deep-pink">
						<i class="fa fa-envelope-open"></i> Enviar Mensagem
						<img class="form_load none" style="margin-left: 10px; display: none;" alt="Enviando Requisição!"
						     title="Enviando Requisição!" src="<?= INCLUDE_PATH; ?>/images/icons/load.gif"
						     data-no-retina="">
					</button>
				</div>
			</div>
	</div>
	</form>
	</div>
</section>
<!-- end form section -->
<?php
    require REQUIRE_PATH . '/inc/google-map.php'; ?>
<section class="wow fadeIn bg-light-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center social-style-4 border round">
				<span class="text-medium font-weight-600 text-uppercase display-block alt-font text-extra-dark-gray margin-30px-bottom">Siga a Doripel nas redes sociais</span>
				<div class="social-icon-style-4">
					<ul class="margin-30px-top large-icon">
                        <?= (!empty(SITE_SOCIAL_FB_PAGE) ? "<li><a class='facebook' href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "' target='_blank'><i class='fa fa-facebook' aria-hidden='true'></i></a></li>" : null); ?>
                        <?= (!empty(SITE_SOCIAL_INSTAGRAM) ? " <li><a class='instagram' href='https://instagram.com/" . SITE_SOCIAL_INSTAGRAM . "' target='_blank'><i class='fa fa-instagram no-margin-right' aria-hidden='true'></i></a>" : null); ?>
                        <?= (!empty(SITE_SOCIAL_LINKEDIN) ? "<li><a class='linkedin' href='https://www.linkedin.com/in/" . SITE_SOCIAL_LINKEDIN . "' target='_blank'><i class='fa fa-linkedin'></i></a></li>" : null); ?>
                        <?= (!empty(SITE_SOCIAL_YOUTUBE) ? "<li><a class='youtube ' href='https://www.youtube.com/channel/" . SITE_SOCIAL_YOUTUBE . "' target='_blank'><i class='fa fa-youtube'></i></a></li>" : null); ?>

					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- end contact form section -->
