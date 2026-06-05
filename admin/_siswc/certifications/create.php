<?php

    use App\Conn\Create;
    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_CERTIFICATIONS;
    if (!APP_CERTIFICATIONS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $CertId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($CertId) {
        $Read->exeRead(DB_CERT, 'WHERE cert_id = :id', 'id=' . $CertId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar uma certificação que não existe ou que foi removida recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=certifications/home');

            exit;
        }
    } else {
        $Read->fullRead('SELECT count(cert_id) as Total FROM ' . DB_CERT . ' WHERE cert_status = :st', 'st=1');

        $CertCreate = [
            'cert_created' => date('Y-m-d H:i:s'),
            'cert_status' => 0,
        ];
        $Create->exeCreate(DB_CERT, $CertCreate);
        header('Location: dashboard.php?wc=certifications/create&id=' . $Create->getResult());
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode((string)$Search['s']);
        header('Location: dashboard.php?wc=certification/search&s=' . $S);

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-new-tab"><?php
                echo $cert_title ? $cert_title : 'Nova Certificação'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=certifications/home">Certificações</a>
			<span class="crumb">/</span>
			Gerenciar Certificação
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE . ('/certificacao/' . $cert_name); ?>"
		   class="wc_view btn btn_green icon-eye">Ver no Site!</a>
	</div>
</header>


<div class='workcontrol_imageupload none' id='post_control'>
	<div class='workcontrol_imageupload_content'>
		<form name='workcontrol_post_upload' action='' method='post' enctype='multipart/form-data'>
			<input type='hidden' name='callback' value='Certifications'/>
			<input type='hidden' name='callback_action' value='sendimage'/>
			<input type='hidden' name='cert_id' value="<?php
                echo $CertId; ?>"/>
			<div class='upload_progress none'
			     style='padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;'>
				0%
			</div>
			<div style='overflow: auto; max-height: 300px;'>
				<img class='image image_default' alt='Nova Imagem' title='Nova Imagem'
				     src='../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>'
				     default='../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>'/>
			</div>
			<div class='workcontrol_imageupload_actions'>
				<input class='wc_loadimage' type='file' name='image' required/>
				<span class='workcontrol_imageupload_close icon-cancel-circle btn btn_red' id='post_control'
				      style='margin-right: 8px;'>Fechar</span>
				<button class='btn btn_green icon-image'>Enviar e Inserir!</button>
				<img class='form_load none' style='margin-left: 10px;' alt='Enviando Requisição!'
				     title='Enviando Requisição!' src='_img/load.gif'/>
			</div>
			<div class='clear'></div>
		</form>
	</div>
</div>


<div class="dashboard_content single_svc_form">
	<form class="auto_save" name="manage_svc" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Certifications"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type='hidden' name='cert_id' value="<?php
            echo $CertId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Certificação:</span>
					<input style="font-size: 1.4em;" type="text" name="cert_title" value="<?php
                        echo $cert_title; ?>"
					       placeholder="Nome da Certificação:" required/>
				</label>

				<label class="label">
					<span class="legend">Breve Descrição:</span>
					<textarea style="font-size: 1.2em;" name="cert_subtitle" rows="3"
					          required><?php
                            echo $cert_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Descrição Completa:</span>
					<textarea name="cert_description" class="work_mce" rows="10"><?php
                            echo $cert_description; ?></textarea>
				</label>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class='label'>
				<label class='label'>
					<span class='legend'>Imagem principal (JPG <?php
                            echo THUMB_W; ?>x<?php
                            echo THUMB_H; ?>px) {</span>
					<input type="file" class="wc_loadimage" name="cert_cover"/>
				</label>
			</div>
            <?php
                $Image = (file_exists('../uploads/' . $cert_cover) && !is_dir(
                    '../uploads/' . $cert_cover
                ) ? 'uploads/' . $cert_cover : 'admin/_img/no_image.jpg');
            ?>
			<img class="cert_cover" alt="Capa da Certificação" title="Capa da Certificação"
			     src="../tim.php?src=<?php
                     echo $Image; ?>&w=<?php
                     echo THUMB_W; ?>&h=<?php
                     echo THUMB_H; ?>"
			     default="../tim.php?src=<?php
                     echo $Image; ?>&w=<?php
                     echo THUMB_W; ?>&h=<?php
                     echo THUMB_H; ?>">

			<div class="box_content">
				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center">
					<div class='switch'>
						<input name='cert_status' type='checkbox' id='cert_status'
						       value='1' <?php
                            echo 1 == $cert_status ? 'checked' : ''; ?>>
						<label for="cert_status" data-on="ON" data-off="OFF"></label>
					</div>
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
                <?php
                    $URLSHARE = '/certificacao/' . $cert_name;

                    require __DIR__ . '/../../_tpl/share.wc.php';
                ?>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
