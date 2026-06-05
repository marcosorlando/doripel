<?php

    use App\Helpers\Check;
    use App\Helpers\DateHelper;

    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
    date_default_timezone_set('America/Sao_Paulo');
?>

<aside class="col-md-3 col-sm-12 col-xs-12 pull-right">
	<div class="display-inline-block width-100 margin-45px-bottom xs-margin-25px-bottom">
		<form name="search" action="" method="post" enctype="multipart/form-data">
			<div class="position-relative">
				<input type="text" name="s"
				       class="bg-transparent text-small no-margin border-color-extra-light-gray medium-input pull-left"
				       placeholder="Pesquisar artigos..." autocomplete="off">
				<button class="bg-transparent btn position-absolute right-0 top-1"><i
							class="fa fa-search no-margin-left"></i></button>
			</div>
		</form>
	</div>
	<!--BANNER CTA-->
    <?php
        //require REQUIRE_PATH.  '/inc/banner-cta.php';?>
	<div class="margin-45px-bottom xs-margin-25px-bottom">
		<div class="text-extra-dark-gray margin-20px-bottom alt-font text-uppercase text-small font-weight-600 aside-title">
			<span>Sobre a empresa</span></div>
		<a href="about-me.html"><img src="<?= INCLUDE_PATH; ?>/images/doripel-fabrica-aerea-2.jpg"
		                             alt="Móveis Doripel Indústria Moveleira - Vista Aérea" class="margin-25px-bottom"/></a>
		<p class="margin-20px-bottom text-small">A Doripel conta com processos de fabricação modernos que resulta em
			produtos de alto padrão e qualidade e uma ótima relação custo/benefício.</p>
		<a class="btn btn-very-small btn-dark-gray text-uppercase" href="<?= BASE; ?>/sobre">Sobre a Doripel</a>
	</div>
	<div class="margin-50px-bottom">
		<div class="text-extra-dark-gray margin-20px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
			<span>Siga-nos</span></div>
		<div class="social-icon-style-1 text-center">
			<ul class="extra-small-icon">
                <?= (!empty(SITE_SOCIAL_FB_PAGE) ? "<li><a class='facebook' href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "' target='_blank'><i class='fa fa-facebook' aria-hidden='true'></i></a></li>" : null); ?>
                <?= (!empty(SITE_SOCIAL_INSTAGRAM) ? " <li><a class='instagram' href='https://instagram.com/" . SITE_SOCIAL_INSTAGRAM . "' target='_blank'><i class='fa fa-instagram no-margin-right' aria-hidden='true'></i></a>" : null); ?>
                <?= (!empty(SITE_SOCIAL_LINKEDIN) ? "<li><a class='linkedin' href='https://www.linkedin.com/in/" . SITE_SOCIAL_LINKEDIN . "' target='_blank'><i class='fa fa-linkedin'></i></a></li>" : null); ?>
                <?= (!empty(SITE_SOCIAL_YOUTUBE) ? "<li><a class='youtube ' href='https://www.youtube.com/channel/" . SITE_SOCIAL_YOUTUBE . "' target='_blank'><i class='fa fa-youtube'></i></a></li>" : null); ?>
			</ul>
		</div>
	</div>
	<div class="margin-45px-bottom xs-margin-25px-bottom">
		<div class="text-extra-dark-gray margin-20px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
			<span>Categorias</span></div>

        <?php
            $Read->exeRead(
                DB_CATEGORIES,
                "WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM " . DB_POSTS . " WHERE post_status <> 0 AND post_date <= NOW()) ORDER BY category_title ASC"
            );
            if (!$Read->getResult()) {
                echo Check::erro("Ainda não existem sessões cadastradas!", E_USER_NOTICE);
            } else {
                echo "<ul class='list-style-6 margin-50px-bottom text-small'>";
                foreach ($Read->getResult() as $Ses) {
                    echo "<li><a title='artigos/{$Ses['category_name']}' href='" . BASE . "/artigos/{$Ses['category_name']}'>&raquo; {$Ses['category_title']}</a></li>";
                    $Read->exeRead(
                        DB_CATEGORIES,
                        "WHERE category_parent = :pr AND category_id IN(SELECT post_category_parent FROM " . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW()) ORDER BY category_title ASC",
                        "pr={$Ses['category_id']}"
                    );
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $Cat) {
                            echo "<li><a title='artigos/{$Cat['category_name']}' href='" . BASE . "/artigos/{$Cat['category_name']}'>&raquo;&raquo; {$Cat['category_title']}</a></li>";
                        }
                    }
                }
                echo "</ul>";
            }
        ?>
	</div>

	<div class="margin-45px-bottom xs-margin-25px-bottom">
		<div class="text-extra-dark-gray margin-25px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
			<span>Mais lidos</span></div>
		<ul class="latest-post position-relative">
            <?php
                $Read->exeRead(
                    DB_POSTS,
                    "WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_views DESC, post_date DESC LIMIT 5"
                );
                if (!$Read->getResult()) {
                    echo Check::erro("Ainda Não existe posts cadastrados. Favor volte mais tarde :)", E_USER_NOTICE);
                } else {
                    foreach ($Read->getResult() as $Post) {
                        ?>
						<li>
							<figure>
								<a title="Ler mais sobre <?= $Post['post_title']; ?>"
								   href="<?= BASE; ?>/artigo/<?= $Post['post_name']; ?>"><img
											title="<?= $Post['post_title']; ?>" alt="<?= $Post['post_title']; ?>"
											src="<?= BASE; ?>/tim.php?src=uploads/<?= $Post['post_cover']; ?>&w=<?= IMAGE_W / 2; ?>&h=<?= IMAGE_H / 2; ?>"/></a>
							</figure>
							<div class="display-table-cell vertical-align-top text-small"><a
										title="Ler mais sobre <?= $Post['post_title']; ?>"
										href="<?= BASE; ?>/artigo/<?= $Post['post_name']; ?>"
										class="text-extra-dark-gray"><span
											class="display-inline-block margin-5px-bottom"><?= $Post['post_title']; ?></span></a>
								<span class="clearfix text-medium-gray text-small"><time
											datetime="<?= DateHelper::iso($Post['post_date']); ?>"
											pubdate="pubdate"><?= DateHelper::human(
                                            $Post['post_date']
                                        ); ?></time></span>
							</div>
						</li>
                        <?php
                    }
                }
            ?>
		</ul>
	</div>

	<div class="margin-45px-bottom xs-margin-25px-bottom">
		<div class="text-extra-dark-gray margin-25px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
			<span>Arquivo</span></div>
		<ul class="list-style-6 margin-20px-bottom text-small">

            <?php
                $Read->fullRead(
                    "SELECT DISTINCT post_month FROM " . DB_POSTS . " WHERE post_status = :st AND post_date <= NOW() ORDER BY post_month ASC LIMIT 12",
                    "st=1"
                );

                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $MesAno) {
                        $Pesquisa = BASE . '/pesquisa/' . $MesAno['post_month'];

                        $Print = "<li><a href='{$Pesquisa}'>" . Check::getWcMonths($MesAno['post_month']) . "</a></li>";

                        echo $Print;
                    }
                }
            ?>
		</ul>
	</div>

	<div class="margin-45px-bottom xs-margin-25px-bottom">
		<div class="text-extra-dark-gray margin-25px-bottom alt-font text-uppercase font-weight-600 text-small aside-title">
			<span>Newsletter</span></div>
		<div class="display-inline-block width-100">
            <?php
                $CAPTION = 'news1';
                require REQUIRE_PATH . '/inc/activeform.php';
            ?>
		</div>
	</div>

    <?php
        require REQUIRE_PATH . "/inc/banner-cta.php";
    ?>
</aside>
