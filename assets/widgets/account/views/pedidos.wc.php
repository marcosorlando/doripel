<?php

use App\Helpers\Check;

if (empty($_SESSION['userLogin']) || !APP_PRODUCTS_TRAVI) {
    exit('<h1 style="padding: 50px 0; text-align: center; font-size: 3em; font-weight: 300; color: #C63D3A">Acesso Negado!</h1>');
}

echo "<div class='workcontrol_account_view'>";
echo "<p class='wc_account_title'><span>Meus Pedidos:</span><p>";

$Page = (empty($URL[2]) ? 1 : $URL[2]);
$Pager = new Pager($AccountBaseUI.'/pedidos/', '<<', '>>', 2);
$Pager->exePager($Page, 10);
$Read->exeRead(
    DB_ORDERS,
    'WHERE user_id = :id ORDER BY order_date DESC LIMIT :limit OFFSET :offset',
    \sprintf('id=%s&limit=%d&offset=%d', $user_id, $Pager->getLimit(), $Pager->getOffset())
);
if (!$Read->getResult()) {
    $Pager->returnPage();
    echo Check::erro('<b>Você ainda não possui pedidos em nosso site!</b>');
} else {
    foreach ($Read->getResult() as $Order) {
        \extract($Order);
        $order_installments = (empty($order_installments) ? 1 : $order_installments);
        $order_installment = (empty($order_installment) ? $order_price : $order_installment);
        echo \sprintf(
            "<div class='wc_account_order'><p><a title='Ver Pedido' href='%s/pedido/%s#acc'>#",
            $AccountBaseUI,
            $order_id
        ).\str_pad((string) $order_id, 7, 0, 0).'</a></p><p>'.\date(
            'd/m/Y H\hi',
            \strtotime((string) $order_date)
        ).'</p><p>R$ '.\number_format(
            $order_installments * $order_installment,
            '2',
            ',',
            '.'
        ).'</p><p>'.\getOrderPayment($order_payment).'</p><p>'.\getOrderStatus($order_status).'</p></div>';
    }
}

$Pager->exePaginator(DB_ORDERS, 'WHERE user_id = :id', 'id='.$user_id, '#acc');
echo $Pager->getPaginator();
echo '</div>';
