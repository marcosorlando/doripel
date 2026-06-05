<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_HELLO;
    if (!APP_PAGES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-bullhorn">Hellobar</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Hellobar
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Nova Hellobar" href="dashboard.php?wc=hello/create" class="btn btn_green icon-plus">Criar
			Hellobar!</a>
	</div>

</header>
<div class="dashboard_content">
    <?php
        $Read->fullRead('SELECT COUNT(hello_id) AS total_hello FROM ' . DB_HELLO);
        $CountHello = $Read->getResult()[0]['total_hello'];

        $getPage = \filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Paginator = new Pager('dashboard.php?wc=hello/home&pg=', '<<', '>>', 5, $CountHello);
        $Paginator->exePager($Page, 12);

        $Read->exeRead(
            DB_HELLO,
            'ORDER BY hello_date DESC LIMIT :limit OFFSET :offset',
            \sprintf('limit=%d&offset=%d', $Paginator->getLimit(), $Paginator->getOffset())
        );
        if (!$Read->getResult()) {
            $Paginator->returnPage();
            echo Check::erro(
                \sprintf(
                    "Ainda não existem hello bars cadastrados %s. Comece agora mesmo criando sua primeira hello!",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $HELLO) {
                \extract($HELLO);

                $hello_cta = (empty($hello_cta) ? 'Aqui um call to action!' : $hello_cta);
                $hello_title = (empty($hello_title) ? 'Você precisa editar essa hellobar!' : $hello_title);
                $hello_status = (1 == $hello_status ? "<span class='icon-checkmark font_green'>Ativo</span>" : "<span class='icon-cross font_red'>Inativo</span>");
                $hello_image = (!empty($hello_image) && \file_exists(
                    '../uploads/' . $hello_image
                ) ? 'uploads/' . $hello_image : 'admin/_img/no_image.jpg');

                echo "<article class='box box25 page_single hello_single' id='{$hello_id}'>
                <img alt='{$hello_cta}' title='{$hello_cta}' src='" . BASE . \sprintf(
                        '/tim.php?src=%s&w=',
                        $hello_image
                    ) . IMAGE_W / 2 . "&h=auto'/></a>
                <div class='box_content wc_normalize_height'>
                    <h1 class='title font_{$hello_color}'>{$hello_cta}</h1>
                    <p>{$hello_title}</p>
                    <p class='m_top box box50 al_center'>{$hello_status}</p>
                    <p class='m_top icon-cogs box box50 al_center'>" . ($hello_rule ? $hello_rule : 'site') . "</p>
                    <p class='m_top icon-eye box box50 al_center'>" . ($hello_views ? $hello_views : '0') . "</p>
                    <p class='m_top icon-eye-plus box box50 al_center'>" . ($hello_clicks ? $hello_clicks : '0') . "</p>
                </div>
                <div class='page_single_action'>
                    <a title='Editar Hello Bar' href='dashboard.php?wc=hello/create&id={$hello_id}' class='post_single_center icon-pencil btn btn_blue'>Editar</a>
                    <span rel='hello_single' class='j_delete_action icon-cancel-circle btn btn_red' id='{$hello_id}'>Excluir</span>
                    <span rel='hello_single' callback='Custom' callback_action='hellobar_delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$hello_id}'>Deletar Hello?</span>
                </div>
            </article>";
            }

            $Paginator->exePaginator(DB_HELLO);
            echo $Paginator->getPaginator();
        }
    ?>
</div>
