<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_bbin_game_names_201612251508 extends CI_Migration {

	public function up() {

        $fields = array(
            'game_type_code' => array(
                'type' => 'VARCHAR',
				'constraint' => '100',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_column('game_type', $fields);

        $this->db->query('create unique index idx_game_type_code_platform_id on game_type(game_platform_id, game_type_code)');
        // $this->player_model->addIndex('game_type', 'idx_game_type_code', 'game_type_code');

        if (!$this->db->field_exists('updated_at', 'game_description')) {

	        $fields = array(
	            'updated_at' => array(
	                'type' => 'DATETIME',
	                'null' => true,
	            ),
	        );

	        $this->dbforge->add_column('game_description', $fields);
	    }


	}

	public function down() {
		$this->db->query('drop index idx_game_type_code_platform_id on game_type');
        $this->dbforge->drop_column('game_type', 'game_type_code');
        $this->dbforge->drop_column('game_type', 'updated_at');

        // $this->dbforge->drop_column('game_description', 'updated_at');
    }

}