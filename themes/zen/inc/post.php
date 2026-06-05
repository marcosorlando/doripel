<?php

    use App\Helpers\Check;

?>
<!-- Blog post-->
<article class="wrap-blog-post wow fadeInUp">
	<div class="wrap-image">
		<a class="post_list_thumb" href="<?= BASE; ?>/artigo/<?= $post_name; ?>" title="<?= $post_title; ?>">
			<img class="img-responsive"
			     src="<?= BASE; ?>/tim.php?src=uploads/<?= $post_cover; ?>&w=<?= IMAGE_W / 2; ?>&h=<?= IMAGE_H / 2; ?>"
			     alt="<?= $post_title; ?>" title="<?= $post_title; ?>"/>
		</a>
	</div>
	<div class="wrap-post-description">
		<a class="post-avatar" href="#fakelink">
			<img class="" src="<?= INCLUDE_PATH; ?>/img/avatars/img2.png" alt="avatar">
		</a>
		<div class="meta">
			<div class="meta-item"><span class="icon icon-Tag"></span><?= $post_category; ?></div>
			<div class="meta-item"><span class="icon icon-Agenda"></span><?= $post_date; ?></div>
			<div class="meta-item"><span class="icon icon-Eye"></span><?= $post_views; ?> views</div>
		</div>
	</div>
	<div class="post-body">
		<h2>
			<a href="<?= BASE; ?>/artigo/<?= $post_name; ?>" title="<?= $post_title; ?>"><?= $post_title; ?></a>
		</h2>
		<p><?= Check::chars($post_subtitle, 120); ?></p>

		<div class="row">
			<div class="col-md-12 clearfix">
				<a href="<?= BASE; ?>/artigo/<?= $post_name; ?>" title="<?= $post_title; ?>"
				   class="btn btn-primary   pull-left">LEIA MAIS...</a>
			</div>
		</div>
	</div>
</article>
<!--blog-post-->
