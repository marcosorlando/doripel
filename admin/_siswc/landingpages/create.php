<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_LANDING_PAGES;
    if (!APP_LANDING_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $PageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($PageId) {
        $Read->exeRead(DB_LANDING_PAGES, 'WHERE page_id = :id', 'id=' . $PageId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                sprintf(
                    '<b>OPPSS %s</b>, você tentou editar uma página que não existe ou que foi removida recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=landingpages/home');

            exit;
        }
    } else {
        $PageCreate = [
            'page_date' => date('Y-m-d H:i:s'),
            'page_status' => 0,
        ];
        $Create->exeCreate(DB_LANDING_PAGES, $PageCreate);
        header('Location: dashboard.php?wc=landingpages/create&id=' . $Create->getResult());

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-page-break"><?php
                echo $page_title ? $page_title : 'Nova Página de Cliente '; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=landingpages/home">Páginas</a>
			<span class="crumb">/</span> Gerenciar Página
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Gerar Landing Page"
		   href="dashboard.php?wc=landingpages/reply&id=<?php
               echo $PageId; ?>"
		   class="btn btn_blue icon-copy">Replicar!</a>
		<a title="Nova Página de Cliente" href="dashboard.php?wc=landingpages/create" class="btn btn_green icon-plus">Nova</a>
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE; ?>/<?php
            echo $page_name; ?>"
		   class="wc_view btn btn_blue icon-eye float-right">Ver no site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Landingpages"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="page_id" value="<?php
                echo $PageId; ?>"/>
			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>
			<div style="overflow: auto; max-height: 300px;">
				<img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
				     src="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"
				     default="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"/>
			</div>
			<div class="workcontrol_imageupload_actions">
				<input class="wc_loadimage" type="file" name="image" required/>
				<span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control"
				      style="margin-right: 8px;">Fechar</span>
				<button class="btn btn_green icon-image">Enviar e Inserir!</button>
				<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>

<div class="dashboard_content">

	<form class="auto_save" name="page_add" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Landingpages"/>
		<input type="hidden" name="callback_action" value="manage"/>
		<input type="hidden" name="page_id" value="<?php
            echo $PageId; ?>"/>

		<div class="box box70">

			<div class="panel_header default">
				<h2 class="icon-page-break">Insira as informações da Landing Page</h2>
			</div>

			<div class="panel">
				<label class="label">
					<span class="legend">Título da página:</span>
					<input style="font-size: 1.4em;" type="text" name="page_title" value="<?php
                        echo $page_title; ?>"
					       placeholder="Título da Página:" required/>
				</label>

				<label class="label">
					<span class="legend">Descrição: (Metatag Description)</span>
					<textarea style="font-size: 1.2em;" name="page_subtitle" rows="3" placeholder="Sobre a Página:"
					          required><?php
                            echo $page_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Cabeçalho: Título Principal e Headline</span>
					<textarea name="page_content" class="work_mce" rows="10"
					          placeholder="Título e Subtítulo:"><?php
                            echo $page_content; ?></textarea>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Texto da Coluna 1:</span>
						<textarea name="page_coluna1" class="work_mce" rows="10"
						          placeholder="Primeira Coluna:"><?php
                                echo $page_coluna1; ?></textarea>
					</label>

					<label class="label">
						<span class="legend">Texto da Coluna 2:</span>
						<textarea name="page_coluna2" class="work_mce" rows="10"
						          placeholder="Segunda Coluna:"><?php
                                echo $page_coluna2; ?></textarea>
					</label>
				</div>
				<label class="label">
					<span class="legend">URL de destino:</span>
					<input type="text" name="page_destino" value="<?php
                        echo $page_destino; ?>" placeholder="Link de destino"
					       required/>
				</label>

				<fieldset style="border: 2px solid red; border-radius: 6px; padding: 10px">
					<legend style="padding: 10px; font-weight: bold">Formulário:</legend>
					<label class="label">
						<span class="legend">Título do Formulário:</span>
						<input type="text" name="form_title" value="<?php
                            echo $form_title; ?>"
						       placeholder="Título do Formulário"/>
					</label>
					<div class="label_50">
						<label class="label">
							<span class="legend">Cor do Fundo:</span>
							<input type="text" name="form_bg_color" class="formBg" value="<?php
                                echo $form_bg_color; ?>"
							       placeholder="Hexadecimal da Cor"/>
							<div id="formBg" class="none"></div>
						</label>
						<label class="label">
							<span class="legend">Cor do Texto:</span>
							<input type="text" name="form_text_color" class="formText" value="<?php
                                echo $form_text_color; ?>"
							       placeholder="Hexadecimal da Cor"/>
							<div id="formText" class="none"></div>
						</label>
					</div>

					<label class="label">
						<span class="legend">Botão CTA - Texto:</span>
						<input type="text" name="page_btn_text" value="<?php
                            echo $page_btn_text; ?>"
						       placeholder="Texto do Botão"/>
					</label>

					<div class="label_33">
						<label class="label">
							<span class="legend">Botão CTA - Cor texto:</span>
							<input type="text" name="page_btn_text_color" class="textPicker"
							       value="<?php
                                       echo $page_btn_text_color; ?>" placeholder="Hexadecimal da Cor"/>
							<div id="textPicker" class="none"></div>
							<!-- /#textPicker -->
						</label>
						<label class="label">
							<span class="legend">Cor do Botão:</span>
							<input type="text" name="page_btn_bg_color" class="btnPicker"
							       value="<?php
                                       echo $page_btn_bg_color; ?>" placeholder="Hexadecimal da Cor"/>
							<div id="btnPicker" class="none"></div>
						</label>
						<label class="label">
							<span class="legend">Botão Hover:</span>
							<input type="text" name="page_btn_bg_color_hover" class="hoverPicker"
							       value="<?php
                                       echo $page_btn_bg_color_hover; ?>" placeholder="Hexadecimal da Cor"/>
							<div id="hoverPicker" class="none"></div>
						</label>
					</div>
				</fieldset>


				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">

			<div class="panel_header default">
				<h2>Imagens e Cores</h2>
			</div>

			<div class="panel">
				<label class="label">
					<span class="legend">Logotipo<b> (200X80px)</b></span>
					<input type="file" class="wc_loadimage" name="page_logo"/>
				</label>
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                        $PageLogo = (!empty($page_logo) && file_exists(
                            '../uploads/landingpages/' . $page_logo
                        ) && !is_dir(
                            '../uploads/landingpages/' . $page_logo
                        ) ? 'uploads/landingpages/' . $page_logo : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb page_logo box_center" id="page_logo" callback="Landingpages"
					     src="../tim.php?src=<?php
                             echo $PageLogo; ?>&w=200&h=auto"
					     default="../tim.php?src=<?php
                             echo $PageLogo; ?>&w=200&h=auto"
					     style="display: block; margin: 0 auto; max-width: 200px; height: auto"/>
				</div>

				<label class="label">
					<span class="legend">Mockup do Material<b> (600X730px)</b></span>
					<input type="file" class="wc_loadimage" name="page_mockup"/>
				</label>
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                        $PageMockup = (!empty($page_mockup) && file_exists(
                            '../uploads/landingpages/' . $page_mockup
                        ) && !is_dir(
                            '../uploads/landingpages/' . $page_mockup
                        ) ? 'uploads/landingpages/' . $page_mockup : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb page_mockup" id="page_mockup" callback="Landingpages"
					     src="../tim.php?src=<?php
                             echo $PageMockup; ?>&w=600&h=auto"
					     default="../tim.php?src=<?php
                             echo $PageMockup; ?>&w=600&h=730"/>
				</div>


				<label class="label">
					<span class="legend">Background Image: Opcional<b> (1200X628px)</b></span>
					<input type="file" class="wc_loadimage" name="page_cover"/>
				</label>
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                        $PageCover = (!empty($page_cover) && file_exists(
                            '../uploads/landingpages/' . $page_cover
                        ) && !is_dir(
                            '../uploads/landingpages/' . $page_cover
                        ) ? 'uploads/landingpages/' . $page_cover : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb page_cover" id="page_cover" callback="Landingpages" alt="Background"
					     title="Background"
					     src="../tim.php?src=<?php
                             echo $PageCover; ?>&w=<?php
                             echo IMAGE_W / 2; ?>&h=<?php
                             echo IMAGE_H / 2; ?>"
					     default="../tim.php?src=<?php
                             echo $PageCover; ?>&w=<?php
                             echo IMAGE_W / 2; ?>&h=<?php
                             echo IMAGE_H / 2; ?>"/>
				</div>

				<label class="label">
					<span class="legend">Social Media: Image<b> (1200X628px)</b></span>
					<input type="file" class="wc_loadimage" name="page_social_media"/>
				</label>
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                        $PageSocial = (!empty($page_social_media) && file_exists(
                            '../uploads/landingpages/' . $page_social_media
                        ) && !is_dir(
                            '../uploads/landingpages/' . $page_social_media
                        ) ? 'uploads/landingpages/' . $page_social_media : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb page_social_media" id="page_social_media" callback="Landingpages"
					     alt="Social Media - Imagem" title="Social Media - Imagem"
					     src="../tim.php?src=<?php
                             echo $PageSocial; ?>&w=<?php
                             echo IMAGE_W / 2; ?>&h=<?php
                             echo IMAGE_H / 2; ?>"
					     default="../tim.php?src=<?php
                             echo $PageSocial; ?>&w=<?php
                             echo IMAGE_W / 2; ?>&h=<?php
                             echo IMAGE_H / 2; ?>"/>
				</div>

				<label class="label">
					<span class="legend">Fundo do Cabeçalho (logo):</span>
					<input type="text" name="page_header_color" class="headerColor" value="<?php
                        echo $page_header_color; ?>"
					       placeholder="Hexadecimal da Cor"/>
					<div id="headerColor" class="none"></div>
				</label>
				<label class="label">
					<span class="legend">Fundo do Títulos e Headline:</span>
					<input type="text" name="page_box_color" class="boxColor" value="<?php
                        echo $page_box_color; ?>"
					       placeholder="Hexadecimal da Cor"/>
					<div id="boxColor" class="none"></div>
				</label>

				<label class="label">
					<span class="legend">Fundo do Texto Colunas:</span>
					<input type="text" name="page_bg_color" class="pageColor" value="<?php
                        echo $page_bg_color; ?>"
					       placeholder="Hexadecimal da Cor"/>
					<div id="pageColor" class="none"></div>
				</label>
				<label class="label">
					<span class="legend">Fundo do Página:</span>
					<input type="text" name="page_footer_color" class="footerColor" value="<?php
                        echo $page_footer_color; ?>"
					       placeholder="Hexadecimal da Cor"/>
					<div id="footerColor" class="none"></div>
				</label>

                <?php
                    if (APP_LINK_PAGES !== 0) { ?>
						<label class="label">
							<span class="legend">Link Alternativo (Opcional):</span>
							<input id="page_add" type="text" name="page_name" value="<?php
                                echo $page_name; ?>"
							       placeholder="Link da Página:"/>
						</label>
                        <?php
                    } ?>

				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center; margin-bottom: 10px;">
                    <?php
                        echo Check::switchOnOff('page_status', $page_status); ?>
					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
			</div>
		</div>
	</form>
</div>
<script src="_js/iro.min.js"></script>
<script>

    $(function () {
        $(document).ready(function () {
            $(".boxColor").css("background", $(".boxColor").val());
            $(".pageColor").css("background", $(".pageColor").val());
            $(".headerColor").css("background", $(".headerColor").val());
            $(".footerColor").css("background", $(".footerColor").val());
            $(".formBg").css("background", $(".formBg").val());
            $(".formText").css("background", $(".formText").val());
            $(".btnPicker").css("background", $(".btnPicker").val());
            $(".hoverPicker").css("background", $(".hoverPicker").val());
            $(".textPicker").css("background", $(".textPicker").val());
        });
    });

    /******************************
     * IRO - Seletor de Cores HexaString*
     * *****************************/
    var boxColor = new iro.ColorPicker("#boxColor", {
        width: $("#boxColor").parent().width(),
        color: $(".boxColor").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });

    $(".boxColor").on('focus', function () {
        $("#boxColor").slideDown()
        boxColor.on('input:end', function (color) {
            $(".boxColor").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".boxColor").on('blur', function () {
        $("#boxColor").slideUp();
    });

    //HEADER COLOR
    var headerColor = new iro.ColorPicker("#headerColor", {
        width: $("#headerColor").parent().width(),
        color: $(".headerColor").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });
    $(".headerColor").on('focus', function () {
        $("#headerColor").slideDown()
        headerColor.on('input:end', function (color) {
            $(".headerColor").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".headerColor").on('blur', function () {
        $("#headerColor").slideUp();
    });

    //FORM BG COLOR
    var formBg = new iro.ColorPicker("#formBg", {
        width: $("#formBg").parent().width(),
        color: $(".formBg").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });
    $(".formBg").on('focus', function () {
        $("#formBg").slideDown()
        formBg.on('input:end', function (color) {
            $(".formBg").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".formBg").on('blur', function () {
        $("#formBg").slideUp();
    });

    //FORM TEXT COLOR
    var formText = new iro.ColorPicker("#formText", {
        width: $("#formText").parent().width(),
        color: $(".formText").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });
    $(".formText").on('focus', function () {
        $("#formText").slideDown()
        formText.on('input:end', function (color) {
            $(".formText").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".formText").on('blur', function () {
        $("#formText").slideUp();
    });

    //FOOTER COLOR
    var footerColor = new iro.ColorPicker("#footerColor", {
        width: $("#footerColor").parent().width(),
        color: $(".footerColor").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });
    $(".footerColor").on('focus', function () {
        $("#footerColor").slideDown()
        footerColor.on('input:end', function (color) {
            $(".footerColor").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".footerColor").on('blur', function () {
        $("#footerColor").slideUp();
    });

    //PAGE COLOR
    var pageColor = new iro.ColorPicker("#pageColor", {
        width: $("#pageColor").parent().width(),
        color: $(".pageColor").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });

    $(".pageColor").on('focus onchange', function () {
        $("#pageColor").slideDown()
        pageColor.on('input:end', function (color) {
            $(".pageColor").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".pageColor").on('blur', function () {
        $("#pageColor").slideUp();
    });

    // Box & hue slider TEXT OF BUTTON
    var textPicker = new iro.ColorPicker("#textPicker", {
        width: $("#textPicker").parent().width(),
        color: $(".textPicker").val(),
        borderWidth: 2,
        borderColor: "#fff",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });

    $(".textPicker").on('focus', function () {
        $("#textPicker").slideDown()
        textPicker.on('input:end', function (color) {
            $(".textPicker").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".textPicker").on('blur', function () {
        $("#textPicker").slideUp();
    });

    //btnPicker: modifica cor de fundo do botão do formulário
    var btnPicker = new iro.ColorPicker("#btnPicker", {
        width: $("#btnPicker").parent().width(),
        color: $(".btnPicker").val(),
        borderWidth: 2,
        borderColor: "#eee",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });
    $(".btnPicker").on('focus', function () {
        $("#btnPicker").slideDown()
        btnPicker.on('input:end', function (color) {
            $(".btnPicker").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".btnPicker").on('blur', function () {
        $("#btnPicker").slideUp();
    });

    //hoverPicker: modifica cor de fundo do botão do formulário no :hover
    var hoverPicker = new iro.ColorPicker("#hoverPicker", {
        width: $("#hoverPicker").parent().width(),
        color: $(".hoverPicker").val(),
        borderWidth: 2,
        borderColor: "#eee",
        layout: [{
            component: iro.ui.Box,
        }, {
            component: iro.ui.Slider,
            options: {
                sliderType: 'hue'
            }
        }]
    });
    $(".hoverPicker").on('focus', function () {
        $("#hoverPicker").slideDown()
        hoverPicker.on('input:end', function (color) {
            $(".hoverPicker").val(color.hexString).css({
                backgroundColor: color.hexString,
                color: 'white'
            })
        });
    });
    $(".hoverPicker").on('blur', function () {
        $("#hoverPicker").slideUp();
    });
    //END IRO
</script>
