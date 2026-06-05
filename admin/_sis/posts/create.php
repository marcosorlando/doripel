<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_POSTS;
    if (!APP_POSTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECTs CRUD
    $Read ??= new Read();
    $Create ??= new Create();

    $PostId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PostId) {
        $Read->exeRead(DB_POSTS, 'WHERE post_id = :id', 'id=' . $PostId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = \sprintf(
                '<b>OPPSS %s</b>, você tentou editar um post que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            \header('Location: dashboard.php?wc=posts/home');
            exit;
        }
    } else {
        $PostCreate = [
            'post_date' => \date('Y-m-d H:i:s'),
            'post_type' => 'post',
            'post_status' => 0,
            'post_author' => $Admin['user_id'],
        ];
        $Create->exeCreate(DB_POSTS, $PostCreate);
        \header('Location: dashboard.php?wc=posts/create&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-new-tab"><?= $post_title ?? 'Novo Post'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=posts/home">Posts</a>
			<span class="crumb">/</span> Gerenciar Post
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?= BASE; ?>/artigo/<?= $post_name; ?>"
		   class="wc_view btn btn_green icon-eye">Ver artigo no site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Posts"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="post_id" value="<?= $PostId; ?>"/>
			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>
			<div style="overflow: auto; max-height: 300px;">
				<img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
				     src="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"
				     default="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
			</div>
			<div class="workcontrol_imageupload_actions">
				<input class="wc_loadimage" type="file" name="image" required/>
				<span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control"
				      style="margin-right: 8px;">Fechar</span>
				<button class="btn btn_green icon-image">Enviar e Inserir!</button>
				<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>
<!--Modal Video Upload-->
<div class="workcontrol_videoupload none" id="video_control">
	<div class="workcontrol_videoupload_content">
		<form name="workcontrol_video_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Posts"/>
			<input type="hidden" name="callback_action" value="sendvideo"/>
			<input type="hidden" name="post_id" value="<?= $PostId; ?>"/>

			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>

			<div style="overflow: auto; max-height: 300px;">
				<video class="video_preview" controls width="100%" style="background: #000;">
					<source src="" type="video/mp4"/>
					Seu navegador não suporta vídeo HTML5.
				</video>
			</div>

			<div class="workcontrol_imageupload_actions">
				<input class="wc_loadvideo" type="file" name="video" accept=".mp4,.webm" required/>
				<span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="video_control"
				      style="margin-right: 8px;">Fechar</span>
				<button class="btn btn_green icon-play2">Enviar e Inserir!</button>
				<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>

			<div class="clear"></div>
		</form>
	</div>
</div>

<div class="dashboard_content">

	<form class="auto_save" name="post_create" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Posts"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="post_id" value="<?= $PostId; ?>"/>

		<div class="box box70">
			<div class="panel_header default">
				<h2 class="icon-blog">Dados sobre o Post</h2>
			</div>
			<div class="panel">
				<label class="label">
					<span class="legend">Título:</span>
					<input style="font-size: 1.4em;" type="text" name="post_title" value="<?= $post_title; ?>"
					       required/>
				</label>

				<label class="label">
					<span class="legend">Subtítulo:</span>
					<textarea style="font-size: 1.2em;" name="post_subtitle" rows="3"
					          required><?= $post_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">TAGS:</span>
					<input style="font-size: 1.2em;" type="text" name="post_tags" value="<?= $post_tags; ?>"
					       list="tags"/>

					<datalist id="tags">
                        <?php
                            $Read->fullRead(
                                'SELECT DISTINCT upper(post_tags) as post_tags FROM ' . DB_POSTS . " WHERE post_tags IS NOT NULL AND post_tags != ''"
                            );
                            foreach ($Read->getResult() as $tags) {
                                echo '<option value="' . $tags['post_tags'] . '">';
                            }
                        ?>
					</datalist>
				</label>

                <?php
                    if (APP_LINK_POSTS !== 0) { ?>
						<label class="label">
							<span class="legend">Link Alternativo (Opcional):</span>
							<input type="text" name="post_name" value="<?= $post_name; ?>"
							       placeholder="Link do Artigo:" required/>
						</label>
                        <?php
                    } ?>

				<label class="label">
					<span class="legend">Conteúdo do Artigo:</span>
					<textarea class="work_mce" name="post_content" required><?= $post_content; ?></textarea>
				</label>
			</div>
		</div>

		<div class="box box30">

			<div class="post_create_cover">
				<div class="upload_progress none">0%</div>
                <?php
                    $PostCover = (!empty($post_cover) && \file_exists('../uploads/' . $post_cover) && !\is_dir(
                        '../uploads/' . $post_cover
                    ) ? 'uploads/' . $post_cover : 'admin/_img/no_image.jpg');
                ?>
				<img class="post_thumb post_cover" alt="Capa" title="Capa"
				     src="../tim.php?src=<?= $PostCover; ?>&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"
				     default="../tim.php?src=<?= $PostCover; ?>&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
			</div>

			<div class="post_create_categories">
				<label class='label'>
					<span class='legend'>Capa: (JPG <?= IMAGE_W; ?>x<?= IMAGE_H; ?>px)</span>
					<input type="file" class="wc_loadimage" name="post_cover"/>
				</label>

				<label class="label">
					<span class="legend">Vídeo: <b>ID do vídeo do Youtube</b>.</span>
					<small>youtube.com/watch?v=<b
								class='text-red'>Xp-rrLuB7QQ</b></small>
					<input type="text" placeholder="" name="post_video"
					       value="<?= $post_video; ?>"/>
				</label>

				<select name="post_category" required>
                    <?php
                        $Read->fullRead(
                            'SELECT category_id, category_title FROM ' . DB_CATEGORIES . ' WHERE category_parent IS NULL'
                        );
                        if (!$Read->getResult()) {
                            echo '<option value="" disabled="disabled">Não existem sessões cadastradas!</option>';
                        } else {
                            foreach ($Read->getResult() as $CatPai) {
                                echo '<option';
                                if ($post_category == $CatPai['category_id']) {
                                    echo " selected='selected'";
                                }
                                echo \sprintf(
                                    " value='%s'>%s</option>",
                                    $CatPai['category_id'],
                                    $CatPai['category_title']
                                );
                            }
                        }
                    ?>
				</select>

                <?php
                    $Read->fullRead(
                        'SELECT category_id, category_title FROM ' . DB_CATEGORIES . ' WHERE category_parent IS NULL'
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
                                'SELECT category_id, category_title FROM ' . DB_CATEGORIES . ' WHERE category_parent = :parent',
                                'parent=' . $Categories['category_id']
                            );
                            if ($Read->getResult()) {
                                echo \sprintf("<p class='post_create_ses'>%s</p>", $Categories['category_title']);
                                foreach ($Read->getResult() as $SubCategories) {
                                    echo \sprintf(
                                        "<p class='post_create_cat'><label class='label_check'><input type='checkbox' name='post_category_parent[]' value='%s'",
                                        $SubCategories['category_id']
                                    );
                                    if (
                                        \in_array(
                                            $SubCategories['category_id'],
                                            \explode(',', (string)$post_category_parent)
                                        )
                                    ) {
                                        echo ' checked';
                                    }
                                    echo \sprintf('> %s</label></p>', $SubCategories['category_title']);
                                }
                            }
                        }
                    }
                ?>
			</div>

			<div class="panel_header default">
				<h2>Publicar:</h2>
			</div>

			<div class="panel">

                <?php
                    if (APP_POSTS_INSTANT_ARTICLE !== 0) {
                        ?>
						<label class="label">
							<span class="legend">INSTANT ARTICLE:</span>
							<select name="post_instant_article" required>
								<option value="0" <?= '0' != $post_instant_article ? "selected='selected'" : ''; ?>>
									Não
								</option>
								<option value="1" <?= '1' == $post_instant_article ? "selected='selected'" : ''; ?>>
									Sim
								</option>
							</select>
						</label>
                        <?php
                    }

                    if (APP_POSTS_AMP !== 0) {
                        ?>
						<label class="label">
							<span class="legend">AMP:</span>
							<select name="post_amp" required>
								<option value="0" <?= '0' != $post_amp ? "selected='selected'" : ''; ?>>Não</option>
								<option value="1" <?= '1' == $post_amp ? "selected='selected'" : ''; ?>>Sim</option>
							</select>
						</label>
                        <?php
                    } ?>

				<label class='label'>
					<span class='legend'>Tempo de Leitura em minutos:</span>
					<input style='font-size: 1em;' min="1" step="1" type='number' name='post_time'
					       value="<?= $post_time; ?>"
					       required/>
				</label>

				<label class="label">
					<span class="legend">DIA:</span>
					<input type="text" class="jwc_datepicker" data-timepicker="true" readonly="readonly"
					       name="post_date"
					       value="<?= $post_date ? \date('d/m/Y H:i', \strtotime((string)$post_date)) : \date(
                               'd/m/Y H:i'
                           ); ?>"
					       required/>
				</label>

				<label class="label">
					<span class="legend">AUTOR:</span>
					<select name="post_author" required>
						<option value="<?= $Admin['user_id']; ?>"><?= $Admin['user_name']; ?> <?= $Admin['user_lastname']; ?></option>
                        <?php
                            $Read->fullRead(
                                'SELECT user_id, user_name, user_lastname FROM ' . DB_USERS . ' WHERE user_level >= :lv AND user_id != :uid',
                                'lv=6&uid=' . $Admin['user_id']
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $PostAuthors) {
                                    echo '<option';
                                    if ($PostAuthors['user_id'] == $post_author) {
                                        echo " selected='selected'";
                                    }
                                    echo \sprintf(
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

				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center">

                <span class='switch'>
                    <input name='post_status' type='checkbox' id='post_status'
                           value='1' <?= 1 == $post_status ? 'checked' : ''; ?>>
                    <label for="post_status" data-on="ON" data-off="OFF"></label>
                </span>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
			</div>


			<div class="panel_header default">
				<h2>Compartilhe nas Redes Sociais:</h2>
                <?php
                    $URLSHARE = '/artigo/' . $post_name;

                    require_once __DIR__ . '/../../_tpl/share.wc.php';
                ?>
			</div>

		</div>
	</form>

</div>
