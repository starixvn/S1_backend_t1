<?php
/**
 *   filename:   agent_binding_player.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2017-11-21
 *   @brief:     bind a player for current agent
 */
$adjustUrl = '/' . $controller_name . '/adjustBindingPlayer/' . $agent_id;
?>
<th class="active"><span id="_under_agent"><?=lang('Binding Player')?></span></th>
<td>
    <span id="binding-player">
        <?=(!isset($binding_player) || empty($binding_player)) ? lang('lang.norecord') : $binding_player?>
    </span>
    <?php if ($controller_name == 'agency_management' && $this->permissions->checkPermissions('agent_binding_player')) {?>
    <a href="#" id="bind-player-button" class="btn btn-xs btn-danger pull-right"
        onclick="modal('<?=$adjustUrl?>','<?=lang('Binding Player')?>')">
        <?=lang('Bind PLayer')?>
    </a>
    <?php }?>
</td>
<div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mainModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<script>
function modal(load, title) {
    var target = $('#mainModal .modal-body');
    $('#mainModalLabel').html(title);
    target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
    $('#mainModal').modal('show');
}

</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_binding_player.php
