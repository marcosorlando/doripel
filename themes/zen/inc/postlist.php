<?php

use App\Helpers\DateHelper;

?>

<div class='<?= $cols ??= 'col-md-4' ?> wow fadeInUp'>
	<div class='news-item'>
		<div class='meta'>
			<div class='meta-item'><span class='icon icon-Tag'></span><?= $category_title; ?>
			</div>
			<div class="meta-item"><span class="icon icon-Watch"></span><?= $post_time; ?>min
			</div>
			<div class="meta-item"><span class="icon icon-Agenda"></span><?= DateHelper::human
                (
                    $post_date
                ); ?>
			</div>
		</div>
		<div class="image">
			<a href="https://blog.zen.ppg.br/artigo/<?= $post_name; ?>" title="Ler artigo"
			   target="_blank">
				<img src="<?= BASE ?>/tim.php?src=uploads/<?= $post_cover; ?>&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"
				     alt="<?= $post_title; ?>"/>
				<div class="image-content">
					<span class="read-more">leia mais</span>
				</div>
			</a>
		</div>
		<div class="user-avatar clearfix">
			<div class="avatar">
				<img src="<?= BASE ?>/tim.php?src=uploads/<?= $user_thumb; ?>"
				     alt="<?= $user_name; ?>"/>
			</div>
		</div>
		<div class="news-body">
			<h5><?= $post_title; ?></h5>
			<p>
                <?= $post_subtitle; ?>
			</p>
		</div>
	</div>
	</div><?php
