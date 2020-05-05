<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_acuris_logs_20190611 extends CI_Migration {

    private $tableName = 'acuris_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'bigint',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'player_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'score' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'person_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'alternative_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'forename' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'middlename' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'surname' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'date_of_birth' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'year_of_birth' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'date_of_death' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'year_of_death' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'is_deceased' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'nationality' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'image_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'telephone_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'fax_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'mobile_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'pep_level' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'is_pep' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_sanctions_current' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_sanctions_previous' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_law_enforcement' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_financial_regulator' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_disqualified_director' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_insolvent' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_adverse_media' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'addresses' => array(
                'type' => 'TEXT',
            ),
            'aliases' => array(
                'type' => 'TEXT',
            ),
            'articles' => array(
                'type' => 'TEXT',
            ),
            'sanctions' => array(
                'type' => 'TEXT',
            ),
            'notes' => array(
                'type' => 'TEXT',
            ),
            'linked_businesses' => array(
                'type' => 'TEXT',
            ),
            'linked_person' => array(
                'type' => 'TEXT',
            ),
            'political_positions' => array(
                'type' => 'TEXT',
            ),
            'generated_by' => array(
                'type' => 'INT',
                'null' => true,
                'default' => 0,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}