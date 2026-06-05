<?php

use App\Conn\Read;
use App\Helpers\Check;
use App\Helpers\DateHelper;

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../_app/Config.inc.php";

$Read = new Read();

$Admin = filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT);
$AdminLevel = defined('LEVEL_WC_LEADS') ? (int) LEVEL_WC_LEADS : 10;

if (null === $Admin || false === $Admin || $Admin < $AdminLevel) {
    exit('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
}

$Read->exeRead(DB_LEADS, "ORDER BY lead_name DESC");

if (!$Read->getResult()) {
    echo Check::erro('<span>Ainda não existem conversões para esse material!</span>', E_USER_NOTICE);
    exit;
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="base-de-leads-doripel.csv"');
header('Cache-Control: max-age=0');
header('Pragma: public');
header('Expires: 0');

$Output = fopen('php://output', 'wb');
if ($Output === false) {
    exit('Não foi possível gerar o arquivo de exportação.');
}

fwrite($Output, "\xEF\xBB\xBF");
fputcsv($Output, ['Nome', 'E-mail', 'Profissão', 'Cidade', 'Conversão', 'Data'], ';', '"', '');

foreach ($Read->getResult() as $Lead) {
    fputcsv($Output, [
        Check::getCapilalize((string) ($Lead['lead_name'] ?? '')),
        strtolower((string) ($Lead['lead_email'] ?? '')),
        Check::getCapilalize((string) ($Lead['lead_job_title'] ?? '')),
        Check::getCapilalize((string) ($Lead['lead_city'] ?? '')),
        Check::getCapilalize((string) ($Lead['lead_conversion'] ?? '')),
        !empty($Lead['lead_date']) ? DateHelper::human((string) $Lead['lead_date'], 'dd/MM/yyyy') : '',
    ], ';', '"', '');
}

fclose($Output);
exit;
