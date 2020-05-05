<div class="row email_template">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="pull-right">
                    <button type="button" class="btn btn-info p-5 p-r-10 email-setting-js"><i class="icon-settings"></i> &nbsp; <?= lang('email.setting.desc') ?></button>
                </div>
                <div class="panel-title">
                    <h4><?= lang('email.template.manager') ?></h4>
                </div>
            </div>
            <div class="panel-body m-t-20">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <?php $plateFormFirstKey = key($platform_type); ?>
                        <?php foreach ($platform_type as $key => $name): ?>
                            <?php $dtPlatformName = 'dt_' . $name; ?>
                            <li role="presentation" class="<?= ($plateFormFirstKey == $key) ? 'active' : '' ?>">
                                <a href="#<?= $dtPlatformName ?>" role="tab" data-toggle="tab" data-dt="<?= $dtPlatformName ?>"><?= strtoupper(lang("email.$name")) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="nav_content">
                        <div class="tab-content">
                            <?php foreach ($platform_type as $pf_key => $pf_name): ?>
                                <?php $dtPlatformName = 'dt_' . $pf_name; ?>
                                <div role="tabpanel" class="tab-pane <?= ($plateFormFirstKey == $pf_key) ? 'active' : '' ?>" id="<?= $dtPlatformName ?>">
                                    <div class="row">
                                        <div class="col-sm-6 email_template_switch">
                                            <label><h5><?= lang('email.template.type') ?> : </h5></label>
                                            <select name="" class="selectpicker m-l-15 email-template-select-type" data-dt="<?= $dtPlatformName ?>">
                                                <option value="0"><?= lang("email.template.type.0") ?></option>
                                                <?php foreach ($template_type as $key => $name): ?>
                                                    <option value="<?= lang("email.template.type.$key") ?>"> <?= lang("email.template.type.$key") ?> </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-condensed table-bordered table-hover email-template-table <?= $dtPlatformName ?>">
                                                <colgroup>
                                                    <col style="width: 5%;">
                                                    <col style="width: 15%;">
                                                    <col style="width: 15%;">
                                                    <col style="width: 50%;">
                                                    <col style="width: 15%;">
                                                </colgroup>
                                                <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th><?= lang('email.template.name') ?></th>
                                                    <th><?= lang('email.template.type') ?></th>
                                                    <th><?= lang('email.column.desc')  ?> </th>
                                                    <th><?= lang('email.column.action') ?> </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (isset($email_template_list[$pf_key])) : ?>
                                                    <?php foreach ($email_template_list[$pf_key] as $key => $row): ?>
                                                        <tr>
                                                            <td><?= ($key + 1) ?> </td>
                                                            <td>
                                                                <?php $tmpl_id = $row['id'] ?>
                                                                <a href="<?= site_url("cms_management/viewEmailTemplateManagerDetail/$tmpl_id") ?>">
                                                                <?= lang('email_template_name_' . $row['template_name']) ?>
                                                                </a>
                                                            </td>
                                                            <td><?= lang('email.template.type.' . $row['template_type']) ?> </td>
                                                            <td><?= nl2br(lang('email_template_desc_' . $row['template_name'])) ?> </td>
                                                            <td>
                                                                <div class="m-l-10 m-t-10 pull-left">
                                                                    <button class="action-btn"
                                                                            data-platform_type="<?= $pf_key ?>"
                                                                            data-template_type="<?= $tmpl_id ?>"
                                                                            data-template_name="<?= $row['template_name'] ?>"
                                                                            data-template_name_lang="<?= lang('email_template_name_' . $row['template_name']) ?>"
                                                                    > <?= lang('email.column.action.preview') ?> </button>
                                                                </div>
                                                                <div class="m-10 pull-left" >
                                                                    <input type="checkbox" class="switch_checkbox"
                                                                           data-on-text="<?= lang('email.column.action.switch.enable') ?>"
                                                                           data-off-text="<?= lang('email.column.action.switch.disable') ?>"
                                                                           data-handle-width="100"
                                                                           data-platform_type="<?= $pf_key ?>"
                                                                           data-template_name="<?= $row['template_name'] ?>"

                                                                           <?= ($row['is_enable']) ? 'checked' : ''?>
                                                                    />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade email-modal" id="email-preview-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal-lg vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title m-5"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <h5> <?= lang('email.subject') ?>  : </h5>
                            </div>
                            <div class="email-subject">
                                <h6>  </h6>
                            </div>
                        </div>
                        <div class="col-sm-6 email_template_switch">
                            <div class="pull-right">
                                <label><h5> <?= lang('email.preview.lang.version') ?> : </h5></label>
                                <select name="preview_lang" class="selectpicker preview_lang m-l-15">
                                    <?php foreach($system_lang as $row): ?>
                                        <option value="<?= $row['key'] ?>"> <?= $row['word'] ?> </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="m-t-10 m-l-10 pull-right">
                                    <i class="fa fa-exclamation-circle tool_tip" data-toggle="tooltip" data-placement="bottom" tooltip-trigger="focus manual" title="<?= lang('email.preview.icon.remind') ?>"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5> <?= lang('email.content') ?>  : </h5>
                            <div class="tab-content">
                                <div id="mail-content" class="tab-pane fade in active">
                                    <div class="email-preview-content">
                                    </div>
                                </div>
                                <div id="mail-content-text" class="tab-pane fade">
                                    <textarea class="email-preview-content-text textarea-plain-text" disabled></textarea>
                                </div>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#mail-content" data-mode="html"> Html </a></li>
                                <li><a data-toggle="tab" href="#mail-content-text" data-mode="text"> <?= lang('email.content.plain.text') ?> </a></li>
                            </ul>
                        </div>
                        <div class="col-sm-12">
                            <div class="pull-right">
                                <a id="preview-edit-btn" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"> <?= lang('Edit') ?> </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5><?= lang('email.preview.sending.test') ?></h5>
                            <h6 class="email-preview-test-description"><?= lang('email.preview.sending.test.description') ?></h6>
                        </div>
                        <div class="col-sm-12 m-b-20">
                            <div class="col-sm-10 p-l-0" >
                                <input type="text" name="player" class="form-control">
                            </div>
                            <div class="col-sm-2 p-r-0">
                                <div class="pull-right">
                                <button id="preview-send-btn" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-matisse' : 'btn-primary'?>"> <?= lang('Send') ?> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="platform_type">
                    <input type="hidden" name="template_type">
                    <input type="hidden" name="template_name">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-modal" id="email-setting-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title m-5"><?= lang('email.setting.desc') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5>1. <?= lang('email.setting.according') ?> : </h5>
                            <div class="form-group p-t-10 email_template_switch">
                                <input type="radio" name="operator_send_lang" value="1"> <span class="m-l-10"><?= lang('email.setting.system.default') ?></span>
                                <select class="m-l-10" name="operator_send_choose_lang">
                                    <?php foreach($system_lang as $row): ?>
                                        <option value="<?= $row['key'] ?>"> <?= $row['word'] ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="radio" name="operator_send_lang" value="2"> <span class="m-l-10"><?= lang('email.setting.player.language') ?></span>
                            </div>
                        </div>
                        <div class="col-sm-12 m-t-10">
                            <h5>2. <?= lang('email.setting.content.mode') ?> : </h5>
                            <div class="form-group p-t-10 email_template_switch">
                                <input type="radio" name="operator_send_mode" value="1"> <span class="m-l-10"> HTML</span>
                            </div>
                            <div class="form-group">
                                <input type="radio" name="operator_send_mode" value="2"> <span class="m-l-10"> <?= lang('email.setting.content.plain.text') ?> </span>
                            </div>
                        </div>
                        <div class="col-sm-12 m-t-20">
                            <div class="text-center">
                                <button type="button" id="saveEmailSetting" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"> Save </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-modal" id="email-send-error-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-danger">
                    <h4 class="modal-title m-5"><?= lang('email.template.manager') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= lang('email.preview.send.error') ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('OK') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-modal" id="email-send-success-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <h4 class="modal-title m-5"><?= lang('email.template.manager') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= lang('email.preview.send.success') ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('OK') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        let dtEmailTmpl = {},
            hash = window.location.hash,
            emailTable = <?= json_encode($platform_type); ?>

        hash && $('ul.nav a[href="' + hash + '"]').tab('show')

        $.each(emailTable, function ($k, $v) {
            let dtName  = 'dt_' + $v,
                dtClass = '.' + dtName

            dtEmailTmpl[dtName] = $(dtClass).DataTable({
                "dom": 'frtp',
                "autoWidth": false,
                "ordering": false,
                "responsive": {
                    details: {
                        type: 'column'
                    }
                },
                "order": [ 1, 'asc' ]
            });
        })

        $(".switch_checkbox").bootstrapSwitch({
            onSwitchChange: function(e, bool) {
                let data = $(this).data(),
                    isEnable = (bool) ? 1 : 0,
                    platformType = data.platform_type,
                    templateName = data.template_name

                $.ajax({
                    url: '/cms_management/ajax_enable_email_template',
                    method: "POST",
                    data: {
                        is_enable: isEnable,
                        platform_type: platformType,
                        template_name: templateName,
                    },
                    success: function(data) {
                        console.log(data)
                    }
                })
            }
        });

        $(".action-btn").click(function() {
            let data = $(this).data(),
                defaultLang = 1,
                platformType = data['platform_type'],
                templateType = data['template_type'],
                templateName = data['template_name'],
                templateNameLang = data['template_name_lang'],
                playerId     = $("input[name='player_id']"),
                previewLang  = $("select[name='preview_lang']"),
                platformTypeElem = $("input[name='platform_type']"),
                templateTypeElem = $("input[name='template_type']"),
                templateNameElem = $("input[name='template_name']")


            playerId.val('')
            previewLang.prop('selectedIndex', 0)
            previewLang.selectpicker('refresh')

            platformTypeElem.val(platformType)
            templateTypeElem.val(templateType)
            templateNameElem.val(templateName)

            $("#preview-edit-btn").prop("href", "/cms_management/viewEmailTemplateManagerDetail/" + templateType + '/' + defaultLang);

            showContentOnPreviewModal(platformType, templateName, defaultLang)
            $('#email-preview-modal .modal-title').text(`<?= lang('email.preview') ?> ${templateNameLang}`)
            $('#email-preview-modal input[name="player"]').val('')
            $('#email-preview-modal').modal('show')
        })

        $(".preview_lang").on("changed.bs.select", function () {
            let templateLang = $(this).val(),
                platformType = $("input[name='platform_type']").val(),
                templateType = $("input[name='template_type']").val(),
                templateName = $("input[name='template_name']").val()

            $("#preview-edit-btn").prop("href", "/cms_management/viewEmailTemplateManagerDetail/" + templateType + '/' + templateLang);
            showContentOnPreviewModal(platformType, templateName, templateLang)
        });

        $("#email-preview-modal #preview-send-btn").click(function() {
            let previewLang  = $("select[name='preview_lang']").val(),
                platformType = $("input[name='platform_type']").val(),
                templateName = $("input[name='template_name']").val(),
                username = $("#email-preview-modal input[name='player']").val(),
                curtMode = $('#email-preview-modal .nav-tabs .active a').data('mode'),
                successModal = $("#email-send-success-modal"),
                errorModal   = $("#email-send-error-modal")

            if (!username) {
                errorModal.modal("show")
                return
            }

            $.ajax({
                url: '/cms_management/ajax_send_preview_email',
                method: 'post',
                data: {
                    username: username,
                    curt_mode: curtMode,
                    template_lang: previewLang,
                    platform_type: platformType,
                    template_name: templateName,
                },
                async: false,
                dataType: "json",
                success: function(bool) {
                    if (bool) {
                        successModal.modal("show")
                    } else {
                        errorModal.modal("show")
                    }
                }
            })
        })

        $(".email-setting-js").click(function() {
            $.ajax({
                url: '/cms_management/ajax_get_email_setting',
                method: 'get',
                async: false,
                dataType: "json",
                success: function(data) {
                    let operatorSendLang = data.operator_send_lang,
                        operatorSendMode = data.operator_send_mode

                    $(`input[name='operator_send_mode'][value=${operatorSendMode}]`).prop('checked', true)
                    $(`input[name='operator_send_lang'][value=${operatorSendLang[0]}]`).prop('checked', true)
                    $(`select[name='operator_send_choose_lang'] option[value=${operatorSendLang[1]}]`).prop('selected', true)

                    if($(`input[name='operator_send_lang'][value=1]`).prop('checked') == false){
                        $("select[name='operator_send_choose_lang']").attr('disabled', true);
                    }

                    $("select[name='operator_send_choose_lang']").selectpicker('refresh')
                }
            })
            $("#email-setting-modal").modal('show')
        })

        $("input[name='operator_send_lang']").on("click change", function () {
            if($(`input[name='operator_send_lang'][value=1]`).prop('checked') == false){
                $("select[name='operator_send_choose_lang']").attr('disabled', true);
            }
            else{
                $("select[name='operator_send_choose_lang']").attr('disabled', false);
            }
            $("select[name='operator_send_choose_lang']").selectpicker('refresh')
        });

        $('#email-setting-modal #saveEmailSetting').click(function(){
            let operatorSendMode = $("input[name='operator_send_mode']:checked").val(),
                operatorSendLang = $("input[name='operator_send_lang']:checked").val(),
                operatorChooseLang = $("select[name='operator_send_choose_lang'] option:selected").val()

            $.ajax({
                url: '/cms_management/ajax_set_email_setting',
                method: 'post',
                async: false,
                dataType: "json",
                data: {
                    operator_send_mode: operatorSendMode,
                    operator_send_lang: [operatorSendLang, operatorChooseLang]
                },
                success: function(data) {
                    $('#email-setting-modal').modal('hide')
                }
            })
        })

        $('.email-template-select-type').on("changed.bs.select", function () {
            let dtName = $(this).data('dt'),
                selectValue = $(this).val()

            if (selectValue != 0 ) {
                dtEmailTmpl[dtName].columns(2).search(selectValue).draw();
            } else {
                dtEmailTmpl[dtName].columns().search('').draw()
            }
        });

        $('.email_template a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let dtName = $(this).data('dt'),
                selectpicker = $('.email-template-select-type')

            dtEmailTmpl[dtName].search('').draw()
            dtEmailTmpl[dtName].columns().search('').draw()
            selectpicker.val(0);
            selectpicker.selectpicker("refresh");
        });

        function showContentOnPreviewModal(platformType, templateName, templateLang) {

            $('ul.nav a[href="#mail-content"]').tab('show')

            $.ajax({
                url: '/cms_management/ajax_get_email_template_by_name',
                method: "GET",
                data: {
                    platform_type: platformType,
                    template_name: templateName,
                    template_lang: templateLang
                },
                async: false,
                dataType: "json",
                success: function(data) {
                    let mailSubject = $(".email-subject > h6"),
                        mailContent = $(".email-preview-content"),
                        mailContentText = $(".email-preview-content-text"),
                        mailTestDescription = $(".email-preview-test-description")

                    if (data.length) {
                        data = data[0]
                        mailSubject.text(data.mail_subject)
                        mailContent.html(data.mail_content)
                        mailContentText.val(data.mail_plain_content)
                        mailTestDescription.text(data.test_description)
                    } else {
                        mailSubject.text('')
                        mailContent.html('')
                        mailContentText.val('')
                        mailTestDescription.text('')
                    }
                }
            })
        }
    })
</script>