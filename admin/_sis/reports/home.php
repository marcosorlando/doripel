<?php

    use App\Conn\Read;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_REPORTS;
    if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $Search = \filter_input_array(INPUT_POST);

    // GET DATES
    $StartDate = (empty($_SESSION['wc_report_date'][0]) ? \date('Y-m-01') : $_SESSION['wc_report_date'][0]);
    $EndDate = (empty($_SESSION['wc_report_date'][1]) ? \date('Y-m-d') : $_SESSION['wc_report_date'][1]);

    // DEFAULT REPORT
    $DateStart = new DateTime($StartDate);
    $DateEnd = new DateTime(\date('Y-m-d', \strtotime($EndDate . '+1day')));
    $DateInt = new DateInterval('P1D');
    $DateInterval = new DatePeriod($DateStart, $DateInt, $DateEnd);
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pie-chart">Relatório de Acessos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>Relatório de Acessos
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Recarregar Relatórios" href="dashboard.php?wc=reports/home"
		   class="btn btn_blue icon-spinner11 icon-notext"> Mês Vigente</a>
	</div>
</header>

<div class="dashboard_content">
	<article class="box box100">
		<div class="panel">
			<div class="wc_ead_chart_control">
				<div class="wc_ead_chart_range">
					<form name="class_add" action="" method="post" enctype="multipart/form-data">
						<input type="hidden" name="callback" value="Reports"/>
						<input type="hidden" name="callback_action" value="get_report"/>
						<input type="hidden" name="report_back" value="reports/home"/>

						<label class="wc_ead_chart_range_picker">
							<span>DE:</span><input readonly="readonly"
							                       value="<?php
                                                       echo \date('d/m/Y', \strtotime((string)$StartDate)); ?>"
							                       name="start_date" type="text" data-language="pt-BR"
							                       class="jwc_datepicker_start"/>
						</label><label class="wc_ead_chart_range_picker">
							<span>ATÉ:</span><input readonly="readonly"
							                        value="<?php
                                                        echo \date('d/m/Y', \strtotime((string)$EndDate)); ?>"
							                        name="end_date" type="text" data-language="pt-BR"
							                        class="jwc_datepicker_end"/>
						</label>
						<button class="btn icon-spinner11 icon-notext"></button>
						<img class="form_load" alt="Enviando Requisição!" title="Enviando Requisição!"
						     src="_img/load.gif"/>
					</form>
				</div>
				<div class="wc_ead_chart_change">
					<span class="icon icon-stats-bars icon-notext jwc_chart_change jwc_area_chart btn btn_blue btn_green"></span>
					<span class="icon icon-stats-bars2 icon-notext jwc_chart_change jwc_column_chart btn btn_blue"></span>
					<span class="icon icon-stats-dots icon-notext jwc_chart_change jwc_line_chart btn btn_blue"></span>
				</div>
			</div>
			<div id="jwc_chart_container"></div>

            <?php
                // GET VIEWS
                $Read->fullRead(
                    'SELECT SUM(views_users) AS viewUsers, SUM(views_views) AS viewViews, SUM(views_pages) AS viewPages FROM ' . DB_VIEWS_VIEWS . ' WHERE views_date >= :start AND views_date <= :end',
                    \sprintf('start=%s&end=%s', $StartDate, $EndDate)
                );
                $viewUsers = $Read->getResult()[0]['viewUsers'];
                $viewViews = $Read->getResult()[0]['viewViews'];
                $viewsPages = $Read->getResult()[0]['viewPages'];
            ?>
			<div class="wc_ead_reports_boxes">
				<div class="box box33 wc_ead_reports_total">
					<div class="box_content">
						<p class="icon-users"><?php
                                echo \str_pad((string)$viewUsers, 3, 0, 0); ?></p>
						<span>Usuários Únicos</span>
					</div>
				</div>
				<div class="box box33 wc_ead_reports_total">
					<div class="box_content">
						<p class="icon-stats-dots"><?php
                                echo \str_pad((string)$viewViews, 3, 0, 0); ?></p>
						<span>Visitas Únicas</span>
					</div>
				</div>
				<div class="box box33 wc_ead_reports_total">
					<div class="box_content">
						<p class="icon-pie-chart"><?php
                                echo $viewViews >= 1 ? \number_format(
                                    $viewsPages / $viewViews,
                                    1,
                                    '.',
                                    ''
                                ) : 0; ?></p>
						<span>Páginas por Visita</span>
					</div>
				</div>
			</div>

			<footer class="wc_ead_reports">
                <?php
                    $getPage = \filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                    $Page = $getPage ?? 1;
                    $Pager = new Pager('dashboard.php?wc=reports/home&page=', '<', '>', 3);
                    $Pager->exePager($Page, 12);
                    $Read->fullRead(
                        'SELECT year(views_date) as AccessYear, Month(views_date) as AccessMonth, SUM(views_users) AS viewUsers, SUM(views_views) AS viewViews, SUM(views_pages) AS viewPages FROM ' . DB_VIEWS_VIEWS . " GROUP BY DATE_FORMAT(views_date,'%Y-%m') ORDER BY views_date DESC LIMIT :limit OFFSET :offset",
                        \sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
                    );
                    if (!$Read->getResult()) {
                        $Pager->returnPage();
                    } else {
                        foreach ($Read->getResult() as $AccessReport) {
                            ?>
							<article class="wc_ead_reports_single">
								<h1 class="row icon-calendar">
                                    <?php
                                        echo \str_pad(
                                                (string)$AccessReport['AccessMonth'],
                                                2,
                                                0,
                                                0
                                            ) . ('/' . $AccessReport['AccessYear']); ?>
								</h1>
								<p class="row icon-users">
                                    <?php
                                        echo \str_pad((string)$AccessReport['viewUsers'], 3, 0, 0); ?> Usuários
								</p>
								<p class="row icon-stats-dots">
                                    <?php
                                        echo \str_pad((string)$AccessReport['viewViews'], 3, 0, 0); ?> Visitas
								</p>
								<p class="row icon-pie-chart">
                                    <?php
                                        echo $AccessReport['viewViews'] >= 1 ? \number_format(
                                            $AccessReport['viewPages'] / $AccessReport['viewViews'],
                                            1,
                                            '.',
                                            ''
                                        ) : '0'; ?> Páginas
								</p>
							</article>
                            <?php
                        }
                    }
                ?>
			</footer>
		</div>
        <?php
            $Pager->exePaginator(DB_VIEWS_VIEWS, "GROUP BY DATE_FORMAT(views_date,'%Y-%m')");
            echo $Pager->getPaginator();
        ?>
	</article>
</div>

<?php
    $getDayChart = [];
    $getSupportChart = [];
    $getResponseChart = [];
    foreach ($DateInterval as $setDayChart) {
        // GET DAYS
        $getDayChart[] = "'" . $setDayChart->format('d/m/Y') . "'";
        // GET DAY FOR READ
        $ReadDay = $setDayChart->format('Y-m-d');
        // GET STATS
        $Read->fullRead(
            'SELECT SUM(views_users) AS viewUsers, SUM(views_views) AS viewViews, SUM(views_pages) AS viewPages FROM ' . DB_VIEWS_VIEWS . ' WHERE views_date = :date',
            'date=' . $ReadDay
        );
        $getAccessUsers[] = ($Read->getResult()[0]['viewUsers'] ? $Read->getResult()[0]['viewUsers'] : 0);
        $getAccessVires[] = ($Read->getResult()[0]['viewViews'] ? $Read->getResult()[0]['viewViews'] : 0);
        $getAccessPages[] = ($Read->getResult()[0]['viewViews'] >= 1 ? \number_format(
            $Read->getResult()[0]['viewPages'] / $Read->getResult()[0]['viewViews'],
            1,
            '.',
            ''
        ) : 0);
    }

    $DaysChart = \implode(', ', (array)$getDayChart);
    $AccessUsers = \implode(', ', (array)$getAccessUsers);
    $AccessVires = \implode(', ', (array)$getAccessVires);
    $AccessPages = \implode(', ', (array)$getAccessPages);

    unset($_SESSION['wc_report_date']);
?>

<script>
    $(function () {
        //DATEPICKER CONFIG
        var wc_datepicker_start = $('.jwc_datepicker_start').datepicker({
            autoClose: true,
            maxDate: new Date()
        }).data('datepicker');
        var wc_datepicker_end = $('.jwc_datepicker_end').datepicker({
            autoClose: true,
            maxDate: new Date()
        }).data('datepicker');

        $('.jwc_datepicker_start').click(function () {
            var DateString = $('.jwc_datepicker_end').val().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            wc_datepicker_start.update('maxDate', new Date(DateString[3], DateString[2] - 1, DateString[1]));
            if (!$(this).val()) {
                $(this).val("<?php echo \date('d/m/Y', \strtotime((string)$StartDate)); ?>");
            }
        });

        $('.jwc_datepicker_end').click(function () {
            var DateString = $('.jwc_datepicker_start').val().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            wc_datepicker_end.update('minDate', new Date(DateString[3], DateString[2] - 1, DateString[1]));
            if (!$(this).val()) {
                $(this).val("<?php echo \date('d/m/Y', \strtotime((string)$EndDate)); ?>");
            }
        });

        //CHART CONFIG
        var wc_chart = Highcharts.chart('jwc_chart_container', {
            chart: {
                type: 'area',
                spacingBottom: 0,
                spacingTop: 5,
                spacingLeft: 0,
                spacingRight: 20
            },
            title: {
                text: null
            },
            subtitle: {
                text: null
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'Registros'
                }
            },
            tooltip: {
                useHTML: true,
                shadow: false,
                headerFormat: '<p class="al_center">{point.key}</p><p class="al_center" style="font-size: 2em">{point.y}</p>',
                pointFormat: '<p class="al_center">{series.name}</p><p class="al_center"></p>',
                backgroundColor: '#000',
                borderWidth: 0,
                padding: 20,
                style: {
                    padding: 20,
                    color: '#fff'
                }
            },
            xAxis: {
                categories: [<?php echo $DaysChart; ?>],
                minTickInterval: 7
            },
            series: [
                {
                    name: 'Visitas',
                    data: [<?php echo $AccessVires; ?>],
                    color: '#FF9326',
                    lineColor: '#B25900'
                },
                {
                    name: 'Usuários',
                    data: [<?php echo $AccessUsers; ?>],
                    color: '#0E96E5',
                    lineColor: '#006699'
                },
                {
                    name: 'Páginas por Visita',
                    data: [<?php echo $AccessPages; ?>],
                    color: '#00B494',
                    lineColor: '#008068'
                }
            ]
        });

        //CHART CHANGE
        $('.jwc_chart_change').click(function () {
            $('.jwc_chart_change').removeClass('btn_green');
            $(this).addClass('btn_green');
        });

        $('.jwc_area_chart').click(function () {
            wc_chart.update({
                chart: {
                    type: 'area'
                }
            });
        });

        $('.jwc_column_chart').click(function () {
            wc_chart.update({
                chart: {
                    type: 'column'
                }
            });
        });

        $('.jwc_line_chart').click(function () {
            wc_chart.update({
                chart: {
                    type: 'line'
                }
            });
        });
    });
</script>
