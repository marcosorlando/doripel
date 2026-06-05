<?php

use App\Conn\Read;
?>
<!-- start slider section -->
<section class="no-padding main-slider height-100 mobile-height wow fadeIn bg_blue ">
    <div class="swiper-full-screen swiper-container height-100 width-100 black-move">
        <div class="swiper-wrapper">

            <?php

                if (empty($Read)) {
                    $Read = new Read;
                }
                $Read->exeRead(DB_SLIDES, "WHERE slide_status = 1 AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_date DESC");
                if (!$Read->getResult()) {

                    ?>
                    <!-- start slider static -->
                    <div class="cover-background aligncenter" >
                        <div class="slide-image position-absolute">
                            <img class="full" src="<?= BASE; ?>/uploads/slides/slide-default.jpg" alt="<?= SITE_ADDR_NAME ?>" title="<?= SITE_ADDR_NAME ?>"/>
                            <img class="mobile" src="<?= BASE; ?>/uploads/slides/slide-default-mobile.jpg" alt="<?= SITE_ADDR_NAME ?>" title="<?= SITE_ADDR_NAME ?>"/>
                        </div>
                        <div class="container position-relative full-screen">
                            <div class="col-md-12 slider-typography text-left xs-text-center">
                                <div class="slider-text-middle-main">
                                    <div class="slider-text-middle">
                                        <h2 class="alt-font font-weight-700 letter-spacing-minus-1  width-55 margin-35px-bottom md-width-60 sm-width-70 md-line-height-auto xs-width-100 xs-margin-15px-bottom">
                                            De Lagoa Vermelha para o mundo</h2>
                                        <p class="text-white text-large margin-four-bottom width-40 md-width-50 sm-width-60 xs-width-100 xs-margin-15px-bottom">
                                            Móveis com a qualidade Doripel</p>
                                        <div class="btn-dual">
                                            <a href="#entrar" target="_blank" class="wc_goto btn btn-dark-gray btn-rounded btn-small no-margin-lr">ENTRAR</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end slider static -->
                <?php
                } else {
                    foreach ($Read->getResult() as $Slide) {
                        extract($Slide);
                        ?>

                        <!-- start slider item background-image:url('<= BASE; ?>/uploads/<= $slide_image; ?>');-->
                        <div class="opacity-extra-medium bg-black position-relative z-index-1"></div>

                        <div class="swiper-slide cover-background bg-black">

                            <div class="slide-image position-absolute opacity<?= $slide_opacity ?>">
                                <img class="full" src="<?= BASE; ?>/uploads/<?= $slide_image; ?>" alt="<?= $slide_desc; ?>" title="<?= $slide_desc; ?>"/>
                                <img class="mobile" src="<?= BASE; ?>/uploads/<?= $mobile_image; ?>" alt="<?= $slide_desc; ?>" title="<?= $slide_desc; ?>"/>
                            </div>

                            <div class="container position-relative full-screen" style="z-index: 9999">
                          <!--      <div class="col-md-12 slider-typography text-left xs-text-center">
                                    <div class="slider-text-middle-main">

                                        <div class="slider-text-middle">
                                            <p data-wow-delay=".2s" class="animated fadeInDown text-white text-large margin-four-bottom width-40 md-width-50 sm-width-60 xs-width-100 xs-margin-15px-bottom">
                                                <?/*= $slide_desc && $show_desc ? $slide_desc : null; */?></p>

                                            <h2 class="alt-font text-white font-weight-700 letter-spacing-minus-1 width-55 margin-35px-bottom md-width-60 sm-width-70 md-line-height-auto xs-width-100 xs-margin-15px-bottom"><?/*= $slide_title && $show_title ? $slide_title : null; */?></h2>

                                            <div class="btn-dual">
                                                <?/*= $slide_purchase == 1 ? "<a href='' target='_blank' title='Saiba onde Comprar' class='jwc_contact btn btn-deep-pink  btn-medium no-margin-lr'>Comprar Agora!</a>" : ''; */?>
                                                <?/*= $slide_information == 1 ? "<a href='{$slide_content}' title='Acessar página do produto' class='btn btn-transparent-white btn-medium margin-20px-lr xs-margin-5px-tb'>&nbsp;&nbsp;Mais informações&nbsp;&nbsp;</a>" : ''; */?>

                                            </div>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                        <!-- end slider item -->
                    <?php
                    }
                }
            ?>
        </div>
        <!-- start slider pagination -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next swiper-button-black-highlight display-none"></div>
        <div class="swiper-button-prev swiper-button-black-highlight display-none"></div>
        <!-- end slider pagination -->
    </div>
</section>
<!-- end slider section -->
