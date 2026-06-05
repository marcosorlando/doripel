<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PRODUCTS;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

// AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

// AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

// AUTO INSTANCE OBJECT UPDATE
    if (empty($Update)) {
        $Update = new Update();
    }

    $PdtId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $Read->exeRead(DB_PDT, 'WHERE pdt_id = :id', 'id=' . $PdtId);
    $ProductCrete = $Read->getResult()[0];

    unset($ProductCrete['pdt_id'], $ProductCrete['pdt_cover']);

    $ProductCrete['pdt_parent'] = $PdtId;
    $ProductCrete['pdt_created'] = \date('Y-m-d H:i:s');
    $ProductCrete['pdt_inventory'] = 0;
    $ProductCrete['pdt_delivered'] = 0;
    $ProductCrete['pdt_status'] = 0;
    $Create->exeCreate(DB_PDT, $ProductCrete);

    $PDTCRTUPDATE = [
        'pdt_name' => Check::name($ProductCrete['pdt_title']) . ('-' . $Create->getResult()),
        'pdt_code' => \str_pad((string)$Create->getResult(), 4, 0, STR_PAD_LEFT),
    ];
    $Update->exeUpdate(DB_PDT, $PDTCRTUPDATE, 'WHERE pdt_id = :id', 'id=' . $Create->getResult());

    \header('Location: dashboard.php?wc=products/create&id=' . $Create->getResult());
