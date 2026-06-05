<?php

use App\Conn\Read;


    if (empty($Read)) {
        $Read = new Read;
    }

    $Read->exeRead(DB_CTAS, "WHERE cta_status = 1 AND cta_start <= NOW() AND (cta_end >= NOW() OR cta_end IS NULL) ORDER BY cta_date DESC LIMIT 1");
    $CtaResult = $Read->getResult();

    if ($CtaResult) {
        extract($CtaResult[0]);

    $cta_btn_rounded = (!empty($cta_btn_rounded) ? 'btn-rounded' : null);
        ?>

        <div class="padding-50px-tb xs-padding-30px-tb text-white" style="background: <?= $cta_bg_color ?>;">
            <div class="container">
                <div class="row equalize xs-equalize-auto">
                    <!-- start slogan -->
                    <div class="col-md-4 col-sm-12 col-xs-12 text-center alt-font display-table xs-text-center xs-margin-15px-bottom">
                        <div class="cta-text display-table-cell vertical-align-middle">
                            <?= $cta_text ?>
                        </div>
                    </div>
                    <!-- end slogan -->

                    <!-- start image -->
                    <div class="col-md-4 col-sm-6 col-xs-12 text-center display-table xs-margin-10px-bottom">
                        <div class="display-table-cell vertical-align-middle">
                            <a class="post_list_thumb" target="_blank" href="<?= $cta_url; ?>" title="<?= $cta_title; ?> - Download">
                                <img src="<?= BASE; ?>/tim.php?src=uploads/<?= $cta_image ?>&w=500&h=333" alt="<?= $cta_title; ?>" title="<?= $cta_title; ?> - Clique para fazer seu Download"/>
                            </a>
                        </div>
                    </div>
                    <!-- end image -->
                    <!-- start btn -->
                    <div class="col-md-4 col-sm-6 col-xs-12 col-xs-12 text-center display-table xs-text-center">
                        <div class="display-table-cell vertical-align-middle">
                            <h5 class="margin-20px-right"><?= $cta_title ?></h5>
                            <div class="social-icon-style-8 display-inline-block vertical-align-middle text-white">
                                <a class="btn <?= $cta_btn_color ?> <?= $cta_btn_rounded ?> btn-extra-large" target="_blank" href="<?= $cta_url; ?>" title="<?= $cta_title; ?> - Download"><i class="fa fa-download fa-2x animated pulse infinite"></i> Fazer Download</a>
                            </div>
                        </div>
                    </div>
                    <!-- end btn -->
                </div>
            </div>
        </div>
        </div>

    <?php
    }

?>
