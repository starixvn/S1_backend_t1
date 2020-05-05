<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_super_report_201705111615 extends CI_Migration {

    public function up() {
        $fields = array(
            'game_type_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
            'game_description_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ),
        );

        $this->dbforge->add_column('super_game_report', $fields);
        $this->dbforge->add_column('super_cashback_report', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('super_game_report', 'game_type_name');
        $this->dbforge->drop_column('super_game_report', 'game_description_name');

        $this->dbforge->drop_column('super_cashback_report', 'game_type_name');
        $this->dbforge->drop_column('super_cashback_report', 'game_description_name');
    }
}
