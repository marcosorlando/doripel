<?php

use App\Helpers\Check;

if (empty($WcSocialRequired)) {
    $WcSocialRequired = true;
    echo "<link rel='stylesheet' href='" . BASE . "/assets/widgets/share/share.wc.css'/>";
}

echo "<ul class='workcontrol_socialshare'>";
echo "<li class='workcontrol_socialshare_cta'><strong>Compartilhe</strong> esse post</li>";

$WcShareText = (empty($WC_TITLE_LINK) ? null : $WC_TITLE_LINK);
$WcShareLink = (empty($WC_SHARE_LINK) ? BASE : $WC_SHARE_LINK);
$WcShareHash = (empty($WC_SHARE_HASH) ? Check::name(SITE_NAME) : $WC_SHARE_HASH);

/**
 * FACEBOOK.
 */
$ShareIconText = 'Compartilhar no Facebook';
echo \sprintf(
        "<li class='workcontrol_socialshare_item workcontrol_socialshare_facebook'><a rel='%s' target='_blank' title='%s' href='https://www.facebook.com/sharer/sharer.php?u=%s'><img alt='%s' title='%s' src='",
        $WcShareLink,
        $ShareIconText,
        $WcShareLink,
        $ShareIconText,
        $ShareIconText
    ) . BASE . "/assets/widgets/share/icons/facebook.svg'/></a></li>";

/**
 * Whatsapp +.
 */
$whatsText = Check::safeUrlEncode(
    '*Oie!* Estou lendo este artigo no Blog da ' . SITE_NAME . ' e resolvi compartilhar com você! Um excelente conteúdo com certeza você vai gostar. *Clique para ler!*'
);
$ShareIconText = 'Compartilhar no Whatsapp';
echo \sprintf(
        "<li class='workcontrol_socialshare_item workcontrol_socialshare_whatsapp'><a rel='%s' target='_blank' title='%s' href='https://api.whatsapp.com/send?text=%s %s'><img alt='%s' title='%s' src='",
        $WcShareLink,
        $ShareIconText,
        $whatsText,
        $WcShareLink,
        $ShareIconText,
        $ShareIconText
    ) . BASE . "/assets/widgets/share/icons/whatsapp.svg'/></a></li>";

/**
 * Linkedin.
 */
$ShareIconText = 'Compartilhar no Linkedin';
echo \sprintf(
        "<li class='workcontrol_socialshare_item workcontrol_socialshare_linkedin'><a rel='%s' target='_blank' title='%s' href='https://www.linkedin.com/cws/share?xd_origin_host=%s&amp;original_referer=%s&amp;url=%s&amp;isFramed=false&amp;token=&amp;lang=pt_BR&amp;_ts=1482238060107%%2E67#state=&amp;from_login=true'><img alt='%s' title='%s' src='",
        $WcShareLink,
        $ShareIconText,
        $WcShareLink,
        $WcShareLink,
        $WcShareLink,
        $ShareIconText,
        $ShareIconText
    ) . BASE . "/assets/widgets/share/icons/linkedin.svg'/></a></li>";

/**
 * E-MAIL.
 */
$ShareIconText = 'Compartilhar por E-mail';
echo \sprintf(
        "<li class='workcontrol_socialshare_item workcontrol_socialshare_mail'><a rel='%s' target='_blank' title='%s' href='mailto:?to=&amp;&subject=Leia o artigo: %s&body=Estou lendo o artigo %s no Blog da ",
        $WcShareLink,
        $ShareIconText,
        $WcShareText,
        $WcShareText
    ) . SITE_NAME . \sprintf(
        " e o conteúdo está excelente acho que você vai gostar, para ler acesse %s'><img alt='%s' title='%s' src='",
        $WcShareLink,
        $ShareIconText,
        $ShareIconText
    ) . BASE . "/assets/widgets/share/icons/envelope.svg'/></a></li>";

echo '</ul>';
