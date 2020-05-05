<?php
/**
 * Created by Danrem P.
 * Date: 1/22/2018
 * Time: 8:13 PM
 */

class banner_promotion extends BaseModel {

    public function insertNewRecord($data){
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        $filter = array('bp.id' => $insert_id);
        $this->getDataList($filter);
    }

    public function getDataList($filter = null){
        $this->db
            ->select('bp.id, bp.title, bp.section_type_id, st.name as category,
            bp.img_url, bp.header_img_name, bp.content,
            bp.game_type, bp.content_img_name, bp.href_link, bp.visible, bp.updated_at,
            bp.expiry_date, au.username as modified_by')
            ->from($this->table. ' bp')
            ->join('section_type_ss st', 'st.id = bp.section_type_id', 'left')
            ->join('adminusers au', 'au.userId = bp.modified_by')
            ->where('bp.deleted_at IS NULL');
        if($filter){
            $this->db->where($filter);
        }
        $this->utils->debug_log('the query ----->', $this->db->_compile_select());
        $qq = $this->db->get();
        return $qq->result_array();
    }

    public function updateExistingData($id, $data){
        //$this->db->replace($this->table, $data);
        $this->db->set($data)
            ->where('id', $id)
            ->update($this->table);
    }

    public function destroyData($id){
        $data = array('deleted_at' => date("Y-m-d H:i:s"));
        $this->db->set($data)
            ->where('id', $id)
            ->update($this->table);
    }

}