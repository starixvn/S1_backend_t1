<?php
	$user_theme = !empty($this->session->userdata('admin_theme')) ? $this->session->userdata('admin_theme') : $this->config->item('sbe_default_theme');
    if( ! empty( $record ) ){

        foreach ($record as $key => $value) {
?>
            <li>
               <a href="/payment_management/deposit_list/?dwStatus=approvedAll">
	                <span style="<?php echo ($user_theme == 'cerulean' OR $user_theme == 'united') ? 'color:white;' : '' ?>"><?=$value['username']?> - <?=lang($value['payment_type_name'])?></span>
	            </a>
            </li>
<?php
        }

    }else{
?>
        <li><a href="javascript:void(0)"><?=lang('Empty')?></a></li>
<?php
    }
?>
