<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (!APP_PARTNERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_PARTNERS) {
        echo Check::accessBlocked();
    }
    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $PartnerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PartnerId) {
        $Read->exeRead(DB_PARTNERS, 'WHERE partner_id = :id', 'id=' . $PartnerId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um parceiro que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=partners/home');
            exit;
        }
    } else {
        $PartnerCreate = ['partner_name' => '', 'partner_page' => ''];
        $Create->exeCreate(DB_PARTNERS, $PartnerCreate);
        header('Location: dashboard.php?wc=partners/create&id=' . $Create->getResult());
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-pen"><?php
                echo $partner_name ?? 'Novo Parceiro'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=partners/home">Parceiros</a>
			<span class="crumb">/</span>
			Gerenciar Parceiro
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Parceiros" href="dashboard.php?wc=partners/home" class="btn btn_blue icon-eye">Ver Todos</a>
		<a title="Novo Parceiro" href="dashboard.php?wc=partners/create" class="btn btn_green icon-plus">Adicionar</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="partner_create" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Partners"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="partner_id" value="<?php
            echo $PartnerId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Nome do parceiro:</span>
					<input class="font_medium" type="text" name="partner_name" value="<?php
                        echo $partner_name; ?>"
					       placeholder="Nome do Parceiro:" required/>
				</label>
				<label class="label">
					<span class="legend">Site do parceiro:</span>
					<input class="font_medium" type="text" name="partner_page" value="<?php
                        echo $partner_page; ?>"
					       placeholder="Site do Parceiro:" required/>
				</label>
				<label class="label border_top">
					<span class="legend">Foto: (JPG 300X200px):</span>
					<input type="file" class="wc_loadimage" name="partner_image"/>
				</label>
				<div class="clear"></div>
			</div>
		</div>
		<div class="box box30">
			<div class="box_content">
                <?php
                    $Image = (file_exists('../uploads/' . $partner_image) && !is_dir(
                        '../uploads/' . $partner_image
                    ) ? 'uploads/' . $partner_image : 'admin/_img/no_image.jpg');
                ?>
				<div class="panel_header al_center">
					<img class='partner_image' src="../tim.php?src=<?php
                        echo $Image; ?>&w=300&h=auto"
					     default="../tim.php?src=<?php
                             echo $Image; ?>&w=300&h=200">
				</div>

				<div class="box_content">
					<div class="wc_actions justify-content-end">
						<button name="public" value="1" class="btn btn_save"><img class='form_load'
						                                                          alt='Enviando Requisição!'
						                                                          title='Enviando Requisição!'
						                                                          src='_img/load_w.gif'/>Salvar
						</button>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</form>
</div>
