<?php

use App\Helpers\Check;

use App\Models\Pager;
use App\Conn\Read;
if (!$Read){
    $Read = new Read;
}

if (empty($URL[1])){
    require REQUIRE_PATH . '/404.php';
    return;
}

$Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_name = :nm", "nm={$URL[1]}");
if (!$Read->getResult()){
    require REQUIRE_PATH . '/404.php';
    return;
} else {
    extract($Read->getResult()[0]);
}
?>
<div class="wow fadeIn bg-light-gray padding-35px-tb page-title-small top-space margin-25px-bottom">
  <div class="container">
    <div class="row equalize xs-equalize-auto">
      <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 display-table">
        <div class="display-table-cell vertical-align-middle text-left xs-text-center">
          <!-- start page title -->
          <h1 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom text-uppercase">
            <a class="text-dark-gray" href="<?= BASE; ?>" title="<?= SITE_NAME .' '. SITE_SUBNAME ?>"><?= SITE_NAME .' '. SITE_SUBNAME ?> </a>
            <i class="fa fa-book text-deep-pink"></i>
            <a class="text-dark-gray" href="<?= BASE; ?>/moveis/<?= $cat_name; ?>" title="Ver mais: <?= $cat_title; ?> em <?= SITE_NAME; ?>!"><?= $cat_title; ?></a>
          </h1>
          <!-- end page title -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- start post content section -->
<section class="no-padding-top">
  <div class="container">
    <div class="row">
      <main class="col-md-9 col-sm-12 col-xs-12 right-sidebar sm-margin-60px-bottom xs-margin-40px-bottom no-padding-left sm-no-padding-right">

          <?php
          $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
          $Page = ($Page > 0 ? $Page : 1);
          $Pager = new Pager(BASE . "/moveis/{$cat_name}/", "<<", ">>", 5);
          $Pager->exePager($Page, 10);

          $Read->fullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_cover, pdt_created, pdt_ref, pdt_color FROM " . DB_PDT_DORIPEL . "   WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_category = :ct OR FIND_IN_SET(:ct, pdt_subcategory)) ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$cat_id}");

          if (!$Read->getResult()){
              $Pager->returnPage();
              echo Check::erro("Ainda Não existe produtos cadastrados nesta seção. Por favor volte mais tarde :)",
                E_USER_NOTICE);
          } else {
              foreach ($Read->getResult() as $Post){
                  extract($Post);
                  $BOX = 1;
                  require REQUIRE_PATH . '/inc/produto.php';
              }
          }

          ?>
        <!-- start pagination -->
        <div class="col-md-12 col-sm-12 col-xs-12 text-center margin-100px-top sm-margin-50px-top wow fadeInUp">
          <div class="pagination text-small text-uppercase text-extra-dark-gray">
              <?php
              $Pager->exePaginator(DB_PDT_DORIPEL,
                "WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_category = :ct OR FIND_IN_SET(:ct, pdt_subcategory))",
                "ct={$cat_id}");

              echo $Pager->getPaginator();
              ?>
          </div>
        </div>
        <!-- end pagination -->
      </main>
        <?php require REQUIRE_PATH . '/inc/sidebar-products.php'; ?>
      </div>
  </div>
</section>
<!-- end blog content section -->

<!--<section class="container blog-box-content" style="background: #00bf8f">-->
<!---->
<!--  <div class="main_blog" style="background: #9c9c9c; border: 1px solid red; height: 100px; width: 70%; ">-->
<!---->
<!--      --><?php
//      $Page = (!empty($URL[2]) ? $URL[2] : 1);
//      $Pager = new Pager(BASE . "/moveis/{$cat_name}/", "<", ">", 5);
//      $Pager->exePager($Page, 10);
//
//      $Read->fullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_cover, pdt_created, pdt_ref, pdt_color FROM " . DB_PDT_DORIPEL . "   WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_category = :ct OR FIND_IN_SET(:ct, pdt_subcategory)) ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
//        "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$cat_id}");
//
//
//      $Pager->exePaginator(DB_PDT_DORIPEL,
//        "WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_category = :ct OR FIND_IN_SET(:ct, pdt_subcategory))",
//        "ct={$cat_id}");
//
//      echo $Pager->getPaginator();
//      ?>
<!--  </div>-->
<!---->
<!--    --><?php //require REQUIRE_PATH . '/inc/sidebar-products.php'; ?>
<!--  <div class="clear"></div>-->
<!---->
<!--</section>-->
