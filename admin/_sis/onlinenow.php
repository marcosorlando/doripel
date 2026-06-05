<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = 6;
    if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    $Where = '';
    $ParseString = '';

    $filter = \filter_input_array(INPUT_GET, FILTER_DEFAULT);
    if (!empty($filter['user'])) {
        $Where = 'AND online_user = :user';
        $ParseString = 'user=' . $filter['user'];
    }

    if (!empty($filter['url'])) {
        $Where = 'AND online_url = :url';
        $ParseString = 'url=' . $filter['url'];
    }
?>
<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-earth">Usuários Online Agora</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Online Agora
		</p>
	</div>
</header>

<div class="dashboard_content">

	<div class="box box100">

		<div class="panel_header success">
            <span>
                <a href="javascript:void(0)" class="btn btn_green icon-loop icon-notext" id="loopOnlineNow"></a>
            </span>
            <?php
                $Read->exeRead(
                    DB_VIEWS_ONLINE,
                    \sprintf('WHERE online_endview >= NOW() %s ORDER BY online_endview DESC', $Where),
                    $ParseString
                ); ?>
			<h2 class="icon-earth jwc_onlinenow">ONLINE AGORA: <?php
                    echo \str_pad(
                        (string)$Read->getRowCount(),
                        4,
                        0,
                        0
                    ); ?></h2>
		</div>

		<div class="panel wc_onlinenow dashboard_online">
            <?php
                if (!$Read->getResult()) {
                    echo Check::erro(
                        'Não existem usuários online neste momento!',
                        E_USER_NOTICE
                    );
                    echo '<div class="clear"></div>';
                } else {
                    $i = 0;
                    foreach ($Read->getResult() as $Online) {
                        ++$i;
                        $Name = ($Online['online_name'] ? \sprintf(
                            "<a href='dashboard.php?wc=users/create&id=%s' title='Ver Cliente'>%s</a>",
                            $Online['online_user'],
                            $Online['online_name']
                        ) : 'guest user');
                        $Date = \date('d/m/Y H\hi', \strtotime((string)$Online['online_startview']));

                        echo "<div class='single_onlinenow'>
                    <p>" . \str_pad($i, 4, 0, STR_PAD_LEFT) . "</p>
                    <p><a href='" . BASE . "/admin/dashboard.php?wc=onlinenow&user={$Online['online_user']}' class='btn btn_green btn_small icon-notext icon-filter'></a> {$Name}</p>
                    <p>{$Date}</p>
                    <p>{$Online['online_ip']}</p>
                    <p><a href='" . BASE . \sprintf(
                                "/admin/dashboard.php?wc=onlinenow&url=%s' class='btn btn_green btn_small icon-notext icon-filter'></a> <a target='_blank' href='",
                                $Online['online_url']
                            ) . BASE . \sprintf(
                                "/%s' title='Ver Destino'>",
                                $Online['online_url']
                            ) . ($Online['online_url'] ? $Online['online_url'] : 'home') . '</a></p>
                    </div>';
                    }
                }
            ?>
		</div>
	</div>
</div>

<script>
    //ICON REFRESH IN DASHBOARD
    $('#loopOnlineNow').click(function () {
        OnlineNow();
    });

    //DASHBOARD REALTIME
    setInterval(function () {
        OnlineNow(<?php echo empty($filter['user']) ? '0' : $filter['user']; ?>, <?php echo empty($filter['url']) ? '0' : \sprintf(
            "'%s'",
            $filter['url']
        ); ?>);
    }, 3000);
</script>
