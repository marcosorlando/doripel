<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

session_start();

// Carrega autoload do Composer, que inclui o Config.inc.php via autoload (files)
require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = 6;

if (empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Dashboard';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)) {
        $Read = new Read();
    }

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)) {
        $Create = new Create();
    }

    // AUTO INSTANCE OBJECT UPDATE
    if (empty($Update)) {
        $Update = new Update();
    }

    // AUTO INSTANCE OBJECT DELETE
    if (empty($Delete)) {
        $Delete = new Delete();
    }

    // LICENCE CHECK
    if (file_exists('../dashboard.json')) {
        $LicenseFile = file_get_contents('../dashboard.json');
        $LicenseDomain = json_decode($LicenseFile);

        if (empty($LicenseDomain->license_auth_date) || empty($LicenseDomain->license_hash)) {
            unlink('../dashboard.json');

            exit;
        }

        if (!empty($LicenseDomain->license_auth_date)) {
            $DateNow = new DateTime();
            $DatePing = new DateTime($LicenseDomain->license_auth_date);
            $DateDiff = $DateNow->diff($DatePing)->days;

            if ($DateDiff >= 5) {
                set_error_handler(function ($severity, $message, $file, $line) {

                    throw new ErrorException($message, $severity, $severity, $file, $line);
                });

                try {
                    $PostLicence = file_get_contents(
                        sprintf(
                            'https://download.workcontrol.com.br?h=%s&d=',
                            $LicenseDomain->license_hash
                        ) . urlencode(BASE)
                    );
                    $resultLicence = json_decode($PostLicence);

                    if (!empty($resultLicence->trigger)) {
                        $_SESSION['trigger_controll'] = $resultLicence->trigger;
                        unlink('../dashboard.json');
                    } else {
                        // UPDATE LICENSE
                        $LicenseUpdate = str_replace(
                            '"license_auth_date":"' . $LicenseDomain->license_auth_date . '"',
                            '"license_auth_date":"' . date('Y-m-d H:i:s') . '"',
                            $LicenseFile
                        );
                        chmod('../dashboard.json', 0755);
                        $LicenseFile = fopen('../dashboard.json', 'w+');
                        fwrite($LicenseFile, $LicenseUpdate);
                        fclose($LicenseFile);
                        chmod('../dashboard.json', 0644);
                    }
                } catch (Exception) {
                    // ERROR HANDLER
                }

                restore_error_handler();
            }
        }
    }

    // SELECIONA AÇÃO
    switch ($Case) {
        // WC LOGIN FIX
        case 'wc_login_fix':
            if (!empty($_SESSION['userLogin']) && $_SESSION['userLogin']['user_level'] >= 6) {
                $Read->exeRead(DB_USERS, 'WHERE user_id = :user', 'user=' . $_SESSION['userLogin']['user_id']);
                if ($Read->getResult() && $Read->getResult()[0]['user_level'] >= 6) {
                    $_SESSION['userLogin'] = $Read->getResult()[0];
                    $jSON['login'] = true;
                } else {
                    unset($_SESSION['userLogin']);
                    $_SESSION['trigger_login'] = Check::ajaxErro(
                        "<div class='al_center icon-warning'>Sua sessão expirou ou você não tem permissão para acessar o painel!</div>",
                        E_USER_ERROR
                    );
                    $jSON['redirect'] = BASE . '/admin';
                }
            } else {
                unset($_SESSION['userLogin']);
                $_SESSION['trigger_login'] = Check::ajaxErro(
                    "<div class='al_center icon-warning'>Sua sessão expirou ou você não tem permissão para acessar o painel!</div>",
                    E_USER_ERROR
                );
                $jSON['redirect'] = BASE . '/admin';
            }

            break;

        // STATS
        case 'siteviews':
            $Read->fullRead(
                'SELECT count(online_id) AS total from ' . DB_VIEWS_ONLINE . ' WHERE online_endview >= NOW()'
            );
            $jSON['useron'] = str_pad((string)$Read->getResult()[0]['total'], 4, 0, STR_PAD_LEFT);

            $Read->exeRead(DB_VIEWS_VIEWS, 'WHERE views_date = date(NOW())');
            if (!$Read->getResult()) {
                $jSON['users'] = '0000';
                $jSON['views'] = '0000';
                $jSON['pages'] = '0000';
                $jSON['stats'] = '0.00';
            } else {
                $Views = $Read->getResult()[0];
                $Stats = number_format($Views['views_pages'] / $Views['views_views'], 2, '.', '');
                $jSON['users'] = str_pad((string)$Views['views_users'], 4, 0, STR_PAD_LEFT);
                $jSON['views'] = str_pad((string)$Views['views_views'], 4, 0, STR_PAD_LEFT);
                $jSON['pages'] = str_pad((string)$Views['views_pages'], 4, 0, STR_PAD_LEFT);
                $jSON['stats'] = $Stats;
            }

            /*  $Read->fullRead(
                  'SELECT COUNT(online_id) AS TotalOnline FROM ' . DB_VIEWS_ONLINE . ' WHERE online_endview >= NOW() AND online_user IN(SELECT user_id FROM ' . DB_EAD_ENROLLMENTS . ')'
              );*/

            $Read->fullRead(
                'SELECT COUNT(online_id) AS TotalOnline FROM ' . DB_VIEWS_ONLINE . ' WHERE online_endview >= NOW()'
            );
            $jSON['students'] = str_pad((string)$Read->getResult()[0]['TotalOnline'], 4, 0, 0);

            break;

        case 'onlinenow':
            $Where = '';
            $ParseString = '';

            if (!empty($PostData['user'])) {
                $Where = 'AND online_user = :user';
                $ParseString = 'user=' . $PostData['user'];
            }

            if (!empty($PostData['url'])) {
                $Where = 'AND online_url = :url';
                $ParseString = 'url=' . $PostData['url'];
            }

            $Read->exeRead(
                DB_VIEWS_ONLINE,
                sprintf('WHERE online_endview >= NOW() %s ORDER BY online_endview DESC', $Where),
                $ParseString
            );
            if (!$Read->getResult()) {
                $jSON['data'] = Check::erro('Não existem usuárion online neste momento!', E_USER_NOTICE);
                $jSON['data'] .= '<div class="clear"></div>';
                $jSON['now'] = '0000';
            } else {
                $i = 0;
                $jSON['data'] = null;
                $jSON['now'] = str_pad((string)$Read->getRowCount(), 4, 0, 0);
                foreach ($Read->getResult() as $Online) {
                    ++$i;
                    $Name = ($Online['online_name'] ? "<a href='dashboard.php?wc=" . (APP_EAD ? 'teach/students_gerent' : 'users/create') . sprintf(
                            "&id=%s' title='Ver Cliente'>%s</a>",
                            $Online['online_user'],
                            $Online['online_name']
                        ) : 'guest user');
                    $Date = date('d/m/Y H\hi', strtotime((string)$Online['online_startview']));
                    $jSON['data'] .= "<div class='single_onlinenow'>
                    <p>" . str_pad($i, 4, 0, STR_PAD_LEFT) . "</p>
                    <p><a href='" . BASE . "/admin/dashboard.php?wc=onlinenow&user={$Online['online_user']}' class='btn btn_green btn_small icon-notext icon-filter'></a> {$Name}</p>
                    <p>{$Date}</p>
                    <p>{$Online['online_ip']}</p>
                    <p><a href='" . BASE . sprintf(
                            "/admin/dashboard.php?wc=onlinenow&url=%s' class='btn btn_green btn_small icon-notext icon-filter'></a> <a target='_blank' href='",
                            $Online['online_url']
                        ) . BASE . sprintf(
                            "/%s' title='Ver Destino'>",
                            $Online['online_url']
                        ) . ($Online['online_url'] ? $Online['online_url'] : 'home') . '</a></p>
                    </div>';
                }
            }

            break;
    }

    // RETORNA O CALLBACK
    if ($jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
