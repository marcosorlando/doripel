<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

\session_start();

require __DIR__.'/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_CONFIG_API;

$jSON = [];
if (!isset($_SESSION['userLogin']['user_level']) || (int) $_SESSION['userLogin']['user_level'] < (int) $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo \json_encode($jSON);

    exit;
}

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = [];
$CallBack = 'Api';
$PostData = \filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
if (!\is_array($PostData)) {
    $PostData = [];
}

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();
    // AUTO INSTANCE OBJECT UPDATE
    $Update ??= new Update();
    // AUTO INSTANCE OBJECT DELETE
    $Delete ??= new Delete();

    // SELECIONA AÇÃO
    switch ($Case) {
        // STATS
        case 'create':
            if (!empty($PostData['api_key']) && \mb_strlen($PostData['api_key']) > 8) {
                $CreateAPP = [
                    'api_key' => $PostData['api_key'],
                    'api_token' => \base64_encode(\time().'wc'.$PostData['api_key']),
                    'api_date' => \date('Y-m-d H:i:s'),
                    'api_status' => 1,
                    'api_loads' => 0,
                    'api_lastload' => \date('Y-m-d H:i:s'),
                ];
                $Create->exeCreate(DB_WC_API, $CreateAPP);
                if ($Create->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        \sprintf(
                            '<b>TUDO CERTO:</b> O APP <b>%s</b> foi criado com sucesso e já pode consumir dados no ',
                            $PostData['api_key']
                        ).ADMIN_NAME.'! <b>Aguarde...</b>'
                    );
                    $jSON['redirect'] = 'dashboard.php?wc=config/wcapi';
                }
            } else {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CRIAR APP:</b> Desculpe, mas não é seguro criar uma key com menos de 8 caracteres!',
                    E_USER_WARNING
                );
            }

            break;

            // ACTIVE ACESS
        case 'active':
            $Api = $PostData['id'];
            $UpdateApi = ['api_status' => '1'];
            $Update->exeUpdate(DB_WC_API, $UpdateApi, 'WHERE api_id = :id', 'id='.$Api);
            $jSON['active'] = 1;

            break;

            // REMVOE ACESS
        case 'inactive':
            $Api = $PostData['id'];
            $UpdateApi = ['api_status' => '0'];
            $Update->exeUpdate(DB_WC_API, $UpdateApi, 'WHERE api_id = :id', 'id='.$Api);
            $jSON['active'] = 0;

            break;

            // REMOVE APP
        case 'delete':
            $Api = $PostData['del_id'];
            $Delete->exeDelete(DB_WC_API, 'WHERE api_id = :id', 'id='.$Api);
            $jSON['success'] = true;

            break;

        case 'license':
            /*
* ATENÇÃO: PARA SUA SEGURANÇA NÃO ALTERE ESSE GATILHO
* E SEMPRE LICENCIE SEUS DOMÍNIOS AO COLOCALOS ONLINE!
*/
            if (\in_array('', $PostData, true)) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>LICENSE KEY:</b> Para licenciar um domínio é preciso informar seus dados e a chave da licença!',
                    E_USER_WARNING
                );
            } else {
                \set_error_handler(function ($severity, $message, $file, $line) {
                    throw new ErrorException($message, $severity, $severity, $file, $line);
                });

                try {
                    $hash = \hash('sha512', (string) $PostData['user_password']);
                    $licenceUrl = \sprintf(
                        'https://download.workcontrol.com.br?k=%s&u=%s&p=%s&v=%s&d=%s',
                        $PostData['licene_key'],
                        $PostData['user_email'],
                        $hash,
                        ADMIN_VERSION,
                        \urlencode(BASE)
                    );
                    $PostLicence = \file_get_contents($licenceUrl);
                    if (false === $PostLicence) {
                        throw new RuntimeException('Falha ao consultar servidor de licença.');
                    }

                    $resultLicence = \json_decode($PostLicence);

                    if (!empty($resultLicence->trigger)) {
                        $jSON['trigger'] = Check::ajaxErro(
                            '<b>ERROR:</b> '.$resultLicence->trigger,
                            E_USER_ERROR
                        );
                    } else {
                        // CREATE LICENCE JSON
                        $LicenceFile = \fopen('../dashboard.json', 'w');
                        if (false !== $LicenceFile) {
                            \fwrite($LicenceFile, (string) $PostLicence);
                            \fclose($LicenceFile);
                        }

                        \chmod('../dashboard.json', 0755);
                        \copy('../dashboard.json', '../_js/workcontrol.json');
                        \copy('../dashboard.json', '../_js/tinymce/tinymce.json');

                        $jSON['trigger'] = Check::ajaxErro(
                            '<span>Licença gerada com sucesso!</span>'
                        );
                        $jSON['redirect'] = 'dashboard.php?wc=config/license';
                    }
                } catch (Exception) {
                    // var_dump($e);
                    $jSON['trigger'] = Check::ajaxErro(
                        '<b>ERRO:</b> Desculpe, mas não foi possível comunicar com download.workcontro.com.br.<p>Favor tente mais tarde!</p>',
                        E_USER_ERROR
                    );
                }

                \restore_error_handler();
            }

            break;
    }

    // RETORNA O CALLBACK
    if ([] !== $jSON) {
        echo \json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo \json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
