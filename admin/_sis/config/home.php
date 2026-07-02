<?php

    use App\Config\ConfigLoader;
    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_CONFIG_MASTER;
    if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_CATEGORIES,
            'WHERE category_title IS NULL AND category_content IS NULL AND category_id >= :st',
            'st=1'
        );
    }

?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-cogs">Configurações</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php">Dashboard</a>
			<span class="crumb">/</span>
			Configurações
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Reiniciar Configurações!" href="dashboard.php?wc=config/home&wc_recet_config=true"
		   class="btn btn_yellow icon-warning wc_resetconfig">Resetar Configurações!</a>
	</div>
</header>
<div class="dashboard_content">
    <?php
        $Read = $Read ?? new Read();
        $Read->fullRead('SELECT conf_type FROM ' . DB_CONF . ' GROUP BY conf_type');
        if ($Read->getResult()) {
            echo "<div class='box box70' style='padding: 0'>";
            $iForm = 0;
            foreach ($Read->getResult() as $Config) {
                $Active = (0 == $iForm ? 'wc_active' : null);
                ++$iForm;
                echo sprintf(
                    "<article class='box box100 box_conf conf wc_tab_target %s' id='%s'>",
                    $Active,
                    $Config['conf_type']
                );

                // Trigger Message
                echo "<div class='trigger trigger_error icon-warning al_center'><b>ATENÇÃO:</b> Todas as configurações a seguir interferem diretamente no funcionamento do projeto!</div>";

                echo sprintf(
                    "<div class='panel_header default'><h2 class='icon-cog'>%s</h2></div>",
                    $Config['conf_type']
                );
                echo "<div class='panel'>";

                $ConfMenu[] = $Config['conf_type'];

                $Read->exeRead(
                    DB_CONF,
                    'WHERE conf_type = :type ORDER BY conf_key ASC',
                    'type=' . $Config['conf_type']
                );
                if (!$Read->getResult()) {
                    echo Check::erro(
                        sprintf('Não existem configurações do tipo %s.', $Config['conf_type']),
                        E_USER_WARNING
                    );
                } else {
                    foreach ($Read->getResult() as $ConfType) {
                        extract($ConfType);
                        echo "<form class='auto_save' name='workcontrol_conf' action='' method='post' enctype='multipart/form-data'>";
                        echo "<input type='hidden' name='callback' value='Config'/>";
                        echo "<input type='hidden' name='callback_action' value='WorkControl'/>";
                        echo sprintf("<input type='hidden' name='conf_id' value='%s'/>", $conf_id);
                        echo "<label class='label'>";
                        echo sprintf("<span class='legend'>%s</span>", $conf_key);
                        echo "<input name='conf_value' value='" . ($conf_value ? htmlspecialchars(
                                (string)$conf_value,
                                ENT_QUOTES
                            ) : 0) . "' type='text'/>";
                        echo '</label>';
                        echo '</form>';
                    }
                }
                echo "<div class='clear'></div>";
                echo '</div>';
                echo '</article>';
            }
            echo '</div>';

            echo "<article class='box box30 box_conf_menu'>"
                . "<div class='panel'>";
            $iMenu = 0;
            foreach ($ConfMenu as $MenuConf) {
                $Active = (0 == $iMenu ? 'wc_active' : null);
                ++$iMenu;
                echo sprintf("<a class='conf_menu wc_tab %s' href='#%s'>%s</a>", $Active, $MenuConf, $MenuConf);
            }
            echo '</div>'
                . '</article>';
        } else {
            $StartConfig = true;
        }

        // Reset acionado pelo botão "Resetar Configurações" OU primeira instalação (tabela vazia).
        // Sobrescreve o ws_config com as constantes atuais do código (ConfigLoader).
        // Fora daqui, o sistema mantém o BANCO como prioridade (ConfigLoader::loadConfigFromDatabase()).
        $getResetConfig = filter_input(INPUT_GET, 'wc_recet_config', FILTER_VALIDATE_BOOLEAN);
        if ($getResetConfig || !empty($StartConfig)) {
            ConfigLoader::syncConfigToDatabase();
            header('Location: dashboard.php?wc=config/home');

            exit;
        }
    ?>
</div>
