<!-- start post item -->
<article class="col-md-4 col-sm-6 col-xs-12 sm-margin-50px-bottom xs-margin-30px-bottom wow fadeIn" data-wow-delay="0.2s">
  <div class="blog-post blog-post-style1 xs-text-center">
    <div class="blog-post-images overflow-hidden margin-25px-bottom sm-margin-20px-bottom">
      <a class="pdt_list_thumb" href="<?= BASE; ?>/movel/<?= $pdt_name; ?>" title="<?= $pdt_title; ?>">
        <img src="<?= BASE; ?>/tim.php?src=uploads/<?= $pdt_cover; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>" alt="<?= $pdt_title; ?>" title="<?= $pdt_title; ?> - <?= $pdt_color; ?> "/>
      </a>
    </div>
    <div class="post-details">
      <a href="<?= BASE; ?>/movel/<?= $pdt_title; ?>">
        <h2 class="post-title text-medium text-dark-gray display-block sm-width-100 text-center"><?= $pdt_title; ?> - <?= $pdt_color; ?></h2>
      </a>
      <h3 class="text-medium sm-width-100 text-center text-extra-medium-gray">REF. <?= $pdt_ref; ?></h3>
    </div>
    <div class="separator-line-horrizontal-full bg-medium-light-gray"></div>
  </div>
</article>
<!-- end post item -->
