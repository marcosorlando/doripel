<!-- start post item -->
<article class="col-md-6 col-sm-6 col-xs-12 sm-margin-50px-bottom xs-margin-30px-bottom wow fadeIn" data-wow-delay="0.2s">
  <div class="blog-post blog-post-style1 xs-text-center">
    <div class="blog-post-images overflow-hidden margin-25px-bottom sm-margin-20px-bottom">
      <a class="post_list_thumb" href="<?= BASE; ?>/artigo/<?= $post_name; ?>" title="<?= $post_title; ?>">
        <img src="<?= BASE; ?>/tim.php?src=uploads/<?= $post_cover; ?>&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>" alt="<?= $post_title; ?>" title="<?= $post_title; ?>"/>
      </a>
    </div>
    <div class="post-details"">
      <span class="post-author text-extra-small text-medium-gray text-uppercase display-block margin-10px-bottom  xs-margin-5px-bottom"><i class='fa fa-calendar-check-o text-deep-pink'></i> <?= date('d-m-Y H:i', strtotime($post_date)); ?>h | <i class='text-deep-pink <?= $user_genre == 1 ? 'icon-profile-male' : 'icon-profile-female'; ?>'></i> por <a href="#" class="text-medium-gray"><?= $AuthorName; ?></a></span>
      <a href="<?= BASE; ?>/artigo/<?= $post_name; ?>" ><h2 class="post-title text-medium text-extra-dark-gray display-block sm-width-100"><?= $post_title; ?></h2></a>
      
      <h4 class=""><?= Check::Chars($post_subtitle, 70); ?>
        <a href="<?= BASE; ?>/artigo/<?= $post_name; ?>" title="<?= $post_title; ?>" class=" float-rt text-deep-pink">Continue Lendo <i class="fa fa-rocket"></i></a>
      
      </h4>
    </div>
    <div class="separator-line-horrizontal-full bg-medium-light-gray margin-20px-tb sm-margin-15px-tb"></div> 
  </div>
</article>

<!-- end post item -->
