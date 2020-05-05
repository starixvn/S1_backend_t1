<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/third_party/jquery-postmessage/jquery.ba-postmessage.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/agency/agency.js')?>"></script>
<?php if($this->authentication->isLoggedIn() && !(empty($agency_agent = $this->CI->load->get_var('player_binding_agency_agent')))): ?>
    <script type="text/javascript">
        (function() {
            var agency_auto_logon = '<?=($this->utils->isEnabledFeature('enable_agency_auto_logon_on_player_center')) ? '1' : '0'?>';
            var player_token = '<?=$this->authentication->getPlayerToken()?>';
            t1t_agency.init({
                'base_url': '<?=$this->utils->getSystemUrl('agency')?>',
                'auto_logon': !!(agency_auto_logon),
                'player_token': player_token
            });
        })();
    </script>
<?php else: ?>
    <script type="text/javascript">
        (function() {
            t1t_agency.init({
                'base_url': '<?=$this->utils->getSystemUrl('agency')?>',
                'auto_logon': false,
                'player_token': null
            });
            t1t_agency.logout();
        })();
    </script>
<?php endif ?>