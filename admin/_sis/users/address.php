<?php

    use App\Conn\Create;
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

    $AddrId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $UserId = \filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);
    if ($AddrId) {
        $Read->exeRead(DB_USERS_ADDR, 'WHERE addr_id = :id', 'id=' . $AddrId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)(\is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);

            $Read->exeRead(DB_USERS, 'WHERE user_id = :user', 'user=' . $user_id);
            if ($Read->getResult()) {
                \extract($Read->getResult()[0]);
            } else {
                $_SESSION['trigger_controll'] = Check::erro(
                    \sprintf(
                        '<b>OPPSS %s</b>, você tentou editar um endereço que não existe ou que foi removido recentemente!',
                        $Admin['user_name']
                    ),
                    E_USER_NOTICE
                );
                \header('Location: dashboard.php?wc=users/home');

                exit;
            }
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                \sprintf(
                    '<b>OPPSS %s</b>, você tentou editar um endereço que não existe ou que foi removido recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            \header('Location: dashboard.php?wc=users/home');

            exit;
        }
    } elseif ($UserId) {
        $NewAddres = ['user_id' => $UserId, 'addr_name' => 'Novo Endereço'];
        $Create->exeCreate(DB_USERS_ADDR, $NewAddres);
        \header('Location: dashboard.php?wc=users/address&id=' . $Create->getResult());

        exit;
    } else {
        $_SESSION['trigger_controll'] = Check::erro(
            \sprintf(
                '<b>OPPSS %s</b>, você tentou editar um endereço que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            ),
            E_USER_NOTICE
        );
        \header('Location: dashboard.php?wc=users/home');

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-truck">Endereço de <?php
                echo \sprintf('%s %s', $user_name, $user_lastname); ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=users/home">Usuários</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=users/create&id=<?php
                echo $user_id; ?>"><?php
                    echo \sprintf(
                        '%s %s',
                        $user_name,
                        $user_lastname
                    ); ?></a>
			<span class="crumb">/</span>
            <?php
                echo $addr_name; ?>
		</p>
	</div>

	<div class="dashboard_header_search" style="font-size: 0.875em; margin-top: 16px;">
		<a class="btn btn_blue icon-undo2" title="<?php
            echo ADMIN_NAME; ?>"
		   href="dashboard.php?wc=users/create&id=<?php
               echo $user_id; ?>">Conta de <?php
                echo $user_name; ?></a>
	</div>

</header>

<div class="dashboard_content">
	<div class="box box100">
		<div class="panel">
			<form class="auto_save" name="user_add_address" action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="callback" value="Users"/>
				<input type="hidden" name="callback_action" value="addr_add"/>
				<input type="hidden" name="addr_id" value="<?php
                    echo $AddrId; ?>"/>

				<label class="label">
					<span class="legend">Nome do Endereço:</span>
					<input name="addr_name" style="font-size: 1.3em;" value="<?php
                        echo $addr_name; ?>"
					       placeholder="Ex: Minha Casa:" required/>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">CEP:</span>
						<input name="addr_zipcode" value="<?php
                            echo $addr_zipcode; ?>" class="formCep wc_getCep"
						       placeholder="Informe o CEP:" required/>
					</label>

					<label class="label">
						<span class="legend">Rua:</span>
						<input class="wc_logradouro" name="addr_street" value="<?php
                            echo $addr_street; ?>"
						       placeholder="Nome da Rua:" required/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Número:</span>
						<input name="addr_number" value="<?php
                            echo $addr_number; ?>" placeholder="Número:" required/>
					</label>

					<label class="label">
						<span class="legend">Complemento:</span>
						<input class="wc_complemento" name="addr_complement" value="<?php
                            echo $addr_complement; ?>"
						       placeholder="Ex: Casa, Apto, Etc:"/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Bairro:</span>
						<input class="wc_bairro" name="addr_district" value="<?php
                            echo $addr_district; ?>"
						       placeholder="Nome do Bairro:" required/>
					</label>

					<label class="label">
						<span class="legend">Cidade:</span>
						<input class="wc_localidade" name="addr_city" value="<?php
                            echo $addr_city; ?>"
						       placeholder="Informe a Cidade:" required/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Estado (UF):</span>
						<input class="wc_uf" name="addr_state" value="<?php
                            echo $addr_state; ?>" maxlength="2"
						       placeholder="Ex: SP" required/>
					</label>

					<label class="label">
						<span class="legend">País:</span>
						<input name="addr_country" value="<?php
                            echo $addr_country ? $addr_country : 'Brasil'; ?>" required/>
					</label>
				</div>

				<p>&nbsp;</p>
				<img class="form_load none fl_right" style="margin-left: 10px; margin-top: 2px;"
				     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<button name="public" value="1" class="btn btn_green fl_right icon-share" style="margin-left: 5px;">
					Atualizar Endereço!
				</button>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
