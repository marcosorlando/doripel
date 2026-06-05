<?php

// Classe PHPExcel carregada pelo AutoLoader
include_once "../../../_app/Library/PHPExcel/Classes/PHPExcel.php";
include_once "../../../_app/Config.inc.php";
include_once "../../../_app/Conn/Read.class.php";
$Read = new Read;

$Admin = filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT);
$AdminLevel = 10;
if (empty($Admin) || $Admin < $AdminLevel):
	die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

// Instanciamos a classe
$objPHPExcel = new PHPExcel();
// Definimos o estilo da fonte
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);

// Criamos as colunas
$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Nome')
				->setCellValue('B1', 'E-mail ')
				->setCellValue('C1', 'Profissão')
				->setCellValue('D1', 'Cidade')
				->setCellValue('E1', 'Conversão')
				->setCellValue('F1', 'Data');

// Adicionamos um estilo de A1 até F1
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(
				array('fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'E0EEEE')
						),
				)
);
// Podemos configurar diferentes larguras paras as colunas como padrão
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

//// Preenchimento de dados de maneira dinâmica, a partir de um resultado do banco de dados

$Read->ExeRead(DB_LEADS, "ORDER BY lead_name DESC");
if (!$Read->getResult()):
	echo Erro("<span class='icon-info al_center'>Ainda não existem conversões para esse material!</span>", E_USER_NOTICE);
else:

    //var_dump($Read->getResult());

	$linha = 2;
	foreach ($Read->getResult() as $Lead):
		$Lead['lead_name'] = ucwords($Lead['lead_name']);
		$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A" . $linha, $Lead['lead_name'])
						->setCellValue("B" . $linha, $Lead['lead_email'])
						->setCellValue("C" . $linha, $Lead['lead_job_title'])
						->setCellValue("D" . $linha, $Lead['lead_city'])
						->setCellValue("E" . $linha, $Lead['lead_conversion'])
						->setCellValue("F" . $linha, Date('d/m/Y', strtotime($Lead['lead_date'])));
		$linha++;

	endforeach;
endif;

// Podemos renomear o nome das planilha atual, lembrando que um único arquivo pode ter várias planilhas
$objPHPExcel->getActiveSheet()->setTitle('Conversões');

// Cabeçalho do arquivo para ele baixar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="base-de-leads-doripel.csv"');
header('Cache-Control: max-age=0');
// Se for o IE9, isso talvez seja necessário
header('Cache-Control: max-age=1');

// Acessamos o 'Writer' para poder salvar o arquivo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

// Salva diretamente no output, poderíamos mudar arqui para um nome de arquivo em um diretório ,caso não quisessemos jogar na tela
$objWriter->save('php://output');
exit;
