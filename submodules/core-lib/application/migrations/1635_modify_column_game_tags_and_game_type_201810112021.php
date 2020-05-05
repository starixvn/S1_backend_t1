<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_game_tags_and_game_type_201810112021 extends CI_Migration {

    public function up() {

        $fields = array(
            'game_type' => array(
                'name'=>'game_type',
                'type' => 'varchar',
                'constraint' => '2000',
                'null' => false,
            ),
            'game_type_lang' => array(
                'name'=>'game_type_lang',
                'type' => 'varchar',
                'constraint' => '2000',
                'null' => false,
            ),
            'created_on' => array(
                'name'=>'created_on',
                'type' => 'datetime',
                'null' => true,
                'default' => 0,
                'extra' => null
            ),
        );
        $this->dbforge->modify_column('game_type', $fields);

        $fields = array(
            'tag_name' => array(
                'name'=>'tag_name',
                'type' => 'varchar',
                'constraint' => '2000',
                'null' => false,
            )
        );

        $this->dbforge->modify_column('game_tags', $fields);

        $table_and_cards = array(
            "tag_name" => '_json:{"1":"Table and Cards","2":"牌桌&牌游戏","3":"Table and Cards","4":"Table and Cards","5":"테이블 & 카드"}',
            "tag_code" => "table_and_cards",
            "updated_at" => $this->utils->getNowForMysql()
        );

        $this->db->where('tag_code', 'table_and_cards');
        $this->db->update('game_tags', $table_and_cards);
    }

    public function down() {

    }
}
