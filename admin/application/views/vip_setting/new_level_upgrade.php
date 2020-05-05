<link href="<?=site_url().'resources/third_party/bower_components/bootstrap-toggle/css/bootstrap-toggle.min.css'?>" rel="stylesheet">
<link href="<?=site_url().'resources/third_party/bower_components/toastr/toastr.min.css'?>" rel="stylesheet">
<script type="text/javascript" src="<?=site_url().'resources/third_party/bower_components/bootstrap-toggle/js/bootstrap-toggle.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/third_party/bower_components/toastr/toastr.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/third_party/bower_components/jquery-mask-plugin/src/jquery.mask.js'?>"></script>
<style>
    @media all and (-webkit-min-device-pixel-ratio:0) and (min-resolution: .001dpcm) {
        select.condition
        {
            -webkit-appearance: none;
            appearance: none;
            padding : 2px 5px 2px 5px;
            box-shadow: none !important;
        }
    }
    .inline { display:inline; }
    .custom-input { width: 60px; }
    .well {
        border-radius: 5px;
        height : 55px;
        padding-left: 2px !important;
    }
    @media screen and (min-width: 992px) {
        .modal-lg {
            width: 950px; /* New width for large modal */
        }
        @-moz-document url-prefix() {
            .modal-lg {
                width: 970px; /* Firefox New width for large modal */
            }
        }
    }
    .popover-title { border-radius: 5px 5px 0 0; text-align: center; }
    .popover {
        background-color: #fff;
        max-width: 100%;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 12px;
        line-height: 1;
        border: 1px solid #ccc;
        border-radius: 6px;
        -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        box-shadow: 0 5px 10px rgba(0,0,0,.2);
        line-break: auto;
        z-index: 1010; /* A value higher than 1010 that solves the problem */
        position: fixed;
    }
    .popover-content {background-color: white; color:#545454;}
    .toast-top-center { margin-top : 80px; }
    #settingTbl_wrapper {
        overflow-y: hidden;
        overflow-x: hidden;
    }
</style>

<!-- Level Upgrade Setting -->
<div id="levelUpModal" class="modal fade " role="dialog">

    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= lang('VIP Setting Form'); ?></h4>
            </div>
            <div class="modal-body custom-height-modal">
                <div class="row">
                    <div class="col-xs-12">
                        <fieldset style="padding:20px;margin-bottom: 5px;">
                            <legend>
                                <h5 id="headerDescription"></h5>
                            </legend>
                            <form class="form-horizontal" id="settingForm">
                                <input type="hidden" id="upgradeId" value="">
                                <div class="form-group">
                                    <label for="settingName" class="col-sm-2 control-label"><?= lang('Setting Name'); ?> </label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="settingName" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="col-sm-2 control-label"><?= lang('sys.description') ?></label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="description">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?= lang('Upgrade Type') ?></label>
                                    <div class="col-sm-7">
                                        <select class="form-control" id="levelUpgrade">
                                            <option value=""><?= lang('Select Upgrade Type'); ?></option>
                                            <option value="1"><?= lang('Single Upgrade'); ?></option>
                                            <option value="2"><?= lang('Multiple Level Upgrade') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?= lang('cms.options'); ?></label>
                                    <div class="col-sm-8">
                                        <label class="checkbox-inline"><input type="checkbox" class="option" value="1"><?= lang('Bet Amount'); ?></label>
                                        <label class="checkbox-inline"><input type="checkbox" class="option" value="2"><?= lang('Deposit Amount'); ?></label>
                                        <label class="checkbox-inline"><input type="checkbox" class="option" value="3"><?= lang('Loss Amount'); ?></label>
                                        <label class="checkbox-inline"><input type="checkbox" class="option" value="4"><?= lang('Win Amount'); ?></label>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <div class="col-sm-offset-0 col-sm-12">
                                        <div class="well well-sm formula-container">
                                            <div class="row ">
                                                <div class="col-lg-12 text-center">
                                                    <div class="help-block notes">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="saveSettingBtn"><i class="fa"></i> <?= lang('Save Setting'); ?></button>
                                <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary' ?>" id="showList"><i class="fa fa-caret-square-o-right" aria-hidden="true"></i> <?= lang('Show List'); ?></button>
                            </form>
                        </fieldset>
                    </div>
                </div>

                <div class="row hide" id="listContainer">
                    <div class="col-xs-12">
                        <fieldset style="padding:20px">
                            <legend>
                                <h5><strong> Setting List </strong></h5>
                            </legend>

                            <table id="settingTbl" class="table table-hover" data-page-length='5'>
                                <thead>
                                <tr>
                                    <th></th>
                                    <th><?= lang('Setting Name'); ?></th>
                                    <th><?= lang('sys.description'); ?></th>
                                    <th><?= lang('Formula'); ?></th>
                                    <th><?= lang('lang.status'); ?></th>
                                    <th><?= lang('lang.action'); ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('lang.close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" style="margin-top:130px !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= lang('Delete Setting') ?></h4>
            </div>
            <input type="hidden" id="hiddenId">
            <div class="modal-body">
                <?= lang('sys.gd4'); ?>  <span style="color:#ff6666" id="name"></span>?
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn btn-default"><?= lang('lang.no'); ?></a>
                <a class="btn btn-primary" id="deleteBtn"><i class="fa"></i> <?= lang('lang.yes'); ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var checkedRows = [];
    var baseUrl = '<?php echo base_url(); ?>';

    var AMOUNT_MSG = {
        BET         : '<?= lang('Bet Amount'); ?>',
        DEPOSIT     : '<?= lang('Deposit Amount'); ?>',
        LOSS        : '<?= lang('Loss Amount'); ?>',
        WIN         : '<?= lang('Win Amount'); ?>'
    };

    var LANG =  {
        DELETE          : '<?= lang('lang.delete'); ?>',
        EDIT            : '<?= lang('lang.edit'); ?>',
        DISABLE         : '<?= lang('Disable'); ?>',
        ENABLE          : '<?= lang('Enable'); ?>',
        GREATER_LESS    : '<?= lang('Select greater than or equal to'); ?>',
        AND_OR          : '<?= lang('Select and or'); ?>',
        ENTER_AMOUNT    : '<?= lang('Enter Amount'); ?>',
        HIDE_LIST       : '<?= lang('Hide List'); ?>',
        SHOW_LIST       : '<?= lang('Show List'); ?>'
    };

    var options = {
        "positionClass": "toast-top-center",
        closeButton         : true,
        timeOut             : 1000,
        preventDuplicates   : true
    };

    var htmlInput = '<input type="text">';
    var $listContainer = $('#listContainer');
    var optionKey = ['bet_amount', 'deposit_amount', 'win_amount', 'loss_amount'];

    $(document).ready(function(){

        $(document).on('show.bs.modal', '.modal', function (event) {
            var zIndex = 1040 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });

        $('#settingTbl').DataTable({
            ajax : {
                url     : baseUrl + 'vipsetting_management/upgradeLevelSetting',
                type    : 'POST',
                async   : true
            },
            "order": [[ 0, "desc" ]],
            "bLengthChange": false,
            "bInfo": false,
            "bFilter": false,
            "pageLength": 50,
            columns : [
                { data : 'upgrade_id' , visible : false },
                { data : 'setting_name' },
                { data : 'description' },
                {
                    data : 'formula',
                    render : function(data, type, row) {
                        var formula = jQuery.parseJSON(data);
                        var formulaHtml = '';
                        var operator = '', amount = '';
                        var arr = '';
                        var formulaKey = Object.keys(formula);
                        for(var i in formulaKey) {
                            if(optionKey.indexOf(formulaKey[i]) >= 0) {
                                arr = formula[formulaKey[i]];
                                operator = arr[0];
                                amount = arr[1];
                                formulaHtml += optionNameByKey(formulaKey[i]) + ' ' + operator + ' ' + amount + ' ';
                            } else {
                                formulaHtml += formula[formulaKey[i]] + ' ';  // conjunction (or and)
                            }
                        }
                        var title = '';
                        if(row.level_upgrade == 1) {
                            title = '<?= lang('Upgrade Only'); ?>';
                        } else {
                            title = '<?= lang('Upgrade and Downgrade'); ?>';
                        }

                        return '<button type="button" title="'+title+'" data-placement="top" data-toggle="popover" data-trigger="focus" ' +
                            'data-content="'+formulaHtml+'" class="red-tooltip"><?= lang("Preview"); ?></button>';
                    }
                },
                {
                    data : 'status',
                    render : function(data) {
                        var status = '';
                        if(data == 1) {
                            status = '<span style="color:#66cc66;font-weight:bold;"><?= lang("lang.active"); ?></span>';
                        } else {
                            status = '<span style="color:#ff6666;font-weight:bold;"><?= lang("lang.inactive"); ?></span>';
                        }
                        return status;
                    }
                },
                {
                    data : 'upgrade_id',
                    render : function(data, type, row) {
                        var glypIcon = '', title = '', color = '';
                        if(row.status == 1) {
                            glypIcon = 'glyphicon-ban-circle';
                            title = LANG.DISABLE;
                            color = 'red';
                        } else {
                            glypIcon = 'glyphicon-ok-sign';
                            title = LANG.ENABLE;
                        }
                        return '<a data-toggle="tooltip" class="deleteSetting" data-id="'+data+'" data-name="'+row.setting_name+'" data-original-title="'+LANG.DELETE+'"><span class="glyphicon glyphicon glyphicon-trash"  style="color:red"></span> </a>  ' +
                            '<a data-toggle="tooltip" class="enableDisable" data-id="'+data+'" data-original-title="'+title+'" data-status="'+row.status+'"><span class="glyphicon '+glypIcon+'" style="color:'+color+'"></span></a> ' +
                            '<a data-toggle="tooltip" class="updateSetting" data-id="'+data+'" data-original-title="'+LANG.EDIT+'"><span class="glyphicon glyphicon-edit"></span> </a>';
                    }
                }
            ],
            drawCallback : function(data) {

                $('[data-toggle="popover"]').popover({
                    html : true
                });

                $('.deleteSetting').on('click', function(){
                    $('#name').text($(this).attr('data-name'));
                    $('#hiddenId').val($(this).attr('data-id'));
                    $('#deleteModal').modal('show');
                });

                $('.enableDisable').on('click', function(){
                    var status =  $(this).attr('data-status');
                    $.post( baseUrl + 'vipsetting_management/enableDisableSetting',
                        {
                            id : $(this).attr('data-id'),
                            status : status
                        }, function(){
                            if(status == 1) {
                                toastr.success('<?= lang('Successfully disable setting'); ?>', '', options).css("width","500px");
                            } else {
                                toastr.success('<?= lang('Successfully enable setting'); ?>', '', options).css("width","500px");
                            }

                            $('#settingTbl').DataTable().ajax.reload(null,false);
                            loadUpDownGradeSetting();
                        }
                    );
                });
            },
            rowCallback : function(row,data) {
                if(data.status == 2) {
                    row.className = 'info';
                }
                $('.updateSetting', row).on('click', function(){
                    addFormData(data);
                });
            }
        });

        $('#levelUpModal').on('hidden.bs.modal', function () {
            resetFormModal();
        });

        $('#showList').on('click', function(){
            if($listContainer.hasClass('hide')) {
                $listContainer.removeClass('hide');
                $(this).html('<i class="fa fa-caret-square-o-down" aria-hidden="true"></i> ' + LANG.HIDE_LIST);
            } else {
                $listContainer.addClass('hide');
                $(this).html('<i class="fa fa-caret-square-o-right" aria-hidden="true"></i> ' + LANG.SHOW_LIST);
            }
        });

        $('#saveSettingBtn').on('click', function(){
            if(validateFields()) {
                toastr.error('<?= lang('player.mp14'); ?>', '', options).css("width","500px");
                return false;
            }

            var conjunction = [];
            var formula = {};
            var $id = $('#upgradeId').val();

            $('.help-block .conjunction').each(function(){
                var $this = $(this);
                if($this.is(':checked')) {
                    conjunction.push('and');
                } else {
                    conjunction.push('or');
                }
            });

            var arrayLenth = checkedRows.length;
            if(arrayLenth >= 1) {
                for( var i=0; i < arrayLenth; i++) {
                    if(checkedRows[i]) {
                        var x = checkedRows[i];
                        var name = jsonKey(x);
                        var operator = $('#operator-' + x).val();
                        var amount = $('#amount-' + x).val();

                        formula[name] = [ operator, amount];
                    }
                }
            }

            var data = {
                settingName : $('#settingName').val(),
                description : $('#description').val(),
                levelUpgrade : $('#levelUpgrade').val(),
                formula : formula,
                conjunction : conjunction
            };
            if($id) {
                data.upgrade_id = $id;
            }
            $(this).find('i').addClass('fa-refresh fa-spin');
            setTimeout(function(){
                $.post( baseUrl + 'vipsetting_management/saveUpgradeSetting', data, function(data){
                    if($id) {
                        toastr.success('<?= lang('Successfully Update Setting'); ?>', '', options).css("width","500px");
                    } else {
                        toastr.success('<?= lang('Successfully Save Setting'); ?>', '', options).css("width",
                            "500px");
                    }
                    $('#settingTbl').DataTable().ajax.reload(null,false);
                    loadUpDownGradeSetting();
                    resetFormModal();
                    $('#saveSettingBtn').find('i').removeClass('fa-refresh fa-spin');
                });
            },200);
        });

        $('#deleteBtn').on('click', function(){
            $('#deleteBtn').find('i').addClass('fa-refresh fa-spin');
            setTimeout(function(){
                $.post( baseUrl + 'vipsetting_management/deleteUpgradeLevelSetting', { id : $('#hiddenId').val() }, function(){
                    toastr.success('<?= lang('Upgrade Setting Deleted'); ?>', '', options).css("width","500px");
                    $('#settingTbl').DataTable().ajax.reload(null,false);
                    loadUpDownGradeSetting();
                    $('#deleteModal').modal('hide');
                    $('#deleteBtn').find('i').removeClass('fa-refresh fa-spin');
                });
            }, 200)
        });

        $('.option').change(function() {
            var isCheck = $(this).is(":checked");
            var value = $(this).val();

            if(isCheck) {
                checkedRows.push($(this).val());
            } else {
                var index = checkedRows.indexOf($(this).val());
                if (index > -1) {
                    checkedRows.splice(index, 1);
                }
            }

            loadFormula(value, checkedRows.length, isCheck);
            activateToolTip();
            loadBootstrapToggle();
        });
    });

    function resetFormModal() {
        $('#levelUpModal').find('form').trigger('reset');
        $('.help-block').html('');
        checkedRows = [];
    }

    function getGetOrdinal(n) {
        var s=["th","st","nd","rd"],
            v=n%100;
        return n+(s[(v-20)%10]||s[v]||s[0]);
    }

    function activateToolTip() {
        $('[data-toggle="tooltip"]').tooltip();
    }


    function loadFormula(option, optionLength, isCheck) {
        var $option = $('.' + option);
        var formulaHtml = '';
        var createAndOr = '';
        var formula = '';

        var name = optionName(option);

        formulaHtml += '<div class="inline '+ option +'">';
        formulaHtml += '  <label style="font-weight: bold;">' + name + '</label>';
        formulaHtml += '  <select class="condition" id="operator-'+ option +'" data-toggle="tooltip" data-placement="top" title="'+LANG.GREATER_LESS+'">';
        formulaHtml += '    <option value="1"> >= </option>';
        formulaHtml += '    <option value="2"> <= </option>';
        formulaHtml += '  </select>';
        formulaHtml += '  <input type="text" class="custom-input" id="amount-'+ option +'" data-toggle="tooltip" data-placement="top" title="'+LANG.ENTER_AMOUNT+'" value="0">';
        formulaHtml += '</div>';

        createAndOr += '<div class="inline check-toggle">';
        createAndOr += '    <input id="toggle-'+option+'" class="conjunction" checked type="checkbox" data-onstyle="success" data-offstyle="info" data-toggle="tooltip" data-placement="top" title="'+LANG.AND_OR+'">';
        createAndOr += '</div>';

        if(isCheck) {
            if(optionLength == 1) {
                formula = formulaHtml;
            } else if(optionLength >= 2) {
                formula = createAndOr + formulaHtml;
            }
        } else {
            if($option.next('.check-toggle').length) {
                $option.next('.check-toggle').remove();
            } else {
                $option.prev('.check-toggle').remove();
            }
            $option.remove();
        }

        $('.help-block').append(formula);
    }

    function optionName(optionVal) {
        var optionName = '';
        if(optionVal == 1) {
            optionName = AMOUNT_MSG.BET;
        } else if(optionVal == 2) {
            optionName = AMOUNT_MSG.DEPOSIT;
        } else if(optionVal == 3) {
            optionName = AMOUNT_MSG.LOSS;
        } else if(optionVal == 4) {
            optionName = AMOUNT_MSG.WIN;
        }
        return optionName;
    }

    function optionNameByKey(key) {
        var optionName = '';
        if(key == 'bet_amount') {
            optionName = AMOUNT_MSG.BET;
        } else if(key == 'deposit_amount') {
            optionName = AMOUNT_MSG.DEPOSIT;
        } else if(key == 'loss_amount') {
            optionName = AMOUNT_MSG.LOSS;
        } else if(key == 'win_amount') {
            optionName = AMOUNT_MSG.WIN;
        }
        return optionName;
    }

    function loadBootstrapToggle() {
        $('#toggle-1, #toggle-2,#toggle-3,#toggle-4').bootstrapToggle({
            on: 'And', off: 'Or', size: "mini"
        });
    }

    function validateFields() {
        var isEmpty = false;
        var $s = $('#settingName').val();
        var $d = $('#description').val();
        var $l = $('#levelUpgrade').val();
        if($s =='' || $d=='' || $l=='' || checkedRows.length <= 0) {
            isEmpty = true;
        }
        return isEmpty;
    }

    function jsonKey(x) {
        var key = 0;
        if(x == 1) {
            key = 'bet_amount';
        } else if(x == 2) {
            key = 'deposit_amount';
        } else if(x == 3) {
            key = 'loss_amount';
        } else if(x == 4) {
            key = 'win_amount';
        }
        return key;
    }

    function addFormData(data) {
        $('#upgradeId').val(data.upgrade_id);
        $('#settingName').val(data.setting_name);
        $('#description').val(data.description);
        $('#levelUpgrade').val(data.level_upgrade);
    }
</script>