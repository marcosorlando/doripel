<?php

use App\Conn\Create;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

$AdminLevel = LEVEL_WC_PRODUCTS_DORIPEL;
if (!APP_PRODUCTS_DORIPEL || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
    Check::accessBlocked();
}

// AUTO INSTANCE OBJECT READ
if (empty($Read)) {
    $Read ??= new Read();
}

// AUTO INSTANCE OBJECT CREATE
if (empty($Create)) {
    $Create ??= new Create();
}

// AUTO INSTANCE OBJECT UPDATE
if (empty($Update)) {
    $Update ??= new Update();
}

$PdtId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$Read->exeRead(DB_PDT_DORIPEL, "WHERE pdt_id = :id", "id={$PdtId}");
if (!$Read->getResult()) {
    $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou criar uma variação de um produto que não existe ou que foi removido recentemente!";
    header('Location: dashboard.php?wc=products/home');
    exit;
}

$ProductCreate = $Read->getResult()[0];

//var_dump($ProductCreate);

unset($ProductCreate['pdt_id'], $ProductCreate['pdt_cover'], $ProductCreate['pdt_scene']);

$ProductCreate['pdt_parent'] = $PdtId;
$ProductCreate['pdt_created'] = date('Y-m-d H:i:s');
$ProductCreate['pdt_status'] = 0;
$Create->exeCreate(DB_PDT_DORIPEL, $ProductCreate);

$PDTCRTUPDATE = ['pdt_name' => Check::name($ProductCreate['pdt_title']) . "-{$Create->getResult()}", 'pdt_code' => str_pad($Create->getResult(), 13, 0, STR_PAD_LEFT)];
$Update->exeUpdate(DB_PDT_DORIPEL, $PDTCRTUPDATE, "WHERE pdt_id = :id", "id={$Create->getResult()}");

header("Location: dashboard.php?wc=products/create&id={$Create->getResult()}");
exit;
