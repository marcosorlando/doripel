<?php

use App\Conn\Create;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

$AdminLevel = LEVEL_WC_THANKYOU_PAGES;
if (!APP_THANKYOU_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
    exit('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não está logado <br>ou não tem permissão para acessar essa página!</div>');
}

// AUTO INSTANCE OBJECT READ
$Read ??= new Read();
// AUTO INSTANCE OBJECT CREATE
$Create ??= new Create();
// AUTO INSTANCE OBJECT UPDATE
$Update ??= new Update();

$PageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$Read->exeRead(DB_THANKYOU_PAGES, 'WHERE page_id = :id', 'id=' . $PageId);

if ($Read->getResult()) {
    $PageCreate = $Read->getResult()[0];
}

unset($PageCreate['page_id'], $PageCreate['page_pdf']);

$PageCreate['page_parent'] = $PageId;
$PageCreate['page_date'] = date('Y-m-d H:i:s');
$PageCreate['page_status'] = 0;

$Create->exeCreate(DB_THANKYOU_PAGES, $PageCreate);

$PAGECRTUPDATE = ['page_name' => Check::name($PageCreate['page_title']) . ('-' . $Create->getResult())];
$Update->exeUpdate(DB_THANKYOU_PAGES, $PAGECRTUPDATE, 'WHERE page_id = :id', 'id=' . $Create->getResult());

header('Location: dashboard.php?wc=thankyoupages/create&id=' . $Create->getResult());

exit;
