<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_visitor_log_20190619 extends CI_Migration {

    private $tableName = 'visitor_log';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'total_requests' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'valid_requests' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'failed_requests' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'init_proc_time' => array(
                'type' => 'int',
                'null' => true
            ),
            'unique_visitors' => array(
                'type' => 'BIGINT',
                'null' => true
            ),
            'unique_files' => array(
                'type' => 'BIGINT',
                'null' => true
            ),
            'exclude_ip_hits' => array(
                'type' => 'BIGINT',
                'null' => true
            ),
            'referrers' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'unique_404' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'static_files' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'log_size' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'bandwidth' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'hits' => array(
                'type' => 'BIGINT',
                'null' => true,
            ),
            'top_request_files' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'static_requests' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'not_found_404' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'visitor_hostname_ip' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'os_data' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'browser_data' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'hour_data' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'domain_data' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'referring_domain_data' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'http_status_data' => array(
                'type' => 'JSON',
                'null' => true,
            ),
            'total_date' => array(
                'type' => 'DATE',
                'null' => false,
            ),
            'client_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );


        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
        # Add Index
        $this->load->model('player_model');
        $this->player_model->addIndex($this->tableName, 'idx_total_date', 'total_date');
        $this->player_model->addIndex($this->tableName, 'idx_client_code', 'client_code');

/*
order by hits desc , and limit 10
top_request_files: {"order": 1, "hits": xx, "vistors": xx, "bandwidth": xx, "method":"", protocol:"", data:""}
static_requests: {"order":1, "hits": x, "vistors": xx, "bandwidth": xx, "method":"", protocol:"", data:""}
not_found_404: {"order":1, "hits": x, "vistors": xx, "bandwidth": xx, "method":"", protocol:"", data:""}
visitor_hostname_ip: {"order":1, "hits": x, "vistors": xx, "bandwidth": xx, data:""}
os_data: {"order":1, "hits": x, "vistors": xx, "bandwidth": xx, data:""}
browser_data: {"order":1, "hits": x, "vistors": xx, "bandwidth": xx, data:""}
domain_data: {"domain":"", "hits": x, "vistors": xx, "bandwidth": xx}
referring_domain_data: {"domain":"", "hits": x, "vistors": xx, "bandwidth": xx}
http_status_data: {"status_code":"", "hits": x, "vistors": xx, "bandwidth": xx}

order by hour asc
hour_data: {"hour":0, "hits": x, "vistors": xx, "bandwidth": xx}
*/


    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
