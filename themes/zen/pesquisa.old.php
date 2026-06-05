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
    $Read->fullRead("SELECT search_id, search_count FROM " . DB_SEARCH . " WHERE search_key = :key", "key={$Search}");

    if ($Read->getResult()) {
        $Update = new Update;
        $DataSearch = ['search_count' => $Read->getResult()[0]['search_count'] + 1];
        $Update->exeUpdate(DB_SEARCH, $DataSearch, "WHERE search_id = :id", "id={$Read->getResult()[0]['search_id']}");
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
<div class="top_conversion breadcrumbs">
	<div class="content">
		<p><a href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><?= SITE_NAME; ?></a> / Pesquisa por <?= $Search; ?></p>
		<div class="clear"></div>
	</div>
</div>

<div class="container main_content wc_blog_content">
	<div class="content">
		<div class="main_blog">
            <?php
            $Page = (!empty($URL[2]) ? $URL[2] : 1);
            $Pager = new Pager(BASE . "/pesquisa/{$SearchPage}/", "<<", ">>", 5);
            $Pager->exePager($Page, 12);
            $Read->exeRead(
                DB_POSTS,
                "WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%') ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
                "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&s={$Search}"
            );
            if (!$Read->getResult()) {
                $Pager->returnPage();
                echo Check::ajaxErro("Ainda não existem posts cadastrados. Favor volte mais tarde!", E_USER_NOTICE);
            } else {
                foreach ($Read->getResult() as $Post) {
                    extract($Post);
                    $BOX = 1;
                    require REQUIRE_PATH . '/inc/post.php';
                }
            }

            $Pager->exePaginator(
                DB_POSTS,
                "WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%')",
                "s={$Search}"
            );
            echo $Pager->getPaginator();
            ?>
		</div><?php
        require REQUIRE_PATH . '/inc/sidebar.php'; ?>
		<div class="clear"></div>
	</div>
</div>
