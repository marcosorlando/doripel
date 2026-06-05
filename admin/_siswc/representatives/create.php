<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    //$rep_all_cities = 1;

    $AdminLevel = LEVEL_WC_REPRESENTATIVES;
    if (!APP_REPRESENTATIVES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }
    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $RepresentativeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($RepresentativeId) {
        $Read->exeRead(DB_REPRESENTATIVES, 'WHERE rep_id = :id', 'id=' . $RepresentativeId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um representante que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=representatives/home');
        }
    } else {
        $RepresentativeCreate = ['rep_created' => date('Y-m-d H:i:s')];
        $Create->exeCreate(DB_REPRESENTATIVES, $RepresentativeCreate);
        header('Location: dashboard.php?wc=representatives/create&id=' . $Create->getResult());
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pen"><?php
                echo $rep_name ?? 'Novo Representante'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=representatives/home">Representantes</a>
			<span class="crumb">/</span> Gerenciar Represenante
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Depoimentos" href="dashboard.php?wc=representatives/home" class="btn btn_blue icon-eye">Ver</a>
		<a title="Novo Depoimento" href="dashboard.php?wc=representatives/create" class="btn btn_green icon-plus">Adicionar</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="deposition_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Representatives"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="rep_id" value="<?php
            echo $RepresentativeId; ?>"/>

		<div class="box box100">
			<div class="box_content">
				<label class="label">
					<span class="legend">Nome da Empresa:</span>
					<input class="font_medium" type="text" name="rep_company" value="<?php
                        echo $rep_company; ?>"
					       placeholder="Nome da Empresa:" required/>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Estado</span>
						<select name="rep_uf" id="UF" required="required">
							<option value="0"> - Selecione -</option>
                            <?php

                                $Read->exeRead(DB_STATES);
                                if ($Read->getResult()) {
                                    foreach ($Read->getResult() as $States) {
                                        extract($States);
                                        echo sprintf(
                                                "<option value='%s' ",
                                                $uf
                                            ) . ($uf == $rep_uf ? 'selected' : '') . sprintf(
                                                '>%s - %s</option>',
                                                $uf,
                                                $name
                                            );
                                    }
                                }
                            ?>
						</select>
					</label>

					<label class="label">
						<span class="legend">Cidade:</span>
						<select id='rep_city' name='rep_city' class='cities_return' required="required">

                            <?php
                                $Read->exeRead(DB_CITIES, 'WHERE uf = :uf ORDER BY name', 'uf=' . $rep_uf);
                                if ($Read->getResult()) {
                                    echo "<option value='0'>- Selecione a cidade -</option>";
                                    foreach ($Read->getResult() as $Cities) {
                                        extract($Cities);
                                        echo sprintf(
                                                "<option value='%s' ",
                                                $name
                                            ) . ($name == $rep_city ? 'selected' : '') . sprintf(
                                                ' >%s</option>',
                                                $name
                                            );
                                    }
                                }
                            ?>
						</select>

					</label>
				</div>
				<div class="label_50">
					<label class="label">
						<span class="legend">Nome do Contato:</span>
						<input type="text" name="rep_name" value="<?php
                            echo $rep_name; ?>" placeholder="Nome da contato:"
						       required/>
					</label>
					<label class="label">
						<span class="legend">E-mail:</span>
						<input type="email" name="rep_email" value="<?php
                            echo $rep_email; ?>" placeholder="E-mail:" required/>
					</label>
				</div>
				<div class="label_50">
					<label class="label">
						<span class="legend">Telefone:</span>
						<input type="text" name="rep_phone" class="formPhone" value="<?php
                            echo $rep_phone; ?>"
						       placeholder="Telefone:" required/>
					</label>
					<label class="label">
						<span class="legend">Celular/WhatsApp:</span>
						<input type="text" name="rep_cellphone" class="formPhone" value="<?php
                            echo $rep_cellphone; ?>"
						       placeholder="Celular/WhatsApp:" required/>
					</label>
				</div>
				<label class="label">
                    <?php
                        echo Check::switchOnOff(
                            'rep_all_cities',
                            $rep_all_cities,
                            "ATENDE TODO ESTADO - <b id='rep-uf'>" . $rep_uf ?? '</b>',
                            'SIM',
                            'NÃO'
                        ); ?>
					<span class='legend font_medium'>Cidades Atendidas (separadas por vírgula ( , ):</span>

					<textarea class="allcities" name="rep_cities" rows="6"
					          placeholder="Inserir cidades atendidas separadas por (, ) vírgula."
					          required><?= $rep_cities ??= ''; ?></textarea>
				</label>

				<div class="clear"></div>
				<div class="m_top">&nbsp;</div>
				<div class="wc_actions">
					<button name="public" value="1" class="btn btn_green icon-share"> ATUALIZAR
						<img class='form_load'
						     alt='Enviando Requisição!'
						     title='Enviando Requisição!'
						     src='_img/load_w.gif'/>
					</button>
				</div>
			</div>
		</div>

	</form>
</div>
