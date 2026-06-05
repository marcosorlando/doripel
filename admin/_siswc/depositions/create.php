<?php

    use App\Conn\Create;
    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_DEPOSITIONS;
    if (!APP_DEPOSITIONS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }
    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $DepositionId = \filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($DepositionId) {
        $Read->exeRead(DB_DEPOSITIONS, 'WHERE depositions_id = :id', 'id=' . $DepositionId);
        if ($Read->getResult()) {
            $FormData = \array_map(
                fn($v) => \htmlspecialchars((string)(\is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            \extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = \sprintf(
                '<b>OPPSS %s</b>, você tentou editar um depoimento que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            \header('Location: dashboard.php?wc=depositions/home');
            exit;
        }
    } else {
        $DepositionCreate = ['depositions_date' => \date('Y-m-d H:i:s')];
        $Create->exeCreate(DB_DEPOSITIONS, $DepositionCreate);
        \header('Location: dashboard.php?wc=depositions/create&id=' . $Create->getResult());
        exit;
    }

    $depositions_name = $depositions_name ?? '';
    $depositions_profession = $depositions_profession ?? '';
    $depositions_text = $depositions_text ?? '';
    $depositions_image = $depositions_image ?? '';
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pen"><?php
                echo $depositions_name ?? 'Novo Depoimento'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=depositions/home">Depoimentos</a>
			<span class="crumb">/</span>
			Gerenciar Depoimento
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Depoimentos" href="dashboard.php?wc=depositions/home" class="btn btn_blue icon-eye">Ver
			Todos</a>
		<a title="Novo Depoimento" href="dashboard.php?wc=depositions/create" class="btn btn_green icon-plus">Adicionar
		</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="deposition_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Depositions"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="depositions_id" value="<?php
            echo $DepositionId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Nome do depoente:</span>
					<input class="font_large" type="text" name="depositions_name"
					       value="<?php
                               echo $depositions_name; ?>" placeholder="Nome do Depoente:" required/>
				</label>
				<label class="label">
					<span class="legend">Profissão do depoente:</span>
					<input class="font_medium" type="text" name="depositions_profession"
					       value="<?php
                               echo $depositions_profession; ?>" placeholder="Profissão do Depoente:" required/>
				</label>
				<label class="label">
					<span class="legend">Depoimento:</span>
					<textarea name="depositions_text" rows="6" required><?php
                            echo $depositions_text; ?></textarea>
				</label>
				<label class="label border_top">
					<span class="legend">Foto: (JPG 500X500px):</span>
					<input type="file" class="wc_loadimage" name="depositions_image"/>
				</label>
				<div class="clear"></div>
			</div>
		</div>
		<div class="box box30">
			<div class="box_content">
                <?php
                    $Image = (\file_exists('../uploads/' . $depositions_image) && !\is_dir(
                        '../uploads/' . $depositions_image
                    ) ? 'uploads/' . $depositions_image : 'admin/_img/no_image.jpg');
                ?>
				<img class="depositions_image" src="../tim.php?src=<?php
                    echo $Image; ?>&w=500&h=500"
				     default="../tim.php?src=<?php
                         echo $Image; ?>&w=500&h=500">
				<div class="box_content">
					<div class="m_top">&nbsp;</div>
					<div class="wc_actions" style="text-align: center">
						<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
						<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
						     title="Enviando Requisição!" src="_img/load.gif"/>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</form>
</div>
