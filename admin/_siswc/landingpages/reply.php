<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;

    /**
     * Created by PhpStorm.
     * User: marcosmoreira
     * Date: 11/04/19
     * Time: 15:17.
     */
    $AdminLevel = LEVEL_WC_LANDING_PAGES;
    if (!APP_LANDING_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

// AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
// AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();
// AUTO INSTANCE OBJECT UPDATE
    $Update ??= new Update();

    $PageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $Read->exeRead(DB_LANDING_PAGES, 'WHERE page_id = :id', 'id=' . $PageId);

    if ($Read->getResult()) {
        $PageCreate = $Read->getResult()[0];
    }

    unset($PageCreate['page_id']);

    $PageCreate['page_parent'] = $PageId;
    $PageCreate['page_date'] = date('Y-m-d H:i:s');
    $PageCreate['page_status'] = 0;

    $Create->exeCreate(DB_LANDING_PAGES, $PageCreate);

    $PAGECRTUPDATE = ['page_name' => Check::name($PageCreate['page_title']) . ('-' . $Create->getResult())];
    $Update->exeUpdate(DB_LANDING_PAGES, $PAGECRTUPDATE, 'WHERE page_id = :id', 'id=' . $Create->getResult());

    header('Location: dashboard.php?wc=landingpages/create&id=' . $Create->getResult());

    exit;
