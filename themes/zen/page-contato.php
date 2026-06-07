<?php

    if (!$Read) {
        $Read = new Read;
    }

    $Read->exeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

<header class="top-header countact-us-header with-bottom-effect transparent-effect dark dark-strong">
	<div class="bottom-effect"></div>
	<div class="header-container">
		<div class="header-title">
			<div class="header-icon"><span class="icon icon-Mail"></span></div>
			<h2 class="title">Fale conosco</h2>
			<em style="color: #ff6600;">estamos esperando seu contato</em>
		</div>
	</div>
</header>

<section class="countact-us-section contact-us-reverse-section">
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-sm-6">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<div class="contact-block">

							<div class="contact-block-heading">
								<h3>MANTENHA CONTATO</h3>
								<em>Gostamos de receber mensagens</em>
								<p>Em até 12 horas você estará recebendo a resposta para sua mensagem enviada via
									formulário, exceto finais de semana e feriados que o tempo é de até 24 horas.
									Agradecemos pelo seu contato em caso de urgência ligue-nos ou contate via
									Whatsapp. Obrigado!</p>
							</div>
							<div class="row contacts-list">
								<div class="col-md-12 clearfix">
									<div class="type-info pull-left">
										<h6><i class="icon icon-House"></i>Endereço</h6>
									</div>
									<div class="info pull-right text-right">
										<p class="no-margin"><?= SITE_ADDR_ADDR ?></p>
										<p class="no-margin">Edifício Palermo</p>
										<p class="no-margin">Bairro Exposição</p>
										<p class="no-margin">Caxias do Sul/RS - Brasil</p>
									</div>
								</div>
								<div class="col-md-12 clearfix">
									<div class="type-info pull-left">
										<h6><i class="icon icon-Phone2"></i>Telefone</h6>
									</div>
									<div class="info pull-right text-right">
										<p class="no-margin">+55 54 3419 - 9425</p>
									</div>
								</div>
								<div class="col-md-12 clearfix">
									<div class="type-info pull-left">
										<h6><i class="icon icon-Mail"></i>E-mail</h6>
									</div>
									<div class="info pull-right text-right">
										<p class="no-margin"><a title="Envie-nos um e-mail"
										                        href="mailto:contato@zen.ppg.br">contato@zen.ppg.br</a>
										</p>
									</div>
								</div>
								<div class="col-md-12 clearfix">
									<div class="type-info pull-left">
										<h6><i class="icon icon-Webcam"></i>Whatsapp</h6>
									</div>
									<div class="info pull-right text-right">
										<p class="no-margin"><a title='Envie-nos um e-mail'
										                        href='<?= \App\Helpers\Check::whatsMessage(
                                                                    SITE_ADDR_WHATS,
                                                                    'Olá! Estou no site da Agência, quero conversar.'
                                                                ) ?>'>+55 54 3419 -
												9425</a></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="col-md-7 col-md-offset-1 col-sm-6">
				<div class="contact-form">
					<div class="form-heading">
						<h3>ENVIE SUA MENSAGEM</h3>
					</div>

					<form action="" class="form-contact" method="post" enctype="multipart/form-data">
						<input type="hidden" name="callback" value="Contact"/>
						<input type="hidden" name="callback_action" value="send"/>
						<div id="response"></div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<input type="text" name="nome" id="nome" placeholder="SEU NOME" class="form-control"
									       required/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<input type="text" name="sobrenome" id="sobrenome" placeholder="SOBRENOME"
									       class="form-control" required/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<input type="text" name="phone" id="phone" placeholder="TELEFONE"
									       class="form-control" onkeyup="mask_fone(this.id)" maxlength="15" required/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<input type="text" name="email" required id="email" placeholder="EMAIL"
									       class="form-control"/>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<input type="text" name="assunto" required id="assunto" placeholder="ASSUNTO"
									       class="form-control"/>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<textarea class="form-control" required name="mensagem" id="mensagem"
									          placeholder="MENSAGEM"></textarea>
								</div>
							</div>
							<div class="col-md-12">
								<div class="wc_contact_modal_button form-group">
									<button class="btn btn-primary">
										<img style="display: none;"
										     src="<?= BASE; ?>/assets/widgets/contact/images/load.gif"
										     alt="Aguarde, enviando contato!" title="Aguarde, enviando contato!"/>
										ENVIAR MENSAGEM
									</button>
								</div>
							</div>
						</div>
					</form>
					<div style="display: none; padding-top: 30px;" class="wc_contant_sended jwc_contant_sended">
						<p class="h2"><span>&#10003;</span><br>Mensagem enviada com sucesso!</p>
						<p><b>Prezado(a) <span class="jwc_contant_sended_name">NOME</span>. Obrigado por entrar em
								contato,</b></p>
						<p>Informamos que recebemos sua mensagem, e que vamos responder o mais breve possível.</p>
						<p><em>Atenciosamente equipe</em><br>
							<img src="<?= INCLUDE_PATH; ?>/assets/images/logo-dark.png" title="Obrigado!"/>
						</p>
						<button class="btn btn_default jwc_contact_close" style="margin-top: 20px;">FECHAR</button>
					</div>
				</div>
			</div>
		</div>

	</div>

</section>
<!-- ========================== -->
<!-- MAP -->
<!-- ========================== -->
<!--<section class="contact-map-section">
	<div class="map" id="bigMap"> MAPA</div>
</section>-->
