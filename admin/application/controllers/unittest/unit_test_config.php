<?php

$config=[
    'unit_test_base_model_queryPlanSelectorByExplain'=>[
        'from'=>'2019-07-02 00:00:00',
        'to'=>'2019-07-02 23:59:59',
    ]
];

if (file_exists(dirname(__FILE__) . '/unit_test_config_local.php')) {
    require_once dirname(__FILE__) . '/unit_test_config_local.php';
}

