<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_city_country_to_http_request_player_ip_last_request_201707071540 extends CI_Migration {

    private $tableName_http_request = 'http_request';
    private $tableName_player_ip_last_request = 'player_ip_last_request';

    public function up() {
        $fields = array(
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'country' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )

        );
        $this->dbforge->add_column($this->tableName_http_request, $fields);
        $this->dbforge->add_column($this->tableName_player_ip_last_request, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName_http_request, 'city');
        $this->dbforge->drop_column($this->tableName_http_request, 'country');
        $this->dbforge->drop_column($this->tableName_player_ip_last_request, 'city');
        $this->dbforge->drop_column($this->tableName_player_ip_last_request, 'country');
    }
}