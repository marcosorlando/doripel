<?php


    use App\Conn\Read;

    $Read ??= new Read();

    $Read->exeRead(
        DB_PAGES,
        'WHERE page_status = :st AND  page_name = :nm',
        'st=1&nm=' . ($URL[0] ?? '')
    );
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }

    $activeCategorySlug = trim((string)(filter_input(INPUT_GET, 'categoria', FILTER_DEFAULT) ?? ''));
    $activeCategoryTitle = null;

    if ('' !== $activeCategorySlug) {
        $Read->fullRead(
            'SELECT title FROM ' . DB_PORTFOLIO_CATEGORIES . ' WHERE name = :nm LIMIT 1',
            'nm=' . $activeCategorySlug
        );

        if ($Read->getResult()) {
            $activeCategoryTitle = $Read->getResult()[0]['title'];
        } else {
            $activeCategorySlug = '';
        }
    }
?>
<!-- ========================== -->
<!-- PORTFOLIO - HEADER -->
<!-- ========================== -->
<section class="top-header portfolio-header with-bottom-effect transparent-effect dark dark-strong">
	<div class="bottom-effect"></div>
	<div class="header-container">
		<div class="header-title">
			<div class="header-icon"><span class="icon icon-Wheelbarrow"></span></div>
			<h1 class="title">Trampos</h1>
			<em>Veja a capacidade criativa do nosso time</em>
		</div>
	</div>
</section>

<section class="portfolio-list-section">
	<div class="container">
		<div class="section-heading">
			<h1 class="section-subtitle">
                <?php
                    if ('' !== $activeCategorySlug && $activeCategoryTitle) {
                        echo 'Projetos da categoria: ' . $activeCategoryTitle;
                    } else {
                        echo 'Últimos projetos realizados pela equipe ZEN';
                    }
                ?>
			</h1>
			<div class="design-arrow"></div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="list-works clearfix">

                <?php
                    if ('' !== $activeCategorySlug) {
                        $Read->fullRead(
                            "SELECT p.*, c.title FROM " . DB_PORTFOLIO . " p INNER JOIN " .
                            DB_PORTFOLIO_CATEGORIES . " c ON (p.category = c.id) 
                            WHERE status = :st AND c.slug = :cat",
                            "st=1&cat={$activeCategorySlug}"
                        );
                    } else {
                        $Read->fullRead(
                            "SELECT p.*, c.title FROM " . DB_PORTFOLIO . " p INNER JOIN " .
                            DB_PORTFOLIO_CATEGORIES . " c ON (p.category = c.id) 
                            WHERE status = :st",
                            "st=1"
                        );
                    }

                    if (!$Read->getResult()) {
                        echo "<div class=\"trigger trigger_info\">Ainda não existem projetos cadastrados. Favor volte mais tarde!<span class=\"ajax_close\"></span></div>";
                    } else {
                        foreach ($Read->getResult() as $Trampos) {
                            extract($Trampos);
                            ?>
							<article class="col-md-4 col-sm-4 col-xs-6">
								<div class="portfolio-item">
									<div class="portfolio-image">
										<a href="<?= BASE; ?>/trampo/<?= $slug; ?>" title="<?= $title; ?>">
											<img src="<?= BASE; ?>/uploads/portfolio/<?= $img_350x350; ?>"
											     alt="<?= $title; ?>">
										</a>
										<div class="portfolio-item-body">
											<h2 class="name"><?= $title ?></h2>
											<div class="under-name"><?= $title ?></div>
										</div>
									</div>
								</div>
							</article>

                            <?php
                        }
                    }
                ?>

			</div>
		</div>
	</div>
</section>
