<?php

    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;
    use App\Models\Pager;

    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
    date_default_timezone_set('America/Sao_Paulo');
    require_once REQUIRE_PATH . '/inc/product-volumes.php';

    if (empty($URL[1])) {
        require REQUIRE_PATH . '/404.php';
        return;
    }

    if (empty($Read)) {
        $Read = new Read;
    }
    $Read->exeRead(DB_PDT_DORIPEL, "WHERE pdt_name = :nm", "nm={$URL[1]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        $Product = $Read->getResult()[0];
        extract($Product);
        $Update = new Update;
        $UpdateView = [
            'pdt_views' => (int)($pdt_views ?? 0) + 1,
            'pdt_lastview' => date('Y-m-d H:i:s')
        ];
        $Update->exeUpdate(DB_PDT_DORIPEL, $UpdateView, "WHERE pdt_id = :id", "id={$pdt_id}");
        $Read->fullRead(
            "SELECT cat_title, cat_name FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :id",
            "id={$pdt_category}"
        );
        $PostCategory = ($Read->getResult() ? $Read->getResult()[0] : []);
    }

    $ProductVolumes = doripelProductVolumes($Product, $Read);
    $ProductVolumeCount = count($ProductVolumes);
    $ProductCubage = doripelProductCubage($ProductVolumes);
?>
<section class="wow fadeIn bg-light-gray padding-15px-tb page-title-small top-space">
	<div class="container">
		<div class="row equalize xs-equalize-auto">
			<div class="col-lg-7 col-md-6 col-sm-6 col-xs-12 display-table">
				<div class="display-table-cell vertical-align-middle text-left xs-text-center">
					<!-- start page title -->
					<h1 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom text-uppercase"><?= $pdt_title; ?></h1>
					<!-- end page title -->
				</div>
			</div>
			<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 display-table text-right xs-text-left xs-margin-10px-top">
				<div class="display-table-cell vertical-align-middle breadcrumb text-small alt-font">
					<!-- breadcrumb -->
					<ul class="xs-text-center text-uppercase">
						<li class="text-dark-gray">
							<h2 class="text-medium"><?= $pdt_color; ?></h2>
						</li>
						<li>
							<h3 class="text-medium">REF.: <b><?= $pdt_ref ?></b></h3>
						</li>
					</ul>
					<!-- end breadcrumb -->
				</div>
			</div>
		</div>
	</div>
</section>
<!-- start call to action section -->
<!-- @SCENE OLD
<section class='parallax wow fadeIn' data-stellar-background-ratio='0.2'
         style="background-image:url('<= BASE; ?>/uploads/<= $pdt_scene; ?>');">

		<div class='container'>
		<div class='row'>
			<div class='col-lg-8 center-col display-table extra-small-screen text-center col-md-8 xs-padding-15px-lr'>
				<div class='display-table-cell vertical-align-middle'>
					<span class='text-pink margin-15px-bottom display-block alt-font text-uppercase
					xs-margin-5px-bottom'>REQUINTE E SOFISTICAÇÃO</span>
					<h3 class='alt-font text-extra-dark-gray font-weight-600'><= $pdt_title; ?>
						<br><= $pdt_color; ?>
					</h3>
					<a href=""
					   class="jwc_contact btn btn-transparent-deep-pink bg-white btn-large margin-20px-top xs-no-margin-top wow fadeInUp"><i
								class="fa fa-shopping-bag"></i> ONDE COMPRAR</a>
				</div>
			</div>
		</div>
	</div>
</section>
-->
<!-- start about product section -->
<section class="wow fadeIn no-padding bg-light-gray">
	<figure class="col-12 pdt_image-scene">
		<img src="<?= BASE . '/tim.php?src=uploads/' . $pdt_scene ?>&w=100%&h=auto"'
		alt="<?= "{$pdt_title} - foto ambientada" ?>">
	</figure>
	<div class="container-fluid">
		<div class="row equalize sm-equalize-auto">
			<!-- start post item -->
			<div class="col-md-8 col-sm-12 col-xs-12 blog-post-content  xs-text-center sm-height-300px xs-height-300px">
				<div class="swiper-full-screen swiper-container white-move">
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<img src="<?= BASE . '/tim.php?src=uploads/' . $pdt_cover; ?>&w=auto&h=auto"
							     title="<?= $pdt_title . ' - ' . $pdt_color . ' - ' . $pdt_ref; ?>"
							     alt="<?= $pdt_title . ' - ' . $pdt_color . ' - ' . $pdt_ref; ?>">
						</div>
                        <?php
                            $Read->fullRead(
                                " SELECT image FROM " . DB_PDT_GALLERY_DORIPEL . " WHERE product_id = :id ",
                                "id={$pdt_id}"
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Imagens) {
                                    extract($Imagens);
                                    ?>
									<div class="swiper-slide">
										<img src="<?= BASE . '/tim.php?src=uploads/' . $image ?>&w=auto&h=auto"
										     title="<?= $pdt_title . ' - ' . $pdt_color . ' - ' . $pdt_ref; ?>"
										     alt="<?= $pdt_title . ' - ' . $pdt_color . ' - REF.: ' . $pdt_ref; ?>">
									</div>
                                    <?php
                                }
                            }
                        ?>
					</div>
					<div class="swiper-pagination swiper-pagination-square swiper-pagination-white"></div>
					<div class="swiper-button-next swiper-button-black-highlight"></div>
					<div class="swiper-button-prev swiper-button-black-highlight"></div>
				</div>
			</div>
			<!-- end post item -->
			<div class="col-md-4 col-sm-12 col-xs-12 no-padding">
				<div class="padding-seventeen-lr padding-twenty-tb md-padding-40px-lr sm-padding-50px-tb xs-padding-30px-all">
					<img src="<?= INCLUDE_PATH ?>/images/apple-touch-icon-72x72.png" class="margin-30px-bottom" alt=""/>
					<h3 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom">REF.
						<span class="text-deep-pink"><?= $pdt_ref ?></span>
					</h3>
					<div class="bg-deep-pink separator-line-horrizontal-full display-inline-block margin-25px-tb"></div>
					<p class="width-90 width-100 margin-35px-bottom text-medium line-height-28"><?= $pdt_subtitle ?></p>
					<h6 class="text-deep-pink">Medidas Montado:</h6>
					<ul class="list-style-3 text-medium">
						<li>Altura:
							<b><?= doripelNormalizeDimension($pdt_dimension_heigth_mounted ?? 0) * 100; ?></b> cm
						</li>
						<li>Largura:
							<b><?= doripelNormalizeDimension($pdt_dimension_width_mounted ?? 0) * 100; ?></b> cm
						</li>
						<li>Profundidade:
							<b><?= doripelNormalizeDimension($pdt_dimension_depth_mounted ?? 0) * 100; ?></b> cm
						</li>
						<li>Peso:
							<b><?= doripelNormalizeDimension($pdt_dimension_weight_mounted ?? 0); ?></b> Kg
						</li>
					</ul>
					<a href="#relacionados" class="btn btn-deep-pink wc_goto"><i class="fa fa-"></i> Outras Opções de
						Cores</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- end about product section -->
<!-- start about product section -->
<section class="wow fadeIn">
	<div class="container">
		<div class="row">
			<div class="col-md-6 sm-margin-50px-bottom xs-margin-30px-bottom wow fadeIn">
				<h3 class="alt-font text-deep-pink font-weight-600"><?= $pdt_title; ?></h3>
				<div class="htmlchars"><?= $pdt_content; ?></div>
				<span class="jwc_contact btn btn-deep-pink btn-medium"><i class="fa fa-shopping-bag"></i> Onde
					Comprar?</span>
				<a href="<?= BASE; ?>/uploads/<?= $pdt_instrutions; ?>" target="_blank"
				   title="Clique para Baixar o Manual em PDF" class="btn btn-transparent-deep-pink btn-medium"><i
							class="fa fa-file-pdf-o"></i> Manual
					de Montagem</a>
			</div>
			<div class="col-md-4 col-md-offset-2 wow fadeIn">
				<ul class="list-style-8 margin-twelve-left">
					<li class="text-uppercase text-extra-dark-gray">
						<span class="display-block text-small text-deep-pink">Referência</span><?= $pdt_ref ?>
					</li>
					<li class="text-uppercase text-extra-dark-gray">
						<span class="display-block text-small text-deep-pink">Código de Barras (EAN)</span><?=
                            $pdt_code ?>
					</li>
					<li class="text-uppercase text-extra-dark-gray">
						<span class="display-block text-small text-deep-pink">Padrão de Cor</span><?= $pdt_color ?>
					</li>
					<li class="text-uppercase text-extra-dark-gray">
						<span class="display-block text-small text-deep-pink">Cubagem na caixa (<span><?=
                                    $ProductVolumeCount; ?></span> volumes)</span>
                        <?= number_format($ProductCubage, 4, ',', '.') ?> M&sup3;
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!-- end about product section -->


<?php
    $Read->exeRead(DB_PDT_OPTIONALS_DORIPEL, " WHERE pdt_id = :pdt_id", "pdt_id=$pdt_id");

    if ($Read->getResult()) {
        ?>

		<!-- start opctionals product section -->
		<section class='wow fadeIn bg-extra-light-gray optionals'>
			<div class='container'>
				<h3 class='alt-font text-deep-pink font-weight-600'><b
							class="text-pink">Opcionais:</b> <?= $pdt_title;
                    ?></h3>

				<div class='row'>
                    <?php
                        foreach ($Read->getResult() as $opt) {
                            echo "<div class='col-md-6 sm-margin-50px-bottom xs-margin-30px-bottom wow fadeIn'>
								<div class='row'>
									<div class='col-md-5'>
										<img src='" . BASE . "/tim.php?src=uploads/{$opt['pdt_optional_img']}&w=600&h=auto'
										alt='{$opt['pdt_optional_title']} - imagem demonstrativa'>
									</div>
									<div class='col-md-7'>
										<span class='ref'><img class='icon-opt' src='" . INCLUDE_PATH . "/images/apple-touch-icon-57x57.png'
										alt='Doripel ícone'> REF.: {$opt['pdt_optional_ref']}</span>
										
										<p class='title'>{$opt['pdt_optional_title']}</p>
										<div class='htmlchars'>{$opt['pdt_optional_desc']}</div>
									</div>
								</div>
							</div>";
                        }
                    ?>
				</div>
			</div>
		</section>
		<!-- end about product section -->

        <?php
    }
    if ($pdt_video) {
        require REQUIRE_PATH . "/inc/video_popup.php";
    }
?>
<section class='no-padding-top' id="relacionados">
	<div class='container'>
		<div class='row'>
			<div class='col-12 padding-40px-top sm-margin-60px-bottom xs-margin-40px-bottom'>

				<h6>Produtos Relacionados:</h6>

                <?php
                    $refParent = strstr((string)$pdt_ref, '-', true) ?: (string)$pdt_ref;
                    $Read->fullRead(
                        'SELECT pdt_title, pdt_name, pdt_cover, pdt_ref, pdt_created, pdt_color FROM ' .
                        DB_PDT_DORIPEL . ' WHERE pdt_status = 1 AND pdt_id != :id AND pdt_ref LIKE :ref ORDER BY pdt_created DESC',
                        "id={$pdt_id}&ref={$refParent}-%"
                    );

                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $Post) {
                            extract($Post);
                            $BOX = 1;
                            require REQUIRE_PATH . '/inc/produto.php';
                        }
                    }
                ?>
			</div>
		</div>
	</div>
</section>

<?php
    include_once "./assets/widgets/contact/contact.wc.php";
?>
<!-- end call to action section -->
