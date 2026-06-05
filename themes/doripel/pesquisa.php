<?php
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Sao_Paulo');


$Search = urldecode($URL[1]);
$SearchPage = urlencode($Search);

if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'])):
  $Read->FullRead("SELECT search_id, search_count FROM " . DB_SEARCH . " WHERE search_key = :key", "key={$Search}");
  if ($Read->getResult()):
    $Update = new Update;
    $DataSearch = ['search_count' => $Read->getResult()[0]['search_count'] + 1];
    $Update->ExeUpdate(DB_SEARCH, $DataSearch, "WHERE search_id = :id", "id={$Read->getResult()[0]['search_id']}");
  else:
    $Create = new Create;
    $DataSearch = ['search_key' => $Search, 'search_count' => 1, 'search_date' => date('Y-m-d H:i:s'), 'search_commit' => date('Y-m-d H:i:s')];
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
          <h1 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom text-uppercase"><a href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a> / Pesquisa por <span class="text-deep-pink"><?= $Search; ?></span></h1>
          <!-- end page title -->
        </div>
      </div>    
    </div>
  </div>

</section>
<div class="clear-both"><br/></div>
<div class="container main_content wc_blog_content">
  <div class="content">
    <div class="main_blog">
      <?php
      $Page = (!empty($URL[2]) ? $URL[2] : 1);
      $Pager = new Pager(BASE . "/pesquisa/{$SearchPage}/", "<<", ">>", 5);
      $Pager->ExePager($Page, 10);

      $Read->FullRead("SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, u.user_name, u.user_lastname, u.user_genre FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE post_status = 1 AND post_date <= NOW() AND post_author = user_id AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%' OR MONTH(post_date) = :s) ORDER BY post_date DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}");

      if (!$Read->getResult()):
        $Pager->ReturnPage();
        echo Erro("Não encontramos conteúdo para a palavra-chave <b class='text-extra-dark-gray'>( {$SearchPage} )</b>.", E_USER_NOTICE);
        ?>
        <!-- start blog section -->
        <section class="bg-light-gray wow fadeIn hover-option4 blog-post-style3 padding-70px-top">
          <div style="padding: 20px">
            <div class="row">
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 center-col margin-five-bottom sm-margin-40px-bottom xs-margin-30px-bottom text-center">                
                <h5 class="alt-font text-extra-dark-gray font-weight-600 width-65 margin-lr-auto md-width-80 xs-width-100">Confira os Blog Posts mais vistos!</h5>
              </div>
            </div>
            <div class="row equalize xs-equalize-auto">
              <!-- start blog item -->
              <?php
              $Read->FullRead("SELECT p.post_title, p.post_subtitle, p.post_content, p.post_name, p.post_cover, p.post_date, p.post_author, u.user_name, u.user_lastname, u.user_genre FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE post_status = 1 AND post_date <= NOW() AND post_author = user_id ORDER BY post_date DESC LIMIT :limit", "limit=3");


              if (!$Read->getResult()):
                echo Erro("Ainda Não existe posts cadastrados nesta secão. Favor volte mais tarde :)", E_USER_NOTICE);
              else:
                foreach ($Read->getResult() as $Post):
                  extract($Post);
                  require REQUIRE_PATH . '/inc/post-index.php';
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
          $Read->FullRead("SELECT user_name, user_lastname, user_thumb, user_genre,user_twitter, user_youtube, user_google, user_description FROM " . DB_USERS . " WHERE user_id = :user", "user={$post_author}");
          $AuthorName = "{$Read->getResult()[0]['user_name']} {$Read->getResult()[0]['user_lastname']}";

          $BOX = 1;
          require REQUIRE_PATH . '/inc/post.php';
        endforeach;
      endif;

      $Pager->ExePaginator(DB_POSTS, "WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%')", "s={$Search}");
      echo $Pager->getPaginator();
      ?>
    </div>

    <!--<-?php require REQUIRE_PATH . '/inc/sidebar.php'; ?>-->
    <div class="clear"></div>
  </div>
</div>