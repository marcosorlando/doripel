<?php

    use App\Helpers\Check;

?>
<div class="grid-item col-md-4 col-sm-6 col-xs-12 margin-30px-bottom xs-text-center wow fadeInUp">
	<div class="blog-post bg-white inner-match-height">
		<div class="blog-post-images overflow-hidden position-relative">
			<a href="<?= BASE; ?>/artigo/<?= $post_name; ?>"
			   title="<?= $post_title; ?> - Clique para continuar a leitura...">
				<img src="<?= BASE; ?>/tim.php?src=uploads/<?= $post_cover; ?>&w=<?= IMAGE_W / 2; ?>&h=<?= IMAGE_H / 2; ?>"
				     alt="<?= $post_title; ?>" title="<?= $post_title; ?>">
				<div class="blog-hover-icon"><span class="text-extra-large font-weight-300 icon-eye">+</span></div>
			</a>
		</div>
		<div class="post-details padding-40px-all sm-padding-20px-all">
			<a href="<?= BASE; ?>/artigo/<?= $post_name; ?>" title="<?= $post_title; ?> - Continue lendo..."
			   class="alt-font post-title text-medium text-extra-dark-gray width-100 display-block md-width-100 margin-15px-bottom"><?= $post_title; ?></a>
			<p class="text-small"><?= Check::chars($post_content, 150); ?></p>
			<div class="separator-line-horrizontal-full bg-medium-gray margin-20px-tb"></div>
			<div class="author">
				<span class="text-medium-gray text-uppercase text-extra-small display-inline-block sm-display-block sm-margin-10px-top">por <a
							href="<?= BASE; ?>/artigo/<?= $post_name; ?>"
							class="text-medium-gray"><?= $user_name . ' ' . $user_lastname;; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?= date(
                        'd/m/Y',
                        strtotime($post_date)
                    ); ?></span>
			</div>
		</div>
	</div>
</div>
