<?php 
    $template_name = $this->utils->getPlayerCenterTemplate();
    $base_path = base_url() . $template_name;
    $arrWithMultiplePageRegistration = array('olobet');

    $standard_js=[
        $this->utils->thirdpartyUrl('jquery/jquery-3.1.1.min.js'),
        $this->utils->thirdpartyUrl('bootstrap/3.3.7/bootstrap.min.js'),
        // $this->utils->thirdpartyUrl('webshim/1.15.8/polyfiller.min.js'),
        $this->utils->thirdpartyUrl('bootstrap-datepicker/1.7.0/bootstrap-datepicker.min.js'),
        $this->utils->thirdpartyUrl('bootstrap-tour/0.10.3/bootstrap-tour.min.js'),
        $this->utils->thirdpartyUrl('jquery-wizard/0.0.7/jquery.wizard.min.js'),
        $this->utils->getPlayerCmsUrl('/common/js/main.js'),
        $this->utils->getPlayerCmsUrl($base_path.'/js/template-script.js'),
    ];

    $standard_css=[
        $this->utils->getPlayerCmsUrl($base_path.'/style.css'),
        $this->utils->getPlayerCmsUrl($this->utils->getActivePlayerCenterTheme()),
        $this->utils->getPlayerCmsUrl($base_path.'/css/font-awesome.min.css'),
        $this->utils->getPlayerCmsUrl($this->utils->getSystemUrl('www').'/css/custom-style.css'),
        $this->utils->thirdpartyUrl('bootstrap-datepicker/1.7.0/bootstrap-datepicker3.min.css'),
    ];



    // Load css
    foreach ($standard_css as $css_url) {
        echo '<link href="'.$css_url.'" rel="stylesheet"/>';
    }

    // Load js
    foreach ($standard_js as $js_url) {
        echo '<script type="text/javascript" src="'.$js_url.'"></script>';
    }
	
?>

<style type="text/css">
	body {
		padding-top: 0;
	}
</style>

<div style="display: relative; height: 100%; width: 100%; <?php if (strtolower($template_name) != 'olobet') { echo "overflow: auto"; } ?>">
	<?=$this->load->view($view)?>	
	<?php if (!in_array(strtolower($template_name), $arrWithMultiplePageRegistration)) : ?>
		<div style="background-color: transparent; top: 0px; right: 15px; position: absolute; width: 100%; height: 100%; z-index:9999;"></div>	
	<?php endif; ?>
</div>