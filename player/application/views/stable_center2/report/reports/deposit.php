<!-- ============ Deposit ================== -->
<div id="ah-deposit" role="tabpanel" class="tab-pane">
    <div id="deposit-box" class="report table-responsive">
        <table id="depositResultTable" width="100%" class="table table-striped table-hover dt-responsive display nowrap"></table>
    </div>
</div>
<?php if($this->utils->isEnabledFeature('enable_upload_deposit_receipt')): ?>
<!-- Upload Deposit Receipt Modal -->
<div class="modal fade" id="upload_deposit_receipt_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="POST" action="<?=site_url('player_center2/report/uploadDepositReceiptImage')?>" id="upload_deposit_receipt_form"  role="form" class="form-horizontal" accept-charset="utf-8" enctype="multipart/form-data" onsubmit="return validateFile();">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo lang("Upload").' '.lang('Invoice') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="hidden" name="deposit_order_id" value="">
                            <div id="viewExistReceiptImage"></div>
                            <div class="panel panel-default upload_receipt receipt1 hidden">
                                <div class="panel-body">
                                    <?=lang("File must not exceed 2MB.")?>
                                    <img id="viewUploadReceiptImage1">
                                </div>
                                <div class="panel-footer">
                                    <label class="btn btn-default btn-file browse-btn">
                                        <?= lang('Browse') ?>
                                        <input type="file" class="form-control input-sm btn-default" id="uploadDepositReceipt1" name="uploadDepositReceipt1[]" accept="image/jpg,image/jpeg,image/png,image/gif" onchange="uploadDepositReceiptImage(this,'viewUploadReceiptImage1')">
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-default upload_receipt receipt2 hidden">
                                <div class="panel-body">
                                    <?=lang("File must not exceed 2MB.")?>
                                    <img id="viewUploadReceiptImage2">
                                </div>
                                <div class="panel-footer">
                                    <label class="btn btn-default btn-file browse-btn">
                                        <?= lang('Browse') ?>
                                        <input type="file" class="form-control input-sm btn-default" id="uploadDepositReceipt2" name="uploadDepositReceipt2[]" accept="image/jpg,image/jpeg,image/png,image/gif" onchange="uploadDepositReceiptImage(this,'viewUploadReceiptImage2')">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span id="errfm_txtImage" class="text-danger"></span>
                    <button type="submit" class="btn btn-default submit-btn"><?=lang('lang.submit');?></button>
                    <button type="button" class="btn btn-default close-btn" id="closeModal" data-dismiss="modal"><?=lang('Close');?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif;?>
<script type="text/javascript">
    var depositTB;
    var modal;
    var HAS_ONE_FILE = 1;
    var HAVE_TWO_FILES = 2;
    var ALLOWED_UPLOAD_FILE = '<?=$this->config->item('allowed_upload_file')?>';
    var LANG_UPLOAD_FILE_ERRMSG = "<?= sprintf(lang('upload image limit and format'),$this->utils->getMaxUploadSizeByte()/1000000,$this->config->item('allowed_upload_file')) ?>";
    var LANG_UPLOAD_IMAGE_MAX_SIZE = '<?=$this->utils->getMaxUploadSizeByte()?>';


    function depositHistory() {
        var table_container = $('#depositResultTable');

        if(depositTB !== undefined){
            depositTB.page.len($('#pageLength').val());
            depositTB.ajax.reload();
            return false;
        }

        var columns = [];

        columns.push({
            "name": "secure_id",
            "title": "<?=lang('pay.sale_order_id')?>",
            "data": 0,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "created_at",
            "title": "<?=lang('pay.reqtime')?>",
            "data": 1,
            "visible": true,
            "orderable": true
        });
        columns.push({
            "name": "payment_flag",
            "title": "<?=lang('Type')?>",
            "data": 2,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "status",
            "title": "<?=lang('Status')?>",
            "data": 3,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "amount",
            "title": "<?=lang('Amount')?>",
            "data": 4,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "content",
            "title": "<?=lang('Player Deposit Note')?>",
            "data": 5,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "content",
            "title": "<?=lang('Notes')?>",
            "data": 6,
            "visible": true,
            "orderable": false
        });
        <?php if($this->utils->isEnabledFeature('enable_upload_deposit_receipt')): ?>
        columns.push({
            "name": "receipt",
            "title": "<?=lang('Invoice')?>",
            "data": 7,
            "visible": true,
            "orderable": false,
            "render": function(data, type, row){
                var pending = '<?=lang('Pending');?>';
                var payment_flag = row[2];
                var status = row[3];

                switch (payment_flag){
                    case '<?=lang('pay.local_bank_offline')?>':
                    case '<?=lang('pay.manual_online_payment')?>':
                        if(status == pending){
                            if(data){
                                return '<button class="btn btn-default btneditUploadDepositReceiptModal" onclick="editUploadDespositReceiptModal(\'' + row[7] + '\',\'' + JSON.parse(data) +'\');" data-toggle="modal"><?=lang('tool.01')?></button>';
                            }else{
                                return '<button class="btn btn-default viewUploadDepositReceiptModal" onclick="showUploadDespositReceiptModal(\'' + row[7] + '\');" data-toggle="modal"><?=lang('Upload')?></button>';
                            }
                        }else{
                            return '<button class="btn btn-default" ><?=lang('N/A')?></button>';
                        }
                        break;
                    default:
                        return '<button class="btn btn-default" ><?=lang('N/A')?></button>';
                        break;
                }

            }
        });
        <?php endif; ?>
        columns.push({ // for the responsive extention to display control row button
            "title": "&nbsp",
            "data": 0,
            "visible": true,
            "orderable": false,
            "render": function(){
                return '&nbsp';
            },
            "responsivePriority": 1
        });

        depositTB = table_container.DataTable($.extend({}, dataTable_options, {
            "pageLength": $('#pageLength').val(),
            "columns":  columns,
            columnDefs: [ {  // for the responsive extention to display control row button
                className: 'control',
                orderable: false,
                targets:   -1
            }],
            order: [[1, 'desc']],
            ajax: {
                url: '/api/DepositWalletTransaction',
                type: 'post',
                data: function ( d ) {
                    d.extra_search = [
                        {
                            'name':'dateRangeValueStart',
                            'value':$('#sdate').val(),
                        },
                        {
                            'name':'dateRangeValueEnd',
                            'value': $('#edate').val(),
                        },
                    ];
                },
            }
        }));
    }
    <?php if($this->utils->isEnabledFeature('enable_upload_deposit_receipt')): ?>
    function showUploadDespositReceiptModal(id){
        var deposit_order_id = id;
        $('input[name=deposit_order_id]').val(deposit_order_id);
        $('#upload_deposit_receipt_modal .upload_receipt').removeClass("hidden");
        modal.modal('show');
    }

    function editUploadDespositReceiptModal(id, filepath){
        var deposit_order_id = id;
        var display_file = [];
        var view_exist_file = $("#viewExistReceiptImage");
        var img_area = '';

        $('input[name=deposit_order_id]').val(deposit_order_id);

        if(filepath){
            var display_file = filepath.split(',');
            for(var i=0; i<display_file.length; i++){
                img_area += '<img src=' + display_file[i] + '>';
            }
            view_exist_file.append(img_area);
        }

        switch (display_file.length){
            case HAS_ONE_FILE:
                $('#upload_deposit_receipt_modal .upload_receipt').first().removeClass('hidden');
                break;
            case HAVE_TWO_FILES:
            default:
                $('.upload_txt, .browse-btn, .submit-btn').hide();
                break;
        }

        modal.modal('show');
    }

    function uploadDepositReceiptImage(input,id){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#'+id).attr('src', e.target.result);
                $('.upload_txt').hide();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function validateDepositReceiept(file){
        var fp = $(file);
        console.log(fp);
        var lg = fp[0].files.length;

        if(lg != 0){
            var allowedUploadFile = ALLOWED_UPLOAD_FILE.split("|");
            for (var i = 0; i < allowedUploadFile.length; i++) {
                allowedUploadFile[i] = 'image/'+allowedUploadFile[i];
            }

            var fileErrMsg = LANG_UPLOAD_FILE_ERRMSG;

            var items = fp[0].files;
            if (lg > 0) {
                for (var i = 0; i < lg; i++) {

                    var fileSize = items[i].size; // get file size
                    var fileType = items[i].type; // get file type
                }
            }

            var limitSize = LANG_UPLOAD_IMAGE_MAX_SIZE;

            if(fileSize<=limitSize){
                if(allowedUploadFile.indexOf(fileType) === -1)
                {
                    flg=0;
                    $('#errfm_txtImage').text(fileErrMsg);
                    return false;
                }

            }else{
                flg=0;
                $('#errfm_txtImage').text(fileErrMsg);
                return false;
            }
        }

        return true;
    }

    function validateFile(){
        var file_1 = $('#uploadDepositReceipt1');
        var file_2 = $('#uploadDepositReceipt2');
        return (validateDepositReceiept(file_1) && validateDepositReceiept(file_2));
    }

    $(document).ready(function(){
        modal = $('#upload_deposit_receipt_modal');

        modal.on('hide.bs.modal', function(){
            $('input', modal).val('');
            $('img', modal).attr('src','');
            $("#viewExistReceiptImage").html('');
            $('.upload_txt, .browse-btn, .submit-btn').show();
            $('#upload_deposit_receipt_modal .upload_receipt').addClass("hidden");
        });
    })
    <?php endif;?>
</script>