<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = 6;
    if (!APP_MATERIALS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    $Read = new Read();
    $Create = new Create();

    $MatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($MatId) {
        $Read->exeRead(DB_MATERIAIS, 'WHERE mat_id = :id', 'id=' . $MatId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um material que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=materiais/home');
            exit;
        }
    } else {
        $PostCreate = [
            'mat_date' => date('Y-m-d H:i:s'),
            'mat_type' => 0,
            'mat_status' => 0,
            'mat_author' => $Admin['user_id'],
        ];
        $Create->exeCreate(DB_MATERIAIS, $PostCreate);
        header('Location: dashboard.php?wc=materiais/create&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-book">Cadastrar Novo Material</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=materiais/home">Materiais</a>
			<span class="crumb">/</span>
			Novo Material
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE; ?>/materiais/#<?php
            echo $mat_name; ?>"
		   class="wc_view btn btn_green icon-eye">Ver material no site!</a>
	</div>
</header>

<div class="dashboard_content">
	<form class="auto_save" name="mat_create" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Mats"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="mat_id" value="<?= $MatId; ?>"/>

		<article class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Título:</span>
					<input class="font_medium" type="text" name="mat_title" value="<?php
                        echo $mat_title; ?>" required/>
				</label>

				<label class="label">
					<span class="legend">Subtítulo:</span>
					<textarea name="mat_subtitle" rows="3"
					          required><?php
                            echo $mat_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Link para Landing page:</span>
					<input type="text" name="mat_link" value="<?php
                        echo $mat_link; ?>" placeholder="Link do Material - LP:"
					       required/>
				</label>


				<div class="label_50">
					<label class="label">
						<span class="legend">Formato do Material:</span>
						<select name="mat_category" required>
							<option value="" disabled="disabled" selected="selected">Selecione uma seção:</option>
                            <?php
                                $Read->fullRead(
                                    'SELECT category_id, category_title FROM ' . DB_MATCATEGORIES . ' WHERE category_parent IS NULL ORDER BY category_title'
                                );
                                if (!$Read->getResult()) {
                                    echo '<option value="" disabled="disabled">Não existem formatos cadastrados!</option>';
                                } else {
                                    foreach ($Read->getResult() as $CatPai) {
                                        $seleted = $mat_category == $CatPai['category_id'] ? " selected='selected'" : '';
                                        echo "<option value='{$CatPai['category_id']}' {$seleted} >{$CatPai['category_title']}</option>";
                                    }
                                }
                            ?>
						</select>
					</label>

					<label class="label">
						<span class="legend">Nível de conhecimento:</span>
						<select name="mat_level" required>
							<option value="" disabled="disabled" selected="selected">Selecione o nível:</option>
                            <?php

                                foreach (Check::getWcMatLevels() as $key => $value) {
                                    echo '<option ' . ($mat_level == $key ? "selected='selected'" : '') . sprintf(
                                            " value='%s'>%s</option>",
                                            $key,
                                            $value
                                        );
                                }
                            ?>
						</select>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">EXIBIR APÓS DIA:</span>
						<input type="text" class="formTime" name="mat_date"
						       value="<?php
                                   echo $mat_date ? date('d/m/Y H:i', strtotime((string)$mat_date)) : date(
                                       'd/m/Y H:i'
                                   ); ?>"
						       required/>
					</label>

					<label class="label">
						<span class="legend">AUTOR:</span>
						<select name="mat_author" required>
							<option value="<?php
                                echo $Admin['user_id']; ?>"><?php
                                    echo $Admin['user_name']; ?><?php
                                    echo $Admin['user_lastname']; ?></option>
                            <?php
                                $Read->fullRead(
                                    'SELECT user_id, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_level >= :lv AND user_id != :uid',
                                    'lv=6&uid=' . $Admin['user_id']
                                );
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $PostAuthors) {
                                        echo '<option';
                                        if ($PostAuthors['user_id'] == $mat_author) {
                                            echo " selected='selected'";
                                        }
                                        echo sprintf(
                                            " value='%s'>%s %s</option>",
                                            $PostAuthors['user_id'],
                                            $PostAuthors['user_name'],
                                            $PostAuthors['user_lastname']
                                        );
                                    }
                                }
                            ?>
						</select>
					</label>
				</div>
				<div class="clear"></div>
			</div>
		</article>

		<article class="box box30">
			<label class="label">
				<span class="legend">Capa: (.jpg 400X250 px)</span>
				<input type="file" class="wc_loadimage" id="jmat_cover" name="mat_cover"/>
			</label>
			<div class="post_create_cover">
				<div class="upload_progress none">0%</div>
                <?php
                    $PostCover = (!empty($mat_cover) && file_exists('../uploads/' . $mat_cover) && !is_dir(
                        '../uploads/' . $mat_cover
                    ) ? 'uploads/' . $mat_cover : 'admin/_img/no_image.jpg');
                ?>
				<img class="post_thumb mat_cover" alt="Capa" id="mat_cover" title="Capa"
				     src="../tim.php?src=<?php
                         echo $PostCover; ?>&w=400&h=auto"
				     default="../tim.php?src=<?php
                         echo $PostCover; ?>&w=400&h=auto"/>
			</div>
			<div class="post_create_categories">
                <?php
                    $Read->fullRead(
                        'SELECT category_id, category_title FROM ' . DB_MATCATEGORIES . ' WHERE category_parent IS NULL ORDER BY category_title'
                    );

                    if (!$Read->getResult()) {
                        echo '<br><br>';
                        echo Check::erro(
                            '<span>Não existem categorias cadastradas!</span>',
                            E_USER_WARNING
                        );
                    } else {
                        foreach ($Read->getResult() as $Categories) {
                            $Read->fullRead(
                                'SELECT category_id, category_title FROM ' . DB_MATCATEGORIES . ' WHERE category_parent = :parent ORDER BY category_title',
                                'parent=' . $Categories['category_id']
                            );
                            if ($Read->getResult()) {
                                echo sprintf("<p class='mat_create_ses'>%s</p>", $Categories['category_title']);
                                foreach ($Read->getResult() as $SubCategories) {
                                    echo sprintf(
                                        "<p class='mat_create_cat'><label class='label_check'><input type='checkbox' name='mat_category_parent[]' value='%s'",
                                        $SubCategories['category_id']
                                    );
                                    if (
                                        in_array(
                                            $SubCategories['category_id'],
                                            explode(',', (string)$mat_category_parent)
                                        )
                                    ) {
                                        echo ' checked';
                                    }
                                    echo sprintf('> %s</label></p>', $SubCategories['category_title']);
                                }
                            }
                        }
                    }
                ?>
			</div>
			<header>
				<h3>Publicar:</h3>
			</header>
			<div class="box_content">
				<div class="m_top">&nbsp;</div>
				<div class="wc_actions">

					<div class='switch'>
						<input name='mat_status' type='checkbox' id='mat_status'
						       value='1' <?php
                            echo 1 == $mat_status ? 'checked' : ''; ?>>
						<label for="mat_status" data-on="ON" data-off="OFF"></label>
					</div>
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
			</div>
		</article>
	</form>
</div>
