<?php

    use App\Conn\Read;

?>
<header class="header scrolling-header">
	<nav id='nav' class='navbar navbar-default navbar-fixed-top' role='navigation'>
		<div class='container relative-nav-container'>
			<a class='toggle-button visible-xs-block' data-toggle='collapse' data-target='#navbar-collapse'>
				<i class='fa fa-navicon'></i>
			</a>
			<a class='navbar-brand scroll' title="Ir para Home" href='<?= BASE; ?>'>
				<img class='normal-logo hidden-xs' src='<?= INCLUDE_PATH; ?>/assets/svg/zen-white.svg'
				     title='Zen Agência Web - Marketing Digital' alt='Logotipo - Zen Agência Web'/>
				<img class='scroll-logo hidden-xs' src='<?= INCLUDE_PATH; ?>/assets/svg/zen-black.svg'
				     title='Zen Agência Web - Marketing Digital' alt='Logotipo - Zen Agência Web'/>
				<img class='scroll-logo visible-xs-block' src='<?= INCLUDE_PATH; ?>/assets/svg/zen-symbol.svg'
				     title='Zen Agência Web - Marketing Digital' alt='Logotipo - Zen Agência Web'/>
			</a>
			<ul class='nav navbar-nav navbar-right nav-icons wrap-user-control'>
				<li>
					<a id='search-open' href='#'><i class='fa fa-search'></i></a>
				</li>
			</ul>
			<div class='navbar-collapse collapse floated' id='navbar-collapse'>
				<ul class='nav navbar-nav navbar-with-inside clearfix navbar-right with-border'>
					<li class="<?= ($URL[0] === 'index') ? 'active' : ''; ?> ">
						<a class='icon icon-House' title="<?= SITE_NAME . ' ' . SITE_SUBNAME ?> -  Home"
						   href='<?= BASE; ?>'></a>
					</li>

					<!-- <li>
                         <a title='Sistemas Zen' href='#'>Sistemas</a>
                         <div class=' wrap-inside-nav animated slideInUp'>
                             <div class='inside-col'>
                                 <ul class='inside-nav'>
                                     <li class='icon-home'><a href='#'> Sistema para EAD</a></li>
                                     <li class='icon-home'><a href='#'> Sistema Imobiliário</a></li>
                                     <li class='icon-home'><a href='#'> Sistema E-commerce</a></li>
                                     <li class='icon-home'><a href='#'> Sistema para Eventos</a></li>
                                     <li class='icon-home'><a href='#'> Ingressos Online</a></li>
                                     <li class='icon-home'><a href='#'> Sistema para RH</a></li>
                                 </ul>
                             </div>
                         </div>
                     </li>-->
                    <?php
                        $Read ??= new Read();
                        $Read->exeRead(
                            DB_CATEGORIES,
                            'WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM ' . DB_POSTS . ' WHERE post_status = 1 AND post_date <= NOW()) ORDER BY category_title ASC'
                        );
                        if ($Read->getResult()) {
                            foreach ($Read->getResult() as $Cat) {
                                echo "<li><a title='{$Cat['category_title']}' href='" . BASE . "/artigos/{$Cat['category_name']}'>{$Cat['category_title']}</a>";

                                $Read->exeRead(
                                    DB_CATEGORIES,
                                    'WHERE category_parent = :ct ORDER BY category_name ASC',
                                    "ct={$Cat['category_id']}"
                                );
                                if ($Read->getResult()) {
                                    echo "<div class='wrap-inside-nav animated slideInUp'>";
                                    echo "<div class='inside-col'>";
                                    echo "<ul class='inside-nav'>";
                                    foreach ($Read->getResult() as $SubCat) {
                                        echo "<li><a title='{$SubCat['category_title']}' href='" . BASE . "/artigos/{$SubCat['category_name']}'>{$SubCat['category_title']}</a></li>";
                                    }
                                    echo '</ul>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</li>';
                            }
                        }

                        $Read->fullRead(
                            'SELECT page_title, page_name FROM ' . DB_PAGES . ' WHERE page_status = 1 AND page_menu != 0 ORDER BY page_order ASC, page_name ASC'
                        );

                        if ($Read->getResult()) {
                            foreach ($Read->getResult() as $Page) {
                                $Class = ($Page['page_name'] == $URL[0]) ? 'active' : '';
                                echo "<li class=\"{$Class}\" ><a title='{$Page['page_title']}' href='" . BASE . "/{$Page['page_name']}'>{$Page['page_title']}</a></li>";
                            }
                        }
                    ?>
				</ul>

			</div>
		</div>
		<div class="navbar-search">
			<div class="container">
				<form class="search_form" name="searchTop" action="" method="post" enctype="multipart/form-data">
					<div class="input-group">
						<input type="text" class="form-control" name="s" placeholder="DIGITE O QUE PROCURA +  ENTER"
						       required>
						<span class="input-group-btn">
                        <button type="reset" class="btn search-close" id="search-close">
                            <i class="fa fa-close "></i>
                        </button>
                    </span>
					</div>
				</form>
			</div>
		</div>

	</nav>
</header>
