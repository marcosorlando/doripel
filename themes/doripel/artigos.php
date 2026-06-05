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

$Read->exeRead(DB_CATEGORIES, "WHERE category_name = :nm", "nm={$URL[1]}");
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
            <a class="text-dark-gray" href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a>
            <i class="fa fa-bullhorn text-deep-pink"></i>
            <a class="text-dark-gray" href="<?= BASE; ?>/artigos/<?= $category_name; ?>" title="Ver mais: <?= $category_title; ?> em <?= SITE_NAME; ?>!"><?= $category_title; ?></a>
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
        <h1 class="title-hidden"><?= $category_title; ?></h1>
          <?php
          $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
          $Page = ($Page > 0 ? $Page : 1);
          $Pager = new Pager(BASE . "/artigos/{$category_name}/", "<", ">", 5);
          $Pager->exePager($Page, 10);

          $Read->fullRead("SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, u.user_name, u.user_lastname, u.user_genre FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent)) AND post_author = user_id ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$category_id}");

          if (!$Read->getResult()){
              $Pager->returnPage();
              echo Check::erro("Ainda Não existe posts cadastrados nesta secão. Favor volte mais tarde :)", E_USER_NOTICE);
          } else {
              foreach ($Read->getResult() as $Post){
                  extract($Post);
                  $BOX = 1;
                  $AuthorName = "{$user_name} {$user_lastname}";
                  require REQUIRE_PATH . '/inc/post.php';
              }
          }


          ?>
        <!-- start pagination -->
        <div class="col-md-12 col-sm-12 col-xs-12 text-center margin-100px-top sm-margin-50px-top wow fadeInUp">
          <div class="pagination text-small text-uppercase text-extra-dark-gray">
              <?php
              $Pager->exePaginator(DB_POSTS,
                "WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent))",
                "ct={$category_id}");
              echo $Pager->getPaginator();
              ?>
          </div>
        </div>
        <!-- end pagination -->
      </main>
        <?php require REQUIRE_PATH . '/inc/sidebar.php'; ?>
    </div>
  </div>
</section>
