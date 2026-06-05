<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_HELLO;
    if (!APP_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $HelloId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($HelloId) {
        $Read->exeRead(DB_HELLO, 'WHERE hello_id = :id', 'id=' . $HelloId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)(\is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                \sprintf(
                    '<b>Oopss %s</b>, você tentou editar uma hellobar que não existe ou que foi removida recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            \header('Location: dashboard.php?wc=hello/home');
        }
    } else {
        $HelloCreate = ['hello_date' => \date('Y-m-d H:i:s'), 'hello_status' => 0, 'user_id' => $Admin['user_id']];
        $Create->exeCreate(DB_HELLO, $HelloCreate);
        \header('Location: dashboard.php?wc=hello/create&id=' . $Create->getResult());
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-bullhorn"><?php
                echo $hello_title ? $hello_title : 'Nova Hellobar'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=hello/home">Hellobar</a>
			<span class="crumb">/</span>
			Gerenciar Hellobar
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Voltar" href="dashboard.php?wc=hello/home" class="btn btn_blue icon-bullhorn">Voltar</a>
	</div>
</header>

<div class="dashboard_content">

	<form name="hello_add" class="auto_save" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Custom"/>
		<input type="hidden" name="callback_action" value="hellobar_update"/>
		<input type="hidden" name="hello_id" value="<?php
            echo $HelloId; ?>"/>

		<div class="box box70">
			<div class="panel">
				<label class="label">
					<span class="legend">Capa (Largura de <?php
                            echo IMAGE_W; ?>px):</span>
					<input type="file" class="wc_loadimage" name="hello_cover"/>
				</label>

				<label class="label">
					<span class="legend">Headline:</span>
					<input class="font_large" type="text" name="hello_title" value="<?php
                        echo $hello_title; ?>"
					       placeholder="Título da Hellobar:" required/>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">CTA (texto do botão):</span>
						<input type="text" name="hello_cta" value="<?php
                            echo $hello_cta; ?>" placeholder="Texto do botão:"
						       required/>
					</label>

					<label class="label">
						<span class="legend">Link:</span>
						<input type="text" name="hello_link" value="<?php
                            echo $hello_link; ?>" placeholder="Link de ação:"
						       required/>
					</label>
				</div>

				<label class="label">
					<span class="legend">Cor do botão?</span>
					<select name="hello_color" required="required">
						<option value="">Selecione a cor</option>
						<option <?php
                            echo 'blue' == $hello_color ? 'selected="selected"' : ''; ?> value="blue">Azul
						</option>
						<option <?php
                            echo 'green' == $hello_color ? 'selected="selected"' : ''; ?> value="green">Verde
						</option>
						<option <?php
                            echo 'yellow' == $hello_color ? 'selected="selected"' : ''; ?> value="yellow">Amarelo
						</option>
						<option <?php
                            echo 'red' == $hello_color ? 'selected="selected"' : ''; ?> value="red">Vermelho
						</option>
					</select>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Onde você quer exibir?</span>
						<select name="hello_position" required="required">
							<option value="">Selecione a posição</option>
							<option <?php
                                echo 'center' == $hello_position ? 'selected="selected"' : ''; ?> value="center">Ao
								centro da página!
							</option>
							<option <?php
                                echo 'right_top' == $hello_position ? 'selected="selected"' : ''; ?>
									value="right_top">Direita Acima!
							</option>
							<option <?php
                                echo 'right_bottom' == $hello_position ? 'selected="selected"' : ''; ?>
									value="right_bottom">Direita Abaixo!
							</option>
						</select>
					</label>

					<label class="label">
                        <span class="legend">Regra de exibição: <span class="icon-info icon-notext wc_tooltip"><span
				                        class="wc_tooltip_balloon">Defina uma palavra chave para disparar sua hellobar!</span></span></span>
						<input type="text" name="hello_rule" value="<?php
                            echo $hello_rule; ?>"
						       placeholder="Regra de exibição:"/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Exibir a partir de:</span>
						<input class="jwc_datepicker" data-timepicker="true" readonly="readonly" type="text"
						       name="hello_start" value="<?php
                            echo empty($hello_start) ? \date('d/m/Y H:i') : \date(
                                'd/m/Y H:i',
                                \strtotime((string)$hello_start)
                            ); ?>" placeholder="Início da programação:" required/>
					</label>

					<label class="label">
						<span class="legend">Parar dia:</span>
						<input class="jwc_datepicker" data-timepicker="true" readonly="readonly" type="text"
						       name="hello_end"
						       value="<?php
                                   echo empty($hello_end) ? \date('d/m/Y H:i', \strtotime('+10days')) : \date(
                                       'd/m/Y H:i',
                                       \strtotime((string)$hello_end)
                                   ); ?>" placeholder="Encerramento da programação:" required/>
					</label>
				</div>


				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class="post_create_cover">
                <?php
                    $HelloImage = (!empty($hello_image) && \file_exists('../uploads/' . $hello_image) && !\is_dir(
                        '../uploads/' . $hello_image
                    ) ? 'uploads/' . $hello_image : 'admin/_img/no_image.jpg');
                ?>
				<img style="width: 100%;" class="hello_cover" alt="Imagem da Hellobar" title="Imagem da Hellobar"
				     src="../tim.php?src=<?php
                         echo $HelloImage; ?>&w=<?php
                         echo IMAGE_W / 3; ?>&h=auto" default="../tim.php?src=<?php
                    echo $HelloImage; ?>&w=<?php
                    echo IMAGE_W / 3; ?>&h=auto"/>
			</div>
			<div class="panel">
				<div class="wc_actions">
                    <?php
                        echo Check::switchOnOff('hello_status', $hello_status, 'Publicar:', 'SIM', 'NÃO'); ?>
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
			</div>
		</div>
	</form>
</div>
