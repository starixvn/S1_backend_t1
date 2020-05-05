<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_vr_game_logs_odds_column_size_201907230100 extends CI_Migration {
    private $tableName = 'vr_game_logs';

    public function up() {
        //modify column size
        $fields = array(
            'odds' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
      //restore original column size
      $fields = array(
          'odds' => array(
              'type' => 'VARCHAR',
              'constraint' => '100',
              'null' => true,
          ),
      );
      $this->dbforge->modify_column($this->tableName, $fields);
    }
}
