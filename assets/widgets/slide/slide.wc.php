<link rel="stylesheet" href="<?php
echo BASE; ?>/assets/widgets/slide/slide.wc.css">
<?php

use App\Conn\Read;

$Read ??= new Read();

$Read->fullRead(
    'SELECT slide_image_mobile, slide_image_tablet, slide_image_desktop, slide_title, slide_headline, slide_desc, slide_category, slide_product, slide_link, slide_link_pdt, slide_link_pdt_btn, slide_link_cat, slide_link_cat_btn, show_headline, show_desc FROM ' . DB_SLIDES . ' WHERE slide_status = :status AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_date DESC',
    'status=1'
);

if ($Read->getResult()) {
    ?>
    <section class="carousel">
        <div class="content">
            <div class="owl-carousel owl-theme">
                <?php
                foreach ($Read->getResult() as $SLIDE) {
                    $image_desktop = $SLIDE['slide_image_desktop'];
                    $image_mobile = (empty($SLIDE['slide_image_mobile']) ? $image_desktop : $SLIDE['slide_image_mobile']);
                    $image_tablet = (empty($SLIDE['slide_image_tablet']) ? $image_desktop : $SLIDE['slide_image_tablet']);
                    ?>
                    <div>
                        <a href="<?php
                        echo $SLIDE['slide_link']; ?>" class="wc_goto" title="<?php
                        echo $SLIDE['slide_title']; ?>">
                            <picture alt="<?php
                            echo $SLIDE['slide_title']; ?>">
                                <source media="(min-width: 992px)"
                                        srcset="<?php
                                        echo BASE; ?>/uploads/<?php
                                        echo $image_desktop; ?>"/>
                                <source media="(min-width: 544px)"
                                        srcset="<?php
                                        echo BASE; ?>/uploads/<?php
                                        echo $image_tablet; ?>"/>
                                <source media="(min-width: 1px)"
                                        srcset="<?php
                                        echo BASE; ?>/uploads/<?php
                                        echo $image_mobile; ?>"/>
                                <img src="<?php
                                echo BASE; ?>/uploads/<?php
                                echo $image_desktop; ?>"
                                     alt="<?php
                                     echo $SLIDE['slide_title']; ?>" title="<?php
                                echo $SLIDE['slide_title']; ?>"/>
                            </picture>
                        </a>

                        <div class="slide-text">
                            <?php
                            if ($SLIDE['show_headline']) {
                                echo \sprintf('<h1>%s</h1>', $SLIDE['slide_headline']);
                            }
                            if ($SLIDE['show_desc']) {
                                echo \sprintf('<p>%s</p>', $SLIDE['slide_desc']);
                            }
                            ?>

                            <div class="slide-buttons">
                                <?php
                                if ($SLIDE['slide_product']) {
                                    echo \sprintf(
                                        "<a class='ind_btn' href='%s' title='Acessar página do produto'><span class='fa fa-arrow-alt-right'> &nbsp;&nbsp;%s&nbsp;&nbsp;</span></a>",
                                        $SLIDE['slide_link_pdt'],
                                        $SLIDE['slide_link_pdt_btn']
                                    );
                                }

                                if ($SLIDE['slide_category']) {
                                    echo \sprintf(
                                        "<a class='ind_btn' href='%s' title='Acessar página de produtos'><span class='fa fa-arrow-alt-right'>&nbsp;&nbsp;%s&nbsp;&nbsp;</span></a>",
                                        $SLIDE['slide_link_cat'],
                                        $SLIDE['slide_link_cat_btn']
                                    );
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="clear"></div>
        </div>
    </section>
    <?php
}
?>
