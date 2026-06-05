<?php

use App\Conn\Read;
use App\Models\Pager;
use App\Helpers\Check;

$Read = new Read();
$URL[1] = $URL[1] ?? '';
$Read->exeRead(DB_CATEGORIES, "WHERE category_name = :nm", "nm={$URL[1]}");

if (!$Read->getResult()) {
    require REQUIRE_PATH . '/404.php';
    return;
} else {
    extract($Read->getResult()[0]);
}
?>
<header class='top-header blog-header with-bottom-effect transparent-effect dark'>
	<div class='bottom-effect'></div>
	<div class='header-container'>
		<div class='header-title'>
			<div class='header-icon'><span class='icon icon-Layers'></span></div>
			<h2 class='title'><a
						href="<?= BASE; ?>/artigos/<?= $category_name; ?>"
						title="Ver mais: <?= $category_title; ?> em <?= SITE_NAME; ?>!"><?= $category_title; ?></a></h2>
			<em class="text-orange">boa leitura</em>
		</div>
	</div>
	<div class='breadcrumbs'>
		<div class='content'>
			<p><a href='<?= BASE; ?>' title='<?= SITE_NAME; ?>'><?= SITE_NAME; ?></a> <i
						class="icon icon-Arrow"> </i> <?= $category_title; ?></p>
			<div class="clear"></div>
		</div>
	</div>
</header>
<!-- ========================== -->
<!-- BLOG - CONTENT -->
<!-- ========================== -->
<section class="blog-content-section">

	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-8 left-column">

                <?php
                $Page = (!empty($URL[2]) ? $URL[2] : 1);
                $Pager = new Pager(BASE . "/artigos/{$category_name}/", "<", ">", 5);
                $Pager->exePager($Page, 10);

                $Read->fullRead(
                    "SELECT p.post_title, p.post_name, p.post_tags,  p.post_subtitle, p.post_cover, p.post_video, p.post_date, p.post_views, p.post_time, u.user_name, u.user_lastname, u.user_thumb, c.category_title FROM " . DB_POSTS . " as p INNER JOIN " . DB_USERS . " as u ON (p.post_author = u.user_id)" . " INNER JOIN " . DB_CATEGORIES . " as c ON (p.post_category = c.category_id) WHERE p.post_status = 1 AND p.post_date <= NOW() AND (p.post_category = :ct OR FIND_IN_SET(:ct, p.post_category_parent)) ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
                    "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$category_id}"
                );

                if (!$Read->getResult()) {
                    $Pager->returnPage();
                    echo Check::erro("Ainda não existem posts cadastrados. Favor volte mais tarde.", E_USER_NOTICE);
                } else {
                    foreach ($Read->getResult() as $Post) {
                        extract($Post);
                        require REQUIRE_PATH . '/inc/postindex.php';
                    }
                }
                ?>
				<!--PAGINATOR-->
				<div class="row wrap-pagination wow fadeInUp">
					<div class="col-md-12">
                        <?php
                        $Pager->exePaginator(
                            DB_POSTS,
                            "WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent))",
                            "ct={$category_id}"
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
