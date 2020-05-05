<?php include $this->utils->getIncludeView("popup_promorules_info.php");?>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> <?=lang('Shop Manager');?>
            <a href="#" class="btn btn-primary pull-right" id="add_item_sec">
                <span id="addItemGlyhicon" class="glyphicon glyphicon-plus-sign"></span> <?=lang('Add New Item')?>
            </a>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add vip group -->
        <div class="row add_item_sec">
            <div class="col-md-12">
                <div class="well overFlow">
                    <form class="form-horizontal" action="<?=site_url('marketing_management/addNewshoppingItem')?>" method="post" role="form" id="form-shopping"  accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="title"><?=lang('Title');?>: </label></h6>
                                <input type="text" maxlength="100" id="title" name="title" class="form-control input-sm promoTitleTxt" required>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="short_description"><?=lang('Short Description');?>: </label></h6>
                                <input type="text" maxlength="100" name="short_description"  id="short_description" class="form-control input-sm promoDescTxt" required>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="title"><?=lang('Points needed to claim');?>: </label></h6>
                                <input type="text" id="required_points" name="required_points" class="form-control input-sm requiredPointsTxt number_only" maxlength="10"  required>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="title"><?=lang('How many available?');?> </label></h6>
                                <input type="text" id="how_many_available" name="how_many_available" class="form-control input-sm howManyAvailableTxt number_only" maxlength="5" required>
                            </div>
                        </div>
                        <br/>

                        <!-- Add Banner Upload Module Start -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="promo_banner_sec">
                                    <h6><label for="banner"><?=lang('Banner')?>: </label></h6>

                                    <div class="banner_container">
                                      <img id="banner_600x300" class="banner_600x300"/>
                                      <div class='upload_req_txt'>
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
                                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="set_default_banner" id="set_default_banner"><?php echo lang("Use default banner") ?>
                                    </div>

                                    <div class="presetBannerType">
                                        <img class="presetBannerImg btn" id="shop_banner_temp1" onclick="setBannerImg(this,'banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg')?>" width="130px" height="90px">
                                        <img class="presetBannerImg btn" id="shop_banner_temp2" onclick="setBannerImg(this,'banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp2.jpg')?>" width="130px" height="90px">
                                        <img class="presetBannerImg btn" id="shop_banner_temp3" onclick="setBannerImg(this,'banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp3.jpg')?>" width="130px" height="90px">
                                        <img class="presetBannerImg btn" id="shop_banner_temp4" onclick="setBannerImg(this,'banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp4.jpg')?>" width="130px" height="90px">
                                    </div>

                                    <div class="fileUpload btn btn-md btn-info">
                                        <span><?php echo lang("Upload") ?></span>
                                        <input type="file" name="userfile[]" class="upload" id="userfile" onchange="uploadImage(this,'banner_600x300');">
                                    </div>

                                    <div class="previewBtn btn btn-md btn-success" onclick="showBannerPreview()" data-toggle="modal" data-target=".bannerPreview">
                                            <span><?php echo lang("Preview") ?></span>
                                    </div>
                                </div>
                                <input type="hidden" name="banner_url" id="banner_url" class="form-control">
                                <input type="hidden" name="is_default_banner_flag" id="isDefaultBannerFlag" class="form-control">

                            </div>
                        </div>

                        <!-- Add Banner Upload Module End -->

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="itemDetails"><?=lang('Item Details');?>: </label></h6>
                                <div class="summerNote">
                                    <input name="item_details" type="hidden" class="itemDetails" value="" required/>
                                    <div id="summernote" class="addPromoDetailsTxtPreview"><?=isset($form['itemDetails']) ? $form['itemDetails'] : ''?></div>
                                </div>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <input type="checkbox" name="tagAsNewFlag" id="tagAsNewFlag" value="1" checked>
                                <?php echo lang("Tag as New") ?>

                                <input type="checkbox" name="hideOnPlayerCenter" id="hideOnPlayerCenter">
                                <?php echo lang("Hide it on player center") ?>
                            </div>
                        </div>

                        <center>
                            <br/>
                            <span class="btn btn-success btn-sm" onclick="showShoppingItemPreview()" data-toggle="modal" data-target=".shoppingItemPreview"><?php echo lang("Preview") ?></span>
                            <input type="submit" value="<?=lang('lang.add');?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default item-cancel-btn" data-toggle="modal" /><?=lang('lang.cancel');?></span>
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <!-- edit cms promo -->
        <div class="row edit_item_sec">
            <div class="col-md-12">
                <div class="well overflow">
                    <form action="<?=site_url('marketing_management/addNewshoppingItem')?>" method="post" role="form" id="form-editshopping"  accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="title"><?=lang('Title');?>: </label></h6>
                                <input type="hidden" id="editItemId" name="itemId" class="form-control input-sm" required>
                                <input type="text" maxlength="100" id="editPromoName" name="title" class="form-control input-sm editItemTitleTxt" required>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="short_description"><?=lang('Short Description');?>: </label></h6>
                                <input type="text" maxlength="100" name="short_description" id="editShortDescription" class="form-control input-sm editPromoDescTxt" required>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="title"><?=lang('Points needed to claim');?>: </label></h6>
                                <input type="number" id="editRequiredPoints" name="required_points" class="form-control input-sm requiredPointsTxt" required>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="title"><?=lang('How many available?');?> </label></h6>
                                <input type="text" id="editHowManyAvailable" name="how_many_available" class="form-control input-sm howManyAvailableTxt number_only" maxlength="5" required>
                            </div>
                        </div>
                        <br/>

                         <!-- Edit Banner Upload Module Start -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="promo_banner_sec">
                                    <h6><label for="bannerUrl"><?=lang('Banner')?>: </label></h6>

                                    <div class="banner_container">
                                      <img id="edit_banner_600x300" class="banner_600x300"/>
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
                                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="set_default_banner" id="edit_set_default_banner"><?php echo lang("Use default banner") ?>
                                    </div>

                                    <div class="presetBannerType">
                                        <img class="presetBannerImg btn" id="shop_banner_temp1" onclick="setBannerImg(this,'edit_banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg')?>" width="130px" height="90px">
                                        <img class="presetBannerImg btn" id="shop_banner_temp2" onclick="setBannerImg(this,'edit_banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp2.jpg')?>" width="130px" height="90px">
                                        <img class="presetBannerImg btn" id="shop_banner_temp3" onclick="setBannerImg(this,'edit_banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp3.jpg')?>" width="130px" height="90px">
                                        <img class="presetBannerImg btn" id="shop_banner_temp4" onclick="setBannerImg(this,'edit_banner_600x300')" src="<?=$this->utils->imageUrl('shopping_banner/shop_banner_temp4.jpg')?>" width="130px" height="90px">
                                    </div>

                                    <div class="fileUpload btn btn-md btn-info">
                                        <span><?php echo lang("Upload") ?></span>
                                        <input type="file" name="userfile[]" class="upload" id="userfile" onchange="uploadImage(this,'edit_banner_600x300');">
                                    </div>

                                    <div class="previewBtn btn btn-md btn-success" onclick="showEditBannerPreview()" data-toggle="modal" data-target=".bannerPreview">
                                            <span><?php echo lang("Preview") ?></span>
                                    </div>
                                </div>
                                <input type="hidden" name="banner_url" id="editBannerUrl" class="form-control">
                                <input type="hidden" name="editItemThumbnail" id="editItemThumbnail" >
                                <input type="text" name="is_default_banner_flag" id="isEditDefaultBannerFlag" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" >
                                <h6><label for="itemDetailsLbl"><?=lang('Item Details');?>: </label></h6>
                                <div class="summerNote">
                                    <input name="item_details" type="hidden" class="itemDetails" required/>
                                    <div class="summernote" id="editItemDetails"></div>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="checkbox" name="tagAsNewFlag" id="editTagAsNewFlag">
                                <?php echo lang("Tag as New") ?>

                                <input type="checkbox" name="hideOnPlayerCenter" id="editHideInPlayerCenter">
                                <?php echo lang("Hide it on player center") ?>
                            </div>
                        </div>

                        <center>
                            <br/>
                            <span class="btn btn-success btn-sm" onclick="showEditShoppingItemPreview()" data-toggle="modal" data-target=".shoppingItemPreview"><?php echo lang("Preview") ?></span>
                            <input type="submit" value="<?=lang('lang.save');?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default edit-item-cancel-btn custom-btn-size" data-toggle="modal" /><?=lang('lang.cancel');?></span>
                        </center>
                    </form>
                </div>
            </div>
        </div>

            <!-- Banner Preview Modal Start -->

            <div class="modal fade bannerPreview" tabindex="-1" role="dialog" aria-labelledby="bannerPreview" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <b><?php echo lang("Preview") ?></b>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                            <div class="bannerPreviewContainer">
                                <center>
                                    <img id="preview_banner_600x300" class="preview_banner_600x300" src="">
                                </center>

                                <span class="itemTitlePreview"></span>
                                <span class="shortDescPreview"></span>

                                <div class="pull-right btn btn-xs btn-danger viewPromoDetailsTxt"><span><?php echo lang('View Details') ?></span></div>
                            </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang("Close") ?></button>
                      </div>
                    </div>
                  </div>
              </div>
            </div>

            <!-- Banner Preview Modal End -->

            <!-- Shopping Item Preview Modal Start -->

            <div class="modal fade shoppingItemPreview" tabindex="-1" role="dialog" aria-labelledby="shoppingItemPreview" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <b><?php echo lang("Preview") ?></b>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                            <div class="bannerPreviewContainer">
                                <center>
                                    <img id="preview_banner_600x300_big" class="preview_banner_600x300_big" src="">
                                </center>

                                <span class="itemTitlePreview floatingTxt"></span>

                                <div class="row itemDetailsSec">
                                    <div class="col-md-12">
                                        <div class="col-md-8">
                                            <span class=""><?php echo lang("Details") ?>:</span>
                                            <div class="addItemDetailsSec">
                                                <span class="itemDetailsTxt"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <span class="itemDetailsSecTxt"><?php echo lang("Required Points") ?>:</span>
                                            <div class="bonusAmountSec">
                                                <!-- insert -->
                                                <i class="fa fa-cubes" aria-hidden="true"></i>
                                                <span id="requiredPointsTxtPreview"></spam>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <br/>
                                        <div class="claimNowSec btn">
                                            <?php echo strtoupper(lang("Avail Now")) ?>
                                        </div>
                                        <br/><br/>
                                    </div>
                                </div>



                            </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang("Close") ?></button>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <br/>
            <!-- Promo CMS Preview Modal End -->

        <div class="row">
            <div class="col-md-12">

                <form action="<?=site_url('marketing_management/deleteSelectedShoppingItem')?>" method="post" role="form">
                    <div id="tag_table" class="table-responsive">
                        <table class="table table-bordered table-hover dataTable" id="my_table" style="width:100%;">
                            <div class="">
                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                                    <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                                </button>&nbsp;
                                <?php if ($export_report_permission) {
                                    if (!empty($shoppingItemList)) {?>
                                    <a href="<?=site_url('marketing_management/commonExportToExcel/shopclaimrequestlist')?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?=lang('lang.exporttitle');?>" data-placement="top">
                                        <i class="glyphicon glyphicon-share"></i>
                                    </a>
                                <?php }
                            }
?>
                            </div>
                            <hr class="hr_between_table"/>
                            <thead>
                                <tr>
                                    <th style="padding:8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                    <th><?=lang('Title');?></th>
                                    <th><?=lang('Short Description');?></th>
                                    <th><?=lang('Shopping Banner');?></th>
                                    <th><?=lang('Required Points');?></th>
                                    <th><?=lang('How Many Available');?></th>
                                    <th><?=lang('Tag as New');?></th>
                                    <th><?=lang('cms.createdon');?></th>
                                    <th><?=lang('cms.createdby');?></th>
                                    <th><?=lang('cms.updatedon');?></th>
                                    <th><?=lang('cms.updatedby');?></th>
                                    <th><?=lang('lang.status');?></th>
                                    <th><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
if (!empty($shoppingItemList)) {
	foreach ($shoppingItemList as $key) {
		?>
	                                           <tr <?php echo !$key['status'] ? 'class="danger"' : ''; ?> >
                                                    <td style="padding:8px"><input type="checkbox" class="checkWhite" id="<?=$key['id']?>" name="items[]" value="<?=$key['id']?>" onclick="uncheckAll(this.id)"/></td>
                                                    <td>
                                                      <?=$key['title'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?>
                                                    </td>

                                                    <td>
                                                      <?=$key['short_description'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?>
                                                    </td>

                                                    <td>
                                        <?php
                                        if ($key['is_default_banner_flag']) {
                                            $bannerName = $this->utils->imageUrl('shopping_banner/' . $key['banner_url']);
                                        } else {
                                        if (file_exists($this->utils->getShopThumbnailsPath().$key['promoThumbnail']) &&!empty($key['banner_url'])) {
                                            $bannerName = base_url().'upload/shopthumbnails/'.$key['banner_url'];
                                        } else {
                                            $bannerName = $this->utils->imageUrl('shopping_banner/shop_banner_temp1.jpg');
                                        }
                                        }
                                        ?>
                            <img id="banner_name" src="<?=$bannerName?>" width=100 height=100 >
                                                    </td>
                                                    <td>
                                                      <?php
$requirements = json_decode($key['requirements'])->required_points;
		echo $requirements;
		?>
                                                    </td>
                                                    <td>
                                                      <?=$key['how_many_available'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?>
                                                    </td>
                                                    <td><?=$key['tag_as_new'] ? lang('Yes') : lang('No')?></td>
                                                    <td><?=$key['created_at'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?></td>
                                                    <td><?=$key['created_by'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?></td>
                                                    <td><?=$key['updated_at'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?></td>
                                                    <td><?=$key['updated_by'] ?: '<i class="help-block">' . lang("lang.norecord") . '<i/>'?></td>
                                                    <td><?=$key['status'] ? lang('Active') : lang('Inactive')?></td>
                                                    <td>
                                                        <div class="actionPromoCMS" align="center">
                                                            <?php if ($key['status']) {?>
                                                                <a href="<?=site_url('marketing_management/activateShoppingItem/' . $key['id'] . '/' . 'inactive')?>">
                                                                <span data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="glyphicon glyphicon-ok-sign" data-placement="top">
                                                                </span>
                                                            </a>
                                                            <?php } else {?>
                                                                <a href="<?=site_url('marketing_management/activateShoppingItem/' . $key['id'] . '/' . 'active')?>">
                                                                <span data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="glyphicon glyphicon-remove-circle" data-placement="top">
                                                                </span>
                                                                </a>
                                                            <?php }
		?>
                                                            <span class="glyphicon glyphicon-edit editShoppingItemBtn" data-toggle="tooltip" title="<?=lang('lang.edit');?>" onclick="getShoppingItemDetails(<?=$key['id']?>)" data-placement="top">
                                                            </span>

                                                            <a href="<?=site_url('marketing_management/deleteShoppingItem/' . $key['id'])?>">
                                                                <span data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="glyphicon glyphicon-trash" data-placement="top">
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                <?php }
}?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div><div class="panel-footer"></div>
    </div>


<script type="text/javascript">
    $(document).ready(function(){

        $('#editPromoCode').on("blur", function(){
            //if it's not empty
            if($(this).val()!=''){
                $('.editPromoCMSCode').text($(this).val());
            }

        })

    });


    function checkAllItem(id) {
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

    //checkbox validation
    function valthis() {
        var addPromoCat = document.getElementsByClassName( 'addPromoCat' );
        var isChecked_addPromoCat = false;
        for (var i = 0; i < addPromoCat.length; i++) {
            if ( addPromoCat[i].checked ) {
                isChecked_addPromoCat = true;
            };
        };
        if ( isChecked_addPromoCat ) {
            $('.addPromoCat').attr('required',false);
        } else {
            //alert( 'Please, check at least one checkbox!' );
            $('.addPromoCat').attr('required',true);
        }
    }

    $("#add_item_sec").tooltip({
          placement: "left",
          title: "<?php echo lang("Add New Item"); ?>",
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

    //checkbox for set default banner
    $("#set_default_banner").on('change', function() {
      if ($(this).is(':checked')) {
        $(this).attr('value', 'true');

        $('.upload_req_txt').hide();
        $('.fileUpload').hide();

        //set initial preset banner
        default_item_banner = "<?php echo $this->utils->imageUrl("shopping_banner/shop_banner_temp1.jpg"); ?>";
        $('#banner_600x300').attr('src', default_item_banner).width(600).height(300);

        $('#banner_url').val("shop_banner_temp1.jpg");

        //show preset banner list
        $('.presetBannerType').show();

        $('#isDefaultBannerFlag').val(true);
      } else {
        $(this).attr('value', 'false');
        $('.upload_req_txt').show();

        $('.fileUpload').show();

        //set banner to empty
        $('#banner_600x300').attr('src','');

        //hide preset banner list
        $('.presetBannerType').hide();
      }
    });

    $("#edit_set_default_banner").on('change', function() {
      if ($(this).is(':checked')) {
        $(this).attr('value', 'true');
        $('.upload_req_txt').hide();
        $('.fileUpload').hide();
        $('#edit_upload_req_txt').hide();
        //show preset banner list
        $('.presetBannerType').show();

        $('#isEditDefaultBannerFlag').val(true);

        default_item_banner = "<?php echo $this->utils->imageUrl("shopping_banner/shop_banner_temp1.jpg"); ?>";
        $('#edit_banner_600x300').attr('src', default_item_banner).width(600).height(300);
      } else {
        $(this).attr('value', 'false');
        $('.upload_req_txt').show();
        $('.fileUpload').show();
        $('#edit_banner_600x300').attr('src','').width(600).height(300);

        //hide preset banner list
        $('.presetBannerType').hide();

        $('#isEditDefaultBannerFlag').val(false);
      }
    });

    //upload
    $("#userfile").on('change', function() {
        $('.upload_req_txt').hide();
    });

    function showBannerPreview(){
       var bannerPreviewUrl = $('#banner_600x300').attr('src');
       $('#preview_banner_600x300').attr('src',bannerPreviewUrl).width(600).height(300);
       $('.itemTitlePreview').text($('.promoTitleTxt').val().toUpperCase());
       $('.shortDescPreview').text($('.promoDescTxt').val());
    }

    function showEditBannerPreview(){
       var bannerPreviewUrl = $('#edit_banner_600x300').attr('src');
       $('#preview_banner_600x300').attr('src',bannerPreviewUrl).width(600).height(300);
       $('.itemTitlePreview').text($('.editItemTitleTxt').val().toUpperCase());
       $('.shortDescPreview').text($('.editPromoDescTxt').val());
    }

    function showShoppingItemPreview(){
        console.log($('.promoTitleTxt').val());
       var bannerPreviewUrl = $('#banner_600x300').attr('src');
       $('#preview_banner_600x300_big').attr('src',bannerPreviewUrl).width(600).height(300);
       $('.itemTitlePreview').text($('.promoTitleTxt').val().toUpperCase());
       $('#requiredPointsTxtPreview').text($('#required_points').val());


       $('.itemDetailsTxt').html($('#summernote').summernote('code').code());

       if ($('#tagAsNewFlag').is(':checked')) {
            $('.tagAsNewBadge').show();
       }else{
            $('.tagAsNewBadge').hide();
       }
    }

    function showEditShoppingItemPreview(){
       var bannerPreviewUrl = $('#edit_banner_600x300').attr('src');
       $('#preview_banner_600x300_big').attr('src',bannerPreviewUrl).width(600).height(300);
       $('.itemTitlePreview').text($('.editItemTitleTxt').val().toUpperCase());
       $('.itemDetailsTxt').text($($("#editItemDetails").code()).text());
       $('#requiredPointsTxtPreview').text($('#editRequiredPoints').val());

       if ($('#editTagAsNewFlag').is(':checked')) {
            $('.tagAsNewBadge').show();
       }else{
            $('.tagAsNewBadge').hide();
       }
    }


    $('.editShoppingItemBtn').click(function(){
        $('#edit_upload_req_txt').hide();
    });

    //cancel add promo
      $(".item-cancel-btn").click(function () {
              is_addPanelVisible = false;
              $('.add_item_sec').hide();
              $('#addItemGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
              $('#addItemGlyhicon').addClass('glyphicon glyphicon-plus-sign');
      });

    function getShoppingItemDetails(itemId) {
      is_editPanelVisible = true;
      flag_true = '1';
      $('.add_item_sec').hide();
      $('.edit_item_sec').show();
      targetUrl = _site_url + 'marketing_management/getShoppingItemDetails/' + itemId;

      $.ajax({
          'url' : targetUrl,
          'type' : 'GET',
          'dataType' : "json",
          'success' : function(data){

                console.log(data);
                $('#editPromoCode').val(data.promo_code);
                $('#editItemId').val(itemId);
                $('#editPromoName').val(data.title);

                $('#editRequiredPoints').val($.parseJSON(data.requirements).required_points);
                $('#editBannerUrl').val(data.banner_url);
                $('#editLanguage').val(data.language);
                $('.editPromoCMSCode').text(data.promo_code);

                $('#editShortDescription').val(data.short_description);
                $('#editItemDetails').summernote({focus: true});
                $('#editItemDetails').code(data.details);

                $('#editPromoLink').val(data.promoId);
                $('#editHowManyAvailable').val(data.how_many_available);
                if(data.banner_url){
                    if(data.is_default_banner_flag == flag_true){
                        src_url = _site_url+'resources/images/shopping_banner/'+data.banner_url;
                        $('#edit_set_default_banner').prop('checked',true);
                        $('.fileUpload').hide();

                        //show preset banner list
                        $('.presetBannerType').show();
                    }else{
                        src_url = "<?= base_url().'upload/shopthumbnails/'?>"+data.banner_url;

                        $('#edit_set_default_banner').prop('checked',false);
                        $('.fileUpload').show();
                        //show preset banner list
                        $('.presetBannerType').hide();
                    }
                    $('#edit_banner_600x300').attr('src',(src_url));
                }else{
                    //if banner url is empty, load default banner 1
                    $('#edit_banner_600x300').attr('src',(_site_url+'resources/images/shopping_banner/shop_banner_temp1.jpg'));
                    $('#edit_set_default_banner').prop('checked',true);
                    $('.fileUpload').hide();

                    //show preset banner list
                    $('.presetBannerType').show();
                }

                //set edit tag_as_new_flag
                if(data.tag_as_new==flag_true){
                    $('#editTagAsNewFlag').prop('checked',true);
                }else{
                    $('#editTagAsNewFlag').prop('checked',false);
                }

                //set edit isEditDefaultBannerFlag
                if(data.is_default_banner_flag==flag_true){
                    $('#isEditDefaultBannerFlag').val(true);
                }else{
                    $('#isEditDefaultBannerFlag').val(false);
                }

                //set edit editHideInPlayerCenter
                if(data.hide_it_on_player_center==flag_true){
                    $('#editHideInPlayerCenter').prop('checked',true);
                }else{
                    $('#editHideInPlayerCenter').prop('checked',false);
                }
              }
      },'json');
      return false;
    }

    //for shopping item panel
    var is_addPanelVisible = false;

    //for ranking level edit form
    var is_editPanelVisible = false;

    if(!is_addPanelVisible){
        $('.add_item_sec').hide();
    }else{
        $('.add_item_sec').show();
    }

    if(!is_editPanelVisible){
        $('.edit_item_sec').hide();
    }else{
        $('.edit_item_sec').show();
    }

    if(!is_editPanelVisible){
        $('.edit_cmsfootercontent_sec').hide();
    }else{
        $('.edit_cmsfootercontent_sec').show();
    }

    $('#editItemDetails').summernote({
            height: 300,   //set editable area's height
          });
    //add cms promo details
      $('#form-shopping').submit( function(e) {
          var code = $('#summernote').code();
          // console.log(code);
          if(code != '<p><br></p>'){
            $('.itemDetails').val(code);
            return true;
          }
      });

      //edit cms promo details
      $('#form-editshopping').submit( function(e) {
          var code = $('#editItemDetails').code();
          // console.log('editcmspromo: '+code);
          //if(code != '<p><br></p>'){
            $('.itemDetails').val(code);
            return true;
          //}
      });

    //cancel edit promo
      $(".edit-item-cancel-btn").click(function () {
          is_editPanelVisible = false;
          $('.edit_item_sec').hide();
      });

    //show hide add shopping item panel
    $("#add_item_sec").click(function () {
          if(!is_addPanelVisible){
              is_addPanelVisible = true;
              $('.add_item_sec').show();
              $('.edit_item_sec').hide();
              $('#addItemGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
              $('#addItemGlyhicon').addClass('glyphicon glyphicon-minus-sign');
              randomCode('8');
          }else{

              resetAddNewPromoCmsView();

              is_addPanelVisible = false;
              $('.add_item_sec').hide();
              $('#addItemGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
              $('#addItemGlyhicon').addClass('glyphicon glyphicon-plus-sign');
          }
    });

    function resetAddNewPromoCmsView(){
        //hide the preset banner initially
        $('#title').val("");
        $('.presetBannerType').hide();
        $('#set_default_banner').prop('checked',false);
        $('.upload_req_txt').show();
        $('.fileUpload').show();
        $('#banner_600x300').attr('src','').width(600).height(300);
    }


    //hide the preset banner initially
    $('.presetBannerType').hide();

    function setBannerImg(item,bannerId){
        // console.log("setBannerImg:"+item.id,bannerId);
        bannerType = item.id;
        if(bannerId == 'banner_600x300'){
            $('#isDefaultBannerFlag').val(true);
            $('#banner_url').val(bannerType+".jpg");
        }else{
            $('#isEditDefaultBannerFlag').val(true);
            $('#editBannerUrl').val(bannerType+".jpg");
            $('#edit_upload_req_txt').hide();
        }

        $('#'+bannerId).attr('src',(_site_url+'resources/images/shopping_banner/'+bannerType+'.jpg'));
    }
</script>
