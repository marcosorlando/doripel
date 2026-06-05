<?php
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Sao_Paulo');
?>

<aside class="col-md-3 col-sm-12 col-xs-12 pull-right">
  <div class="display-inline-block width-100 margin-45px-bottom xs-margin-25px-bottom">
    <form name="search" action="" method="post" enctype="multipart/form-data">
      <div class="position-relative">
        <input type="text" name="p" class="bg-transparent text-small no-margin border-color-extra-light-gray medium-input pull-left" placeholder="Pesquisar móveis..." autocomplete="off">
        <button class="bg-transparent btn position-absolute right-0 top-1"><i class="fa fa-search no-margin-left"></i>
        </button>
      </div>
    </form>
  </div>

  <div class="margin-45px-bottom xs-margin-25px-bottom">
    <div class="text-extra-dark-gray margin-20px-bottom alt-font text-uppercase text-small font-weight-600 aside-title">
      <span>Padrão/Cor</span></div>

    <form name="search" action="" method="post" enctype="multipart/form-data">
      <div class="position-relative">
                  <?php
          $Read->ExeRead(DB_PDT_COLORS_DORIPEL,
            "WHERE color_title IN(SELECT pdt_color FROM " . DB_PDT_DORIPEL . " WHERE pdt_status <> 0 AND pdt_created <= NOW()) ORDER BY color_title ASC");
          if (!$Read->getResult()):
              echo Erro("Ainda não existem cores cadastradas!", E_USER_NOTICE);
          else:

              foreach ($Read->getResult() as $Colors):
                  extract($Colors);
                  echo "<label><input name='p' type='radio' class='input-check-filter' value='{$color_name}'>  {$color_title}</label><br>";
              endforeach;
          endif;
          ?>
        <button class="btn btn-deep-pink btn-small"><i class="fa fa-filter"></i> FILTRAR</button>
      </div>
    </form>

  </div>


  <div class="margin-45px-bottom xs-margin-25px-bottom">
    <div class="text-extra-dark-gray margin-20px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
      <span>Categorias</span></div>

      <?php
      $Read->ExeRead(DB_PDT_CATS_DORIPEL,
        "WHERE cat_parent IS NULL AND cat_id IN(SELECT pdt_category FROM " . DB_PDT_DORIPEL . " WHERE pdt_status <> 0 AND pdt_created <= NOW()) ORDER BY cat_title ASC");
      if (!$Read->getResult()):
          echo Erro("Ainda não existem sessões cadastradas!", E_USER_NOTICE);
      else:
          echo "<ul class='list-style-6 margin-50px-bottom text-small'>";
          foreach ($Read->getResult() as $Ses):
              echo "<li><a title='moveis/{$Ses['cat_name']}' href='" . BASE . "/moveis/{$Ses['cat_name']}'><i class='fa fa-tags text-deep-pink'></i> {$Ses['cat_title']}</a></li>";
              $Read->ExeRead(DB_PDT_CATS_DORIPEL,
                "WHERE cat_parent = :cp AND cat_id IN(SELECT pdt_subcategory FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = 1 AND pdt_created <= NOW()) ORDER BY cat_title ASC",
                "cp={$Ses['cat_id']}");
              if ($Read->getResult()):
                  foreach ($Read->getResult() as $Cat):
                      echo "<li><a title='moveis/{$Cat['cat_name']}' href='" . BASE . "/moveis/{$Cat['cat_name']}'><i class='margin-10px-left fa fa-tag'></i> {$Cat['cat_title']}</a></li>";
                  endforeach;
              endif;
          endforeach;
          echo "</ul>";
      endif;
      ?>
  </div>
  <div class="margin-45px-bottom xs-margin-25px-bottom">
    <div class="text-extra-dark-gray margin-25px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
      <span>Newsletter</span></div>
    <div class="display-inline-block width-100">
        <?php
        $CAPTION = 'news1';
        $AC_BUTTON = 'CADASTRE-SE!';
        require REQUIRE_PATH . '/inc/activeform.php';
        ?>
    </div>
  </div>

  <?php
   require REQUIRE_PATH. "/inc/banner-cta.php";
   ?>
</aside>