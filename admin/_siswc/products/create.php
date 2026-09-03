<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_PRODUCTS_DORIPEL;
    if (!APP_PRODUCTS_DORIPEL || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)) {
        $Read ??= new Read();
    }

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)) {
        $Create ??= new Create();
    }

    $PdtId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PdtId) {
        $Read->exeRead(DB_PDT_DORIPEL, "WHERE pdt_id = :id", "id={$PdtId}");
        if ($Read->getResult()) {
            $FormData = array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0]);
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um produto que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=products/home');
            exit;
        }
    } else {
        $Read->fullRead("SELECT count(pdt_id) as Total FROM " . DB_PDT_DORIPEL . " WHERE pdt_status = :st", "st=1");

        $PdtCreate = [
            'pdt_created' => date('Y-m-d H:i:s'),
            'pdt_status' => 0,
            'pdt_inventory' => 0,
            'pdt_ref' => 0
        ];
        $Create->exeCreate(DB_PDT_DORIPEL, $PdtCreate);
        header('Location: dashboard.php?wc=products/create&id=' . $Create->getResult());
        exit;
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && !empty($Search['s'])) {
        $S = urlencode((string)$Search['s']);
        header("Location: dashboard.php?wc=product/search&s={$S}");
        exit;
    }

    $ProductVolumes = [
        [
            'volume_weight' => $FormData['pdt_dimension_weight'] ?? '0',
            'volume_depth' => $FormData['pdt_dimension_depth'] ?? '0',
            'volume_width' => $FormData['pdt_dimension_width'] ?? '0',
            'volume_height' => $FormData['pdt_dimension_heigth'] ?? '0',
        ],
    ];

    $SecondLegacyVolume = [
        'volume_weight' => $FormData['pdt_dimension_weight_cx2'] ?? '0',
        'volume_depth' => $FormData['pdt_dimension_depth_cx2'] ?? '0',
        'volume_width' => $FormData['pdt_dimension_width_cx2'] ?? '0',
        'volume_height' => $FormData['pdt_dimension_heigth_cx2'] ?? '0',
    ];

    if (array_filter($SecondLegacyVolume, static fn($value) => (float)$value > 0)) {
        $ProductVolumes[] = $SecondLegacyVolume;
    }

    try {
        $Read->exeRead(
            DB_PDT_VOLUMES_DORIPEL,
            "WHERE pdt_id = :id ORDER BY volume_order ASC, volume_id ASC",
            "id={$PdtId}"
        );
        if ($Read->getResult()) {
            $ProductVolumes = array_map(static fn($Volume) => [
                'volume_weight' => Check::safeHtmlChars($Volume['volume_weight'] ?? '0'),
                'volume_depth' => Check::safeHtmlChars($Volume['volume_depth'] ?? '0'),
                'volume_width' => Check::safeHtmlChars($Volume['volume_width'] ?? '0'),
                'volume_height' => Check::safeHtmlChars($Volume['volume_height'] ?? '0'),
            ], $Read->getResult());
        }
    } catch (\Throwable) {
        // Mantém compatibilidade enquanto a tabela auxiliar ainda não foi criada.
    }

    $ProductOptionals = [
        [
            'pdt_optional_ref' => '',
            'pdt_optional_title' => '',
            'pdt_optional_img' => '',
            'pdt_optional_desc' => '',
        ],
    ];

    try {
        $Read->exeRead(
            DB_PDT_OPTIONALS_DORIPEL,
            "WHERE pdt_id = :id ORDER BY optional_order ASC, optional_id ASC",
            "id={$PdtId}"
        );
        if ($Read->getResult()) {
            $ProductOptionals = array_map(static fn($Optional) => [
                'pdt_optional_ref' => Check::safeHtmlChars($Optional['pdt_optional_ref'] ?? ''),
                'pdt_optional_title' => Check::safeHtmlChars($Optional['pdt_optional_title'] ?? ''),
                'pdt_optional_img' => Check::safeHtmlChars($Optional['pdt_optional_img'] ?? ''),
                'pdt_optional_desc' => Check::safeHtmlChars($Optional['pdt_optional_desc'] ?? ''),
            ], $Read->getResult());
        }
    } catch (\Throwable) {
        // Mantém compatibilidade enquanto a tabela auxiliar ainda não foi criada.
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-new-tab"><?= $pdt_title ?: 'Novo Produto'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			Gerenciar Produto
		</p>
	</div>
	
	<div class="dashboard_header_search">
		<a title="Criar Variação deste Produto"
		   href="dashboard.php?wc=products/reply&id=<?= ($pdt_parent ?: $PdtId); ?>"
		   class="btn btn_blue icon-copy">Criar
			Variação!</a>
		<a target="_blank" title="Ver no site" href="<?= BASE; ?>/movel/<?= $pdt_name; ?>"
		   class="wc_view btn btn_green icon-eye">Ver
			no Site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="pdt_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_pdt_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="ProductsDoripel"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>
			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>
			<div style="overflow: auto; max-height: 300px;">
				<img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
				     src="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"
				     default="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
			</div>
			<div class="workcontrol_imageupload_actions">
				<input class="wc_loadimage" type="file" name="image" required/>
				<span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="pdt_control"
				      style="margin-right: 8px;">Fechar</span>
				<button class="btn btn_green icon-image">Enviar e Inserir!</button>
				<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>

<?php
    if (E_PDT_SIZE) { ?>
		<div class="workcontrol_pdt_size">
			<form name="pdt_size" action="" method="post">
				<p class="icon-folder-plus">Estoque por variação:</p>
				<input type="hidden" name="callback" value="ProductsDoripel"/>
				<input type="hidden" name="callback_action" value="pdt_stock"/>
				<input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>
				
				<div class="inputs jwc_product_stock_target">
					<div class="callback_return"></div>
					<div class="clear"></div>
                    <?php
                        $CatSizes = E_PDT_SIZE;
                        if ($pdt_subcategory) {
                            $Read->fullRead(
                                "SELECT cat_sizes FROM " . DB_PDT_CATS_DORIPEL . " WHERE cat_id = :id",
                                "id={$pdt_subcategory}"
                            );
                            if ($Read->getResult() && !empty($Read->getResult()[0]['cat_sizes'])) {
                                $CatSizes = $Read->getResult()[0]['cat_sizes'];
                            }
                        }
                        $WcPdtSize = explode(',', $CatSizes);
                        foreach ($WcPdtSize as $Size) {
                            $Size = trim(rtrim($Size));
                            $Read->fullRead(
                                "SELECT stock_inventory, stock_sold FROM " . DB_PDT_STOCK_DORIPEL . " WHERE pdt_id = :pdt AND stock_code = :key",
                                "pdt={$PdtId}&key={$Size}"
                            );
                            if ($Read->getResult()) {
                                echo "<label><span class='size'>{$Size}:</span><input name='{$Size}' type='number' min='0' value='{$Read->getResult()[0]['stock_inventory']}'><span class='cart'><b class='icon-cart'>" . str_pad(
                                        $Read->getResult()[0]['stock_sold'],
                                        2,
                                        0,
                                        0
                                    ) . "</b></span></label>";
                            } else {
                                echo "<label><span class='size'>{$Size}:</span><input name='{$Size}' type='number' min='0' value='0'><span class='cart'><b class='icon-cart'>00</b></span></label>";
                            }
                        }
                    ?>
				</div>
				<button class="btn btn_green icon-ungroup">Atualizar Estoque!</button>
				<img class="form_load" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<div class="workcontrol_pdt_size_close">X</div>
				<div class="clear"></div>
			</form>
		</div>
        <?php
    } ?>

<div class="dashboard_content single_pdt_form">
	<form class="auto_save" name="manage_pdt" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="ProductsDoripel"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>
		
		<main class="box box70">
			
			<div class="panel_header default">
				<h2 class='icon-newspaper'>Dados do Produto:</h2>
			</div>
			
			<div class="panel">
				
				<label class="label">
					<span class="legend">Produto:</span>
					<input class="font_large" type="text" name="pdt_title" value="<?= $pdt_title; ?>"
					       placeholder="Nome do Produto:" required/>
				</label>
				
				<label class="label">
					<span class="legend">Breve Descrição: <span class='caracteres'></span></span>

					<textarea maxlength="255" class="font_medium subtitle_optimize"" name="pdt_subtitle" rows="3"
					required><?= $pdt_subtitle; ?></textarea>
				</label>
				
				<label class="label">
					<span class="legend">TAGS (palavras-chave separadas por vírgula:</span>
					<input class='font_medium' type="text" name="pdt_tags" value="<?= $pdt_tags; ?>" list="tags"/>
					
					<datalist id="tags">
                        <?php
                            $Read->fullRead(
                                "SELECT DISTINCT upper(pdt_tags) as pdt_tags FROM " . DB_PDT_DORIPEL . " WHERE pdt_tags IS NOT NULL AND pdt_tags != ''"
                            );
                            foreach ($Read->getResult() as $tags) {
                                echo '<option value="' . $tags['pdt_tags'] . '">';
                            }
                        ?>
					</datalist>
				</label>
				
				<div class="label_33">
					<label class="label">
						<span class="legend">Referência: (VPN)</span>
						<input type="text" name="pdt_ref" value="<?= ($pdt_ref ? $pdt_ref : ''); ?>" required/>
					</label>
					<label class="label">
						<span class="legend">Padrão/Cor:</span>
                        <?php
                            $Read->exeRead(DB_PDT_COLORS_DORIPEL, "ORDER BY color_title ASC");
                            if (!$Read->getResult()) {
                                echo Check::erro(
                                    "<span class='icon-warning'>Cadastre alguns padrões e/ou cores antes de começar!</span>",
                                    E_USER_WARNING
                                );
                            } else {
                                echo "<select name='pdt_color' required>";
                                echo "<option value=''>Selecione um Padrão</option>";
                                foreach ($Read->getResult() as $Color) {
                                    echo "<option";
                                    if ($pdt_color == $Color['color_title']) {
                                        echo " selected='selected'";
                                    }
                                    echo " value='{$Color['color_title']}'>{$Color['color_title']}</option>";
                                }

                                echo "</select>";
                            }
                        ?>
					</label>
					<label class="label">
						<span class="legend">Código de Barras: (EAN 13)</span>
						<input type="text" name="pdt_code" placeholder="13 caracteres numéricos"
						       title="O campo não deve conter LETRAS" maxlength="13" pattern="[0-9]+$"
						       value="<?= ($pdt_code ? $pdt_code : ''); ?>" required/>
					</label>
				
				</div>
				
				
				<div class="label_33">
					<label class="label">
						<span class="legend">Marca/Fabricante:</span>
                        <?php
                            $Read->exeRead(DB_PDT_BRANDS_DORIPEL, "ORDER BY brand_title ASC");
                            if (!$Read->getResult()) {
                                echo Check::erro(
                                    "<span class='icon-warning'>Cadastre algumas marcas ou fabricantes antes de começar!</span>",
                                    E_USER_WARNING
                                );
                            } else {
                                echo "<select name='pdt_brand' required>";
                                echo "<option value=''>Selecione um Fabricante</option>";
                                foreach ($Read->getResult() as $Brand) {
                                    echo "<option";
                                    if ($pdt_brand == $Brand['brand_id']) {
                                        echo " selected='selected'";
                                    }
                                    echo " value='{$Brand['brand_id']}'>{$Brand['brand_title']}</option>";
                                }

                                echo "</select>";
                            }
                        ?>
					</label>
					
					<label class="label">
						<span class="legend">Linha/Ano:</span>
                        <?php
                            $Line = DATE('Y') - 1;

                            echo "<select name='pdt_line' required>";
                            echo "<option value=''>Selecione uma Linha/Ano:</option>";

                            for ($i = 0; $i <= 2; $i++) {
                                $LineYear['line'] = $Line + $i;

                                echo "<option";
                                if ($pdt_line == $LineYear['line']) {
                                    echo " selected='selected'";
                                }
                                echo " value='{$LineYear['line']}'> Linha {$LineYear['line']}</option>";
                            }
                            echo "</select>";


                        ?>
					</label>
					
					<label class="label">
						<span class="legend">Categoria:</span>
                        <?php
                            $Read->exeRead(DB_PDT_CATS_DORIPEL, "WHERE cat_parent IS NULL ORDER BY cat_title ASC");
                            if (!$Read->getResult()) {
                                echo Check::erro(
                                    "<span class='icon-warning'>Cadastre algumas categorias de produtos antes de começar!</span>",
                                    E_USER_WARNING
                                );
                            } else {
                                echo "<select name='pdt_subcategory' class='jwc_product_stock' required>";
                                echo "<option value=''>Selecione uma Categoria</option>";
                                foreach ($Read->getResult() as $Cat) {
                                    echo "<option disabled='disabled' value='{$Cat['cat_id']}'>{$Cat['cat_title']}</option>";
                                    $Read->exeRead(
                                        DB_PDT_CATS_DORIPEL,
                                        "WHERE cat_parent = :id",
                                        "id={$Cat['cat_id']}"
                                    );
                                    if (!$Read->getResult()) {
                                        echo "<option disabled='disabled' value=''>&raquo;&raquo; Cadastre uma categoria nessa sessão!</option>";
                                    } else {
                                        foreach ($Read->getResult() as $SubCat) {
                                            echo "<option";
                                            if ($pdt_subcategory == $SubCat['cat_id']) {
                                                echo " selected='selected'";
                                            }
                                            echo " value='{$SubCat['cat_id']}'>&raquo;&raquo; {$SubCat['cat_title']}</option>";
                                        }
                                    }
                                }
                                echo "</select>";
                            }
                        ?>
					</label>
				</div>
				
				<label class="label">
					<span class="legend">Descrição Completa:</span>
					<textarea name="pdt_content" class="work_mce" rows="10"><?= $pdt_content; ?></textarea>
				</label>
			
			</div>
			
			<div class='panel_header default'>
				<h2 class='icon-insert-template'>OPCIONAIS DO PRODUTO:</h2>
			</div>
			<div class="panel">
				<div class="j_pdt_optionals" data-next-optional="<?= count($ProductOptionals); ?>">
                    <?php
                        foreach ($ProductOptionals as $OptionalIndex => $Optional) { ?>
							<div class="j_pdt_optional_item" data-optional-index="<?= $OptionalIndex; ?>">
								<span class="section icon-insert-template">Opcional <?= $OptionalIndex + 1; ?></span>
								<div class="label_50">
									<label class='label'>
										<span class='legend'>Ref. do Opcional:</span>
										<input type='text'
										       name='optionals[<?= $OptionalIndex; ?>][pdt_optional_ref]'
										       value="<?= $Optional['pdt_optional_ref']; ?>"/>
									</label>
									<label class='label'>
										<span class='legend'>Título do Opcional:</span>
										<input type='text'
										       name='optionals[<?= $OptionalIndex; ?>][pdt_optional_title]'
										       value="<?= $Optional['pdt_optional_title']; ?>"/>
									</label>
									<div class="clear"></div>
								</div>
								
								<div class="label_50">
									<label class='label'>
										<span class='legend'>Imagem do Opcional:</span>
										<input type='hidden'
										       name='optionals[<?= $OptionalIndex; ?>][pdt_optional_img_current]'
										       value="<?= $Optional['pdt_optional_img']; ?>"/>
										<input type='file'
										       class="j_pdt_optional_img"
										       name='optionals[<?= $OptionalIndex; ?>][pdt_optional_img]'
										       accept="image/*"/>
                                        <?php
                                            if ($Optional['pdt_optional_img']) { ?>
												<span><b>Imagem atual:</b> <?= $Optional['pdt_optional_img']; ?></span>
                                                <?php
                                            } ?>
									</label>

                                    <?php
                                        $OptionalImage = ($Optional['pdt_optional_img'] && file_exists(
                                            "../uploads/{$Optional['pdt_optional_img']}"
                                        ) && !is_dir(
                                            "../uploads/{$Optional['pdt_optional_img']}"
                                        ) ? "uploads/{$Optional['pdt_optional_img']}" : 'admin/_img/no_image.jpg');
                                    ?>
									<div for="" class="label">
										<img style="display: block; margin: 0 auto" class='j_pdt_optional_preview'
										     alt='Imagem do Opcional'
										     title='Imagem do Opcional'
										     src="../tim.php?src=<?= $OptionalImage; ?>&w=300&h=auto"
										     default="../tim.php?src=<?= $OptionalImage; ?>&w=300&h=auto"/>
									
									</div>
									
									<div class="clear"></div>
								</div>
								
								<label class='label'>
									<span class='legend'>Descrição Opcional:</span>
									<textarea id="pdt_optional_desc_<?= $OptionalIndex; ?>"
									          name='optionals[<?= $OptionalIndex; ?>][pdt_optional_desc]'
									          class='work_mce_basic'
									          rows='5'><?= $Optional['pdt_optional_desc']; ?></textarea>
								</label>
								
								<button type="button" class="btn btn_red icon-cross j_remove_pdt_optional">Remover
									opcional
								</button>
								<div class="clear"></div>
							</div>
                            <?php
                        } ?>
				</div>
				
				<div class="pdt_volume_actions">
					<button type="button" class="btn btn_blue icon-plus j_add_pdt_optional">Adicionar opcional
					</button>
				</div>
			</div>
			
			<div class="panel_header default">
				<h2 class="icon-box-add">VOLUMES DO PRODUTO:</h2>
			</div>
			<div class='panel'>
				<div class='j_pdt_volumes' data-next-volume="<?= count($ProductVolumes); ?>">
                    <?php
                        foreach ($ProductVolumes as $VolumeIndex => $Volume) { ?>
							<div class="j_pdt_volume_item" data-volume-index="<?= $VolumeIndex; ?>">
								<span class="section icon-box-remove">Volume <?= $VolumeIndex + 1; ?></span>
								<div class="label_50">
									<label class="label">
										<span class="legend">Peso Bruto Em KG:</span>
										<input type="number" step="0.0001" min="0"
										       name="volumes[<?= $VolumeIndex; ?>][weight]"
										       value="<?= $Volume['volume_weight']; ?>"
										       placeholder="Peso em KG:"
										       required/>
									</label>
									<label class="label">
										<span class="legend">Comprimento Em Metros:</span>
										<input type="number" step="0.0001" min="0"
										       name="volumes[<?= $VolumeIndex; ?>][depth]"
										       value="<?= $Volume['volume_depth']; ?>"
										       placeholder="Comprimento em Metros:" required/>
									</label>
									<div class="clear"></div>
								</div>
								
								<div class="label_50">
									<label class="label">
										<span class="legend">Largura Em Metros:</span>
										<input type="number" step="0.0001" min="0"
										       name="volumes[<?= $VolumeIndex; ?>][width]"
										       value="<?= $Volume['volume_width']; ?>"
										       placeholder="Largura em Metros:"
										       required/>
									</label>
									<label class="label">
										<span class="legend">Altura Em Metros:</span>
										<input type="number" step="0.0001" min="0"
										       name="volumes[<?= $VolumeIndex; ?>][height]"
										       value="<?= $Volume['volume_height']; ?>"
										       placeholder="Altura em Metros:"
										       required/>
									</label>
									<div class="clear"></div>
								</div>
								<button type="button" class="btn btn_red icon-cross j_remove_pdt_volume">Remover
									volume
								</button>
								<div class="clear"></div>
							</div>
                            <?php
                        } ?>
				</div>
				
				<div class="pdt_volume_actions">
					<button type="button" class="btn btn_blue icon-plus j_add_pdt_volume">Adicionar volume
					</button>
				</div>
			
			</div>
			
			
			<div class="panel_header default">
				<h2 class="icon-equalizer">DIMENSÕES DO PRODUTO MONTADO:</h2>
			</div>
			
			
			<div class="panel">
				<div class='label_50'>
					<label class='label'>
						<span class='legend'>Altura Em Metros:</span>
						<input type='number' step='0.0001' name='pdt_dimension_heigth_mounted'
						       value="<?= $pdt_dimension_heigth_mounted; ?>" placeholder='Altura em Metros:'
						       required/>
					</label>
					<label class='label'>
						<span class='legend'>Largura Em Metros:</span>
						<input type='number' step='0.0001' name='pdt_dimension_width_mounted'
						       value="<?= $pdt_dimension_width_mounted; ?>" placeholder='Largura em Metros:'
						       required/>
					</label>
					
					<div class='clear'></div>
				</div>
				
				<div class='label_50'>
					<label class='label'>
						<span class='legend'>Profundidade Em Metros:</span>
						<input type='number' step='0.0001' name='pdt_dimension_depth_mounted'
						       value="<?= $pdt_dimension_depth_mounted; ?>"
						       placeholder='Profundidade em Metros:'
						       required/>
					</label>
					<label class='label'>
						<span class='legend'>Peso Bruto Em KG:</span>
						<input type='number' name='pdt_dimension_weight_mounted'
						       value="<?= $pdt_dimension_weight_mounted; ?>" placeholder='Peso em KG:'
						       required/>
					</label>
					
					<div class='clear'></div>
				</div>
			</div>
			
			<div class="clear"></div>
		
		
		</main>
		
		<aside class="box box30">
			
			<div class="panel_header default">
				<label class='label'>
					<span class='legend'>Imagem principal (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
					<input type="file" class="wc_loadimage" name="pdt_cover"/>
				</label>

                <?php
                    $Image = (file_exists("../uploads/{$pdt_cover}") && !is_dir(
                        "../uploads/{$pdt_cover}"
                    ) ? "uploads/{$pdt_cover}" : 'admin/_img/no_image.jpg');
                ?>
				<img class="pdt_cover" alt="Capa do Produto" title="Capa do Produto"
				     src="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>"
				     default="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>">
                <?php
                    $Read->exeRead(DB_PDT_GALLERY_DORIPEL, "WHERE product_id = :id", "id={$pdt_id}");
                    if ($Read->getResult()) {
                        echo '<div class="pdt_images gallery pdt_single_image">';
                        foreach ($Read->getResult() as $Image) {
                            $ImageUrl = ($Image['image'] && file_exists("../uploads/{$Image['image']}") && !is_dir(
                                "../uploads/{$Image['image']}"
                            ) ? "../uploads/{$Image['image']}" : '_img/no_image.jpg');
                            echo "<img rel='Products' id='{$Image['id']}' alt='Imagem em {$pdt_title}' title='Imagem em {$pdt_title}' src='{$ImageUrl}'/>";
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="pdt_images gallery pdt_single_image"></div>';
                    }
                ?>
				
				<label class="label">
					<span class="legend">Fotos Adicionais (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
					<input type="file" name="image[]" multiple/>
				</label>

                <?php
                    $Cena = (file_exists("../uploads/{$pdt_scene}") && !is_dir(
                        "../uploads/{$pdt_scene}"
                    ) ? "uploads/{$pdt_scene}" : 'admin/_img/no_image.jpg');
                ?>
				<img class="pdt_scene" alt="Cena do Produto" title="Cena do Produto"
				     src="../tim.php?src=<?= $Cena; ?>&w=1920/2&h=1152/2"
				     default="../tim.php?src=<?= $Cena; ?>&w=1920&h=1152"/>
				
				<label class="label">
					<span class="legend">Cena (JPG 1920x1152px):</span>
					<input type="file" class="wc_loadimage" name="pdt_scene"/>
				</label>
				
				<h2 class='icon-file-pdf'>Gabarito de Montagem (PDF):</h2>
				<label class='label'>
					<span class='legend'></span>
					<input type='file' value="<?= ($pdt_instrutions ? $pdt_instrutions : ''); ?>"
					       name='pdt_instrutions'/>
				</label>
				
				<h2 class="icon-youtube">Youtube - Vídeo ID:</h2>
				
				<label class="label">
					<span class="legend">youtube.com/watch?v=<b>VuM-UIMqHkk</b></span>
					<input type=" text" name="pdt_video" placeholder="Somente ID do Vídeo"
					       value="<?= isset($pdt_video) ? $pdt_video : ''; ?>"/>
				</label>
				
				<div class="m_top">&nbsp;</div>
				<div class="wc_actions">

                    <?php
                        echo Check::switchOnOff(
                            'pdt_status',
                            $pdt_status,
                            'Publicado:',
                            'SIM',
                            'NÃO'
                        );
                    ?>
					
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
			</div>
			
			<div class='panel panel_share'>
				<h2 class="icon-share2">Compartilhe nas Redes Sociais:</h2>
                <?php
                    $WC_SHARE_LINK = "/movel/{$pdt_name}";
                    $WC_SHARE_HASH = "#doripelMoveis";
                    $WC_TITLE_LINK = $pdt_title;
                    require_once __DIR__ . '/../../../assets/widgets/share/share.product.wc.php';
                ?>
			</div>
		
		</aside>
	
	</form>
</div>


<script>
    (function () {
        var wrapper = document.querySelector('.j_pdt_optionals');
        var addButton = document.querySelector('.j_add_pdt_optional');
        
        if (!wrapper || !addButton) {
            return;
        }
        
        function renderOptional(index) {
            var displayIndex = index + 1;
            
            return '<div class="j_pdt_optional_item" data-optional-index="' + index + '">' +
                '<span class="section icon-insert-template">Opcional ' + displayIndex + '</span>' +
                '<div class="label_50">' +
                '<label class="label"><span class="legend">Ref. do Opcional:</span>' +
                '<input type="text" name="optionals[' + index + '][pdt_optional_ref]" value=""/></label>' +
                '<label class="label"><span class="legend">Título do Opcional:</span>' +
                '<input type="text" name="optionals[' + index + '][pdt_optional_title]" value=""/></label>' +
                '<div class="clear"></div></div>' +
                '<div class="label_50">' +
                '<label class="label"><span class="legend">Imagem do Opcional:</span>' +
                '<input type="hidden" name="optionals[' + index + '][pdt_optional_img_current]" value=""/>' +
                '<input type="file" class="j_pdt_optional_img" name="optionals[' + index + '][pdt_optional_img]" accept="image/*"/></label>' +
                '<div for="" class="label">' +
                '<img class="j_pdt_optional_preview" alt="Imagem do Opcional" title="Imagem do Opcional" src="../tim' +
                '.php?src=admin/_img/no_image.jpg&w=300&h=auto" default="../tim.php?src=admin/_img/no_image' +
                '.jpg&w=300&h=300"/>' +
                '</div>' +
                '<div class="clear"></div></div>' +
                '<label class="label"><span class="legend">Descrição Opcional:</span>' +
                '<textarea id="pdt_optional_desc_' + index + '" name="optionals[' + index + '][pdt_optional_desc]" class="work_mce_basic" rows="5"></textarea></label>' +
                '<button type="button" class="btn btn_red icon-cross j_remove_pdt_optional">Remover opcional</button>' +
                '<div class="clear"></div></div>';
        }
        
        function initOptionalEditor(index) {
            var editorId = 'pdt_optional_desc_' + index;
            
            if (typeof tinyMCE === 'undefined' || !document.getElementById(editorId)) {
                return;
            }
            
            if (tinyMCE.get(editorId)) {
                tinyMCE.execCommand('mceRemoveEditor', false, editorId);
            }
            
            tinyMCE.execCommand('mceAddEditor', false, editorId);
        }
        
        function removeOptionalEditor(item) {
            var textarea = item ? item.querySelector('textarea.work_mce_basic') : null;
            
            if (textarea && typeof tinyMCE !== 'undefined' && tinyMCE.get(textarea.id)) {
                tinyMCE.execCommand('mceRemoveEditor', false, textarea.id);
            }
        }
        
        function syncUploadedOptionalImages(data) {
            if (!data || !data.optional_images) {
                return;
            }
            
            Object.keys(data.optional_images).forEach(function (index) {
                var image = data.optional_images[index];
                var hidden = wrapper.querySelector('[name="optionals[' + index + '][pdt_optional_img_current]"]');
                var item = hidden ? hidden.closest('.j_pdt_optional_item') : null;
                var preview = item ? item.querySelector('.j_pdt_optional_preview') : null;
                var imageUrl = '../tim.php?src=uploads/' + image + '&w=300&h=300';
                
                if (hidden) {
                    hidden.value = image;
                }
                
                if (preview) {
                    preview.setAttribute('default', imageUrl);
                    preview.setAttribute('src', imageUrl);
                }
            });
        }
        
        function refreshRemoveButtons() {
            var items = wrapper.querySelectorAll('.j_pdt_optional_item');
            var canRemove = items.length > 1;
            
            items.forEach(function (item, index) {
                var title = item.querySelector('.section');
                var removeButton = item.querySelector('.j_remove_pdt_optional');
                
                if (title) {
                    title.textContent = 'Opcional ' + (index + 1);
                }
                
                if (removeButton) {
                    removeButton.style.display = canRemove ? 'inline-block' : 'none';
                }
            });
        }
        
        addButton.addEventListener('click', function () {
            var index = parseInt(wrapper.getAttribute('data-next-optional'), 10) || wrapper.querySelectorAll('.j_pdt_optional_item').length;
            wrapper.insertAdjacentHTML('beforeend', renderOptional(index));
            wrapper.setAttribute('data-next-optional', String(index + 1));
            initOptionalEditor(index);
            refreshRemoveButtons();
        });
        
        wrapper.addEventListener('click', function (event) {
            var button = event.target.closest('.j_remove_pdt_optional');
            
            if (!button || wrapper.querySelectorAll('.j_pdt_optional_item').length <= 1) {
                return;
            }
            
            var item = button.closest('.j_pdt_optional_item');
            removeOptionalEditor(item);
            item.remove();
            refreshRemoveButtons();
        });
        
        wrapper.addEventListener('change', function (event) {
            var input = event.target.closest('.j_pdt_optional_img');
            
            if (!input) {
                return;
            }
            
            var item = input.closest('.j_pdt_optional_item');
            var target = item ? item.querySelector('.j_pdt_optional_preview') : null;
            
            if (!target) {
                return;
            }
            
            if (!input.files || !input.files[0]) {
                target.setAttribute('src', target.getAttribute('default'));
                return;
            }
            
            if (!input.files[0].type.match(/^image\/(jpeg|png|gif|webp)$/)) {
                if (typeof Trigger === 'function') {
                    Trigger('<div class="trigger trigger_alert trigger_ajax"><b>ERRO AO SELECIONAR:</b> O arquivo <b>' + input.files[0].name + '</b> não é válido! <b>Selecione uma imagem (.jpg, .jpeg, .png, .gif ou .webp)</b></div>');
                }
                input.value = '';
                target.setAttribute('src', target.getAttribute('default'));
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function (readerEvent) {
                target.setAttribute('src', readerEvent.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        });
        
        if (window.jQuery) {
            jQuery(document).ajaxSuccess(function (event, xhr, settings, data) {
                if (!settings || settings.url.indexOf('_ajax/ProductsDoripel.ajax.php') === -1) {
                    return;
                }
                
                if (!data && xhr && xhr.responseText) {
                    try {
                        data = JSON.parse(xhr.responseText);
                    } catch (error) {
                        data = null;
                    }
                }
                
                syncUploadedOptionalImages(data);
            });
        }
        
        refreshRemoveButtons();
    })();
    
    (function () {
        var wrapper = document.querySelector('.j_pdt_volumes');
        var addButton = document.querySelector('.j_add_pdt_volume');
        
        if (!wrapper || !addButton) {
            return;
        }
        
        function renderVolume(index) {
            var displayIndex = index + 1;
            
            return '<div class="j_pdt_volume_item" data-volume-index="' + index + '">' +
                '<span class="section icon-box-remove">Volume ' + displayIndex + '</span>' +
                '<div class="label_50">' +
                '<label class="label"><span class="legend">Peso Bruto Em KG:</span>' +
                '<input type="number" step="0.0001" min="0" name="volumes[' + index + '][weight]" value="0" placeholder="Peso em KG:" required/></label>' +
                '<label class="label"><span class="legend">Comprimento Em Metros:</span>' +
                '<input type="number" step="0.0001" min="0" name="volumes[' + index + '][depth]" value="0" placeholder="Comprimento em Metros:" required/></label>' +
                '<div class="clear"></div></div>' +
                '<div class="label_50">' +
                '<label class="label"><span class="legend">Largura Em Metros:</span>' +
                '<input type="number" step="0.0001" min="0" name="volumes[' + index + '][width]" value="0" placeholder="Largura em Metros:" required/></label>' +
                '<label class="label"><span class="legend">Altura Em Metros:</span>' +
                '<input type="number" step="0.0001" min="0" name="volumes[' + index + '][height]" value="0" placeholder="Altura em Metros:" required/></label>' +
                '<div class="clear"></div></div>' +
                '<button type="button" class="btn btn_red icon-cross j_remove_pdt_volume">Remover volume</button>' +
                '<div class="clear"></div></div>';
        }
        
        function refreshRemoveButtons() {
            var items = wrapper.querySelectorAll('.j_pdt_volume_item');
            var canRemove = items.length > 1;
            
            items.forEach(function (item, index) {
                var title = item.querySelector('.section');
                var removeButton = item.querySelector('.j_remove_pdt_volume');
                
                if (title) {
                    title.textContent = 'Volume ' + (index + 1);
                }
                
                if (removeButton) {
                    removeButton.style.display = canRemove ? 'inline-block' : 'none';
                }
            });
        }
        
        addButton.addEventListener('click', function () {
            var index = parseInt(wrapper.getAttribute('data-next-volume'), 10) || wrapper.querySelectorAll('.j_pdt_volume_item').length;
            wrapper.insertAdjacentHTML('beforeend', renderVolume(index));
            wrapper.setAttribute('data-next-volume', String(index + 1));
            refreshRemoveButtons();
        });
        
        wrapper.addEventListener('click', function (event) {
            var button = event.target.closest('.j_remove_pdt_volume');
            
            if (!button || wrapper.querySelectorAll('.j_pdt_volume_item').length <= 1) {
                return;
            }
            
            button.closest('.j_pdt_volume_item').remove();
            refreshRemoveButtons();
        });
        
        refreshRemoveButtons();
    })();
</script>
