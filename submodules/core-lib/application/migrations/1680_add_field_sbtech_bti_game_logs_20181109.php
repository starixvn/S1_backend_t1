<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_field_sbtech_bti_game_logs_20181109 extends CI_Migration {

	private $tableName = 'sbtech_bti_game_logs';

	public function up() {
		$newFields = array();

		if (!$this->db->field_exists('gain', $this->tableName)) {
			$newFields['gain'] = array(
                'type' => 'DOUBLE',
                'null' => false,
			);
		}

		# REMOVE incorrect name feilds
        if($this->db->field_exists('odds_style_of_user":', $this->tableName)){
			$this->dbforge->drop_column($this->tableName, 'odds_style_of_user":');
        }
        if($this->db->field_exists('odds_dec":', $this->tableName)){
			$this->dbforge->drop_column($this->tableName, 'odds_dec":');
        }

        # ADD Correct Fields
        if(!$this->db->field_exists('odds_style_of_user', $this->tableName)){
			$newFields['odds_style_of_user'] = array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
			);
        }

        if(!$this->db->field_exists('odds_dec', $this->tableName)){
			$newFields['odds_dec'] = array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
			);
        }

        $this->dbforge->add_column($this->tableName, $newFields);
	
	}

    public function down(){
    }
}