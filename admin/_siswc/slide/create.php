<?php

use App\Conn\Create;
use App\Conn\Read;

$AdminLevel = LEVEL_WC_SLIDES;
if (!APP_SLIDE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

// AUTO INSTANCE OBJECT READ
if (empty($Read)):
    $Read = new Read;
endif;

// AUTO INSTANCE OBJECT CREATE
if (empty($Create)):
    $Create = new Create;
endif;

$SlideId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($SlideId):
    $Read->ExeRead(DB_SLIDES, "WHERE slide_id = :id", "id={$SlideId}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);
    else:
        $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um slide que não existe ou que foi removido recentemente!";
        header('Location: dashboard.php?wc=slide/home');
    endif;
else:
    $SlideCreate = [
        'slide_date' => date('Y-m-d H:i:s'),
        'slide_start' => date('Y-m-d H:i:s')
    ];
    $Create->ExeCreate(DB_SLIDES, $SlideCreate);
    header('Location: dashboard.php?wc=slide/create&id=' . $Create->getResult());
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-camera"><?= $slide_title ? $slide_title : 'Novo Slide'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=slide/home">Slides</a>
            <span class="crumb">/</span>
            Gerenciar Destaque
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver Slides!" href="dashboard.php?wc=slide/home" class="btn btn_blue icon-eye">Ver Destaques!</a>
        <a title="Novo Slide!" href="dashboard.php?wc=slide/create" class="btn btn_green icon-plus">Adicionar
            Destaque!</a>
    </div>
</header>

<div class="dashboard_content">
    <form name="post_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Slides" />
        <input type="hidden" name="callback_action" value="manager" />
        <input type="hidden" name="slide_id" value="<?= $SlideId; ?>" />

        <article class="box box100">
            <div class="panel">
                <div class="slide_create_cover">
                    <div class="upload_progress none">0%</div>
                    <?php

                    $SlideImage = (!empty($slide_image) && file_exists("../uploads/{$slide_image}") && !is_dir("../uploads/{$slide_image}") ? "uploads/{$slide_image}" : 'admin/_img/no_image.jpg');
                    $MobileImage = (!empty($mobile_image) && file_exists("../uploads/{$mobile_image}") && !is_dir("../uploads/{$mobile_image}") ? "uploads/{$mobile_image}" : 'admin/_img/no_image.jpg');
                    ?>
                    <div class="box box70" style="border: 1px solid #f1f1f1;">
                        <img class="slide_image post_cover" alt="Capa" title="Imagem principal" src="../tim.php?src=<?= $SlideImage; ?>&w=<?= SLIDE_W / 2; ?>&h=<?= SLIDE_H / 2; ?>" default="../tim.php?src=<?= $SlideImage; ?>&w=<?= SLIDE_W; ?>&h=<?= SLIDE_H; ?>" />
                    </div>
                    <div class="box box30" style="border: 1px solid #f1f1f1;">
                        <img class="post_cover mobile_image" alt="Mobile Image" title="Imagem para Mobile" src="../tim.php?src=<?= $MobileImage; ?>&w=640&h=870" default="../tim.php?src=<?= $MobileImage; ?>&w=640&h=870" />
                    </div>
                </div>
                <div class="panel">
                    <label class="label">
                        <span class="legend">Selecione Opacidade (Opacidade Padrão)</span>
                        <select name="slide_opacity" class="">
                            <option value="" disabled="disabled" selected="selected">Selecione o nível de opacidade:
                            </option>
                            <?php
                            foreach (getWcOpacitySlides() as $SlideOpacity => $Opacity):
                                echo "<option " . ($slide_opacity == $SlideOpacity ? "selected='selected'" : null) . " value='{$SlideOpacity}'>{$Opacity}</option>";
                            endforeach;
                            ?>
                        </select>
                    </label>
                </div>

                <div class="label_50">
                    <label class="label m_top">
                        <span class="legend">Slide FULL: (JPG: <?= SLIDE_W; ?>x<?= SLIDE_H; ?>px)</span>
                        <input type="file" class="wc_loadimage" name="slide_image" />
                    </label>
                    <label class="label m_top">
                        <span class="legend">Mobile Slide: (JPG: 640X900px)</span>
                        <input type="file" class="wc_loadimage" name="mobile_image" />
                    </label>
                </div>

                <label class="label">
                    <span class="legend">Título:</span>
                    <input style="font-size: 1.5em;" type="text" name="slide_title" value="<?= $slide_title; ?>" required />
                </label>

                <label class="label">
                    <span class="legend">Descrição: (MÁXIMO 150 carateres)</span>
                    <textarea style="font-size: 1.2em;" maxlength="150" name="slide_desc" rows="3" required><?= $slide_desc; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Landing Page <b>conteúdo</b>)</span>
                    <input style="font-size: 1.2em;" type="text" name="slide_content" value="<?= $slide_content; ?>" />
                </label>
                <label class="label">
                    <span class="legend">Landing Page <b>compra</b>)</span>
                    <input style="font-size: 1.2em;" type="text" name="slide_registration" value="<?= $slide_registration; ?>" />
                </label>

                <div class="label_50">

                    <label class="label">
                        <span class="legend">Divulgar <b>a partir de:</b></span>
                        <input style="font-size: 1.2em;" type="text" class="formTime" name="slide_start" value="<?= (!empty($slide_start) ? date('d/m/Y H:i:s', strtotime($slide_start)) : date('d/m/Y H:i:s')); ?>" required />
                    </label>

                    <label class="label">
                        <span class="legend">Divulgar <b>até dia:</b> (opcional)</span>
                        <input style="font-size: 1.2em;" type="text" class="formTime" name="slide_end" value="<?= (!empty($slide_end) ? date('d/m/Y H:i:s', strtotime($slide_end)) : date('d/m/Y H:i:s', strtotime("+1month"))); ?>" />
                    </label>

                </div>
                <div class="clear"></div>

                <div class="wc_actions" style="background:#2E4051">


                    <div class="box box50" style="tex-align: left">

                        <label class="label_check label_publish <?= ($show_title == 1 ? 'active' : ''); ?>">
                            <input style="margin-top: -1px;" type="checkbox" value="1" name="show_title" <?= ($show_title == 1 ? 'checked' : ''); ?>>
                            TÍTULO
                        </label>
                        <label class="label_check label_publish <?= ($show_desc == 1 ? 'active' : ''); ?>">
                            <input style="margin-top: -1px;" type="checkbox" value="1" name="show_desc" <?= ($show_desc == 1 ? 'checked' : ''); ?>>
                            DESCRIÇÃO
                        </label>

                        <label class="label_check label_publish <?= ($slide_purchase == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="slide_purchase" <?= ($slide_purchase == 1 ? 'checked' : ''); ?>>
                            COMPRAR AGORA
                        </label>
                        <label class="label_check label_publish <?= ($slide_information == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="slide_information" <?= ($slide_information == 1 ? 'checked' : ''); ?>>
                            MAIS INFORMAÇÕES</label>
                    </div>

                    <div class="box box50" style="tex-align: rigth">
                        <label class="label_check label_publish <?= ($slide_status == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="slide_status" <?= ($slide_status == 1 ? 'checked' : ''); ?>>Publicar
                            Agora!</label>
                        <button name="public" value="1" class="btn btn_green icon-share" style="margin-left: 5px;">
                            Atualizar
                            Destaque!
                        </button>
                        <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif" />
                    </div>
                </div>
            </div>
        </article>
    </form>
</div>
