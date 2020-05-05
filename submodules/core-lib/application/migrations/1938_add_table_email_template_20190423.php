<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_email_template_20190423 extends CI_Migration {

    private $tableName = 'email_template';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'platform_type' => array(
                'type' => 'TINYINT',
                'null' => false,
            ),
            'template_type' => array(
                'type' => 'TINYINT',
                'null' => false,
            ),
            'template_name' => array(
                'type' => 'VARCHAR',
                'constraint'=> 150,
                'null' => false,
            ),
            'template_lang' => array(
                'type' => 'TINYINT',
                'null' => false,
            ),
            'mail_subject' => array(
                'type' => 'VARCHAR',
                'constraint'=> 300,
                'null' => TRUE
            ),
            'mail_content' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'mail_content_text' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'is_enable' => array(
                'type' => 'TINYINT',
                'null' => false,
                'default' => 0
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'createdBy' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'updatedBy' => array(
                'type' => 'INT',
                'null' => true,
            )
        );

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex($this->tableName, 'idx_template_name', 'template_name');


            $this->load->library("email_manager");
            $this->load->model('email_template_model');

            # Player
            $this->email_manager->createTemplate(email_template_model::PLAYER_PLATFORM_TYPE, email_template_model::EMAIL_VERIFICATION_TEMPLATE, 'player_verify_email');
            $this->email_manager->createTemplate(email_template_model::PLAYER_PLATFORM_TYPE, email_template_model::FORGOT_PASSWORD_TEMPLATE,    'player_change_login_password_successfully');
            $this->email_manager->createTemplate(email_template_model::PLAYER_PLATFORM_TYPE, email_template_model::FORGOT_PASSWORD_TEMPLATE,    'player_forgot_login_password');
            $this->email_manager->createTemplate(email_template_model::PLAYER_PLATFORM_TYPE, email_template_model::FORGOT_PASSWORD_TEMPLATE,    'player_change_withdrawal_password_successfully');
            $this->email_manager->createTemplate(email_template_model::PLAYER_PLATFORM_TYPE, email_template_model::EMAIL_VERIFICATION_TEMPLATE, 'player_verify_email_success');

            # Affiliate
            $this->email_manager->createTemplate(email_template_model::AFFILIATE_PLATFORM_TYPE, email_template_model::REGISTRATION_TEMPLATE, 'affiliate_registered_success');
            $this->email_manager->createTemplate(email_template_model::AFFILIATE_PLATFORM_TYPE, email_template_model::REGISTRATION_TEMPLATE, 'affiliate_activated');
        }
    }

    public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
