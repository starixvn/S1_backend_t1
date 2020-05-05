<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Cmsbanner_library
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 * @property BaseController $CI
 */
class Cmsbanner_library{
    const IMAGE_RELATIVE_PATH = '/shared_images/banner';

    protected $_allow_file_ext;
    protected $_categories;
    protected $_languages;
    /** @var $cmsbanner_model Cmsbanner_model */
    protected $cmsbanner_model;

    public function __construct(){
        $this->CI =& get_instance();
        $this->utils=$this->CI->utils;

        $this->CI->load->model(['cmsbanner_model']);

        $this->cmsbanner_model = $this->CI->cmsbanner_model;

        $this->_categories = [
            CMSBANNER_CATEGORY_HOME => lang('cms.home'),
            CMSBANNER_CATEGORY_MOBILE_HOME => lang('cms.mobileHome'),
        ];

        $this->_languages = [
            Language_function::INT_LANG_ENGLISH => lang('English'),
            Language_function::INT_LANG_CHINESE => lang('Chinese'),
        ];

        $this->_allow_file_ext = [
            'jpg',
            'jpeg',
            'gif',
            'png'
        ];
    }

    public function getUploadPath($filepath = NULL){
        $path=$this->CI->utils->getUploadPath() . self::IMAGE_RELATIVE_PATH;
        $this->CI->utils->addSuffixOnMDB($path);
        return $path . ((empty($filepath)) ? '' : '/' . $filepath);
    }

    public function getPublicPath($filepath = NULL){
        $path=$this->CI->utils->getConfig('PUBLIC_UPLOAD_PATH') . self::IMAGE_RELATIVE_PATH;
        $this->utils->addSuffixOnMDB($path);
        return  $path. ((empty($filepath)) ? '' : '/' . $filepath);
    }

    public function uploadBannerImage($field_name, $file_name = NULL){
        $file_name = (empty($file_name)) ? 'cmsbanner-' . $this->CI->utils->generateRandomCode() : $field_name;

        $config = [
            'allowed_types' => implode('|', $this->_allow_file_ext),
            'upload_path' => $this->getUploadPath(),
            'max_size' => $this->CI->utils->getMaxUploadSizeByte(),
            'overwrite' => TRUE,
            'file_name' => $file_name,
        ];

        $this->CI->load->library('upload', $config);
        $result = $this->CI->upload->do_upload($field_name);

        return ($result) ? $this->CI->upload->file_name : FALSE;
    }

    public function allowUploadFormat($ext){
        return (in_array(strtolower($ext), $this->_allow_file_ext)) ? TRUE : FALSE;
    }

    public function getCategories(){
        return $this->_categories;
    }

    public function getCategoryNameByKey($category_id){
        return (isset($this->_categories[$category_id])) ? $this->_categories[$category_id] : lang('lang.norecord');
    }

    public function getLanguages(){
        return $this->_languages;
    }

    public function getLanguageNameByKey($language_id){
        return (isset($this->_languages[$language_id])) ? $this->_languages[$language_id] : lang('lang.norecord');
    }

    protected function _processData($data){
        $data['category_name'] = $this->getCategoryNameByKey($data['category']);
        $data['language_name'] = $this->getLanguageNameByKey($data['language']);

        $data['banner_img_url'] = NULL;
        if(!empty($data['bannerName']) && file_exists($this->getUploadPath($data['bannerName']))){
            $data['banner_img_url'] = $this->getPublicPath($data['bannerName']);
        }

        return $data;
    }

    public function getAllCMSBanner(){
        $result = $this->cmsbanner_model->getAllCMSBanner();
        if(empty($result)){
            return FALSE;
        }

        foreach($result as &$data){
            $data = $this->_processData($data);
        }

        return $result;
    }

    public function getActiveCMSBannerByCategory($category_id){
        $result = $this->cmsbanner_model->getActiveCmsBannerByCategory($category_id);
        if(empty($result)){
            return FALSE;
        }

        foreach($result as &$data){
            $data = $this->_processData($data);
        }

        return $result;
    }

    public function addCmsBanner($data){
        return $this->cmsbanner_model->addCmsBanner($data);
    }

    public function editBannerCms($data, $bannerId){
        $cmsBannerItem = $this->cmsbanner_model->getBannerCmsDetails($bannerId);
        if(empty($cmsBannerItem)){
            return FALSE;
        }

        if(!isset($data['bannerName']) || empty($data['bannerName'])){
            $data['bannerName'] = $cmsBannerItem['bannerName'];
        }

        if(($data['bannerName'] !== $cmsBannerItem['bannerName']) && !empty($cmsBannerItem['bannerName']) && file_exists($this->getUploadPath($cmsBannerItem['bannerName']))){
            @unlink($this->getUploadPath($cmsBannerItem['bannerName']));
        }

        return $this->cmsbanner_model->editBannerCms($data, $bannerId);
    }

    public function getBannerCmsDetails($bannerId){
        $data = $this->cmsbanner_model->getBannerCmsDetails($bannerId);
        if(empty($data)){
            return FALSE;
        }

        return $this->_processData($data);
    }

    public function deleteBannerCms($bannerId){
        $cmsBannerItem = $this->cmsbanner_model->getBannerCmsDetails($bannerId);
        if(empty($cmsBannerItem)){
            return FALSE;
        }

        if(!empty($cmsBannerItem['bannerName']) && file_exists($this->getUploadPath($cmsBannerItem['bannerName']))){
            @unlink($this->getUploadPath($cmsBannerItem['bannerName']));
        }

        return $this->cmsbanner_model->deleteBannerCms($bannerId);
    }

    public function activateBannerCms($bannerId, $status){
        return $this->cmsbanner_model->activateBannerCms($bannerId, $status);
    }
}