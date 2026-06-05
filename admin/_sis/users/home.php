<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_USERS;
    if (!APP_USERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE USER TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_USERS,
            'WHERE user_name IS NULL AND user_email IS NULL and user_password IS NULL and user_level = :st',
            'st=1'
        );
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $rawSearch = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $searchTerm = is_string($rawSearch) ? trim($rawSearch) : '';

    $rawOpt = filter_input(INPUT_GET, 'opt', FILTER_DEFAULT);
    $filterOption = is_string($rawOpt) ? trim($rawOpt) : '';
    if (!in_array($filterOption, ['customers', 'team'], true)) {
        $filterOption = '';
    }

    $Search = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
    $postSearch = isset($Search['s']) ? trim((string)$Search['s']) : '';
    $postOpt = isset($Search['opt']) ? trim((string)$Search['opt']) : '';

    if ($Search) {
        $query = [];

        if ('' !== $postSearch) {
            $query['s'] = $postSearch;
        }

        if ('' !== $postOpt && in_array($postOpt, ['customers', 'team'], true)) {
            $query['opt'] = $postOpt;
        }

        $redirectUrl = 'dashboard.php?wc=users/home';
        $queryString = http_build_query($query);
        if ('' !== $queryString) {
            $redirectUrl .= '&' . $queryString;
        }

        header('Location: ' . $redirectUrl);

        exit;
    }

    $conditions = [];
    $queryParams = [];

    if ('' !== $searchTerm) {
        $conditions[] = '(
        user_name LIKE :search OR
        user_lastname LIKE :search OR
        concat(user_name, " ", user_lastname) LIKE :search OR
        user_email LIKE :search OR
        user_id LIKE :search OR
        user_document LIKE :search
    )';
        $queryParams['search'] = '%' . $searchTerm . '%';
    }

    if ('customers' === $filterOption) {
        $conditions[] = 'user_level <= 5';
    } elseif ('team' === $filterOption) {
        $conditions[] = 'user_level >= 6';
    }

    $WhereClause = [] !== $conditions ? ' AND ' . implode(' AND ', $conditions) : '';
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-users">Usuários</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Usuários
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchUsers" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?php
                echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>" name="s" placeholder="Pesquisar:"
			       style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos</option>
				<option <?php
                    echo 'customers' === $filterOption ? "selected='selected'" : ''; ?> value="customers">Clientes
				</option>
				<option <?php
                    echo 'team' === $filterOption ? "selected='selected'" : ''; ?> value="team">Equipe
				</option>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
    <?php

        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Pager = new Pager('dashboard.php?wc=users/home&page=', '<<', '>>', 5);
        $Pager->exePager($Page, 12);
        $queryParams['limit'] = $Pager->getLimit();
        $queryParams['offset'] = $Pager->getOffset();

        $placeholders = http_build_query($queryParams);

        $Read->exeRead(
            DB_USERS,
            sprintf('WHERE 1 = 1%s ORDER BY user_name ASC LIMIT :limit OFFSET :offset', $WhereClause),
            '' !== $placeholders ? $placeholders : null
        );

        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    'Ainda não existem usuários cadastrados %s. Comece agora mesmo cadastrando um novo usuário. Ou aguarde novos clientes!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Users) {
                extract($Users);
                $user_name ??= 'Novo';
                $user_lastname ??= 'Usuário';
                $UserThumb = '../uploads/' . $user_thumb;
                $user_thumb = (file_exists($UserThumb) && !is_dir(
                    $UserThumb
                ) ? 'uploads/' . $user_thumb : 'admin/_img/no_avatar.jpg');

                // var_dump($user_level);
                //var_dump(Check::getWcLevel($user_level));

                echo "<article class='single_user box box25 al_center'>
                    <div class='box_content wc_normalize_height'>
                        <img alt='Este é {$user_name}' title='Este é {$user_name}' src='../tim.php?src={$user_thumb}&w=400&h=400'/>
                        <h1>{$user_name} {$user_lastname}</h1>
                        <p class='nivel icon-equalizer'>" . Check::getWcLevel($user_level) . "</p>
                        <p class='info icon-envelop'>{$user_email}</p> 
                        <p class='info icon-calendar'>Desde " . date(
                        'd/m/Y \a\s H\h\si',
                        strtotime((string)$user_registration)
                    ) . "</p>
                    </div>
                    <div class='single_user_actions'>
                        <a class='btn btn_green icon-user' href='dashboard.php?wc=users/create&id={$user_id}' title='Gerenciar Usuário!'>Gerenciar Usuário!</a>
                    </div>
                </article>";
            }
            $paginatorParams = '' !== $searchTerm ? http_build_query(['search' => '%' . $searchTerm . '%']) : null;
            $Pager->exePaginator(
                DB_USERS,
                sprintf('WHERE 1 = 1%s', $WhereClause),
                $paginatorParams
            );

            echo $Pager->getPaginator();
        }
    ?>
</div>
