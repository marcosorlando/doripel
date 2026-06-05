<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_LINKTREE;
    if (!APP_LINKTREE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_CARD_USER,
            'WHERE carduser_name IS NULL AND carduser_email IS NULL and carduser_status = :st',
            'st=0'
        );
    }

    $S = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $searchFromGet = is_string($S) ? trim($S) : '';

    $Search = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
    $searchFromPost = isset($Search['s']) ? trim((string)$Search['s']) : '';

    if ($Search) {
        $redirectUrl = 'dashboard.php?wc=linktree/home';

        if ('' !== $searchFromPost) {
            $redirectUrl .= sprintf('&s=%s', rawurlencode($searchFromPost));
        }

        header('Location: ' . $redirectUrl);

        exit;
    }

    $WhereString = '';
    $queryParams = [];

    if ('' !== $searchFromGet) {
        $WhereString = " AND (carduser_name LIKE :search OR carduser_lastname LIKE :search OR concat(carduser_name, ' ', carduser_lastname) LIKE :search OR carduser_email LIKE :search OR carduser_id LIKE :search)";
        $queryParams['search'] = '%' . $searchFromGet . '%';
    }
?>
<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-newspaper">Cartões de Usuário - LinkTree</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Cartões
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchUsers" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?php
                echo htmlspecialchars($searchFromGet, ENT_QUOTES, 'UTF-8'); ?>" name="s" placeholder="Pesquisar:"
			       style="width: 38%; margin-right: 3px;"/>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>
<div class="dashboard_content">
    <?php

        $Read ??= new Read();
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Pager = new Pager('dashboard.php?wc=linktree/home&page=', '<<', '>>', 5);
        $Pager->exePager($Page, 12);

        $queryParams['limit'] = $Pager->getLimit();
        $queryParams['offset'] = $Pager->getOffset();

        $placeholders = http_build_query($queryParams);

        $Read->exeRead(
            DB_CARD_USER,
            "WHERE 1 = 1 {$WhereString} ORDER BY carduser_name ASC LIMIT :limit
			 OFFSET :offset",
            '' !== $placeholders ? $placeholders : null
        );

        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    '<span>Ainda não existem cartões de usuário cadastrados %s. Comece agora mesmo cadastrando um novo.</span>',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $CardUsers) {
                extract($CardUsers);
                $carduser_name ??= 'Novo';

                $UserThumb = '../uploads/linktree/' . $carduser_thumb;
                $carduser_thumb = (file_exists($UserThumb) && !is_dir(
                    $UserThumb
                ) ? 'uploads/linktree/' . $carduser_thumb : 'admin/_img/no_avatar.jpg');

                $cardUserStatus = $CardUsers['carduser_status'] === 0 ? 'off' : 'on';

                echo "<article class='single_user box box25 al_center $cardUserStatus' id='{$carduser_id}'>
	                    <div class='box_content wc_normalize_height'>
	                        <img alt='Este é {$carduser_name}' title='Este é {$carduser_name}' src='../tim.php?src={$carduser_thumb}&w=400&h=400'/>
	                        <h1><b>{$carduser_name}</b> {$carduser_lastname}</h1>	                        
	                        <a class='btn_whatsapp' target='_blank' href='" . Check::whatsMessage(
                        $carduser_phone,
                        sprintf('Olá, %s ', $carduser_name)
                    ) . "'>{$carduser_phone}</a>
	                        <p class='info icon-envelop'>{$carduser_email}</p>
	                        <p class='info icon-calendar'>Atualizado em: " . date(
                        'd/m/Y \a\s H\h\si',
                        strtotime(
                            (string)$carduser_updated
                        )
                    ) . "</p>
	                        <br>
	                        <p class='info'><a class='btn btn_yellow font_black font_medium' target='_blank' href='" . BASE .
                    "/{$carduser_url}'><i 
	                        class='icon-eye'></i> Visualizar</a></p>
	                    </div>
	                    
	                    <div class='single_user_actions'>
	                        <a class='btn btn_blue icon-pencil' href='dashboard.php?wc=linktree/create&id={$carduser_id}' title='Gerenciar Cartão'>Editar</a>
	                        
	                         <span rel='single_user' class='j_delete_action icon-cancel-circle btn btn_red' id='{$carduser_id}'>Excluir</span>
                            <span rel='single_user' callback='Linktree' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$carduser_id}'>Confirmar?</span>
	    
	                    </div>
                     </article>
                ";
            }
            $paginatorParams = '' !== $searchFromGet ? http_build_query(['search' => '%' . $searchFromGet . '%']
            ) : null;
            $Pager->exePaginator(DB_CARD_USER, "WHERE 1 = 1 {$WhereString}", $paginatorParams);
            echo $Pager->getPaginator();
        }
    ?>
</div>
