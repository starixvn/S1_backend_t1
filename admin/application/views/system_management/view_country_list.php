<style>
    .fa {  font-size:initial;  }
    .iframe { border:0; width:0; height:0; }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt"><i class="fa fa-cogs"></i> <?=lang('Country Rules Settings')?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well" style="overflow: auto">
                            <form method="post" id="countrySettingForm" role="form" class="form-inline">
                                <div class="row">
                                    <div class="form-group" style="padding-left: 10px;">
                                        <label class=""><b><?=lang('Country Rules Mode');?> : </b></label>
                                        <label class="radio-inline"> <input type="radio" id="allow" name="rulesMode" value="allow_all" /><?= lang('Country Allow All'); ?></label>
                                        <label class="radio-inline"> <input type="radio" id="deny"  name="rulesMode" value="deny_all" /><?= lang('Country Deny All'); ?></label>
                                    </div>
                                    <div class="form-group" style="margin-left: 30px;">
                                        <label for="blockUrl"><b><?= lang('Country Block Url Page'); ?> : </b></label>
                                        <input class="form-control" name="blockUrl" id="blockUrl" type="text" value="" style="width: 25em;" />
                                    </div>
                                    <div class="form-group">
                                        <button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" type="button" id="countrySettingsBtn"><i class="fa fa-save"></i> <?php echo lang('Save');?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt"><i class="fa fa-flag"></i> <?=lang('Country Rules')?></h3>
            </div>

            <div class="panel-body">
                <div class="row collapse" id="vipGroupShow">
                    <div class="col-md-12">
                        <div class="well" style="overflow: auto;">
                            <form method="post" id="addCountryForm" role="form" class="">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="countryList"><?=lang('Country'); ?> : </label>
                                            <select style="width:100%;" class="form-control input-sm" multiple id="countryList" name="country[]" required></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="status"><?=lang('cashier.134') ?> : </label>
                                            <textarea style="resize: none; height: 36px; max-height: 80px;" onkeyup="autogrow(this);" type="text" id="status" name="status" class="form-control input-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 col-md-offset-9">
                                        <div class="form-group pull-right">
                                            <button class="btn-sm btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>" id="reset" type="button"><?php echo lang('lang.reset') ?></button>
                                            <button class="btn-sm btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" type="button" id="addCountry"><i class=""></i> <?=lang('sys.ip13');?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr/>
                    </div>
                </div>

                <form method="post">
                    <div class="table-responsive">
                        <table class="table table-hover" style="width:100%;" id="countryRulesTable">
                            <div class="btn-action">
                                <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary '?>" id="addToggle" data-toggle="collapse" data-target="#vipGroupShow">
                                    <i class="glyphicon glyphicon-plus-sign" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="Add"></i> <?= lang('Add Country'); ?>
                                </button>
                                <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" id="btnDeleteAll">
                                    <i class="glyphicon glyphicon-trash" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="Delete"></i> <?= lang('Delete Country'); ?>
                                </button>
                                <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>" id="btnBlockAll">
                                    <i class="glyphicon glyphicon-ban-circle" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="Block"></i> <?= lang('Block Country'); ?>
                                </button>
                                <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-success'?>" id="btnAllowAll">
                                    <i class="glyphicon glyphicon-check" style="color:white;" data-toggle="tooltip" data-placement="bottom" title="Unblock"></i> <?= lang('Allow Country'); ?>
                                </button>
                                <div class="clearfix" style="margin-bottom: 15px;"></div>
                                <div class=""></div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 15px;"></div>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkAllCountry" /><?php lang('sys.ip07'); ?></th>
                                    <th><?=lang('Country Name');?></th>
                                    <th><?=lang('Country Code');?></th>
                                    <th><?=lang('sys.ip09');?></th>
                                    <th><?=lang('sys.ip10');?></th>
                                    <th><?=lang('sys.ip11');?></th>
                                    <?php if ($this->utils->isEnabledFeature('enable_country_blocking_affiliate') || $this->utils->isEnabledFeature('enable_country_blocking_agency')) : ?>
                                        <th><?=lang('sys.ip.column.www_m');?></th>
                                    <?php endif; ?>
                                    <?php if($this->utils->isEnabledFeature('enable_country_blocking_affiliate')) : ?>
                                        <th><?=lang('a_header.affiliate');?></th>
                                    <?php endif; ?>
                                    <?php if($this->utils->isEnabledFeature('enable_country_blocking_agency')) : ?>
                                        <th><?=lang('Agency');?></th>
                                    <?php endif; ?>
                                    <th><?=lang('cashier.134');?></th>
                                    <th><?=lang('mess.07');?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt"><i class="fa fa-flag"></i> <?=lang('IP Rules')?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>
                            <?=lang('White List');?>
                        </p>
                    </div>
                </div>
                <form id="frm_save_white_list" action="<?=site_url('/country_rules_management/save_white_list');?>" method="POST">
                    <input type="hidden" name="white_list" />
                    <div class="row">
                        <div class="col-md-12">
                            <pre class="form-control" id="ip_white_list" name="ip_white_list" style="height: 300px"><?=$www_ip_white_list;?></pre>
                        </div>
                        <div class="col-md-12">
                            <input id="btn_save_white_list" type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?php echo lang('Save White List');?>">
                        </div>
                    </div>
                </form>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p>
                            <?=lang('Block List');?>
                        </p>
                    </div>
                </div>
                <form id="frm_save_block_list" action="<?=site_url('/country_rules_management/save_block_list');?>" method="POST">
                    <input type="hidden" name="block_list" />
                    <div class="row">
                        <div class="col-md-12">
                            <pre class="form-control" id="ip_block_list" name="ip_block_list" style="height: 300px"><?=$www_ip_block_list;?></pre>
                        </div>
                        <div class="col-md-12">
                            <input id="btn_save_block_list" type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?php echo lang('Save Block List');?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden" />
    </form>
<?php }?>

<script>
    var baseUrl = '<?=base_url(); ?>';
    var checkedRows = [];
    var status, statusClass, icon = '';

    var countryRules = {
        whiteList : 1,
        blockList : 2,
        enabled : 1,
        disabled : 2
    };

    var message = {
        block           : '<?= lang('tool.pm08'); ?>',
        allow           : '<?= lang('lang.allow'); ?>',
        deleteCountry   : '<?= lang('lang.delete'); ?>',
        update          : '<?= lang('lang.edit'); ?>',
        successAdd      : '<?= lang('Success Add'); ?>',
        successDelete   : '<?= lang('Success Delete'); ?>',
        empty           : '<?= lang('Select Country First'); ?>',
        successSetting  : '<?= lang('Save settings successfully'); ?>'
    };

    var countrySettings = {
        mode            : '<?= $this->operatorglobalsettings->getSettingValue('country_rules_mode'); ?>',
        blockUrlPage    : '<?= $this->operatorglobalsettings->getSettingValue('block_page_url'); ?>'
    };

    function isJsonString(str) {
        if (str == '') return true;
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    var $excelForm = $('#_export_excel_queue_form');

    function autogrow(textarea){
        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
        if (adjustedHeight>textarea.clientHeight){
            textarea.style.height = adjustedHeight + 'px';
        }
    }

    $(document).ready(function(){
        initializeSetting();
        loadCountries();

        var text_blocked = '<span class="help-block" style="color:#ff6666;">Blocked</span>',
            text_allowed = '<span class="help-block" style="color:#66cc66;">Allowed</span>';

        $('#countryRulesTable').DataTable({
            destroy: true,
            responsive : true,
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l>" +
                 "<'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p>" +
                 "<'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                },
                <?php if( $this->permissions->checkPermissions('export_country_rules') ){ ?>
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {};
                        $.post(site_url('/export_data/countryRules'), d, function(data){
                            if(data && data.success){
                                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" class="frame"></iframe>');
                            }else{
                                alert('export failed');
                            }
                        });
                    }
                }
                <?php } ?>
            ],
            ajax : {
                url     : baseUrl + 'country_rules_management/countryRulesList',
                type    : 'POST',
                async   : true
            },
            order : [[ 3, "desc" ]],
            columnDefs: [
                {
                    'targets': [0,6],
                    'orderable': false,
                    'searchable ' : false
                }
            ],
            columns : [
                {
                    data : 'id',
                    render : function(data) {
                        return "<input type='checkbox' value='"+data+"'>";
                    }
                },
                { data : 'country_name' },
                { data : 'country_code' },
                { data : 'created_at' },
                { data : 'username'},
                {
                    data : 'flag',
                    render : function(data) {
                        var flag = '';
                        flag = data == 1 ? text_allowed : text_blocked;
                        return flag;
                    }
                },

                <?php if ($this->utils->isEnabledFeature('enable_country_blocking_affiliate') || $this->utils->isEnabledFeature('enable_country_blocking_agency')): ?>
                {
                    data: 'blocked_www_m' ,
                    render : function(data, type, row) {
                        var status_unblocked = row.blocked_www_m == null || row.blocked_www_m == 2;
                        if (status_unblocked == true) {
                            statusClass = 'btn-success'; icon = 'glyphicon-unchecked';
                        } else {
                            statusClass = 'btn-warning'; icon = 'glyphicon-check';
                        }
                        return row.flag == 2
                        ? '<div class="btn-toolbar" data-id="'+data+'">' +
                        '<button type="button" class="btn btn-sm '+statusClass+' btnWwwmAllow"><i class="glyphicon '+icon+'"></i></button>'+
                        '</div>' + (status_unblocked == true ? text_allowed : text_blocked)
                        : '';
                    }
                },
                <?php endif; ?>

                <?php if($this->utils->isEnabledFeature('enable_country_blocking_affiliate')): ?>
                {
                    data: 'is_affiliate',
                    render : function(data, type, row) {
                        var status_unblocked = row.is_affiliate == null || row.is_affiliate == 2;
                        if (status_unblocked == true) {
                            statusClass = 'btn-success'; icon = 'glyphicon-unchecked';
                        } else {
                            statusClass = 'btn-warning'; icon = 'glyphicon-check';
                        }
                        return row.flag == 2
                        ? '<div class="btn-toolbar" data-id="'+data+'">' +
                        '<button type="button" class="btn btn-sm '+statusClass+' btnAffiAllow"><i class="glyphicon '+icon+'"></i></button>'+
                        '</div>' + (status_unblocked == true ? text_allowed : text_blocked)
                        : '';
                    }
                },
                <?php endif;  if($this->utils->isEnabledFeature('enable_country_blocking_agency')):?>
                {
                    data: 'is_agent',
                    render: function (data, type, row) {
                        var status_unblocked = row.is_agent == null || row.is_agent == 2;
                        if (status_unblocked == true) {
                            statusClass = 'btn-success'; icon = 'glyphicon-unchecked';
                        } else {
                            statusClass = 'btn-warning'; icon = 'glyphicon-check';
                        }
                        return row.flag == 2
                        ? '<div class="btn-toolbar" data-id="' + data + '">' +
                        '<button type="button" class="btn btn-sm ' + statusClass + ' btnAgencyAllow"><i class="glyphicon ' + icon + '"></i></button>' +
                        '</div>' + (status_unblocked == true ? text_allowed : text_blocked)
                        : '';
                    }
                },
                <?php endif; ?>
                { data : 'notes'},
                {
                    data : 'id',
                    render : function(data, type, row) {
                        if(row.flag == 1) {
                            status = message.block;
                            statusClass = "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-burntsienna' : 'btn-warning'?>"; icon = 'glyphicon-ban-circle';
                        } else {
                            status = message.allow;
                            statusClass = "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-emerald' : 'btn-success'?>"; icon = 'glyphicon-check';
                        }
                        return '' +
                            '<div class="btn-toolbar" data-id="'+data+'">' +
                            '<button type="button" class="btn btn-sm '+statusClass+' btnBlock"><i class="glyphicon '+icon+'"></i> '+status+'</button>' +
                            <?php if($this->utils->getConfig('use_new_sbe_color')){?>
                                '<button type="button" class="btn btn-sm btnDelete btn-chestnutrose"><i class="glyphicon glyphicon-trash" style="color:white;"></i>'+message.deleteCountry+'</button>' +
                            <?php }else{?>
                                '<button type="button" class="btn btn-sm btnDelete btn-danger"><i class="glyphicon glyphicon-trash"></i>'+message.deleteCountry+'</button>' +
                            <?php }?>
                            '</div><div class="clearfix"></div>';
                    }
                }
            ],
            drawCallback : function() {
                $('.buttons-collection, .progress').addClass('hide');
                 if ( $('#countryRulesTable').DataTable().rows( { selected: true } ).indexes().length === 0 ) {
                    $('#countryRulesTable').DataTable().buttons().disable();
                }
                else {
                    $('#countryRulesTable').DataTable().buttons().enable();
                }
            },
            createdRow: function( row, data ) {
                $(row).attr('data-row-id', data.id);
                $(row).attr('data-country-code', data.country_code);
                $(row).attr('data-country-name', data.country_name);
            },
            rowCallback : function(row, data) {
                if(data.flag == 2) {
                    row.className = 'info';
                }
                $("#countryList option[value='"+data.country_code+"']").remove();

                $('input', row).on( 'change', function(){
                    if( $(this).is(":checked") ) {
                        checkedRows.push(data.id);
                    } else {
                        var index = checkedRows.indexOf(data.id);
                        if (index > -1) {
                            checkedRows.splice(index, 1);
                        }
                    }
                });

                selectAll($('#checkAllCountry'), data);

                $('.btnDelete', row).on('click', function(){
                    clearNotify();
                    checkedRows = [];
                    checkedRows.push(data.id);
                    deleteCountry(checkedRows, $(this), 'glyphicon-trash');
                    $('#countryList').prepend($("<option></option>").attr("value",data.country_code).text(data.country_name));
                });

                $('.btnBlock', row).on('click', function(){
                    clearNotify();
                    checkedRows = [];
                    checkedRows.push($(this).closest('tr').find(':checkbox').val());
                    if(data.flag == 1) {
                        status = countryRules.blockList;
                        blockAllowCountry(checkedRows, status, $(this), 'glyphicon-check');
                    } else {
                        status = countryRules.whiteList;
                        blockAllowCountry(checkedRows, status, $(this), 'glyphicon-ban-circle');
                    }
                });

                $('.btnWwwmAllow', row).on('click', function(){
                    clearNotify();
                    checkedRows = [];
                    checkedRows.push($(this).closest('tr').find(':checkbox').val());
                    if(data.blocked_www_m == 1) {
                        status = countryRules.enabled;
                        enableWwwm(checkedRows, status, $(this), 'glyphicon-check');
                    } else {
                        status = countryRules.disabled;
                        enableWwwm(checkedRows, status, $(this), 'glyphicon-unchecked');
                    }
                });

                $('.btnAffiAllow', row).on('click', function(){
                    clearNotify();
                    checkedRows = [];
                    checkedRows.push($(this).closest('tr').find(':checkbox').val());
                    if(data.is_affiliate == 1) {
                        status = countryRules.enabled;
                        enableAffiliate(checkedRows, status, $(this), 'glyphicon-check');
                    } else {
                        status = countryRules.disabled;
                        enableAffiliate(checkedRows, status, $(this), 'glyphicon-unchecked');
                    }
                });
                $('.btnAgencyAllow', row).on('click', function(){
                    clearNotify();
                    checkedRows = [];
                    checkedRows.push($(this).closest('tr').find(':checkbox').val());
                    if(data.is_agent == 1) {
                        status = countryRules.enabled;
                        enableAgency(checkedRows, status, $(this), 'glyphicon-check');
                    } else {
                        status = countryRules.disabled;
                        enableAgency(checkedRows, status, $(this), 'glyphicon-unchecked');
                    }
                });

                $('.btnUpdate', row).on('click', function(){
                    $('#countryModal').modal('show');
                });
            }
        });

        toggleAdd($('#addToggle'));

        $("a#viewCountry").addClass("active");

        var ip_white_list = ace.edit("ip_white_list");
        ip_white_list.setTheme("ace/theme/tomorrow");
        ip_white_list.session.setMode("ace/mode/json");

        var ip_block_list = ace.edit("ip_block_list");
        ip_block_list.setTheme("ace/theme/tomorrow");
        ip_block_list.session.setMode("ace/mode/json");

        $('#btn_save_white_list').click(function(){
            var ip_white_list_value=ip_white_list.getValue();
            if ( ! isJsonString(ip_white_list_value)) {
                alert('Invalid JSON');
                return false;
            }

            $('#frm_save_white_list [name=white_list]').val(ip_white_list_value);
            $('#frm_save_white_list').submit();
        });

        $('#btn_save_block_list').click(function(){
            var ip_block_list_value=ip_block_list.getValue();

            if ( ! isJsonString(ip_block_list_value)) {
                alert('Invalid JSON');
                return false;
            }
            $('#frm_save_block_list [name=block_list]').val(ip_block_list_value);
            $('#frm_save_block_list').submit();
        });
    });

    $('#addCountry').on('click', function(){
        clearNotify();
        var countryLen = $("select[name='country[]'] option:selected").length;
        if(countryLen > 0) {
            buttonLoadStart($(this));
            setTimeout(function(){
                $.post(baseUrl + 'country_rules_management/addCountryRules',$('#addCountryForm').serialize(),function(){
                    $('#countryRulesTable').DataTable().ajax.reload(null,false);
                    $.notify( message.successAdd ,{type: 'success'});
                    clearSelect();
                    buttonLoadEnd($('#addCountry'));
                });
            }, 200);
        } else {
            $.notify(message.empty);
        }
    });

    $("#reset").on('click', function(){
        clearSelect();
    });

    $('#btnDeleteAll').on('click', function(){
        clearNotify();
        deleteCountry(checkedRows, $(this), 'glyphicon-trash');
    });

    $('#btnBlockAll').on('click', function(){
        clearNotify();
        blockAllowCountry(checkedRows, countryRules.blockList, $(this), 'glyphicon-ban-circle');
    });

    $('#btnAllowAll').on('click', function(){
        clearNotify();
        blockAllowCountry(checkedRows, countryRules.whiteList, $(this), 'glyphicon-check');
    });

    function selectAll(element, data) {
        element.on('change', function(){
            if( $(this).is(":checked")) {
                $( ':checkbox' ).prop('checked', true);
                checkedRows.push(data.id);
            } else {
                $( ':checkbox' ).prop('checked', false);
                checkedRows = [];
            }
        });
    }

    function deleteCountry(countryIds, btn, glypIcon) {
        if(countryIds.length > 0) {
            buttonLoadStart(btn,glypIcon);
            setTimeout(function(){
                $.post(baseUrl + 'country_rules_management/deleteCountryRules',{ countryIds : countryIds },function(){
                    $('#countryRulesTable').DataTable().ajax.reload(null,false);
                    checkedRows = [];
                    $.notify( message.successDelete ,{type: 'success'});
                    buttonLoadEnd(btn, glypIcon);
                });
                $(':checkbox').prop('checked', false);
            }, 200)
        } else {
            errorMessage();
        }
    }

    function blockAllowCountry(countryIds, countryFlag, btn, glypIcon) {
        if(countryIds.length > 0) {
            buttonLoadStart(btn,glypIcon);
            setTimeout(function(){
                $.post(baseUrl + 'country_rules_management/blockCountryRules',{ countryIds : countryIds, flag : countryFlag },function(data){
                    $('#countryRulesTable').DataTable().ajax.reload(null,false);
                    checkedRows = [];
                    $.notify( data.message ,{type: 'success'});
                },"json");
                $(':checkbox').prop('checked', false);
                buttonLoadEnd(btn, glypIcon);
            }, 200);
        } else {
            errorMessage();
        }
    }

    function enableWwwm(countryIds, status, btn, glypIcon){
        if(countryIds.length > 0) {
            buttonLoadStart(btn,glypIcon);
            setTimeout(function(){
                $.post(baseUrl + 'country_rules_management/enableWwwm',{ countryIds : countryIds, status : status },function(data){
                    $('#countryRulesTable').DataTable().ajax.reload(null,false);
                    checkedRows = [];
                    $.notify( data.message ,{type: 'success'});
                },"json");
                $(':checkbox').prop('checked', false);
                buttonLoadEnd(btn, glypIcon);
            }, 200);
        } else {
            errorMessage();
        }
    }

    function enableAffiliate(countryIds, status, btn, glypIcon){
        if(countryIds.length > 0) {
            buttonLoadStart(btn,glypIcon);
            setTimeout(function(){
                $.post(baseUrl + 'country_rules_management/enableAffiliateOrAgency',{ countryIds : countryIds, status : status, field: 'is_affiliate'  },function(data){
                    $('#countryRulesTable').DataTable().ajax.reload(null,false);
                    checkedRows = [];
                    $.notify( data.message ,{type: 'success'});
                },"json");
                $(':checkbox').prop('checked', false);
                buttonLoadEnd(btn, glypIcon);
            }, 200);
        } else {
            errorMessage();
        }
    }

    function enableAgency(countryIds, status, btn, glypIcon){
        if(countryIds.length > 0) {
            buttonLoadStart(btn,glypIcon);
            setTimeout(function(){
                $.post(baseUrl + 'country_rules_management/enableAffiliateOrAgency',{ countryIds : countryIds, status : status, field: 'is_agent'  },function(data){
                    $('#countryRulesTable').DataTable().ajax.reload(null,false);
                    checkedRows = [];
                    $.notify( data.message ,{type: 'success'});
                },"json");
                $(':checkbox').prop('checked', false);
                buttonLoadEnd(btn, glypIcon);
            }, 200);
        } else {
            errorMessage();
        }
    }

    function errorMessage() {
        $.notify( message.empty ,{type: 'danger'});
    }

    function buttonLoadStart(btn,icon) {
        btn.find('i').removeClass(icon).addClass('fa fa-refresh fa-spin');
        btn.prop("disabled", true);
    }

    function buttonLoadEnd(btn,icon) {
        btn.find('i').removeClass('fa fa-refresh fa-spin').addClass(icon);
        btn.prop("disabled", false);
    }

    function toggleAdd(element) {
        element.on('click', function(){
            var i = $(this).find('i');
            if(i.hasClass('glyphicon-plus-sign')) {
                i.removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
            } else if(i.hasClass('glyphicon-minus-sign')) {
                i.removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
            }
        });
    }

    function loadCountries() {
        $.post(baseUrl + 'country_rules_management/countryList',function(data){
            var dataList = data;
            var option = '';
            for(var coutryCode in dataList) {
                option += '<option value="'+data[coutryCode]+'">'+capitalize(coutryCode)+'</option>'
            }
            $('#countryList').html(option);
        },"json");

        $("#countryList").select2();
    }

    function clearSelect() {
        $('.select2-selection__choice').remove();
        $('#status').val('');
    }

    function capitalize(string) {
        return string.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
        });
    }

    function clearNotify() {
        $.notifyClose('all');
    }

    function initializeSetting() {
        if(countrySettings.mode == 'deny_all') {
            $("#deny").attr('checked', 'checked');
        } else {
            $("#allow").attr('checked', 'checked');
        }
        $('#blockUrl').val(countrySettings.blockUrlPage);

        $('#countrySettingsBtn').on('click', function(){
            clearNotify();
            $(this).find('i').addClass('fa-spin');
            setTimeout(function(){
                $.post( base_url + 'country_rules_management/countryRulesSetting', $('#countrySettingForm').serialize(), function(){
                    $('#countrySettingsBtn').find('i').removeClass('fa-spin');
                    $.notify( message.successSetting ,{type: 'success'});
                });
            }, 200);
        });
    }
</script>
