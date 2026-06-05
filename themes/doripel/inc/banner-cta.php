<?php

    $Read->ExeRead(DB_CTAS, "WHERE cta_status = 1 AND cta_start <= NOW() AND (cta_end >= NOW() OR cta_end IS NULL) ORDER BY cta_date DESC LIMIT 1");

    if ($Read->getResult()):
        extract($Read->getResult()['0']);

        $cta_btn_rounded = (!empty($cta_btn_rounded) ? 'btn-rounded' : null);
        ?>

        <!--CTA BANNER-->
        <div class="padding-30px-all text-white text-center margin-45px-bottom xs-margin-25px-bottom" style="background: <?= $cta_bg_color ?>">
            <i class="fa fa-quote-left icon-small margin-15px-bottom display-block"></i>
            <span class="text-extra-large font-weight-300 margin-20px-bottom display-block"><?= $cta_title; ?></span>

            <div class="display-table-cell vertical-align-middle padding-20px-bottom">
                <a class="post_list_thumb" target="_blank" href="<?= $cta_url; ?>" title="<?= $cta_title; ?> - Download">
                    <img src="<?= BASE; ?>/tim.php?src=uploads/<?= $cta_image ?>&w=500&h=333" alt="<?= $cta_title; ?>" title="<?= $cta_title; ?> - Clique para fazer seu Download"/>
                </a>
            </div>

            <a class="btn <?= $cta_btn_color ?> <?= $cta_btn_rounded ?> btn-medium" href="<?= $cta_url; ?>" target="_blank" title="<?= $cta_title; ?> - Download"><i class="fa fa-download fa-2x animated pulse infinite"></i> Baixar Agora</a>
        </div>
        <!--CTA BANNER-->


    <?php
    endif;
?>

