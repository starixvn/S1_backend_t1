
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-coin-dollar"></i> <?=lang('payment.massBonusAdd');?>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div class="well">
                    <form class="form-horizontal" id="form_add" action="<?=site_url('payment_account_management/add_edit_payment_account')?>" method="POST" role="form" enctype="multipart/form-data">
                        <div class="form-group" style="margin-left:0px;margin-right: 0px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <fieldset style="padding:10px;">
                                        <legend><h4><strong><?=lang('pay.player_level');?></strong></h4></legend>
                                        <div class="tree">
                                                                <ul id="treeMBA">
                                                                    <?php //var_dump($playerLvl);
foreach ($memberGroup as $memberGroup) {
	//$gp_status = in_array($gamePro['gameId'], $vipSettingGameProvider) ? "checked" : "";
	?>
                                                                        <li><a style='text-decoration:none;'><input type="checkbox" class="nonDepositPromoGameProviderAGT" <?php //$gp_status?> /><?=strtoupper($memberGroup['groupName'])?></a>
                                                                            <ul>
                                                                            <?php
foreach ($playerLvl as $key) {
		//$pt_gt_status = in_array($key['game_type_id'], $vipsettingGamesType) ? "checked" : "";
		if ($memberGroup['vipSettingId'] == $key['vipSettingId']) {
			?>
                                                                                        <li>
                                                                                            <a style='text-decoration:none;'>
                                                                                                <input type="checkbox" <?php //$pt_gt_status?>  name="member_level[]" value="<?=$key['vipsettingcashbackruleId'];?>" />
                                                                                                <?=$key['vipLevelName'];?>
                                                                                            </a>
                                                                                        </li>
                                                                                <?php }
	}
	?>
                                                                            </ul>
                                                                        </li>
                                                                        <?php }
?>
                                                                </ul>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-9">
                                    <fieldset style="padding:10px;">
                                        <legend><h4><strong><?=lang('aff.as19');?></strong></h4></legend>
                                        <select name="member_list" id="player_list" multiple="multiple">
                                            <?php
foreach ($players as $key) {?>
                                                    <option value="<?=$key['playerId']?>"><?=$key['username']?></option>
                                            <?php }
?>
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <fieldset style="padding:10px;">
                                        <legend><h4><strong><?=lang('aff.as19');?></strong></h4></legend>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- <input type="text" value="test" id="test"> -->
    <div class="row">
        <div class="col-md-6">


        </div>
    </div>
</div>

<script type="text/javascript" src="<?=$this->utils->jsUrl('jquery-checktree.js')?>"></script>
<script>
    $('#tree').checktree();
    $('#treeMBA').checktree();

    $( document ).ready( function( ) {
        $( '.tree li' ).each( function() {
            if( $( this ).children( 'ul' ).length > 0 ) {
                    $( this ).addClass( 'parent' );
            }
        });

        $( '.tree li.parent > a' ).click( function( ) {
            $( this ).parent().toggleClass( 'active' );
            $( this ).parent().children( 'ul' ).slideToggle( 'fast' );
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#player_list').multiselect({
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            // optionLabel: function(element) {
            //     return $(element).html() + '(' + $('#test').val() + ')';
            // }
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Click here to select player which you want to give bonus.';
                }
                // else if (options.length > 3) {
                //     return 'More than 3 options selected!';
                // }
                 else {
                     var labels = [];
                     options.each(function() {
                         if ($(this).attr('label') !== undefined) {
                             labels.push($(this).attr('label'));
                         }
                         else {
                             labels.push($(this).html());
                         }
                     });
                     return labels.join(', ') + '';
                 }
            }
        });

    });
</script>