<?php

    use App\Conn\Delete;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_SLIDES;
    if (!APP_SLIDE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_SLIDES,
            'WHERE slide_image_desktop IS NULL AND slide_title IS NULL AND slide_id >= :st',
            'st=1'
        );
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-images">Conteúdo em Destaque</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span> Em destaque
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Slide" href="dashboard.php?wc=slide/create" class="btn btn_green icon-plus">Adicionar Slide!</a>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Page = ($getPage ?? 1);
        $Pager = new Pager('dashboard.php?wc=slide/home&page=', '<<', '>>', 5);
        $Pager->exePager($Page, 5);
        $Read->exeRead(
            DB_SLIDES,
            'WHERE slide_status = 1 AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_date DESC LIMIT :limit OFFSET :offset',
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                'Ainda não existe conteúdo em destaque cadastrado em seu site. Comece cadastrando o primeiro!',
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Slide) {
                extract($Slide);
                echo "<article class='box box50 slide_single' id='{$slide_id}'>
                    <header>
                        <h1><a target='_blank' href='" . BASE . "' title='{$slide_title}'>{$slide_title}</a></h1>
                    </header>
                    <div class='box_content'>
                    <img src='" . BASE . sprintf(
                        '/tim.php?src=uploads/%s&w=',
                        $slide_image_desktop
                    ) . SLIDE_W / 2 . '&h=' . SLIDE_H / 2 . "' title='{$slide_title}' alt='{$slide_title}'>                   
                    ";
                ?>
				<div class="wc_actions">
					<div class="box box100">
						<label class="label_check label_publish <?php
                            echo 1 == $show_headline ? 'active' : ''; ?>">
							<input type="checkbox" value="1" name="show_title" <?php
                                echo 1 == $show_headline ? 'checked' : ''; ?>> HEADLINE
						</label>
						<label class="label_check label_publish <?php
                            echo 1 == $show_desc ? 'active' : ''; ?>">
							<input type="checkbox" value="1"
							       name="show_desc" <?php
                                echo 1 == $show_desc ? 'checked' : ''; ?>> TEXTO COMPLEMENTAR
						</label>

						<label class="label_check label_publish <?php
                            echo 1 == $slide_product ? 'active' : ''; ?>"><input
									type="checkbox" value="1"
									name="slide_product" <?php
                                echo 1 == $slide_product ? 'checked' : ''; ?>> PRODUTO
						</label>
						<label class="label_check label_publish <?php
                            echo 1 == $slide_category ? 'active' : ''; ?>"><input
									type="checkbox" value="1"
									name="slide_category" <?php
                                echo 1 == $slide_category ? 'checked' : ''; ?>> CATEGORIA</label>
					</div>
				</div>

                <?php
                echo "<p><b class='icon-calendar'>De " . date(
                        'd/m/Y H\hi',
                        strtotime((string)$slide_start)
                    ) . ' - ' . ($slide_end ? date('d/m/Y H\hi', strtotime((string)$slide_end)) : 'SEMPRE') . ":</b> {$slide_desc}</p>
	                    <a title='Editar Destaque' href='dashboard.php?wc=slide/create&id={$slide_id}' class='icon-notext icon-pencil btn btn_blue'></a>                  
	                    <span rel='slide_single' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$slide_id}'></span>
	                    <span rel='slide_single' callback='Slides' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$slide_id}'>Deletar Destaque?</span>
                    </div>                    
                </article>";
            }

            $Pager->exePaginator(DB_SLIDES, 'WHERE slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL)');
            echo $Pager->getPaginator();
        }
    ?>
</div>
