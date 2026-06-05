<?php

use App\Models\Pager;
use App\Conn\Read;
if (!$Read){
  $Read = new Read;
}
$Read->exeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
if (!$Read->getResult()){
  require REQUIRE_PATH . '/404.php';
  return;
} else {
  extract($Read->getResult()[0]);
}
?>
<!-- start page title section -->
<section class="wow fadeIn parallax" data-stellar-background-ratio="0.5" style="background-image:url('<?= INCLUDE_PATH; ?>/images/keuren-canedo-munique-2.jpg');">
  <div class="opacity-medium bg-extra-dark-gray"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 extra-small-screen display-table page-title-large">
        <div class="display-table-cell vertical-align-middle text-center">
          <!-- start page title -->
          <h1 class="text-white alt-font font-weight-600 letter-spacing-minus-1 margin-10px-bottom">Biblioteca de Conteúdos Ricos</h1>
          <!-- end page title -->
          <!-- start sub title -->
          <span class="text-white opacity6 alt-font">Aproveite e baixe a vontade, foram feitos com carinho para você!</span>
          <!-- end sub title -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end page title section -->
<!-- start portfolio section -->
<section class="wow fadeIn padding-90px-top bg-light-gray sm-padding-50px-top xs-padding-30px-top">

  <?php
  $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
  $Page = ($Page > 0 ? $Page : 1);
  $Pager = new Pager(BASE . "/materiais/{$page_name}/", "<", ">", 5);
  $Pager->exePager($Page, 10);

  $Read->fullRead("SELECT c.category_id, c.category_name, c.category_title, m.mat_title, m.mat_subtitle, m.mat_cover, m.mat_category, m.mat_category_parent, m.mat_link FROM " . DB_MATCATEGORIES . " c, " . DB_MATERIAIS . " m WHERE mat_status = 1 AND mat_date <= NOW() AND mat_category = category_id ORDER BY mat_date DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");

  if (!$Read->getResult()){
    $Pager->returnPage();
    echo '<div class="container"><div class="row"><div class="col-md-12 trigger trigger_info"><p>Ainda Não existe materiais cadastrados na biblioteca. Favor volte mais tarde <i class="icon-hourglass"></i></p></div></div></div>';

  } else {
    ?>        

    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <!-- start filter navigation -->
          <ul class="portfolio-filter nav nav-tabs border-none portfolio-filter-tab-1 font-weight-600 alt-font text-uppercase text-center margin-80px-bottom text-small sm-margin-40px-bottom xs-margin-20px-bottom">

            <?php
            echo '<li class="nav active"><a href="javascript:void(0);" data-filter="*" class="xs-display-inline light-gray-text-link text-very-small">Todos</a></li>';

            foreach ($Read->getResult() as $MatCategory){
              extract($MatCategory);
              ?>

            <li class="nav"><a href="javascript:void(0);" title="Filtrar: <?= $category_title; ?>" data-filter=".<?= $category_name; ?>" class="xs-display-inline light-gray-text-link text-very-small"><?= $category_title; ?></a></li>

              <?php
            }
            ?>

          </ul>                                                                           
          <!-- end filter navigation -->
        </div>
      </div>
    </div>
    <!-- start filter content -->
    <div class="container-fluid container">
      <div class="row">
        <div class="col-md-12 no-padding xs-padding-15px-lr">
          <div class="filter-content overflow-hidden">
            <ul class="portfolio-grid work-4col gutter-medium hover-option6 lightbox-portfolio">
              <?php
              foreach ($Read->getResult() as $Mat){
                extract($Mat);
                ?>
                <li class="grid-sizer"></li>
                <!-- start portfolio-item item -->
                <li class="<?= $category_name; ?> grid-item wow fadeInUp last-paragraph-no-margin">
                  <figure>
                    <div class="portfolio-img bg-deep-pink position-relative text-center overflow-hidden">
                      <img src="<?= BASE; ?>/tim.php?src=uploads/<?= $mat_cover; ?>&w=800&h=650" alt="<?= $mat_title ?>" title="<?= $mat_title ?>" />
                      <div class="portfolio-icon">
                        <a href="<?= $mat_link; ?>" title="Baixar <?= $category_title;?> - <?= $mat_title;?>"><i class="fa fa-download text-extra-dark-gray animated pulse" aria-hidden="true"></i></a>
                      </div>
                    </div>
                    <figcaption class="bg-white">
                      <div class="portfolio-hover-main text-center">
                        <div class="portfolio-hover-box vertical-align-middle">
                          <div class="portfolio-hover-content position-relative">
                            <a href="<?= $mat_link; ?>" title="Baixar <?= $category_title;?> - <?= $mat_title;?>"><span class="line-height-normal font-weight-600 text-small alt-font margin-5px-bottom text-extra-dark-gray text-uppercase display-block"><?= $mat_title; ?></span></a>
                            <p class="text-medium-gray text-extra-small text-uppercase"><?= $category_title; ?></p>
                          </div>
                        </div>
                      </div>
                    </figcaption>
                  </figure>
                </li>
                <!-- end portfolio item -->


                <?php
              }

            }

            $Pager->exePaginator(DB_MATERIAIS, "WHERE mat_status = 1 AND mat_date <= NOW() AND (mat_category = :ct OR FIND_IN_SET(:ct, mat_category_parent))", "ct={$page_id}");
            echo $Pager->getPaginator();
            ?>
            <!-- end portfolio item -->
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!-- end filter content -->
</section>
