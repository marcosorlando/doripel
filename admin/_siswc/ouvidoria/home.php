<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_OUVIDORIA;
    if (!APP_OUVIDORIA || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $S = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $O = filter_input(INPUT_GET, 'opt', FILTER_DEFAULT);

    $WhereString = (empty($S) ? ''
        : " AND (first_name LIKE '%{$S}%' O{$S}R last_name LIKE '%{$S}%' OR email LIKE '%{$S}%') ");
    $WhereOpt = ((empty($O)) ? '' : sprintf(" AND sector = '%s' ", $O));

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        $S = urlencode((string)$Search['s']);
        $O = urlencode((string)$Search['opt']);
        header(sprintf('Location: dashboard.php?wc=ouvidoria/home&opt=%s&s=%s', $O, $S));

        exit;
    }

    $Read->fullRead('SELECT DISTINCT(sector) FROM ' . DB_OUVIDORIA . ' WHERE status = :st', 'st=1');
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-bullhorn">Ouvidoria</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span> Ouvidoria
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos os Setores</option>
                <?php
                    $Read->fullRead('SELECT DISTINCT(sector) FROM ' . DB_OUVIDORIA . ' WHERE status = :st', 'st=1');
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $Lista) {
                            extract($Lista);
                            $sectors[] = $Lista['sector'];
                        }
                        $sectors = array_unique($sectors, SORT_REGULAR);

                        foreach ($sectors as $key => $value) {
                            $selected = ($value === $O) ? 'selected' : '';
                            echo "<option value='{$value}' {$selected}>{$value}</option>";
                        }
                    }
                ?>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $RedirectOpt = ('' === $WhereOpt || '0' === $WhereOpt ? '' : '&opt=' . $O);
        $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
        $Pager = new Pager(sprintf('dashboard.php?wc=ouvidoria/home%s&page=', $RedirectOpt), '<<', '>>', 5);
        $Pager->exePager($Page, 12);
        $Read->exeRead(
            DB_OUVIDORIA,
            sprintf(' WHERE 1=1 %s %s ORDER BY created_at DESC LIMIT :limit OFFSET :offset', $WhereString, $WhereOpt),
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );

        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                '<span>Não encontramos dados para sua busca. Experimente novamente com filtro de todos os setores.</span>',
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $complaint) {
                extract($complaint);

                $full_name = empty($first_name) ? 'ANÔNIMO' : sprintf('%s %s', $first_name, $last_name);

                $PdtClass = (1 != $status ? 'inactive' : '');

                echo "<article class='box box25 panel_header default ouvidoria cv' id='{$id}'>			            
			           <h2 class='icon-bullhorn'> {$full_name}</h2>
			     
			                <div class='info'>
					            <p class='icon-user-check'> <b>{$sector}</b></p>					            
					            <p>{$complaint}</p>					      
			                </div>
			        
				            <div class='actions'> 
				                <a class='icon-mail3 btn btn_green' target='_blank' href='mailto:{$email}?cc=testes@zen.ppg.br&subject=Ouvidoria TRAVI - Resposta de {$sector}&body=Olá!<br> {$complaint}'> RESPONDER</a>              
				               	<span rel='ouvidoria' class='j_delete_action icon-cancel-circle btn btn_red' id='{$id}'>Excluir</span>
				                <span rel='ouvidoria' callback='Ouvidoria' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$id}'>Remover?</span>
				            </div>
                    </article>";
            }

            $Pager->exePaginator(DB_OUVIDORIA, sprintf('WHERE 1 = 1 %s %s', $WhereString, $WhereOpt));
            echo $Pager->getPaginator();
        }
    ?>
</div>
