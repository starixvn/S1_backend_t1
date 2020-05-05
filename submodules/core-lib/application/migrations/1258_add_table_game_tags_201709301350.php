<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_game_tags_201709301350 extends CI_Migration {

    private $tableName = 'game_tags';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'tag_name' => array(
                'type' => 'varchar',
                'constraint' => '100',
                'null' => false,
            ),
            'tag_code' => array(
                'type' => 'varchar',
                'constraint' => '100',
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $data = array(
            "slots" => array(
                "tag_name" => "Slots",
                "tag_code" => "slots",
            ),
            "Lottery" => array(
                "tag_name" => "Lottery",
                "tag_code" => "lottery",
            ),
            "Fishing Game" => array(
                "tag_name" => "Fishing Game",
                "tag_code" => "fishing_game",
            ),
            "Live Dealer" => array(
                "tag_name" => "Live Dealer",
                "tag_code" => "live_dealer",
            ),
            "Casino" => array(
                "tag_name" => "Casino",
                "tag_code" => "casino",
            ),
            "Gamble" => array(
                "tag_name" => "Gamble",
                "tag_code" => "gamble",
            ),
            "Table Games" => array(
                "tag_name" => "Table Games",
                "tag_code" => "table_games",
            ),
            "Table and Cards" => array(
                "tag_name" => "Table and Cards",
                "tag_code" => "table_and_cards",
            ),
            "Card Games" => array(
                "tag_name" => "Card Games",
                "tag_code" => "card_games",
            ),
            "E-Sports" => array(
                "tag_name" => "E-Sports",
                "tag_code" => "e_sports",
            ),
            "Fixed Odds" => array(
                "tag_name" => "Fixed Odds",
                "tag_code" => "fixed_odds",
            ),
            "Arcade" => array(
                "tag_name" => "Arcade",
                "tag_code" => "arcade",
            ),
            "Horce Racing" => array(
                "tag_name" => "Horce Racing",
                "tag_code" => "horce_racing",
            ),
            "Progressives" => array(
                "tag_name" => "Progressives",
                "tag_code" => "progressives",
            ),
            "Sports" => array(
                "tag_name" => "Sports",
                "tag_code" => "sports",
            ),
            "Unknown" => array(
                "tag_name" => "Unknown",
                "tag_code" => "unknown",
            ),
        );

        foreach ($data as $value) {
            $value['created_at'] = $this->utils->getNowForMysql();
            $this->db->insert($this->tableName,$value);
        }

        $field_game_tag_id = array(
            'game_tag_id' => array(
                'type' => 'INT',
                'null' => false,
            )
        );

        $this->dbforge->add_column('game_type', $field_game_tag_id);

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
        $this->dbforge->drop_column('game_type', 'game_tag_id');
    }
}
