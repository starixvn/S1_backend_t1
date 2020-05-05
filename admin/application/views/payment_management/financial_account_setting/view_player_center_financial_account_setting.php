<style>
    .sub_title {
        border-radius: 0;
        position: relative;
        display: block;
        padding: 10px 15px;
        color: #008cba;
        background-color: transparent;
    }
    .checkbox_align {
        vertical-align: top;
        margin-right: 5px !important;
    }
</style>
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-html5sortable/1.0.0/jquery.sortable.min.js');?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css')?>" />
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js');?>"></script>

<div id="registration_main_content">
<div class="panel panel-primary">
    <div class="panel-heading">
        <h1 class="panel-title"><i class="icon-cog"></i> <?=lang('pay.financial_account_setting');?></h1>
    </div>

    <div class="panel-body" id="player_panel_body">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li id="bank_account"><a href="#" onclick="changeFinancialAccountSetting('1');" data-toggle="tab"><?=lang('pay.payment_type_bank');?></a></li>
                <li id="ewallet"><a href="#" onclick="changeFinancialAccountSetting('2');" data-toggle="tab"><?=lang('pay.payment_type_ewallet');?></a></li>
                <li id="crypto"><a href="#" onclick="changeFinancialAccountSetting('3');" data-toggle="tab"><?=lang('pay.payment_type_crypto');?></a></li>
                <li id="others"><a href="#" onclick="changeFinancialAccountSetting('0');" data-toggle="tab"><?=lang('financial_account.others');?></a></li>
            </ul>

            <div id="nav_content" style="width: 100%; height: auto; float: left; border: 1px solid lightgray; border-top: none; padding-bottom: 10px">

            </div>
        </div>
    </div>

    <div class="panel-footer">
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //submenu
        $('#collapseSubmenu').addClass('in');
        $('#view_payment_settings').addClass('active');
        $('#viewPlayerCenterFinancialAccountSettings').addClass('active');
        changeFinancialAccountSetting(<?=$type?>);
    });

    function changeFinancialAccountSetting(type) {
        var xmlhttp = GetXmlHttpObject();

        if (xmlhttp == null) {
            alert("Browser does not support HTTP Request");
            return;
        }

        var div = document.getElementById("nav_content");
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                div.innerHTML = xmlhttp.responseText;
                $('.tab').removeClass('active');
                $(".switch_checkbox").bootstrapSwitch();

                if(type == 1) {
                    $('#bank_account').addClass('active');
                }else if (type == 2) {
                    $('#ewallet').addClass('active');
                }else if (type == 3){
                    $('#crypto').addClass('active');
                }else if (type == 0){
                    $('#others').addClass('active');
                }
            }

            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/><?=lang('text.loading')?></td></tr></table>';
            }
        }

        url = base_url + "payment_management/changeFinancialAccountSetting/" + type;
        xmlhttp.open("GET", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send();
    }

    function toggleFieldVisibility(field) {
        var isVisible = $('#' + field + '_visible:checked').val();
        if (!isVisible) {
            $('#' + field + '_required').prop('checked', false);
            $('#' + field + '_required').bootstrapSwitch('state', false);
            if(field =='name'){
                $('#name_edit').prop('checked', false);
                $('#name_edit').attr("disabled", true);
            }
        }
        else {
            $('#name_edit').attr("disabled", false);
        }
        $('#' + field + '_required').prop('disabled', !isVisible);
        $('#' + field + '_required').bootstrapSwitch('disabled', !isVisible);
    }

    function toggleDepositBank() {
        var isEnable = $('#enable_deposit_bank:checked').val();
        if (!isEnable) {
            $('.deposit_bank').prop('checked', false);
            $('#max_deposit_account_number').val('');
        }
        $('.deposit_bank').prop('disabled', !isEnable);
    }

    function toggleAccountLimit(field) {
        var isEnable = $('#' + field + '_account_limit:checked').val();
        if (!isEnable) {
            $('#max_' + field + '_account_number').val('');
        }
        else{
            $('#max_' + field + '_account_number').val(10);
            $('#max_' + field + '_account_number').prop('required', 'required');
        }
        $('#max_' + field + '_account_number').prop('disabled', !isEnable);
    }

    function checkMinMaxValue() {
        var min = $('#length_min').val();
        var max = $('#length_max').val();

        if(parseInt(min) > parseInt(max)){
            alert('<?=lang('financial_account.minmax.prompt')?>');
        }
        else{
            $('.financial_account_setting_form').submit();
        }
    }
</script>