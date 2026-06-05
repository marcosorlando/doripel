<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Helpers\DateHelper;

?>

<aside class="col-lg-3 col-lg-offset-1 col-md-4  sidebar">
	<!-- SEARCH -->
	<div class="form-group search has-feedback wow fadeInUp" data-wow-duration="2s">
		<form class="search_form" name="search" action="" method="post" enctype="multipart/form-data">
			<input type="text" class="form-control" name="s" placeholder="DIGITE O QUE PROCURA + ENTER" required>
			<button type="submit" class="icon icon-Search"></button>
		</form>
	</div>
	<!--<a href="http://materiais.zen.ppg.br/inbound-marketing-uma-introducao" target="_blank">
		<img class='img-responsive'
		     src="<?php
        /*= BASE; */ ?>/tim.php?src=images/cta/cta_introducao-ao-inbound-marketing.png&w=300&h=300"
		     alt="" title=""/>
	</a>-->
	<div class="divider">
		<br>
	</div>
	<div class="sidebar-box wow fadeInUp">
		<h5>Categorias</h5>
		<div class="sidebar-box-content">
            <?php
                $Read ??= new Read();
                $Read->exeRead(DB_CATEGORIES, "WHERE category_parent IS NULL ORDER BY category_title ASC");
                if (!$Read->getResult()) {
                    echo Check::erro("Ainda não existem sessões cadastradas!", E_USER_NOTICE);
                } else {
                    echo "<ul class=\"category-list\">";
                    foreach ($Read->getResult() as $Ses) {
                        echo "<li style='text-transform: uppercase;'><a title='{$Ses['category_title']}' href='" . BASE . "/artigos/{$Ses['category_name']}'>{$Ses['category_title']}</a></li>";
                        $Read->fullRead(
                            "SELECT category_title, category_name FROM " . DB_CATEGORIES . " WHERE category_parent = :pr ORDER BY category_title ASC",
                            "pr={$Ses['category_id']}"
                        );
                        if ($Read->getResult()) {
                            foreach ($Read->getResult() as $Cat) {
                                echo "<li><a title='{$Ses['category_title']}' href='" . BASE . "/artigos/{$Cat['category_name']}'><i class='icon icon-Goto'></i> {$Cat['category_title']}</a></li>";
                            }
                        }
                    }
                    echo "</ul>";
                }
            ?>
		</div>
	</div>

	<div class="sidebar-box wow fadeInUp">
		<h5>Posts Recentes</h5>
		<div class="sidebar-box-content">
			<div class="recent-posts">
                <?php
                    $Read->exeRead(
                        DB_POSTS,
                        "WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_date DESC LIMIT 5"
                    );
                    if (!$Read->getResult()) {
                        echo Check::ajaxErro(
                            "Ainda não existem posts cadastrados. Favor volte mais tarde.",
                            E_USER_NOTICE
                        );
                    } else {
                        foreach ($Read->getResult() as $Post) {
                            ?>
							<article class="post-item">
								<figure class="image">
									<a title="Ler mais sobre <?= $Post['post_title']; ?>"
									   href="<?= BASE; ?>/artigo/<?= $Post['post_name']; ?>">
										<img title="<?= $Post['post_title']; ?>" alt="<?= $Post['post_title']; ?>"
										     src="<?= BASE; ?>/tim.php?src=uploads/<?= $Post['post_cover']; ?>&w=<?= IMAGE_W / 2; ?>&h=<?= IMAGE_H / 2; ?>"/>
									</a>
								</figure>
								<header>
									<h5>
										<a title="Ler mais sobre <?= $Post['post_title']; ?>"
										   href="<?= BASE; ?>/artigo/<?= $Post['post_name']; ?>"><?= $Post['post_title']; ?></a>
									</h5>
									<div class="meta-item">
										<span class="icon icon-Agenda"></span>
										<time datetime="<?= date('Y-m-d', strtotime($Post['post_date'])); ?>"
										      pubdate="pubdate"><?= DateHelper::human($Post['post_date']); ?></time>
									</div>
								</header>
							</article>
                            <?php
                        }
                    }
                ?>

			</div>
		</div>
	</div>

	<div class="sidebar-box wow fadeInUp">
		<h5>Instagram</h5>
		<div class="instagram-follow-api_"></div>

		<div class="instagram-follow-api" id="instafeed-container">
			<ul id="instaFeed-aside"></ul>
		</div>
	</div>

	<div class="sidebar-box wow fadeInUp">
		<h5>Palavras Chave</h5>
		<ul class="list-tags">
            <?php
                $URL[1] = $URL[1] ?? '';

                $Read->exeRead(DB_POSTS, "WHERE post_name = :nm", "nm={$URL[1]}");
                if (!$Read->getResult()) {
                    $tags = explode(
                        ',',
                        'Marketing Digital, Inbound Marketing, E-mail Marketing, e-commerce, landing page, google adwords, facebook, mídias sociais, consultoria, RD Station'
                    );
                    foreach ($tags as $key => $value) {
                        ?>
						<li>
							<a title="Pesquisar por " href="<?= BASE; ?>/pesquisa/<?= $value ?>">
								<h6><?= $value ?></h6>
							</a>
						</li>
                        <?php
                    }
                } else {
                    extract($Read->getResult()[0]);
                    $tags = $post_tags ? explode(',', $post_tags) : [];

                    foreach ($tags as $key => $value) {
                        ?>

						<li>
							<a title="Pesquisar por " href="<?= BASE; ?>/pesquisa/<?= $value ?>">
								<h6><?= $value ?></h6>
							</a>
						</li>

                        <?php
                    }
                }
            ?>
		</ul>
	</div>
</aside>
