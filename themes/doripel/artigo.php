<?php
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Sao_Paulo');

if (!$Read):
  $Read = new Read;
endif;

$Read->ExeRead(DB_POSTS, "WHERE post_name = :nm", "nm={$URL[1]}");
if (!$Read->getResult()):
  require REQUIRE_PATH . '/404.php';
  return;
else:
  extract($Read->getResult()[0]);
  $Update = new Update;
  $UpdateView = ['post_views' => $post_views + 1, 'post_lastview' => date('Y-m-d H:i:s')];
  $Update->ExeUpdate(DB_POSTS, $UpdateView, "WHERE post_id = :id", "id={$post_id}");

  $Read->FullRead("SELECT category_title, category_name FROM " . DB_CATEGORIES . " WHERE category_id = :id", "id={$post_category}");
  $PostCategory = $Read->getResult()[0];

  $Read->FullRead("SELECT user_name, user_lastname, user_thumb, user_genre,user_twitter, user_youtube, user_google, user_description FROM " . DB_USERS . " WHERE user_id = :user", "user={$post_author}");
  $AuthorName = "{$Read->getResult()[0]['user_name']} {$Read->getResult()[0]['user_lastname']}";
endif;
extract($Read->getResult()[0]);
?>

<section class="wow fadeIn bg-light-gray padding-35px-tb page-title-small top-space">
  <div class="container">
    <div class="row equalize xs-equalize-auto">
      <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 display-table">
        <div class="display-table-cell vertical-align-middle text-left xs-text-center">
          <!-- start page title -->
          <h1 class="alt-font text-extra-dark-gray font-weight-600 no-margin-bottom text-uppercase"><?= $post_title; ?></h1>
          <!-- end page title -->
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 display-table text-right xs-text-left xs-margin-10px-top">
        <div class="display-table-cell vertical-align-middle breadcrumb text-small alt-font">
          <!-- breadcrumb -->
          <ul class="xs-text-center text-uppercase">
            <li><a class="text-dark-gray" href="<?= BASE; ?>/artigos/<?= $PostCategory['category_name']; ?>" title="Ver mais: <?= $PostCategory['category_title']; ?> em <?= SITE_NAME; ?>!"><?= $PostCategory['category_title']; ?></a></li>
            <li class="text-dark-gray"><a href="#">Por <?= $AuthorName; ?></a></li>
          </ul>
          <!-- end breadcrumb -->   
        </div>
      </div>
    </div>
  </div>
</section>
<!-- start post content section --> 
<section class="single_post">
  <div class="container">
    <div class="row">
      <main class="col-md-9 col-sm-12 col-xs-12 right-sidebar sm-margin-60px-bottom xs-margin-40px-bottom no-padding-left sm-no-padding-right">
        <div class="col-md-12 col-sm-12 col-xs-12 blog-details-text last-paragraph-no-margin">
          <?php
          if ($post_video):
            echo "<div class='embed-container'>";
            echo "<iframe id='mediaview' width='640' height='360' src='https://www.youtube.com/embed/{$post_video}?rel=0&amp;showinfo=0&autoplay=0&origin=" . BASE . "' frameborder='0' allowfullscreen></iframe>";
            echo "</div>";
          else:
            echo "<img class='width-100' title='{$post_title}' alt='{$post_title}' src='" . BASE . "/tim.php?src=uploads/{$post_cover}&w=" . IMAGE_W . "&h=" . IMAGE_H . "'/>";
          endif;
          ?>
          <h2><?= $post_subtitle; ?></h2>
          <p class="postby">
            <i class="<?= $user_genre == 1 ? 'icon-profile-male' : 'icon-profile-female'; ?>"></i> <b><?= $AuthorName; ?></b> 
            <i class="icon-calendar"></i> <time datetime="<?= date('Y-m-d', strtotime($post_date)); ?>" pubdate="pubdate"><?= utf8_encode(strftime(" %d de %B de %Y", strtotime($post_date))); ?></time>
            <i class="icon-ribbon"></i> <b><?= $PostCategory['category_title']; ?></b> 
            <i class="icon-laptop"></i> <b><?= $post_views; ?></b> views 
            <i class="icon-clock"></i> <b><?= $post_time; ?> </b> min. de leitura
          </p>
          <?php
          $WC_TITLE_LINK = $post_title;
          $WC_SHARE_HASH = "#KeurenCanedo";
          $WC_SHARE_LINK = BASE . "/artigo/{$post_name}";
//          require './_cdn/widgets/share/share.wc.php';
          ?>
          <div class="htmlchars">
            <?= $post_content; ?>
          </div>
          <?php require './_cdn/widgets/share/share.wc.php'; ?>



          <!--              
                        
                        <blockquote class="border-color-deep-pink">
                          <p>Reading is not only informed by what’s going on with us at that moment, but also governed by how our eyes and brains work to process information. What you see and what you’re experiencing as you read these words is quite different.</p>
                          <footer>Jason Maria</footer>
                        </blockquote>
                        <img src="http://placehold.it/900x600" alt="" class="width-100 margin-45px-bottom">
                         dropcaps 
                        <p><span class="first-letter first-letter-block bg-extra-dark-gray text-white">M</span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. It has survived not only five centuries. Simply dummy text of the printing and typesetting industry. It has survived not only five centuries. There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                         end dropcaps 
                        <figure class="wp-caption alignleft"><img alt="" src="http://placehold.it/350x255"><figcaption class="wp-caption-text">There is no sincerer love than the love of food.</figcaption></figure>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the lorem ipsum generators on the internet tend to repeat predefined chunks as necessary, making this the first true generator on the internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour.</p>
                        <span class="text-extra-dark-gray font-weight-500 margin-15px-bottom display-block text-medium">You can never quit. Winners never quit, and quitters never win</span>
                        <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the lorem ipsum generators on the internet tend to repeat predefined chunks as necessary, making this the first true generator on the internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour.</p>
          -->
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12 sm-text-center">
          <div class="tag-cloud margin-20px-bottom">

            <?php
            $tags = explode(',', $post_tags);
            foreach ($tags as $key => $value) :
              ?>
              <a href="<?= BASE; ?>/pesquisa/<?= $value ?>"><?= $value ?></a>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12 margin-30px-top">
          <div class="display-table width-100 border-all border-color-extra-light-gray padding-50px-all sm-padding-30px-all xs-padding-20px-all">

            <div class="display-table-cell width-130px text-center vertical-align-top xs-margin-15px-bottom xs-width-100 xs-display-block xs-text-center">
              <img src="<?= BASE; ?>/uploads/<?= $user_thumb; ?>" class="img-circle width-100px" alt="<?= $user_name; ?> <?= $user_lastname; ?>" title="<?= $user_name; ?> <?= $user_lastname; ?>" />
            </div>
            <div class="padding-40px-left display-table-cell vertical-align-top last-paragraph-no-margin xs-no-padding-left xs-display-block xs-text-center">
              <a href="#" class="text-extra-dark-gray text-uppercase alt-font font-weight-600 margin-10px-bottom display-inline-block text-small"><?= $user_name; ?> <?= $user_lastname; ?></a>
              <p><?= $user_description; ?></p>
              <a class="btn btn-very-small btn-black margin-20px-top">Todos as publicações do(a) autor(a)</a>
            </div>
          </div>
        </div>



        <!--
                <div class="col-md-12 col-sm-12 col-xs-12 blog-details-comments">
                  <div class="width-100 margin-lr-auto text-center margin-80px-tb sm-margin-50px-tb xs-margin-30px-tb">
                    <div class="position-relative overflow-hidden width-100">
                      <span class="text-small text-outside-line-full alt-font font-weight-600 text-uppercase text-extra-dark-gray">10 Comments</span>
                    </div>
                    
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 margin-eight-top" id="comments">
                  <div class="divider-full bg-medium-light-gray"></div>
                </div>-->
        <div class="col-md-12 col-sm-12 col-xs-12 margin-lr-auto text-center margin-80px-tb sm-margin-50px-tb xs-margin-30px-tb">
          <div class="position-relative overflow-hidden width-100">

            <?php
            if (APP_COMMENTS && COMMENT_ON_POSTS):
              $CommentKey = $post_id;
              $CommentType = 'post';
              require '_cdn/widgets/comments/comments.php';
            endif;
            ?>
          </div>
        </div>
        <!-- start post item -->
        <?php
        $Read->ExeRead(DB_POSTS, "WHERE post_status = 1 AND post_date <= NOW() AND post_category_parent != :ct AND post_id != :id ORDER BY post_date DESC LIMIT 3", "ct={$post_category_parent}&id={$post_id}");

        if ($Read->getResult()):

          echo '<div class="col-md-12 col-sm-12 col-xs-12 no-padding">';
          echo '<div class="col-md-12 col-sm-12 col-xs-12 margin-lr-auto text-center margin-80px-tb sm-margin-50px-tb xs-margin-30px-tb">';
          echo '<div class="position-relative overflow-hidden width-100">';
          echo '<span class="text-small text-outside-line-full alt-font font-weight-600 text-uppercase text-extra-dark-gray">Artigos Relacionados</span>';
          echo '</div>';
          echo '</div>';

          foreach ($Read->getResult() as $More):
            extract($More);
            require REQUIRE_PATH . '/inc/post.php';
          endforeach;

          echo '</div>';
        endif;
        ?>
      </main>
      <?php require REQUIRE_PATH . '/inc/sidebar.php'; ?>
    </div>
  </div>
</section>

