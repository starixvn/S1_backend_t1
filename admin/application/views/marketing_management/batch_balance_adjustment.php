<div class="container-fluid">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <?php echo lang('Batch Balance Adjustment'); ?>
                <a href="#" onclick="history.back()" class="close">&times;</a>
            </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading0">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                        <?=lang('pay.mainwalltbal')?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse0" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading0">
                                <div class="list-group">
                                    <a href="/marketing_management/manually_batch_bonus" title="<?=lang('transaction.transaction.type.' . Transactions::ADD_BONUS)?>" class="list-group-item"><i class="fa fa-star"></i> <?=lang('Batch Add Bonus')?></a>
    <!--                                <a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/--><?//=implode('/', array(0, Transactions::BATCH_ADD_BONUS))?><!--" title="--><?//=lang('transaction.transaction.type.' . Transactions::BATCH_ADD_BONUS)?><!--" class="list-group-item"><i class="fa fa-plus"></i> --><?//=lang('Batch Add Bonus')?><!--</a>-->
                                    <a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::MANUAL_ADD_BALANCE))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE)?>" class="list-group-item"><i class="fa fa-plus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE)?></a>
                                    <a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/<?=implode('/', array(0, Transactions::MANUAL_SUBTRACT_BALANCE))?>" title="<?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE)?>" class="list-group-item"><i class="fa fa-minus"></i> <?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE)?></a>
                                <?php if(!$this->utils->isEnabledFeature('close_cashback')): ?>
                                <!--  <a data-target="#balance-adjustment-form" href="/payment_management/adjust_balance_form/ --><?//=implode('/', array(0, Transactions::AUTO_ADD_CASHBACK_TO_BALANCE))?><!--  "title=" --><?//=lang('transaction.transaction.type.' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE)?> <!-- "class="list-group-item"><i class="fa fa-plus"></i>  --><?//=lang('transaction.transaction.type.30')?><!-- </a> -->
                                <a href="/marketing_management/manually_batch_add_cashback_bonus" title="<?=lang('transaction.transaction.type.' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE)?>" class="list-group-item"><i class="fa fa-plus"></i> <?=lang('transaction.transaction.type.30')?></a>
                                <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" id="balance-adjustment-form"></div>

            </div>

        </div>
        <div class="panel-footer"></div>
    </div>
</div> <!-- EOF .container-fluid -->

<style type="text/css">
.extra-tag {
    background-color: #e4e4e4;
    color: #222 !important;
    border: 1px solid #aaa !important;
    border-radius: 3px;
    margin: 2px;
    padding: 1px;
    line-height: 2em;
    padding-right: 4px;
    padding-left: 4px;
}
</style>

<script type="text/javascript">
    $(function() {

        $('a[data-target]').click( function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var url = $(this).attr('href');
            $(target).load(url);
        });

    });
    $(document).ready(function(){
        $('#view_batch_balance_adjustment').addClass('active');
        $(".list-group a").on('click', function(){
            if($('#note-footer').length < 1){
                 $( ".panel-footer" ).append('<section id="note-footer" style="color:red" class="five"><?=lang('Note: Upload file format must be CSV')?></section>');
            }
        });
    });



</script>