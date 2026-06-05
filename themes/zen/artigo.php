<?php

    use App\Conn\Read;
    use App\Conn\Update;

    $Read = new Read();
    $URL[1] = $URL[1] ?? '';

    $Read->fullRead(
        "SELECT p.*, u.user_name, u.user_lastname, u.user_thumb, c.category_title, c.category_name FROM " . DB_POSTS . " as p INNER JOIN " . DB_USERS . " as u ON (p.post_author = u.user_id)" . " INNER JOIN " . DB_CATEGORIES . " as c ON (p.post_category = c.category_id) WHERE p.post_name = :nm",
        "nm={$URL[1]}"
    );

    $userLevel = isset($_SESSION['userLogin']) ? $_SESSION['userLogin']['user_level'] : 1;

    if (!$Read->getResult() || $userLevel < 6) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
        $Update = new Update();
        $UpdateView = ['post_views' => $post_views + 1, 'post_lastview' => date('Y-m-d H:i:s')];
        $Update->exeUpdate(DB_POSTS, $UpdateView, "WHERE post_id = :id", "id={$post_id}");
    }
?>
<header class='top-header blog-header with-bottom-effect transparent-effect dark'>
	<div class='bottom-effect'></div>
	<div class='header-container'>
		<div class='header-title'>
			<div class='header-icon'><span class='icon icon-Layers'></span></div>
			<p class='title'><a
						href="<?= BASE; ?>/artigos/<?= $category_name; ?>"
						title="Ver mais: <?= $category_title; ?> em <?= SITE_NAME; ?>!"><?= $category_title; ?></a></p>
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
			<div class="col-lg-8 col-md-9 left-column">
				<!--Blog post-->
				<article class="wrap-blog-post">

                    <?php
                        if ($post_video) {
                            echo "<div class='embed-container'>";
                            echo "<iframe id='mediaview' class='embed-responsive-item' width='100%' height='360' src='https://www.youtube.com/embed/{$post_video}?rel=0&amp;showinfo=0&autoplay=0&origin=" . BASE . "' frameborder='0' allowfullscreen></iframe>";
                            echo "</div>";
                        } else {
                            echo "<div class='wrap-image'>"
                                . "<a class='post_list_thumb' href='" . BASE . "/artigo/{$post_name}' title='{$post_title}'>"
                                . "<img class='img-responsive' src='" . BASE . "/tim.php?src=uploads/{$post_cover}&w=" . IMAGE_W . "&h=" . IMAGE_H . "' alt='{$post_title}' title='{$post_title}'/>"
                                . "</a>"
                                . "</div>";
                        }
                    ?>

					<div class="wrap-post-description">
						<a class="post-avatar" href="#fakelink">
							<img class="" alt="<?= $user_name . " " . $user_lastname; ?>"
							     title="<?= $user_name . " " . $user_lastname; ?>"
							     src="<?= BASE; ?>/tim.php?src=uploads/<?= $user_thumb; ?>"/>
						</a>
						<div class="meta">
							<div class="meta-item"><span
										class="icon icon-User"></span><b><?= $user_name ?></b> <?= $user_lastname; ?>
							</div>
							<div class="meta-item"><span class="icon icon-Tag"></span><?= $category_title; ?></div>
							<div class="meta-item"><span class="icon icon-Agenda"></span><?= date(
                                    'd/m/Y H:m',
                                    strtotime($post_date)
                                ); ?>h
							</div>
							<div class="meta-item"><span class="icon icon-Eye"></span><?= $post_views; ?> views</div>
							<div class="meta-item"><span class="icon icon-Hourglass"></span><?= $post_time; ?> minutos
							</div>
						</div>
					</div>

					<header>
						<h1 class='title'><?= $post_title; ?></h1>
						<h2 class="tagline"><?= $post_subtitle; ?></h2>
					</header>

					<div class="htmlchars post-body wow fadeIn">
                        <?= $post_content; ?>
					</div>

                    <?php
                        $WC_TITLE_LINK = $post_title;
                        $WC_SHARE_HASH = 'ZenAgenciaWeb';
                        $WC_SHARE_LINK = BASE . "/artigo/{$post_name}";
                        require './assets/widgets/share/share.wc.php'; ?>
					<BR>

					<div class="post_comments">
                        <?php
                            $CommentType = 'post';
                            $CommentKey = (int)$post_id;
                            require_once './assets/widgets/comments/comments.php'; ?>
					</div>

				</article>
				<div class="clear"></div>
			</div>
            <?php
                require REQUIRE_PATH . '/inc/sidebar.php'; ?>
		</div>
	</div>

	<div class='buy-section'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-8 col-md-offset-1 col-sm-9 wow fadeInLeft'>
					<div class='section-text'>
						<div class=' vcenter like'>
							<span class='icon icon-Like'></span>
						</div>
						<div class='buy-text vcenter'>
							<div class='top-text'>
								<span>Assine nosso Blog sobre <em>Marketing Digital</em></span>
							</div>
							<div class='bottom-text'>Clique no botão ao lado para realizar sua assinatura agora...</div>
						</div>
					</div>
				</div>
				<div class='col-md-3 col-sm-4  wow fadeInRight'>
					<a href='<?= BASE; ?>/conta/cadastro#acc' title='Clique!' tabindex='2'
					   class='btn btn-info'>ASSINAR</a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
    $Read->exeRead(
        DB_POSTS,
        "WHERE post_status = 1 AND post_date <= NOW() AND post_category_parent = :ct AND post_id != :id ORDER BY post_date DESC LIMIT 4",
        "ct={$post_category_parent}&id={$post_id}"
    );
    if ($Read->getResult()) {
        echo '<section class="single_post_more">';
        echo "<div class='content'>";
        echo '<header class="site_header">';
        echo '<h4>Veja Também!</h4>';
        echo '<p>Os artigos relacionados podem te interessar:</p>';
        echo '</header>';
        echo "<div class='row'>";

        foreach ($Read->getResult() as $More) {
            extract($More);
            ?>
			<!-- Blog post-->
			<article class="wrap-blog-post wow fadeInUp col-lg-3 col-md-2 col-sm-1">
				<div class="wrap-image">
					<a class="post_list_thumb" href="<?= BASE; ?>/artigo/<?= $post_name; ?>"
					   title="<?= $post_title; ?>">
						<img class="img-responsive"
						     src="<?= BASE; ?>/tim.php?src=uploads/<?= $post_cover; ?>&w=<?= IMAGE_W / 2; ?>&h=<?= IMAGE_H / 2; ?>"
						     alt="<?= $post_title; ?>" title="<?= $post_title; ?>"/>
					</a>
				</div>

				<div class="post-body">
					<h6><a href="<?= BASE; ?>/artigo/<?= $post_name; ?>"
					       title="<?= $post_title; ?>"><?= $post_title; ?></a>
					</h6>
				</div>
			</article>
            <?php
        }
        echo '<div class="clear"></div></div></div>';
        echo '</section>';
    }
?>
