<?php

    $AdminLevel = LEVEL_WC_CTAS;
    if (!APP_CTAS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
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

    $CtaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($CtaId):
        $Read->ExeRead(DB_CTAS, "WHERE cta_id = :id", "id={$CtaId}");
        if ($Read->getResult()):
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        else:
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um CTA que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=ctas/home');
        endif;
    else:
        $CtaCreate = ['cta_date' => date('Y-m-d H:i:s')];
        $Create->ExeCreate(DB_CTAS, $CtaCreate);
        header('Location: dashboard.php?wc=ctas/create&id=' . $Create->getResult());
    endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-pen"><?= $cta_title ? $cta_title : 'Novo Call to action'; ?></h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=ctas/home">Call to actions</a>
            <span class="crumb">/</span>
            Gerenciar Call to action
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver Call to actions" href="dashboard.php?wc=ctas/home" class="btn btn_blue icon-eye">Ver</a>
        <a title="Novo Call to action" href="dashboard.php?wc=ctas/create" class="btn btn_green icon-plus">Adicionar</a>
    </div>
</header>

<div class="dashboard_content">
    <form name="deposition_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Ctas"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="cta_id" value="<?= $CtaId; ?>"/>

        <div class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Título:</span>
                    <input style="font-size: 1.4em;" type="text" name="cta_title" value="<?= $cta_title; ?>" placeholder="Título:" required/>
                </label>

                <label class="label">
                    <span class="legend">Headline:</span>
                    <textarea style="font-size: 1.2em;" name="cta_text" rows="3" required><?= $cta_text; ?></textarea>
                </label>

                <div class="label_33">
                    <label class="label">
                        <span class="legend">Cor do fundo:</span>
                        <input type="text" name="cta_bg_color" value="<?= $cta_bg_color; ?>" placeholder="Background:" required/>
                    </label>
                    <label class="label">
                        <span class="legend">Cor do Botão:</span>
                        <select name="cta_btn_color" required>
                            <option value="" disabled="disabled" selected="selected">Selecione o tipo do botão:</option>
                            <?php
                                foreach (getWcBtnCta() as $BtnClass => $Option):
                                    echo "<option " . ($cta_btn_color == $BtnClass ? "selected='selected'" : null) . " value='{$BtnClass}'>{$Option}</option>";
                                endforeach;
                            ?>
                        </select>
                    </label>
                    <label class="label">
                        <span class="legend">Cantos Arredondados:</span>
                        <select name="cta_btn_rounded">
                            <option value="" disabled="disabled" selected="selected">Selecione:</option>
                             <?php
                                foreach (getWcBtnRounded() as $BtnRounded => $Rounded):
                                    echo "<option " . ($cta_btn_rounded == $BtnRounded ? "selected='selected'" : null) . " value='{$BtnRounded}'>{$Rounded}</option>";
                                endforeach;
                            ?>
                        </select>
                    </label>

                </div>
                <label class="label">
                    <span class="legend">URL de Destino:</span>
                    <input type="text" name="cta_url" value="<?= $cta_url; ?>" placeholder="Destino do Click:" required/>
                </label>


                <div class="label_50">
                    <label class="label">
                        <span class="legend">Divulgar <b>a partir de:</b></span>
                        <input style="font-size: 1.2em;" type="text" class="formTime" name="cta_start" value="<?= (!empty($cta_start) ? date('d/m/Y H:i:s', strtotime($cta_start)) : date('d/m/Y H:i:s')); ?>" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Divulgar <b>até dia:</b> (opcional)</span>
                        <input style="font-size: 1.2em;" type="text" class="formTime" name="cta_end" value="<?= (!empty($cta_end) ? date('d/m/Y H:i:s', strtotime($cta_end)) : date('d/m/Y H:i:s', strtotime("+1month"))); ?>"/>
                    </label>
                </div>

            </div>


            <div class="clear"></div>
        </div>


        <div class="box box30">
            <div class="box_content">
                <?php

                    $Image = (file_exists("../uploads/{$cta_image}") && !is_dir("../uploads/{$cta_image}") ? "uploads/{$cta_image}" : 'admin/_img/no_image.jpg');
                ?>
                <img class="cta_image" src="../tim.php?src=<?= $Image; ?>&w=500&h=auto" default="../tim.php?src=<?= $Image; ?>&w=500&h=auto">

                <label class="label border_top">
                    <span class="legend">Imagem: (JPG 500X400px):</span>
                    <input type="file" class="wc_loadimage" name="cta_image"/>
                </label>

                <div class="panel">
                    <div class="m_top">&nbsp;</div>
                    <div class="wc_actions" style="text-align: center">
                        <label class="label_check label_publish <?= ($cta_status == 1 ? 'active' : ''); ?>"><input style="margin-top: -1px;" type="checkbox" value="1" name="cta_status" <?= ($cta_status == 1 ? 'checked' : ''); ?>>Publicar
                            Agora!</label>
                        <button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
                        <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </form>
</div>