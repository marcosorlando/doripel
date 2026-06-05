<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PAGES;
    if (!APP_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $PageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PageId) {
        $Read->exeRead(DB_PAGES, 'WHERE page_id = :id', 'id=' . $PageId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );

            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                sprintf(
                    '<b>OPPSS %s</b>, você tentou editar uma página que não existe ou que foi removida recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=pages/home');
            exit;
        }
    } else {
        $PageCreate = ['page_status' => 0];
        $Create->exeCreate(DB_PAGES, $PageCreate);
        header('Location: dashboard.php?wc=pages/create&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-page-break"><?php
                echo $page_title ? $page_title : 'Nova Página'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=pages/home">Páginas</a>
			<span class="crumb">/</span>
			Gerenciar Página
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE; ?>/<?php
            echo $page_name; ?>"
		   class="wc_view btn btn_green icon-eye">Ver página no site!</a>
	</div>
</header>
<!-- MODALS -->
<!--Modal Video Upload-->
<div class='workcontrol_videoupload none' id='video_control'>
	<div class='workcontrol_videoupload_content'>
		<form name='workcontrol_video_upload' action='' method='post' enctype='multipart/form-data'>
			<input type='hidden' name='callback' value='Pages'/>
			<input type='hidden' name='callback_action' value='sendvideo'/>
			<input type='hidden' name='page_id' value="<?php
                echo $PageId; ?>"/>

			<div class='upload_progress none'
			     style='padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;'>
				0%
			</div>

			<div style='overflow: auto; max-height: 300px;'>
				<video class='video_preview' controls width='100%' style='background: #000;'>
					<source src='' type='video/mp4'/>
					Seu navegador não suporta vídeo HTML5.
				</video>
			</div>

			<div class='workcontrol_imageupload_actions'>
				<input class='wc_loadvideo' type='file' name='video' accept='.mp4,.webm' required/>
				<span class='workcontrol_imageupload_close icon-cancel-circle btn btn_red' id='video_control'
				      style='margin-right: 8px;'>Fechar</span>
				<button class='btn btn_green icon-play2'>Enviar e Inserir!</button>
				<img class='form_load none' style='margin-left: 10px;' alt='Enviando Requisição!'
				     title='Enviando Requisição!' src='_img/load.gif'/>
			</div>

			<div class='clear'></div>
		</form>
	</div>
</div>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Pages"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="page_id" value="<?php
                echo $PageId; ?>"/>
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

<div class="dashboard_content">

	<form class="auto_save" name="page_add" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Pages"/>
		<input type="hidden" name="callback_action" value="manage"/>
		<input type="hidden" name="page_id" value="<?= $PageId; ?>"/>

		<div class="box box70">

			<div class="panel_header default">
				<h2 class="icon-page-break">Insira as informações da Página</h2>
			</div>

			<div class="panel">
				<label class="label">
					<span class='legend'><b>Título:</b> (máx 150 caracteres) - Tamanho atual: <mark
								class='chars'>0</mark>:<i class='icon-info icon-2x font_blue wc_tooltip'><span
									class='wc_tooltip_balloon'>Um título otimizado deve conter no máximo 65 caracteres.</span></i></span>

					<input class="font_medium title_optimize" type="text" name="page_title" value="<?= $page_title ??
                        ''; ?>"
					       placeholder="Título da Página:" required/>
				</label>

				<label class='label'>
					<span class='legend'><b>Breve Descrição:</b> (máx 150 caracteres) - Tamanho atual:
						<mark class='caracteres'>0</mark>:</span>
					<textarea name='page_subtitle' rows='3' class='subtitle_optimize font_medium' maxlength='150'
					          required><?= $page_subtitle ?? '' ?></textarea>

				</label>


				<label class="label">
					<span class="legend">Conteúdo:</span>
					<textarea name="page_content" class="work_mce" rows="10"
					          placeholder="Conteúdo da Página:"><?= $page_content; ?></textarea>
				</label>
				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">

			<div class="panel_header default">
				<h2>Dados Adicionais</h2>
			</div>

			<div class="panel">
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                        $PageCover = (!empty($page_cover) && file_exists('../uploads/' . $page_cover) && !is_dir(
                            '../uploads/' . $page_cover
                        ) ? 'uploads/' . $page_cover : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb page_cover" alt="Capa" title="Capa"
					     src="../tim.php?src=<?= $PageCover; ?>&w=<?= IMAGE_W ?>&h=<?= IMAGE_H ?>"
					     default="../tim.php?src=<?= $PageCover; ?>&w=<?= IMAGE_W ?>&h=<?= IMAGE_H ?>"/>
				</div>

				<label class="label">
					<span class="legend">Capa:</span>
					<input type="file" class="wc_loadimage" name="page_cover"/>
				</label>

                <?php
                    if (APP_LINK_PAGES !== 0) { ?>
						<label class="label">
							<span class="legend">Link Alternativo (Opcional):</span>
							<input id="page_add" type="text" name="page_name" value="<?php
                                echo $page_name; ?>"
							       placeholder="Link da Página:"/>
						</label>
                        <?php
                    } ?>
				<label class="label">
					<span class="legend">Ordem da página no menu:</span>
					<input type="number" multiple="1" min="-1" max="10" required name="page_order"
					       value="<?php
                               echo $page_order; ?>" placeholder="Ordem no menu:"/>
				</label>

				<div class="m_top">&nbsp;</div>

				<div class="panel_header default">
                    <?= Check::switchOnOff(
                        'page_menu',
                        $page_menu,
                        'Mostrar no Menu',
                        'SIM',
                        'NÃO'
                    ) ?>
				</div>

				<div class="wc_actions m_top">

                    <?= Check::switchOnOff(
                        'page_status',
                        $page_status,
                        'Publicar:',
                        'SIM',
                        'NÃO'
                    ) ?>
					<button name="public" value="1" class="btn btn_green icon-floppy-disk">SALVAR <img
								class='form_load' alt='Salvando...' src='_img/load_w.gif'/>
					</button>

				</div>
			</div>
		</div>
	</form>
</div>
