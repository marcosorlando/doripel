<?php

use App\Helpers\Check;

    use App\Conn\Create;
    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_SERVICES;
    if (!APP_SERVICES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $SvcId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($SvcId) {
        $Read->exeRead(DB_SVC, 'WHERE svc_id = :id', 'id=' . $SvcId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um serviço que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=services/home');

            exit;
        }
    } else {
        $Read->fullRead('SELECT count(svc_id) as Total FROM ' . DB_SVC . ' WHERE svc_status = :st', 'st=1');

        $SvcCreate = [
            'svc_created' => date('Y-m-d H:i:s'),
            'svc_status' => 0,
        ];
        $Create->exeCreate(DB_SVC, $SvcCreate);
        header('Location: dashboard.php?wc=services/create&id=' . $Create->getResult());
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode((string)$Search['s']);
        header('Location: dashboard.php?wc=service/search&s=' . $S);

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-hammer"><?php
                echo $svc_title ?? 'Novo Processo'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=services/home">Processos</a>
			<span class="crumb">/</span>
			Gerenciar Processo
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE . ('/servico/' . $svc_name); ?>"
		   class="wc_view btn btn_green icon-eye">Ver no Site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Services"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="svc_id" value="<?php
                echo $SvcId; ?>"/>
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

<div class="dashboard_content single_svc_form">
	<form class="auto_save" name="manage_svc" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Services"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="svc_id" value="<?php
            echo $SvcId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Processo:</span>
					<input class="font_large" type="text" name="svc_title" value="<?php
                        echo $svc_title; ?>"
					       placeholder="Nome do Processo:" required/>
				</label>

				<label class="label">
					<span class="legend">Breve Descrição:</span>
					<textarea class="font_medium" name="svc_subtitle" rows="3"
					          required><?php
                            echo $svc_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Descrição Completa:</span>
					<textarea name="svc_description" class="work_mce" rows="10"><?php
                            echo $svc_description; ?></textarea>
				</label>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class="panel_header default">
				<h2 class="icon-file-picture">Imagem Principal do Processo:</h2>
				<label class='label'>
					<span class='legend'>Tamanho (JPG <?php
                            echo IMAGE_W; ?>x<?php
                            echo IMAGE_H; ?>px):</span>
					<input type="file" class="wc_loadimage" name="svc_cover"/>
				</label>
                <?php
                    $Image = (file_exists('../uploads/' . $svc_cover) && !is_dir(
                        '../uploads/' . $svc_cover
                    ) ? 'uploads/' . $svc_cover : 'admin/_img/no_image.jpg');
                ?>
				<img class="svc_cover" alt="Capa do Processo" title="Capa do Processo"
				     src="../tim.php?src=<?php
                         echo $Image; ?>&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"
				     default="../tim.php?src=<?php
                         echo $Image; ?>&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>">
                <?php
                    $Read->exeRead(DB_SVC_GALLERY, 'WHERE svc_id = :id', 'id=' . $svc_id);
                    if ($Read->getResult()) {
                        echo '<div class="pdt_images gallery pdt_single_image">';
                        foreach ($Read->getResult() as $Image) {
                            $ImageUrl = ($Image['image'] && file_exists('../uploads/' . $Image['image']) && !is_dir(
                                '../uploads/' . $Image['image']
                            ) ? '../uploads/' . $Image['image'] : '_img/no_image.jpg');
                            echo sprintf(
                                "<img rel='Services' id='%s' alt='Imagem em %s' title='Imagem em %s' src='%s'/>",
                                $Image['id'],
                                $svc_title,
                                $svc_title,
                                $ImageUrl
                            );
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="pdt_images gallery pdt_single_image"></div>';
                    }
                ?>
			</div>

			<div class="box_content">
				<label class="label">
					<span class="legend">Fotos Adicionais (JPG <?php
                            echo IMAGE_W; ?>x<?php
                            echo IMAGE_H; ?>px):</span>
					<input type="file" name="image[]" multiple/>
				</label>

				<div class='label'>
					<label class='label'>
						<span class='legend'>ÍCONE (PNG <?php
                                echo AVATAR_W; ?>x<?php
                                echo AVATAR_H; ?>px):</span>
						<input type="file" class="wc_loadimage" name="svc_icon"/>
					</label>
				</div>
                <?php
                    $icone = (file_exists('../uploads/' . $svc_icon) && !is_dir(
                        '../uploads/' . $svc_icon
                    ) ? 'uploads/' . $svc_icon : 'admin/_img/no_image.jpg');
                ?>
				<img class="svc_icon" alt="Ícone do Segmento" title="Ícone do Segmento"
				     src="../tim.php?src=<?php
                         echo $icone; ?>&w=<?php
                         echo AVATAR_W; ?>&h=<?php
                         echo AVATAR_H; ?>"
				     default="../tim.php?src=<?php
                         echo $icone; ?>&w=<?php
                         echo AVATAR_W; ?>&h=<?php
                         echo AVATAR_H; ?>">

				<div class="m_top">&nbsp;</div>

				<div class="wc_actions">
					<div class='switch'>
						<input name='svc_status' type='checkbox' id='svc_status'
						       value='1' <?php
                            echo 1 == $svc_status ? 'checked' : ''; ?>>
						<label for="svc_status" data-on="ON" data-off="OFF"></label>
					</div>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
                <?php
                    $URLSHARE = '/servico/' . $svc_name;
                    $pdt_title = $svc_title;
                    $pdt_subtitle = $svc_subtitle;

                    require __DIR__ . '/../../_tpl/share.wc.php';
                ?>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
