<?php

/**
 * Class Widget_Banner
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 * @property Cmsbanner_library $cmsbanner_library
 * @property Language_function $language_function
 */
class Widget_Banner extends MY_Widget {
    protected $_default_options = [
        'category' => NULL,
        'indicators' => TRUE,
        'interval' => 5000
    ];

    public function initialize($options = []){
        $this->load->library(['cmsbanner_library']);

        $this->_data['options'] = $this->_options = $options = array_replace_recursive($this->_default_options, $options);

        $language = $this->language_function->getCurrentLanguage();

        if(empty($options['category'])){
            $banner_list = $this->cmsbanner_library->getAllCMSBanner();
        }else{
            $banner_list = $this->cmsbanner_library->getActiveCMSBannerByCategory($options['category']);
        }

        $banner_list = (empty($banner_list)) ? [] : $banner_list;

        $banner_list = array_filter($banner_list, function($data) use($language){
            return ($data['language'] == $language);
        });

        $this->_data['banner_list'] = $banner_list;
    }
}