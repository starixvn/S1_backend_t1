<?php
/**
 *   filename:   agent_information.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-25
 *   @brief:     view for agent Information
 */

$player_id =  $playerDetails['playerId'];
$reset_pass_url = site_url('agency/reset_player_password/' . $player_id);
$edit_url = site_url('agency/edit_player/' . $player_id);
?>

<div class="content-container">
	<br/>

	<!-- SIGNUP Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
                <h4>
                    <?=lang('lang.signupinfo');?>
                    <div class="pull-right">
                        <?php if ($agent['status'] == 'active' && $this->utils->isEnabledFeature('enable_create_player_in_agency')) {?>
                            <a href="<?=$edit_url?>" class="btn btn-sm btn-default">
                                <i class="glyphicon glyphicon-edit"></i> <?=lang('lang.edit');?>
                            </a>
                        <?php } ?>
                    </div>
                </h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="info_panel_body">
                <table class="table table-hover table-bordered" style="margin-bottom:0;">
                    <tbody>
                        <tr>
                            <th class="active col-md-2"><?=lang('reg.03');?></th>
                            <td class="col-md-4"><?=$player_signup_info['username'];?>
                                <span class="text-danger"><?php echo $player_signup_info['blocked']=='1' ? lang('Blocked') : '';?></span>
                            </td>
                            <th class="active col-md-2">
                                <span id="_password_label"><?=lang('Password');?></span>
                            </th>
                            <td>
                                <?php if ($this->utils->isEnabledFeature('enable_reset_player_password_in_agency') && $agent['status'] == 'active') { ?>
                                    <a href="<?=$reset_pass_url;?>" class="btn btn-xs btn-primary">
                                        <?=lang('lang.reset');?>
                                    </a>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <th class="active col-md-2"><?=lang('aff.al24');?></th>
                            <td class="col-md-4"><?=$player_signup_info['createdOn'];?></td>
                            <th class="active col-md-2"><?=lang('player.ui09');?></th>
                            <td class="col-md-4"><?=$player_signup_info['typeOfPlayer'];?></td>
                        </tr>
                        <tr>
                            <th class="active col-md-2"><?=lang('Note');?></th>
                            <td class="col-md-4">
                                <button type="button" class="btn btn-primary btn-sm" onclick="player_notes(<?=$player_id?>)">
                                 <i class="fa fa-sticky-note-o"></i> <?=lang('Details')?>
                                </button>
                            </td>
                            <th class="active col-md-2"><?=lang('aff.ai40');?></th>
                            <td class="col-md-4"><?=$player_signup_info['invitationCode'];?></td>
                        </tr>
                        <?php if($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency') && $this->utils->isEnabledRollingCommByAgentInSession()){?>
                        <tr>
                            <th class="active col-md-2"><?=lang('Rolling Comm');?></th>
                            <td class="col-md-4"><?=$player_signup_info['rolling_comm'];?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End of SIGNUP Information -->

      <div class="modal fade in" id="player_notes" tabindex="-1" role="dialog" aria-labelledby="label_player_notes">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                      <h4 class="modal-title" id="label_player_notes"></h4>
                  </div>
                  <div class="modal-body"></div>
                  <div class="modal-footer"></div>
              </div>
          </div>
      </div> <!--  modal for level name setting }}}4 -->

    <!-- Account Information -->
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><?=lang('aff.action.balInfo');?></h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="info_panel_body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: right;">
                                    <?=lang('aff.action.mainwallet')?>
                                </th>
                                <?php foreach ($game_platforms as $game_platform) {?>
                                    <th style="text-align: right;">
                                        <?=$game_platform['system_code']?> <?=lang('pay.walltbal')?>
                                    </th>
                                <?php } ?>
                                <th style="text-align: right;">
                                    <?=lang('pay.totalbal')?>
                                </th>
                                <th style="text-align: right;">
                                    <?=lang('Base Credit')?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="right" class="<?=$playerDetails['main'] ? '' : 'text-muted'?>">
                                    <?=number_format($playerDetails['main'], 2)?>
                                </td>
                                <?php foreach ($game_platforms as $game_platform) {?>
                                    <td align="right" class="<?=$playerDetails[strtolower($game_platform['system_code'])] ? '' : 'text-muted'?>">
                                        <a href="/agency/playerAction/3/<?=$player_id ?>" class="agent-oper" data-toggle="tooltip" title="<?=lang('Subwallet to main wallet')?>">
                                            <span class="fa fa-arrow-circle-up text-warning"></span>
                                        </a>
                                        <a href="/agency/playerAction/4/<?=$player_id ?>" class="agent-oper" data-toggle="tooltip" title="<?=lang('Main wallet to sub wallet')?>">
                                            <span class="fa fa-arrow-circle-down text-success"></span>
                                        </a>
                                        <?=number_format($playerDetails[strtolower($game_platform['system_code'])], 2)?>
                                    </td>
                                <?php } ?>
                                <td align="right" class="<?=$playerDetails['total'] ? '' : 'text-muted'?>">
                                    <strong><?=number_format($playerDetails['total'], 2)?></strong>
                                </td>
                                <td align="right" class="text-muted">
                                    (<?=number_format($base_credit, 2)?>)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Account Information -->

	<div class="row">
        <?php
            if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) {
                $this->load->view('includes/game_platform_settings_tier_comm', $game_platform_settings, TRUE);
            } else {
                $this->load->view('includes/game_platform_settings', $game_platform_settings, TRUE);
            }
        ?>
    </div>
</div>

<script type="text/javascript">
    function player_notes(player_id) {
        var dst_url = "/agency/player_notes/" + player_id;
        open_modal('player_notes', dst_url, "<?php echo lang('Player Notes'); ?>");
    }

    function add_player_notes(self, player_id) {
        var url = $(self).attr('action');
        var params = $(self).serializeArray();
        $.post(url, params, function(data) {
            if (data.success) {
                refresh_modal('player_notes', "/agency/player_notes/" + player_id, "<?php echo lang('Player Notes'); ?>");
            }
        });
        return false;
    }

    function remove_player_note(note_id, player_id) {
        var confirm_val = confirm("<?php echo lang('Are you sure you want to delete this player note?');?>");
        if (confirm_val) {
            var url = '/agency/remove_player_note/' + note_id;
            // console.log(url);
            $.getJSON(url, function(data) {
                if (data.success) {
                    refresh_modal('player_notes', "/agency/player_notes/" + player_id,
                        "<?php echo lang('Player Notes'); ?>");
                }
            });
        }
        return false;
    }

    function open_modal(name, dst_url, title) {
        var main_selector = '#' + name;

        var label_selector = '#label_' + name;
        $(label_selector).html(title);

        var body_selector = main_selector + ' .modal-body';
        var target = $(body_selector);
        target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);

        $(main_selector).modal('show');
    }

    function refresh_modal(name, dst_url, title) {
        var main_selector = '#' + name;
        var body_selector = main_selector + ' .modal-body';
        var target = $(body_selector);
        target.load(dst_url);
    }
</script>
