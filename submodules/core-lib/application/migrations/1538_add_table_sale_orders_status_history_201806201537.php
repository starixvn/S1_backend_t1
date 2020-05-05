<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_sale_orders_status_history_201806201537 extends CI_Migration {

    private $tableName = 'sale_orders_status_history';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'order_id' => array(
                'type' => 'BIGINT',
                'null' => false,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('order_id');
        $this->dbforge->create_table($this->tableName);

        //add status details
        $fields = [
            'detail_status' => [
                'type' => 'INT',
                'null' => true,
            ],
        ];

        if(!$this->db->field_exists('detail_status', 'sale_orders')){
            $this->dbforge->add_column('sale_orders', $fields);
        }
    }

    public function down() {

        $this->dbforge->drop_table($this->tableName);

        if($this->db->field_exists('detail_status', 'sale_orders')){
            $this->dbforge->drop_column('sale_orders', 'detail_status');
        }

    }
}
