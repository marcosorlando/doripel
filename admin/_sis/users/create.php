<?php

    use App\Conn\Create;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_USERS;
    if (!APP_USERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT READ
    $Create ??= new Create();

    $UserId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($UserId) {
        $Read->exeRead(DB_USERS, 'WHERE user_id = :id', 'id=' . $UserId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);

            if ($user_level > $_SESSION['userLogin']['user_level']) {
                $_SESSION['trigger_controll'] = sprintf(
                    '<b>OPPSS %s</b>. Por questões de segurança, é restrito o acesso a usuário com nível de acesso maior que o seu!',
                    $Admin['user_name']
                );
                header('Location: dashboard.php?wc=users/home');

                exit;
            }
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um usuário que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=users/home');

            exit;
        }
    } else {
        $CreateUserDefault = [
            'user_registration' => date('Y-m-d H:i:s'),
            'user_level' => 1,
        ];
        $Create->exeCreate(DB_USERS, $CreateUserDefault);
        header('Location: dashboard.php?wc=users/create&id=' . $Create->getResult());

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-user-plus">Novo Usuário</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=users/home">Usuários</a>
			<span class="crumb">/</span>
			Novo Usuário
		</p>
	</div>

	<div class="dashboard_header_search" style="font-size: 0.875em; margin-top: 16px;" id="<?php
        echo $UserId; ?>">
		<span rel='dashboard_header_search' class='j_delete_action icon-warning btn btn_red' id='<?php
            echo $UserId; ?>'>Deletar Usuário!</span>
		<span rel='dashboard_header_search' callback='Users' callback_action='delete'
		      class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='<?php
            echo $UserId; ?>'>EXCLUIR AGORA!</span>
	</div>
</header>

<div class="dashboard_content dashboard_users">
	<div class="box box70">
		<article class="wc_tab_target wc_active" id="profile">

			<div class="panel_header default">
				<h2 class="icon-user-plus">Dados de <?php
                        echo $user_name; ?></h2>
			</div>

			<div class="panel">
				<form class="auto_save form_capitalize" class="j_tab_home tab_create" name="user_manager" action=""
				      method="post"
				      enctype="multipart/form-data">
					<input type="hidden" name="callback" value="Users"/>
					<input type="hidden" name="callback_action" value="manager"/>
					<input type="hidden" name="user_id" value="<?php
                        echo $UserId; ?>"/>

					<div class="label_50">
						<label class="label">
							<span class="legend">Primeiro nome:</span>
							<input value="<?php
                                echo $user_name; ?>" type="text" name="user_name" placeholder="Primeiro Nome:"
							       required/>
						</label>

						<label class="label">
							<span class="legend">Sobrenome:</span>
							<input value="<?php
                                echo $user_lastname; ?>" type="text" name="user_lastname"
							       placeholder="Sobrenome:"
							       required/>
						</label>
					</div>
					<label class="label">
						<span class="legend">Descrição: (Max.: 150 caracteres)</span>
						<textarea name="user_description" placeholder="Descrição do usuário"
						          required/><?php
                            echo $user_description; ?></textarea>
					</label>

					<div class="label_33">
						<label class="label">
							<span class="legend">CPF:</span>
							<input value="<?php
                                echo $user_document; ?>" type="text" name="user_document" class="formCpf"
							       placeholder="CPF:"/>
						</label>
						<label class="label">
							<span class="legend">Telefone:</span>
							<input value="<?php
                                echo $user_telephone; ?>" class="formPhone" type="text" name="user_telephone"
							       placeholder="(55) 5555.5555"/>
						</label>

						<label class="label">
							<span class="legend">Celular:</span>
							<input value="<?php
                                echo $user_cell; ?>" class="formPhone" type="text" name="user_cell"
							       placeholder="(XX) XXXXX-XXXX"/>
						</label>
					</div>

					<div class="clear"></div>

                    <?php
                        if ($user_level < 10 || 10 === $_SESSION['userLogin']['user_level']) { ?>
							<div class="label_50">
								<label class="label">
									<span class="legend">Nível de acesso:</span>
									<select name="user_level" required>
										<option selected disabled value="">Selecione o nível de acesso:</option>
                                        <?php

                                            foreach (Check::getWcLevel() as $Nivel => $Desc) {
                                                if ($Nivel <= $_SESSION['userLogin']['user_level']) {
                                                    echo '<option';
                                                    if ($Nivel == $user_level) {
                                                        echo " selected='selected'";
                                                    }
                                                    echo sprintf(" value='%s'>%s</option>", $Nivel, $Desc);
                                                }
                                            }
                                        ?>
									</select>
								</label>

								<label class="label">
									<span class="legend">Gênero do Usuário:</span>
									<select name="user_genre" required>
										<option selected disabled value="">Selecione o Gênero do Usuário:</option>
										<option value="1" <?php
                                            echo 1 == $user_genre ? 'selected="selected"' : ''; ?>>
											Masculino
										</option>
										<option value="2" <?php
                                            echo 2 == $user_genre ? 'selected="selected"' : ''; ?>>Feminino
										</option>
									</select>
								</label>
							</div>
                            <?php
                        } else { ?>
							<label class="label">
								<span class="legend">Gênero do Usuário:</span>
								<select name="user_genre" required>
									<option selected disabled value="">Selecione o Gênero do Usuário:</option>
									<option value="1" <?php
                                        echo 1 == $user_genre ? 'selected="selected"' : ''; ?>>Masculino
									</option>
									<option value="2" <?php
                                        echo 2 == $user_genre ? 'selected="selected"' : ''; ?>>Feminino
									</option>
								</select>
							</label>
                            <?php
                        } ?>
					<div class="label_50">
						<label class="label">
							<span class="legend">E-mail:</span>
							<input value="<?php
                                echo $user_email; ?>" type="email" name="user_email" placeholder="E-mail:"
							       required/>
						</label>

						<label class="label">
							<span class="legend">Senha:</span>
							<input value="" type="password" name="user_password" placeholder="Senha:"/>
						</label>
					</div>

					<label class="label">
						<span class="legend">Foto (<?php
                                echo AVATAR_W; ?>x<?php
                                echo AVATAR_H; ?>px, JPG ou PNG):</span>
						<input type="file" name="user_thumb" class="wc_loadimage"/>
					</label>
					<div class="clear"></div>

					<img class="form_load none fl_right" style="margin-left: 10px; margin-top: 2px;"
					     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
					<button name="public" value="1" class="btn btn_green fl_right icon-share" style="margin-left: 5px;">
						Atualizar Usuário!
					</button>
					<div class="clear"></div>
				</form>
			</div>
		</article>

		<article class="box box100 wc_tab_target" id="address" style="padding: 0; margin: 0; display: none;">
			<div class="panel_header default">
        <span>
          <a href="dashboard.php?wc=users/address&user=<?php
              echo $user_id; ?>" class="btn btn_green icon-plus a"
             title="Novo Endereço">Cadastrar Novo</a>
        </span>
				<h2>Endereços </h2>
			</div>
			<div class="panel">
                <?php
                    // DELETE TRASH ADDR
                    if (DB_AUTO_TRASH !== 0) {
                        $Delete = new Delete();
                        $Delete->exeDelete(
                            DB_USERS_ADDR,
                            'WHERE user_id = :id AND addr_street IS NULL AND addr_zipcode IS NULL',
                            'id=' . $user_id
                        );
                    }

                    $Read->exeRead(
                        DB_USERS_ADDR,
                        'WHERE user_id = :user ORDER BY addr_key DESC, addr_name ASC',
                        'user=' . $user_id
                    );
                    if (!$Read->getResult()) {
                        echo Check::erro('Ainda não possui endereços de entrega cadastrados!', E_USER_NOTICE);
                    } else {
                        foreach ($Read->getResult() as $Addr) {
                            $Addr['addr_complement'] = ($Addr['addr_complement'] ? ' - ' . $Addr['addr_complement'] : null);
                            $Primary = ($Addr['addr_key'] ? ' - Principal' : null);
                            echo "<div class='single_user_addr' id='{$Addr['addr_id']}'>
                            <h1 class='icon-home'>{$Addr['addr_name']}{$Primary}</h1>
                            <p>{$Addr['addr_street']}, {$Addr['addr_number']}{$Addr['addr_complement']}</p>
                            <p>B. {$Addr['addr_district']}, {$Addr['addr_city']}/{$Addr['addr_state']}, {$Addr['addr_country']}</p>
                            <p>CEP: {$Addr['addr_zipcode']}</p>

                            <div class='single_user_addr_actions'>
                                <a title='Editar endereço' href='dashboard.php?wc=users/address&id={$Addr['addr_id']}' class='post_single_center icon-notext icon-location btn btn_blue'></a>
                                <span rel='single_user_addr' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$Addr['addr_id']}'></span>
                                <span rel='single_user_addr' callback='Users' callback_action='addr_delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$Addr['addr_id']}'>Deletar Endereço?</span>
                            </div>
                        </div>";
                        }
                    }
                ?>
				<div class="clear"></div>
			</div>
		</article>
	</div>

	<div class="box box30">
        <?php
            $Image = (file_exists('../uploads/' . $user_thumb) && !is_dir(
                '../uploads/' . $user_thumb
            ) ? 'uploads/' . $user_thumb : 'admin/_img/no_avatar.jpg');
        ?>
		<img class="user_thumb" style="width: 100%;" src="../tim.php?src=<?php
            echo $Image; ?>&w=400&h=400" alt="" title=""/>

		<div class="panel">
			<div class="box_conf_menu">
				<a class='conf_menu wc_tab wc_active' href='#profile'>Perfil</a>

				<a class='conf_menu wc_tab' href='#address'>Endereços</a>
			</div>
		</div>
	</div>
</div>
