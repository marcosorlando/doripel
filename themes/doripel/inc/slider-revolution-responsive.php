<?php

use App\Conn\Read;
?>
<!-- start slider section -->
<section class="no-padding main-slider height-100 mobile-height wow fadeIn bg_blue">
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
                    <div class="swiper-slide cover-background" style="background-image:url('<?= BASE; ?>/uploads/slides/default.jpg');">
                        <div class="opacity-medium bg-black"></div>
                        <div class="container position-relative full-screen">

                            <div class="slider-typography text-center">
                                <div class="slider-text-middle-main">
                                    <div class="slider-text-middle">
                                        <h6 class="text-very-light-gray padding-ten-lr font-weight-300 margin-two-bottom md-margin-four-bottom sm-margin-15px-bottom">De Lagoa Vermelha para o mundo</h6>

                                        <h2 class="alt-font text-white font-weight-700 letter-spacing-minus-1 margin-35px-bottom md-width-60 sm-width-70 md-line-height-auto xs-width-100 xs-margin-15px-bottom">Móveis com a qualidade Doripel</h2>
                                        <div class="btn-dual">
                                            <a href="#entrar" class="wc_goto btn btn-dark-gray btn-rounded btn-small no-margin-lr">ENTRAR</a>
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
                        <!-- start slider item -->
                        <div class="swiper-slide cover-background" style="background:url('<?= BASE; ?>/uploads/<?= $slide_image; ?>')">
                            <div class="<?= $slide_opacity; ?> bg-black"></div>
                            <div class="container position-relative full-screen">

                                <div class="slider-typography text-center">
                                    <div class="slider-text-middle-main">
                                        <div class="slider-text-middle">
                                            <h6 class="text-very-light-gray padding-ten-lr font-weight-300 margin-two-bottom md-margin-four-bottom sm-margin-15px-bottom"><?= $slide_desc && $show_desc ? $slide_desc : null; ?></h6>

                                            <h2 class="alt-font text-white font-weight-700 letter-spacing-minus-1 margin-35px-bottom md-width-60 sm-width-70 md-line-height-auto xs-width-100 xs-margin-15px-bottom"><?= $slide_title && $show_title ? $slide_title : null; ?></h2>
                                            <!--                                            <h6 class="text-very-light-gray padding-ten-lr font-weight-300 margin-two-bottom md-margin-four-bottom sm-margin-15px-bottom">we combine design, thinking and technical craft</h6>-->
                                            <!--                                            <div class="alt-font text-white text-uppercase font-weight-600 letter-spacing-minus-3 title-extra-large margin-two-bottom width-60 mx-auto lg-width-80 md-margin-four-bottom sm-width-90 sm-margin-25px-bottom sm-letter-spacing-0">creative thinker</div>-->
                                            <div class="btn-dual">
                                                <?= $slide_purchase == 1 ? "<a href='' target='_blank' title='Saiba onde Comprar' class='jwc_contact btn btn-deep-pink  btn-medium no-margin-lr'>Comprar Agora!</a>" : ''; ?>
                                                <?= $slide_information == 1 ? "<a href='{$slide_content}' title='Acessar página do produto' class='btn btn-transparent-white btn-medium margin-20px-lr xs-margin-5px-tb'>&nbsp;&nbsp;Mais informações&nbsp;&nbsp;</a>" : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
