<?php
require_once 'PlayerCenterBaseController.php';

/**
 * Lists promotion
 */
class Promotion extends PlayerCenterBaseController {
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->preloadPromotionVars();
    }

    private function preloadPromotionVars() {
        $playerId = $this->load->get_var('playerId');

        $allpromo = $this->utils->getPlayerPromo("allpromo", $playerId);
        //unset the promo that not equivalent to promo rules language
        if(!empty($allpromo)){
            if(isset($allpromo['promo_list'])) {
                foreach ($allpromo['promo_list'] as $allpromokey => $allpromovalue) {
                    if(isset($allpromovalue['promorule'])){
                        if(!empty($allpromovalue['promorule'])){
                            if(isset($allpromovalue['promorule']['language'])){
                                if($allpromovalue['promorule']['language']){
                                    //var_dump($this->language_function->getCurrentLanguage());die();
                                    if($allpromovalue['promorule']['language'] != $this->language_function->getCurrentLanguage()){

                                        unset($allpromo['promo_list'][$allpromokey]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //echo "<pre>";print_r($allpromo);die();
        $subwallet=null;
        $success=$this->wallet_model->lockAndTransForPlayerBalance($playerId, function () use (
            $playerId, &$subwallet) {

            $subwallet = $this->wallet_model->getAllPlayerAccountByPlayerId($playerId);
            return !empty($subwallet);
        });

        $promoCategoryList = $this->utils->getAllPromoType();
        if($this->utils->isEnabledFeature('show_promotion_view_all')){
            $view_all_category_entry = [
                'id' => 0,
                'name' => lang('View All')
            ];
            array_unshift($promoCategoryList, $view_all_category_entry);

            @reset($promoCategoryList);
            $default_show_category_id = 0;
        }else{
            @reset($promoCategoryList);
            $default_show_category_id = (empty($promoCategoryList)) ? 0 : current($promoCategoryList)['id'];
        }

        $this->load->vars('mypromo', $this->utils->getPlayerPromo("mypromo", $playerId));
        $this->load->vars('allpromo', $allpromo);
        $this->load->vars('promo_list', (isset($allpromo['promo_list'])) ? $allpromo['promo_list'] : []);
        $this->load->vars('promoCategoryList', $promoCategoryList);
        $this->load->vars(['default_show_category_id' => $default_show_category_id]);
        $this->load->vars('currency', $this->utils->getCurrentCurrency());
    }

    public function index() {
        # Templates
        $this->loadTemplate();

        # Custom
        $this->template->add_function_js('/common/js/player_center/promotions.js');

        # Template-related variables
        $data['activeNav'] = 'promotions';
        $data['content_template'] = 'default_with_menu.php';

        # Render
        $this->template->append_function_title(lang('cms.mobile.promoReqAppList'));
        $this->template->write_view('main_content', $this->utils->getPlayerCenterTemplate() . '/promotion/promotion', $data);
        $this->template->render();
    }
}
