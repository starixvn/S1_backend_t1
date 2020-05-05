<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_player_details_tables_201711211234 extends CI_Migration {

    public function up() {
        $fields = array(
            'id_card_number' => array(
                'type' => 'VARCHAR',
                'constraint'=>"30",
                'null' => true,
            ),
        );

        $this->dbforge->add_column('playerdetails', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('playerdetails', 'id_card_number');
    }

}
