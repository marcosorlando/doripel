<?php

    if (!$Read) {
        $Read = new Read;
    }
    $Read->exeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>
<!-- ========================== -->
<!-- MATERIAIS - HEADER -->
<!-- ========================== -->
<section class="top-header portfolio-header with-bottom-effect transparent-effect dark dark-strong">
	<div class="bottom-effect"></div>
	<div class="header-container">
		<div class="header-title">
			<div class="header-icon"><span class="icon icon-Book"></span></div>
			<h1 class="title">BIBLIOTECA DO MARKETING DIGITAL</h1>
			<em>e-books, webinars, whitepapers, infográficos, kits, planilhas</em>
		</div>
	</div><!--container-->
</section>

<section class="portfolio-list-section">
	<div class="container">
		<div class="section-heading">
			<h2 class="section-subtitle">Materiais ricos sobre <b>Marketing Digital</b> para você baixar!</h2>
			<div class="design-arrow"></div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="list-works clearfix">
                <?php
                    $Read->exeRead(DB_MATERIAIS, "WHERE mat_status = :st ORDER BY mat_date", "st=1");
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $Mat) {
                            extract($Mat);
                            ?>
							<a href="<?= $mat_link; ?>" title="Clique para Salvar" target="_blank">
								<div class="col-md-4 col-sm-6 col-xs-12" id="<?= $mat_name; ?>">
									<div class="portfolio-item">
										<div class="portfolio-image">
											<img src="<?= BASE; ?>/uploads/<?= $mat_cover; ?>" alt="<?= $mat_title; ?>">
											<div class="portfolio-item-body">
												<h4 class="name"><?= $mat_title; ?></h4>
												<p class="under-name"><?= getWcMatFormato($mat_type); ?></p>
											</div>
										</div>
									</div>
								</div>
							</a>
                            <?php
                        }
                    }
                ?>

			</div>
		</div>
	</div>
</section>


<?php
    if (APP_COMMENTS && COMMENT_ON_PAGES) { ?>
		<div class="container" style="background: #fff; padding: 20px 0;">
			<div class="content">
                <?php
                    $CommentKey = $page_id;
                    $CommentType = 'page';
                    require 'assets/widgets/comments/comments.php';
                ?>
				<div class="clear"></div>
			</div>
		</div>
        <?php
    } ?>
