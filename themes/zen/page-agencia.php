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
<!-- ========================== -->
<!-- AGENCIA - HEADER -->
<!-- ========================== -->
<section class="top-header about-header with-bottom-effect transparent-effect dark">
	<div class="bottom-effect"></div>
	<div class="header-container">
		<div class="header-title wow fadeInUp">
			<div class="header-icon"><span class="icon icon-Planet"></span></div>
			<h1 class="title">Olá somos a ZEN AGÊNCIA WEB</h1>
			<em>Bem vindo(a) ao nosso MUNDO. Veja a seguir o que temos a oferecer</em>
		</div>
	</div><!--container-->
</section>

<!-- ========================== -->
<!-- AGENCIA - FEATURES -->
<!-- ========================== -->
<section class="features-section about-features-section">
	<h1 class="title-hidden">NOSSOS DIFERENCIAIS</h1>
	<div class="container">
		<div class="row wow fadeInUp">
			<div class="col-md-3 col-sm-6 wow fadeIn">
				<article>
					<div class="feature-item ">
						<div class="wrap-feature-icon">
							<div class="feature-icon">
								<span class="icon icon-Carioca"></span>
							</div>
						</div>
						<h2 class="title">conteúdo gerenciável</h2>
						<div class="text">
							O cliente fica com total controle sobre o conteúdo do seu site ou blog. Podendo avaliar o
							volume de acessos, quais páginas estão em destaque. Além de gerar todo o SEO e SEM de forma
							automática conforme novas páginas e publicações são criadas.
						</div>
					</div>
				</article>
			</div>
			<div class="col-md-3 col-sm-6 wow fadeIn">
				<article>
					<div class="feature-item active">
						<div class="wrap-feature-icon">
							<div class="feature-icon">
								<span class="icon icon-Heart"></span>
							</div>
						</div>
						<h2 class="title">design único</h2>
						<div class="text">
							Nós projetamos experiências que despertam a curiosidade, ambição, expressam e incentivam a
							lealdade. Contamos com princípios de design moderno para garantir os detalhes pensados
							cuidadosamente considerando a persuasão do projeto a ser executado.
						</div>
					</div>
				</article>
			</div>
			<div class="col-md-3 col-sm-6 wow fadeIn">
				<article>
					<div class="feature-item">
						<div class="wrap-feature-icon">
							<div class="feature-icon">
								<span class="icon  icon-Tools"></span>
							</div>
						</div>
						<h2 class="title">multiplataforma</h2>
						<div class="text">
							Nossas soluções são projetadas para oferecer a melhor experiência digital ao usuário,
							independente da plataforma em que o sistema seja acessado. Todos os detalhes são focados na
							UX (User Experience Design) onde os fluxos do sistema são pensados de forma holística.
						</div>
					</div>
				</article>
			</div>
			<div class="col-md-3 col-sm-6 wow fadeIn">
				<article>
					<div class="feature-item">
						<div class="wrap-feature-icon">
							<div class="feature-icon">
								<span class="icon icon-Blog"></span>
							</div>
						</div>
						<h2 class="title">100% responsivos</h2>
						<div class="text">
							Projetos que reconhecem e se adaptam ao tamanho da tela do dispositivo (celular, smartphone,
							smartTv, tablets, notebooks e desktops) em que são acessados. Oferecendo uma experiência de
							navegabilidade agradável e de fácil interação.
						</div>
					</div>
				</article>
			</div>
		</div>

	</div>
</section>
<!-- ========================== -->
<!-- AGENCIA - LAPTOPS -->
<!-- ========================== -->
<figure class="laptops-section">
	<div class="container">
		<div class="laptops text-center">
			<img src="<?= INCLUDE_PATH; ?>/assets/images/websites-gerenciaveis-cms.png"
			     title="Desenvolvimento de Websites gerenciáveis com design inovador, responsivo e multiplataformas"
			     alt="Desenvolvimento de sites responsivos e com gerenciador de conteúdo" class="img-responsive"/>
		</div>
	</div>
</figure>
<!-- ========================== -->
<!-- AGENCIA - STEPS  -->
<!-- ========================== -->
<section class="steps-section with-icon ">
	<div class="section-icon"><span class="icon icon-Medal"></span></div>
	<div class="container">
		<div class="section-heading">
			<div class="section-title">excelente planejamento . resultados rápidos</div>
			<div class="section-subtitle">Nosso foco está em entregar algo além das expectativas</div>
			<div class="design-arrow"></div>
		</div>
	</div>
	<div class="container">
		<div class="row steps-list">
			<h4 class="title-hidden">NOSSO TRABALHO</h4>
			<div class="col-md-4 col-sm-4 col-xs-4 wow fadeIn">
				<div class="step-item">
					<div class="item-icon" data-count="1">
						<span class="icon icon-Pencil"></span>
					</div>
					<div class="item-text">
						<h5>Planejamento &amp; <br/>
							Esboço .
						</h5>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-4 wow fadeIn" data-wow-delay="0.3s">
				<div class="step-item invert">
					<div class="item-icon" data-count="2">
						<span class="icon icon-Glasses"></span>
					</div>
					<div class="item-text">
						<h5>Design &amp; <br/>
							Desenvolvimento .
						</h5>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-4 wow fadeIn" data-wow-delay="0.6s">
				<div class="step-item">
					<div class="item-icon" data-count="3">
						<span class="icon icon-Plaine"></span>
					</div>
					<div class="item-text">
						<h5>Testes &amp; <br/>
							Entrega .
						</h5>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container results-container">
		<div class="row">
			<div class="col-md-6  col-sm-6">
				<ul class="skills">
					<li data-percent="85" class="skills-animated ">
						<span>web design</span>
						<div class="progress">
							<div class="progress-percent">
								<span class="progress-bar-tooltip">85</span>%
							</div>
						</div>
					</li>
					<li data-percent="90" class="skills-animated orange">
						<span>programação</span>
						<div class="progress">
							<div class="progress-percent">
								<span class="progress-bar-tooltip">90</span>%
							</div>
						</div>
					</li>
					<li data-percent="85" class="skills-animated">
						<span>marketing de conteúdo</span>
						<div class="progress">
							<div class="progress-percent">
								<span class="progress-bar-tooltip">85</span>%
							</div>
						</div>
					</li>
					<li data-percent="95" class="skills-animated orange">
						<span>inbound marketing</span>
						<div class="progress">
							<div class="progress-percent">
								<span class="progress-bar-tooltip">95</span>%
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="col-md-6 col-sm-6">
				<div class="results-description">
					<h5 class="italic-title">Cada cliente é muito importante para Nós!</h5>
					<h4>O time experiente da ZEN sempre dará o seu melhor para fazer os clientes felizes !!!</h4>

					<p>Nós aliamos visão criativa com dados para entregar experiências inovadoras aos usuários. Através
						de um processo de iteração e prototipagem, podemos projetar interfaces que trazem alegria para
						as pessoas, permitindo-lhes interagir..
						Medimos o sucesso e garantimos o alcance ideal para gerar integração entre monitoramento,
						análise, SEO, social e distribuição de conteúdo.
						Estamos constantemente explorando novas disciplinas e evoluindo os produtos e serviços
						existentes para melhor atender às necessidades dos nossos clientes. Manter várias áreas-chave de
						especialização ajuda a melhorar a nossa criatividade, estratégia e produção.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ========================== -->
<!-- AGENCIA - ACHIEVEMENTS -->
<!-- ========================== -->
<?php
    include_once 'inc/achievements.php'; ?>
<!-- ========================== -->
<!--- AGENCIA TEAM -->
<!-- ========================== -->
<!--<php include_once 'inc/time.php'; ?>-->
<!-- ========================== -->
<!-- AGENCIA - HISTORY -->
<!-- ========================== -->
<section class="history-section">
	<div class="container">
		<div class="section-heading">
			<h4 class="section-title">NOSSA HISTÓRIA</h4>
			<div class="section-subtitle">Conheça os principais marcos em nossa trajetória</div>
			<div class="design-arrow"></div>
		</div>
	</div>

	<div class="wrap-timeline">
		<div class="container">
			<div class="row top-row">
				<div class="col-md-12">
					<div class="time-title" id="timel"><br/>
						<div class="round-ico"><span class="icon icon-Flag"></span></div>
					</div>
				</div>
			</div>
			<article class="row left-row">
				<div class="round-ico little"></div>
				<div class="col-md-6 col-sm-6 time-item wow fadeInUp" data-wow-duration="2s">
					<div class="date">Janeiro 2017</div>
					<h5 class="title">Lançamento do Novo Website</h5>
					<p>A Zen Agência Web apresenta seu novo Website com design clean, 100% responsivo, totalmente
						reformulado com foco na experiência do usuário. O Site conta com um CMS (Content Manager
						System), onde todos os dados são gerenciados e atualizados, com várias metricas integradas, SEO
						e SEM dinâmicos. Tecnologia utilizada HTML5, CSS3, JS, PHP e MySQL.</p>
				</div>
			</article>

			<article class="row right-row">
				<div class="round-ico big"></div>
				<div class="col-md-6 col-sm-6"></div>
				<div class="col-md-6 col-sm-6 time-item wow fadeInUp" data-wow-duration="2s">
					<div class="date">Outubro 2016</div>
					<h5 class="title">Lançamento do Blog da Agência</h5>
					<div class="time-image">
						<img src="<?= INCLUDE_PATH; ?>/assets/images/zen-agencia-web-blog.jpg"
						     title="Zen Agência Web - Blog de Marketing Digital entrou no ar em Outubro de 2016"
						     alt="Zen Agência Web - Blog de Marketing Digital"/>
					</div>
					<p>Blog sobre Marketing Digital com objetivo de trazer para nossa audiencia, conteúdo gratuito e com
						alta qualidade.</p>
				</div>
			</article>

			<article class="row left-row">
				<div class="round-ico little"></div>
				<div class="col-md-6 col-sm-6 time-item wow fadeInUp" data-wow-duration="2s">
					<div class="date">Setembro 2016</div>
					<h5 class="title">Certificação em Inbound Marketing</h5>
					<p>Time de estrategistas de marketing da agência recebem 2 certificações em Inbound Marketing e RD
						Station pela Resultados Digitais empresa de Florianópolis referencia internacional no segmento
						de Marketing Digital. Consolidando a partir desta data um parceria de resultados e muito sucesso
						pela frente.</p>
				</div>
			</article>
			<div class="plus">
				<a href="#" class="plus-ico">+</a>
			</div>
		</div>
	</div>
</section>
<!-- ========================== -->
<!-- AGENCIA - VIDEO INSTITUCIONAL -->
<!-- ========================== -->
<?php
    include_once "inc/video.php" ?>
<!-- ========================== -->
<!-- AGENCIA - CULTURE -->
<!-- ========================== -->
<section class="latest-news-section clearfix">
	<div class="container">
		<div class="section-heading">
			<h3 class="section-title">CULTURA ZEN</h3>
			<p class="section-subtitle">estratégia, criatividade, design, tecnologia, conteúdo</p>
			<div class="design-arrow"></div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-sm-4 wow fadeInUp">
				<div class="culture-item">
					<figure class="image">
						<img src="<?= INCLUDE_PATH; ?>/assets/images/culture/solucoes-estrategicas-nosso-negocio-zen-agencia-web.jpg"
						     title="Soluções estratégicas para Internet esse é o Nosso Negócio"
						     alt="Soluções estratégicas para Internet esse é o Nosso Negócio"/>
					</figure>
					<div class="news-body">
						<h5>Nosso negócio</h5>
						<p>Soluções estratégicas para Internet.</p>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 wow fadeInUp">
				<div class="culture-item">
					<figure class="image">
						<img src="<?= INCLUDE_PATH; ?>/assets/images/culture/valores-e-principios-da-zen-agencia-web.jpg"
						     title="Valores e Princípios constroem relacionamentos de confiança"
						     alt="Valores e princípios da Zen Agência Web"/>
					</figure>
					<div class="news-body">
						<h5>Valores & Princípios</h5>
						<p>
							<b>Clientes:</b> construir relacionamentos de confiança<br>
							<b>Resultados:</b> desafio permanente<br>
							<b>Pessoas:</b> nosso diferencial<br>
							<b>Qualidade:</b> nosso compromisso<br>
							<b>Inovação:</b> busca constante
						</p>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 wow fadeInUp">
				<div class="culture-item">
					<figure class="image">
						<img src="<?= INCLUDE_PATH; ?>/assets/images/culture/missao-da-zen-agencia-web.jpg"
						     title="Criar soluções estratégicas aliando desenvolvimento de Sistemas para Internet e Marketing Digital"
						     alt="Missão da Zen Agência Web Marketing Digital e Desenvolvimento de Websites"/>
					</figure>
					<div class="news-body">
						<h5>Missão</h5>
						<p>Criar soluções estratégicas aliando desenvolvimento de Sistemas para Internet e Marketing
							Digital (<em> Inbound Marketing </em>), com foco na melhoria da experiencia do usuário e
							resultados para o cliente.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ========================== -->
<!-- AGENCIA - CREATE IDEAS -->
<!-- ========================== -->
<section class="create-ideas-section with-icon with-top-effect clearfix">
	<div class="section-icon"><span class="icon icon-ClipboardText"></span></div>
	<div class="container">
		<div class="section-heading">
			<div class="section-title">DIAGNÓSTICO DE INBOUND MARKETING</div>
			<div class="section-subtitle">Nossos especialistas em estratégias estão esperando seu contato</div>
			<div class="design-arrow"></div>
		</div>
	</div>
	<div class="container">
		<div class="idea-image">
			<a href="http://materiais.zen.ppg.br/marketing-digital-diagnostico-gratuito" target="_blank">
				<img src="<?= INCLUDE_PATH; ?>/assets/images/diagnostico-de-marketing-digital-gratuito.jpg"
				     title="Diagnóstico gratuito para sua empresa - Clique para REALIZAR AGORA!"
				     alt="Marketing Digital Diagnostico diginal de Inbound Marketing gratuito"/>
			</a>
		</div>
	</div>
</section>
<!-- ========================== -->
<?php
    if (APP_COMMENTS && COMMENT_ON_PAGES) { ?>
		<div class="container" style="background: #fff; padding: 20px 0;">
			<div class="content">
                <?php
                    $CommentKey = $page_id;
                    $CommentType = 'page';
                    require '_cdn/widgets/comments/comments.php';
                ?>
				<div class="clear"></div>
			</div>
		</div>
        <?php
    } ?>
