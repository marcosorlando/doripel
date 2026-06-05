<footer>
	<!--========================== -->
	<!--FOOTER - SOCIAL -->
	<!--========================== -->
	<section class="social-section dark dark-strong">
		<h5 class="title-hidden">Zen Agência Web acompanhe-nos nas mídias sociais</h5>
		<div class="container dark-content">
			<div class="tt">Nós estamos 24/7 nas redes sociais - Siga a <em class="text-orange">Zen Agência Web</em> no:
			</div>
			<ul class="list-socials">
				<li><a target="_blank" title="Zen Agência Web no Facebook"
				       href="https://www.facebook.com/ZenAgenciaWeb"><i class="fa fa-facebook"></i></a></li>
				<li><a target="_blank" title="Zen Agência Web no Instagram"
				       href="https://www.instagram.com/ZenAgenciaWeb"><i class="fa fa-instagram"></i></a></li>
				<li><a target="_blank" title="Zen Agência Web no Linkedin"
				       href="https://www.linkedin.com/company/zen-multimídia-agência-digital"><i
								class="fa fa-linkedin"></i></a></li>
				<li><a target="_blank" title="Zen Agência Web no Twitter" href="https://twitter.com/ZenAgenciaWeb"><i
								class="fa fa-twitter"></i></a></li>

				<li><a target="_blank" title="Zen Agência Web no Pinterest"
				       href="https://br.pinterest.com/zenagencia"><i class="fa fa-pinterest-p"></i></a></li>
			</ul>
		</div>
	</section>

	<!--========================== -->
	<!--FOOTER - FOOTER -->
	<!--========================== -->
	<section class="footer-section">
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-sm-3">

					<img class='footer-logo' src='<?= INCLUDE_PATH; ?>/assets/svg/zen-white.svg'
					     alt='Logotipo - Zen Agência Web'/>

					<p>A ZEN é uma agência especializada em prestar serviços na área do desenvolvimento técnico e
						criativo de produtos relacionados a Internet.</p>
				</div>
				<div class="col-md-3 col-sm-3">
					<h5>Sitemap</h5>
					<div class="row">
						<div class="col-md-6">
							<ul class="footer-nav">
								<li><a href="<?= BASE; ?>/agencia">Agência</a></li>
								<li><a href="<?= BASE; ?>/portfolio">Portfólio</a></li>
								<li><a href="<?= BASE; ?>/blog" title="Confira os últimos artigos publicados">Blog</a>
								</li>
								<li><a href="<?= BASE; ?>/contato">Fale conosco</a></li>
							</ul>
						</div>
						<div class="col-md-6">
							<ul class="footer-nav">
								<li><a href="<?= BASE; ?>/solucoes">Soluções</a></li>
								<li><a href="<?= BASE; ?>/contato">Orçamentos</a></li>
								<li><a href="<?= BASE; ?>/contato">Dúvidas</a></li>
								<li><a href="<?= BASE; ?>/materiais" title="Biblioteca do Marketing B2B">Materiais</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-3">
					<h5>Entre em contato</h5>
					<ul class="contacts-list">
						<li>
							<p><i class="icon icon-House"></i><?= SITE_ADDR_ADDR ?></p>
						</li>
						<li>
							<p><i class="icon icon-Pointer"></i>Caxias do Sul/RS - BRASIL</p>
						</li>
						<li>
							<p><i class="icon icon-Phone2"></i>+55 54 3419 - 9425</p>
						</li>
						<li>
							<p><i class="icon icon-Mail"></i><a href="mailto:agencia@zen.ppg.br">AGENCIA@ZEN.PPG.BR</a>
							</p>

						</li>
					</ul>
				</div>
				<div class="col-md-3 col-sm-3">
					<h5>Newsletter</h5>

					<form name="lead_capture" class="j_formsubmit" action="" method="post"
					      enctype="multipart/form-data" novalidate>
						<h6 class="text-white">Receba em seu E-mail novidades. Assine nossa Newsletter Mensal:</h6>
						<div class="callback_return trigger_ajax"></div>
						<input type="hidden" name="callback" value="Contact"/>
						<input type="hidden" name="callback_action" value="newsletter"/>

						<div class="form-group has-feedback">
							<input type="email" autocomplete="on" name="newsletter_email" class="form-control"
							       placeholder="Digite seu melhor e-mail" required/>

							<button name="public" type="submit"
							        class="btn btn_optin">
								<i class="icon icon-Mail form-control-feedback">
									<img class="form_load"
									     src="<?= BASE; ?>/assets/widgets/contact/images/load_w.gif"
									     alt="Registrando E-mail" title="Registrando E-mail" .../>
								</i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>

	<div class="copyright-section">
		<p>©<?= date('Y'); ?> <span>ZEN AGÊNCIA WEB </span>. Todos os direitos reservados.</p>
	</div>
</footer>
