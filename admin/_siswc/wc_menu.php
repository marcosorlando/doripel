<?php

    use App\Conn\Read;

    $Read ??= new Read();
    $Admin['user_level'] = $Admin['user_level'] ?? $_SESSION['userLogin']['user_level'];

    if (APP_PORTFOLIO && $Admin['user_level'] >= LEVEL_WC_PORTFOLIO) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'custom/') ? 'dashboard_nav_menu_active' : ''; ?>"><a
					class='icon-books' href='dashboard.php?wc=portfolio/home'>Trampos</a>

			<ul class='dashboard_nav_menu_sub'>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'portfolio/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href='dashboard.php?wc=portfolio/create'>&raquo; Novo Trampo</a></li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'portfolio/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href='dashboard.php?wc=portfolio/home'>&raquo; Ver Trampos </a></li>
				<li class="dashboard_nav_menu_sub_li <?= strstr(
                    $getViewInput,
                    'portfolio/categor'
                ) ? 'dashboard_nav_menu_active' : ''; ?>"><a href='dashboard.php?wc=portfolio/categories'>&raquo;
						Categorias</a></li>

			</ul>

		</li>
        <?php
    }
    if (APP_CV && $Admin['user_level'] >= LEVEL_WC_CV) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'curriculum/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-drawer" href="dashboard.php?wc=curriculos/home">Base de Currículos</a>
		</li>
        <?php
    }

    if (APP_OUVIDORIA && $Admin['user_level'] >= LEVEL_WC_OUVIDORIA) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'ouvidoria/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-bullhorn" href="dashboard.php?wc=ouvidoria/home">Ouvidoria</a>
		</li>
        <?php
    }

    if (APP_LINKTREE && $Admin['user_level'] >= LEVEL_WC_LINKTREE) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'linktree/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-tree" href="dashboard.php?wc=linktree/home">Cartões LinkTree</a>
			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'linktree/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=linktree/create">&raquo; Novo Cartão</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'linktree/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=linktree/home">&raquo; Todos os Cartões </a>
				</li>
			</ul>
		</li>
        <?php
    }
    if (APP_CERTIFICATIONS && $Admin['user_level'] >= LEVEL_WC_CERTIFICATIONS) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'certifications/'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-file-text" href="dashboard.php?wc=certifications/home">Certificações</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'certifications/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=certifications/create">&raquo; Nova Certificação</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'certifications/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=certifications/home">&raquo; Ver Certificações</a>
				</li>
			</ul>

		</li>
        <?php
    }
    if (APP_HELLO && $Admin['user_level'] >= LEVEL_WC_HELLO) {
        $wc_hellobars_alerts = null;
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'hello/'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-bullhorn" title="Hellobar"
			   href="dashboard.php?wc=hello/home">Hellobar <?php
                    echo $wc_hellobars_alerts; ?></a>
		</li>
        <?php
    }

    if (APP_LEADS && $Admin['user_level'] >= LEVEL_WC_LEADS) {
        $wc_leads = null;
        $Read->fullRead('SELECT count(lead_id) as total FROM ' . DB_LEADS . ' WHERE lead_status != 1');
        if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
            $wc_leads .= sprintf("<span class='wc_alert bar_yellow'>%s</span>", $Read->getResult()[0]['total']);
        }
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'leads/'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-users" title="Leads" href="dashboard.php?wc=leads/home">Base de Leads <?php
                    echo $wc_leads; ?></a>
		</li>
        <?php
    }
    if (APP_THANKYOU_PAGES && $Admin['user_level'] >= LEVEL_WC_THANKYOU_PAGES) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'thankyoupages/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-heart" href="dashboard.php?wc=thankyoupages/home">Thank You Pages</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'thankyoupages/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=thankyoupages/create">&raquo; Nova Thank You Page</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'thankyoupages/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=thankyoupages/home">&raquo; Ver Thank You Pages </a>
				</li>
			</ul>

		</li>
        <?php
    }
    if (APP_LANDING_PAGES && $Admin['user_level'] >= LEVEL_WC_LANDING_PAGES) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'landingpages/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-download" href="dashboard.php?wc=landingpages/home">Landing Pages</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'landingpages/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=landingpages/create">&raquo; Nova Landing Pages</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'landingpages/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=landingpages/home">&raquo; Ver Landing Pages </a>
				</li>
			</ul>

		</li>
        <?php
    }
    if (APP_MATERIALS !== 0) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'materiais/'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-book" title="Materiais" href="dashboard.php?wc=materiais/home">Materiais</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'materiais/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Ver Materiais" href="dashboard.php?wc=materiais/home">&raquo; Ver Materials </a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo strstr(
                        (string)$getViewInput,
                        'materiais/categor'
                    ) ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Categorias" href="dashboard.php?wc=materiais/categories">&raquo; Categorias</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'materiais/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Novo Material" href="dashboard.php?wc=materiais/create">&raquo; Novo Material</a>
				</li>
			</ul>
		</li>
        <?php
    }

    if (APP_DEPOSITIONS !== 0) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'depositions/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-man-woman" href="dashboard.php?wc=depositions/home">Depoimentos</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'depositions/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=depositions/create">&raquo; Novo Depoimento</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'depositions/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=depositions/home">&raquo; Depoimentos </a>
				</li>
			</ul>
		</li>
        <?php
    }
    if (APP_PARTNERS !== 0) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'partners/home'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-users" href="dashboard.php?wc=partners/home">Parceiros</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'partners/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=partners/create">&raquo; Novo Parceiro</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'partners/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=partners/home">&raquo; Parceiros </a>
				</li>
			</ul>
		</li>
        <?php
    }

    if (APP_VIDEOS !== 0) {
        ?>
		<li class="dashboard_nav_menu_li <?php
            echo strstr(
                (string)$getViewInput,
                'videos/'
            ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class='icon-youtube' title='Vídeos Youtube' href='dashboard.php?wc=videos/home'>Vídeos Youtube</a>
			<ul class='dashboard_nav_menu_sub'>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'videos/create' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href='dashboard.php?wc=videos/create'>&raquo; Novo Vídeo</a></li>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'videos/home' == $getViewInput ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href='dashboard.php?wc=videos/home'>&raquo; Ativos </a>
				<li class="dashboard_nav_menu_sub_li <?php
                    echo 'videos/end' == $getViewInput ? 'dashboard_nav_menu_active' : '';
                ?>"><a href='dashboard.php?wc=videos/end'>&raquo; Expirados </a>
				</li>
			</ul>
		</li>
        <?php
    }

	/*DORIPEL*/

    if (APP_PRODUCTS_DORIPEL && $_SESSION['userLogin']['user_level'] >= LEVEL_WC_PRODUCTS_DORIPEL) {
        $wc_pdt_alerts = null;
        $Read->fullRead('SELECT count(pdt_id) as total FROM ' . DB_PDT_DORIPEL . ' WHERE pdt_status != 1');
        if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
            $wc_pdt_alerts .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
        }
        ?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'products/') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-bullhorn" title="Produtos"
			   href="dashboard.php?wc=products/home">Produtos <?= $wc_pdt_alerts; ?></a>
			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'products/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Ver Produtos" href="dashboard.php?wc=products/home">&raquo; Ver Produto</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= strstr(
                    $getViewInput,
                    'products/home&opt=outsale'
                ) ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Fora de Estoque ou Inativos" href="dashboard.php?wc=products/home&opt=outsale">&raquo;
						Indisponíveis <?= $wc_pdt_alerts; ?></a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= strstr(
                    $getViewInput,
                    'products/categor'
                ) ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Categorias de Produtos" href="dashboard.php?wc=products/categories">&raquo; Categorias</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= strstr(
                    $getViewInput,
                    'products/bran'
                ) ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Marcas ou Fabricantes" href="dashboard.php?wc=products/brands">&raquo; Fabricantes</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= strstr(
                    $getViewInput,
                    'products/colo'
                ) ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Padrões ou Cores" href="dashboard.php?wc=products/colors">&raquo; Padrões/Cores</a>
				</li>

				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'products/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Novo Produto" href="dashboard.php?wc=products/create">&raquo; Novo Produto</a>
				</li>
			</ul>
		</li>
        <?php
    }
    if (APP_HELLO && $Admin['user_level'] >= LEVEL_WC_HELLO) {
        $wc_hellobars_alerts = null;
        ?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'hello/') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-bullhorn" title="Hellobar"
			   href="dashboard.php?wc=hello/home">Hellobar <?= $wc_hellobars_alerts; ?></a>
		</li>
        <?php
    }
    if (APP_LEADS && $Admin['user_level'] >= LEVEL_WC_LEADS) {
        $wc_leads = null;
        $Read->fullRead('SELECT count(lead_id) as total FROM ' . DB_LEADS . ' WHERE lead_status != 1');
        if ($Read->getResult() && $Read->getResult()[0]['total'] >= 1) {
            $wc_leads .= "<span class='wc_alert bar_yellow'>{$Read->getResult()[0]['total']}</span>";
        }
        ?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'leads/') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-users" title="Leads" href="dashboard.php?wc=leads/home">Base de Leads <?= $wc_leads; ?></a>
		</li>
        <?php
    }
    if (APP_LANDING_PAGES && $Admin['user_level'] >= LEVEL_WC_LANDING_PAGES) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr(
            $getViewInput,
            'landingpages/home'
        ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-insert-template" href="dashboard.php?wc=landingpages/home">Landing Pages</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'landingpages/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=landingpages/create">&raquo; Nova Landing Pages</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'landingpages/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=landingpages/home">&raquo; Ver Landing Pages </a>
				</li>
			</ul>

		</li>
        <?php
    }

    if (APP_THANKYOU_PAGES && $Admin['user_level'] >= LEVEL_WC_THANKYOU_PAGES) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr(
            $getViewInput,
            'thankyoupages/home'
        ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-clipboard" href="dashboard.php?wc=thankyoupages/home">Thank You Pages</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'thankyoupages/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=thankyoupages/create">&raquo; Nova Thank You Page</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'thankyoupages/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=thankyoupages/home">&raquo; Ver Thank You Pages </a>
				</li>
			</ul>

		</li>
        <?php
    }
    if (APP_CTAS && $Admin['user_level'] >= LEVEL_WC_CTAS) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'ctas/home') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-download" href="dashboard.php?wc=ctas/home">Call to actions</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'ctas/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=ctas/create">&raquo; Novo CTA</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'ctas/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=ctas/home">&raquo; Call to actions </a>
				</li>
			</ul>
		</li>
        <?php
    }

    if (APP_ALBUMS) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr(
            $getViewInput,
            'albums/home'
        ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-camera" href="dashboard.php?wc=albums/home">Álbuns de Fotos</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'albums/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=albums/create">&raquo; Novo Álbum</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'albums/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=albums/home">&raquo; Ver Álbuns </a>
				</li>
			</ul>

		</li>
        <?php
    }
    if (APP_DEPOSITIONS) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr(
            $getViewInput,
            'depositions/home'
        ) ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-man-woman" href="dashboard.php?wc=depositions/home">Depoimentos</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'depositions/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=depositions/create">&raquo; Novo Depoimento</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'depositions/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a href="dashboard.php?wc=depositions/home">&raquo; Depoimentos </a>
				</li>
			</ul>
		</li>
        <?php
    }
    if (APP_MATERIALS) {
        ?>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'custom/') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-youtube" title="Vídeos Youtube" href="dashboard.php?wc=videos/home">Vídeos Youtube</a>
		</li>
		<li class="dashboard_nav_menu_li <?= strstr($getViewInput, 'custom/') ? 'dashboard_nav_menu_active' : ''; ?>">
			<a class="icon-book" title="Materiais" href="dashboard.php?wc=materiais/home">Materiais</a>

			<ul class="dashboard_nav_menu_sub">
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'materiais/home' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Ver Materiais" href="dashboard.php?wc=materiais/home">&raquo; Ver Materials </a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= strstr(
                    $getViewInput,
                    'materiais/categor'
                ) ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Categorias" href="dashboard.php?wc=materiais/categories">&raquo; Categorias</a>
				</li>
				<li class="dashboard_nav_menu_sub_li <?= $getViewInput == 'materiais/create' ? 'dashboard_nav_menu_active' : ''; ?>">
					<a title="Novo Material" href="dashboard.php?wc=materiais/create">&raquo; Novo Material</a>
				</li>
			</ul>
		</li>
        <?php
    }
?>
