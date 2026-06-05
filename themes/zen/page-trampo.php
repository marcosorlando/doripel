<?php

    use App\Conn\Read;
    use App\Conn\Update;

    $Read ??= new Read();

    $Read->fullRead(
        'SELECT p.*, c.id as cat_id, c.title as cat_title, c.slug as cat_slug FROM ' . DB_PORTFOLIO .
        ' p INNER JOIN ' . DB_PORTFOLIO_CATEGORIES . ' c ON (p.category = c.id) 
        WHERE p.slug = :slug AND status = 1',
        "slug=" . ($URL[1] ?? '')
    );

    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        $case = $Read->getResult()[0];

        $Update = new Update();
        $UpdateView = ['views' => $case['views'] + 1, 'lastview' => date('Y-m-d H:i:s')];
        $Update->exeUpdate(
            DB_PORTFOLIO,
            $UpdateView,
            "WHERE id = :id",
            "id={$case['id']}"
        );


        $Category = $case['cat_id'];
        $CurrentCategorySlug = $case['cat_slug'];
        $portfolioCategoryUrl = BASE . '/portfolio?categoria=' . rawurlencode((string)$CurrentCategorySlug);

        $Read->fullRead(
            'SELECT slug FROM ' . DB_PORTFOLIO . ' WHERE status = :st AND category = :ct AND id < :id ORDER BY id DESC LIMIT 1',
            "st=1&ct={$Category}&id={$case['id']}"
        );
        $prevWorkUrl = $Read->getResult() ? BASE . '/trampo/' . $Read->getResult()[0]['slug'] : null;

        $Read->fullRead(
            'SELECT slug FROM ' . DB_PORTFOLIO . ' WHERE status = :st AND category = :ct AND id > :id ORDER BY id ASC LIMIT 1',
            "st=1&ct={$Category}&id={$case['id']}"
        );
        $nextWorkUrl = $Read->getResult() ? BASE . '/trampo/' . $Read->getResult()[0]['slug'] : null;
        ?>

		<!-- ========================== -->
		<!-- PORTFOLIO - HEADER -->
		<!-- ========================== -->
		<div class="top-header portfolio-header with-bottom-effect transparent-effect dark dark-strong">
			<div class="bottom-effect"></div>
			<div class="header-container">
				<div class="header-title">
					<div class="header-icon"><span class="icon icon-Wheelbarrow"></span></div>
                    <?php
                        echo "<div class='title'>Case</div>
					<em>{$case['cat_title']}</em>";
                    ?>
				</div>
			</div>
		</div>

		<!-- ========================== -->
		<!-- PORTFOLIO - SINGLE SECTION -->
		<!-- ========================== -->
		<section class="portfolio-single-section">
			<div class="container">
				<div class="row">

					<article class="col-md-10 col-md-offset-1 col-sm-12">
						<div class="work-heading">
							<h1><?= $case['title'] ?></h1>
							<div class="category"><?= $case['cat_title'] ?></div>

							<div class="views">
								<span class="icon icon-Eye"></span>
								<span><?= $case['views'] ?></span>
							</div>

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
							</div>
						</div>
						<div class="work-image">
							<div class="image">
								<img src="<?= BASE; ?>/tim.php?src=uploads/portfolio/<?= $case['img_970x500'];
                                ?>&w=1200&h=628"
								     alt="<?= $case['title']; ?>"/>
								<div class="controls">
									<div class="big-view"><a href="#"><span class="icon icon-Search"></span></a></div>
								</div>
							</div>
						</div>
						<div class="work-body">
							<div class="row">
								<div class="col-md-8 col-sm-7 work-body-left">
									<h5>DESCRIÇÃO:</h5>
									<p><?= $case['description'] ?></p>

									<a href="<?= $case['link_project'] ?>" target="_blank" title="Clique-me!"
									   class="btn btn-primary">VER PROJETO</a>
								</div>
								<div class="col-md-4 col-sm-5 work-body-right">
									<h5>Resumo</h5>
									<div class="row summary-list">


										<div class='col-md-12 clearfix'>
											<div class='type-info pull-left'>
												<i class='icon icon-User'></i>
												Nicho
											</div>
											<div class='info pull-right text-right'>
												<p class='no-margin'><?= $case['niche'] ?></p>
											</div>
										</div>

										<div class="col-md-12 clearfix">
											<div class="type-info pull-left">
												<i class="icon icon-User"></i>
												Problema
											</div>
											<div class="info pull-right text-right">
												<p class="no-margin"><?= $case['problem'] ?></p>
											</div>
										</div>

										<div class='col-md-12 clearfix'>
											<div class='type-info pull-left'>
												<i class='icon icon-User'></i>
												Objetivos
											</div>
											<div class='info pull-right text-right'>
												<p class='no-margin'><?= $case['objectives'] ?></p>
											</div>
										</div>
										<div class='col-md-12 clearfix'>
											<div class='type-info pull-left'>
												<i class='icon icon-User'></i>
												Período de Mensuração
											</div>
											<div class='info pull-right text-right'>
												<p class='no-margin'><?= $case['measurement_period'] ?></p>
											</div>
										</div>
										<div class='col-md-12 clearfix'>
											<div class='type-info pull-left'>
												<i class='icon icon-User'></i>
												Métricas chave (KPIs)
											</div>
											<div class='info pull-right text-right'>
												<p class='no-margin'><?= $case['key_metrics'] ?></p>
											</div>
										</div>
										<div class='col-md-12 clearfix'>
											<div class='type-info pull-left'>
												<i class='icon icon-User'></i>
												Duração do projeto
											</div>
											<div class='info pull-right text-right'>
												<p class='no-margin'><?= $case['project_duration'] ?></p>
											</div>
										</div>

										<div class="col-md-12 clearfix">
											<div class="type-info pull-left">
												<i class="icon icon-Agenda"></i>
												Entregue em
											</div>
											<div class="info pull-right text-right">
												<p class="no-margin"><?= date(
                                                        'd/m/Y',
                                                        strtotime($case['deliveryted_at'])
                                                    );
                                                    ?></p>
											</div>
										</div>
										<div class="col-md-12 clearfix">
											<div class="type-info pull-left">
												<i class="icon icon-Layers"></i>
												Habilidades
											</div>
											<div class="info pull-right text-right">
												<p class="no-margin"><?= $case['skills'] ?></p>
											</div>
										</div>

										<div class="col-md-12 clearfix">
											<div class="type-info pull-left">
												<i class="icon icon-DesktopMonitor"></i>
												Cliente
											</div>
											<div class="info pull-right text-right">
												<p class="no-margin"><?= $case['client'] ?></p>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</article>
				</div>
			</div>
		</section>


		<!-- ========================== -->
		<!-- PORTFOLIO - RELATE WORKS SECTION -->
		<!-- ========================== -->
		<section class="portfolio-related-projects-section with-icon with-top-effect">
			<div class="section-icon"><span class="icon icon-Umbrella"></span></div>
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
                                "SELECT p.*, c.title FROM " . DB_PORTFOLIO . " p
                                INNER JOIN " . DB_PORTFOLIO_CATEGORIES . " c ON (p.category = c.id) 
                                WHERE status = :st AND category = :ct AND p.id <> :id ",
                                "st=1&ct={$Category}&id={$case['id']}"
                            );

                            foreach ($Read->getResult() as $Relacionados) {
                                extract($Relacionados);
                                ?>
								<div class="col-md-4 col-sm-4 col-xs-6">
									<div class="portfolio-item">
										<div class="portfolio-image">
											<a href="<?= BASE ?>/trampo/<?= $slug ?>"><img
														src="<?= BASE; ?>/uploads/portfolio/<?= $img_350x350; ?>"
														alt="<?= $case['title']; ?>"></a>
											<div class="portfolio-item-body">
												<div class="name"><?= $case['title']; ?></div>
												<div class="under-name"><?= $case['title']; ?></div>
											</div>
										</div>
									</div>
								</div>
                                <?php
                            } ?>
					</div>
				</div>
			</div>
		</section>


        <?php
    }
    if (APP_COMMENTS && COMMENT_ON_PAGES) { ?>
		<div class="container" style="background: #fff; padding: 20px 0;">
			<div class="content">
                <?php
                    $CommentKey = $page_id;
                    $CommentType = 'page';
                    require_once 'assets/widgets/comments/comments.php';
                ?>
				<div class="clear"></div>
			</div>
		</div>
        <?php
    } ?>
