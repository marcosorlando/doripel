<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = 6;
    if (!APP_VIDEOS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    $Read = new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_YOUTUBE, 'WHERE video_image IS NULL AND video_title IS NULL AND video_id >= :st', 'st=1');
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-youtube">Vídeos Expirados</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=end">Dashboard</a>
			<span class="crumb">/</span>
			Vídeos
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Vídeo" href="dashboard.php?wc=videos/create" class="btn btn_green icon-plus">Adicionar Vídeo!</a>
	</div>
</header>
<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Page = ($getPage ?? 0);
        $Pager = new Pager('dashboard.php?wc=videos/end&page=', '<<', '>>', 3);
        $Pager->exePager($Page, 4);
        $Read->exeRead(
            DB_YOUTUBE,
            'WHERE video_start <= NOW() AND video_end < NOW() ORDER BY video_date DESC LIMIT :limit OFFSET :offset',
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
        $Pager->returnPage();
        echo Check::erro(
            "<span>Ainda não existem vídeos inativos em seu site.</span>",
            E_USER_NOTICE
        );
    } else {
    ?>
	<div class="testimony">
		<div class="testimony_content">
			<span class="testimony_close">X</span>
			<h1><b>Assistir Vídeo: </b></h1>
			<div class="embed-container"></div>
			<p><b>Descrição: </b></p>
			<div class="content_like">
				<div class="box_like"></div>
			</div>
		</div>
	</div>

	<div class='flex align-items-stretch justify-content-start'>
        <?php
            foreach ($Read->getResult() as $Video) {
                extract($Video);

                if (!empty($video_image)) {
                    $Capa = BASE . sprintf(
                            '/tim.php?src=uploads/%s&w=%d&h=%d',
                            $video_image,
                            VIDEO_W,
                            VIDEO_H
                        );
                } else {
                    $YouTubeThumb = Check::youtubeThumbnailUrl((string)$video_link);
                    $Capa = $YouTubeThumb
                        ? BASE . sprintf('/tim.php?src=%s&w=%d&h=%d', $YouTubeThumb, VIDEO_W, VIDEO_H)
                        : BASE . sprintf('/tim.php?src=admin/_img/no_image.jpg&w=%d&h=%d', VIDEO_W, VIDEO_H);
                }

                $video_title = Check::chars($video_title, 70);
                $video_start = date(
                    'd/m/Y',
                    strtotime((string)$video_start)
                );
                $video_end = ($video_end ? date('d/m/Y', strtotime((string)$video_end)) : 'Sempre');
                $video_desc = Check::chars($video_desc, 80);

                echo "<article class='video_single' id='{$video_id}'>							
					<div class='lead_take'>
				
						<div class='thumb testimony_start' id='{$video_link}'>
							<img src='{$Capa}' title='{$video_title}' alt='{$video_title}'/>
							<div class='false_bg take_play'></div>
						</div>   
						<div class='info'>
						    <h2>{$video_title}</h2> 
						    <p>{$video_desc}</p>          
							<p class='al_center'>Disponível no Site:<br>
							<b class='icon-calendar'>De {$video_start} - {$video_end}</b></p>
						</div>	
						
						<div class='wc_actions'>
		                    <a title='Editar Vídeo' href='dashboard.php?wc=videos/create&id={$video_id}' class=' icon-pencil btn btn_blue'>Editar</a>
		                    
		                    <span rel='video_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$video_id}'>Deletar</span>
	                    
		                    <span rel='video_single' callback='Videos' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$video_id}'>Excluir Vídeo?</span>  
						</div>
					</div>	   
				</article>";
            }

            $Pager->exePaginator(
                DB_YOUTUBE,
                'WHERE video_start <= NOW() AND (video_end >= NOW() OR video_end IS NULL)'
            );
            echo $Pager->getPaginator();
            }
        ?>
	</div>
</div>
<script>

    $(function () {

        const BASE = <?= BASE ?>

        //PLAY TAKE
        $('.testimony_start').click(function () {
            let videoId = $(this).attr('id');
            let videoTitle = $('.info').find('h2').html();
            let videoDesc = $('.info').find('p').html();

            $('.testimony_content h1').append(' ' + videoTitle);
            $('.testimony_content p').append(' ' + videoDesc);

            $('.testimony_content .embed-container').html('<iframe width="640" height="360" src="https://www.youtube' +
                '.com/embed/' + videoId + '?rel=0&amp;showinfo=0&autoplay=1&origin="' + BASE +
                'frameborder="0" allowfullscreen></iframe>');
            $('.testimony').fadeIn(200);
        });

        $('.testimony_close').click(function () {
            $('.testimony').fadeOut(200, function () {
                $('.testimony_content .embed-container').html('');
            });
        });
        //END PLAY TAKE
    });

</script>
