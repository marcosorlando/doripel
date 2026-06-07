<?php
use App\Conn\Create;
use App\Conn\Read;

// AUTO INSTANCE OBJECT READ
if (empty($Read)) {
    $Read ??= new Read();
}

// AUTO INSTANCE OBJECT CREATE
if (empty($Create)) {
    $Create ??= new Create();
}

//$BrandId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
//if ($BrandId) {
//    $Read->exeRead(DB_PDT_BRANDS_DORIPEL, "WHERE brand_id = :id", "id={$BrandId}");
//    if ($Read->getResult()) {
//        $FormData = array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0]);
//        extract($FormData);
//    } else {
//        $_SESSION['trigger_controll'] = Check::erro("<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um fabricante que não existe ou que foi removido recentemente!",
//          E_USER_NOTICE);
//      //  header('Location: dashboard.php?wc=products/brands');
//        exit;
//    }
//} else {
//    $Date = date('Y-m-d H:i:s');
//    $Title = "Novo Fabricante - {$Date}";
//    $Name = Check::name($Title);
//    $CarCreate = ['brand_name' => $Name, 'brand_created' => $Date];
//    $Create->exeCreate(DB_PDT_BRANDS_DORIPEL, $CarCreate);
//   // header('Location: dashboard.php?wc=products/brand&id=' . $Create->getResult());
//    exit;
//}
?>
<!---->
<!--<header class="dashboard_header">-->
<!--  <div class="dashboard_header_title">-->
<!--    <h1 class="icon-price-tags">--><? //= $brand_title ? $brand_title : 'Nova Marca ou Fabricante'; ?><!--</h1>-->
<!--    <p class="dashboard_header_breadcrumbs">-->
<!--      &raquo; --><? //= ADMIN_NAME; ?>
<!--      <span class="crumb">/</span>-->
<!--      <a title="--><? //= ADMIN_NAME; ?><!--" href="dashboard.php?wc=home">Dashboard</a>-->
<!--      <span class="crumb">/</span>-->
<!--      <a title="--><? //= ADMIN_NAME; ?><!--" href="dashboard.php?wc=products/home">Produtos</a>-->
<!--      <span class="crumb">/</span>-->
<!--      <a title="--><? //= ADMIN_NAME; ?><!--" href="dashboard.php?wc=products/brands">Fabricantes</a>-->
<!--      <span class="crumb">/</span>-->
<!--      Gerenciar Marca/Fabricante-->
<!--    </p>-->
<!--  </div>-->
<!---->
<!--  <div class="dashboard_header_search">-->
<!--    <a title="Ver Fabricantes!" href="dashboard.php?wc=products/brands" class="btn btn_blue icon-eye">Ver-->
<!--      Fabricantes!</a>-->
<!--    <a title="Novo Fabricantes" href="dashboard.php?wc=products/brand" class="btn btn_green icon-plus">Adicionar-->
<!--      Fabricante!</a>-->
<!--  </div>-->
<!---->
<!--</header>-->

<div class="dashboard_content">
  <div class="box box100">

    <div class="panel_header default">
      <h2 class="icon-price-tags">Dados da Marca/Fabricante</h2>
    </div>
    <div class="panel">

      <form class="auto_save" name="pdt_import_add" action="" method="post" enctype="multipart/form-data">
        <div class="callback_return"></div>
        <input type="hidden" name="callback" value="ProductsDoripel"/>
        <input type="hidden" name="callback_action" value="pdt_import"/>

        <label class="label">
          <span class="legend">Escolher arquivo para importar (XML):</span>
          <input style="font-size: 1.5em;" type="file" name="arquivo" required/>
          <img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
        </label>

        <div class="m_top">&nbsp;</div>

        <button class="btn btn_green icon-price-tags fl_right">IMPORTAR!</button>
        <div class="clear"></div>
      </form>
    </div>
  </div>
</div>
