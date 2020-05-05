<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_withdrawRequirementBetCntCondition_201509151935 extends CI_Migration {

    private $tableName = 'promorules';

    public function up() {
        $fields = array(
                                'withdrawRequirementBetCntCondition' => array(
                                                                 'type' => 'DOUBLE',
                                                        ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }

    public function down() {
        $fields = array(
                                'withdrawRequirementBetCntCondition' => array(
                                                                 'type' => 'INT',
                                                        ),
        );
        $this->dbforge->modify_column($this->tableName, $fields);
    }
}