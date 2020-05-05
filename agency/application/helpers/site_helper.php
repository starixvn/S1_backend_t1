<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

function get_site_name(){
    $CI =& get_instance();
    $CI->db->select('site_name');
    $result = $CI->db->get('static_sites');

    if($result->num_rows() > 0){
        return $result->result()[0]->site_name;
    }else{
        return 'null';
    }
}

function get_site_login_logo($site_name = 'default')
{
    $CI =& get_instance();
    $CI->db->select('logo_icon_filepath');
    $CI->db->where('site_name', $site_name);
    $result = $CI->db->get('static_sites');

    $path = false;
    if ($result->num_rows() > 0) {
        $logo_path=$result->row()->logo_icon_filepath;
        if(!empty($logo_path)){
            $path = site_url('/upload/' . $logo_path);
        }
    }
    return $path;
}

function get_site_navbar_logo(){
    $CI =& get_instance();
    $CI->db->select('logo_icon_horizontal_filepath');
    $CI->db->where('site_name', 'default');
    $result = $CI->db->get('static_sites');

    $path = false;
    if($result->num_rows() > 0){
        $logo_path = $result->row()->logo_icon_horizontal_filepath;
        if(!empty($logo_path))
            $path = site_url('/upload/' . $logo_path);{
        }
    }

    return $path;
}

function get_site_favicon($site_name = 'default'){
    $CI =& get_instance();
    $CI->db->select('fav_icon_filepath');
    $CI->db->where('site_name', $site_name);
    $result = $CI->db->get('static_sites');

    $path = FALSE;
    if($result->num_rows() > 0){
        $logo_path = $result->row()->fav_icon_filepath;
        if(!empty($logo_path))
            $path = site_url('/upload/' . $logo_path);{
        }
    }

    return $path;
}

?>