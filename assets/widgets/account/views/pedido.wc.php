<?php

if (empty($_SESSION['userLogin']) || !APP_PRODUCTS_TRAVI) {
    exit('<h1 style="padding: 50px 0; text-align: center; font-size: 3em; font-weight: 300; color: #C63D3A">Acesso Negado!</h1>');
}

$Read = new Read();
$OrId = \filter_var($URL[2], FILTER_VALIDATE_INT);
if (!$OrId) {
    echo "<div style='margin: 20px 20px 0 20px'>";
    echo \sprintf(
        "<div class='trigger trigger_alert' style='margin: 0;'>Olá %s, favor selecione um pedido para ver os detalhes!</div>",
        $user_name
    );
    echo '</div>';

    require __DIR__.'/pedidos.wc.php';
} else {
    $Read->exeRead(DB_ORDERS, 'WHERE order_id = :or AND user_id = :us', \sprintf('or=%s&us=%s', $OrId, $user_id));
    echo "<div class='workcontrol_account_view'>";
    if (!$Read->getResult()) {
        echo \sprintf(
            "<div class='trigger trigger_alert' style='margin: 0;'><b>Caro(a) %s,</b><p>Você tentou acessar um pedido que não existe ou não está disponível para ser acessado por sua conta %s.</p><p><a href='%s/pedidos#acc' title='Meus Pedidos'>Clique aqui para acessar seus pedidos!</a></p></div>",
            $user_name,
            $user_email,
            $AccountBaseUI
        );
    } else {
        \extract($Read->getResult()[0]);
        $order_installments = (empty($order_installments) ? 1 : $order_installments);
        $order_installment = (empty($order_installment) ? $order_price : $order_installment);

        $ShipmentUrl = ($order_shipcode > 4000 ? 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' : ECOMMERCE_SHIPMENT_COMPANY_LINK);
        echo "<p class='wc_account_title'><span>Detalhes do pedido:</span><p>";
        echo "<div class='workcontrol_account_home'>";
        echo '<p><b>Pedido: </b>'.\str_pad((string) $order_id, 7, 0, 0).'</p>';
        echo '<p><b>Data: </b>'.\date('d/m/Y H\hi', \strtotime((string) $order_date)).'</p>';
        echo '<p><b>Valor: </b>R$ '.\number_format($order_installments * $order_installment, '2', ',', '.').'</p>';
        echo '<p><b>Desconto: </b>'.($order_coupon ? $order_coupon : 0).'%</p>';
        echo '<p><b>Pagamento: </b>'.\getOrderPayment($order_payment).'</p>';
        echo '<p><b>Status: </b>'.\getOrderStatus($order_status).'</p>';
        echo '<p><b>Postado dia: </b>'.($order_shipment ? \date(
            'd/m/Y',
            \strtotime((string) $order_shipment)
        ) : 'Aguardando envio!').' '.($order_tracking && 1 != $order_tracking ? \sprintf(
            "- <a class='font_blue' target='_blank' href='%s%s' title='Rastrear Pedido'>Acompanhar Envio!</a>",
            $ShipmentUrl,
            $order_tracking
        ) : '').'</p>';
        echo '<p><b>Nota fiscal: </b>'.($order_nfepdf ? "<a class='font_blue' target='_blank' href='".BASE.\sprintf(
            "/uploads/%s' title='Nota Fiscal'>NFE</a>",
            $order_nfepdf
        ) : 'Aguardando Emissão').($order_nfexml ? ", <a class='font_blue' target='_blank' href='".BASE.\sprintf(
            "/uploads/%s' title='XML da nota'>XML</a>",
            $order_nfexml
        ) : null).'</p>';

        $Read->exeRead(
            DB_USERS_ADDR,
            'WHERE user_id = :usr AND addr_id = :addr',
            \sprintf('usr=%s&addr=%s', $user_id, $order_addr)
        );
        if ($Read->getResult()) {
            \extract($Read->getResult()[0]);
            echo \sprintf(
                "<p style='width: 100%%;'><b>Endereço: </b>%s<br>%s, %s<br>B. %s, %s/%s<br>%s - %s</p>",
                $addr_name,
                $addr_street,
                $addr_number,
                $addr_district,
                $addr_city,
                $addr_state,
                $addr_zipcode,
                $addr_country
            );
        }

        echo '</div>';

        if ('3' == $order_status) {
            echo "<div style='display: block; text-align: right; margin: 20px 0 40px 0;'><a class='btn btn_blue' title='Pagar agora' href='".BASE.'/pedido/pagamento/'.\base64_encode(
                (string) $order_id
            )."#cart' target='_blanck'>PAGAR AGORA!</a></div>";
        } elseif (
            2 != $order_status && 1 != $order_status && 6 != $order_status && \date(
                'Y-m-d H:i:s',
                \strtotime($order_date.'+'.E_ORDER_DAYS.'days')
            ) > \date('Y-m-d H:i:s')
        ) {
            echo "<div style='text-align: right;'>";
            if ($order_billet) {
                echo \sprintf(
                    "<div style='display: inline-block; text-align: right; margin: 20px 0 0 0;'><a class='btn btn_blue' title='Imprimir boleto' href='%s' target='_blank'>IMPRIMIR BOLETO!</a></div>",
                    $order_billet
                );
            }

            echo "<div style='display: inline-block; text-align: right; margin: 20px 0 40px 20px;'><a class='btn btn_blue' title='Pagar agora' href='".BASE.'/pedido/pagamento/'.\base64_encode(
                (string) $order_id
            )."#cart' target='_blanck'>PAGAR COM CARTÃO!</a></div>";
            echo '</div>';
        }

        echo "<div class='workcontrol_order_completed_card m_top'><p class='product'>Produto</p><p>Preço</p><p>Quant.</p><p>Total</p></div>";
        $SideTotalCart = 0;
        $SideTotalExtra = 0;
        $SideTotalPrice = 0;
        $Read->exeRead(DB_ORDERS_ITEMS, 'WHERE order_id = :orid', 'orid='.$order_id);
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $SideProduct) {
                if ($SideProduct['pdt_id']) {
                    $Read->fullRead(
                        'SELECT stock_code FROM '.DB_PDT_STOCK.' WHERE stock_id = :stid',
                        'stid='.$SideProduct['stock_id']
                    );
                    $ProductSize = ($Read->getResult() && 'default' != $Read->getResult()[0]['stock_code'] ? \sprintf(
                        " <span class='wc_cart_tag'>TAMANHO: %s</span>",
                        $Read->getResult()[0]['stock_code']
                    ) : null);
                    $Read->fullRead(
                        'SELECT pdt_name, pdt_cover FROM '.DB_PDT.' WHERE pdt_id = :pid',
                        'pid='.$SideProduct['pdt_id']
                    );

                    echo "<div class='workcontrol_order_completed_card items'>";
                    echo \sprintf(
                        "<p class='product'><img title='%s' alt='%s' src='",
                        $SideProduct['item_name'],
                        $SideProduct['item_name']
                    ).BASE.\sprintf(
                        '/tim.php?src=uploads/%s&w=',
                        $Read->getResult()[0]['pdt_cover']
                    ).THUMB_W / 5 .'&h='.THUMB_H / 5 ."'/><span><a target='_blank' href='".BASE.\sprintf(
                        "/produto/%s' title='Ver %s no site'>",
                        $Read->getResult()[0]['pdt_name'],
                        $SideProduct['item_name']
                    ).Check::Chars($SideProduct['item_name'], 42).\sprintf('</a>%s</span></p>', $ProductSize);
                    echo '<p>R$ '.\number_format($SideProduct['item_price'], '2', ',', '.').'</p>';
                    echo \sprintf('<p>%s</p>', $SideProduct['item_amount']);
                    echo '<p>R$ '.\number_format(
                        $SideProduct['item_price'] * $SideProduct['item_amount'],
                        '2',
                        ',',
                        '.'
                    ).'</p>';
                    $SideTotalCart += $SideProduct['item_price'] * $SideProduct['item_amount'];
                    echo '</div>';
                } else {
                    $SideTotalExtra += $SideProduct['item_price'] * $SideProduct['item_amount'];
                }
            }
        }

        $TotalCart = $SideTotalCart;
        $TotalExtra = $SideTotalExtra;
        echo "<div class='workcontrol_order_completed_card total'>";
        echo "<div class='wc_cart_total'>Sub-total: <b>R$ <span>".\number_format(
            $TotalCart,
            '2',
            ',',
            '.'
        ).'</span></b></div>';
        if ($order_coupon) {
            echo "<div class='wc_cart_discount'>Desconto: <b><strike>R$ <span>".\number_format(
                $SideTotalCart * ($order_coupon / 100),
                '2',
                ',',
                '.'
            ).'</span></strike></b></div>';
        }

        echo '<div>Frete: <b>R$ <span>'.\number_format($order_shipprice, '2', ',', '.').'</span></b></div>';
        if ($order_installments > 1) {
            echo '<div>Total : <b>R$ <span>'.\number_format($order_price, '2', ',', '.').'</span></b></div>';
            echo \sprintf(
                "<div class='wc_cart_price'><small><sup>%sx</sup> R\$ %s</small>:<b>R\$ <span>",
                $order_installments,
                $order_installment
            ).\number_format($order_installments * $order_installment, '2', ',', '.').'</span></b></div>';
        } else {
            echo "<div class='wc_cart_price'>Total : <b>R$ <span>".\number_format(
                $order_price,
                '2',
                ',',
                '.'
            ).'</span></b></div>';
        }

        echo '</div>';
    }

    echo '</div>';
}
