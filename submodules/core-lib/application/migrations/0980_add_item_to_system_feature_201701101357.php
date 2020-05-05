<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_item_to_system_feature_201701101357 extends CI_Migration {

    const DISABLE_FEATURE = 0;

    private $tableName = 'system_features';

    public function up() {
        $this->db->insert($this->tableName,['name' => 'iovation_fraud_prevention', 'enabled' => self::DISABLE_FEATURE]);
    }

    public function down() {
        $this->db->delete($this->tableName, ['name' => 'iovation_fraud_prevention']);
    }
}
