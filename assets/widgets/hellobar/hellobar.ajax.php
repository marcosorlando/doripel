<?php

require __DIR__ . '/../../../vendor/autoload.php';

use App\Conn\Read;
use App\Conn\Update;

ob_start();
session_start();

if (empty($_SESSION['wc_hello'])) {
    $_SESSION['wc_hello'] = [];
}

$getPost = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$Read = new Read();
if (!empty($getPost)) {
    if (!empty($getPost['action']) && 'track' == $getPost['action']) {
        $HelloId = $getPost['hello'];

        $Read->fullRead('SELECT hello_clicks FROM ' . DB_HELLO . ' WHERE hello_id = :id', 'id=' . $HelloId);
        if ($Read->getResult()) {
            $UpdateHello = ['hello_clicks' => $Read->getResult()[0]['hello_clicks'] + 1];
            $Update = new Update();
            $Update->exeUpdate(DB_HELLO, $UpdateHello, 'WHERE hello_id = :id', 'id=' . $HelloId);

            setcookie('wc_hello', $HelloId, time() + 604800, '/');
        }
    } elseif (!empty($getPost['url'])) {
        $HelloUrl = str_replace(BASE, '', $getPost['url']);
        $HelloArr = array_filter(explode('/', $HelloUrl));
        $HelloClear = array_map('strip_tags', $HelloArr);
        $HelloKeys = "'" . implode("','', ", array_map('strval', (array)$HelloClear)) . "'";

        $WhereSession = null;
        if (!empty($_SESSION['wc_hello'])) {
            $WhereSession = "hello_id NOT IN('" . implode(
                    "','",
                    array_map('strval', (array)($_SESSION['wc_hello'] ?? []))
                ) . "') AND";
        }

        $getCookie = filter_input(INPUT_COOKIE, 'wc_hello', FILTER_VALIDATE_INT);
        $WhereCookie = null;
        if ($getCookie) {
            $WhereCookie = sprintf('hello_id != %s AND', $getCookie);
        }

        $Read->fullRead(
            'SELECT '
            . 'h.* '
            . 'FROM ' . DB_HELLO . ' h '
            . sprintf(
                'WHERE %s %s (hello_rule IN(%s) OR hello_rule IS NULL) ',
                $WhereSession,
                $WhereCookie,
                $HelloKeys
            )
            . 'AND hello_start <= NOW() AND hello_end >= NOW() AND hello_status = 1 '
            . 'LIMIT 1'
        );

        if ($Read->getResult()) {
            extract($Read->getResult()[0]);

            $UpdateHello = ['hello_views' => $hello_views + 1];
            $Update = new Update();
            $Update->exeUpdate(DB_HELLO, $UpdateHello, 'WHERE hello_id = :id', 'id=' . $hello_id);

            $jSON['hello'] = sprintf("<div class='wc_hellobar wc_hellobar_%s' id='%s'>", $hello_position, $hello_id)
                . "<div class='wc_hellobar_box'>"
                . sprintf("<span id='%s' class='wc_hellobar_close'>X</span>", $hello_id)
                . "<img src='" . BASE . sprintf('/tim.php?src=uploads/%s&w=', $hello_image) . IMAGE_W / 2 . sprintf(
                    "' alt='%s' title='%s'/>",
                    $hello_title,
                    $hello_title
                )
                . "<div class='wc_hellobar_box_content'>"
                . "<div class='wc_hellobar_box_content_btn'>"
                . sprintf(
                    "<a href='%s' class='btn_cta_%s wc_hellobar_cta' title='Clique para fazer o Download' id='%s'>%s</a>",
                    $hello_link,
                    $hello_color,
                    $hello_id,
                    $hello_cta
                )
                . '</div>'
                . sprintf('<p>%s</p>', $hello_title)
                . '</div>' // wc_hellobar_box_content
                . '</div>' // wc_hellobar_box
                . '</div>';

            $_SESSION['wc_hello'][$hello_id] = $hello_id;
            $jSON['hello_position'] = $hello_position;
            echo json_encode($jSON);
        }
    }
}

ob_end_flush();
