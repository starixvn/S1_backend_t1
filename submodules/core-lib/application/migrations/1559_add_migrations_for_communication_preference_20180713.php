<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_migrations_for_communication_preference_20180713 extends CI_Migration {

    private $tableName_1 = 'playerdetails';
    private $tableName_2 = 'player_communication_preference_history';


    public function up() {

        // -- Add column in playerdetails table
        $playerdetails_fields = array(
            'communication_preference' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->add_column($this->tableName_1, $playerdetails_fields);


        // -- Create new table for communication preference history
        $new_table_fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'player_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'preferences' => array(
                'type' => 'TEXT',
                'null' => false,
            ),
            'changes' => array(
                'type' => 'TEXT',
                'null' => false,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => true,
            ),
            'notes' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ),
            'requested_by' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'requested_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        );

        $this->dbforge->add_field($new_table_fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName_2);

        $this->db->query("CREATE INDEX idx_player_id ON ".$this->tableName_2."(player_id)");
    }

    public function down() {

        $this->db->query("DROP INDEX idx_player_id ON ".$this->tableName_2);
        $this->dbforge->drop_column($this->tableName_1, 'communication_preference');
        $this->dbforge->drop_table($this->tableName_2);
    }
}
