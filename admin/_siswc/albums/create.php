<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_ALBUMS;
    if (!APP_ALBUMS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $AlbId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($AlbId) {
        $Read->exeRead(DB_ALBUMS, 'WHERE album_id = :id', 'id=' . $AlbId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)(\is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = \sprintf(
                '<b>OPPSS %s</b>, você tentou editar um álbum que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            \header('Location: dashboard.php?wc=albums/home');
        }
    } else {
        $AlbCreate = ['album_created' => \date('Y-m-d H:i:s'), 'album_status' => 0];
        $Create->exeCreate(DB_ALBUMS, $AlbCreate);
        \header('Location: dashboard.php?wc=albums/create&id=' . $Create->getResult());
    }

    $Search = \filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = \urlencode((string)$Search['s']);
        \header('Location: dashboard.php?wc=albums/search&s=' . $S);
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-new-tab"><?php
                echo $album_title ? $album_title : 'Novo Álbum'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=albums/home">Álbuns de Fotos</a>
			<span class="crumb">/</span>
			Gerenciar Álbum
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE; ?>/album/<?php
            echo $album_name; ?>"
		   class="wc_view btn btn_green icon-eye">Ver no Site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Albums"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="album_id" value="<?php
                echo $AlbId; ?>"/>
			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>
			<div style="overflow: auto; max-height: 300px;">
				<img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
				     src="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"
				     default="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"/>
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

<div class="dashboard_content single_product_form">
	<form class="auto_save" name="manage_alb" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Albums"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="album_id" value="<?php
            echo $AlbId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Álbum:</span>
					<input style="font-size: 1.4em;" type="text" name="album_title" value="<?php
                        echo $album_title; ?>"
					       placeholder="Nome do Álbum:" required/>
				</label>

				<label class="label">
					<span class="legend">Breve Descrição:</span>
					<textarea style="font-size: 1.2em;" name="album_subtitle" rows="3"
					          required><?php
                            echo $album_subtitle; ?></textarea>
				</label>

                <?php
                    if (APP_LINK_PRODUCTS !== 0) { ?>
						<label class="label">
							<span class="legend">Link Alternativo (Opcional):</span>
							<input type="text" name="album_name" value="<?php
                                echo $album_name; ?>" placeholder="Link do Álbum:"/>
						</label>
                        <?php
                    } ?>

				<div class="label_50">
					<label class="label">
						<span class="legend">Capa (JPG <?php
                                echo THUMB_W; ?>x<?php
                                echo THUMB_H; ?>px):</span>
						<input type="file" class="wc_loadimage" name="album_cover"/>
					</label>

					<label class="label">
						<span class="legend">Categoria:</span>
                        <?php
                            $Read->exeRead(
                                DB_PRESENTIAL_CATEGORIES,
                                'WHERE presential_cat_parent IS NULL ORDER BY presential_cat_title ASC'
                            );
                            if (!$Read->getResult()) {
                                echo Check::erro(
                                    "<span class='icon-warning'>Cadastre algumas categorias de cursos antes de começar!</span>",
                                    E_USER_WARNING
                                );
                            } else {
                                echo "<select name='album_subcategory' class='jwc_product_stock' required>";
                                echo "<option value=''>Selecione uma Categoria</option>";
                                foreach ($Read->getResult() as $Cat) {
                                    echo \sprintf(
                                        "<option disabled='disabled' value='%s'>%s</option>",
                                        $Cat['presential_cat_id'],
                                        $Cat['presential_cat_title']
                                    );
                                    $Read->exeRead(
                                        DB_PRESENTIAL_CATEGORIES,
                                        'WHERE presential_cat_parent = :id',
                                        'id=' . $Cat['presential_cat_id']
                                    );

                                    if (!$Read->getResult()) {
                                        echo "<option disabled='disabled' value=''>&raquo;&raquo; Cadastre uma categoria nessa sessão!</option>";
                                    } else {
                                        foreach ($Read->getResult() as $SubCat) {
                                            echo '<option';
                                            if ($album_subcategory == $SubCat['presential_cat_id']) {
                                                echo " selected='selected'";
                                            }
                                            echo \sprintf(
                                                " value='%s'>&raquo;&raquo; %s</option>",
                                                $SubCat['presential_cat_id'],
                                                $SubCat['presential_cat_title']
                                            );
                                        }
                                    }
                                }
                                echo '</select>';
                            }
                        ?>
					</label>
				</div>
				<label class="label">
					<span class="legend">Fotos do Álbum (JPG <?php
                            echo THUMB_W; ?>x<?php
                            echo THUMB_H; ?>px):</span>
					<input type="file" name="image[]" multiple/>
				</label>

                <?php
                    $Read->exeRead(DB_ALBUMS_IMAGE, 'WHERE album_id = :id', 'id=' . $album_id);
                    if ($Read->getResult()) {
                        echo '<div class="album_images gallery">';
                        foreach ($Read->getResult() as $Image) {
                            $ImageUrl = ($Image['image'] && \file_exists(
                                '../uploads/albuns/' . $Image['image']
                            ) && !\is_dir(
                                '../uploads/albuns/' . $Image['image']
                            ) ? 'uploads/albuns/' . $Image['image'] : '_img/no_image.jpg');
                            echo \sprintf(
                                "<img rel='Albums' id='%s' alt='Imagem em %s' title='Imagem em %s' src='../tim.php?src=%s&w=1200&h=628'/>",
                                $Image['id'],
                                $album_title,
                                $album_title,
                                $ImageUrl
                            );
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="album_images gallery"></div>';
                    }
                ?>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
            <?php
                $Image = (\file_exists('../uploads/albuns/' . $album_cover) && !\is_dir(
                    '../uploads/albuns/' . $album_cover
                ) ? 'uploads/albuns/' . $album_cover : 'admin/_img/no_image.jpg');
            ?>
			<img class="album_cover" alt="Capa do Álbum" title="Capa do Álbum"
			     src="../tim.php?src=<?php
                     echo $Image; ?>&w=1200&h=628" default="../tim.php?src=<?php
                echo $Image; ?>&w=1200&h=628">

			<div class="box_content">

				<p class="section">Tempo de Exposição:</p>

				<label class="label">
					<span class="legend">Início Divulgação:</span>
					<input type="text" class="formTime" name="album_start"
					       value="<?php
                               echo $album_start ? \date('d/m/Y H:i', \strtotime((string)$album_start)) : null; ?>"/>
				</label>

				<label class="label">
					<span class="legend">Fim da Divulgação:</span>
					<input type="text" class="formTime" name="album_end"
					       value="<?php
                               echo $album_end ? \date('d/m/Y H:i', \strtotime((string)$album_end)) : null; ?>"/>
				</label>

				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center">
					<label class="label_check label_publish <?php
                        echo 1 == $album_status ? 'active' : ''; ?>"><input
								style="margin-top: -1px;" type="checkbox" value="1"
								name="album_status" <?php
                            echo 1 == $album_status ? 'checked' : ''; ?>> Publicar
						Agora!</label>
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
                <?php
                    $URLSHARE = '/album/' . $album_name;

                    require __DIR__ . '/_tpl/Share.wc.php';
                ?>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
