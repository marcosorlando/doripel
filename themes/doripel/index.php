<?php

use App\Helpers\Check;

    require REQUIRE_PATH . '/inc/swiper-custom.php';
    require 'assets/widgets/contact/contact.wc.php';
    require REQUIRE_PATH . "/inc/belt-cta.php";
?>

<!-- start services section -->
<section id="entrar" class="wow fadeIn animated bg-pink">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-8 center-col margin-eight-bottom text-center last-paragraph-no-margin">
				<h5 class="alt-font text-white font-weight-600">Móveis para sua casa</h5>
				<p class="width-80 center-col display-inline-block xs-width-100 text-white">A Doripel Móveis, em sua
					essência traz a
					história e a tradição de mais de <?= DATE('Y') - 1986; ?> anos produzindo móveis de alto padrão e
					qualidade, uma excelente relação custo benefício, além de ser uma empresa comprometida com o
					meio ambiente..</p>
			</div>
		</div>
		<div class="row equalize">
			<!-- start features box item -->
			<div class="col-md-3 col-sm-6 col-xs-12 sm-margin-30px-bottom wow fadeInUp last-paragraph-no-margin">
				<a href="<?= BASE; ?>/moveis/cabeceira" title="Móveis Doripel - Cabeceiras - Clique para ver mais!">
					<div class="bg-white border-color-extra-medium-gray border-solid border-width-1 text-center padding-eighteen-tb border-radius-4 position-relative inner-match-height">
						<div class="display-inline-block margin-20px-bottom">
							<i class="flaticon-bed text-white icon-round-small bg-deep-pink"></i>
						</div>
						<h2 class="alt-font text-extra-dark-gray font-weight-600 margin-10px-bottom text-medium">
							CABECEIRAS</h2>
						<p class="width-75 center-col">Perfeitas para seu lugar de descanso e para deixar seu quarto
							completo. Pensamos em cada detalhe para proporcionar mais felicidade a você.</p>
					</div>
				</a>
			</div>
			<!-- end feature box item -->
			<!-- start features box item -->
			<div class="col-md-3 col-sm-6 col-xs-12 sm-margin-30px-bottom wow fadeInUp last-paragraph-no-margin"
			     data-wow-delay="0.2s">
				<a href="<?= BASE; ?>/moveis/comoda" title="Móveis Doripel - Cômodas - Clique para ver mais!">
					<div class="bg-white border-color-extra-medium-gray border-solid border-width-1 text-center padding-eighteen-tb border-radius-4 position-relative inner-match-height">
						<div class="display-inline-block margin-20px-bottom">
							<i class="flaticon-cabinet-1 text-white icon-round-small bg-deep-pink"></i>
						</div>
						<h2 class="alt-font text-extra-dark-gray font-weight-600 margin-10px-bottom text-medium">
							CÔMODAS</h2>
						<p class="width-75 center-col">Excelentes opções para guardar roupas, calçados ou acessórios.
							Além de super práticas, ocupam pouco espaço.</p>
					</div>
				</a>
			</div>
			<!-- end feature box item -->
			<!-- start features box item -->
			<div class="col-md-3 col-sm-6 col-xs-12 xs-margin-30px-bottom wow fadeInUp last-paragraph-no-margin"
			     data-wow-delay="0.4s">
				<a href="<?= BASE; ?>/moveis/multiuso" title="Móveis Doripel - Multiuso - Clique para ver mais!">
					<div class="bg-white border-color-extra-medium-gray border-solid border-width-1 text-center padding-eighteen-tb border-radius-4 position-relative inner-match-height">
						<div class="display-inline-block margin-20px-bottom">
							<i class="flaticon-closet text-white icon-round-small bg-deep-pink"></i>
						</div>
						<h2 class="alt-font text-extra-dark-gray font-weight-600 margin-10px-bottom text-medium">
							MULTIUSOS</h2>
						<p class="width-75 center-col">A linha de Armários Multiuso foi pensada para você usar sua
							imaginação e definir a melhor forma de utilização, os Multiusos são muito versáteis.</p>
					</div>
				</a>
			</div>
			<!-- end feature box item -->
			<!-- start features box item -->
			<div class="col-md-3 col-sm-6 col-xs-12 wow fadeInUp last-paragraph-no-margin" data-wow-delay="0.6s">
				<a href="<?= BASE; ?>/moveis/roupeiro" title="Móveis Doripel - Roupeiros - Clique para ver mais!">
					<div class="bg-white border-color-extra-medium-gray border-solid border-width-1 text-center padding-eighteen-tb border-radius-4 position-relative inner-match-height">
						<div class="display-inline-block margin-20px-bottom">
							<i class="flaticon-closet-1 text-white icon-round-small bg-deep-pink"></i>
						</div>
						<h2 class="alt-font text-extra-dark-gray font-weight-600 margin-10px-bottom
                        text-medium">GUARDA ROUPAS</h2>
						<p class="width-75 center-col">A Linha <?= date('Y'); ?> traz requinte e sofisticação para seu
							cômodo. Os modelos tem variações com 2 a 6 portas, tendo assim a opção perfeita pra
							você.</p>
					</div>
				</a>
			</div>
			<!-- end feature box item -->
		</div>
	</div>
</section>
<!-- end services section -->


<section class="parallax cover-background" data-stellar-background-ratio="0"
         style="background-image: url('<?= INCLUDE_PATH; ?>/images/doripel-fabrica-aerea.jpg')">
	<div class="opacity-full bg-extra-dark-gray"></div>
	<div class="container position-relative">
		<div class="row">
			<div class="col-lg-9 col-md-10 col-sm-12 text-center center-col wow fadeIn last-paragraph-no-margin">
				<!--<a class="popup-youtube" href="#"><img src="<?
                    /*= INCLUDE_PATH; */ ?>/images/icon-play-white.png" class="width-10 xs-width-50px margin-30px-bottom" alt="Vídeo Institucional Móveis Doripel"/></a>-->
				<h4 class="alt-font text-white">A Doripel Móveis utiliza processos modernos de fabricação, que resultam
					em produtos com alto padrão de qualidade.</h4>
				<p class="width-75 margin-lr-auto text-black md-width-90 xs-width-100 xs-margin-30px-bottom">Hoje,
					Doripel Móveis, antes, Estofaria Doripel, que iniciou em 1986, com a união dos irmãos Dorival e
					Pedro em tocar uma simples estofaria, que inicialmente atuava em reformas de estofados. Tempos
					depois, visando o crescimento do negócio, os proprietários decidiram migrar para o ramo moveleiro,
					onde inicialmente se dedicaram na produção de racks, estantes e salas de jantar. O negócio deu tão
					certo que em 2002 os filhos do Sr. Pedro, Jacson e Clevinson, hoje diretores da Doripel Móveis,
					mudaram a linha de produção, focando em dormitórios de casal, solteiro e incluindo uma linha
					infantil. Venha conferir de perto como é possível unir o útil ao agradável através de uma ótima
					relação de custo benefício, preço acessível e design moderno em um único produto.</p>
				<a href="<?= BASE; ?>/sobre"
				   class="btn btn-white btn-small text-extra-small border-radius-4 margin-45px-top xs-no-margin-top"><i
							class="fa fa-eye icon-very-small margin-5px-right no-margin-left" aria-hidden="true"></i>
					Sobre a Doripel</a>
			</div>
		</div>
	</div>
</section>
<!-- end video section -->

<!-- start blog section -->
<section class="bg-light-gray wow fadeIn hover-option4 blog-post-style3">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 center-col margin-five-bottom sm-margin-40px-bottom xs-margin-30px-bottom text-center">
				<div class="alt-font text-medium-gray margin-5px-bottom text-uppercase text-small">ÚLTIMAS NOVIDADES
				</div>
				<h5 class="alt-font text-extra-dark-gray font-weight-600 width-65 margin-lr-auto md-width-80 xs-width-100">
					Visite nosso Blog regularmente e acompanhe os artigos</h5>
			</div>
		</div>
		<div class="row equalize xs-equalize-auto">
			<!-- start blog item -->
            <?php

                $Read->fullRead(
                    "SELECT p.post_title, p.post_subtitle, p.post_content, p.post_name, p.post_cover, p.post_date, p.post_author, u.user_name, u.user_lastname, u.user_genre FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE post_status = 1 AND post_date <= NOW() AND post_author = user_id ORDER BY post_date DESC LIMIT :limit",
                    "limit=3"
                );

                if (!$Read->getResult()) {
                    echo Check::erro(
                        "Ainda Não existe posts cadastrados nesta secão. Favor volte mais tarde :)",
                        E_USER_NOTICE
                    );
                } else {
                    foreach ($Read->getResult() as $Post) {
                        extract($Post);
                        require REQUIRE_PATH . '/inc/post-index.php';
                    }
                }
            ?>
			<!-- end blog item -->
		</div>
	</div>
</section>
<!-- end blog section -->
