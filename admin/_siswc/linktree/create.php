<?php

use App\Conn\Create;
use App\Conn\Read;
use App\Helpers\Check;

$AdminLevel = LEVEL_WC_LINKTREE;
if (!APP_LINKTREE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
    exit(
        '<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; '
        . 'background: #fff; float: left; width: 100%; padding: 30px 0;">'
        . '<b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!'
        . '</div>'
    );
}

// AUTO INSTANCE OBJECT READ
$Read ??= new Read();

// AUTO INSTANCE OBJECT CREATE
$Create ??= new Create();

$CarduserId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($CarduserId) {
    $Read->exeRead(DB_CARD_USER, 'WHERE carduser_id = :id', 'id=' . $CarduserId);
    if ($Read->getResult()) {
        $FormData = array_map(
            fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $Read->getResult()[0]
        );
        extract($FormData);
    } else {
        $_SESSION['trigger_controll'] = Check::erro(
            sprintf(
                '<b>OPPSS %s</b>, você tentou editar um Cartão de Usuário que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            ),
            E_USER_NOTICE
        );
        header('Location: dashboard.php?wc=cardusers/home');

        exit;
    }
} else {
    $CarduserCreate = [
        'carduser_created' => date('Y-m-d H:i:s'),
        'carduser_status' => '0',
    ];

    $Create->exeCreate(DB_CARD_USER, $CarduserCreate);
    header('Location: dashboard.php?wc=linktree/create&id=' . $Create->getResult());

    exit;
}
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-newspaper"><?php
            echo $carduser_name ?? 'Novo Cartão LinkTree '; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
            echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
            echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
            echo ADMIN_NAME; ?>" href="dashboard.php?wc=linktree/home">Cartões de Usuários</a>
			<span class="crumb">/</span>
			Gerenciar LinkTree
		</p>
	</div>

	<div class="dashboard_header_search">
		<a href="dashboard.php?wc=linktree/create"
		   class="btn btn_green icon-plus">Adicionar</a>
		<a href="dashboard.php?wc=linktree/home" class="wc_view btn
        btn_blue icon-eye float-right">Ver todos</a>
	</div>
</header>

<div class="dashboard_content">

	<form class="auto_save form_capitalize" name="carduser_add" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Linktree"/>
		<input type="hidden" name="callback_action" value="manage"/>
		<input type="hidden" name="carduser_id" value="<?php
        echo $CarduserId; ?>"/>

		<div class="box box70">
			<div class="panel_header default">
				<h2 class="icon-newspaper">Insira os dados do Cartão</h2>
			</div>

			<div class="panel">

				<div class="flex_box">
					<label class="label">
						<span class="legend">Nome:</span>
						<input class="font_medium" type="text" name="carduser_name" value="<?php
                        echo $carduser_name; ?>"
						       placeholder="Nome que aparecerá no cartão:" required/>
					</label>
					<label class="label">
						<span class="legend">Sobrenome:</span>
						<input class="font_medium" type="text" name="carduser_lastname" value="<?php
                        echo $carduser_lastname;
                        ?>"
						       placeholder="Sobrenome que aparecerá no cartão:" required/>
					</label>
				</div>


				<div class="flex_box">
					<label class="label">
						<span class="legend">Email:</span>
						<input class="font_medium" type="email" name="carduser_email" value="<?php
                        echo $carduser_email; ?>"
						       placeholder="E-mail:" required/>
					</label>

					<label class="label">
						<span class="legend">Número de Whatsapp:</span><input class="font_medium formPhone" type="text"
						                                                      name="carduser_phone"
						                                                      value="<?php
                                                                              echo $carduser_phone; ?>"
						                                                      placeholder="Whatsapp:" required/>
					</label>

					<label class="label">
						<span class="legend">Cargo:</span>
						<input class="font_medium" type="text" name="carduser_cargo"
						       value="<?php
                               echo $carduser_cargo; ?>" placeholder="Cargo:" required/>
					</label>
				</div>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">

			<div class="panel_header default">
				<h2>Foto (1X1) - 500x500px</h2>
			</div>

			<div class="panel">
				<label class="label">
					<input type="file" class="wc_loadimage" name="carduser_thumb"/>
				</label>
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                    $CarduserThumb = (!empty($carduser_thumb) && file_exists(
                        '../uploads/linktree/' . $carduser_thumb
                    ) && !is_dir(
                        '../uploads/linktree/' . $carduser_thumb
                    ) ? 'uploads/linktree/' . $carduser_thumb : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb carduser_thumb" style="display: block; margin: 0 auto !important;
                    border-radius: 50%"
					     src="../tim.php?src=<?php
                         echo $CarduserThumb; ?>&w=500&h=auto"
					     default="../tim.php?src=<?php
                         echo $CarduserThumb; ?>&w=500&h=auto"/>
				</div>

				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center; margin-bottom: 10px;">

                    <?php
                    echo Check::switchOnOff('carduser_status', $carduser_status); ?>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
			</div>
		</div>
	</form>
</div>
