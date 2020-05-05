<style type="text/css">
textarea {
    resize: none;
}
/* The switch - the box around the slider */

.switch {
  position: relative;
  display: inline-block;
  height: 34px;
  top: 19px;
  left: 0;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
  width: 60px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.btn-radius {
    border-radius: 5px;
}
.switch-btn{
    display: inline-block;
    width: 10%;
}
.promo-text{
    display: inline-block;
}

</style>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-note"></i> <?=lang('cms.promoTypeList');?>

            <a href="javascript:void(0);" class="btn  pull-right btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default' ?>" id="addPromoTypeBtn">
                <i class="fa fa-plus-circle"></i> <?=lang('cms.addNewPromoType');?>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12" id="promotype_sec">
                <div class="well" style="overflow: auto;" id="addPlayerTagMngmt">
                    <form class="form-inline" role="form" action="<?=BASEURL . 'marketing_management/addPromoType'?>" method="post" role="form" enctype="multipart/form-data">
                        <div class="form-group col-lg-8 col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="promoTypeName" class="control-label"><?=lang('cms.addNewPromoTypeOrder');?>: </label>
                                    <input type="text" name="promoTypeOrderId" class="form-control input-sm" id="promoTypeOrderId" style="width:100%;">
                                </div>
                                <div class="col-md-6">
                                    <label for="promoTypeName" class="control-label"><?=lang('cms.addpromoTypeName');?>: </label>
                                    <input type="text" name="promoTypeNameView" class="form-control input-sm" id="promoTypeNameView" style="width:100%" required>
                                    <input type="hidden" id="promoTypeName" name="promoTypeName" class="form-control input-sm">
                                    <?php echo form_error('promoTypeName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row" style="padding-top:30px">
                                <div class="col-md-12">
                                    <label for="promoTypeDesc" class="control-label"><?=lang('cms.addpromoTypeDesc');?>: </label><br/>
                                    <textarea name="promoTypeDesc" id="promoTypeDesc" class="form-control input-sm" style="width:100%" style="max-height: 80px;" rows="10" required></textarea>
                                    <?php echo form_error('promoTypeDesc', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="switch-btn">
                                    <label class="switch">
                                        <input type="checkbox" name="useToPromoManager" id="useToPromoManager" value="1">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="promo-text">
                                    </br>
                                    <label><?=lang('enable_promo_category_for_promo_manager');?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 col-md-12" style="padding:12px 0 0 70px;">
                            <label for="promoTypeDesc" class="control-label"><?=lang('Promo Category Icon');?>: </label><br/>
                            <img src="/resources/images/no.png" id="imgPromoCatIcon" alt="" class="img-thumbnail" height="150" width="150">
                            <div class="col-md-12" style="padding-top: 12px;">

                                <label class="btn  btn-sm filPromoCatIcon <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary btn-radius' ?>" style="display: none;">
                                    <input type="file" name="filPromoCatIcon[]" id="filPromoCatIcon" style="display: none;">
                                    <span><?= lang('cms.selectIcon') ?></span>
                                </label>
                                <button class="btn btn-warning btn-sm clearPromoCatIcon" onclick="return clearPromoCatIcon();" style="display: none;"><?= lang('cms.clearIcon') ?></button>
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-md-offset-9" style="padding:30px 0 0 10%;">
                            <input type="reset" value="<?=lang('lang.reset');?>" class="btn  btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>"/>
                            <input type="submit" value="<?=lang('lang.save');?>" class="btn  review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info' ?>"/>
                        </div>
                    </form>
                </div>
                <hr/>
            </div>
            <div class="col-md-12" id="editpromotype_sec">
                <div class="well" style="overflow: auto;" id="addPlayerTagMngmt">
                    <form class="form-inline" role="form" action="<?=BASEURL . 'marketing_management/editPromoType'?>" method="post" role="form" enctype="multipart/form-data">
                        <div class="form-group col-lg-8 col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="promoTypeName" class="control-label"><?=lang('cms.editPromoTypeOrder');?>: </label>
                                    <input type="text" name="editpromoTypeOrderId" class="form-control input-sm" id="editpromoTypeOrderId" style="width:100%">
                                </div>
                                <div class="col-md-6">
                                    <label for="promoTypeName" class="control-label"><?=lang('cms.editpromoTypeName');?>: </label>
                                    <input type="text" name="editpromoTypeNameView" class="form-control input-sm" id="editpromoTypeNameView" style="width:100%">
                                    <input type="hidden" name="promoTypeId" class="form-control" id="promoTypeId">
                                    <input type="hidden" id="editpromoTypeName" name="editpromoTypeName" class="form-control input-sm">
                                </div>
                            </div>
                            <div class="row" style="padding-top:30px">
                                <div class="col-md-12">
                                    <label for="promoTypeDesc" class="control-label"><?=lang('cms.editpromoTypeDesc');?>: </label><br/>
                                    <textarea name="editpromoTypeDesc" id="editpromoTypeDesc" class="form-control input-sm" style="width:100%" style="max-height: 80px;" rows="10" required></textarea>
                                    <?php echo form_error('promoTypeDesc', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="switch-btn">
                                    <label class="switch">
                                        <input type="checkbox" name="editUseToPromoManager" id="editUseToPromoManager" value="1">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="promo-text">
                                    </br>
                                    <label><?=lang('enable_promo_category_for_promo_manager');?></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-md-12" style="padding:12px 0 0 70px;">
                            <label for="promoTypeDesc" class="control-label"><?=lang('Promo Category Icon');?>: </label><br/>
                            <img src="/resources/images/no.png" id="editImgPromoCatIcon" alt="" class="img-thumbnail" height="150" width="150">
                            <input type="hidden" id="promoIconFileName">
                        </div>
                        <div class="form-group col-lg-4 col-md-12" style="padding:0px 0px 0px 48px;">
                            <div class="col-md-12" style="padding-top: 12px;">
                                <label class="btn btn-sm filEditPromoCatIcon <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary btn-radius' ?>" style="display: none;">
                                    <input type="file" name="filEditPromoCatIcon[]" id="filEditPromoCatIcon" style="display: none;">
                                    <span><?= lang('cms.selectIcon') ?></span>
                                </label>
                                <button class="btn btn-warning btn-sm btn-radius clearPromoCatIcon" onclick="return clearPromoCatIcon();" style="display: none;"><?= lang('cms.clearIcon') ?></button>
                                <button class="btn btn-danger btn-sm btn-radius removePromoCatIcon" onclick="return removePromoCatIcon();" style="display: none;"><?= lang('cms.deleteIcon') ?></button>
                            </div>
                        </div>
                        <div class="form-group col-md-3 col-md-offset-9" style="padding:30px 0 0 10%;">
                            <input type="reset" value="<?=lang('lang.reset');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>"/>
                            <input type="submit" value="<?=lang('lang.save');?>" class="btn review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info' ?>"/>
                        </div>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="<?=BASEURL . 'marketing_management/deleteSelectedPromoType'?>" method="post" role="form">

                    <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                        <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                    </button>
                    <hr class="hr_between_table"/>

                    <div id="tag_table" class="table-responsive">
                        <table class="table table-striped table-hover" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="padding: 8px;"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?=lang('cms.promoTypeOrder');?></th>
                                    <th><?=lang('cms.promoTypeName');?></th>
                                    <th><?=lang('cms.promoTypeDesc');?></th>
                                    <th><?=lang('cms.createdby');?></th>
                                    <th><?=lang('cms.updatedby');?></th>
                                    <th><?=lang('pay.createdon');?></th>
                                    <th><?=lang('pay.updatedon');?></th>
                                    <th><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if (!empty($promoType)) {
                                foreach ($promoType as $row) { ?>
                                    <tr>
                                        <td></td>
                                        <td style="padding: 8px;"><input type="checkbox" class="checkWhite" id="<?=$row['promotypeId']?>" name="promoType[]" value="<?=$row['promotypeId']?>" onclick="uncheckAll(this.id)"/></td>
                                        <td><?=$row['promotypeOrder']?></td>
                                        <td><?=lang($row['promoTypeName'])?></td>
                                        <td><?=$row['promoTypeDesc'] ? $row['promoTypeDesc'] : lang('player.tm06')?></td>
                                        <td><?=$row['createdBy']?></td>
                                        <td><?=$row['updatedBy'] ? $row['updatedBy'] : lang('lang.norecord')?></td>
                                        <td><?=$row['createdOn']?></td>
                                        <td><?=$row['updatedOn']?></td>
                                        <td>
                                                <a href="javascript:void(0);" id="editPromoType">
                                                    <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?=lang('cms.editPromoType');?>"  data-placement="top" onclick="getPromotypeDetails(<?=$row['promotypeId']?>,<?= $this->language_function->getCurrentLanguage(); ?>)">
                                                    </span>
                                                </a>
                                                <a href="<?=BASEURL . 'marketing_management/fakeDeletePromoType/' . $row['promotypeId']?>">
                                                    <span class="glyphicon glyphicon-trash delete-promo" data-toggle="tooltip" title="<?=lang('cms.deletePromoType');?>"  data-placement="top">
                                                    </span>
                                                </a>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                        <!-- <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                        </div> -->
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<?php include('promo_type_name_modal.php') ?>

<script type="text/javascript">
    $(document).ready(function(){
          //submenu
        $('#collapseSubmenu').addClass('in');
        $('#promoCategorySettings').addClass('active');

        $('#my_table').DataTable({
            dom: "<'panel-body'<'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            },{
                orderable: false,
                targets:   1
            } ],
            "order": [ 3, 'asc' ]
        });

        $('.delete-promo').click(function(){
            if(confirm("Are you sure you want to delete this?")){
            }
            else{
                return false;
            }
        });

    });
    $('#promotype_sec').hide();
    $('#editpromotype_sec').hide();
    $('#addPromoTypeBtn').click(function() {
        $('.filPromoCatIcon').show();
        $('.clearPromoCatIcon, .removePromoCatIcon').hide();
        $('#imgPromoCatIcon').attr('src', '/resources/images/no.png');

        //check if div is visible
        var promoTypeFlag = $('#promotype_sec').is(':visible');
        if(!promoTypeFlag){
            $('#promotype_sec').show();
            $('#editpromotype_sec').hide();
            $( '#form_promo_name' ).each(function(){
                this.reset();
            });
            // promoTypeFlag = true;
        }else{
            $('#promotype_sec').hide();
            // promoTypeFlag = false;
        }

    });

    function getPromotypeDetails(promotypeId,currentLang){
        $('.filEditPromoCatIcon, .clearPromoCatIcon, .removePromoCatIcon').hide();

        $('#editpromotype_sec').show();
        $('#promotype_sec').hide();
        $.ajax({
            'url' : base_url + 'marketing_management/getPromoTypeDetails/' + promotypeId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data) {
                if (data[0].promoTypeName.toLowerCase().indexOf("_json:") >= 0){
                    var langConvert = jQuery.parseJSON(data[0].promoTypeName.substring(6));
                    $('#editpromoTypeNameView').val(langConvert[currentLang]);
                    $('#editpromoTypeName').val(data[0].promoTypeName);
                } else {
                    $('#editpromoTypeNameView').val(data[0].promoTypeName);
                    $('#editpromoTypeName').val(data[0].promoTypeName);
                }
                $('#editpromoTypeDesc').val(data[0].promoTypeDesc);
                $('#promoTypeId').val(data[0].promotypeId);
                $('#editUseToPromoManager').prop('checked', parseInt(data[0].isUseToPromoManager) > 0 ? true : false);
                $('#editImgPromoCatIcon').attr('src', data[0].icon_path);
                $('#editpromoTypeOrderId').val(data[0].promotypeOrder);
                $('#promoIconFileName').val(data[0].promoIcon);
                if(data[0].promoIcon){
                    $('.removePromoCatIcon').show();
                }else{
                    $('.filEditPromoCatIcon').show();
                }
            }
         });
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }

    function clearPromoCatIcon(){
        var promoTypeId = $('#promoTypeId').val();

        if(promoTypeId){
            $('#editImgPromoCatIcon').attr('src', '/resources/images/no.png');
            $('.clearPromoCatIcon').hide();
        }else{
            $('#imgPromoCatIcon').attr('src', '/resources/images/no.png');
            $('.clearPromoCatIcon').hide();
        }

        return false;
    }

    function removePromoCatIcon(){
        var file = $('#promoIconFileName').val();
        if(!file){
            alert('This promo category icon is not exist now!');
            return false;
        }

        var c = confirm('Are you sure you want to remove promo category icon ?');
        if(!c){
            return false;
        }

        var promoTypeId = $('#promoTypeId').val();

        $.ajax({
            'url' : base_url + 'marketing_management/removePromoCatIcon',
            'type' : 'POST',
            'dataType' : "json",
            'data' : {
                promoTypeId: promoTypeId,
            },
            'success' : function(data) {
                if (data.status){
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            }
        });

        return false;
    }

    function checkAll(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
            }
        } else {
            all.checked;

            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
            }
        }
    }

    function readURL(input, imgSelector) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#'+imgSelector).attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#filPromoCatIcon").change(function(){
        readURL(this, 'imgPromoCatIcon');
        $('.clearPromoCatIcon').show();
    });

    $("#filEditPromoCatIcon").change(function(){
        readURL(this, 'editImgPromoCatIcon');
        $('.clearPromoCatIcon').show();
    });

</script>