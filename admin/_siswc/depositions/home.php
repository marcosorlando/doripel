<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_DEPOSITIONS;
    if (!APP_DEPOSITIONS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_DEPOSITIONS, 'WHERE depositions_profession IS NULL and depositions_text IS NULL', '');

        // AUTO TRASH IMAGES
        //    $Read->fullRead("SELECT image FROM " . DB_PRESENTIAL_IMAGE . " WHERE depositions_id NOT IN(SELECT depositions_id FROM " . DB_PRESENTIAL . ")");
        //    if ($Read->getResult()):
        //        $Delete->exeDelete(DB_PRESENTIAL_IMAGE, "WHERE id >= :id AND depositions_id NOT IN(SELECT depositions_id FROM " . DB_PRESENTIAL . ")", "id=1");
        //        foreach ($Read->getResult() as $ImageRemove):
        //            if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
        //                unlink("../uploads/{$ImageRemove['image']}");
        //            endif;
        //        endforeach;
        //    endif;
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $S = \filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $Search = \filter_input_array(INPUT_POST);
    if ($Search && (isset($Search['s']) || isset($Search['status']))) {
        $S = (isset($Search['s']) ? \urlencode($Search['s']) : $S);
        $SearchCat = (empty($Search['searchcat']) ? null : $Search['searchcat']);
        \header(\sprintf('Location: dashboard.php?wc=depositions/home&s=%s&cat=%s&tag=%s', $S, $SearchCat, $T));
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pen">Depoimentos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="Todos os Depoimentos" href="dashboard.php?wc=depositions/home">Depoimentos</a>
            <?php
                echo $S ? \sprintf("<span class='crumb'>/</span> <span class='icon-search'>%s</span>", $S) : ''; ?>
		</p>
	</div>

	<div class="dashboard_header_search">

		<form style="width: 100%; display: inline-block;" name="searchCategoriesPost" action="" method="post"
		      enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?php
                echo $S; ?>" name="s" placeholder="Pesquisar:"
			       style="width: 38%; margin-right: 3px;">
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $getPage = \filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Paginator = new Pager(\sprintf('dashboard.php?wc=depositions/home&s=%s&pg=', $S), '<<', '>>', 5);
        $Paginator->exePager($Page, 100);

        if (!empty($S)) {
            $WhereString[0] = "AND ( depositions_name LIKE '%' :s '%' OR depositions_profession LIKE '%' :s '%')";
            $WhereString[1] = '&s=' . $S;
        } else {
            $WhereString[0] = '';
            $WhereString[1] = '';
        }

        $Read->fullRead(
            'SELECT * FROM ' . DB_DEPOSITIONS . ' WHERE 1=1 '
            . ($WhereString[0] . ' ')
            . 'ORDER BY depositions_order ASC, depositions_name ASC '
            . 'LIMIT :limit OFFSET :offset',
            \sprintf('limit=%d&offset=%d%s', $Paginator->getLimit(), $Paginator->getOffset(), $WhereString[1])
        );

        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                \sprintf(
                    '<span>Ainda não existem depoimentos cadastrados %s. Comece agora mesmo criando seu primeiro depoimento!</span>',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Deposition) {
                \extract($Deposition);

                $DepositionCover = (\file_exists('../uploads/' . $depositions_image) && !\is_dir(
                    '../uploads/' . $depositions_image
                ) ? 'uploads/' . $depositions_image : 'admin/_img/no_image.jpg');

                $depositions_name = (empty($depositions_name) ? 'Edite esse rascunho para poder exibir como depoimento em seu site!' : $depositions_name);

                $CourseDragAndDrop = (empty($segment_title) ? 'wc_draganddrop' : null);

                echo "<article class='box box25 post_single {$CourseDragAndDrop}' callback='Depositions' callback_action='depositions_order' id='{$depositions_id}'>        
                <div class='post_single_cover box_content'>
                   <img alt='{$depositions_name}' title='{$depositions_name}' src='../tim.php?src={$DepositionCover}&w=500&h=500'/></a>
                    
                <div class='post_single_content wc_normalize_height'>
                    <h1 class='title' style='font-size:1.25em;'>{$depositions_name}</h1>                    
                </div>
                
                <div class='post_single_actions'>
                    <a title='Editar Depoimento' href='dashboard.php?wc=depositions/create&id={$depositions_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$depositions_id}'>Deletar</span>
                    <span rel='post_single' callback='Depositions' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$depositions_id}'>Deletar Depoimento?</span>
                      
                </div>
            </article>";
            }

            $Paginator->exePaginator(
                DB_DEPOSITIONS,
                "WHERE (depositions_name LIKE '%' :s '%' OR depositions_profession LIKE '%' :s '%')",
                's=' . $S
            );
            echo $Paginator->getPaginator();
        }
    ?>
</div>
