<!-- GTranslate: https://gtranslate.io/ -->
<!--<a href="#" onclick="doGTranslate('pt|es');return false;" title="Spanish" class="gflag nturl" style="background-position:-600px -200px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="Spanish"/></a>
<a href="#" onclick="doGTranslate('pt|ja');return false;" title="Japan" class="gflag nturl" style="background-position:-700px -100px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="Japan"/></a>
<a href="#" onclick="doGTranslate('pt|en');return false;" title="English" class="gflag nturl" style="background-position:-0px -0px;">
    <img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="English"/></a>
<a href="#" onclick="doGTranslate('pt|fr');return false;" title="French" class="gflag nturl" style="background-position:-200px -100px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="French"/></a>
<a href="#" onclick="doGTranslate('pt|de');return false;" title="German" class="gflag nturl" style="background-position:-300px -100px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="German"/></a>
<a href="#" onclick="doGTranslate('pt|it');return false;" title="Italian" class="gflag nturl" style="background-position:-600px -100px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="Italian"/></a>
<a href="#" onclick="doGTranslate('pt|pt');return false;" title="Portuguese" class="gflag nturl" style="background-position:-300px -200px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="Portuguese"/></a>
<a href="#" onclick="doGTranslate('pt|ru');return false;" title="Russian" class="gflag nturl" style="background-position:-500px -200px;"><img src="//gtranslate.net/flags/blank.png" height="32" width="32" alt="Russian"/></a>-->

<div class="absolute-flags">

    <!-- <a href="#" onclick="doGTranslate('pt|pt');return false;" title="Portuguese" class="gflag nturl">
        <span class="icon-eye text-white">IDIOMA</span>
     </a>-->

    <a href="#" onclick="doGTranslate('pt|pt');return false;" title="Portuguese" class="gflag nturl">
        <img src="<?php
        echo BASE; ?>/assets/gtranslate/country-flags-main/svg/br.svg" alt="Portugues"/>
    </a>

    <a href="#" onclick="doGTranslate('pt|es');return false;" title="Spanish" class="gflag nturl">
        <img src="<?php
        echo BASE; ?>/assets/gtranslate/country-flags-main/svg/es.svg" alt="Espanhol"/>
    </a>

    <a href="#" onclick="doGTranslate('pt|ja');return false;" title="Japan" class="gflag nturl">
        <img src="<?php
        echo BASE; ?>/assets/gtranslate/country-flags-main/svg/jp.svg" alt="Japonês"/>
    </a>

    <a href="#" onclick="doGTranslate('pt|en');return false;" title="English" class="gflag nturl">
        <img src="<?php
        echo BASE; ?>/assets/gtranslate/country-flags-main/svg/sh.svg" alt="Inglês"/>
    </a>
</div>
<style> <!--
    .absolute-flags {
        position: relative;
        width: 100%;
        align-items: center;
        text-align: right;
        margin-top: 5px;
        margin-right: 5px;
        z-index: 9999;
    }

    a.gflag {
        color: #333333;
        vertical-align: middle;
        line-height: 20px;
        padding: 0;
        text-decoration: none;
    }

    a .gflag span, a.gflag span:before {
        margin-right: 3px
    }

    a.gflag img {
        border: 0;
        height: 20px;
        width: auto;
        box-shadow: -1px -1px 2px rgba(0, 0, 0, .1);
        opacity: 0.9;
    }

    a.gflag img:hover {
        border: 0;
        box-shadow: 1px 1px 3px rgba(0, 0, 0, .2);
        opacity: 1;
    }

    #goog-gt-tt {
        display: none !important;
    }

    .goog-te-banner-frame {
        display: none !important;
    }

    .goog-te-menu-value:hover {
        text-decoration: none !important;
    }

    body {
        top: 0 !important;
    }

    #google_translate_element2, .skiptranslate {
        display: none !important;
    }

    --></style>

<div id="google_translate_element2"></div>
<script type="text/javascript">
    function googleTranslateElementInit2() {
        new google.translate.TranslateElement({
            pageLanguage: 'pt',
            autoDisplay: false
        }, 'google_translate_element2');
    }
</script>
<script type="text/javascript"
        src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
<script type="text/javascript">
    /* <![CDATA[ */
    eval(function (p, a, c, k, e, r) {
        e = function (c) {
            return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
        };
        if (!''.replace(/^/, String)) {
            while (c--) r[e(c)] = k[c] || e(c);
            k = [function (e) {
                return r[e]
            }];
            e = function () {
                return '\\w+'
            };
            c = 1
        }
        ;
        while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
        return p
    }('6 7(a,b){n{4(2.9){3 c=2.9("o");c.p(b,f,f);a.q(c)}g{3 c=2.r();a.s(\'t\'+b,c)}}u(e){}}6 h(a){4(a.8)a=a.8;4(a==\'\')v;3 b=a.w(\'|\')[1];3 c;3 d=2.x(\'y\');z(3 i=0;i<d.5;i++)4(d[i].A==\'B-C-D\')c=d[i];4(2.j(\'k\')==E||2.j(\'k\').l.5==0||c.5==0||c.l.5==0){F(6(){h(a)},G)}g{c.8=b;7(c,\'m\');7(c,\'m\')}}', 43, 43, '||document|var|if|length|function|GTranslateFireEvent|value|createEvent||||||true|else|doGTranslate||getElementById|google_translate_element2|innerHTML|change|try|HTMLEvents|initEvent|dispatchEvent|createEventObject|fireEvent|on|catch|return|split|getElementsByTagName|select|for|className|goog|te|combo|null|setTimeout|500'.split('|'), 0, {}))
    /* ]]> */
</script>
