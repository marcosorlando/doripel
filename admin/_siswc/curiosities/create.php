<?php

    use App\Conn\Create;
    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_CURIOSITIES;
    if (!APP_CURIOSITIES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $CurId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($CurId) {
        $Read->exeRead(DB_CURIOSITIES, 'WHERE cur_id = :id', 'id=' . $CurId);

        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar UMA CURIOSIDADE que não existe ou que foi removida recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=curiosities/home');

            exit;
        }
    } else {
        $Read->fullRead('SELECT count(cur_id) as Total FROM ' . DB_CURIOSITIES . ' WHERE cur_status = :st', 'st=1');

        $curCreate = [
            'cur_created' => date('Y-m-d H:i:s'),
            'cur_status' => 0,
        ];
        $Create->exeCreate(DB_CURIOSITIES, $curCreate);
        header('Location: dashboard.php?wc=curiosities/create&id=' . $Create->getResult());
    }

?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-newspaper"><?php
                echo $cur_title ?: 'Nova Curiosidade'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=curiosities/home">Curiosidades</a>
			<span class="crumb">/</span>
			Gerenciar Curiosidade
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE . '/#funfact'; ?>"
		   class="wc_view btn btn_green icon-eye">Ver no Site!</a>
	</div>
</header>

<div class="dashboard_content single_cur_form">
	<form class="auto_save" name="curiosidades" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Curiosities"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="cur_id" value="<?php
            echo $CurId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Título:</span>
					<input class="font_big" type="text" name="cur_title" value="<?php
                        echo $cur_title; ?>"
					       placeholder="Título" required/>
				</label>

				<label class="label">
					<span class="legend">Sub título:</span>
					<textarea class="font_medium" name="cur_subtitle" rows="2"
					          required><?php
                            echo $cur_subtitle; ?></textarea>
				</label>
				<div class="clear"></div>

				<fieldset class="form_line">
					<legend>ITEM - 1</legend>
					<input type="file" class="wc_loadimage" id="cur_line_one_icon" name="cur_line_one_icon"/>
                    <?php
                        $icone1 = (file_exists('../uploads/' . $cur_line_one_icon) && !is_dir(
                            '../uploads/' . $cur_line_one_icon
                        ) ? 'uploads/' . $cur_line_one_icon : 'admin/_img/no_image.jpg');
                    ?>

					<div class="row">

						<label class="icone" for="cur_line_one_icon">
							<img class="cur_line_one_icon" alt="Ícone do Curiosidade" title="Ícone do Curiosidade"
							     src="../tim.php?src=<?php
                                     echo $icone1; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>"
							     default="../tim.php?src=<?php
                                     echo $icone1; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>">
						</label>

						<input class="qtde" type="number" step="1" min="0" name="cur_line_one_int" value="<?php
                            echo $cur_line_one_int;
                        ?>"
						       required/>

						<input class="unit" type="text" name="cur_line_one_unit" value="<?php
                            echo $cur_line_one_unit; ?>"
						       placeholder="Unidade" required/>

						<input class="rotulo" type="text" name="cur_line_one_label" value="<?php
                            echo $cur_line_one_label; ?>"
						       placeholder="Rótulo" required/>
					</div>

					<textarea name="cur_line_one_text" rows="2" placeholder="Texto paragráfo"
					          required><?php
                            echo $cur_line_one_text; ?></textarea>


				</fieldset>

				<fieldset class="form_line">
					<legend>ITEM - 2</legend>
					<input type="file" class="wc_loadimage" id="cur_line_two_icon" name="cur_line_two_icon"/>
                    <?php
                        $icone2 = (file_exists('../uploads/' . $cur_line_two_icon) && !is_dir(
                            '../uploads/' . $cur_line_two_icon
                        ) ? 'uploads/' . $cur_line_two_icon : 'admin/_img/no_image.jpg');
                    ?>

					<div class="row">

						<label class="icone" for="cur_line_two_icon">
							<img class="cur_line_two_icon" alt="Ícone do Curiosidade" title="Ícone do Curiosidade"
							     src="../tim.php?src=<?php
                                     echo $icone2; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>"
							     default="../tim.php?src=<?php
                                     echo $icone2; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>">
						</label>

						<input class="qtde" type="number" step="1" min="0" name="cur_line_two_int" value="<?php
                            echo $cur_line_two_int;
                        ?>"
						       required/>

						<input class="unit" type="text" name="cur_line_two_unit" value="<?php
                            echo $cur_line_two_unit; ?>"
						       placeholder="Unidade" required/>

						<input class="rotulo" type="text" name="cur_line_two_label" value="<?php
                            echo $cur_line_two_label; ?>"
						       placeholder="Rótulo" required/>
					</div>

					<textarea name="cur_line_two_text" rows="2" placeholder="Texto paragráfo"
					          required><?php
                            echo $cur_line_two_text; ?></textarea>


				</fieldset>

				<fieldset class="form_line">
					<legend>ITEM - 3</legend>
					<input type="file" class="wc_loadimage" id="cur_line_three_icon" name="cur_line_three_icon"/>
                    <?php
                        $icone3 = (file_exists('../uploads/' . $cur_line_three_icon) && !is_dir(
                            '../uploads/' . $cur_line_three_icon
                        ) ? 'uploads/' . $cur_line_three_icon : 'admin/_img/no_image.jpg');
                    ?>

					<div class="row">

						<label class="icone" for="cur_line_three_icon">
							<img class="cur_line_three_icon" alt="Ícone do Curiosidade" title="Ícone do Curiosidade"
							     src="../tim.php?src=<?php
                                     echo $icone3; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>"
							     default="../tim.php?src=<?php
                                     echo $icone3; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>">
						</label>

						<input class="qtde" type="number" step="1" min="0" name="cur_line_three_int" value="<?php
                            echo $cur_line_three_int;
                        ?>"
						       required/>

						<input class="unit" type="text" name="cur_line_three_unit" value="<?php
                            echo $cur_line_three_unit; ?>"
						       placeholder="Unidade" required/>

						<input class="rotulo" type="text" name="cur_line_three_label"
						       value="<?php
                                   echo $cur_line_three_label; ?>"
						       placeholder="Rótulo" required/>
					</div>

					<textarea name="cur_line_three_text" rows="2" placeholder="Texto paragráfo"
					          required><?php
                            echo $cur_line_three_text; ?></textarea>

				</fieldset>

				<fieldset class="form_line">
					<legend>ITEM - 4</legend>
					<input type="file" class="wc_loadimage" id="cur_line_four_icon" name="cur_line_four_icon"/>
                    <?php
                        $icone4 = (file_exists('../uploads/' . $cur_line_four_icon) && !is_dir(
                            '../uploads/' . $cur_line_four_icon
                        ) ? 'uploads/' . $cur_line_four_icon : 'admin/_img/no_image.jpg');
                    ?>

					<div class="row">

						<label class="icone" for="cur_line_four_icon">
							<img class="cur_line_four_icon" alt="Ícone do Curiosidade" title="Ícone do Curiosidade"
							     src="../tim.php?src=<?php
                                     echo $icone4; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>"
							     default="../tim.php?src=<?php
                                     echo $icone4; ?>&w=<?php
                                     echo AVATAR_W; ?>&h=<?php
                                     echo AVATAR_H; ?>">
						</label>

						<input class="qtde" type="number" step="1" min="0" name="cur_line_four_int" value="<?php
                            echo $cur_line_four_int;
                        ?>"
						       required/>

						<input class="unit" type="text" name="cur_line_four_unit" value="<?php
                            echo $cur_line_four_unit; ?>"
						       placeholder="Unidade" required/>

						<input class="rotulo" type="text" name="cur_line_four_label"
						       value="<?php
                                   echo $cur_line_four_label; ?>"
						       placeholder="Rótulo" required/>
					</div>

					<textarea name="cur_line_four_text" rows="2" placeholder="Texto paragráfo"
					          required><?= $cur_line_four_text; ?></textarea>

				</fieldset>


			</div>
		</div>

		<div class="box box30">

			<div class='label'>
				<label class='label'>
					<span class='legend'>Imagem (JPG <?php
                            echo THUMB_W; ?>x<?php
                            echo THUMB_H; ?>px):</span>
					<input type="file" class="wc_loadimage" name="cur_cover"/>
				</label>
			</div>

            <?php
                $Image = (file_exists('../uploads/' . $cur_cover) && !is_dir(
                    '../uploads/' . $cur_cover
                ) ? 'uploads/' . $cur_cover : 'admin/_img/no_image.jpg');
            ?>
			<img class="cur_cover" alt="Capa do Curiosidade" title="Capa do Curiosidade"
			     src="../tim.php?src=<?php
                     echo $Image; ?>&w=952&h=auto"
			     default="../tim.php?src=<?php
                     echo $Image; ?>&w=<?php
                     echo THUMB_W; ?>&h=<?php
                     echo THUMB_H; ?>">

			<div class="box_content">

				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center">
					<div class='switch'>
						<input name='cur_status' type='checkbox' id='cur_status'
						       value='1' <?php
                            echo 1 == $cur_status ? 'checked' : ''; ?>>
						<label for="cur_status" data-on="ON" data-off="OFF"></label>
					</div>
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
