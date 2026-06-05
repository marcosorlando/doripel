<?php

declare(strict_types=1);

require_once __DIR__.'/../../../vendor/autoload.php';

use App\Conn\Read;
use App\Helpers\Check;

$read = new Read();

$adminLevelRequired = 10;
$adminId = \filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT);

if (null === $adminId || false === $adminId || $adminId < $adminLevelRequired) {
    exit(
        '<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;">'
        .'<b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!'
        .'</div>'
    );
}

$read->exeRead(DB_LEADS, 'ORDER BY lead_name DESC');

if (!$read->getResult()) {
    echo Check::erro('<span>Ainda não existem conversões para esse material!</span>', E_USER_NOTICE);

    exit;
}

$filename = 'base-de-leads.csv';

\header('Content-Type: text/csv; charset=UTF-8');
\header('Content-Disposition: attachment; filename="'.$filename.'"');
\header('Cache-Control: max-age=0');
\header('Pragma: public');
\header('Expires: 0');

$output = \fopen('php://output', 'wb');

if (false === $output) {
    throw new RuntimeException('Não foi possível iniciar o processo de exportação.');
}

// Adiciona BOM para compatibilidade com o Excel.
\fwrite($output, "\xEF\xBB\xBF");

$columns = ['Nome', 'E-mail', 'Profissão', 'Cidade', 'Conversão', 'Data'];
\fputcsv($output, $columns, ';');

foreach ($read->getResult() as $lead) {
    $leadName = isset($lead['lead_name']) ? Check::getCapilalize((string) $lead['lead_name']) : '';
    $leadEmail = isset($lead['lead_email']) ? \strtolower((string) $lead['lead_email']) : '';
    $leadProfession = isset($lead['lead_job_title']) ? Check::getCapilalize(
        (string) $lead['lead_job_title']
    ) : '';
    $leadCity = isset($lead['lead_city']) ? Check::getCapilalize((string) $lead['lead_city']) : '';
    $leadConversion = isset($lead['lead_conversion']) ? Check::getCapilalize((string) $lead['lead_conversion']) : '';

    $leadDate = '';
    if (!empty($lead['lead_date'])) {
        try {
            $leadDateTime = new DateTimeImmutable((string) $lead['lead_date']);
            $leadDate = $leadDateTime->format('d/m/Y');
        } catch (Exception) {
            // Mantém a data vazia caso não seja possível convertê-la.
        }
    }

    \fputcsv(
        $output,
        [
            $leadName,
            $leadEmail,
            $leadProfession,
            $leadCity,
            $leadConversion,
            $leadDate,
        ],
        ';'
    );
}

\fclose($output);

exit;
