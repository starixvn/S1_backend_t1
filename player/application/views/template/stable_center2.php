<?php
/**
 * this template will serve as the short hand call
 * meaning we separated the template both mobile and
 * web to avoid redandant styles or js files
 * and to avoid overlapping styles.
 *
 * Note: only to this player center this style is appliead since they
 * have choosen a separate design for the mobile version instead of
 * using responsive, and to avoid also changing the code in the backend.
 */

$mobile = $this->utils->is_mobile();

$template_name = $this->utils->getPlayerCenterTemplate(false);

$ext_templates = ( $mobile ) ? 'mobile_' : '';

// from $this->load->get_var('force_ext_template');
if(!empty($force_ext_template)){
    $ext_templates = $force_ext_template . '_';
}

$template_name = $template_name . '/' . $ext_templates . 'template';

// $this->utils->debug_log("mobile", $mobile, 'template', $template_name, 'ext_templates', $ext_templates;

$this->load->view( $template_name , ['template_settings'=>$this->utils->getConfig('template_settings')]);