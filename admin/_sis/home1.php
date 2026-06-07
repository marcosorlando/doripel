<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = 6;
    if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
?>
<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-home">Dashboard</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
		</p>
	</div>
</header>

<div class="dashboard_content">

	<div class="box box25">
		<div class="panel_header success">
            <span>
                <a href="javascript:void(0)" class="btn btn_green icon-loop icon-notext" id="loopDashboard"></a>
            </span>
			<h2 class="icon-earth">ONLINE AGORA:</h2>
		</div>
		<div class="panel dashboard_onlinenow">
            <?php
                $Read->fullRead(
                    'SELECT count(online_id) AS total from ' . DB_VIEWS_ONLINE . ' WHERE online_endview >= NOW()'
                );
                echo "<p class='icon-users wc_useronline'>" . str_pad(
                        (string)$Read->getResult()[0]['total'],
                        4,
                        0,
                        STR_PAD_LEFT
                    ) . '</p>';
            ?>
			<a class="icon-target" href="dashboard.php?wc=onlinenow" title="Ver Usuários Online">ACOMPANHAR USUÁRIOS</a>
			<div class="clear"></div>
		</div>
	</div>

	<div class="box box25 dashboard_stats">
		<div class="panel_header info">
			<h2 class="icon-stats-dots">HOJE:</h2>
		</div>
		<div class="panel">
            <?php
                $Read->exeRead(DB_VIEWS_VIEWS, 'WHERE views_date = date(NOW())');
                if (!$Read->getResult()) {
                    echo "<p class='wc_viewsusers'><b>0000</b><span>Usuários</span></p>";
                    echo "<p class='wc_viewsviews'><b>0000</b><span>Visitas</span></p>";
                    echo "<p class='wc_viewspages'><b>0000</b><span>Páginas</span></p>";
                    echo "<h3 class='wc_viewsstats icon-shuffle'><b>0.00</b> Páginas por Visita</h3>";
                } else {
                    $Views = $Read->getResult()[0];
                    $Stats = number_format($Views['views_pages'] / $Views['views_views'], 2, '.', '');
                    echo "<p class='wc_viewsusers'><b>" . str_pad(
                            (string)$Views['views_users'],
                            4,
                            0,
                            STR_PAD_LEFT
                        ) . '</b><span>Usuários</span></p>';
                    echo "<p class='wc_viewsviews'><b>" . str_pad(
                            (string)$Views['views_views'],
                            4,
                            0,
                            STR_PAD_LEFT
                        ) . '</b><span>Visitas</span></p>';
                    echo "<p class='wc_viewspages'><b>" . str_pad(
                            (string)$Views['views_pages'],
                            4,
                            0,
                            STR_PAD_LEFT
                        ) . '</b><span>Páginas</span></p>';
                    echo sprintf("<h3 class='wc_viewsstats icon-shuffle'><b>%s</b> Páginas por Visita</h3>", $Stats);
                }
            ?>
			<div class="clear"></div>
		</div>
	</div>

	<div class="box box25 dashboard_stats">
		<div class="panel_header alert">
			<h2 class="icon-stats-dots">ESTE MÊS:</h2>
		</div>
		<div class="panel">
            <?php
                $Read->fullRead(
                    'SELECT sum(views_users) AS users, sum(views_views) AS views, sum(views_pages) AS pages FROM ' . DB_VIEWS_VIEWS . ' WHERE year(views_date) = year(NOW()) AND month(views_date) = month(NOW())'
                );
                if (!$Read->getResult()) {
                    echo '<p>0000<span>Usuários</span></p>';
                    echo '<p>0000<span>Visitas</span></p>';
                    echo '<p>0000<span>Páginas</span></p>';
                    echo "<h3 class='icon-shuffle'>0.00 Páginas por Visita</h3>";
                } else {
                    $mViews = $Read->getResult()[0];
                    $Stats = (empty($mViews['pages']) ? '0.00' : number_format(
                        $mViews['pages'] / $mViews['views'],
                        2,
                        '.',
                        ''
                    ));
                    echo '<p>' . str_pad((string)$mViews['users'], 4, 0, STR_PAD_LEFT) . '<span>Usuários</span></p>';
                    echo '<p>' . str_pad((string)$mViews['views'], 4, 0, STR_PAD_LEFT) . '<span>Visitas</span></p>';
                    echo '<p>' . str_pad((string)$mViews['pages'], 4, 0, STR_PAD_LEFT) . '<span>Páginas</span></p>';
                    echo sprintf("<h3 class='icon-shuffle'>%s Páginas por Visita</h3>", $Stats);
                }
            ?>
			<div class="clear"></div>
		</div>
	</div>

	<div class="box box25 dashboard_stats">
		<div class="panel_header warning">
			<h2 class="icon-stats-dots">TOTAL:</h2>
		</div>
		<div class="panel">
            <?php
                $Read->fullRead(
                    'SELECT sum(views_users) AS users, sum(views_views) AS views, sum(views_pages) AS pages FROM ' . DB_VIEWS_VIEWS
                );
                if (!$Read->getResult()) {
                    echo '<p>0000<span>Usuários</span></p>';
                    echo '<p>0000<span>Visitas</span></p>';
                    echo '<p>0000<span>Páginas</span></p>';
                    echo "<h3 class='icon-shuffle'>0.00 Páginas por Visita</h3>";
                } else {
                    $tViews = $Read->getResult()[0];
                    $Stats = (empty($tViews['pages']) ? '0.00' : number_format(
                        $tViews['pages'] / $tViews['views'],
                        2,
                        '.',
                        ''
                    ));
                    echo '<p>' . str_pad((string)$tViews['users'], 4, 0, STR_PAD_LEFT) . '<span>Usuários</span></p>';
                    echo '<p>' . str_pad((string)$tViews['views'], 4, 0, STR_PAD_LEFT) . '<span>Visitas</span></p>';
                    echo '<p>' . str_pad((string)$tViews['pages'], 4, 0, STR_PAD_LEFT) . '<span>Páginas</span></p>';
                    echo sprintf("<h3 class='icon-shuffle'>%s Páginas por Visita</h3>", $Stats);
                }
            ?>
			<div class="clear"></div>
		</div>
	</div>

    <?php
        if (APP_COMMENTS !== 0) { ?>
			<div class="box box50 dashboard_comments">
				<div class="panel_header success">
					<h2 class="icon-bubbles4">COMENTÁRIOS:</h2>
				</div>
				<div class="panel">
                    <?php
                        $Read->exeRead(
                            DB_COMMENTS,
                            'WHERE alias_id IS NULL ORDER BY status DESC, interact DESC LIMIT 5'
                        );
                        if (!$Read->getResult()) {
                            echo Check::erro(
                                '<span>Ainda Não Existem Comentários em Seu Site!</span>',
                                E_USER_NOTICE
                            );
                        } else {
                            foreach ($Read->getResult() as $Comm) {
                                $Read->fullRead(
                                    'SELECT user_id, user_name, user_lastname, user_thumb FROM ' . DB_USERS . ' WHERE user_id = :id',
                                    'id=' . $Comm['user_id']
                                );
                                $UserData = $Read->getResult()[0] ?? null;
                                $UserId = ($UserData['user_id'] ?? 0);
                                $User = trim(
                                    sprintf(
                                        '%s %s',
                                        ($UserData['user_name'] ?? 'Usuário'),
                                        ($UserData['user_lastname'] ?? 'removido')
                                    )
                                );
                                $Photo = (
                                    !empty($UserData['user_thumb']) &&
                                    file_exists('../uploads/' . $UserData['user_thumb'])
                                ) ? sprintf(
                                    '../tim.php?src=uploads/%s&w=100&h=100',
                                    $UserData['user_thumb']
                                ) : '../tim.php?src=admin/_img/no_avatar.jpg&w=100&h=100';

                                $Link = '#';
                                $Title = 'Conteúdo removido';
                                if ($Comm['post_id']) {
                                    $Read->fullRead(
                                        'SELECT post_name, post_title FROM ' . DB_POSTS . ' WHERE post_id = :id',
                                        'id=' . $Comm['post_id']
                                    );
                                    if ($Read->getResult()) {
                                        $Link = 'artigo/' . $Read->getResult()[0]['post_name'];
                                        $Title = $Read->getResult()[0]['post_title'];
                                    }
                                } elseif ($Comm['pdt_id']) {
                                    $Read->fullRead(
                                        'SELECT pdt_name, pdt_title FROM ' . DB_PDT_DORIPEL . ' WHERE pdt_id = :id',
                                        'id=' . $Comm['pdt_id']
                                    );
                                    if ($Read->getResult()) {
                                        $Link = 'produto/' . $Read->getResult()[0]['pdt_name'];
                                        $Title = $Read->getResult()[0]['pdt_title'];
                                    }
                                } elseif ($Comm['page_id']) {
                                    $Read->fullRead(
                                        'SELECT page_name, page_title FROM ' . DB_PAGES . ' WHERE page_id = :id',
                                        'id=' . $Comm['page_id']
                                    );
                                    if ($Read->getResult()) {
                                        $Link = $Read->getResult()[0]['page_name'];
                                        $Title = $Read->getResult()[0]['page_title'];
                                    }
                                }

                                $Created = date('d/m/y H\hi', strtotime((string)$Comm['created']));
                                $Stars = str_repeat("<span class='icon-star-full'></span>", $Comm['rank']);
                                $Status = ($Comm['status'] >= 2 ? 'pending' : null);
                                $CommentRef = "comment{$Comm['id']}";
                                $CommentLinkHtml = ($Link !== '#'
                                    ? "<a target='_blank' title='Ver Comentário' href='" . BASE . "/{$Link}#{$CommentRef}'>{$Title}</a>"
                                    : "<span class='na'>{$Title}</span>");

                                echo "<article class='{$Status}'>
                    <div class='thumb'>
                        <img alt='{$User}' title='{$User}' src='{$Photo}'/>
                    </div>
                    <div class='comment'>
                        <h1><a title='Perfil do Usuário' href='dashboard.php?wc=users/create&id={$UserId}'>{$User}</a> - {$Created}</h1>
                        <p>em {$CommentLinkHtml} - {$Stars}</p>
                    </div>
                </article>
            ";
                            }
                        }
                    ?>
					<div class="clear"></div>
				</div>
			</div>
            <?php
        } elseif (APP_POSTS !== 0) {
            ?>
			<div class="box box50 dashboard_mostviews">
				<div class="panel_header success">
					<h2 class="icon-eye-plus">ÚLTIMOS POSTS:</h2>
				</div>
				<div class="panel">
                    <?php
                        $Read->exeRead(DB_POSTS, 'WHERE post_status = 1 ORDER BY post_date DESC LIMIT 5');
                        if (!$Read->getResult()) {
                            echo Check::erro(
                                '<span>Ainda não existem posts cadastrados!</span>',
                                E_USER_NOTICE
                            );
                        } else {
                            foreach ($Read->getResult() as $Post) {
                                echo "
                        <article>
                            <img src='" . BASE . sprintf(
                                        '/tim.php?src=uploads/%s&w=',
                                        $Post['post_cover']
                                    ) . IMAGE_W / 6 . '&h=' . IMAGE_H / 6 . "' title='{$Post['post_title']}' alt='{$Post['post_title']}'/>
                            <div class='info'>
                                <span>{$Post['post_views']} visitas</span>
                                <h1><a href='dashboard.php?wc=posts/create&id={$Post['post_id']}' title='Ver Post'>{$Post['post_title']}</a></h1>
                            </div>
                         </article>
                    ";
                            }
                        }
                    ?>
					<div class="clear"></div>
				</div>
			</div>
            <?php
        }
        if (APP_POSTS !== 0) {
            ?>
			<div class="box box50 dashboard_mostviews">
				<div class="panel_header info">
					<h2 class="icon-eye-plus">POSTS MAIS VISTOS:</h2>
				</div>
				<div class="panel">
                    <?php
                        $Read->exeRead(
                            DB_POSTS,
                            'WHERE post_status = 1 ORDER BY post_views DESC, post_date DESC LIMIT 5'
                        );
                        if (!$Read->getResult()) {
                            echo Check::erro(
                                '<span>Ainda não existem posts cadastrados!</span>',
                                E_USER_NOTICE
                            );
                        } else {
                            foreach ($Read->getResult() as $Post) {
                                echo "
                        <article>
                            <img src='" . BASE . sprintf(
                                        '/tim.php?src=uploads/%s&w=',
                                        $Post['post_cover']
                                    ) . IMAGE_W / 6 . '&h=' . IMAGE_H / 6 . "' title='{$Post['post_title']}' alt='{$Post['post_title']}'/>
                            <div class='info'>
                                <span>{$Post['post_views']} visitas</span>
                                <h1><a href='dashboard.php?wc=posts/create&id={$Post['post_id']}' title='Ver Post'>{$Post['post_title']}</a></h1>
                            </div>
                         </article>
                    ";
                            }
                        }
                    ?>
					<div class="clear"></div>
				</div>
			</div>
            <?php
        }

        if (APP_SEARCH && APP_PRODUCTS_DORIPEL) {
            ?>
			<div class="box box100">
				<div class="panel_header info">
					<h2 class="icon-search">ÚLTIMAS PESQUISAS POR PRODUTOS (30 DIAS):</h2>
				</div>
				<div id="panel_search" class="panel dashboard_search">
                    <?php
                        $Read->exeRead(
                            DB_SEARCH,
                            "WHERE search_commit >= date(NOW() - INTERVAL 30 DAY) AND search_publish IS NULL AND search_origin = 'PROD' ORDER BY search_commit DESC, search_count DESC LIMIT 5"
                        );
                        if (!$Read->getResult()) {
                            echo Check::erro(
                                '<span>Seus usuários ainda não pesquisaram por produtos em sua Loja. Assim que isso acontecer você poderá receber dicas de conteúdo pelas pesquisas realizadas!</span>',
                                E_USER_NOTICE
                            );
                            echo "<div class='clear'></div>";
                        } else {
                            foreach ($Read->getResult() as $Search) {
                                extract($Search);
                                $Read->fullRead(
                                    'SELECT pdt_id FROM ' . DB_PDT_DORIPEL . " WHERE pdt_status = 1 AND (pdt_title LIKE '%' :s '%' OR pdt_subtitle LIKE '%' :s '%' OR pdt_tags LIKE '%' :s '%')",
                                    's=' . $search_key
                                );
                                $ResultPdts = $Read->getRowCount();
                                echo "
                            <article id='{$search_id}'>
                               <h1 class='icon-search'><a href='dashboard.php?wc=products/home&opt=&s=" . urlencode(
                                        (string)$search_key
                                    ) . "' title='Ver resultados'>{$search_key}</a></h1>
                               <p>DIA " . date('d/m/Y H\hi', strtotime((string)$search_date)) . '</p>
                               <p>' . str_pad((string)$search_count, 4, 0, STR_PAD_LEFT) . ' VEZES</p>
                               <p>' . str_pad((string)$ResultPdts, 4, 0, STR_PAD_LEFT) . " RESULTADOS</p>
                               <p>
                                    <button class='btn btn_green icon-notext icon-checkmark wc_tooltip j_wc_action' data-callback='Search' data-callback-action='publish' data-value='{$search_id}'><span class='wc_tooltip_balloon'>Publicar</span></button>
                                    <button class='btn btn_red icon-notext icon-cross wc_tooltip j_wc_action' data-callback='Search' data-callback-action='delete' data-value='{$search_id}'><span class='wc_tooltip_balloon'>Deletar</span></button>
                               </p>
                            </article>
                        ";
                            }
                        }
                    ?>
					<a class="dashboard_searchnowlink" href="dashboard.php?wc=searchnow-products" title="Ver Mais">MAIS
						PESQUISAS!</a>
					<div class="clear"></div>
				</div>
			</div>


            <?php
        }
        if (APP_SEARCH && APP_POSTS) {
            ?>
			<div class="box box100">
				<div class="panel_header success">
					<h2 class="icon-search">ÚLTIMAS PESQUISAS DO BLOG (30 DIAS):</h2>
				</div>
				<div class="panel dashboard_search">
                    <?php
                        $Read->exeRead(
                            DB_SEARCH,
                            "WHERE search_commit >= date(NOW() - INTERVAL 30 DAY) AND search_publish IS NULL AND search_origin = 'POST' ORDER BY search_commit DESC, search_count DESC LIMIT 5"
                        );
                        if (!$Read->getResult()) {
                            echo Check::erro(
                                '<span>Seus usuários ainda não pesquisaram em seu Blog. Assim que isso acontecer você poderá receber dicas de conteúdo pelas pesquisas realizadas!</span>',
                                E_USER_NOTICE
                            );
                            echo "<div class='clear'></div>";
                        } else {
                            foreach ($Read->getResult() as $Search) {
                                extract($Search);
                                $Read->fullRead(
                                    'SELECT post_id FROM ' . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%' OR post_tags LIKE '%' :s '%' OR post_month = :s OR post_author = :s)",
                                    's=' . $search_key
                                );
                                $ResultPosts = $Read->getRowCount();

                                echo "
                            <article id='{$search_id}'>
                               <h1 class='icon-search'><a href='dashboard.php?wc=posts/home&s=" . urlencode(
                                        (string)$search_key
                                    ) . "' title='Ver resultados'>" . ('month' == $search_parse
                                        ? '<b>Mês: </b>' . getWcMonths($search_key) : $search_key) . '</a></h1>


                               <p>DIA ' . date('d/m/Y H\hi', strtotime((string)$search_date)) . '</p>
                               <p>' . str_pad((string)$search_count, 4, 0, STR_PAD_LEFT) . ' VEZES</p>
                               <p>' . str_pad((string)$ResultPosts, 4, 0, STR_PAD_LEFT) . " RESULTADOS</p>
                               <p>
                                    <button class='btn btn_green icon-notext icon-checkmark wc_tooltip j_wc_action' data-callback='Search' data-callback-action='publish' data-value='{$search_id}'><span class='wc_tooltip_balloon'>Publicar</span></button>
                                    <button class='btn btn_red icon-notext icon-cross wc_tooltip j_wc_action' data-callback='Search' data-callback-action='delete' data-value='{$search_id}'><span class='wc_tooltip_balloon'>Deletar</span></button>
                               </p>
                            </article>
                        ";
                            }
                        }
                    ?>
					<a class="dashboard_searchnowlink" href="dashboard.php?wc=searchnow" title="Ver Mais">MAIS
						PESQUISAS!</a>
					<div class="clear"></div>
				</div>
			</div>

            <?php
        } ?>
</div>

<script>
    //ICON REFRESH IN DASHBOARD
    $('#loopDashboard').click(function () {
        Dashboard();
    });

    //DASHBOARD REALTIME
    setInterval(function () {
        Dashboard();
    }, 10000);
</script>
