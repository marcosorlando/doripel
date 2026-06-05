<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_CONFIG_API;
    if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-cog">Work Control API</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=config/home">Configurações</a>
			<span class="crumb">/</span>
			API
		</p>
	</div>
</header>
<div class="dashboard_content">
	<div class="wc_api_new">
		<form action="" method="post" enctype="multipart/form-data">
			<div style="text-align: center">
				<img class="form_load none" style="margin: 0 auto 20px auto;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>
			<input type="hidden" name="callback" value="Api"/>
			<input type="hidden" name="callback_action" value="create"/>
			<div style="display: flex">
				<input required type='text' name='api_key' value='' placeholder='Api Key, Ex: www.workcontrol.com.br'/>
				<button class='btn btn_green'>Criar APP</button>
			</div>
		</form>
		<p class="wc_api_new_info">( ! ) Sua Key deve identificar o uso da API e por isso aconselhamos sempre definir
			a mesma com o link do site que vai consumir os dados. <a href="https://github
            .com/RobsonVLeite/WorkControlAPI" target="_blank">Classe de consumo</a></p>
	</div>
    <?php
        $Read->exeRead(DB_WC_API, 'ORDER BY api_date DESC');
        if (!$Read->getResult()) {
            echo Check::erro(
                'Ainda não existem APPs cadastrados para consumo de API. Você pode começar agora!',
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $APP) {
                \extract($APP);
                ?>
				<article class="box box50 wc_api_app api_single" id="<?php
                    echo $api_id; ?>">
					<header>
						<h1 class="icon-power-cord">APP <?php
                                echo $api_id; ?></h1>
					</header>
					<div class="box_content">
						<p>
							<b><span>Key:</span></b>
							<textarea spellcheck="false" onclick="this.select();" rows="1"
							          style="resize: none;"><?php
                                    echo $api_key; ?></textarea>
						</p>
						<p>
							<b><span>Token:</span></b>
							<textarea spellcheck="false" onclick="this.select();" rows="1"
							          style="resize: none;"><?php
                                    echo $api_token; ?></textarea>
						</p>
						<p class="box50">
							<b><span>Loads:</span></b> <?php
                                echo \str_pad((string)$api_loads, 4, 0, 0); ?></p>
						<p class="box50">
							<b><span>Último Load:</span></b> <?php
                                echo \date('d/m/y H\hi', \strtotime((string)$api_lastload)); ?>
						</p>
						<p class="box50">
							<b><span>Cadastro:</span></b> <?php
                                echo \date('d/m/y H\hi', \strtotime((string)$api_date)); ?></p>
						<p class="box50">
							<b><span>Status:</span></b> <?php
                                echo 1 == $api_status ? '<span class="font_green jwc_status">Ativo</span>' : '<span class="font_red jwc_status">Inativo</span>'; ?>
						</p>
						<a target="_blank" class="wc_api_test jwc_api_test"
						   href="<?php
                               echo BASE; ?>/_api/post.php?key=<?php
                               echo $api_key; ?>&token=<?php
                               echo $api_token; ?>&limit=1"
						   title="Testar Post">Testar post via API</a>
						<div class="wc_api_app_actions">
                        <span rel="api_single" style="<?php
                            echo 1 == $api_status ? '' : 'display: none;'; ?>" callback="Api"
                              callback_action="inactive"
                              class="jwc_active_action jwc_inactive icon-checkmark icon-notext btn btn_green"
                              id="<?php
                                  echo $api_id; ?>"></span>
							<span rel="api_single" style="<?php
                                echo 1 == $api_status ? 'display: none;' : ''; ?>" callback="Api"
							      callback_action="active"
							      class="jwc_active_action jwc_active icon-warning icon-notext btn btn_yellow"
							      id="<?php
                                      echo $api_id; ?>"></span>
							<span rel="api_single" class="j_delete_action icon-notext icon-cancel-circle btn btn_red"
							      id="<?php
                                      echo $api_id; ?>"></span>
							<span rel="api_single" callback="Api" callback_action="delete"
							      class="j_delete_action_confirm icon-warning btn btn_yellow" style="display: none"
							      id="<?php
                                      echo $api_id; ?>">Deletar App?</span>
						</div>
					</div>
				</article>
                <?php
            }
        }
    ?>
</div>
