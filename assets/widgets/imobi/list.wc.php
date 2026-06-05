<?php

use App\Helpers\Check;

$WcImobiFilter = (empty($_SESSION['wc_imobi_filter']) ? null : $_SESSION['wc_imobi_filter']);
unset($WcImobiFilter['min_price'], $WcImobiFilter['max_price'], $WcImobiFilter['realty_bedrooms']);

$FilterAdd = null;
$FilterValues = (empty($WcImobiFilter) ? null : \http_build_query($WcImobiFilter));

if ($WcImobiFilter) {
    foreach ($WcImobiFilter as $fKey => $fValue) {
        $FilterAdd .= \sprintf(' AND %s = :%s', $fKey, $fKey);
    }
}

$BedRooms = (empty($_SESSION['wc_imobi_filter']['realty_bedrooms']) ? '' : \sprintf(
    "AND realty_bedrooms >= '%s'",
    $_SESSION['wc_imobi_filter']['realty_bedrooms']
));
$MinPrice = (empty($_SESSION['wc_imobi_filter']['min_price']) ? '' : \sprintf(
    "AND realty_price >= '%s'",
    $_SESSION['wc_imobi_filter']['min_price']
));
$MaxPrice = (empty($_SESSION['wc_imobi_filter']['max_price']) ? '' : \sprintf(
    "AND realty_price <= '%s'",
    $_SESSION['wc_imobi_filter']['max_price']
));

$Page = (empty($URL[1]) ? 1 : $URL[1]);
$Pager = new Pager(BASE.'/filtro/', '<<', '>>', 3);
$Pager->exePager($Page, 12);
$Read->exeRead(
    DB_IMOBI,
    \sprintf(
        'WHERE realty_status = 1 %s %s %s %s LIMIT :limit OFFSET :offset',
        $FilterAdd,
        $BedRooms,
        $MinPrice,
        $MaxPrice
    ),
    \sprintf('%s&limit=%d&offset=%d', $FilterValues, $Pager->getLimit(), $Pager->getOffset())
);

if ($Read->getResult()) {
    foreach ($Read->getResult() as $IMOBI) {
        \extract($IMOBI);
        $BOX = 3;

        require REQUIRE_PATH.'/inc/realty.php';
    }

    $Pager->exePaginator(
        DB_IMOBI,
        \sprintf('WHERE realty_status = 1 %s %s %s %s', $FilterAdd, $BedRooms, $MinPrice, $MaxPrice),
        $FilterValues
    );
    echo $Pager->getPaginator();
} else {
    $Pager->returnPage();
    echo Check::erro(
        "<div style='text-align: center'>Desculpe, mas não encontramos imóveis cadastrados nos termos desta consulta!</div>",
        E_USER_NOTICE
    );
}
