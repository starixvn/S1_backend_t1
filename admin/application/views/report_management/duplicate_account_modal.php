<style>
    @media screen and (min-width: 992px) {
        .modal-lg {
            max-width: 1450px; /* New width for large modal */
            width: 97%;
        }
        @-moz-document url-prefix() {
            .modal-lg {
                width: 970px; /* Firefox New width for large modal */
            }
        }
    }
</style>

<!-- Level Upgrade Setting -->
<div id="duplicateAccountModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= lang('Duplicate Account'); ?></h4>
            </div>
            <div class="modal-body custom-height-modal">
                <div id="logList" class="table-responsive">
                    <?php $dup_enabled_column = $this->utils->getConfig('duplicate_account_info_enalbed_condition') ?>
                    <table id="duplicateTableModal" class="table table-striped table-hover table-bordered"  width=100%>
                        <thead>
                            <tr>
                                <th> <?=lang('Username');?></th>
                                <th> <?=lang('Total Rate');?></th>
                                <th> <?=lang('Possibly Duplicate');?></th>
                                <?php if (in_array('ip', $dup_enabled_column)) : ?>
                                    <th><?= lang('Reg IP')				?></th>
                                    <th><?= lang('Login IP')			?></th>
                                    <th><?= lang('Deposit IP')			?></th>
                                    <th><?= lang('Withdraw IP')			?></th>
                                    <th><?= lang('Transfer Main To Sub IP')	?></th>
                                    <th><?= lang('Transfer Sub To Main IP')	?></th>
                                <?php endif; ?>

                                <?php if (in_array('realname', $dup_enabled_column)) : ?>
                                    <th><?= lang('Real Name') 			?></th>
                                <?php endif; ?>

                                <?php if (in_array('password', $dup_enabled_column)) : ?>
                                    <th><?= lang('Password') 			?></th>
                                <?php endif; ?>

                                <?php if (in_array('email', $dup_enabled_column)) : ?>
                                    <th><?= lang('Email') 				?></th>
                                <?php endif; ?>

                                <?php if (in_array('mobile', $dup_enabled_column)) : ?>
                                    <th><?= lang('Mobile') 				?></th>
                                <?php endif; ?>

                                <?php if (in_array('address', $dup_enabled_column)) : ?>
                                    <th><?= lang('Address') 			?></th>
                                <?php endif; ?>

                                <?php if (in_array('city', $dup_enabled_column)) : ?>
                                    <th><?= lang('City') 				?></th>
                                <?php endif; ?>

                                <?php if (in_array('country', $dup_enabled_column)) : ?>
                                    <th><?= lang('pay.country')			?></th>
                                <?php endif; ?>

                                <?php if (in_array('cookie', $dup_enabled_column)) : ?>
                                    <th><?= lang('Cookie') 				?></th>
                                <?php endif; ?>

                                <?php if (in_array('referrer', $dup_enabled_column)) : ?>
                                    <th><?= lang('From') 				?></th>
                                <?php endif; ?>

                                <?php if (in_array('device', $dup_enabled_column)) : ?>
                                    <th><?= lang('Device') 				?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('lang.close'); ?></button>
            </div>
        </div>
    </div>
</div>