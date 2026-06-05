<footer class="footer-classic-dark bg-dark-blue padding-five-bottom xs-padding-30px-bottom text-white">
    <div class="bg-dark-footer padding-50px-tb xs-padding-30px-tb">
        <div class="container">
            <div class="row equalize xs-equalize-auto">
                <!-- start slogan -->
                <div class="col-md-4 col-sm-5 col-xs-12 text-center alt-font display-table xs-text-center xs-margin-15px-bottom">
                    <div class="display-table-cell vertical-align-middle">
                        De Lagoa Vermelha para o mundo...
                    </div>
                </div>
                <!-- end slogan -->
                <!-- start logo -->
                <div class="col-md-4 col-sm-2 col-xs-12 text-center display-table xs-margin-10px-bottom">
                    <div class="display-table-cell vertical-align-middle">
                        <a href="<?= BASE; ?>" title="Voltar ao Início"><img class="footer-logo" src="<?= INCLUDE_PATH; ?>/images/logo-full-white.png" data-at2x="<?= INCLUDE_PATH; ?>/images/logo-full-white@2x.png" alt="Doripel Móveis - logotipo"></a>
                    </div>
                </div>
                <!-- end logo -->
                <!-- start social media -->
                <div class="col-md-4 col-sm-5 col-xs-12 col-xs-12 text-center display-table xs-text-center">
                    <div class="display-table-cell vertical-align-middle">
                        <span class="alt-font margin-20px-right">Siga-nos nas redes sociais</span>
                        <div class="social-icon-style-8 display-inline-block vertical-align-middle">
                            <ul class="small-icon no-margin-bottom">

                                <?= (!empty(SITE_SOCIAL_FB_PAGE) ? "<li><a class='facebook text-white' href='https://www.facebook.com/" . SITE_SOCIAL_FB_PAGE . "' target='_blank'><i class='fa fa-facebook' aria-hidden='true'></i></a></li>" : null); ?>
                                <?= (!empty(SITE_SOCIAL_INSTAGRAM) ? " <li><a class='instagram text-white' href='https://instagram.com/". SITE_SOCIAL_INSTAGRAM . "' target='_blank'><i class='fa fa-instagram no-margin-right' aria-hidden='true'></i></a>" : null); ?>
                                <?= (!empty(SITE_SOCIAL_LINKEDIN) ? "<li><a class='linkedin text-white' href='https://www.linkedin.com/in/".SITE_SOCIAL_LINKEDIN ."' target='_blank'><i class='fa fa-linkedin'></i></a></li>" : null); ?>
                                   <?= (!empty(SITE_SOCIAL_YOUTUBE) ? "<li><a class='youtube text-white' href='https://www.youtube.com/channel/".SITE_SOCIAL_YOUTUBE ."' target='_blank'><i class='fa fa-youtube'></i></a></li>" : null); ?>


                            </ul>
                        </div>
                    </div>
                </div>
                <!-- end social media -->
            </div>
        </div>
    </div>
    <div class="footer-widget-area padding-five-top padding-30px-bottom xs-padding-30px-top">
        <div class="container">
            <div class="row">
                <!-- start about -->
                <div class="col-md-3 col-sm-6 col-xs-12 widget sm-margin-30px-bottom xs-text-center">
                    <div class="widget-title alt-font text-small text-medium-gray text-uppercase margin-15px-bottom font-weight-600">
                        Sobre nós
                    </div>
                    <p class="text-small width-95 xs-width-100 no-margin">Visando sempre a satisfação dos clientes, a Doripel
                        conta com processos de fabricação com tecnologia moderna que resulta em produtos de alto padrão e qualidade
                        e uma ótima relação custo/benefício, preço acessível e design moderno em um único produto.</p>
                    <a href="<?= BASE; ?>/sobre" title="" class="btn btn-very-small btn-white margin-10px-top">Continue lendo!</a>
                </div>
                <!-- end about -->
                <!-- start blog post -->
                <div class="col-md-3 col-sm-6 col-xs-12 widget sm-margin-30px-bottom">
                    <div class="widget-title alt-font text-small text-medium-gray text-uppercase margin-15px-bottom font-weight-600 xs-text-center">
                        Últimos Posts no Blog
                    </div>
                    <ul class="latest-post position-relative top-3">
                        <?php

                            $Read->FullRead("SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, u.user_name, u.user_lastname, u.user_genre FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE post_status = 1 AND post_date <= NOW() AND post_author = user_id ORDER BY post_date DESC LIMIT :limit", "limit=3");

                            if (!$Read->getResult()):
                                //              $Pager->ReturnPage();
                                echo Erro("Ainda Não existe posts cadastrados nesta secão. Favor volte mais tarde :)", E_USER_NOTICE);
                            else:
                                foreach ($Read->getResult() as $Post):
                                    extract($Post);
                                    ?>

                                    <li class="border-bottom border-color-light-gray">
                                        <figure>
                                            <a href="<?= BASE; ?>/artigo/<?= $post_name; ?>"><img src="<?= BASE; ?>/tim.php?src=uploads/<?= $post_cover; ?>&w=<?= IMAGE_W / 2 ?>&h<?= IMAGE_H / 2; ?>" alt=""></a>
                                        </figure>
                                        <div class="text-small">
                                            <a href="<?= BASE; ?>/artigo/<?= $post_name; ?>"><?= $post_title; ?> </a>
                                            <span class="clearfix"></span> <?= date('d-m', strtotime($post_date)); ?> | por
                                            <a href="<?= BASE; ?>/artigo/<?= $post_name; ?>"><?= $user_name . ' ' . $user_lastname; ?></a>
                                        </div>
                                    </li>

                                <?php
                                endforeach;
                            endif;
                        ?>

                    </ul>
                </div>
                <!-- end blog post -->
                <!-- start newsletter -->
                <div class="col-md-3 col-sm-6 col-xs-12 widget sm-margin-30px-bottom xs-text-center">
                    <div class="widget-title alt-font text-small text-medium-gray text-uppercase margin-15px-bottom font-weight-600">
                        Escreva-se na Lista VIP
                    </div>
                    <p class="text-small width-90 xs-width-100">Receba em primeira mão nossas novidades.</p>
                    <form name="lead_capture" class="j_formsubmit form_lead" action="" method="post" enctype="multipart/form-data">
                        <div class="callback_return trigger_ajax"></div>
                        <input type="hidden" name="callback" value="Leads">
                        <input type="hidden" name="callback_action" value="news2">

                        <input type="text" class="name" placeholder="Insira seu Nome" name="name" required="required">
                        <input type="email" class="email" placeholder="Insira seu E-mail" name="email" required="required">

                        <button name="public" type="submit" class="fl_right btn btn-small btn-deep-pink">ESCREVA-SE!
                            <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="<?= INCLUDE_PATH; ?>/images/icons/load.gif">
                        </button>
                    </form>


                </div>
                <!-- end newsletter -->
                <!-- start instagram -->
                <div class="col-md-3 col-sm-6 col-xs-12 widget xs-margin-5px-bottom xs-text-center">
                    <div class="widget-title alt-font text-small text-medium-gray text-uppercase margin-20px-bottom font-weight-600">
                        Siga-nos no Instagram
                    </div>
                    <div class="instagram-follow-api">
                        <ul id="instaFeed-footer"></ul>
                    </div>
                </div>
                <!-- end instagram -->
            </div>
        </div>
    </div>
    <div class="container">
        <div class="footer-bottom border-top border-color-light-gray padding-30px-top">
            <div class="row">
                <!-- start copyright -->
                <div class="col-md-6 col-sm-6 col-xs-12 text-left text-small xs-text-center">Doripel®</div>
                <div class="col-md-6 col-sm-6 col-xs-12 text-right text-small xs-text-center">&COPY; <?= Date('Y'); ?> Doripel
                    Móveis é produzido por
                    <a class="zen" href="https://zen.ppg.br" title="Zen Agência Web - Marketing Digital" target="_blank"></a>
                </div>

                <!-- end copyright -->
            </div>
        </div>
    </div>
</footer>
<!-- end footer -->

