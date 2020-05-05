<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_imAccount3_to_playerdetails_201711061400 extends CI_Migration {
// class Migration_add_column_to_playerdetails_201706061530 extends CI_Migration {

	public function up() {
		// Alter playerdetails
		$fields = array(
			'imAccount3' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
			),
			'imAccountType3' => array(
				'type' => 'VARCHAR',
				'constraint' => 32,
			),
		);
		$this->dbforge->add_column('playerdetails', $fields);

		// Insert into registration_fields
        $data = array(
            array(
                'registrationFieldId' => 47,
                'type' => '1',
                'field_name' => 'Instant Message 3',
                'alias' => 'imAccount3',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-11-06 14:00:00',
                'can_be_required' => '0',
            ),
        );
        $this->db->insert_batch('registration_fields', $data);

	}

	public function down() {
        $this->db->where('alias', array('imAccount3'));
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');

		$this->dbforge->drop_column('playerdetails', 'imAccountType3');
		$this->dbforge->drop_column('playerdetails', 'imAccount3');
	}
}