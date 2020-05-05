<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_png_game_logs_201707072115 extends CI_Migration {

    private $tableName = 'png_game_logs';

    public function up() {
        $fields = array(
            'casinoTransactionReleaseOpen' => array(
                'type' => 'text',
                'null' => true
            ),
            'casinoTransactionReleaseClosed' => array(
                'type' => 'text',
                'null' => true
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->tableName, 'casinoTransactionReleaseOpen');
        $this->dbforge->drop_column($this->tableName, 'casinoTransactionReleaseClosed');
    }
}