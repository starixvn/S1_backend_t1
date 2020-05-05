<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_tables_for_cms_static_site_20180122 extends CI_Migration {

    public function up() {

        $section_fields = [
            'id'            => ['type' => 'INT', 'auto_increment' => TRUE],
            'name'          => ['type' => 'VARCHAR', 'constraint' => '100'],
            'description'   => ['type' => 'VARCHAR', 'constraint' => '100'],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'    => ['type' => 'TIMESTAMP', 'null' => true]
        ];
        $this->dbforge->add_field($section_fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('section_type_ss');

        $fields = [
            'id'                => ['type' => 'INT', 'auto_increment' => TRUE],
            'section_type_id'   => ['type' => 'INT'],
            'header_img_name'   => ['type' => 'VARCHAR', 'constraint' => '100'],
            'img_url'           => ['type' => 'VARCHAR', 'constraint' => '100'],
            'title'             => ['type' => 'VARCHAR', 'constraint' => '100'],
            'game_type'         => ['type' => 'VARCHAR', 'constraint' => '50'],
            'content'           => ['type' => 'TEXT', 'null' => true],
            'content_img_name'  => ['type' => 'VARCHAR', 'constraint' => '100'],
            'visible'           => ['type' => 'INT'],
            'modified_by'       => ['type' => 'INT(10)', 'unsigned' => true],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'expiry_date'       => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'        => ['type' => 'TIMESTAMP', 'null' => true]
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('banner_promotion_ss');

        $this->load->model('player_model');
        $this->player_model->addIndex('banner_promotion_ss', 'idx_section_type_id', 'section_type_id');
        $this->player_model->addIndex('banner_promotion_ss', 'idx_modified_by', 'modified_by');
    }

    public function down() {
        $this->dbforge->drop_table('banner_promotion_ss');
        $this->dbforge->drop_table('section_type_ss');
    }

}
