<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (!APP_PORTFOLIO || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_PORTFOLIO) {
        Check::accessBlocked();
    }

    $Read ??= new Read();
    $Create ??= new Create();
    $ptfDefaults = [
        'id' => null,
        'slug' => '',
        'title' => '',
        'description' => '',
        'client' => '',
        'link_project' => '',
        'deliveryted_at' => '',
        'category' => null,
        'key_metrics' => '',
        'measurement_period' => '',
        'problem' => '',
        'objectives' => '',
        'niche' => '',
        'skills' => '',
        'project_duration' => '',
        'img_970x500' => '',
        'img_450x350' => '',
        'img_350x350' => '',
        'status' => 0,
        'slug' => ''
    ];

    $ptfId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($ptfId) {
        $Read->exeRead(DB_PORTFOLIO, "WHERE id = :id", "id={$ptfId}");
        if ($Read->getResult()) {
            /** @var array<string, mixed> $ptf */
            $ptf = array_replace(
                $ptfDefaults,
                array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0])
            );
        } else {
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um trampo que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=portfolio/home');
            exit;
        }
    } else {
        $postCreate = [
            'status' => 0,
            'author' => $Admin['user_id']
        ];

        $Create->exeCreate(DB_PORTFOLIO, $postCreate);

        if ($Create->getResult()) {
            header('Location: dashboard.php?wc=portfolio/create&id=' . $Create->getResult());
            exit;
        }

        $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, não foi possível criar o novo trampo agora. Tente novamente.";
        header('Location: dashboard.php?wc=portfolio/home');
        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-new-tab">Cadastrar Novo Trampo</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=portfolio/home">Portfólio</a>
			<span class="crumb">/</span>
			Novo Trampo
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?= BASE; ?>/trampo/<?= $ptf['slug']; ?>"
		   class="wc_view btn btn_green icon-eye">Ver trampo no site!</a>
	</div>
</header>

<div class="dashboard_content">
	<form class="auto_save" name="create" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Portfolio"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="id" value="<?= $ptfId; ?>"/>
		<input type="hidden" name="author" value="<?= $Admin['user_id']; ?>"/>

		<article class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Título:</span>
					<input class="font_medium" type="text" name="title" value="<?= $ptf['title']; ?>" required/>
				</label>

				<label class="label" for="description">
					<span class="legend txcontador">Descrição: (MAX: <strong class="caracteres">680</strong> caracteres)</span>
					<textarea class="work_mce" name="description" rows="10"
					          id="description" required><?= $ptf['description']; ?></textarea>

				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Cliente:</span>
						<input type="text" name="client" value="<?= $ptf['client']; ?>" placeholder="Nome fantasia"
						       required/>
					</label>

					<label class="label">
						<span class="legend">Link para projeto:</span>
						<input type="text" name="link_project" value="<?= $ptf['link_project']; ?>"
						       placeholder="Link do projeto"/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Data de entrega:</span>

						<input type="text" class="jwc_datepicker" data-timepicker='false' name="deliveryted_at"
						       readonly="readonly"
						       value="<?= empty($ptf['deliveryted_at']) ? date('d/m/Y') : date(
                                   'd/m/Y',
                                   strtotime((string)$ptf['deliveryted_at'])
                               ); ?>"
						       required/>
					</label>

					<label class="label">
						<span class="legend">Selecione a categoria:</span>
						<select name="category" required>
							<option value="" disabled="disabled" selected="selected">Selecione uma categoria:</option>
                            <?php
                                $Read->fullRead(
                                    "SELECT id, title FROM " . DB_PORTFOLIO_CATEGORIES . " 
                                            WHERE parent IS NULL"
                                );
                                if (!$Read->getResult()) {
                                    echo '<option value="" disabled="disabled">Não existem sessões cadastradas!</option>';
                                } else {
                                    foreach ($Read->getResult() as $parent) {
                                        echo "<option";
                                        if ($ptf['category'] == $parent['id']) {
                                            echo " selected='selected'";
                                        }
                                        echo " value='{$parent['id']}'>{$parent['title']}</option>";
                                    }
                                }
                            ?>
						</select>
					</label>
				</div>

				<div class="panel_header default">
					<h2>Resultados e Métricas</h2>
					<label class='label'>
						<span class='legend'>Principais Métricas Alcançadas::</span>
						<input type='text' name='key_metrics' value="<?= $ptf['key_metrics']; ?>"
						       placeholder='ex: +150% tráfego, 4.2% conversão, R$ 50k faturamento'
						       required/>
					</label>

					<label class='label'>
						<span class='legend'>Período de Medição::</span>
						<input type='text' name='measurement_period' value="<?= $ptf['measurement_period']; ?>"
						       placeholder='ex: primeiros 90 dias, 6 meses, 1 ano'
						       required/>
					</label>
				</div>

				<div class='panel_header default'>
					<h2>🎯 Contexto Estratégico</h2>
					<label class='label'>
						<span class='legend'>Desafio/Problema:</span>
						<input type='text' name='problem' value='<?= $ptf['problem']; ?>'
						       placeholder='o que o cliente precisava resolver'
						       required/>
					</label>

					<label class='label'>
						<span class='legend'>Objetivos do Projeto: </span>
						<input type='text' name='objectives' value='<?= $ptf['objectives']; ?>'
						       placeholder='heckbox: Gerar Leads, Aumentar Vendas, Melhorar SEO, Reduzir CAC, etc.'
						       required/>
					</label>

					<label class='label' for="niche">
						<span class='legend'>Segmento/Nicho:</span>

						<select name="niche" id="niche" required="required">
							<option value="" selected disabled>Selecione...</option>
                            <?php
                                foreach (Check::leadSegmento() as $segment) {
                                    $checked = $ptf['niche'] == $segment ? 'selected' : '';
                                    echo "<option value='{$segment}' $checked>{$segment}</option>";
                                }
                            ?>
						</select>
					</label>
				</div>

				<div class='panel_header default'>
					<h2>🛠️ Soluções Aplicadas</h2>
					<label class='label'>
						<span class='legend'>Principais Serviços/Habilidades:</span>
						<input type='text' name='skills' value='<?= $ptf['skills']; ?>'
						       placeholder='tags: WordPress, WooCommerce, Google Ads, SEO Técnico, etc.'
						       required/>
					</label>

					<label class='label'>
						<span class='legend'>Duração do Projeto:</span>
						<input type='text' name='project_duration' value='<?= $ptf['project_duration']; ?>'
						       placeholder='dropdown: 1-3 meses, 3-6 meses, 6+ meses, Projeto Contínuo'
						       required/>
					</label>
				</div>


				<div class="clear"></div>
			</div>
		</article>

		<article class="box box30">
			<h2>📱 Assets Visuais</h2>
			<label class="label">
				<span class="legend">Capa: (JPG 1200X628px)</span>
				<input type="file" class="wc_loadimage" id="jimg_970x500" name="img_970x500"/>
			</label>

			<label class="label box box50">
				<span class="legend">JPG 450X350px</span>
				<input type="file" class="wc_loadimage" id="jimg_450x350" name="img_450x350"/>
			</label>
			<label class="label box box50">
				<span class="legend">JPG 350X350px</span>
				<input type="file" class="wc_loadimage" id="jimg_350x350" name="img_350x350"/>
			</label>


			<div class="post_create_cover">
				<div class="upload_progress none">0%</div>
                <?php
                    $PostCover = (!empty($ptf['img_970x500']) && file_exists(
                        "../uploads/portfolio/{$ptf['img_970x500']}"
                    ) && !is_dir(
                        "../uploads/portfolio/{$ptf['img_970x500']}"
                    ) ? "uploads/portfolio/{$ptf['img_970x500']}" : 'admin/_img/no_image.jpg');
                    $PostCover2 = (!empty($ptf['img_450x350']) && file_exists(
                        "../uploads/portfolio/{$ptf['img_450x350']}"
                    ) && !is_dir(
                        "../uploads/portfolio/{$ptf['img_450x350']}"
                    ) ? "uploads/portfolio/{$ptf['img_450x350']}" : 'admin/_img/no_image.jpg');
                    $PostCover3 = (!empty($ptf['img_350x350']) && file_exists(
                        "../uploads/portfolio/{$ptf['img_350x350']}"
                    ) && !is_dir(
                        "../uploads/portfolio/{$ptf['img_350x350']}"
                    ) ? "uploads/portfolio/{$ptf['img_350x350']}" : 'admin/_img/no_image.jpg'); ?>

				<img class="post_thumb img_970x500" alt="Capa" id="img_970x500" title="Capa"
				     src="../tim.php?src=<?= $PostCover; ?>&w=970&h=500"
				     default="../tim.php?src=<?= $PostCover; ?>&w=970&h=500"/>

				<div class="works_images">
					<figure class="img_w450">
						<img class="post_thumb img_450x350" alt="Capa" id="img_450x350" title="Capa"
						     src="../tim.php?src=<?= $PostCover2; ?>&w=450&h=350"
						     default="../tim.php?src=<?= $PostCover2; ?>&w=450&h=350"/>
					</figure>
					<figure class="img_w350">
						<img class="post_thumb img_350x350" alt="Capa" id="img_350x350" title="Capa"
						     src="../tim.php?src=<?= $PostCover3; ?>&w=350&h=350"
						     default="../tim.php?src=<?= $PostCover3; ?>&w=350&h=350"/>
					</figure>
				</div>
			</div>

			<div class="panel_header default">
				<h2 class="icon-share">Configurações de Exibição:</h2>
                <?= Check::switchOnOff('show_client', $ptf['show_client'], 'Mostrar cliente:') ?>
                <?= Check::switchOnOff('highlight_case', $ptf['highlight_case'], 'Case destacado:') ?>
				<div class="wc_actions">
                    <?= Check::switchOnOff('status', $ptf['status'], 'Status:') ?>

					<button name="public" value="1" class="btn btn_save">
						<img class='form_load' alt='Enviando Requisição!' src='_img/load_w.gif'/>Salvar
					</button>
				</div>
			</div>

			<div class="panel_header default">
				<h2 class="icon-share2">Compartilhar:</h2>
                <?php
                    $WC_TITLE_LINK = $ptf['title'];
                    $URLSHARE = "/trampo/{$ptf['slug']}";
                    require_once __DIR__ . '/../../../assets/widgets/share/share.wc.php';
                ?>
			</div>

		</article>
	</form>
</div>

<script>
    $(document).on("input", "#txtMensagem", function () {
        var limite = 680;
        var caracteresDigitados = $(this).val().length;
        var caracteresRestantes = limite - caracteresDigitados;
        $(".caracteres").text(caracteresRestantes);
    });
</script>
