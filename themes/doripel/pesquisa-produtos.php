<?php

use App\Helpers\Check;

use App\Conn\Create;
use App\Models\Pager;
use App\Conn\Update;

    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
    date_default_timezone_set('America/Sao_Paulo');
    
    
    if (empty($URL[1])){
        require REQUIRE_PATH . '/404.php';
        return;
    }

    $Search = urldecode($URL[1]);
    $SearchPage = urlencode($Search);
    
    if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'])){
        $Read->fullRead("SELECT search_id, search_count FROM " . DB_SEARCH . " WHERE search_key = :key",
            "key={$Search}");
        if ($Read->getResult()){
            $Update = new Update;
            $DataSearch = ['search_count' => $Read->getResult()[0]['search_count'] + 1];
            $Update->exeUpdate(DB_SEARCH, $DataSearch, "WHERE search_id = :id",
                "id={$Read->getResult()[0]['search_id']}");
        } else {
            $Create = new Create;
            $DataSearch = [
                'search_key' => $Search,
                'search_count' => 1,
                'search_date' => date('Y-m-d H:i:s'),
                'search_commit' => date('Y-m-d H:i:s')
            ];
            $Create->exeCreate(DB_SEARCH, $DataSearch);
        }
        $_SESSION['search'][] = $Search;
    }
?>
<section class="wow fadeIn bg-light-gray padding-35px-tb page-title-small top-space">
  <div class="container">
    <div class="row equalize xs-equalize-auto">
      <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 display-table">
        <div class="display-table-cell vertical-align-middle text-left xs-text-center">
          <!-- start page title -->
          <h1 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom text-uppercase">
            <a href="<?= BASE; ?>" title="<?= SITE_NAME . ' ' . SITE_SUBNAME ?>"><?= SITE_NAME . ' ' . SITE_SUBNAME ?></a>
            / Pesquisa por
            <span class="text-deep-pink"><?= $Search; ?></span></h1>
          <!-- end page title -->
        </div>
      </div>
    </div>
  </div>
</section>

<!-- start post content section -->
<section class="padding-70px-top">
  <div class="container">
    <div class="row">
      <main class="col-md-9 col-sm-12 col-xs-12 right-sidebar sm-margin-60px-bottom xs-margin-40px-bottom no-padding-left sm-no-padding-right">
          
          <?php
              $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
              $Page = ($Page > 0 ? $Page : 1);
              $Pager = new Pager(BASE . "/pesquisa-produtos/{$SearchPage}/", "<<", ">>", 5);
              $Pager->exePager($Page, 9);

              if (strpos($Search, '-') !== false) {
                  $SearchExplode = array_unique(explode('-', $Search), SORT_REGULAR);
                  $ColorTerms = [];
                  $ColorParams = [
                      'limit' => $Pager->getLimit(),
                      'offset' => $Pager->getOffset()
                  ];

                  foreach ($SearchExplode as $Pattern => $Key) {
                      $Key = trim((string)$Key);
                      if ($Key === '') {
                          continue;
                      }

                      $Param = "color_{$Pattern}";
                      $ColorTerms[] = "pdt_color LIKE :{$Param}";
                      $ColorParams[$Param] = "%{$Key}%";
                  }

                  $Read->fullRead(
                      "SELECT pdt_title, pdt_subtitle, pdt_name, pdt_ref, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (" . implode(' OR ', $ColorTerms ?: ['pdt_color LIKE :color_fallback']) . ") ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
                      http_build_query($ColorTerms ? $ColorParams : array_merge($ColorParams, ['color_fallback' => "%{$Search}%"]))
                  );
              } else  {
                  $SearchExplode = null;
                  $SearchLike = "%{$Search}%";
                  $Read->fullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_ref, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_title LIKE :title OR pdt_subtitle LIKE :subtitle OR pdt_ref LIKE :ref OR pdt_color LIKE :color) ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
                      http_build_query([
                          'limit' => $Pager->getLimit(),
                          'offset' => $Pager->getOffset(),
                          'title' => $SearchLike,
                          'subtitle' => $SearchLike,
                          'ref' => $SearchLike,
                          'color' => $SearchLike
                      ]));
              }
              
              if (!$Read->getResult()){
                  $Pager->returnPage();
                  echo Check::erro("Não encontramos conteúdo para a palavra-chave <b class='text-extra-dark-gray'>( {$SearchPage} )</b>.",
                      E_USER_NOTICE);
                  
                  ?>
                <!-- start blog section -->
                <section class="bg-light-gray wow fadeIn hover-option4 blog-post-style3">
                  <div style="padding: 20px">
                    <div class="row">
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 center-col margin-five-bottom sm-margin-40px-bottom xs-margin-30px-bottom text-center">
                        <h5 class="alt-font text-extra-dark-gray font-weight-600 width-65 margin-lr-auto md-width-80 xs-width-100">
                          Confira os Móveis mais vistos!</h5>
                      </div>
                    </div>
                    <div class="row equalize xs-equalize-auto">
                      <!-- start blog item -->
                        <?php
                            $Read->fullRead("SELECT p.pdt_title, p.pdt_ref, p.pdt_subtitle, p.pdt_name, p.pdt_cover, p.pdt_created, p.pdt_color FROM " . DB_PDT_DORIPEL . " p WHERE p.pdt_status = 1 AND p.pdt_created <= NOW() ORDER BY p.pdt_created DESC LIMIT :limit",
                                "limit=3");
                            
                            if (!$Read->getResult()){
                                echo Check::erro("Ainda Não existe produtos cadastrados para esta pesquisa. Favor volte mais tarde :)",
                                    E_USER_NOTICE);
                            } else {
                                foreach ($Read->getResult() as $Post){
                                    extract($Post);
                                    require REQUIRE_PATH . '/inc/produto.php';
                                }
                            }
                        ?>
                      <!-- end blog item -->
                    </div>
                  </div>
                </section>
                <!-- end blog section -->
              
              <?php
              } else {
                  foreach ($Read->getResult() as $Post){
                      extract($Post);
                      $BOX = 1;
                      require REQUIRE_PATH . '/inc/produto.php';
                  }
              }
              
              $Pager->exePaginator(DB_PDT_DORIPEL,
                  "WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_title LIKE :title OR pdt_subtitle LIKE :subtitle OR pdt_ref LIKE :ref OR pdt_color LIKE :color)",
                  http_build_query([
                      'title' => "%{$Search}%",
                      'subtitle' => "%{$Search}%",
                      'ref' => "%{$Search}%",
                      'color' => "%{$Search}%"
                  ]));
              echo $Pager->getPaginator();
          ?>
      </main>
        
        <?php require REQUIRE_PATH . '/inc/sidebar-products.php'; ?>

    </div>
  </div>
</section>
