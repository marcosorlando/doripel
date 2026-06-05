<!-- start header -->
<header class="header-with-topbar">
    <!-- start navigation -->
    <nav class="navbar navbar-default bootsnav navbar-fixed-top header-dark bg-transparent nav-box-width padding-four-lr">
        <div class="container-fluid nav-header-container">
            <div class="row">
                <!-- start logo -->
                <div class="col-lg-2 col-md-2 col-xs-5">
                    <a href="<?= BASE; ?>" title="Doripel Móveis" class="logo"><img src="<?= INCLUDE_PATH; ?>/images/logo.png" data-at2x="<?= INCLUDE_PATH; ?>/images/logo@2x.png" class="logo-dark default" alt="Doripel Móveis"><img src="<?= INCLUDE_PATH; ?>/images/logo-full-white.png" data-at2x="<?= INCLUDE_PATH; ?>/images/logo-full-white@2x.png" alt="Doripel Móveis" class="logo-light"></a>
                </div>
                <!-- end logo -->
                <div class="col-lg-9 col-md-9 col-xs-2 accordion-menu">
                    <button type="button" class="navbar-toggle collapsed pull-right" data-toggle="collapse" data-target="#navbar-collapse-toggle-1">
                        <span class="sr-only">toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <div class="navbar-collapse collapse no-padding-right" id="navbar-collapse-toggle-1">
                        <ul id="accordion" class="nav navbar-nav navbar-center alt-font text-normal" data-in="fadeIn" data-out="fadeOut">
                            <!--start menu item-->
                            <li>
                                <a title="<?= SITE_NAME; ?> | Home" href="<?= BASE; ?>"><i style="font-size:1.5em; padding-left: 8px;" class="fa fa-home"></i></a>
                            </li>

                            <?php

                                $Read->fullRead("SELECT cat_id, cat_title, cat_name FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_parent IS NULL AND cat_id IN(SELECT pdt_category FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = 1) ORDER BY cat_title ASC");
                                //$Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_parent IS NULL ORDER BY cat_name ASC");
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $Cat) {
                                        echo "<li class='dropdown simple-dropdown'><a title=' " . SITE_NAME . " | {$Cat['cat_title']}' href='" . BASE . "/moveis/{$Cat['cat_name']}'>{$Cat['cat_title']}</a>";

                                        $Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_parent = :ct AND EXISTS(SELECT 1 FROM " . DB_PDT_DORIPEL . " p WHERE p.pdt_status = 1 AND p.pdt_created <= NOW() AND FIND_IN_SET(cat_id, p.pdt_subcategory)) ORDER BY cat_name ASC", "ct={$Cat['cat_id']}");
                                        if ($Read->getResult()) {
                                            echo "<ul class='dropdown-menu' role='menu' >";
                                            foreach ($Read->getResult() as $SubCat) {
                                                echo "<li><a title='{$Cat['cat_title']} | {$SubCat['cat_title']}' href='" . BASE . "/moveis/{$SubCat['cat_name']}'>{$SubCat['cat_title']}</a></li>";
                                            }
                                            echo "</ul>";
                                        }
                                        echo "</li>";
                                    }
                                }

                                $Read->exeRead(DB_CATEGORIES, "WHERE category_parent IS NULL ORDER BY category_name ASC");
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $Cat) {
                                        echo "<li class='dropdown simple-dropdown'><a title=' " . SITE_NAME . " | {$Cat['category_title']}' href='" . BASE . "/artigos/{$Cat['category_name']}'>{$Cat['category_title']}</a>";
                                        $Read->exeRead(DB_CATEGORIES, "WHERE category_parent = :ct ORDER BY category_name ASC", "ct={$Cat['category_id']}");
                                        if ($Read->getResult()) {
                                            echo "<ul class='dropdown-menu' role='menu' >";
                                            foreach ($Read->getResult() as $SubCat) {
                                                echo "<li><a title='{$Cat['category_title']} | {$SubCat['category_title']}' href='" . BASE . "/artigos/{$SubCat['category_name']}'>{$SubCat['category_title']}</a></li>";
                                            }
                                            echo "</ul>";
                                        }
                                        echo "</li>";
                                    }
                                }

                                $Read->fullRead("SELECT page_title, page_name FROM " . DB_PAGES . " WHERE page_status = 1 AND page_id != 2 ORDER BY page_order ASC, page_name ASC");
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $Page) {
                                        echo "<li><a title='" . SITE_NAME . " | {$Page['page_title']}' href='" . BASE . "/{$Page['page_name']}'>{$Page['page_title']}</a></li>";
                                    }
                                }

                                //if (ACC_MANAGER) {
                                //   echo "<li class='login'>";
                                // require '_cdn/widgets/account/account.bar.php';
                                // echo "</li>";
                                //}
                            ?>
                            <!--              <li>-->
                            <!--                <a title="--><? //= SITE_NAME; ?><!-- | PEDIDOS ONLINE" >-->
                            <!--                  <span class="btn btn-black" style="margin-top: -8px">-->
                            <!--                  <i style="font-size:1em; padding-left: 0px;" class="fa fa-file-powerpoint-o animated pulse infinite"> </i>  PEDIDOS </span>-->
                            <!--                </a>-->
                            <!--              </li>-->
                            <li>
                                <a title="<?= SITE_NAME; ?> | PEDIDOS ONLINE" href="http://200.215.160.7:8085/pedidos4/login.asp" target="_blank">
                                    <span class="btn_pedidos"><i style="font-size:1.2em;" class="fa fa-file-powerpoint-o animated  infinite"> </i>  PEDIDOS</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-xs-5 sm-width-auto text-right sm-no-padding-left">
                    <div class="header-searchbar no-border-left">
                        <a href="#search-header" class="header-search-form text-white"><i class="fa fa-search search-button"></i></a>
                        <!-- search input-->
                        <form id="search-header" method="post" action="" name="search" class="mfp-hide search-form-result">
                            <div class="search-form position-relative">
                                <button class="fa fa-search close-search search-button"></button>
                                <input type="text" name="p" class="search-input" placeholder="Digite sua pesquisa..." autocomplete="on">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- end navigation -->
</header>
<!-- end header -->
