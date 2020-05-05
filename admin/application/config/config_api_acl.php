<?php
/**
 * config_api_acl.php
 *
 * Available ACL Type:
 *   ip_bandwidth:
 *     support rule type:
 *       time_range
 *
 *   total_reqs_of_day: @TODO
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */

$config['api_acl'] = [];
$config['api_acl_override'] = []; // for config_secrect_local.php or config_locao.php to used.

/**
 * For api_acl_override example.
 *
 * $config['api_acl_override']['default']['ip_bandwidth']['enabled'] = FALSE;
 * or
 * $config['api_acl_override']['default']['ip_bandwidth']['limit_value'] = 5;
 */

$config['api_acl']['default'] = [
    'ip_bandwidth' => [
        'enabled' => TRUE,
        'type' => 'time_range',
        'time_range' => 60,
        'limit_value' => 30,
        'blocked_time' => 600,
        'response' => [
            'code' => 403,
            'msg' => 'Access Denied'
        ]
    ]
];