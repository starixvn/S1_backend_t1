<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_upload_csv_file_history_20180224 extends CI_Migration {

    private $tableName = 'upload_csv_file_history';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'csv_filename' => array(
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ),
            'csv_fullpath' => array(
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => true,
            ),
            'application_type' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'uploaded_by' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('csv_filename');
        $this->dbforge->create_table($this->tableName);

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
