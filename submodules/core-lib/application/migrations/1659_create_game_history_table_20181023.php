<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_game_history_table_20181023 extends CI_Migration {

    public function up() {

        $game_description_history_fields = [
            'id'                    => ['type' => 'INT',        'auto_increment' => TRUE,'null' => false],
            'game_description_id'   => ['type' => 'INT',        'null' => false],
            'game_platform_id'      => ['type' => 'INT',        'constraint' => 11,'null' => false],
            'game_type_id'          => ['type' => 'INT',        'constraint' => 11,'null' => false],
            'game_name'             => ['type' => 'VARCHAR',    'constraint' => '500','null' => true],
            'game_code'             => ['type' => 'VARCHAR',    'constraint' => '200','null' => true],
            'attributes'            => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'note'                  => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'english_name'          => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'external_game_id'      => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'clientid'              => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'moduleid'              => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'sub_game_provider'     => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'flash_enabled'         => ['type' => 'INT',        'null' => false,'default' => 1],
            'status'                => ['type' => 'INT',        'null' => false,'default' => 1],
            'flag_show_in_site'     => ['type' => 'INT',        'null' => false, 'default' => 0],
            'no_cash_back'          => ['type' => 'INT',        'null' => true, 'default' => 0],
            'void_bet'              => ['type' => 'INT',        'null' => true],
            'game_order'            => ['type' => 'INT',        'null' => true],
            'related_game_desc_id'  => ['type' => 'INT',        'null' => true],
            'dlc_enabled'           => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'progressive'           => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'enabled_freespin'      => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'offline_enabled'       => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'mobile_enabled'        => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'enabled_on_android'    => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'enabled_on_ios'        => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'flag_new_game'         => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'html_five_enabled'     => ['type' => 'TINYINT',    'constraint' => 4,'null' => false,'default' => 0],
            'demo_link'             => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'md5_fields'            => ['type' => 'VARCHAR',    'constraint' => '100','null' => true],
            'deleted_at'            => ['type' => 'DATETIME',   'null' => true],
            'created_on'            => ['type' => 'DATETIME',   'null' => true],
            'updated_at'            => ['type' => 'DATETIME',   'null' => true],
            'action'                  => ['type' => 'varchar',    'constraint' => '20', 'null' => false],
        ];
        $this->dbforge->add_field($game_description_history_fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('game_description_history');

        $game_type_history_fields = [
            'id'                    => ['type' => 'INT', 'auto_increment' => TRUE,'null' => false],
            'game_type_id'          => ['type' => 'INT', 'null' => false],
            'game_platform_id'      => ['type' => 'INT', 'constraint' => 11,'null' => false],
            'game_type'             => ['type' => 'varchar', 'constraint' => 2000,],
            'game_type_lang'        => ['type' => 'varchar', 'constraint' => 2000,],
            'note'                  => ['type' => 'varchar', 'constraint' => 1000,'null'=>true],
            'status'                => ['type' => 'tinyint', 'constraint' => 1,],
            'flag_show_in_site'     => ['type' => 'tinyint', 'constraint' => 1,],
            'order_id'              => ['type' => 'int', 'constraint' => 11,'null'=>true],
            'auto_add_new_game'     => ['type' => 'tinyint', 'constraint' => 1,],
            'related_game_type_id'  => ['type' => 'int', 'constraint' => 11,'null'=>true],
            'created_on'            => ['type' => 'datetime',],
            'auto_add_to_cashback'  => ['type' => 'tinyint', 'constraint' => 1,],
            'game_type_code'        => ['type' => 'varchar', 'constraint' => 100,],
            'updated_at'            => ['type' => 'datetime','null'=>true],
            'game_tag_id'           => ['type' => 'int', 'constraint' => 11,],
            'md5_fields'            => ['type' => 'varchar', 'constraint' => 32,],
            'deleted_at'            => ['type' => 'datetime',],
            'action'                  => ['type' => 'varchar',    'constraint' => '20', 'null' => false],
        ];

        $this->dbforge->add_field($game_type_history_fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('game_type_history');

    }

    public function down() {
        $this->dbforge->drop_table('game_description_history');
        $this->dbforge->drop_table('game_type_history');
    }

}
