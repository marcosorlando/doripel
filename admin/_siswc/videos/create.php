<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = 6;
    if (!APP_VIDEOS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    $Read = new Read();
    $Create = new Create();

    $VideoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($VideoId) {
        $Read->exeRead(DB_YOUTUBE, 'WHERE video_id = :id', 'id=' . $VideoId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um video que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=videos/home');
        }
    } else {
        $VideoCreate = ['video_date' => date('Y-m-d H:i:s'), 'video_start' => date('Y-m-d H:i:s')];
        $Create->exeCreate(DB_YOUTUBE, $VideoCreate);
        header('Location: dashboard.php?wc=videos/create&id=' . $Create->getResult());
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-youtube2">Cadastrar Vídeo</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=videos/home">Vídeos</a>
			<span class="crumb">/</span>
			Novo Vídeo
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Vídeos!" href="dashboard.php?wc=videos/home" class="btn btn_blue icon-eye">Ver Videos</a>
		<a title="Novo Vídeo!" href="dashboard.php?wc=videos/create" class="btn btn_green icon-plus">Adicionar
			Vídeos!</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="post_create" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Videos"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="video_id" value="<?php
            echo $VideoId; ?>"/>

		<article class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Título:</span>
					<input class="font_large" type="text" name="video_title" value="<?php
                        echo $video_title; ?>"
					       required/>
				</label>

				<label class="label">
					<span class="legend">Descrição:</span>
					<textarea class="font_medium" name="video_desc" rows="3"
					          required><?php
                            echo $video_desc; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Link do YouTube: (cole a URL completa ou o ID do vídeo)</span>
					<input class="font_medium" type="text" name="video_link" value="<?php
                        echo $video_link; ?>"
					       required/>
				</label>

				<fieldset class="label_50">
					<legend>Veiculação</legend>
					<label class="label">
						<span class="legend">A partir de:</span>
						<input class="font_medium" type="text" class="formTime" name="video_start"
						       value="<?php
                                   echo empty($video_start) ? date('d/m/Y H:i:s') : date(
                                       'd/m/Y H:i:s',
                                       strtotime((string)$video_start)
                                   ); ?>" required/>
					</label>

					<label class="label">
						<span class="legend">Até dia: (opcional)</span>
						<input class="font_medium" type="text" class="formTime" name="video_end"
						       value="<?php
                                   echo empty($video_end) ? '' : date(
                                       'd/m/Y H:i:s',
                                       strtotime((string)$video_end)
                                   ); ?>"/>
					</label>
				</fieldset>

				<div class="clear"></div>
			</div>
		</article>
		<aside class="box box30">

			<div class="panel_header default">
				<div class='video_create_cover'>
					<div class='upload_progress none'>0%</div>
                    <?php
                        if (
                            !empty($video_image) && file_exists('../uploads/' . $video_image) && !is_dir(
                                '../uploads/' . $video_image
                            )
                        ) {
                            $VideoImage = 'uploads/' . $video_image;
                        } else {
                            $YouTubeThumb = Check::youtubeThumbnailUrl((string)($video_link ?? ''));
                            $VideoImage = $YouTubeThumb ?: 'admin/_img/no_image.jpg';
                        }
                    ?>
					<img class="video_image post_cover" alt="Capa" title="Capa"
					     src="../tim.php?src=<?php
                             echo $VideoImage; ?>&w=<?php
                             echo VIDEO_W; ?>&h=<?php
                             echo VIDEO_H; ?>"
					     default="../tim.php?src=<?php
                             echo $VideoImage; ?>&w=<?php
                             echo VIDEO_W; ?>&h=<?php
                             echo VIDEO_H; ?>"/>
				</div>
				<label class='label m_top'>
					<span class='legend'>Capa: (JPG <?php
                            echo VIDEO_W; ?>x<?php
                            echo VIDEO_H; ?>px)</span>
					<input type="file" class="wc_loadimage" name="video_image"/>
				</label>
			</div>

			<div class="wc_actions panel_header default">

				<button name='public' value='1' class='btn btn_green fl_right icon-share'>Atualizar Vídeo!
					<img class='form_load' alt='Enviando Requisição!'
					     title='Enviando Requisição!' src='_img/load_w.gif'/>
				</button>
			</div>
		</aside>
	</form>
</div>
