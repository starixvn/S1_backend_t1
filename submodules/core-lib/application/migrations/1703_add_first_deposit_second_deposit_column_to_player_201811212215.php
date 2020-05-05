<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_first_deposit_second_deposit_column_to_player_201811212215 extends CI_Migration {

    public function up() {
        $field = array(
            "first_deposit" => array(
                'type' => 'DOUBLE',
                'default' => 0,
                'null' => true,
            ),
            
        );

        if(!$this->db->field_exists('first_deposit', 'player'))
            $this->dbforge->add_column('player', $field);
        

        $field = array(
            "second_deposit" => array(
                    'type' => 'DOUBLE',
                    'default' => 0,
                    'null' => true,
                ),
        );

        if(!$this->db->field_exists('second_deposit', 'player'))
            $this->dbforge->add_column('player', $field);

    }

    public function down() {

        if($this->db->field_exists('first_deposit', 'player'))
            $this->dbforge->drop_column('player', 'first_deposit');
        

        if($this->db->field_exists('second_deposit', 'player'))
            $this->dbforge->drop_column('player', 'second_deposit');
    
    }
}
