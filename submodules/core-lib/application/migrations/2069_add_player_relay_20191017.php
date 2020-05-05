<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_player_relay_20191017 extends CI_Migration {

	private $tableName = 'player_relay';

    public function up() {

        $fields = array(
            'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
            ),
            'player_id' => array(
				'type' => 'INT',
				'null' => null,
            ),
            'username' => array(
				'type' => 'VARCHAR',
                'constraint' => '50',
				'null' => false,
			),
			'affiliate_id' => array(
				'type' => 'INT',
				'null' => false,
            ),
            'agent_id' => array(
				'type' => 'INT',
				'null' => false,
            ),
            'referee_player_id' => array(
				'type' => 'INT',
				'null' => false,
            ),
			'first_deposit_amount' => array(
				'type' => 'DOUBLE',
				'null' => false,
			),
			'first_deposit_datetime' => array(
				'type' => 'DATETIME',
				'null' => true,
			),
			'sync_created_at' => array( // for sync
				'type' => 'DATETIME',
				'null' => false,
            ),
            'sync_updated_at' => array( // for sync
				'type' => 'DATETIME',
				'null' => false,
            ),
            'created_on' => array( // for player
				'type' => 'DATETIME',
				'null' => true,
            ),
            'deleted_at' => array( // for player
				'type' => 'DATETIME',
				'null' => true,
            ),


        );

        if( ! $this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
        }

        $indexPreStr = 'idx_';
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'affiliate_id', 'affiliate_id');
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'agent_id', 'agent_id');
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'referee_player_id', 'referee_player_id');

        $this->player_model->addIndex($this->tableName, $indexPreStr. 'username', 'username');
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'player_id', 'player_id');
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'created_on', 'created_on'); // @todo sync player.createdOn
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'deleted_at', 'deleted_at');

        $this->player_model->addIndex($this->tableName, $indexPreStr. 'first_deposit_datetime', 'first_deposit_datetime');
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'sync_created_at', 'sync_created_at');
        $this->player_model->addIndex($this->tableName, $indexPreStr. 'sync_updated_at', 'sync_updated_at');
    }

    public function down() {

        if($this->db->table_exists($this->tableName)){
			$this->dbforge->drop_table($this->tableName);
        }

    }
}