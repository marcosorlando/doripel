<?php

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';

use App\Conn\Read;
use App\Conn\Update;

// AUTO INSTANCE OBJECT READ
$Read = new Read();

$ApiKey = filter_input(INPUT_GET, 'key', FILTER_DEFAULT);
$ApiToken = filter_input(INPUT_GET, 'token', FILTER_DEFAULT);
$Error = [];

if (!empty($ApiKey) && !empty($ApiToken)) {
    $Read->exeRead(
        DB_WC_API,
        'WHERE api_key = :key AND api_token = :token AND api_status = 1',
        sprintf('key=%s&token=%s', $ApiKey, $ApiToken)
    );
    if (!$Read->getResult()) {
        $Error['error'] = 'Acesso negado ao APP!';
        echo json_encode($Error);
    } else {
        $ApiLoadUpdate = [
            'api_loads' => $Read->getResult()[0]['api_loads'] + 1,
            'api_lastload' => date('Y-m-d H:i:s'),
        ];
        $Update = new Update();
        $Update->exeUpdate(DB_WC_API, $ApiLoadUpdate, 'WHERE api_id = :id', 'id=' . $Read->getResult()[0]['api_id']);
        $jSON = null;

        // VARS
        $Limit = (filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ? filter_input(
            INPUT_GET,
            'limit',
            FILTER_VALIDATE_INT
        ) : 5);
        $Offset = (filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT) ? filter_input(
            INPUT_GET,
            'offset',
            FILTER_VALIDATE_INT
        ) : 0);
        $CatId = (filter_input(INPUT_GET, 'catid', FILTER_VALIDATE_INT) ? 'AND post_category = ' . filter_input(
                INPUT_GET,
                'catid',
                FILTER_VALIDATE_INT
            ) : null);
        $Order = (filter_input(INPUT_GET, 'order', FILTER_VALIDATE_INT) ? filter_input(
            INPUT_GET,
            'order',
            FILTER_VALIDATE_INT
        ) : null);
        $By = (filter_input(INPUT_GET, 'by', FILTER_VALIDATE_INT) ? filter_input(
            INPUT_GET,
            'order',
            FILTER_VALIDATE_INT
        ) : 'post_date');

        switch ($By) {
            case 2:
                $By = 'post_views';

                break;

            default:
                $By = 'post_date';
        }

        // SET ORDER
        $Order = match ($Order) {
            1 => sprintf('ORDER BY %s ASC', $By),
            2 => sprintf('ORDER BY %s DESC', $By),
            3 => 'ORDER BY RAND()',
            default => sprintf('ORDER BY %s DESC', $By),
        };

        $Read = new Read();
        $Read->exeRead(
            DB_POSTS,
            sprintf('WHERE post_status = 1 %s %s LIMIT :limit OFFSET :offset', $CatId, $Order),
            sprintf('limit=%s&offset=%d', $Limit, $Offset)
        );
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $REST) {
                unset($REST['post_content'], $REST['post_status'], $REST['post_type']);

                // LINKS
                $REST['post_name'] = BASE . ('/artigo/' . $REST['post_name']);
                $REST['post_cover'] = BASE . ('/uploads/' . $REST['post_cover']);

                // ANCHOR DATES
                // Author
                $Read->fullRead(
                    'SELECT user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_id = :id',
                    'id=' . $REST['post_author']
                );
                $REST['post_author'] = ($Read->getResult() ? sprintf(
                    '%s %s',
                    $Read->getResult()[0]['user_name'],
                    $Read->getResult()[0]['user_lastname']
                ) : $REST['post_author']);
                // Category
                $Read->fullRead(
                    'SELECT category_title, category_name FROM ' . DB_CATEGORIES . ' WHERE category_id = :id',
                    'id=' . $REST['post_category']
                );
                if ($Read->getResult()) {
                    $CategoryId = $REST['post_category'];
                    $REST['post_category'] = [];
                    $REST['post_category']['category_id'] = $CategoryId;
                    $REST['post_category']['category_title'] = (empty(
                    $Read->getResult()[0]['category_title']
                    ) ? null : $Read->getResult()[0]['category_title']);
                    $REST['post_category']['category_link'] = (empty(
                    $Read->getResult()[0]['category_name']
                    ) ? null : BASE . ('/artigos/' . $Read->getResult()[0]['category_name']));
                }

                // Category Parents
                $CategoryParent = explode(',', (string)$REST['post_category_parent']);
                $REST['post_category_parent'] = [];
                foreach ($CategoryParent as $Cat) {
                    $Read->fullRead(
                        'SELECT category_title, category_name FROM ' . DB_CATEGORIES . ' WHERE category_id = :id',
                        'id=' . $Cat
                    );
                    if ($Read->getResult()) {
                        $CategoryData = $Read->getResult()[0];
                        $REST['post_category_parent'][$Cat] = [
                            'category_id' => $Cat,
                            'category_title' => $CategoryData['category_title'],
                            'category_name' => BASE . ('/artigos/' . $CategoryData['category_name']),
                        ];
                    }
                }

                $jSON[] = $REST;
            }

            echo json_encode($jSON, JSON_PRETTY_PRINT);
        } else {
            return false;
        }
    }
} else {
    $Error['error'] = 'Informar dados de acesso!';
    echo json_encode($Error, JSON_PRETTY_PRINT);
}
