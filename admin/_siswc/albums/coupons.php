<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_PRODUCTS;
    if (!APP_PRODUCTS_TRAVI || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_PDT_COUPONS, 'WHERE cp_coupon IS NULL AND cp_id >= :st', 'st=1');
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-ticket">Cupons de Desconto</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
			<span class="crumb">/</span>
			Cupons de Desconto
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Desconto" href="dashboard.php?wc=products/coupon" class="btn btn_green icon-plus">Adicionar
			Cupom!</a>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $getPage = \filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Page = $getPage ?? 1;
        $Pager = new Pager('dashboard.php?wc=products/coupons&page=', '<<', '>>', 3);
        $Pager->exePager($Page, 12);
        $Read->exeRead(
            DB_PDT_COUPONS,
            'ORDER BY cp_end DESC LIMIT :limit OFFSET :offset',
            \sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset())
        );
        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                \sprintf(
                    "<span class='al_center icon-notification'>Ainda não existem cupons de desconto cadastrados %s. Comece agora mesmo criando uma nova oferta e aumente suas conversões!</span>",
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Coupon) {
                $Active = ($Coupon['cp_start'] <= \date('Y-m-d H:i:s') && $Coupon['cp_end'] >= \date(
                    'Y-m-d H:i:s'
                ) ? 'active' : '');
                echo "<article class='product_coupon {$Active} box box25' id='{$Coupon['cp_id']}'>
            <div class='box_content'>
                <header>
                    <h1 class='icon-ticket'>{$Coupon['cp_discount']}%<span>{$Coupon['cp_coupon']}</span></h1>
                    <p>{$Coupon['cp_title']}</p>
                    <p>Início: " . \date('d.m.Y \a\s H\hi', \strtotime((string)$Coupon['cp_start'])) . '</p>
                    <p>Término: ' . \date('d.m.Y \a\s H\hi', \strtotime((string)$Coupon['cp_end'])) . '</p>
                    <p>Ativações: ' . \str_pad((string)$Coupon['cp_hits'], 4, 0, 0) . "</p>
                </header>
                <a title='Editar Cupom!' href='dashboard.php?wc=products/coupon&id={$Coupon['cp_id']}' class='btn btn_blue icon-pencil icon-notext'></a>
                <span rel='product_coupon' class='j_delete_action btn btn_red icon-cancel-circle icon-notext' id='{$Coupon['cp_id']}'></span>
                <span rel='product_coupon' callback='Products' callback_action='coupon_remove' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none;' id='{$Coupon['cp_id']}'>Deletar Cupom?</span>
            </div>
        </article>";
            }
            $Pager->exePaginator(DB_PDT_COUPONS);
            echo $Pager->getPaginator();
        }
    ?>
</div>
