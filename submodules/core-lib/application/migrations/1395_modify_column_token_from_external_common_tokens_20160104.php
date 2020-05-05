<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_token_from_external_common_tokens_20160104 extends CI_Migration {

    private $tableName = 'external_common_tokens';

    public function up() {
        $this->dbforge->modify_column($this->tableName, array(
            'token' => array(
                'type' => 'VARCHAR',
                'constraint' => '350',
                'null' => true,
            ),
        ));
    }

    public function down() {
    }
}