<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (!APP_LEADS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_LEADS) {
        Check::accessBlocked();
    }

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_LEADS, 'WHERE lead_name IS NULL AND lead_email IS NULL and lead_status = :st', 'st=1');
    }
?>
<div class="box box100">
	<header class="header_pink">
		<h1 class="icon-books">BASE DE LEADS:
			<a class="icon-download btn btn_yellow fl_right" style="margin-top: -5px;"
			   href="<?php
                   echo INCLUDE_PATH; ?>/lp/excell-export.php?a=<?php
                   echo $Admin['user_level']; ?>"
			   title="Clique para Salvar em Excel">Download</a>
			<a class="icon-user-plus btn btn_green fl_right" style="margin-top: -5px; margin-right: 5px"
			   href="dashboard.php?wc=leads/create" title="Novo Lead">Inserir Lead</a>
		</h1>
	</header>
	<div class="panel dashboard_search">

		<article class="lead_header">
			<p>NOME</p>
			<p>DATA</p>
			<p>E-MAIL</p>
			<p>ID CONVERSÃO</p>
			<p>

			</p>
		</article>
        <?php
            $Read ??= new Read();
            $Read->exeRead(DB_LEADS, 'ORDER BY lead_date DESC');
            if (!$Read->getResult()) {
                echo Check::erro('<span>Ainda não existem Leads cadastrados!</span>', E_USER_NOTICE);
                echo "<div class='clear'></div>";
            } else {
                foreach ($Read->getResult() as $Lead) {
                    extract($Lead);
                    echo " <article>
                    <h1 class='icon-eye'><a href='#' title='Ver detalhes'>{$lead_name}</a></h1>
                      <p>DIA " . date('d/m/Y H\hi', strtotime((string)$lead_date)) . "</p>
                        <p><a href='mailto:{$lead_email}' title='Enviar e-mail para esse lead'>{$lead_email}</a></p>
                               <p>{$lead_conversion}</p>
                               <p>                            
                                    <a href='dashboard.php?wc=leads/create&id={$lead_id}' class='btn btn_blue icon-notext icon-pen wc_tooltip'>
                                      <span class='wc_tooltip_balloon'>Editar</span></button>
                                      </a>
                                    <button class='btn btn_red icon-notext icon-cross wc_tooltip j_wc_action' data-callback='Leads' data-callback-action='delete' data-value='{$lead_id}'><span class='wc_tooltip_balloon'>Deletar</span></button>
                                </p>
                            </article>
                        ";
                }
            }
        ?>
		<!--<a class="dashboard_searchnowlink" href="dashboard.php?wc=searchnow" title="Ver Mais">MAIS PESQUISAS!</a>-->
		<div class="clear"></div>
	</div>
</div>
