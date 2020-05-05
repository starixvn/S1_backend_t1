<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseAffiliateBanner" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseAffiliateBanner" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form class="" id ="search-form" action="<?=site_url('affiliate_management/bannerSearchPage')?>" method="post" role="form" name="myForm">
                <div class="col-md-4">
                    <label for="start_date" class="control-label"><?=lang('aff.vb08');?> </label>
                    <div class="form-group">
                        <div class="input-group">
                    <input type="text" class="form-control input-sm dateInput" id="filterDate" data-start="#start_date" data-end="#end_date"
                        <?php echo isset($input['enabled_date']) && $input['enabled_date'] ? "" : "disabled='disabled'";?> />
                    <span class="input-group-addon"><input type="checkbox" name="enabled_date" id="enabled_date" value="true" <?php echo isset($input['enabled_date']) && $input['enabled_date'] ? "checked='checked'" : "";?> ></span>
                        </div>
                    <input type="hidden" name="start_date" id="start_date" value="<?=(isset($input['start_date']) ? $input['start_date'] : '')?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?=(isset($input['end_date']) ? $input['end_date'] : '')?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="input-group">
                    <label for="status" class="control-label"><?=lang('aff.vb10');?>: </label>
                    <select name="status" class="form-control input-sm">
                        <option value="" ><?=lang('aff.vb11');?></option>
                        <option value="active" <?=$input['status'] == 'active' ? 'selected' : ''?>><?=lang('aff.vb12');?></option>
                        <option value="inactive" <?=$input['status'] == 'inactive' ? 'selected' : ''?>><?=lang('aff.vb13');?></option>
                    </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2" style="padding-top:23px;">
                    <div class="form-group">
                        <input type="button" value="<?=lang('aff.vb15');?>" onclick= "resetForm()" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>">
                        <input type="submit" value="<?=lang('aff.vb14');?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info'?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end of main-->

<!-- display banner -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt pull-left">
            <i class="glyphicon glyphicon-picture"></i> <?=lang('aff.vb24');?>
         <!--    <button id="addAffTagMngmtGlyph"  class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-plus-sign"></i></button> -->
        </h4>
        <a href="#" class="btn btn-info pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs' : 'btn-sm'?>" id="add_banner_group">
            <i class="fa fa-plus-circle"></i> <?=lang('aff.t09');?>
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="panel-body" id="details_panel_body">

        <!-- add banner -->
        <div class="row">
            <div class="col-md-12">
                <div class="well" style="overflow: auto" id="add_banner_form">

                <h4>
                <span id="form-mode-icon"class="glyphicon glyphicon-plus-sign"></span>

                  <span id="form-mode-title"> <?=lang('aff.t09');?></span>
                </h4><hr>

                    <form class="form-horizontal" id="banner-upload-form" action="<?=site_url('affiliate_management/actionBanner')?>" method="POST" role="form" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label for="bannerName" class="control-label"><?=lang('aff.vb34');?> </label>
                                <input type="hidden" name="bannerId" class="form-control" id="bannerId">
                                <input type="hidden" name="changes_has_made" value="0"class="form-control" id="changes_has_made">
                                <input type="text" required name="bannerName" class="form-control input-sm" id="bannerName" placeholder="<?=lang('tool.am16')?>" value="<?php set_value('bannerName')?>">
                                <?php echo form_error('bannerName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                <span id="error-bannerName" class="help-block" style="color:#ff6666;font-size:11px;"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="bannerLanguage" class="control-label"><?=lang('aff.vb35');?> </label>
                                <select name="bannerLanguage" required id="bannerLanguage" class="form-control input-sm">
                                    <option value=""><?=lang('aff.vb36');?></option>
                                    <option value="English" <?=(set_value('bannerLanguage') == "English") ? 'selected' : ''?>><?=lang('aff.vb37');?></option>
                                    <option value="Chinese" <?=(set_value('bannerLanguage') == "Chinese") ? 'selected' : ''?>><?=lang('aff.vb38');?></option>
                                </select>
                                <?php echo form_error('bannerLanguage', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                <span id="error-bannerLanguage" class="help-block" style="color:#ff6666;font-size:11px;"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="txtImage" class="control-label"><?=lang('aff.vb40');?> </label>
                                <input type="hidden" name="banner_url" id="banner_url">
                            <span id="form-input-file-container">
                               <input required type="file" id="txtImage" name="txtImage" class="form-control input-sm" onchange="setURL(this.value);" value="<?=set_value('txtImage');?>">
                            </span>
                                <span id="error-txtImage" class="help-block" style="color:green;font-size:11px;"></span>
                                 <span id="error-txtImage-2" class="help-block" style="color:#ff6666;font-size:11px;"></span>
                               <span style ="display:none;"class="help-block text-success" id="pic-desc"><i><b><?=lang('tool.am17')?>: </b><span id="banner-size">88KB</span></i> &nbsp;&nbsp;&nbsp;&nbsp;  <i><b><?=lang('tool.am18')?>: </b><span id="banner-format">mpeg</span></i>   </span>
                                <img id="img-prev" src="#" style="display:none;max-width:100px;height:40px;" />
                                <input type="button" style="display:none;" class="btn btn-xs btn-danger" id="change-image"value="<?=lang('payment.changeImage')?>"  />
                            </div>
                        </div>
                        <div class="form-group" style="text-align:center;margin-top:21px;">
                            <input type="reset" id="reset-upload-form" value="<?=lang('aff.vb42');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"/>
                            <input type="button" id="cancel-edit" value="<?=lang('lang.cancel');?>" class="btn btn-default btn-sm"/>
                            <input type="submit" id="btn-submit_" value="<?=lang('aff.vb41');?>" class="btn review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info'?>"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            <!-- end of add banner -->

        <form action="<?=site_url('affiliate_management/deleteSelectedBanner')?>" id="delete_form" method="post" role="form">
            <div class="row">
                <div class="col-md-12 col-md-offset-0" id="bannerList">
                    <table class="table table-bordered table-hover dataTable" id="bannerTable" style="width: 100%;">
                        <div class="btn-action">
                            <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('aff.vb25');?>">
                                <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                            </button>
                        </div>
                        <dir class="clearfix"></dir>
                        <thead>
                            <tr>
                                <th></th>
                                <th style="padding:8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                <th><?=lang('aff.vb26');?></th>
                                <th><?=lang('aff.vb27');?></th>
                                <th><?=lang('aff.vb28');?></th>
                                <th><?=lang('aff.vb29');?></th>
                                <th><?=lang('aff.vb30');?></th>
                                <th><?=lang('aff.vb31');?></th>
                                <th><?=lang('aff.vb32');?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
if (!empty($banner)) {
	foreach ($banner as $row) {
                // $pub_image_link=$this->utils->getSystemUrl('player', '/pub/banner/'.$row['bannerId']);
                $pub_image_link=site_url('/affiliate_management/get_banner/'.$row['bannerId']);
		?>
                                <tr>
                                    <td></td>
                                    <td style="padding:8px"><input type="checkbox" class="checkWhite" id="<?=$row['bannerId']?>" name="banner[]" value="<?=$row['bannerId']?>" onclick="uncheckAll(this.id)"/></td>
                                    <td><?=$row['createdOn']?></td>
                                    <td><?=$row['bannerName']?></td>
                                    <td><?php echo !empty($row['width']) && !empty($row['height']) ? $row['width'] . " x " . $row['height'] : lang('N/A'); ?></td>
                                    <td><?=$row['language']?></td>
                                    <td>
                                        <div><a href="<?php echo $pub_image_link;?>" target="_blank"><?php echo $pub_image_link;?></a> </div>
                                    <?php if(!empty($row['bannerURL'])){
                                        $imageUrl=site_url('/affiliate_management/get_banner/'.$row['bannerId']);
                                        ?>
                                    <a href="#" onclick="window.open('<?php echo $imageUrl;?>','_blank', 'width=<?=$row['width']?>,height=<?=$row['height']?>,scrollbars=yes,status=yes,resizable=no,screenx=0,screeny=0')"><img src="<?php echo $imageUrl; ?>" style="width=100px; height: 40px;"/></a>
                                    <?php }else{
                                        echo lang('N/A');
                                     }?>
                                    </td>
                                    <td><?=($row['status'] == 0) ? lang('Active') : lang('Inactive')?></td>
                                    <td>
                                        <a href="#editbanner" class="editbanner">
                                            <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?=lang('tool.am08');?>"  data-placement="top" onclick="getBannerDetails(<?=$row['bannerId']?>)">
                                            </span>
                                        </a>
                                        <a href="#delete" onclick="deleteBanner('<?=$row['bannerId']?>', '<?=$row['bannerName']?>')">
                                            <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?=lang('tool.am09');?>"  data-placement="top">
                                            </span>
                                        </a>

                                        <?php if ($row['status'] == 0) {?>
                                            <a href="#deactivate" onclick="deactivateBanner('<?=$row['bannerId']?>', '<?=$row['bannerName']?>')">
                                                <span class="glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="<?=lang('tool.am10');?>"  data-placement="top">
                                                </span>
                                            </a>
                                        <?php } else {?>
                                            <a href="#activate" onclick="activateBanner('<?=$row['bannerId']?>', '<?=$row['bannerName']?>')">
                                                <span class="glyphicon glyphicon-ok-sign" data-toggle="tooltip" title="<?=lang('tool.am11');?>"  data-placement="top">
                                                </span>
                                            </a>
                                        <?php }?>
                                    </td>
                                </tr>
                            <?php }
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-footer"></div>
</div>
<!-- end of display banner -->

<script type="text/javascript">

    function resetForm(){
        $('#search-form')[0].reset();
        $('#start_date').val("<?php echo $start_date = date("Y-m-d"); ?>");
        $('#end_date').val("<?php echo $end_date = date("Y-m-d"); ?>");
        $('#filterDate').val("<?php echo $start_date = date("Y-m-d") . ' to ' . $end_date = date("Y-m-d"); ?>");
        var checked=$(this).is(":checked");
        $("#filterDate").prop("disabled", !checked);
        
    }

    $(document).ready(function(){


  /*------------FORM DATATABLE START-------------*/

        $('#bannerTable').DataTable( {
            dom: "<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
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
            "order": [ 2, 'desc' ],
            //"dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        } );

 /*------------FORM DATATABLE END-------------*/


 /*------------FORM VALIDATION START-------------*/

var error = [],
    bannerId = $('#bannerId'),
    changesHasMade = $('#changes_has_made'),
    bannerName= $('#bannerName'),
    bannerLanguage = $('#bannerLanguage'),
    fileInput = $('#txtImage'),
    bannerSizeView = $('#banner-size'),
    picDescView = $('#pic-desc'),
    bannerFormatView = $('#banner-format'),
    bannerUploadForm = $('#banner-upload-form'),
    submit =$('#btn-submit_'),
    reset =$('#reset-upload-form'),
    cancelEdit = $('#cancel-edit'),
    helpBlock =$('.help-block'),
    imagePrev =$('#img-prev'),
    changeImage = $('#change-image'),
    VALIDATION_URL = '<?php echo site_url('affiliate_management/validateThruAjax') ?>',
    IS_EDIT_MODE = false,
    IS_FILE_CHOSEN = false,
    BANNER_NAME_LABEL = "<?=lang('aff.vb18');?>",
    BANNER_LANG_LABEL ="<?=lang('aff.vb35');?>",
    FILE_INPUT_LABEL = "<?=lang('aff.vb40');?>"
;

    //Set style to file input to customize languages
     fileInput.filestyle({
         'buttonBefore': true,
         'placeholder' : '<?=lang("tool.am15")?>',
         'buttonText': '<?=lang("tool.am14")?>',
         'iconName' : 'glyphicon glyphicon-plus'
       });



    if(!IS_EDIT_MODE){
       cancelEdit.hide();
    }


    bannerName.blur(function(){

        if(requiredCheck($(this).val(),'bannerName',BANNER_NAME_LABEL)){
              validateThruAjax($(this).val(),'bannerName',BANNER_NAME_LABEL)
        }

    });


    bannerLanguage.blur(function(){
      requiredCheck($(this).val(),'bannerLanguage',BANNER_LANG_LABEL);
    });




   fileInput.bind('change', function() {

    if(requiredCheckHelp(fileInput.val(),'txtImage',FILE_INPUT_LABEL)){
       if(checkChosenFileIfAccepted(this.files[0].name,'txtImage',FILE_INPUT_LABEL)) {

            showImageDesc(this.files[0]);
            IS_FILE_CHOSEN = true;
            readShowImageURL(this);
            changesHasMade.val('1');

       }else{
          imagePrev.hide();
           IS_FILE_CHOSEN = false;
           changesHasMade.val(0);

       }
    }


     });

    fileInput.blur(function(){
        requiredCheckHelp($(this).val(),'txtImage',FILE_INPUT_LABEL)
    });

    //During edit
    changeImage.click(function(){
      fileInput.filestyle('disabled',false);
      $(this).hide();
      requiredCheckHelp(fileInput.val(),'txtImage',FILE_INPUT_LABEL);
     });


    bannerUploadForm.submit(function(){


    if(IS_EDIT_MODE) {

          if(bannerName.val() &&  bannerLanguage.val()){

            var errorLength = error.length;
            if(errorLength > 0){
                return false;
            }else{
                disableSubmitButton();
                }
        }else{
            return false;
        }

    }else{

         if(bannerName.val() &&  bannerLanguage.val() && (IS_FILE_CHOSEN) ){

            var errorLength = error.length;
            if(errorLength > 0){
                return false;
            }else{
                disableSubmitButton();
                }
        }else{
            return false;
        }
    }


    });

    reset.on('click', function(){
        resetFormUpload();
    });

    cancelEdit.on('click', function(){
       showAddBannerForm();
       changesHasMade.val(0);
    });




function showAddBannerForm(){
    addBannerForm.hide();
    bannerName.val("");
    bannerLanguage.val("");
    bannerId.val("");
    picDescView.hide();
    imagePrev.hide();
    cancelEdit.hide();
    changeImage.hide();
    changeImage.hide();
    reset.show();
    fileInput.filestyle('clear');
    fileInput.filestyle('disabled',false);
    removeErrorOnField("txtImage");
    removeErrorOnField("bannerName");
    removeErrorOnField("bannerLanguage");
    formModeTitle.html("Add Banner");
    formModeIcon.removeClass('glyphicon-pencil').addClass('glyphicon-plus-sign');

}

function resetFormUpload(){

    error =[];
    picDescView.hide();
    IS_FILE_CHOSEN = false;
    removeErrorOnField('txtImage-2');
    removeErrorOnField('bannerName');
    removeErrorOnField('bannerLanguage');
    imagePrev.hide();
    fileInput.filestyle('clear');

}

function clearFileFormatView(){

    bannerSizeView.html("");
    picDescView.hide();
    bannerFormatView.html("");

}


function showImageDesc(FILE){

   bannerSizeView.html(bytesToSize(FILE.size ));
   bannerFormatView.html(FILE.type);
   picDescView.show();
}

function bytesToSize(bytes) {
       var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
       if (bytes == 0) return '0 Byte';
       var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
       return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};


function readShowImageURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
               imagePrev.attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
 }



function validateThruAjax(fieldVal,id,label){

var  data = {bannerName:fieldVal};

 $.ajax({
        url : VALIDATION_URL,
        type : 'POST',
        data : data,
        dataType : "json",
        cache : false,
      }).done(function (data) {
        if (data.status == "success") {
            removeErrorItem(id);
            removeErrorOnField(id);
        }
        if (data.status == "error") {
            var message = data.msg;
            showErrorOnField(id,message);
            addErrorItem(id);
        }
      }).fail(function (jqXHR, textStatus) {
        /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
        if(jqXHR.status>=300 && jqXHR.status<500){
            location.reload();
        }else{
            alert(textStatus);
        }
      });
}


function checkChosenFileIfAccepted(fieldVal,id,label){
    var message = "Your file chosen is not supported! Please select an image.(ex:jpg, jpeg, gif, png) ";
    var allowedExt =["jpg", "jpeg", "gif", "png"],
    fileExtension = fieldVal.replace(/^.*\./, '');
    if(jQuery.inArray(fileExtension, allowedExt) != -1){
        removeErrorItem(id);
        removeErrorOnField(id+'-2');
         return true;
    }else{
       showErrorOnField(id+'-2',message);
       addErrorItem(id);
        return false;
    }
}

function requiredCheck(fieldVal,id,label){
    var message = label+" is required";
    if(!fieldVal && (fieldVal == "")){
        showErrorOnField(id,message)
        addErrorItem(id);
        return false;
    }else{
        removeErrorOnField(id);
        removeErrorItem(id);

        return true;
    }
}
function requiredCheckHelp(fieldVal,id,label){
    var message = " Select your "+label;
    if(!fieldVal && (fieldVal == "")){
        showErrorOnField(id,message)
        addErrorItem(id);
        return false;
    }else{
        removeErrorOnField(id);
        removeErrorItem(id);

        return true;
    }
}

function showErrorOnField(id,message){
    $('#error-'+id).html(message);
}

function removeErrorOnField(id){
    $('#error-'+id).html("");
}

function removeErrorItem(item){

    var i = error.indexOf(item);
        if(i != -1) {
            error.splice(i, 1);
        }

 }

 function addErrorItem(item){
    if(jQuery.inArray(item, error) == -1){
            error.push(item);

    }

 }

function disableSubmitButton(){
    submit.prop('disabled', true);

}
function ableSubmitButton(){
    submit.prop('disabled', false);

}


/*------------FORM VALIDATION END-------------*/


/*------------FORM TOGGLE START-------------*/

//add_banner_group

    is_bannerFormVisible = false,
    is_editBannerFormVisible = false,
    addBannerForm = $('#add_banner_form'),
    formModeTitle = $('#form-mode-title'),
    formModeIcon = $('#form-mode-icon')
    ;


    if(!is_bannerFormVisible){
        addBannerForm.hide();
    }else{
       addBannerForm.show();
    }



    $('#add_banner_group').click(function () {

        if(!is_bannerFormVisible){
            is_bannerFormVisible = true;
            addBannerForm.show();
         }else{
            is_bannerFormVisible = false;
             addBannerForm.hide();
        }
    });

     $(".editbanner").click(function () {
        is_editBannerFormVisible = true;
        formModeTitle.html("Edit Banner");
        formModeIcon.removeClass('glyphicon-plus-sign').addClass('glyphicon-pencil');
        IS_EDIT_MODE = true;
        addBannerForm.show();
        clearFileFormatView();
        reset.hide();
        cancelEdit.show();
        changeImage.show();
        fileInput.filestyle('disabled',true);
        fileInput.filestyle('clear');
        removeErrorOnField("txtImage");
        removeErrorOnField("bannerName");
        removeErrorOnField("bannerLanguage");
        });

/*------------FORM TOGGLE END-------------*/
    }); //end ready

    function deleteBanner(banner_id, banner_name) {
        var deleteMsg = "<?php echo lang('sys.gd4') . ": "; ?>"
        if (confirm(deleteMsg + banner_name + '?')) {
            window.location = base_url + "affiliate_management/deleteBanner/" + banner_id;
        }
    }

     $("#delete_form").submit(function(){
        var checked = $(".checkWhite:checked").length > 0;
        var deleteCheckboxWarningMsg = "<?php echo lang('Please check at least one item'); ?>";
        if (!checked){
            alert(deleteCheckboxWarningMsg);
            return false;
        }else{
            return confirmDelete();
        }
    });

     $("#enabled_date").change(function(){
        var checked=$(this).is(":checked");
        $("#filterDate").prop("disabled", !checked);
     });

    function getBannerDetails(banner_id) {
        $.ajax({
            'url' : base_url + 'affiliate_management/getBannerDetails/' + banner_id,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                if(data){
                    $('#bannerId').val(data.bannerId);
                    $('#bannerName').val(data.bannerName);
                    $('#bannerLanguage').val(data.language);
                    $('#banner_url').val(data.bannerURL);
                    $('#img-prev').attr('src',data.bannerURL).show() ;
                }
            }
        },'json');
        return false;
    }

</script>