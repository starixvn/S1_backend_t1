<?php
    $uploadUri=$this->utils->getPromoThumbnailRelativePath();

    $view_template = $this->utils->getPlayerCenterTemplate();
    if ($view_template == 'iframe') {
        $show_promo_uri = 'iframe_module/show_promo/';
        $preapplication_promo_uri = 'iframe_module/preapplication/';
        $addto_promo_uri = 'iframe_module/addtopromo/';
    } else {
        $show_promo_uri = 'player_center/show_promo/';
        $preapplication_promo_uri = 'player_center/preapplication/';
        $addto_promo_uri = 'player_center/addtopromo/';
    }
?>

<style type="text/css">
    /*page loader*/
    .loader {
      z-index: 99999;
      display: inline-block;
      position: fixed;
      top: 50%;
      left: 50%;
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      border-bottom: 16px solid #3498db;
      width: 120px;
      height: 120px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .form-control.forread {
       background: linear-gradient(#FFF, #EBEBEB);
    }
</style>

<div class="loader"></div>
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> <?=lang('cms.06');?>
            <a href="#" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-primary'?>" id="add_new_promo_setting">
                <i class="fa fa-plus-circle"></i> <?=lang('cms.addNewPromo2');?> </span>
            </a>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <input type="hidden" id="promoCmsID" class="form-control" readonly>
        <div class="row edit_promocms_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;">
                    <form action="<?=site_url('marketing_management/addNewCmsPromo')?>" method="post" role="form" id="form-editcmspromo"  accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-5 form-group required">
                                <label for="promoCmsCategory"><?=lang('cms.promoCmsCategory');?>: </label>
                                <span class="text-danger error-editPromoCmsCategoryId" hidden><?=sprintf(lang('gen.error.required'), lang('cms.promoCmsCategory'))?></span>
                                <select name="promoCmsCategoryId" id="editPromoCmsCategoryId" class="form-control" required>
                                    <option value=""><?=lang('cms.selectPromoCat');?></option>
                                     <?php foreach ($promoCategoryList as $value) : ?>
                                        <option value="<?=$value['id']?>"><?=lang($value['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5 form-group required">
                                <label for="promoLink"><?=lang('cms.promoRuleSettings');?>: </label>
                                <span class="text-danger error-editPromoLink" hidden><?=sprintf(lang('gen.error.required'), lang('cms.promoRuleSettings'))?></span>
                                <select name="promoLink" id="editPromoLink" class="form-control" required>
                                    <option value="" <?=$this->session->userdata('paymentReportSortByPlayerLevel') == '' ? 'selected' : ''?>>-- <?=lang('cms.selpromolink');?> --</option>
                                    <?php foreach ($promorules as $key => $value) {?>
                                        <option value="<?=$value['promorulesId']?>" <?=($value['used'])?'disabled="disabled"':'';?>><?=$value['promoName']?></option>
                                    <?php } ?>
                               </select>
                            </div>
                            <div class="col-md-2">
                                <label><?=lang('cms.showPromoRulesType');?></label>
                                <div class="form-control input-sm forread">
                                    <span id="" class="showPromoRulesType"></span>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-5 form-group required">
                                <label for="promoName"><?=lang('cms.promotitle');?>: </label>
                                <span class="text-danger error-editpromoName" hidden><?=sprintf(lang('gen.error.required'), lang('cms.promotitle'))?></span>
                                <input type="hidden" id="editPromocmsId" name="promocmsId" class="form-control input-sm" required>
                                <input type="text" maxlength="100" id="editPromoName" name="promoName" class="form-control input-sm editPromoTitleTxt" required>
                            </div>
                            <div class="col-md-5 form-group required">
                                <label for="promoDescription"><?=lang('cms.promodesc');?>: </label>
                                <span class="text-danger error-editPromoDescription" hidden><?=sprintf(lang('gen.error.required'), lang('cms.promodesc'))?></span>
                                <input type="text" maxlength="100" name="promoDescription" id="editPromoDescription" class="form-control input-sm editPromoDescTxt" required>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12 form-group required" >
                                <label for="promoDetails"><?=lang('cms.promodetail');?>: </label>
                                <span class="text-danger error-editPromoDetails" hidden><?=sprintf(lang('gen.error.required'), lang('cms.promodetail'))?></span>
                                <div style="background-color:#fff;">
                                    <input name="promoDetails" type="hidden" class="promoDetails" required/>
                                    <div class="summernote" id="editPromoDetails"></div>
                                    <input type="hidden" name="promoDetailsLength" id="promoDetailsLength">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="checkbox" name="tagAsNewFlag" id="editTagAsNewFlag">
                                <?=lang("Tag as New") ?>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-5">
                                <label><?=lang('cms.promocode');?></label>
                                <div class="input-group">
                                    <input type="text" id="editPromoCode" name="promoCode" class="form-control input-sm editPromoCode" maxlength="15" required>
                                    <div class="input-group-btn">
                                        <a href="javascript:void(0)" class="btn btn-default btn-scooter btn-sm" onclick="randomCode('8','true');" style="border-radius: 0 !important;">
                                        <i class="glyphicon glyphicon-chevron-left"></i><?=lang('aff.ai38') ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="requestPromoLink"><?=lang('Promo Link');?></label>
                                <div class="form-control input-sm forread">
                                    <?=$this->utils->getSystemUrl('player') . '/' . $addto_promo_uri; ?><span id="requestPromoLink" class="editPromoCMSCode"></span>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-5">
                                <label><?=lang('The promotion display in');?></label>
                                <div class="well">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="show_on_player_promotion" name="show_on_player[]" class="show_on_player" value="<?=Promorules::SHOW_ON_PLAYER_PROMOTION?>">
                                                <?=lang('promo.manager.show_on_player.promotion_page')?>
                                            </label>
                                        </div>
                                        <div class="col-md-7">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" id="show_on_player_deposit" name="show_on_player[]" class="show_on_player" value="<?=Promorules::SHOW_ON_PLAYER_DEPOSIT?>">
                                                <?=lang('promo.manager.show_on_player.deposit_page')?>
                                            <br><?=lang('promo.manager.show_on_player.deposit_page_hint')?>
                                        </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-5">
                                <label class="control-label"><?=lang('promo.allow_claim_promo_in_promo_page');?></label>
                                    &nbsp;&nbsp;
                                    <label class="radio-inline">
                                        <input type="radio" class="input-control" name="allow_claim_promo_in_promo_page" value="1" checked="checked">
                                        <span class="label-text"><?=lang('lang.yes')?></span>
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="radio-inline">
                                        <input type="radio" class="input-control" name="allow_claim_promo_in_promo_page" value="0">
                                        <span class="label-text"><?=lang('lang.no')?></span>
                                    </label>
                                <div class="row">
                                    <div class="col col-md-5">
                                        <label class="control-label" for="claim_button_link"><?=lang('promo.claim_promo_btn_link');?></label>
                                        <select type="text" class="form-control" id="claim_button_link" name="claim_button_link">
                                            <option value="deposit"><?=lang('Deposit')?></option>
                                        </select>
                                    </div>
                                    <div class="col col-md-7">
                                        <label class="control-label" for="claim_button_name"><?=lang('promo.claim_promo_btn_name');?></label>
                                        <input type="text" class="form-control" id="claim_button_name" name="claim_button_name" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <?php if ($this->utils->isEnabledFeature('enable_multi_lang_promo_manager')) { ?>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="col-md-5">
                                        <label for=""><?=lang('Select default language');?>
                                            <select name="promo_item_default_lang" id="edit_promo_item_default_lang" class="form-control" style="width: 185%;">
                                                <?php foreach ($systemLanguages as $lang) { ?>
                                                    <option value="<?=$lang['short_code']?>"><?=$lang['word']?></option>
                                                <?php } ?>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col-md-7" style="padding-top: 16px;">
                                        <div class="btn btn-md btn-primary" data-toggle="modal" onclick="setPromoItemDefaultLanguage()">
                                            <span><?=lang("Setup Multi Language") ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br/>
                        <?php } ?>

                        <!-- Edit Banner Upload Module Start -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="promo_banner_sec">
                                    <h6><label for="promoThumbnail"><?=lang('Banner')?>: </label></h6>
                                    <div class="banner_container">
                                        <img id="edit_promo_cms_banner_600x300" class="promo_cms_banner_600x300" style="width: 600px; height: 300px;"/>
                                        <div class='upload_req_txt' id="edit_upload_req_txt">
                                            <?php
                                                echo "600px x 300px<br/>";
                                                echo "JPEG,PNG,GIF<br/>";
                                                echo lang("File must not exceed 2MB.");
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="promo_banner_sec upload_btn_sec">
                                    <div class="">
                                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="set_default_banner" id="edit_set_default_banner"> <label for="edit_set_default_banner"><?=lang("Use default banner") ?></label>
                                    </div>
                                    <div class="presetBannerType">
                                        <img class="presetBannerImg btn" id="default_promo_cms_1" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_1.jpg')?>" width="130px">
                                        <img class="presetBannerImg btn" id="default_promo_cms_2" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_2.jpg')?>" width="130px">
                                        <img class="presetBannerImg btn" id="default_promo_cms_3" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_3.jpg')?>" width="130px">
                                        <img class="presetBannerImg btn" id="default_promo_cms_4" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_4.jpg')?>" width="130px">
                                        <br/>
                                        <img class="presetBannerImg btn" id="default_promo_cms_5" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_5.jpg')?>" width="130px">
                                        <img class="presetBannerImg btn" id="default_promo_cms_6" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_6.jpg')?>" width="130px">
                                        <img class="presetBannerImg btn" id="default_promo_cms_7" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_7.jpg')?>" width="130px">
                                        <img class="presetBannerImg btn" id="default_promo_cms_8" onclick="setBannerImg(this,'edit_promo_cms_banner_600x300')" src="<?=$this->utils->imageUrl('promothumbnails/default_promo_cms_8.jpg')?>" width="130px">
                                    </div>

                                    <div class="fileUpload btn btn-md btn-info">
                                        <span><?=lang("Upload") ?></span>
                                        <input type="file" name="userfile[]" class="upload" id="userfile" onchange="uploadImage(this,'edit_promo_cms_banner_600x300');">
                                    </div>
                                    <div class="previewBtn btn btn-md <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success' ?>" onclick="showEditBannerPreview()" data-toggle="modal" data-target=".bannerPreview">
                                        <span><?=lang("Preview") ?></span>
                                    </div>
                                </div>
                                <input type="hidden" name="banner_url" id="editBannerUrl" class="form-control" readonly>
                                <input type="hidden" name="editPromoThumbnail" id="editPromoThumbnail" >
                                <input type="hidden" name="is_default_banner_flag" id="isEditDefaultBannerFlag" class="form-control" readonly>
                            </div>
                        </div>
                        <br/>
                        <center>
                            <br/>
                            <span class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success' ?>" onclick="showEditPromoCmsPreview()" data-toggle="modal" data-target=".promoCmsPreview"><?=lang("Preview") ?></span>
                            <span class="btn btn-sm editcmspromo-cancel-btn custom-btn-size <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>" data-toggle="modal"><?=lang('lang.cancel');?></span>
                            <input type="submit" value="<?=lang('lang.save');?>" class="btn btn-sm review-btn custom-btn-size <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info' ?>" data-toggle="modal" id="edit_promo_submit" />
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="tag_table" class="table-responsive">
                    <table class="table table-bordered table-hover dataTable" id="promo_list_table" style="width:100%;">
                        <hr class="hr_between_table"/>

                        <thead>
                            <tr>
                                <th ><?=lang('cms.promotitle');?></th>
                                <th ><?=lang('cms.promoCat');?></th>
                                <th ><?=lang('Short Description');?></th>
                                <th ><?=lang('cms.promothumb');?></th>
                                <th ><?=lang('cms.promolink');?></th>
                                <th ><?=lang('Tag as New');?></th>
                                <th ><?=lang('cms.createdon');?></th>
                                <th ><?=lang('cms.createdby');?></th>
                                <th ><?=lang('cms.updatedon');?></th>
                                <th ><?=lang('cms.updatedby');?></th>
                                <th ><?=lang('lang.status');?></th>
                                <th ><?=lang('lang.action');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($promoList)) {
                                foreach ($promoList as $data) { ?>
                                    <tr <?=$data['status'] == 'inactive' ? 'class="danger"' : ''; ?> >
                                        <td>
                                            <?=$data['promoName'] == '' ?
                                                '<i class="help-block">' . lang("lang.norecord") . '<i/>' :
                                                '<span data-toggle="tooltip" title="' . lang('tool.cms05') . '" data-placement="top">
                                                <a href="javascript:void(0)" onclick="getPromoCmsDetails(' . $data['promoCmsSettingId'] . ', false, null, ' . $data['promorulesId'] . ')">' . $data['promoName'] . '</a></span>'?>
                                        </td>
                                        <td>
                                            <?=lang($data['promoTypeName'])?>
                                        </td>
                                        <td><?=$data['promoDescription'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $data['promoDescription']?></td>
                                        <?php
                                            if(file_exists($this->utils->getPromoThumbnails().$data['promoThumbnail']) && !empty($data['promoThumbnail'])) {
                                                $promoThumbnail = $uploadUri . $data['promoThumbnail'];
                                            } else {
                                                if(!empty($data['promoThumbnail'])){
                                                    $promoThumbnail = $this->utils->imageUrl('promothumbnails/'.$data['promoThumbnail']);
                                                } else {
                                                    $promoThumbnail = $this->utils->imageUrl('promothumbnails/default_promo_cms_1.jpg');
                                                }
                                            }
                                        ?>
                                        <td><img id="banner_name" src="<?=$promoThumbnail?>" width=100></td>
                                        <td>
                                            <?php if ($data['promoRuleName'] == '') {?>
                                                <i class="help-block"><?=lang("lang.norecord")?></i>
                                            <?php } else {
                                                ?>
                                                <a href="/marketing_management/editPromoRule/<?=$data['promorulesId']?>" target="_blank"><?=$data['promoRuleName']; // createPromoDetailButton($data['promorulesId'], $data['promoName']); ?></a>
                                            <?php } ?>
                                        </td>
                                        <td><?=$data['tag_as_new_flag'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $data['tag_as_new_flag'] ? lang('Yes') : lang('No')?></td>
                                        <td><?=$data['createdOn'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $data['createdOn']?></td>
                                        <td><?=$data['createdBy'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $data['createdBy']?></td>
                                        <td><?=$data['updatedOn'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $data['updatedOn']?></td>
                                        <td><?=$data['updatedBy'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $data['updatedBy']?></td>
                                        <td><?=$data['status'] == '' ? '<i class="help-block">' . lang("cms.nodailymaxwithdrawal") . '<i/>' : $data['status']?></td>
                                        <td>
                                            <div class="actionPromoCMS" align="center">
                                                <a href="/marketing_management/dryrun_promo/<?=$data['promoCmsSettingId'];?>"><?=lang('Dry Run');?></a>
                                                <?php if ($data['status'] == 'active') {?>
                                                    <a href="<?=site_url('marketing_management/activatePromoCms/' . $data['promoCmsSettingId'] . '/' . 'inactive')?>">
                                                        <span data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="glyphicon glyphicon-ok-sign" data-placement="top"></span>
                                                    </a>
                                                <?php } else {?>
                                                    <a href="<?=site_url('marketing_management/activatePromoCms/' . $data['promoCmsSettingId'] . '/' . 'active')?>">
                                                        <span data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="glyphicon glyphicon-remove-circle" data-placement="top"></span>
                                                    </a>
                                                <?php } ?>
                                                <span class="glyphicon glyphicon-edit editCmsPromoBtn" data-toggle="tooltip" title="<?=lang('lang.edit');?>" onclick="getPromoCmsDetails(<?=$data['promoCmsSettingId']?>, false, null, <?=$data['promorulesId']?>)" data-placement="top">
                                                </span>
                                                <a data-toggle="modal" data-target="#deletePromoCmsItemModal" class="deletePromoCmsItem" data-id="<?=$data['promoCmsSettingId']?>">
                                                    <span data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="glyphicon glyphicon-trash" data-placement="top"></span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
                            }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>

<!-- Delete Promo Cms Item Modal Start -->
<div class="modal fade" id="deletePromoCmsItemModal" tabindex="-1" role="dialog" aria-labelledby="deletePromoCmsItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deletePromoCmsItemModalLabel"><?=lang('cms.deletePromoManager')?></h4>
            </div>
            <div class="modal-body">
                <?=lang('cms.deletePromoManagerMsg')?>
            </div>
            <div class="modal-footer">
                <input type="hidden" class="deletePromoCmsId">
                <input type="hidden" class="deleteSelectedPromoCmsId">
                <button type="button" class="btn btn-primary delete-func" onclick="deletePromoCmsItem()"><?=lang('Confirm')?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('lang.cancel')?></button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Promo Cms Item Modal End -->

<!-- Banner Preview Modal Start -->
<div class="modal fade bannerPreview" tabindex="-1" role="dialog" aria-labelledby="bannerPreview" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <b><?=lang("Preview") ?></b>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="bannerPreviewContainer">
                        <center>
                            <img id="preview_promo_cms_banner_600x300" class="preview_promo_cms_banner_600x300" src="">
                        </center>

                            <div class="promoTitlePreview promoText"></div>
                            <div class="shortDescPreview promoText"></div>
                        <div class="pull-right btn btn-xs btn-danger viewPromoDetailsTxt"><span><?=lang('View Promo Details') ?></span></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang("Close") ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner Preview Modal End -->

<!-- Multi lang Modal Start -->
<div class="modal fade multiLangWindow" tabindex="-1" role="dialog" aria-labelledby="multiLangWindow" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <b><?=lang("Setup Multi Language") ?></b>
                    <button type="button" class="close close-multiLang-modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="multiLangWindowContainer">
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default" id="panel_en">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-target="#collapseCh"
                                           href="#collapseCh">
                                            <?=lang("English"); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseCh" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <?=lang("cms.promotitle") ?>
                                        <input type="text" id="promo_title_en" class="form-control"/>
                                        <br/>
                                        <?=lang("Short Description") ?>
                                        <textarea id="short_desc_en" class="form-control"></textarea>
                                        <br/>
                                        <?=lang('cms.promodetail')?>
                                        <div style="background-color:#fff;">
                                            <div id="details_en"></div>
                                        </div>
                                        <br/>
                                        <?=lang("Upload Banner")?>:
                                        <form id="upload_imgbnr_form_en" enctype="multipart/form-data" method="post">
                                            <input type="file" name="userfile[]" id="upload_imgbnr_btn_en" data-image-upload="uploadimg_en" />
                                            <input type="hidden" name="banner_en" id="banner_en" />
                                            <br/>
                                            <img id="uploadimg_en" width="600" height="300" src="#"/>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default" id="panel_ch">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-target="#collapseCh"
                                           href="#collapseCh">
                                            <?=lang("Chinese"); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseCh" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <?=lang("cms.promotitle") ?>
                                        <input type="text" id="promo_title_ch" class="form-control"/>
                                        <br/>
                                        <?=lang("Short Description") ?>
                                        <textarea id="short_desc_ch" class="form-control"></textarea>
                                        <br/>
                                        <?=lang('cms.promodetail')?>
                                        <div style="background-color:#fff;">
                                            <div id="details_ch"></div>
                                        </div>
                                        <br/>
                                        <?=lang("Upload Banner")?>:
                                        <form id="upload_imgbnr_form_ch" enctype="multipart/form-data">
                                            <input type="file" name="userfile[]" id="upload_imgbnr_btn_ch" data-image-upload="uploadimg_ch" />
                                            <input type="hidden" name="banner_ch" id="banner_ch" />
                                            <br/>
                                            <img id="uploadimg_ch" width="600" height="300" src="#" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default" id="panel_kr">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-target="#collapseKr"
                                           href="#collapseKr" class="collapsed">
                                            <?=lang("Korean"); ?>
                                        </a>
                                    </h4>

                                </div>
                                <div id="collapseKr" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?=lang("cms.promotitle") ?>
                                        <input type="text" id="promo_title_kr" class="form-control"/>
                                        <br/>
                                        <?=lang("Short Description") ?>
                                        <textarea id="short_desc_kr" class="form-control"></textarea>
                                        <br/>
                                        <?=lang('cms.promodetail')?>
                                        <div style="background-color:#fff;">
                                            <div id="details_kr"></div>
                                        </div>
                                        <br/>
                                        <form id="upload_imgbnr_form_kr" enctype="multipart/form-data">
                                            <input type="file" name="userfile[]" id="upload_imgbnr_btn_kr" data-image-upload="uploadimg_kr" />
                                            <input type="hidden" name="banner_kr" id="banner_kr" />
                                            <br/>
                                            <img id="uploadimg_kr" width="600" height="300" src="#" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default" id="panel_id">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-target="#collapseInd"
                                           href="#collapseInd" class="collapsed">
                                            <?=lang("Indonesian"); ?>
                                        </a>
                                    </h4>

                                </div>
                                <div id="collapseInd" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?=lang("cms.promotitle") ?>
                                        <input type="text" id="promo_title_id" class="form-control"/>
                                        <br/>
                                        <?=lang("Short Description") ?>
                                        <textarea id="short_desc_id" class="form-control"></textarea>

                                        <br/>
                                        <?=lang('cms.promodetail')?>
                                        <div style="background-color:#fff;">
                                            <div id="details_id"></div>
                                        </div>
                                        <br/>
                                        <form id="upload_imgbnr_form_id" enctype="multipart/form-data">
                                            <input type='file' name="userfile[]" id="upload_imgbnr_btn_id" data-image-upload="uploadimg_id" />
                                            <input type="hidden" name="banner_id" id="banner_id" />
                                            <br/>
                                            <img id="uploadimg_id" width="600" height="300" src="#" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default" id="panel_vn">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-target="#collapseVn" href="#collapseVn" class="collapsed">
                                            <?=lang("Vietnamese"); ?>
                                        </a>
                                    </h4>

                                </div>
                                <div id="collapseVn" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?=lang("cms.promotitle") ?>
                                        <input type="text" id="promo_title_vn" class="form-control"/>
                                        <br/>
                                        <?=lang("Short Description") ?>
                                        <textarea id="short_desc_vn" class="form-control"></textarea>

                                        <br/>
                                        <?=lang('cms.promodetail')?>
                                        <div style="background-color:#fff;">
                                            <div id="details_vn"></div>
                                        </div>
                                        <br/>
                                        <form id="upload_imgbnr_form_vn" enctype="multipart/form-data">
                                            <input type='file' name="userfile[]" id="upload_imgbnr_btn_vn" data-image-upload="uploadimg_vn" />
                                            <input type="hidden" name="banner_vn" id="banner_vn" />
                                            <br/>
                                            <img id="uploadimg_vn" width="600" height="300" src="#" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default" id="panel_th">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-target="#collapseTh"
                                           href="#collapseTh" class="collapsed">
                                            <?=lang("Thai"); ?>
                                        </a>
                                    </h4>

                                </div>
                                <div id="collapseTh" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?=lang("cms.promotitle") ?>
                                        <input type="text" id="promo_title_th" class="form-control"/>
                                        <br/>
                                        <?=lang("Short Description") ?>
                                        <textarea id="short_desc_th" class="form-control"></textarea>

                                        <br/>
                                        <?=lang('cms.promodetail')?>
                                        <div style="background-color:#fff;">
                                            <div id="details_th"></div>
                                        </div>
                                        <br/>
                                        <form id="upload_imgbnr_form_th" enctype="multipart/form-data">
                                            <input type='file' name="userfile[]" id="upload_imgbnr_btn_th" data-image-upload="uploadimg_th" />
                                            <input type="hidden" name="banner_th" id="banner_th" />
                                            <br/>
                                            <img id="uploadimg_th" width="600" height="300" src="#" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveMultiLangBtn" class="btn btn-primary" data-dismiss="modal"><?=lang("Save") ?></button>
                    <button type="button" class="btn btn-danger close-multiLang-modal"><?=lang("Close") ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner Preview Modal End -->

<!-- Promo CMS Preview Modal Start -->
<div class="modal fade promoCmsPreview" tabindex="-1" role="dialog" aria-labelledby="promoCmsPreview" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <b><?=lang("Preview") ?></b>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="bannerPreviewContainer">
                        <center>
                            <img id="preview_promo_cms_banner_600x300_big" class="preview_promo_cms_banner_600x300_big" src="">
                        </center>
                        <span class="promoTitlePreview floatingTxt"></span>
                        <div class="row promoDetailsSec">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    <div class="badge tagAsNewBadge">
                                        <strong><?=strtoupper(lang("New")) ?></strong>
                                    </div>
                                    <br/>
                                    <strong><?=lang("Promo Type") ?></strong>:<span id="promoTypeTxt"></span>
                                </div>
                                <div class="col-md-4">
                                    <span class="promoDetailsSecTxt"><?=lang("Bonus Amount") ?>:</span>
                                    <div class="bonusAmountSec">
                                        $200
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="addPromoDetailsSec">
                                        <span class="promoDetailsTxt"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <br/>
                                <div class="claimNowSec btn">
                                    <?=strtoupper(lang("Claim Now")) ?>
                                </div>
                                <br/><br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang("Close") ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<br/>
<!-- Promo CMS Preview Modal End -->

<script type="text/javascript">
    $(document).ready(function(){
        $('#details_en,#details_ch,#details_kr,#details_id,#details_vn,#details_th').summernote({
            enterHtml : '<p></br><p>'
        });

        //remove upload image button
        $('.note-editor .note-toolbar .note-insert button[data-event~="showImageDialog"]').remove();

        var dataTable = $('#promo_list_table').DataTable({
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            autoWidth: false,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-linkwater' : 'btn btn-sm btn-default' ?>'
                }
            ],
            columnDefs: [ {orderable: false,targets: [0] } ],
            order: [[ 6, 'desc' ]],
            drawCallback: function () {
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                    dataTable.buttons().disable();
                }
                else {
                    dataTable.buttons().enable();
                }
            }
        });

        $('#editPromoCode').on("blur", function(){
            //if it's not empty
            if($(this).val()!=''){
                $('.editPromoCMSCode').text($(this).val());
            }
        });

        $(".close-multiLang-modal").click (function () {
            if(confirm("Unsaved changes will disregard. Are you sure you want to close this?")){
                $(".multiLangWindow").modal('toggle');
                getPromoCmsDetails($('#promoCmsID').val(), true);
            }
        });

        $('.deletePromoCmsItem').on('click', function(){
            var val = $(this).attr('data-id');
            $('.deletePromoCmsId').val(val);
        });

    });

    function uploadImage(input,id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.upload_req_txt').hide();
                $('#'+id).attr('src', e.target.result).width(600).height(300);
                $('#banner_url').val(input.files[0].name);
                $('#editBannerUrl').val(input.files[0].name);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#edit_set_default_banner").on('change', function() {
        if ($(this).is(':checked')) {
            $(this).attr('value', 'true');
            $('.upload_req_txt').hide();
            $('.fileUpload').hide();
            $('#edit_upload_req_txt').hide();
            //show preset banner list
            $('.presetBannerType').show();

            $('#isEditDefaultBannerFlag').val(true);

            default_promo_cms_banner = "<?=$this->utils->imageUrl($this->utils->getConfig('default_promo_cms_banner_url')); ?>";
            $('#edit_promo_cms_banner_600x300').attr('src', default_promo_cms_banner).width(600).height(300);
        } else {
            $(this).attr('value', 'false');
            $('.upload_req_txt').show();
            $('.fileUpload').show();
            $('#edit_promo_cms_banner_600x300').attr('src','').width(600).height(300);

            //hide preset banner list
            $('.presetBannerType').hide();

            $('#isDefaultBannerFlag').val(false);
        }
    });

    //upload
    $("#userfile").on('change', function() {
        $('.upload_req_txt').hide();
    });

    $('[name="allow_claim_promo_in_promo_page"]').change(function(){
        if(!!parseInt($(this).val())){
            $('#claim_button_name').parent().hide();
            $('#claim_button_link').parent().hide();
        }else{
            $('#claim_button_name').parent().show();
            $('#claim_button_link').parent().show();
        }
    });
    $('[name="allow_claim_promo_in_promo_page"]:checked').trigger('change');

    function showEditBannerPreview(){
        var bannerPreviewUrl = $('#edit_promo_cms_banner_600x300').attr('src');
        $('#preview_promo_cms_banner_600x300').attr('src',bannerPreviewUrl).width(600).height(300);
        $('.promoTitlePreview').text($('.editPromoTitleTxt').val().toUpperCase());
        $('.shortDescPreview').text($('.editPromoDescTxt').val());
    }

    function showEditPromoCmsPreview(){
        var bannerPreviewUrl = $('#edit_promo_cms_banner_600x300').attr('src');
        $('#preview_promo_cms_banner_600x300_big').attr('src',bannerPreviewUrl).width(600).height(300);
        $('.promoTitlePreview').text($('.editPromoTitleTxt').val().toUpperCase());
        $('.promoDetailsTxt').html($("#editPromoDetails").code());

        var promo_type_name = $("#editPromoCmsCategoryId option:selected").text();
        $('#promoTypeTxt').text(promo_type_name);

        if ($('#editTagAsNewFlag').is(':checked')) {
            $('.tagAsNewBadge').show();
        }else{
            $('.tagAsNewBadge').hide();
        }
    }

    $('.editCmsPromoBtn').click(function(){
        $('#edit_upload_req_txt').hide();
    });

    $('#editPromoCmsCategoryId').change(function(){
        var promoCategoryId = $('#editPromoCmsCategoryId').val();
        var promorule = <?=json_encode($promorules)?>;
        $('#editPromoLink').val('');
        $('.showPromoRulesType').empty();
        $.each(promorule ,function (k,v) {
            if(v.promoCategory != promoCategoryId){
                $("#editPromoLink option[value="+v.promorulesId+"]").addClass('hide');
            }else{
                $("#editPromoLink option[value="+v.promorulesId+"]").removeClass('hide');
            }
        });
    });

    $('#editPromoLink').change(function(){
        var promorulesId = $('#editPromoLink').val();

        if(promorulesId == ''){
            $('.showPromoRulesType').empty();
        }

        var promorule = <?=json_encode($promorules)?>;
        $.each(promorule ,function (k,v) {
            if(v.promorulesId == promorulesId){
                if(v.promoType == '<?=Promorules::PROMO_TYPE_DEPOSIT?>'){
                    $('.showPromoRulesType').text("<?=lang("cms.depPromo")?>");
                    $('#show_on_player_deposit').removeAttr("disabled");
                }else{
                    $('.showPromoRulesType').text("<?=lang("cms.nonDepPromo")?>");
                    $('#show_on_player_deposit').attr("disabled", true);
                }
            }
        });
    });

    var isEditPromoFlag = false;

    function resetPromoruleSettings(promorulId){
        var promorule = <?=json_encode($promorules)?>;
        $.each(promorule ,function (k,v) {
            if(v.promorulesId != promorulId){
                $("#editPromoLink option[value="+v.promorulesId+"]").addClass('hide');
            }
        });
    }

    function getPromoCmsDetails(promocmsId, multiLangOnly = false, selectedLang = null, promorulId = null) {
        is_editPanelVisible = true;
        flag_true = '1';

        if(promorulId){
            resetPromoruleSettings(promorulId);
            $('#editPromoLink>option[value="' + promorulId +'"]').removeAttr('disabled');
        }

        $('.edit_promocms_sec').show();
        $('#promoCmsID').val(promocmsId);
        targetUrl = _site_url + 'marketing_management/getPromoCmsItemDetails/' + promocmsId;

        $.ajax({
            'url': targetUrl,
            'type': 'GET',
            'dataType': "json",
            'success': function(data){
                if(multiLangOnly == false && selectedLang == null) {
                    $('#editPromoCode').val(data.promo_code);
                    $('#editPromocmsId').val(promocmsId);
                    $('#editPromoName').val(data.promoName);
                    $('#u_visit_limit').val(parseInt(data.visit_limit));
                    $('#editPromoThumbnail').val(data.promoThumbnail);
                    $('#editLanguage').val(data.language);
                    $('.editPromoCMSCode').text(data.promo_code);

                    $("#editPromoCmsCategoryId").val(data.promo_category);

                    if(data.promoDepositType == '<?=Promorules::PROMO_TYPE_DEPOSIT?>'){
                        $('.showPromoRulesType').text("<?=lang("cms.depPromo")?>");
                        $('#show_on_player_deposit').removeAttr("disabled");
                    }else{
                        $('.showPromoRulesType').text("<?=lang("cms.nonDepPromo")?>");
                        $('#show_on_player_deposit').attr("disabled", true);
                    }

                    var show_on_player = parseInt(data.hide_on_player);
                    if (show_on_player == '<?=Promorules::SHOW_ON_PLAYER_PROMOTION?>') {
                        $('#show_on_player_promotion').prop('checked', true);
                    }else if(show_on_player == '<?=Promorules::SHOW_ON_PLAYER_DEPOSIT?>'){
                        $('#show_on_player_deposit').prop('checked', true);
                    }else if(show_on_player == '<?=Promorules::SHOW_ON_PLAYER_PROMOTION_AND_DEPOSIT?>'){
                        $('#show_on_player_promotion').prop('checked', true);
                        $('#show_on_player_deposit').prop('checked', true);
                    }else{
                        $('#show_on_player_promotion').prop('checked', false);
                        $('#show_on_player_deposit').prop('checked', false);
                    }

                    if (data.promo_code == null) {
                        $('.editPromoCodeLinkSec').hide();
                    } else {
                        $('.editPromoCodeLinkSec').show();
                    }

                    $('#editPromoDescription').val(data.promoDescription);
                    $("#editPromoDetails").next('.note-editor').find('.note-editable').html(_pubutils.decodeHtmlEntities(data.promoDetails, data.default_lang));
                    $('#editPromoLink').val(data.promoId);

                    if (data.promoThumbnail != null) {
                        if (data.is_default_banner_flag == flag_true) {
                            $('#edit_promo_cms_banner_600x300').attr('src', (_site_url + 'resources/images/promothumbnails/' + data.promoThumbnail));
                            $('#isEditDefaultBannerFlag').val('true');
                            $('#editBannerUrl').val(data.promoThumbnail);
                        } else {
                            $('#edit_promo_cms_banner_600x300').attr('src', ("<?=$uploadUri?>" + data.promoThumbnail));
                        }
                        $('#edit_upload_req_txt').hide();
                    } else {
                        $('#edit_promo_cms_banner_600x300').attr('src', (_site_url + 'resources/images/promothumbnails/default_promo_cms_1.jpg'));
                    }

                    if (data.is_default_banner_flag == flag_true) {

                        $('#edit_set_default_banner').prop('checked', true);
                        $('.fileUpload').hide();

                        //show preset banner list
                        $('.presetBannerType').show();
                    }

                    //set edit tag_as_new_flag
                    if (data.tag_as_new_flag == flag_true) {
                        $('#editTagAsNewFlag').prop('checked', true);
                        $('#isDefaultBannerFlag').val(true);
                    } else {
                        $('#editTagAsNewFlag').prop('checked', false);
                        $('#isDefaultBannerFlag').val(false);
                    }

                    //set edit tag_as_new_flag
                    if (!!parseInt(data.allow_claim_promo_in_promo_page)) {
                        $('[name="allow_claim_promo_in_promo_page"][value="1"]').prop('checked', true).trigger('change');
                    } else {
                        $('[name="allow_claim_promo_in_promo_page"][value="0"]').prop('checked', true).trigger('change');
                    }

                    $("#claim_button_link").val(data.claim_button_link);

                    $("#claim_button_name").val(data.claim_button_name);
                }
                // OGP-5225
                var isEnabledMultiLangPromoMgr = "<?=$this->utils->isEnabledFeature('enable_multi_lang_promo_manager')?>";
                if(isEnabledMultiLangPromoMgr){
                    var promoMultiLangData = JSON.parse(data.promo_multi_lang);
                    $.each(promoMultiLangData, function(index, value){
                        $.each(value, function(index2, value2){
                            var promo_title_key = "promo_title_" + index2;
                            var short_desc_key = "short_desc_" + index2;
                            var details_key = "details_" + index2;
                            var banner_key = "banner_"+index2;

                            $("#" + promo_title_key).val(value2[promo_title_key]);
                            $("#" + short_desc_key).val(value2[short_desc_key]);
                            $("#" + details_key).next('.note-editor').find('.note-editable').html(_pubutils.decodeHtmlEntities(value2[details_key], index2));
                            $("#"+banner_key).val(value2[banner_key]);
                            var bannerSrcPath = "<?=$uploadUri?>";
                            $("#uploadimg_"+index2).attr("src",bannerSrcPath+value2[banner_key]);
                            $("#uploadimg_"+index2).show();

                            if(index2 == selectedLang){
                                $('#editPromoName').val(value2[promo_title_key]);
                                $('#editPromoDescription').val(value2[short_desc_key]);
                                $("#editPromoDetails").next('.note-editor').find('.note-editable').html(_pubutils.decodeHtmlEntities(value2[details_key], selectedLang));

                                if( value2[banner_key] != null ) {
                                    $('#edit_promo_cms_banner_600x300').attr('src',bannerSrcPath+value2[banner_key]);
                                    $('#editBannerUrl').val(value2[banner_key]);
                                } else {
                                    $('#edit_promo_cms_banner_600x300').attr('src', ("<?=$uploadUri?>" + value2[banner_key]));
                                }
                            }
                        });
                    });

                    if(multiLangOnly == false && selectedLang == null) {
                        $('#edit_promo_item_default_lang option[value=' + data.default_lang + ']').prop('selected', true);
                    }
                    isEditPromoFlag = true;

                    // set multi lang data to form from db
                    saveMultiLangData();
                }
            }
        }, 'json');
        return false;
    }

    //for ranking level edit form
    var is_editPanelVisible = false;
    $('.edit_promocms_sec').hide();

    //show hide add promo cms panel
    $("#add_new_promo_setting").click(function(){
        // resetPromoruleSettings();
        resetAddNewPromoCmsView();
        clearMultiLangPromo();

        if(!is_editPanelVisible){
            is_editPanelVisible = true;
            $('.edit_promocms_sec').show();
        }else{
            is_editPanelVisible = false;
            $('.edit_promocms_sec').hide();
        }
    });

    //cancel edit promo
    $(".editcmspromo-cancel-btn").click(function () {
        is_editPanelVisible = false;
        $('.edit_promocms_sec').hide();
    });

    function resetAddNewPromoCmsView(){
        //hide the preset banner initially
        $('#editPromocmsId').val('');
        $('.showPromoRulesType').empty();
        $('#editPromoCmsCategoryId').val('');
        $('#editPromoLink').val('');

        randomCode('8', 'true');

        $('#show_on_player_promotion').trigger('click');

        $('#editPromoName').val('');
        $('#editPromoDescription').val('');
        $('#editPromoDetails').code('');

        $('#edit_set_default_banner').prop('checked', true).trigger('change');

        $('#editTagAsNewFlag').prop('checked', true);
    }

    //hide the preset banner initially
    $('.presetBannerType').hide();

    function setBannerImg(item,bannerId){
        bannerType = item.id;
        if(bannerId == 'promo_cms_banner_600x300'){
            $('#isDefaultBannerFlag').val(true);
            $('#banner_url').val(bannerType+".jpg");
        }else{
            $('#isEditDefaultBannerFlag').val(true);
            $('#editBannerUrl').val(bannerType+".jpg");
            $('#edit_upload_req_txt').hide();
        }

        $('#'+bannerId).attr('src',(_site_url+'resources/images/promo_cms/'+bannerType+'.jpg'));
    }

    $('#edit_promo_item_default_lang').change(function(){
        getPromoCmsDetails($('#promoCmsID').val(),false, $(this).val());
    });

    $("#edit_promo_submit").click(function () {
        var promoLink = $('#editPromoLink').val();
        var promoName = $('.editPromoTitleTxt').val();
        var promoDescription = $('.editPromoDescTxt').val();
        var promoCategory =$('#editPromoCmsCategoryId').val();
        var notValidate = false;

        var promoDetails = $("#editPromoDetails").code();
        var encodePromoDetails = encode64(encodeURIComponent(promoDetails));
        var promoDetailsLength = encodePromoDetails.length;
        $("#editPromoDetails").code(encodePromoDetails);
        $("#promoDetailsLength").val(promoDetailsLength);

        $(".error-promoLink").hide();
        $(".error-promoName").hide();
        $(".error-promoDescription").hide();
        $(".error-promoDetails").hide();
        $('.error-editPromoCmsCategoryId').hide();

        if(promoLink.length == 0) {
            $(".error-editPromoLink").show();
            notValidate = true;
        }

        if(promoName.length == 0) {
            $(".error-editpromoName").show();
            notValidate = true;
        }

        if(promoDescription.length == 0) {
            $(".error-editPromoDescription").show();
            notValidate = true;
        }

        if(promoDetails.length == 0) {
            $(".error-editPromoDetails").show();
            notValidate = true;
        }

        if(promoCategory.length == 0) {
            $(".error-editPromoCmsCategoryId").show();
            notValidate = true;
        }

        if(notValidate){
            alert("<?=lang('con.d02')?>");
        } else {
            submitMultiLangBannerUpload();
            $("#form-editcmspromo").submit();
        }

        return false;
    });

    function clearMultiLangPromo(){
        $('#promo_title_en').val("");
        $('#short_desc_en').val("");
        $('#details_en').code("");
        $('#banner_en').val("");
        $('#uploadimg_en').attr("src","#").hide();

        $('#promo_title_ch').val("");
        $('#short_desc_ch').val("");
        $('#details_ch').code("");
        $('#banner_ch').val("");
        $('#uploadimg_ch').attr("src","#").hide();

        $('#promo_title_kr').val("");
        $('#short_desc_kr').val("");
        $('#details_kr').code("");
        $('#banner_kr').val("");
        $('#uploadimg_kr').attr("src","#").hide();

        $('#promo_title_id').val("");
        $('#short_desc_id').val("");
        $('#details_id').code("");
        $('#banner_id').val("");
        $('#uploadimg_id').attr("src","#").hide();

        $('#promo_title_vn').val("");
        $('#short_desc_vn').val("");
        $('#details_vn').code("");
        $('#banner_vn').val("");
        $('#uploadimg_vn').attr("src","#").hide();

        $('#promo_title_th').val("");
        $('#short_desc_th').val("");
        $('#details_th').code("");
        $('#banner_th').val("");
        $('#uploadimg_th').attr("src","#").hide();
    }

    // OGP-5225
    $("#saveMultiLangBtn").click(function () {
        saveMultiLangData();
    });

    function saveMultiLangData(){
        var promoTitleEn = $('#promo_title_en').val();
        var shortDescEn = $('#short_desc_en').val();
        var detailsEn = $('#details_en').code();
        detailsEn = encode64(encodeURIComponent(detailsEn));
        var bannerEn = $('#banner_en').val();

        var promoTitleCh = $('#promo_title_ch').val();
        var shortDescCh = $('#short_desc_ch').val();
        var detailsCh = $('#details_ch').code();
        detailsCh = encode64(encodeURIComponent(detailsCh));
        var bannerCh = $('#banner_ch').val();

        var promoTitleKr = $('#promo_title_kr').val();
        var shortDescKr = $('#short_desc_kr').val();
        var detailsKr = $('#details_kr').code();
        detailsKr = encode64(encodeURIComponent(detailsKr));
        var bannerKr = $('#banner_kr').val();

        var promoTitleId = $('#promo_title_id').val();
        var shortDescId = $('#short_desc_id').val();
        var detailsId = $('#details_id').code();
        detailsId = encode64(encodeURIComponent(detailsId));
        var bannerId = $('#banner_id').val();

        var promoTitleVn = $('#promo_title_vn').val();
        var shortDescVn = $('#short_desc_vn').val();
        var detailsVn = $('#details_vn').code();
        detailsVn = encode64(encodeURIComponent(detailsVn));
        var bannerVn = $('#banner_vn').val();

        var promoTitleTh = $('#promo_title_th').val();
        var shortDescTh = $('#short_desc_th').val();
        var detailsTh = $('#details_th').code();
        detailsTh = encode64(encodeURIComponent(detailsTh));
        var bannerTh = $('#banner_th').val();

        var postUrl = "<?=site_url('marketing_management/savePromoCmsMultiLang')?>";
        var postData = {
            promo_title_en : promoTitleEn,
            short_desc_en : shortDescEn,
            details_en : detailsEn,
            banner_en : bannerEn,
            promo_title_ch : promoTitleCh,
            short_desc_ch : shortDescCh,
            details_ch : detailsCh,
            banner_ch : bannerCh,
            promo_title_kr : promoTitleKr,
            short_desc_kr : shortDescKr,
            details_kr : detailsKr,
            banner_kr : bannerKr,
            promo_title_id : promoTitleId,
            short_desc_id : shortDescId,
            details_id : detailsId,
            banner_id : bannerId,
            promo_title_vn : promoTitleVn,
            short_desc_vn : shortDescVn,
            details_vn : detailsVn,
            banner_vn : bannerVn,
            promo_title_th : promoTitleTh,
            short_desc_th : shortDescTh,
            details_th : detailsTh,
            banner_th : bannerTh
        };

        $.post(postUrl, postData, function(data){});
    }

    // OGP-5225
    function setPromoItemDefaultLanguage(){
        $("#panel_en,#panel_ch,#panel_kr,#panel_id,#panel_vn").show();
        var selected_default_lang;
        if(isEditPromoFlag){
            selected_default_lang = $('#edit_promo_item_default_lang option:selected').val();
        }else{
            selected_default_lang = $('#promo_item_default_lang option:selected').val();
        }
        $("#panel_"+selected_default_lang).hide();
        $('.multiLangWindow').modal({backdrop: 'static', keyboard: false});
    }

    function readURL() {
        var targetImg = "#"+this.getAttribute("data-image-upload");
        //  rehide the image and remove its current "src",
        //  this way if the new image doesn't load,
        //  then the image element is "gone" for now
        $(targetImg).attr('src', '').hide();
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            $(reader).load(function(e) {
                $(targetImg)
                    //  first we set the attribute of "src" thus changing the image link
                    .attr('src', e.target.result)   //  this will now call the load event on the image

                var targetLang = targetImg.substr(targetImg.indexOf('_') + 1);
                var uploadedFileName = $("#upload_imgbnr_btn_"+targetLang).val().replace(/C:\\fakepath\\/i, '');
                var uploadedFileNameExt = uploadedFileName.substr(uploadedFileName.indexOf('.') + 1);
                var fileName = "";

                var uploadedBannerName = $("#banner_"+targetLang).val();
                if(uploadedBannerName == ""){
                    fileName = "promomgr_item_"+Math.random().toString(36).substring(7);
                }else{
                    fileName = uploadedBannerName.substr(0, uploadedBannerName.indexOf('.'));
                }
                $('#banner_'+targetLang).val(fileName+"."+uploadedFileNameExt);
            });
            reader.readAsDataURL(this.files[0]);

        }
    }

    //  below makes use of jQuery chaining. This means the same element is returned after each method, so we don't need to call it again
    $('#uploadimg_ch,#uploadimg_en,#uploadimg_id,#uploadimg_kr,#uploadimg_vn,#uploadimg_th')
        //  here we first set a "load" event for the image that will cause it change it's height to a set variable
        //      and make it "show" when finished loading
        .load(function(e) {
            //  $(this) is the jQuery OBJECT of this which is the element we've called on this load method
            $(this)
                //  note how easy adding css is, just create an object of the css you want to change or a key/value pair of STRINGS
                .css('height', '300px') //  or .css({ height: '200px' })
                //  now for the next "method" in the chain, we show the image when loaded
                .show();    //  just that simple
        })
        //  with the load event set, we now hide the image as it has nothing in it to start with
        .hide();    //  done

    $("#upload_imgbnr_btn_ch,#upload_imgbnr_btn_en,#upload_imgbnr_btn_id,#upload_imgbnr_btn_kr,#upload_imgbnr_btn_vn,#upload_imgbnr_btn_th").change(readURL);

    function submitMultiLangBannerUpload(){
        $(".loader").show();
        var bannerNameCh = $("#banner_ch").val();
        if(bannerNameCh != "" && $("#upload_imgbnr_btn_ch")[0].files.length > 0){
            var form_ch = document.getElementById('upload_imgbnr_form_ch');
            var formDataCh = new FormData(form_ch);
            formDataCh.append('file', userfile);
            uploadMultiLangBanner(bannerNameCh,formDataCh);
        }

        var bannerNameKr = $("#banner_kr").val();
        if(bannerNameKr != "" && $("#upload_imgbnr_btn_kr")[0].files.length > 0){
            var form_kr = document.getElementById('upload_imgbnr_form_kr');
            var formDataKr = new FormData(form_kr);
            formDataKr.append('file', userfile);
            uploadMultiLangBanner(bannerNameKr,formDataKr);
        }

        var bannerNameId = $("#banner_id").val();
        if(bannerNameId != "" && $("#upload_imgbnr_btn_id")[0].files.length > 0){
            var form_id = document.getElementById('upload_imgbnr_form_id');
            var formDataId = new FormData(form_id);
            formDataId.append('file', userfile);
            uploadMultiLangBanner(bannerNameId,formDataId);
        }

        var bannerNameVn = $("#banner_vn").val();
        if(bannerNameVn != "" && $("#upload_imgbnr_btn_vn")[0].files.length > 0){
            var form_vn = document.getElementById('upload_imgbnr_form_vn');
            var formDataVn = new FormData(form_vn);
            formDataVn.append('file', userfile);
            uploadMultiLangBanner(bannerNameVn,formDataVn);
        }

        var bannerNameTh = $("#banner_th").val();
        if(bannerNameTh != "" && $("#upload_imgbnr_btn_th")[0].files.length > 0){
            var form_th = document.getElementById('upload_imgbnr_form_th');
            var formDataTh = new FormData(form_th);
            formDataTh.append('file', userfile);
            uploadMultiLangBanner(bannerNameTh,formDataTh);
        }

        var bannerNameEn = $("#banner_en").val();
        if(bannerNameEn != "" && $("#upload_imgbnr_btn_en")[0].files.length > 0){
            var form_en = document.getElementById('upload_imgbnr_form_en');
            var formDataEn = new FormData(form_en);
            formDataEn.append('file', userfile);
            uploadMultiLangBanner(bannerNameEn,formDataEn);
        }
    }

    // multi upload loader
    $('.loader').hide();
    function uploadMultiLangBanner(name,formData){
        $.ajax({
            url: "<?=site_url('marketing_management/uploadBannerMultiLang')?>/"+name,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            async: false,
            success: function(data){
                // alert(data);
                $(".loader").show();
            },
            beforeSend: function(){
                $(".loader").show();
            }
        });
    }

    //single select id
    function deletePromoCmsItem(){
        var del_id = $('.deletePromoCmsId').val();
        var url = "<?=site_url('marketing_management/deletePromoCmsItem')?>" + "/" + del_id;
        $.post(url, {del_id}).done(function(data){
            if(data && data.success){
                window.location.reload();
            }
        }).fail(function(){
            alert('Delete Promo Cms Failed');
        });

    }

    var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    //turn string to base64 encode string
    function encode64(input) {
        var output = "";
        var chr1, chr2, chr3 = "";
        var enc1, enc2, enc3, enc4 = "";
        var i = 0;

        do {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                keyStr.charAt(enc1) +
                keyStr.charAt(enc2) +
                keyStr.charAt(enc3) +
                keyStr.charAt(enc4);
            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";
        } while (i < input.length);

        return output;
    }
</script>