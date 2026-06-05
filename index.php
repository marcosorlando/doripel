<?php

    //@TODO REMOVER ou comentar DEPOIS DE ATUALIZAR
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);

    ini_set('allow_url_fopen', 'On');
    ob_start();
    session_start();

    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');

    require_once __DIR__ . '/vendor/autoload.php';

    use App\Conn\Read;
    use App\Conn\Update;
    use App\Models\Seo;
    use App\Models\Session;

    // CHANCE THEME IN SESSION
    $WC_THEME = filter_input(INPUT_GET, 'wctheme', FILTER_DEFAULT);
    if ($WC_THEME && $WC_THEME != null) {
        $_SESSION['WC_THEME'] = $WC_THEME;
    } else {
        unset($_SESSION['WC_THEME']);
    }

    // READ CLASS AUTO INSTANCE
    $Read ??= new Read();
    $Sesssion = new Session(SIS_CACHE_TIME);

    // USER SESSION VALIDATION
    if (!empty($_SESSION['userLogin']) && !empty($_SESSION['userLogin']['user_id'])) {
        if (empty($Read)) {
            $Read = new Read();
        }
        $Read->exeRead(DB_USERS, 'WHERE user_id = :user_id', 'user_id=' . $_SESSION['userLogin']['user_id']);
        if ($Read->getResult()) {
            $_SESSION['userLogin'] = $Read->getResult()[0];
        } else {
            unset($_SESSION['userLogin']);
        }
    }

    // GET PARAMETER URL
    $getURL = strip_tags(trim((string)filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
    if ('' === $getURL || '0' === $getURL || 'index.php' === $getURL || '/index.php' === $getURL) {
        $setURL = 'index';
    } else {
        $setURL = $getURL;
    }
    $URL = explode('/', $setURL);
    $SEO = new Seo($setURL);
?>

<!DOCTYPE html>
<html lang="pt-br" itemscope itemtype="https://schema.org/<?= $SEO->getSchema(); ?>">

<head>
	<meta charset="UTF-8">
	<meta name="mit" content="2017-11-16T11:05:36-02:00+24186">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<title><?= $SEO->getTitle(); ?></title>
	<meta name="description" content="<?= $SEO->getDescription(); ?>"/>
	<meta name="robots" content="index, follow"/>
	<meta name="msvalidate.01" content=""/>
	<link rel="base" href="<?= BASE; ?>"/>
	<link rel="canonical" href="<?= BASE; ?>/<?= $getURL; ?>"/>
	<link rel="alternate" type="application/rss+xml" href="<?= BASE; ?>/rss.php"/>
	<link rel="sitemap" type="application/xml" href="<?= BASE; ?>/sitemap.xml"/>
	<meta itemprop="name" content="<?= $SEO->getTitle(); ?>"/>
	<meta itemprop="description" content="<?= $SEO->getDescription(); ?>"/>
	<meta itemprop="image" content="<?= $SEO->getImage(); ?>"/>
	<meta itemprop="url" content="<?= BASE; ?>/<?= $getURL; ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:title" content="<?= $SEO->getTitle(); ?>"/>
	<meta property="og:description" content="<?= $SEO->getDescription(); ?>"/>
	<meta property="og:image" content="<?= $SEO->getImage(); ?>"/>
	<meta property="og:url" content="<?= BASE; ?>/<?= $getURL; ?>"/>
	<meta property="og:site_name" content="<?= SITE_NAME; ?>"/>
	<meta property="og:locale" content="pt_BR"/>

    <?php

        if (SITE_SOCIAL_FB !== 0) {
            echo '<meta property="article:author" content="https://www.facebook.com/'
                . SITE_SOCIAL_FB_AUTHOR . '" />' . "\r\n";

            echo '<meta property="article:publisher" content="https://www.facebook.com/'
                . SITE_SOCIAL_FB_PAGE . '" />' . "\r\n";

            if (SITE_SOCIAL_FB_APP !== '') {
                echo '<meta property="og:app_id" content="' . SITE_SOCIAL_FB_APP . '" />' . "\r\n";
            }

            if (SEGMENT_FB_PAGE_ID !== '') {
                echo '<meta property="fb:pages" content="' . SEGMENT_FB_PAGE_ID . '" />' . "\r\n";
            }

            if (SITE_SOCIAL_FB_DOMAIN_VERIFICATION !== '') {
                echo '<meta name="facebook-domain-verification" content="' . SITE_SOCIAL_FB_DOMAIN_VERIFICATION . '" />' . "\r\n";
            }
        }

    ?>

	<meta property="twitter:card" content="summary_large_image"/>
    <?php

        if (SITE_SOCIAL_TWITTER !== '') {
            echo '<meta property="twitter:site" content="@' . SITE_SOCIAL_TWITTER . '" />' . "\r\n";
        }
    ?>
	<meta property="twitter:domain" content="<?= BASE; ?>"/>
	<meta property="twitter:title" content="<?= $SEO->getTitle(); ?>"/>
	<meta property="twitter:description" content="<?= $SEO->getDescription(); ?>"/>
	<meta property="twitter:image" content="<?= $SEO->getImage(); ?>"/>
	<meta property="twitter:url" content="<?= BASE; ?>/<?= $getURL; ?>"/>

	<!-- favicon -->
	<link rel='shortcut icon' href='<?= INCLUDE_PATH; ?>/images/favicon.png'>
	<link rel='apple-touch-icon' href='<?= INCLUDE_PATH; ?>/images/apple-touch-icon-57x57.png'>
	<link rel='apple-touch-icon' sizes='72x72' href='<?= INCLUDE_PATH; ?>/images/apple-touch-icon-72x72.png'>
	<link rel='apple-touch-icon' sizes='114x114' href='<?= INCLUDE_PATH; ?>/images/apple-touch-icon-114x114.png'>

	<link rel='stylesheet' href='<?= BASE; ?>/assets/bootcss/reset.min.css'/>

    <?php
        // MAIN STYLE THEME
        /* if (file_exists('themes/' . THEME . '/assets/style.css')):
             echo '<link rel="stylesheet" href="' . INCLUDE_PATH . '/assets/css/style.css"/>' . "\r\n";
         endif;*/
    ?>

	<!--DORIPEL THEME CSS-->

	<!-- animation -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/animate.css'/>
	<!-- bootstrap -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/bootstrap.min.css'/>
	<!-- et line icon -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/et-line-icons.css'/>
	<!-- font-awesome icon -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/font-awesome.min.css'/>
	<!-- themify icon -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/themify-icons.css'>
	<!-- flaticon icon -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/flaticon.css'>
	<!-- swiper carousel -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/swiper.min.css'>
	<!-- justified gallery -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/justified-gallery.min.css'>
	<!-- magnific popup -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/magnific-popup.css'/>
	<!-- revolution slider -->
	<link rel='stylesheet' type='text/css' href='<?= INCLUDE_PATH; ?>/revolution/css/settings.css' media='screen'/>
	<link rel='stylesheet' type='text/css' href='<?= INCLUDE_PATH; ?>/revolution/css/layers.css'>
	<link rel='stylesheet' type='text/css' href='<?= INCLUDE_PATH; ?>/revolution/css/navigation.css'>
	<!-- bootsnav -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/bootsnav.css'>
	<!-- style -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/css/style.css'/>
	<!-- responsive css -->
	<link rel='stylesheet' href='<?= INCLUDE_PATH; ?>/wc_css/responsive.css'/>

	<!--ZEN THEME JS-->

	<!-- JS BASE ASSETS PROJECT -->
	<script src=" <?= BASE; ?>/assets/js/jquery.js"></script>
	<script src="<?= BASE; ?>/assets/js/workcontrol.min.js"></script>
	<script src='<?= BASE; ?>/assets/shadowbox/shadowbox.js'></script>

	<!-- Facebook Pixel Code -->
	<script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= SEGMENT_FB_PIXEL_ID; ?>');
        fbq('track', 'PageView');
	</script>
	<noscript>
		<img height="1" width="1" style="display:none"
		     src="https://www.facebook.com/tr?id=pixel_facebook&ev=PageView&noscript=1"/>
	</noscript>
	<!-- End Facebook Pixel Code -->

    <?php
        // GOOGLE ANALYTICS GA4 WITH DEFINE IN CONFIG
        if (SEGMENT_GL_ANALYTICS !== '' && SEGMENT_GL_ANALYTICS !== '0') {
            // Global site tag (gtag.js) - Google Analytics
            echo "<script async src='https://www.googletagmanager.com/gtag/js?id="
                . SEGMENT_GL_ANALYTICS . "'></script>";
            echo "<script>
					window.dataLayer = window.dataLayer || [];
						function gtag() { dataLayer.push(arguments); }
                            gtag('js', new Date());
                            gtag('config', '" . SEGMENT_GL_ANALYTICS . "'); "
                . (SEGMENT_GL_ADWORDS_ID !== '' ? "gtag('config', '" . SEGMENT_GL_ADWORDS_ID . "');" : '')
                . '</script>';
        }

        if (SEGMENT_GL_TAGMANAGER !== '' && SEGMENT_GL_TAGMANAGER !== '0') {
            // <!-- Google Tag Manager -->
            echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':"
                . " new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],"
                . " j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= "
                . "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })"
                . "(window,document,'script','dataLayer','" . SEGMENT_GL_TAGMANAGER . "');</script>";
            // <!-- End Google Tag Manager -->
        }
    ?>
</head>

<body data-scrolling-animations="true" class="">
<?php
    if (SEGMENT_GL_TAGMANAGER !== '' && SEGMENT_GL_TAGMANAGER !== '0') {
        // <!-- Google Tag Manager (noscript) -->
        echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='
            . SEGMENT_GL_TAGMANAGER
            . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
        // <!-- End Google Tag Manager (noscript) -->
    }
?>

<div id="texto">
    <?php
        // MESSAGE MAINTENANCE FOR ADMIN
        if (
            ADMIN_MAINTENANCE
            && !empty($_SESSION['userLogin']['user_level'])
            && $_SESSION['userLogin']['user_level'] >= 6
        ) {
            echo "<div class='workcontrol_maintenance'>&#x267A; O MODO de manutenção está ativo. "
                . 'Somente administradores podem ver o site assim &#x267A;</div>';
        }

        // REDIRECT PUBLIC TO MAINTENANCE
        if (
            ADMIN_MAINTENANCE
            && (empty($_SESSION['userLogin']['user_level'])
                || $_SESSION['userLogin']['user_level'] < 6)
        ) {
            require __DIR__ . '/maintenance.php';
        } else {
            // PESQUISA PRODUTOS
            $Search = filter_input_array(INPUT_POST);
            if ($Search && !empty($Search['p'])) {
                $Search = urlencode(strip_tags(trim($Search['p'])));
                header('Location: ' . BASE . '/pesquisa-produtos/' . $Search);

                exit;
            }

            // PESQUISA
            $Search = filter_input_array(INPUT_POST);
            if ($Search && !empty($Search['s'])) {
                $Search = urlencode(strip_tags(trim($Search['s'])));
                header('Location: ' . BASE . '/pesquisa/' . $Search);

                exit;
            }

            // LANDING_PAGES MODULE - LP
            $Customers = [];
            $Read->fullRead('SELECT page_name FROM ' . DB_LANDING_PAGES);
            if ($Read->getResult()) {
                foreach ($Read->getResult() as $SinglePage) {
                    $Customers[] = $SinglePage['page_name'];
                }
            }

            // TP
            $Tps = [];
            $Read->fullRead('SELECT page_name FROM ' . DB_THANKYOU_PAGES);
            if ($Read->getResult()) {
                foreach ($Read->getResult() as $SinglePage) {
                    $Tps[] = $SinglePage['page_name'];
                }
            }

            // LINKTREE
            $Cards = [];
            $Read->fullRead('SELECT carduser_url FROM ' . DB_CARD_USER);
            if ($Read->getResult()) {
                foreach ($Read->getResult() as $SinglePage) {
                    $Cards[] = $SinglePage['carduser_url'];
                }
            }

            if (in_array($URL[0], $Tps) && file_exists(REQUIRE_PATH . '/thankyou-page.php')) {
                if (file_exists(REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]))) {
                    require REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]);
                } else {
                    require REQUIRE_PATH . '/thankyou-page.php';
                }
            } elseif (in_array($URL[0], $Customers) && file_exists(REQUIRE_PATH . '/landing-page.php')) {
                if (file_exists(REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]))) {
                    require REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]);
                } else {
                    require REQUIRE_PATH . '/landing-page.php';
                }
                // END LANDING_PAGES MODULE
            } elseif (in_array($URL[0], $Cards) && file_exists(REQUIRE_PATH . '/cartao-de-contato.php')) {
                if (file_exists(REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]))) {
                    require REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]);
                } else {
                    require REQUIRE_PATH . '/cartao-de-contato.php';
                }
            } elseif (isset($_SESSION['WC_THEME'])) {
                // HEADER
                if (file_exists('themes/' . $_SESSION['WC_THEME'] . '/inc/header.php')) {
                    require 'themes/' . $_SESSION['WC_THEME'] . '/inc/header.php';
                } else {
                    trigger_error('Crie um arquivo /inc/header.php na pasta do tema!');
                }

                // CONTENT
                echo "<main class='main-wrapper oh'>";
                $URL[1] = (empty($URL[1]) ? null : $URL[1]);

                if (in_array($setURL, ['rss', 'feed', 'rss.xml'], true)) {
                    require __DIR__ . '/rss.php';

                    exit;
                }

                $Pages = [];
                $Read->fullRead('SELECT page_name FROM ' . DB_PAGES);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $SinglePage) {
                        $Pages[] = $SinglePage['page_name'];
                    }
                }

                if (in_array($URL[0], $Pages) && file_exists('themes/' . $_SESSION['WC_THEME'] . '/pagina.php')) {
                    if (file_exists('themes/' . $_SESSION['WC_THEME'] . sprintf('/page-%s.php', $URL[0]))) {
                        require 'themes/' . $_SESSION['WC_THEME'] . sprintf('/page-%s.php', $URL[0]);
                    } else {
                        require 'themes/' . $_SESSION['WC_THEME'] . '/pagina.php';
                    }
                } elseif (file_exists('themes/' . $_SESSION['WC_THEME'] . '/' . $URL[0] . '.php')) {
                    if (
                        'artigos' == $URL[0] && file_exists(
                            'themes/' . $_SESSION['WC_THEME'] . sprintf('/cat-%s.php', $URL[1])
                        )
                    ) {
                        require 'themes/' . $_SESSION['WC_THEME'] . sprintf('/cat-%s.php', $URL[1]);
                    } else {
                        require 'themes/' . $_SESSION['WC_THEME'] . '/' . $URL[0] . '.php';
                    }
                } elseif (file_exists('themes/' . $_SESSION['WC_THEME'] . '/' . $URL[0] . '/' . $URL[1] . '.php')) {
                    require 'themes/' . $_SESSION['WC_THEME'] . '/' . $URL[0] . '/' . $URL[1] . '.php';
                } elseif (file_exists('themes/' . $_SESSION['WC_THEME'] . '/404.php')) {
                    require 'themes/' . $_SESSION['WC_THEME'] . '/404.php';
                } else {
                    trigger_error(
                        'Não foi possível incluir o arquivo themes/'
                        . THEME . sprintf('/%s.php <b>(O arquivo 404 também não existe!)</b>', $getURL)
                    );
                }
                echo '</main>';

                // FOOTER
                if (file_exists('themes/' . $_SESSION['WC_THEME'] . '/inc/footer.php')) {
                    require 'themes/' . $_SESSION['WC_THEME'] . '/inc/footer.php';
                } else {
                    trigger_error('Crie um arquivo /inc/footer.php na pasta do tema!');
                }
            } else {
                // HEADER
                if (file_exists(REQUIRE_PATH . '/inc/header.php')) {
                    require REQUIRE_PATH . '/inc/header.php';
                } else {
                    trigger_error('Crie um arquivo /inc/header.php na pasta do tema!');
                }

                // CONTENT
                echo "<main class='main-wrapper oh'>";
                $URL[1] = (empty($URL[1]) ? null : $URL[1]);

                if (in_array($setURL, ['rss', 'feed', 'rss.xml'], true)) {
                    require __DIR__ . '/rss.php';

                    exit;
                }

                $Pages = [];
                $Read->fullRead('SELECT page_name FROM ' . DB_PAGES);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $SinglePage) {
                        $Pages[] = $SinglePage['page_name'];
                    }
                }

                if (in_array($URL[0], $Pages) && file_exists(REQUIRE_PATH . '/pagina.php')) {
                    if (file_exists(REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]))) {
                        require REQUIRE_PATH . sprintf('/page-%s.php', $URL[0]);
                    } else {
                        require REQUIRE_PATH . '/pagina.php';
                    }
                } elseif (file_exists(REQUIRE_PATH . '/' . $URL[0] . '.php')) {
                    if ('artigos' == $URL[0] && file_exists(REQUIRE_PATH . sprintf('/cat-%s.php', $URL[1]))) {
                        require REQUIRE_PATH . sprintf('/cat-%s.php', $URL[1]);
                    } else {
                        require REQUIRE_PATH . '/' . $URL[0] . '.php';
                    }
                } elseif (file_exists(REQUIRE_PATH . '/' . $URL[0] . '/' . $URL[1] . '.php')) {
                    require REQUIRE_PATH . '/' . $URL[0] . '/' . $URL[1] . '.php';
                } elseif (file_exists(REQUIRE_PATH . '/404.php')) {
                    require REQUIRE_PATH . '/404.php';
                } else {
                    trigger_error(
                        'Não foi possível incluir o arquivo themes/'
                        . THEME . sprintf('/%s.php <b>(O arquivo 404 também não existe!)</b>', $getURL)
                    );
                }
                echo '</main>';

                // FOOTER
                if (file_exists(REQUIRE_PATH . '/inc/footer.php')) {
                    require REQUIRE_PATH . '/inc/footer.php';
                } else {
                    trigger_error('Crie um arquivo /inc/footer.php na pasta do tema!');
                }
            }
        }

        // WC CODES
        $Read->exeRead(DB_WC_CODE);
        if ($Read->getResult()) {
            if (empty($Update)) {
                $Update = new Update();
            }

            $ActiveCodes = filter_input(INPUT_GET, 'url');
            echo "\r\n\r\n\r\n<!--JS Codes-->\r\n";
            foreach ($Read->getResult() as $HomeCodes) {
                if (empty($HomeCodes['code_condition'])) {
                    echo $HomeCodes['code_script'];
                    $UpdateCodes = ['code_views' => $HomeCodes['code_views'] + 1];
                    $Update->exeUpdate(
                        DB_WC_CODE,
                        $UpdateCodes,
                        'WHERE code_id = :id',
                        'id=' . $HomeCodes['code_id']
                    );
                } elseif (
                    preg_match(
                        '/' . str_replace('/', '\/', $HomeCodes['code_condition']) . '/',
                        $ActiveCodes
                    )
                ) {
                    echo $HomeCodes['code_script'];
                    $UpdateCodes = ['code_views' => $HomeCodes['code_views'] + 1];
                    $Update->exeUpdate(
                        DB_WC_CODE,
                        $UpdateCodes,
                        'WHERE code_id = :id',
                        'id=' . $HomeCodes['code_id']
                    );
                }
            }
            echo "\r\n<!--/JS Codes-->\r\n\r\n\r\n";
        }
        /*if (!empty(SEGMENT_FB_PIXEL_ID)) {
    require 'assets/wc_track.php';
    }*/
    ?>

</div>

<?php
    // MAIN SCRIPT THEME
    if (file_exists('themes/' . THEME . '/scripts.js')) {
        echo '<script src="' . INCLUDE_PATH . '/scripts.js"></script>' . "\r\n";
    }

?>

<!--ZEN THEME JS-->
<!-- components -->

<!-- start scroll to top -->
<a class='scroll-top-arrow' href='javascript:void(0);'><i class='ti-arrow-up'></i></a>
<!-- end scroll to top -->
<!-- javascript libraries -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/modernizr.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/bootstrap.min.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.easing.1.3.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/skrollr.min.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/smooth-scroll.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.appear.js'></script>
<!-- menu navigation -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/bootsnav.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.nav.js'></script>
<!-- animation -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/wow.min.js'></script>
<!-- page scroll -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/page-scroll.js'></script>
<!-- swiper carousel -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/swiper.min.js'></script>
<!-- counter -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.count-to.js'></script>
<!-- parallax -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.stellar.js'></script>
<!-- magnific popup -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.magnific-popup.min.js'></script>
<!-- portfolio with shorting tab -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/isotope.pkgd.min.js'></script>
<!-- images loaded -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/imagesloaded.pkgd.min.js'></script>
<!-- pull menu -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/classie.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/hamburger-menu.js'></script>
<!-- counter -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/counter.js'></script>
<!-- fit video -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.fitvids.js'></script>
<!-- equalize -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/equalize.min.js'></script>
<!-- skill bars -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/skill.bars.jquery.js'></script>
<!-- justified gallery -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/justified-gallery.min.js'></script>
<!--pie chart-->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/jquery.easypiechart.min.js'></script>
<!-- instagram -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/instafeed.min.js'></script>
<!-- retina -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/wc_js/retina.min.js'></script>
<!-- revolution -->
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/revolution/js/jquery.themepunch.tools.min.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/revolution/js/jquery.themepunch.revolution.min.js'></script>
<!-- revolution slider extensions (load below extensions JS files only on local file systems to make the slider work! The following part can be removed on server for on demand loading) -->
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.actions.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.carousel.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.kenburn.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.layeranimation.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.migration.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.navigation.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.parallax.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.slideanims.min.js'></script>
<script type='text/javascript'
        src='<?= INCLUDE_PATH; ?>/revolution/js/extensions/revolution.extension.video.min.js'></script>
<script type='text/javascript' src='<?= INCLUDE_PATH; ?>/js/main.js'></script>
<!--ZEN THEME JS-->

<!--ACCESS-->
<?php
    //require __DIR__ . '/assets/widgets/accessibility/accessibility.inc.php';
?>

</body>

</html>
<?php

    ob_end_flush();

    if (!file_exists('.htaccess')) {
        $htaccesswrite = "RewriteEngine On\r\nOptions All -Indexes\r\n\r\n"
            . "# WC WWW Redirect.\r\n#RewriteCond %{HTTP_HOST} !^www\\. [NC]\r\n"
            . "#RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n\r\n"
            . "# WC HTTPS Redirect\r\nRewriteCond %{HTTP:X-Forwarded-Proto} !https\r\n"
            . "RewriteCond %{HTTPS} off\r\nRewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n\r\n"
            . "# WC URL Rewrite\r\nRewriteCond %{SCRIPT_FILENAME} !-f\r\n"
            . "RewriteCond %{SCRIPT_FILENAME} !-d\r\nRewriteRule ^(.*)$ index.php?url=$1";
        $htaccess = fopen('.htaccess', 'w');
        fwrite($htaccess, str_replace("'", '"', $htaccesswrite));
        fclose($htaccess);
    }
?>
