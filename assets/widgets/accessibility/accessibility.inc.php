<?php

echo "
    <link rel='stylesheet' href='" . BASE . "/assets/widgets/accessibility/css/buttons.css'/>
     <link rel='stylesheet' href='" . BASE . "/assets/widgets/accessibility/css/highcontrast.css'/>
      <link rel='stylesheet' href='" . BASE . "/assets/widgets/accessibility/css/daltonism.css'/> ";
?>
<ul class="accessibility-buttons">


    <!--CONTRAST: HIGTHCONTRAST / DALTONISM / RESET-->
    <li class="higth-contrast">
        <a title="ALTO CONTRASTE" class="link-alto-contraste btn-lat-rigth" data-class="contrast">
            <span class="fa fa-adjust"></span>
            <span class="btn-text">CONTRASTE</span>
        </a>
    </li>
    <!--<li class="daltonism-contrast">
        <a title="Adaptar site para Daltonicos" class="link-alto-contraste btn-lat-rigth" data-class="daltonism">
            <span class="fa fa-low-vision"></span>
            <span class="btn-text">DALTÔNISMO</span>
        </a>
    </li>-->
    <li class="contrast-default">
        <a title="Resetar - Voltar as cores padrão" class="link-alto-contraste btn-lat-rigth" data-class="">
            <span class="fa fa-recycle"></span>
            <span class="btn-text">RESTAURAR CORES</span>
        </a>
    </li>
    <!-- /.higth-contrast -->
    <!--ZOOM IN / OUT / RESET-->
    <li class="access-font-size-a-plus no-zoomm">
        <a class="btn-lat-rigth" id="In" accesskey="A" title="Aumenta 10%">
            <span class="fa fa-search-plus"></span>
            <span class="btn-text">AUMENTAR ZOOM</span>
        </a>
    </li>
    <li class="access-font-size-a-minus">
        <a class="btn-lat-rigth" id="Out" accesskey="D" title="Diminui 10%">
            <span class="fa fa-search-minus"></span>
            <span class="btn-text">DIMINUIR ZOOM</span>
        </a>
    </li>
    <li class="access-font-size-a-reset">
        <a class="btn-lat-rigth" id="Rst" accesskey="R" title="Restaurar tamanho normal">
            <span class="fa fa-search-location"></span>
            <span class="btn-text">RESTAURAR ZOOM</span>
        </a>
    </li>
</ul>

<!--VLIBRAS - LINGUA BRASILEIRA DE SINAIS-->
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>

<script src="<?= BASE; ?>/assets/widgets/accessibility/js-cookie-latest/src/js.cookie.js"></script>
<script src="<?= BASE; ?>/assets/widgets/accessibility/js/scripts.js"></script>
<script src="<?= BASE; ?>/assets/widgets/accessibility/js/textzoom.js"></script>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
