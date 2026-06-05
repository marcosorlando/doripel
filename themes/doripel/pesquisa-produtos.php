<?php
    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
    date_default_timezone_set('America/Sao_Paulo');
    
    
    $Search = urldecode($URL[1]);
    $SearchPage = urlencode($Search);
    
    if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'])):
        $Read->FullRead("SELECT search_id, search_count FROM " . DB_SEARCH . " WHERE search_key = :key",
            "key={$Search}");
        if ($Read->getResult()):
            $Update = new Update;
            $DataSearch = ['search_count' => $Read->getResult()[0]['search_count'] + 1];
            $Update->ExeUpdate(DB_SEARCH, $DataSearch, "WHERE search_id = :id",
                "id={$Read->getResult()[0]['search_id']}");
        else:
            $Create = new Create;
            $DataSearch = [
                'search_key' => $Search,
                'search_count' => 1,
                'search_date' => date('Y-m-d H:i:s'),
                'search_commit' => date('Y-m-d H:i:s')
            ];
            $Create->ExeCreate(DB_SEARCH, $DataSearch);
        endif;
        $_SESSION['search'][] = $Search;
    endif;
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
              $Page = (!empty($URL[2]) ? $URL[2] : 1);
              $Pager = new Pager(BASE . "/pesquisa-produtos/{$SearchPage}/", "<<", ">>", 5);
              $Pager->ExePager($Page, 9);
              
              if (strpos($Search, '-')) {

                  $SearchExplode = array_unique(explode('-', $Search), SORT_REGULAR);
                  
                  foreach ($SearchExplode as $Pattern => $Key) {
                      $Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_ref, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_color LIKE '%' :s '%') ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset","limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Key}");
                  }
              } else {
                  $SearchExplode = null;
                  $Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_name, pdt_ref, pdt_cover, pdt_created, pdt_color FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = 1 AND pdt_created <= NOW() AND (pdt_title LIKE '%' :s '%' OR pdt_subtitle LIKE '%' :s '%' OR pdt_ref LIKE '%' :s '%' OR pdt_color LIKE '%' :s '%') ORDER BY pdt_created DESC LIMIT :limit OFFSET :offset",
                      "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}");
              }
              
              if (!$Read->getResult()):
                  $Pager->ReturnPage();
                  echo Erro("Não encontramos conteúdo para a palavra-chave <b class='text-extra-dark-gray'>( {$SearchPage} )</b>.",
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
                            $Read->FullRead("SELECT p.pdt_title, p.pdt_ref, p.pdt_subtitle, p.pdt_name, p.pdt_cover, p.pdt_created, p.pdt_color, g.id, g.image FROM " . DB_PDT_DORIPEL . " p, " . DB_PDT_GALLERY_DORIPEL . " g WHERE pdt_status = 1 AND pdt_created<= NOW() ORDER BY pdt_created DESC LIMIT :limit",
                                "limit=3");
                            
                            if (!$Read->getResult()):
                                echo Erro("Ainda Não existe produtos cadastrados para esta pesquisa. Favor volte mais tarde :)",
                                    E_USER_NOTICE);
                            else:
                                foreach ($Read->getResult() as $Post):
                                    extract($Post);
                                    require REQUIRE_PATH . '/inc/produto.php';
                                endforeach;
                            endif;
                        ?>
                      <!-- end blog item -->
                    </div>
                  </div>
                </section>
                <!-- end blog section -->
              
              <?php
              else:
                  foreach ($Read->getResult() as $Post):
                      extract($Post);
                      $BOX = 1;
                      require REQUIRE_PATH . '/inc/produto.php';
                  endforeach;
              endif;
              
              $Pager->ExePaginator(DB_PDT_DORIPEL,
                  "WHERE pdt_status = 1 AND pdt_created <= NOW() AND(pdt_title LIKE '%' :s '%' OR pdt_subtitle LIKE '%' :s '%' OR pdt_ref LIKE '%' :s '%' OR pdt_color LIKE '%' :s '%')",
                  "s={$Search}");
              echo $Pager->getPaginator();
          ?>
      </main>
        
        <?php require REQUIRE_PATH . '/inc/sidebar-products.php'; ?>

    </div>
  </div>
</section>