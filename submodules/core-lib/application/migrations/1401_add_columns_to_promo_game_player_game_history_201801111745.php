<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// class Migration_add_theme_id_to_promo_game_resources_201801092050 extends CI_Migration {
class Migration_add_columns_to_promo_game_player_game_history_201801111745 extends CI_Migration {

	private $tableName = 'promo_game_player_game_history';

    public function up() {
        $fields = array(
            'realized_at' => array(
                'type' => 'TIMESTAMP',
                'null' => true,
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'player_to_game_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->dbforge->modify_column($this->tableName, [
            'status' => [
                'type' => 'ENUM("started","closed","blocked")'   ,
                'null' => false,
                'default' => 'started'
            ]
        ]);

    }

    public function down() {
        $this->dbforge->modify_column($this->tableName, [
            'status' => [
                'type' => 'ENUM("started","closed")'   ,
                'null' => false,
                'default' => 'started'
            ]
        ]);
        $this->dbforge->drop_column($this->tableName, 'player_to_game_id');
        $this->dbforge->drop_column($this->tableName, 'notes');
        $this->dbforge->drop_column($this->tableName, 'realized_at');
    }
}

////END OF FILE////
