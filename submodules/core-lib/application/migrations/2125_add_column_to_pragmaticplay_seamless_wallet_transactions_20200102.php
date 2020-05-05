<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_pragmaticplay_seamless_wallet_transactions_20200102 extends CI_Migration {

    private $tableName = 'pragmaticplay_seamless_wallet_transactions';

    public function up() {

        $fields = array(
            'campaign_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'campaign_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('campaignId', $this->tableName) &&
            !$this->db->field_exists('campaignType', $this->tableName) &&
            !$this->db->field_exists('currency', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'external_uniqueid');
        }

    }

    public function down() {
        if($this->db->field_exists('campaignId', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'campaignId');
        }
        if($this->db->field_exists('campaignType', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'campaignType');
        }
        if($this->db->field_exists('currency', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'currency');
        }
    }

}