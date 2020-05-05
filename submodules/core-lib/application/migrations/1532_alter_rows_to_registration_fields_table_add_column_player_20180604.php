<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_rows_to_registration_fields_table_add_column_player_20180604 extends CI_Migration {

    public function up() {
        $regFieldsTbl = 'registration_fields';
        $this->db->where('alias', 'newsletter_subscription')->where('type', '1');
        $q = $this->db->get($regFieldsTbl);

        if($q->num_rows() == 0){
            $data = array(
                'registrationFieldId' => 52,
                'type' => '1',
                'field_name' => 'Newsletter Subscription',
                'alias' => 'newsletter_subscription',
                'visible' => '1',
                'required' => '1',
                'can_be_required' => '0');

            $this->db->insert($regFieldsTbl, $data);
        }

        $fields = array(
            'newsletter_subscription' => array(
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0,
            )
        );
        if(!$this->db->field_exists('newsletter_subscription', 'player')){
            $this->dbforge->add_column('player', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('newsletter_subscription', 'player')){
            $this->dbforge->drop_column('player', 'newsletter_subscription');
        }
    }

}