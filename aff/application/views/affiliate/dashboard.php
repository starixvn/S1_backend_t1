<style>
    .modal {
        text-align: center;
        padding: 0!important;
    }
    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
    }
    .modal-dialog {
        display: inline-block;
    }

    .modal-dialog {
        text-align: left;
        vertical-align: middle;
    }
</style>

<div class="container">
    <div class="row">
        <?php if($this->utils->isEnabledFeature('enable_move_up_dashboard_statistic_in_affiliate_backoffice')) {
            if( !$this->utils->isEnabledFeature('enable_new_dashboard_statistics') ){
                $this->load->view('affiliate/dashboard_stats');
            }else{
                $this->load->view('affiliate/dashboard_stats_new');
            }
        } else {
            echo '<div class="clearfix"></div>';
        }?>

        <?php if (!empty($affiliate['trackingCode'])) {?>
            <?php if($this->utils->getConfig('editable_tracking_code_on_aff')){ ?>
            <form action="<?=site_url('affiliate/createCode/' . $affiliate['affiliateId'])?>" method="POST" class="form-horizontal">
                <div class="col-md-6 col-md-offset-3">
                    <div class="form-group col-xs-5">
                        <label for="tracking_code" class="control-label" style="text-align:right;"><?=lang('aff.ai40'); //lang('aff.ai37');?> </label>
                        <div>
                            <input type="text" name="tracking_code" id="tracking_code" class="form-control <?=$this->utils->isEnabledFeature('affiliate_tracking_code_numbers_only') ? 'number_only' : ''?>" minlength="5" maxlength="8" value="<?=(empty($affiliate['trackingCode'])) ? 'n/a' : $affiliate['trackingCode']?>"/>
                            <?php echo form_error('tracking_code', '<span style="color:#CB3838;">'); ?>
                        </div>
                    </div>
                    <div class="btn-group col-xs-7" role="group" aria-label="..." style="margin-top: 27px;">
                        <a href="#randomCode" class="btn btn-info hidden-xs" id="random_code" onclick="randomCode('8');"/> <?=lang('aff.ai38');?> </a>
                        <input type="submit" class="btn btn-primary" value="<?=lang('aff.ai39');?>"/>
                    </div>
                </div>
                <div class="clearfix"></div>
                <script>
                function randomCode(len)
                {
                    var text = '';

                    <?php if ($this->utils->isEnabledFeature('affiliate_tracking_code_numbers_only')): ?>
                        var charset = "1234567890";
                    <?php else: ?>
                        var charset = "abcdefghijklmnopqrstuvwxyz0123456789";
                    <?php endif ?>

                    for( var i=0; i < len; i++ ) {
                        text += charset.charAt(Math.floor(Math.random() * charset.length));
                    }

                    $('#tracking_code').val(text);
                }
                </script>
            </form>
            <?php }else{?>
            <!-- <div  col-md-offset-3"> -->
            <div class="col-md-6" style="">
                <label class="control-label" style="text-align:right;margin-top: 1%;background-color: #9a9a9a;color:#000;"><?php echo lang('aff.ai40'); ?> <?php echo (empty($affiliate['trackingCode'])) ? 'n/a' : $affiliate['trackingCode']; ?></label>
            </div><br>
            <!-- </div> -->
            <?php }?>
        <div class="col-md-12">
            <table class="table table-striped table-hover" id="trackingTable" style="width:100%;">
                <thead>
                    <th></th>
                    <th class="input-sm">#</th>
                    <th class="input-sm"><?=lang('mod.url');?></th>
                    <th class="input-sm"></th>
                    <th class="input-sm"><?=lang('mod.updateDate');?></th>
                    <th class="input-sm"><?=lang('lang.status');?></th>
                </thead>

                <tbody>
                    <?php
$cnt = 0;

foreach ($domain as $key => $domain_value) {
$cnt++;
?>
                            <tr>
                                <td></td>
                                <td class="input-sm"><?=$cnt?></td>
                                <td class="input-sm"><?=$domain_value['domainName'] . '/aff/' . $affiliate['trackingCode']?></td>
                                <td class="input-sm"><?=$domain_value['domainName'] . '/aff.html?code=' . $affiliate['trackingCode']?></td>
                                <!--td class="input-sm"><?=$domain_value['domainName'] . $player_register_uri . '?aff=' . $affiliate['trackingCode']?></td-->
                                <td class="input-sm"><?=$domain_value['updatedOn']?></td>
                                <td class="input-sm"><?=($domain_value['status'] == 0) ? lang('lang.active') : lang('Blocked')?></td>
                            </tr>
                    <?php
}
?>
                </tbody>
            </table>
        </div>
            <?php if($this->utils->isEnabledFeature("affiliate_source_code")){?>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-hover">
                        <thead>
                            <th><?php echo lang('Affiliate Source Code');?></th>
							<th><?php echo lang('Link Example');?></th>
                        </thead>
                        <tbody>
                        <?php foreach($aff_source_code_list as $source_code){?>
                            <tr>
                                <td><?php echo $source_code['tracking_source_code']; ?></td>
                                <td><?php echo !empty($first_domain) ? $first_domain.'/aff/'.$affiliate['trackingCode'].'/'.$source_code['tracking_source_code'] : ""; ?><br>
                                <?php echo !empty($first_domain) ? $first_domain.'/aff.html?code='.$affiliate['trackingCode'].'&source='.$source_code['tracking_source_code'] : ""; ?></td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php }?>
        <?php }?>

        <div class="col-md-12 well" style="overflow: auto;">
            <table class="table table-striped">
                <thead>
                    <th><?=lang('lang.affdomain');?></th>
                </thead>
                <tbody>
                    <tr>
                        <td><?=empty($affiliate['affdomain']) ? lang('lang.norecyet') : $affiliate['affdomain'];?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php if($this->utils->isEnabledFeature('affiliate_additional_domain')){?>
        <div class="col-md-12 well" style="overflow: auto;">
            <table class="table table-striped">
                <thead>
                    <th><?php echo lang('Affiliate Additional Domain');?></th>
                </thead>
                <tbody>
                <?php
                if($aff_additional_domain_list){
                    foreach($aff_additional_domain_list as $add_domain){?>
                        <tr>
                            <td><a href="http://<?php echo $add_domain['tracking_domain']; ?>" target='_blank'><?php echo $add_domain['tracking_domain']; ?></a></td>
                        </tr>
                    <?php
                    }
                }else{
                ?>
                    <tr><td><?php echo lang('N/A');?></td></tr>
                <?php }?>
                </tbody>
            </table>
        </div>
        <?php }?>

        <?php if( ($commonSettings['manual_open'] || $commonSettings['sub_link']) && !empty($sublink) && !empty($affiliate['trackingCode'])){ ?>
        <div class="col-md-12 well" style="overflow: auto;">
            <table class="table table-striped">
                <thead>
                    <th><?=lang('aff.asb11');?></th>
                </thead>

                <tbody>
                    <tr>
                        <td><?=$sublink;?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php }?>
    </div>

    <?php if( !$this->utils->isEnabledFeature('enable_move_up_dashboard_statistic_in_affiliate_backoffice') ) {
        if( !$this->utils->isEnabledFeature('enable_new_dashboard_statistics') ){
            $this->load->view('affiliate/dashboard_stats');
        }else{
            $this->load->view('affiliate/dashboard_stats_new');
        }
    }else{
        echo '<div class="clearfix"></div>';
    } ?>

</div>

<div class="modal fade" id="popup-notify-register-bankcard" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= lang('Notifications') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('Hi honor affiliate, thanks for your login! Kindly bind your bank account for withdrawal!') ?></p>
            </div>
            <div class="modal-footer">
                <a href="/affiliate/modifyAccount#back-info" class="btn btn-primary"><?= lang('Bind the Bank Account') ?></a>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){

    $('#trackingTable').DataTable({
        "columnDefs": [ {
            className: 'control',
            orderable: false,
            targets:   0
        } ],
        "order": [ 1, 'asc' ]
    } );

    let backcard_exists = <?= $bank_card_exists ?>,
        is_login_behavior = <?= $is_login_behavior ?>

    if (backcard_exists == 0 && is_login_behavior == 1) {
        $("#popup-notify-register-bankcard").modal('show')
    }
});

</script>