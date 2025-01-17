<?php

global $wpdb;

global $woocommerce;


$order = wc_get_order($pid);

if ($order && $pid) {



    $payment_gateway_id = MNS_ID;
    $payment_gateway_qr_id = MNS_ID_QRPROM;
    $payment_gateway_installment_id = MNS_ID_INSTALLMENT;


    $payment_gateways = WC_Payment_Gateways::instance();
    $payment_gateway = $payment_gateways->payment_gateways()[$payment_gateway_id];
    $payment_gateway_qr = $payment_gateways->payment_gateways()[$payment_gateway_qr_id];
    $payment_gateway_installment = $payment_gateways->payment_gateways()[$payment_gateway_installment_id];

    $gateways = WC()->payment_gateways->get_available_payment_gateways();


    $ms_secret_id = $payment_gateway->settings['secret_id'];
    $ms_secret_key = $payment_gateway->settings['secret_key'];

    $MNS_PAYMENT_TYPE = get_post_meta($order->id, 'MNS_PAYMENT_TYPE', true);

    if ($MNS_PAYMENT_TYPE == "Qrnone") {

        $MNS_transaction = get_post_meta($order->id, 'MNS_transaction', true);
        $MNS_QR_TIME = get_post_meta($order->id, 'MNS_QR_TIME', true);
        $auto_cancel = $payment_gateway_qr->settings['auto_cancel'];

        if(empty($auto_cancel)){
            $limit_time = 1200;
        }else{
            $limit_time = $auto_cancel;
        }

        if ((time() - $MNS_QR_TIME) > $limit_time){

            $call_cancel = wp_remote_post(MNS_CANCEL_TRANSACTION, array(
                'method' => 'POST',
                'timeout' => 120,
                'body' => array(
                    'secret_id' => $ms_secret_id,
                    'secret_key' => $ms_secret_key,
                    'transaction_ID' => $MNS_transaction,
                )
            ));

            if (!is_wp_error($call_cancel)) {

                $json_status = json_decode($call_cancel["body"]);
                $text_check = "Transaction id : ".$MNS_transaction." Canceled";

                if($json_status[0]->status == "success" && $json_status[0]->message == $text_check){

                    $order->update_status("wc-cancelled");
                    wp_redirect(wc_get_order($order->id)->get_checkout_order_received_url());

                }else{
                    wp_redirect(wc_get_order($order->id)->get_checkout_order_received_url());
                }
            }else{
                wp_redirect(wc_get_order($order->id)->get_checkout_order_received_url());
            }
        }else{
            wp_redirect(wc_get_order($order->id)->get_checkout_order_received_url());
        }
    }else{
        wp_redirect(wc_get_order($order->id)->get_checkout_order_received_url());
    }
} else {
    wp_redirect(wc_get_order($order->id)->get_checkout_order_received_url());
}
