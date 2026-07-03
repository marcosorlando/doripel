<?php

    use App\Helpers\Check;

    $base = BASE;
    $siteName = SITE_NAME;
    $siteTwitter = SITE_SOCIAL_TWITTER;

    if (empty($WcSocialRequired)) {
        $WcSocialRequired = true;
        echo "<link rel='stylesheet' href='{$base}/assets/widgets/share/share.wc.css'/>";
    }

    echo "<ul class='workcontrol_socialshare'>";
    echo "<li class='workcontrol_socialshare_cta'><strong>Compartilhe</strong> </li>";

    $WcShareText = empty($WC_TITLE_LINK) ? '' : $WC_TITLE_LINK;
    $WcShareLink = empty($WC_SHARE_LINK) ? $base : $WC_SHARE_LINK;
    $WcShareHash = empty($WC_SHARE_HASH) ? Check::name($siteName) : $WC_SHARE_HASH;

// Facebook
    $shareIconText = 'Compartilhar no Facebook';
    echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_facebook'><a rel='{$WcShareLink}' target='_blank' title='{$shareIconText}' href='https://www.facebook.com/sharer/sharer.php?u={$WcShareLink}'><img alt='{$shareIconText}' title='{$shareIconText}' src='{$base}/assets/widgets/share/icons/facebook.svg'/></a></li>";

// Whatsapp
    $whatsText = Check::safeUrlEncode(
        "*Oie!* Estou lendo este artigo no Blog da {$siteName} e resolvi compartilhar com você! Um excelente conteúdo com certeza você vai gostar. *Clique para ler!*"
    );
    $shareIconText = 'Compartilhar no Whatsapp';
    echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_whatsapp'><a rel='{$WcShareLink}' target='_blank' title='{$shareIconText}' href='https://api.whatsapp.com/send?text={$whatsText} {$WcShareLink}'><img alt='{$shareIconText}' title='{$shareIconText}' src='{$base}/assets/widgets/share/icons/whatsapp.svg'/></a></li>";

// Linkedin
    $shareIconText = 'Compartilhar no Linkedin';
    echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_linkedin'><a rel='{$WcShareLink}' target='_blank' title='{$shareIconText}' href='https://www.linkedin.com/cws/share?xd_origin_host={$WcShareLink}&amp;original_referer={$WcShareLink}&amp;url={$WcShareLink}&amp;isFramed=false&amp;token=&amp;lang=pt_BR&amp;_ts=1482238060107%2E67#state=&amp;from_login=true'><img alt='{$shareIconText}' title='{$shareIconText}' src='{$base}/assets/widgets/share/icons/linkedin.svg'/></a></li>";

// Twitter
    $WcShareText = Check::safeUrlEncode($WcShareText);
    $shareIconText = 'Compartilhar no Twitter';
    echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_twitter'><a rel='{$WcShareLink}' target='_blank' title='{$shareIconText}' href='https://twitter.com/intent/tweet?url={$WcShareLink}&text={$WcShareText}&via={$siteTwitter}'><img alt='{$shareIconText}' title='{$shareIconText}' src='{$base}/assets/widgets/share/icons/twitter.svg'/></a></li>";

// E-mail
    $shareIconText = 'Compartilhar por E-mail';
    echo "<li class='workcontrol_socialshare_item workcontrol_socialshare_mail'><a rel='{$WcShareLink}' target='_blank' title='{$shareIconText}' href='mailto:?to=&amp;&subject=Leia o artigo: {$WcShareText}&body=Estou lendo o artigo {$WC_TITLE_LINK} no Blog da {$siteName} e o conteúdo está excelente acho que você vai gostar, para ler acesse {$WcShareLink}'><img alt='{$shareIconText}' title='{$shareIconText}' src='{$base}/assets/widgets/share/icons/envelope.svg'/></a></li>";

    echo '</ul>';
