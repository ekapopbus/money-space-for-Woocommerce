<?php

class MNS_Processpayment
{

    public static function init()
    {
        add_action('mns_router_generate_routes', array(get_class(), 'generate_routes'), 10, 1);
    }

    public static function generate_routes(MNS_Router $router)
    {
        $router->add_route('processpayment', array(
            'path' => '^process/payment/(.*?)$',
            'query_vars' => array(
                'pid' => 1,
            ),
            'page_callback' => function () {
                # code ..
            },
            'page_arguments' => array('pid'),
            'access_callback' => TRUE,
            'template' => array('../view/process-payment.php', dirname(__FILE__) . DIRECTORY_SEPARATOR . '../view/process-payment.php')
        ));
    }
}
