<?php
if (!$Read):
  $Read = new Read;
endif;

$Email = new Email;

$Read->ExeRead(DB_PAGES, "WHERE page_name = :nm", "nm={$URL[0]}");
if (!$Read->getResult()):
  require REQUIRE_PATH . '/404.php';
  return;
else:
  extract($Read->getResult()[0]);
endif;
?>
<!-- start page title section -->
<section class="wow fadeIn bg-extra-dark-gray padding-35px-tb page-title-small top-space">
  <div class="container">
    <div class="row equalize">
      <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 display-table">
        <div class="display-table-cell vertical-align-middle text-left xs-text-center">
          <!-- start page title -->
          <h1 class="alt-font text-white font-weight-600 no-margin-bottom text-uppercase">Meus vídeos</h1>
          <!-- end page title -->
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 display-table text-right xs-text-left xs-margin-10px-top">
        <div class="display-table-cell vertical-align-middle breadcrumb text-small alt-font">
          <!-- breadcrumb -->
          <ul class="xs-text-center">
            <li class="text-deep-pink">Escreva-se no meu canal</li>
            <li>
              <script src="https://apis.google.com/js/platform.js"></script>
              <div class="g-ytsubscribe" data-channelid="<?= SITE_SOCIAL_YOUTUBE; ?>" data-layout="default" data-count="default"></div>
            </li>
          </ul>
          <!-- end breadcrumb -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end page title section -->
<!-- start video style 01 section -->
<section class="wow fadeIn">
  <div class="container">
    <div class="row">
      <div class="col-md-7 col-sm-12 col-xs-12 center-col text-center margin-100px-bottom xs-margin-40px-bottom">
        <div class="position-relative overflow-hidden width-100">
          <span class="text-small text-outside-line-full alt-font font-weight-600 text-uppercase">Vídeos mais recentes</span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 col-sm-12 col-xs-12 fit-videos text-center sm-margin-30px-bottom">
        <!-- start vimeo video -->
        <iframe width="560" height="315" src="https://www.youtube.com/embed/Xn5_OVGvws4?rel=0&amp;showinfo=0" allowfullscreen></iframe>
        <!-- end vimeo video -->
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12 text-center fit-videos sm-margin-30px-bottom">
        <!-- start youtube video -->
        <iframe width="560" height="315" src="https://www.youtube.com/embed/1hSxFJamM3Y?rel=0&amp;showinfo=0" allowfullscreen></iframe>
        <!-- end youtube video -->
      </div>
    </div>
  </div>
</section>
<!-- end video style 01 section -->
<!-- start video style 02 section -->
<section class="parallax wow fadeIn" data-stellar-background-ratio="0.1" style="background:url('<?= INCLUDE_PATH; ?>/images/keuren-canedo-melhor-equipe-do-mundo1600.jpg');">
  <div class="opacity-extra-medium bg-black"></div>
  <div class="container position-relative">
    <div class="row">
      <div class="col-lg-6 col-md-6 text-center center-col">
          <a  href="https://www.youtube.com/watch?v=4C9P00hug_8" class="popup-youtube"><img src="<?= INCLUDE_PATH; ?>/images/icon-play.png" class="width-30" alt=""/></a>
        <h5 class="alt-font text-white">Venha trabalhar na melhor equipe do mundo!</h5>
      </div>
    </div>
  </div>
</section>
<!-- end video style 02 section -->
<!-- start video style 03 section -->
<section class="wow fadeIn">
  <div class="container">
    <div class="row">
      <div class="col-md-7 col-sm-12 col-xs-12 center-col text-center margin-100px-bottom xs-margin-40px-bottom">
        <div class="position-relative overflow-hidden width-100">
          <span class="text-small text-outside-line-full alt-font font-weight-600 text-uppercase">MEU MUNDO, SUA CHANCE</span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 text-center display-table sm-margin-50px-bottom xs-margin-30px-bottom center-col wow fadeIn">
        <div class="display-table-cell vertical-align-middle overflow-hidden position-relative">
          <div class="opacity-medium bg-extra-dark-gray"></div>
          <img src="<?= INCLUDE_PATH; ?>/images/video-tia-marcela.jpg" class="width-100" alt="Kéuren Cañedo e tia Marcela"/>
          <div class="absolute-middle-center text-center">
            <span class="text-medium-gray text-extra-small letter-spacing-1 alt-font text-uppercase margin-20px-bottom display-block">Minha tia Marcela</span>
            <h5 class="alt-font text-white width-70 sm-width-100 center-col">A força do amor.</h5>
            <a href="https://www.youtube.com/watch?v=KzmLncvoPmQ" class="popup-youtube btn btn-medium btn-transparent-white text-medium btn-rounded">Assistir Vídeo <i class="fa fa-youtube-play icon-very-small" aria-hidden="true"></i></a>
          
                       
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end video style 03 section -->
<!-- start video style 04 section -->
<section class="position-relative wow fadeIn">
  <div class="opacity-medium bg-extra-dark-gray z-index-0"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-sm-8 col-xs-12 display-table small-screen center-col">
        <div class="display-table-cell vertical-align-middle text-center">
          <span class="text-medium margin-20px-bottom display-block alt-font">Se gostou do vídeo e quer saber mais, entre em contato.</span>
          <h4 class="alt-font text-light-gray">Como fazer seu negócio online funcionar.</h4>
          <a href="http://conteudo.keurencanedo.com/quer-trabalhar-comigo" title="Oportunidade para trabalhar na minha equipe" target="_blank" class="btn btn-small btn-white btn-rounded margin-15px-top">COMECE SEU NEGÓCIO</a>
        </div>
      </div>
    </div>
  </div>
  <!-- start HTML5 video -->
  <video loop="" autoplay="" controls="" muted class="html-video" poster="http://placehold.it/1920x1280">
    <source type="video/mp4" src="video/video.mp4">
    <source type="video/ogg" src="video/video.ogg">
    <source type="video/webm" src="<?= BASE; ?>/uploads/video/video.webm">
  </video>
  <!-- end HTML5 video -->
</section>
<!-- end video style 04 section -->