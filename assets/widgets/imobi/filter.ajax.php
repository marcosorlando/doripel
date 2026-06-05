<?php

\session_start();

$getPost = \filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (empty($getPost) || empty($getPost['workcontrol'])) {
    exit('Acesso Negado!');
}

$strPost = \array_map('strip_tags', $getPost);
$POST = \array_map('trim', $strPost);

$Action = $POST['workcontrol'];
$jSON = null;
unset($POST['workcontrol']);

require __DIR__.'/../../../vendor/autoload.php';
$Read = new Read();
$Create = new Create();
$Update = new Update();

switch ($Action) {
    case 'transaction':
        $jSON['type'] = null;
        $_SESSION['wc_imobi_filter'] = [];
        $_SESSION['wc_imobi_filter']['realty_transaction'] = $POST['transaction'];

        $Read->fullRead(
            'SELECT realty_type FROM '.DB_IMOBI.' WHERE realty_transaction = :tra GROUP BY realty_type ORDER BY realty_type ASC',
            'tra='.$POST['transaction']
        );
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $TRA) {
                $jSON['type'] .= \sprintf("<option value='%s'>", $TRA['realty_type']).\getWcRealtyType(
                    $TRA['realty_type']
                ).'</option>';
            }
        }

        break;

    case 'type':
        $jSON['finality'] = null;
        $_SESSION['wc_imobi_filter']['realty_type'] = $POST['type'];

        $Read->fullRead(
            'SELECT realty_finality FROM '.DB_IMOBI.' WHERE realty_transaction = :tra AND realty_type = :typ GROUP BY realty_finality ORDER BY realty_finality ASC',
            \sprintf('tra=%s&typ=%s', $POST['transaction'], $POST['type'])
        );
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $TRA) {
                $jSON['finality'] .= \sprintf("<option value='%s'>", $TRA['realty_finality']).\getWcRealtyFinality(
                    $TRA['realty_finality']
                ).'</option>';
            }
        }

        break;

    case 'finality':
        $jSON['district'] = null;
        $_SESSION['wc_imobi_filter']['realty_finality'] = $POST['finality'];

        $Read->fullRead(
            'SELECT realty_district FROM '.DB_IMOBI.' WHERE realty_transaction = :tra AND realty_type = :typ AND realty_finality = :fin GROUP BY realty_district ORDER BY realty_district ASC',
            \sprintf('tra=%s&typ=%s&fin=%s', $POST['transaction'], $POST['type'], $POST['finality'])
        );
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $TRA) {
                $jSON['district'] .= \sprintf(
                    "<option value='%s'>%s</option>",
                    $TRA['realty_district'],
                    $TRA['realty_district']
                );
            }
        }

        break;

    case 'district':
        $jSON['bedrooms'] = null;
        $_SESSION['wc_imobi_filter']['realty_district'] = $POST['district'];

        $Read->fullRead(
            'SELECT realty_bedrooms FROM '.DB_IMOBI.' WHERE realty_transaction = :tra AND realty_type = :typ AND realty_finality = :fin AND realty_district = :dis GROUP BY realty_bedrooms ORDER BY realty_bedrooms ASC',
            \sprintf(
                'tra=%s&typ=%s&fin=%s&dis=%s',
                $POST['transaction'],
                $POST['type'],
                $POST['finality'],
                $POST['district']
            )
        );
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $TRA) {
                $jSON['bedrooms'] .= \sprintf(
                    "<option value='%s'>A partir de %s quarto(s)</option>",
                    $TRA['realty_bedrooms'],
                    $TRA['realty_bedrooms']
                );
            }
        }

        break;

    case 'bedrooms':
        $jSON['min_price'] = null;
        $_SESSION['wc_imobi_filter']['realty_bedrooms'] = $POST['bedrooms'];

        $Read->fullRead(
            'SELECT realty_price FROM '.DB_IMOBI.' WHERE realty_transaction = :tra AND realty_type = :typ AND realty_finality = :fin AND realty_district = :dis AND realty_bedrooms = :bed ORDER BY realty_price DESC LIMIT 1',
            \sprintf(
                'tra=%s&typ=%s&fin=%s&dis=%s&bed=%s',
                $POST['transaction'],
                $POST['type'],
                $POST['finality'],
                $POST['district'],
                $POST['bedrooms']
            )
        );
        $MaxPrice = (empty($Read->getResult()[0]['realty_price']) ? $POST['min_price'] : $Read->getResult(
        )[0]['realty_price']);
        for ($Min = 100; $Min < $MaxPrice; $Min *= 10) {
            $jSON['min_price'] .= \sprintf("<option value='%s'>A partir de R\$ ", $Min).\number_format(
                $Min,
                '2',
                ',',
                '.'
            ).'</option>';
        }

        break;

    case 'min_price':
        $jSON['max_price'] = null;
        $_SESSION['wc_imobi_filter']['min_price'] = $POST['min_price'];

        $Read->fullRead(
            'SELECT realty_price FROM '.DB_IMOBI.' WHERE realty_transaction = :tra AND realty_type = :typ AND realty_finality = :fin AND realty_district = :dis AND realty_bedrooms = :bed AND realty_price >= :prc ORDER BY realty_price ASC LIMIT 1',
            \sprintf(
                'tra=%s&typ=%s&fin=%s&dis=%s&bed=%s&prc=%s',
                $POST['transaction'],
                $POST['type'],
                $POST['finality'],
                $POST['district'],
                $POST['bedrooms'],
                $POST['min_price']
            )
        );
        $MinPrice = (empty($Read->getResult()[0]['realty_price']) ? $POST['min_price'] : $Read->getResult(
        )[0]['realty_price']);
        for ($Min = 10000000; $Min > $MinPrice; $Min /= 10) {
            $jSON['max_price'] .= \sprintf("<option value='%s'>Até R\$ ", $Min).\number_format(
                $Min,
                '2',
                ',',
                '.'
            ).'</option>';
        }

        break;

    case 'max_price':
        $_SESSION['wc_imobi_filter']['max_price'] = $POST['max_price'];

        break;
}

if (!([] === $jSON || [] === $jSON || null === $jSON)) {
    echo \json_encode($jSON);
}
