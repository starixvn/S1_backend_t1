<form id="depositCountForm" method="post" class="form-inline">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> <?= lang('Deposit Count Setting'); ?></h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6" >
                    <b><?= lang('Deposit Count Setting'); ?></b>

                    <label class="radio-inline"><input type="radio" name="deposit_count[]" value="1"><?= lang('This Week'); ?></label>
                    <label class="radio-inline"><input type="radio" name="deposit_count[]" value="2"><?= lang('This Month'); ?></label>
                    <label class="radio-inline"><input type="radio" name="deposit_count[]" value="3"><?= lang('This Year'); ?></label>
                    <label class="radio-inline"><input type="radio" name="deposit_count[]" value="4"><?= lang('Total'); ?></label>

                    <button id="saveBtn" type="button" class="btn btn-primary"><i class=""></i> <?= lang('lang.save') ?></button>
                </div>
            </div
        </div>
    </div>
</form>

<script type="text/javascript">

    var baseUrl = '<?php echo base_url(); ?>';
    var count = '<?php echo $count; ?>';
    var message = {
        save        : '<?= lang('lang.save'); ?>',
        saving      : '<?= lang('Saving'); ?>',
        success     : '<?= lang('Successfully Update Deposit Count Setting'); ?>'
    };
    var $saveBtn = $('#saveBtn');

    $(document).ready(function () {

        initializeActiveClass();
        initializeDepositSetting(count);

        $saveBtn.on('click', function(){
            saveDepositCount();
        });
    });

    function saveDepositCount() {
        clearNotify();
        $saveBtn.html('<i class="fa fa-refresh fa-spin"></i> ' + message.saving );
        setTimeout( function(){
            $.post( baseUrl + 'payment_management/saveDepositCountList', $('#depositCountForm').serialize(), function(){
                $saveBtn.html(message.save);
                $.notify( message.success ,{type: 'success'});
            });
        }, 500);
    }

    function initializeDepositSetting(count) {
        $(":radio[value="+count+"]").prop('checked', true);
    }

    function clearNotify() {
        $.notifyClose('all');
    }

    function initializeActiveClass() {
        $('#collapseSubmenu').addClass('in');
        $('#view_payment_settings').addClass('active');
        $('#depositCountSetting').addClass('active');
    }
</script>