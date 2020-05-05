<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_columns_to_cmsnews_20171024 extends CI_Migration {

	private $tableName = 'cmsnews';

	public function up() {

        $fields = array(
            'categoryId' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            )
        );
        if (!$this->db->field_exists('categoryId', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);
        }

        #create default record
        $newsLangList = $this->getAllNewsLang();
        $this->createDefaultNewsCategoryRecord($newsLangList);
        $this->updateNewsCategoryId($newsLangList);
        

        $this->db->get('cmsnews');
	}

	public function down() {
        if (!$this->db->field_exists('categoryId', $this->tableName)) {
		    $this->dbforge->drop_column($this->tableName, 'categoryId');
        }
	}

    private function getAllNewsLang() {
        $this->db->select("language");
        $this->db->group_by("language"); 
        $qry = $this->db->get('cmsnews');
        $res = $qry->result_array();

        return $res;
    }

    private function createDefaultNewsCategoryRecord(&$newsLangList)
    {
        $_newLangList = [];

        foreach ($newsLangList as $list) {
            $this->db->insert('cmsnewscategory', [
                'name' => "default " . $list['language'] . " category",
                'language' => $list['language']
            ]);

            $id = $this->db->insert_id();
            $_newLangList[$id] = $list;
        }

        $newsLangList = $_newLangList;
    }

    private function updateNewsCategoryId($newsLangList)
    {
        foreach ($newsLangList as $id => $list) {
            $this->db->where('language', $list['language']);
            $this->db->update($this->tableName, [
                'categoryId' => $id
            ]);
        }
    }
}

///END OF FILE//////////