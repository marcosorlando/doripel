<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Models\Pager;
    use App\Conn\Update;
    use App\Helpers\Check;

    $URL[1] = $URL[1] ?? '';

    $Search = urldecode($URL[1]);
    $SearchPage = urlencode(trim($Search));

    $Read ??= new Read();

    if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'])) {
        $Read->fullRead(
            "SELECT search_id, search_count FROM " . DB_SEARCH . " WHERE search_key = :key",
            "key={$Search}"
        );

        if ($Read->getResult()) {
            $Update = new Update;
            $DataSearch = ['search_count' => $Read->getResult()[0]['search_count'] + 1];
            $Update->exeUpdate(
                DB_SEARCH,
                $DataSearch,
                "WHERE search_id = :id",
                "id={$Read->getResult()[0]['search_id']}"
            );
        } else {
            $Create ??= new Create();
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

<header class='top-header blog-header with-bottom-effect transparent-effect dark'>
	<div class='bottom-effect'></div>
	<div class='header-container'>
		<div class='header-title'>
			<div class='header-icon'><span class='icon icon-Search'></span></div>
			<p class='title'>
				<a href="<?= BASE; ?>"
				   title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a>/ Pesquisa por

			</p>
			<em class="text-orange"><?= $Search; ?></em>
		</div>
	</div>
</header>
<!-- ========================== -->
<!-- BLOG - CONTENT -->
<!-- ========================== -->
<section class="blog-content-section">

	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-8 left-column row">


                <?php
                    $Page = (!empty($URL[2]) ? $URL[2] : 1);
                    $Pager = new Pager(BASE . "/pesquisa/{$SearchPage}/", '<<', '>>', 5);
                    $Pager->exePager($Page, 12);
                    $Read->fullRead(
                        "SELECT p.*, c.category_title, u.user_name, u.user_lastname, u.user_thumb FROM " . DB_POSTS .
                        " as p INNER JOIN " . DB_CATEGORIES . " as c ON(p .post_category = category_id) 
                        INNER JOIN " . DB_USERS . " as u ON (p.post_author = u.user_id)                    
                    WHERE p.post_status = :st
                    AND p.post_date <= NOW()
                    AND (p.post_title LIKE '%' :s '%'
                    OR p.post_subtitle LIKE '%' :s '%')
                    ORDER BY p.post_date DESC
                    LIMIT :limit
                    OFFSET :offset",
                        "st=1&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}"
                    );
                    if (!$Read->getResult()) {
                        $Pager->returnPage();
                        echo Check::ajaxErro(
                            'Ainda não existem posts cadastrados. Favor volte mais tarde!',
                            E_USER_NOTICE
                        );
                    } else {
                        foreach ($Read->getResult() as $Post) {
                            extract($Post);
                            $cols = 'col-md-6';
                            require REQUIRE_PATH . '/inc/postlist.php';
                        }
                    }
                ?>

				<!--PAGINATOR-->
				<div class="row wrap-pagination wow fadeInUp">
					<div class="col-md-12">
                        <?php
                            $Pager->exeFullPaginator(
                                'SELECT p.*, c.category_title, u.user_name, u.user_lastname, u.user_thumb FROM ' . DB_POSTS .
                                ' as p INNER JOIN ' . DB_CATEGORIES . ' as c ON(p
    .post_category = category_id) 
                    INNER JOIN ' . DB_USERS . " as u ON (p.post_author = u.user_id)                    
                    WHERE p.post_status = :st
                    AND p.post_date <= NOW()
                    AND (p.post_title LIKE '%' :s '%'
                    OR p.post_subtitle LIKE '%' :s '%')
                    ORDER BY p.post_date DESC
                    LIMIT :limit
                    OFFSET :offset",
                                "st=1&limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}"
                            );
                            echo $Pager->getPaginator();
                        ?>
					</div>
				</div><!--END PAGINATOR-->
			</div>
            <?php
                require REQUIRE_PATH . '/inc/sidebar.php'; ?>
		</div>
	</div>
</section>
<!-- ========================== -->
