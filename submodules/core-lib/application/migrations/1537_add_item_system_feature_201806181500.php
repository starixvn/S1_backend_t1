<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_item_system_feature_201806181500 extends CI_Migration {

    private $tableName = 'system_features';

    public function up() {

        $show_upload_documents = (array) $this->db->get_where($this->tableName, array('name =' => 'show_upload_documents'))->row();

        if(!empty($show_upload_documents))
        {
            $show_player_upload_realname_verification = (array) $this->db->get_where($this->tableName, array('name =' => 'show_player_upload_realname_verification'))->row();

            if(!empty($show_player_upload_realname_verification)){
                $this->db->set('enabled', $show_upload_documents['enabled']);
                $this->db->where('name', 'show_player_upload_realname_verification');
                $this->db->update($this->tableName);
            }
            else{
                $this->db->insert($this->tableName, array(
                    "name" => "show_player_upload_realname_verification",
                    "type" => "kyc_riskscore",
                    "enabled" => $show_upload_documents['enabled'],
                ));
            }
        }
    }

    public function down() {

    }
}
