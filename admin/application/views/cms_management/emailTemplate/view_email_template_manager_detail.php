<div class="row email_template">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?= lang('email.template.manager') ?></h4>
                </div>
            </div>
            <div class="panel-body m-t-20">
                <div class="table-responsive">
                    <div class="pull-left">
                        <ol class="breadcrumb">
                            <li><a href="/cms_management/viewEmailTemplateManager#dt_<?= $platform_name ?>"> <?= strtoupper(lang("email.$platform_name")) ?> </a></li>
                            <li class="active"> <?= lang('email_template_name_' . $template_name) ?> </li>
                        </ol>
                    </div>
                    <table class="table table-condensed table-bordered table-hover email-template-table">
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 15%;">
                            <col style="width: 15%;">
                            <col style="width: 55%;">
                            <col style="width: 10%;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th><?= lang('Language') ?></th>
                            <th><?= lang('email.subject') ?></th>
                            <th><?= lang('email.content') ?></th>
                            <th><?= lang('email.column.action') ?></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-modal" id="email-edit-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal-lg vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <h4 class="modal-title m-5"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5><?= lang('email.subject') ?> :</h5>
                            <input type="text" class="form-control" name="mail_subject">
                        </div>
                        <div class="col-sm-12 m-t-10">
                            <h5><?= lang('email.content') ?> :</h5>

                            <div class="tab-content">
                                <div id="summernote-editor" class="tab-pane fade in active">
                                    <textarea class="summernote" id="summernote"></textarea>
                                </div>
                                <div id="textarea-editor" class="tab-pane fade">
                                    <textarea name="mail_content_text" class="textarea-plain-text"></textarea>
                                </div>

                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#summernote-editor"> Html </a></li>
                                <li><a data-toggle="tab" href="#textarea-editor"> <?= lang('email.content.plain.text') ?> </a></li>
                            </ul>
                        </div>
                        <div class="col-sm-12 m-t-20">
                            <h6><?= lang('email_element.note') ?></h6>
                        </div>
                        <?php $elemCount = count($template_element); ?>
                        <?php if ($elemCount > 0): ?>
                        <?php $elemPart = array_chunk($template_element, ceil($elemCount / 2 )) ?>
                        <?php if (isset($elemPart[0])): ?>
                        <div class="col-sm-6">
                            <table class="table table-bordered email-replace-msg">
                                <thead>
                                <tr>
                                    <td><?= lang('email_element.element') ?></td>
                                    <td><?= lang('email_element.description') ?></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($elemPart[0] as $elem): ?>
                                <tr>
                                    <td><?= $elem ?></td>
                                    <td><?= lang('email_element_desc_' . substr($elem, 1, -1)) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($elemPart[1])): ?>
                        <div class="col-sm-6">
                            <table class="table table-bordered email-replace-msg">
                                <thead>
                                <tr>
                                    <td><?= lang('email_element.element') ?></td>
                                    <td><?= lang('email_element.description') ?></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($elemPart[1] as $elem): ?>
                                <tr>
                                    <td><?= $elem ?></td>
                                    <td><?= lang('email_element_desc_' . substr($elem, 1, -1)) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="col-sm-12">
                            <div class="text-center m-t-15">
                                <button type="button" id="email-cancel-edit" data-edited="false" onclick="checkEdited()" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"> <?= lang('Cancel') ?> </button>
                                <button type="button" id="email-submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter m-l-5' : 'btn-info m-l-10'?>"> <?= lang('Save') ?> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-modal" id="email-edit-error-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-danger">
                    <h4 class="modal-title m-5"><?= lang('email.template.manager') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= nl2br(lang('email.edit.error')) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>" data-dismiss="modal"><?=lang('OK') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-modal" id="email-edit-success-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <h4 class="modal-title m-5"><?= lang('email.template.manager') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= lang('email.edit.success') ?>
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

<div class="modal fade email-modal" id="email-edit-warning-modal" tabindex="-1" role="dialog">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal vertical-align-center" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-warning">
                    <h4 class="modal-title m-5"><?= lang('email.template.manager') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= nl2br(lang('email.edit.warning')) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-dismiss="modal"><?= lang('No') ?></button>
                    <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>" data-dismiss="modal" onclick="closeEditModal()"><?= lang('Yes') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        let templateId, templateLang, templateName, templateType, platformType,
            mailContent = $('#summernote'),
            mailSubject = $("input[name='mail_subject']"),
            mailContentText = $("textarea[name='mail_content_text']"),
            dtEmailTmpl = $('.email-template-table').DataTable({
                "ajax": '/cms_management/ajax_get_email_template_detail/' + <?= $template_id ?>,
                "autoWidth": false,
                "searching": false,
                "dom": 'frt',
                "ordering": false,
                "columns": [
                    {"data": "no"},
                    {"data": "lang"},
                    {"data": "mail_subject"},
                    {
                        "data": "mail_content",
                        "render": function ( data, type, full, meta ) {
                            if (data == null) {
                                data = ''
                            }
                            return `<div class="email-content">${data}</div>`;

                        }
                    },
                    {
                        "data": "edit_btn",
                        "render": function ( data, type, full, meta ) {
                            return `
                              <div class="m-l-10 m-t-10 pull-left">
                                    <button class="btn-action <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-scooter' : 'action-btn'?>"
                                        data-id="${data.data_id}"
                                        data-lang="${data.data_lang}"
                                        data-lang_word="${data.data_lang_word}"
                                        data-tmpl_type="${data.data_tmpl_type}"
                                        data-tmpl_name="${data.data_tmpl_name}"
                                        data-tmpl_name_lang="<?= lang('email_template_name_' . $template_name) ?>"
                                        data-platf_type="${data.data_platf_type}"
                                    > <?= lang('Edit') ?> </button>
                                </div>
                            `;

                        }
                    },
                ]
            });


        <?php if(!is_null($lang_id)): ?>
            $.ajax({
                url: '/cms_management/ajax_get_email_template_modal/' + <?= $template_id ?> + '/'+ <?= $lang_id ?>,
                method: "GET",
                dataType: "json",
                success: function(data) {

                    templateId   = data.id
                    templateLang = data.lang
                    templateLangWord = data.lang_word
                    templateName = data.tmpl_name
                    templateType = data.tmpl_type
                    platformType = data.platf_type
                    templateNameLang = "<?= lang('email_template_name_' . $template_name) ?>"

                    $.ajax({
                        url: '/cms_management/ajax_get_email_template',
                        method: "GET",
                        async: false,
                        data: {
                            template_name: templateName,
                            template_lang: templateLang,
                            platform_type: platformType,
                        },
                        success: function(data) {
                            mailSubject.val(data.mail_subject)
                            mailContent.code(data.mail_content)
                            mailContentText.val(data.mail_plain_content)
                        }
                    })

                    $('ul.nav a[href="#summernote-editor"]').tab('show')
                    $("#email-edit-modal .modal-title").text(`<?= lang('Edit') ?> ${templateNameLang} > ${templateLangWord}`)
                    $("#email-edit-modal").modal('show')
                }
            })
        <?php endif; ?>

        $(".email_template").on('click', ".btn-action", function() {
            let data = $(this).data()

            templateId   = data['id']
            templateLang = data['lang']
            templateLangWord = data['lang_word']
            templateName = data['tmpl_name']
            templateType = data['tmpl_type']
            platformType = data['platf_type']
            templateNameLang = data['tmpl_name_lang']

            mailSubject.val('')
            mailContent.code('')
            mailContentText.val('')

            $.ajax({
                url: '/cms_management/ajax_get_email_template',
                method: "GET",
                async: false,
                data: {
                    template_name: templateName,
                    template_lang: templateLang,
                    platform_type: platformType,
                },
                success: function(data) {
                    mailSubject.val(data.mail_subject)
                    mailContent.code(data.mail_content)
                    mailContentText.val(data.mail_plain_content)
                }
            })

            $('ul.nav a[href="#summernote-editor"]').tab('show')
            $("#email-edit-modal .modal-title").text(`<?= lang('Edit') ?> ${templateNameLang} > ${templateLangWord}`)
            $("#email-edit-modal").modal('show')
        })

        $('#email-submit').click(function(){
            let mailContentVal = mailContent.code(),
                mailSubjectVal = mailSubject.val(),
                mailContentTextVal = mailContentText.val()

            let contentCheck = mailContentVal.replace(/&nbsp;/g, '').replace(/<[^>]*>?/gm, '').trim()


            if (contentCheck == '' || mailSubjectVal == '' || mailContentTextVal == '') {
                $("#email-edit-error-modal").modal('show')
                return
            }

            $.ajax({
                url: '/cms_management/ajax_edit_email_template',
                method: "POST",
                data: {
                    id: templateId,
                    template_lang: templateLang,
                    template_name: templateName,
                    template_type: templateType,
                    platform_type: platformType,
                    mail_subject: mailSubjectVal,
                    mail_content: mailContentVal,
                    mail_content_text: mailContentTextVal,
                },
                success: function(data) {
                    if (data == 1) {
                        $("#email-edit-modal").modal('hide')
                        $("#email-edit-success-modal").modal('show')
                        dtEmailTmpl.ajax.reload();
                    } else {
                        $("#email-edit-error-modal").modal('show')
                    }
                }
            })
        })

        // check if modal content edited
        $("#email-edit-modal").on('keydown', function() {
            $("#email-cancel-edit").data('edited', true)
            $('#email-edit-modal').data('bs.modal').options.backdrop = 'static';
            $('#email-edit-modal').data('bs.modal').options.keyboard = false;
        })
    })

    function checkEdited() {
        let edited = $("#email-cancel-edit").data('edited')

        if(edited == true) {
            $("#email-edit-warning-modal").modal('show')
        }
        else {
            closeEditModal()
        }
    }

    function closeEditModal() {
        $("#email-cancel-edit").data('edited', false)
        $("#email-edit-modal").modal('hide')
    }
</script>