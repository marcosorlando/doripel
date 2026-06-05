<?php

    if (!$Read) {
        $Read = new Read;
    }

    $Read->exeRead(DB_PORTFOLIO, "WHERE name = :nm AND status = 1", "nm={$URL[1]}");
    if (!$Read->getResult()) {
    require REQUIRE_PATH . '/404.php';
    return;
} else {
    extract($Read->getResult()[0]);
    $Update = new Update;
    $UpdateView = ['views' => $views + 1, 'lastview' => date('Y-m-d H:i:s')];
    $Update->exeUpdate(DB_PORTFOLIO, $UpdateView, "WHERE id = :id", "id={$id}");
?>
	<!-- ========================== -->
	<!-- PORTFOLIO - HEADER -->
	<!-- ========================== -->
	<header class="top-header portfolio-header-trampo  transparent-effect dark dark-strong"></header>
<!-- ========================== -->
<!-- PORTFOLIO - SINGLE SECTION -->
<!-- ========================== -->
<section class="portfolio-single-section">
	<div class="container">
		<div class="row">
            <?php
                $Read->fullRead(
                    "SELECT ws_works.*, ws_works_categories.title, ws_works_categories.id FROM " . DB_PORTFOLIO . " INNER JOIN " . DB_PORTFOLIO_CATEGORIES . " ON (`ws_works`.`category` = `ws_works_categories`.`id`) WHERE status = :st AND name = :nm ",
                    "st=1&nm={$URL[1]}"
                );

                if (!$Read->getResult()) {
                    echo "<div class=\"trigger trigger_info\">O projeto foi removido do sistema, veja abaixo projetos relacionados.<span class=\"ajax_close\"></span></div>";
                } else {
                    extract($Read->getResult()[0]);
                    $Category = $id;
                    $CurrentCategorySlug = $name;
                    $portfolioCategoryUrl = BASE . '/portfolio?categoria=' . rawurlencode((string)$CurrentCategorySlug);

                    $Read->fullRead(
                        "SELECT name FROM " . DB_PORTFOLIO . " WHERE status = :st AND category = :ct AND id < :id ORDER BY id DESC LIMIT 1",
                        "st=1&ct={$Category}&id={$id}"
                    );
                    $prevWorkUrl = $Read->getResult() ? BASE . '/trampo/' . $Read->getResult()[0]['name'] : null;

                    $Read->fullRead(
                        "SELECT name FROM " . DB_PORTFOLIO . " WHERE status = :st AND category = :ct AND id > :id ORDER BY id ASC LIMIT 1",
                        "st=1&ct={$Category}&id={$id}"
                    );
                    $nextWorkUrl = $Read->getResult() ? BASE . '/trampo/' . $Read->getResult()[0]['name'] : null;
                    ?>
					<h2 class="title-hidden">Projetos entregues:</h2>
					<article class="col-md-10 col-md-offset-1 col-sm-12">
						<div class="work-heading">
							<h1 id="wk_title"><?= $title ?></h1>
							<p class="category"><?= $title ?></p>

							<div class="controls">
								<ul>
									<li>
                                        <?php
                                            if ($prevWorkUrl) { ?>
												<a href="<?= $prevWorkUrl; ?>"><span
															class="fa fa-angle-left"></span></a>
                                                <?php
                                            } else { ?>
												<a href="javascript:void(0)" class="is-disabled"><span
															class="fa fa-angle-left"></span></a>
                                                <?php
                                            } ?>
									</li>
									<li><a href="<?= $portfolioCategoryUrl; ?>"><span class="fa fa-th"></span></a></li>
									<li>
                                        <?php
                                            if ($nextWorkUrl) { ?>
												<a href="<?= $nextWorkUrl; ?>"><span
															class="fa fa-angle-right"></span></a>
                                                <?php
                                            } else { ?>
												<a href="javascript:void(0)" class="is-disabled"><span
															class="fa fa-angle-right"></span></a>
                                                <?php
                                            } ?>
									</li>
								</ul>
								<div class="views"><span class="icon icon-Eye"></span><span
											class=""> <?= $views ?></span></div>
							</div>
						</div>
						<div class="work-image">
							<div class="image">
								<img src="<?= BASE; ?>/uploads/portfolio/<?= $img_970x500; ?>"
								     alt="<?= title; ?>"/>
								<div class="controls">
									<div class="big-view">
										<a class="fancybox"
										   href="<?= BASE; ?>/uploads/portfolio/<?= $img_970x500; ?>">
											<span class="icon icon-Search"></span>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="work-body">
							<div class="row">
								<div class="col-md-8 col-sm-7 work-body-left">
									<h2>DESCRIÇÃO</h2>
									<p><?= $description ?></p>
									<a href="<?= $link_project ?>" title="Acessar agora!" target="_blank"
									   class="btn btn-primary-warning">VER PROJETO</a>
								</div>
								<div class="col-md-4 col-sm-5 work-body-right">
									<h2>RESUMO</h2>
									<div class="row summary-list">
										<div class="col-md-12 clearfix">
											<h6 class="type-info pull-left">
												<i class="icon icon-User"></i>
												Criado por
											</h6>
											<div class="info pull-right text-right">
												<p class="no-margin creator"><?= $creator ?></p>
											</div>
										</div>
										<div class="col-md-12 clearfix">
											<h6 class="type-info pull-left">
												<i class="icon icon-Agenda"></i>
												Entregue
											</h6>
											<div class="info pull-right text-right">
												<p class="no-margin delivery"><?= date(
                                                        'd/m/Y',
                                                        strtotime($delivery)
                                                    ); ?></p>
											</div>
										</div>
										<div class="col-md-12 clearfix">
											<h6 class="type-info pull-left">
												<i class="icon icon-Tools"></i>
												Habilidades
											</h6>
											<div class="info pull-right text-right">
												<p class="no-margin skills"><?= $skills ?></p>
											</div>
										</div>
										<div class="col-md-12 clearfix">
											<div class="type-info pull-left">
												<i class="icon icon-Tie"></i>
												Cliente
											</div>
											<div class="info pull-right text-right">
												<p class="no-margin client"><?= $client ?></p>
											</div>
										</div>
										<div class="col-md-12 clearfix">
											<div class="type-info pull-left">
												<i class="icon icon-Share"></i>
												Compartilhar
                                                <?php
                                                    $WC_TITLE_LINK = $title;
                                                    $WC_SHARE_HASH = "ZenTrampos";
                                                    $WC_SHARE_LINK = BASE . "/trampo/{$name}";
                                                    //require './_cdn/widgets/share/share.wc.php';
                                                ?>
											</div>
											<div class="info pull-right text-right">
												<ul class="list-socials">
													<li>
														<a href="https://www.facebook.com/sharer/sharer.php?u=<?= BASE; ?>/<?= $getURL; ?>"><i
																	class="fa fa-facebook" target="_blank"></i></a></li>

													<li>
														<a href="https://br.pinterest.com/pin/create/button/?url=<?= BASE; ?>/<?= $getURL; ?>&media=&description=<?= $description; ?>"
														   target="_blank"><i class="fa fa-pinterest"></i></a></li>

													<li>
														<a href="https://twitter.com/intent/tweet?hashtags=ZenAgenciaWeb&url=<?= BASE; ?>/<?= $getURL; ?>&text=<?= $title; ?>"
														   target="_blank"><i class="fa fa-twitter"></i></a></li>

													<li>
														<a href="https://plus.google.com/share?url=<?= BASE; ?>/<?= $getURL; ?>"
														   target="_blank"><i class="fa fa-google-plus"></i></a></li>

													<li>
														<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= BASE; ?>/<?= $getURL; ?>"
														   target="_blank"><i class="fa fa-linkedin"></i></a></li>
												</ul>
											</div>
										</div>
									</div>
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
</section>
<!-- ========================== -->
<!-- PORTFOLIO - RELATE WORKS SECTION -->
<!-- ========================== -->
<section class="portfolio-related-projects-section with-icon with-top-effect">
	<div class="section-icon"><span class="icon icon-Briefcase"></span></div>
	<div class="container">
		<div class="section-heading">
			<div class="section-title">PROJETOS RELACIONADOS</div>
			<div class="section-subtitle">Selecionamos mais alguns projetos do mesmo gênero para você</div>
			<div class="design-arrow"></div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="list-works clearfix">

                <?php
                    $Read->fullRead(
                        "SELECT ws_works.*, ws_works_categories.title FROM " . DB_PORTFOLIO . " INNER JOIN " . DB_PORTFOLIO_CATEGORIES . " ON (`ws_works`.`category` = `ws_works_categories`.`id`) WHERE status = :st AND category = :ct AND id <> :id ",
                        "st=1&ct={$Category}&id={$id}"
                    );

                    foreach ($Read->getResult() as $Relacionados) {
                        extract($Relacionados);
                        ?>
						<div class="col-md-4 col-sm-4 col-xs-6">
							<div class="portfolio-item">
								<div class="portfolio-image">
									<a href="<?= BASE ?>/trampo/<?= $name ?>" title="Clique!"><img
												src="<?= BASE; ?>/uploads/portfolio/<?= $img_350x350; ?>"
												alt="<?= $title; ?>"></a>
									<div class="portfolio-item-body">
										<div class="name"><?= $title; ?></div>
										<div class="under-name"><?= $title; ?></div>
									</div>
								</div>
							</div>
						</div>
                        <?php
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
                    require '_cdn/widgets/comments/comments.php';
                ?>
				<div class="clear"></div>
			</div>
		</div>
        <?php
    }
?>
