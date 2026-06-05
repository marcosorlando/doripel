<?php

    use App\Conn\Read;
    use App\Helpers\Check;

?>

<!-- ========================== -->
<!-- HOME - LATEST NEWS -->
<!-- ========================== -->
<section class="latest-news-section with-icon with-top-effect clearfix">

	<div class="section-icon"><span class="icon icon-Blog"></span></div>
	<div class="container">
		<div class="section-heading">
			<div class="section-title">ÚLTIMAS NOVIDADES</div>
			<div class="section-subtitle">acompanhe nosso Blog, escreva-se para receber
				novidades em primeira mão
			</div>
			<div class="design-arrow"></div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="box">
                <?php
                    $Read ??= new Read();
                    $Read->fullRead(
                        'SELECT p.*, c.category_title, u.user_name, u.user_lastname, u.user_thumb FROM ' . DB_POSTS .
                        ' as p INNER JOIN ' . DB_CATEGORIES . ' as c ON (p.post_category = c.category_id) 
                        INNER JOIN ' . DB_USERS . " as u ON (p.post_author = u.user_id)                    
                    WHERE p.post_status = :st
                    AND p.post_date <= NOW()
                    ORDER BY p.post_date DESC
                    LIMIT :limit",
                        "st=1&limit=3"
                    );

                    if (!$Read->getResult()) {
                        echo Check::ajaxErro(
                            'Ainda não existem posts cadastrados. Favor volte mais tarde!',
                            E_USER_NOTICE
                        );
                    } else {
                        foreach ($Read->getResult() as $Post) {
                            extract($Post);
                            $cols = 'col-md-4';
                            require REQUIRE_PATH . '/inc/postlist.php';
                        }
                    }

                ?>
			</div>
		</div>
	</div>
</section>
