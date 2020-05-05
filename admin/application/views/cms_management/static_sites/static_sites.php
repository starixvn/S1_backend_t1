<!-- display static site -->
	<!-- Modal -->
		<div class="modal fade " id="addStaticSiteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document" style="width: 80%">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel"><?php echo lang('Static Site');?></h4>
		      </div>
		      <div class="modal-body">
		       		<!-- add static site -->
			        <div class="row">
			            <div class="col-md-12">
			                <div class="well"  id="add_staticsites_form">
			                    <form class="form-horizontal" id="static-site-upload-form" action="<?=site_url('cms_management/saveStaticSites')?>" method="POST" role="form" enctype="multipart/form-data">
			                        <div class="row">
				                        <div class="form-group">
				                            <div class="col-md-4">
				                                <label for="siteName" class="control-label"><?=lang('cms.siteName');?> </label>
				                                <input type="hidden" name="id" class="form-control" id="staticSiteId">
				                                <input type="text" required name="site_name" class="form-control input-sm" id="siteName" placeholder="<?=lang('cms.addSiteName')?>" value="<?php set_value('siteName')?>">
				                                <?php echo form_error('siteName', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
				                                <span id="error-siteName" class="help-block" style="color:#ff6666;font-size:11px;"></span>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="siteUrl" class="control-label"><?=lang('cms.siteUrl');?> </label>
				                                <input type="text"  name="site_url" class="form-control input-sm" id="siteUrl" placeholder="<?=lang('cms.addSiteUrl')?>" value="<?php set_value('siteUrl')?>">
				                            </div>
				                            <div class="col-md-4">
				                                <label for="templateName" class="control-label"><?=lang('cms.templateName');?> </label>
				                                <input type="text"  name="template_name" class="form-control input-sm" id="templateName" placeholder="<?=lang('cms.addTemplateName')?>" value="<?php set_value('templateName')?>">
				                            </div>
					                    </div>
					                </div>
			                        <div class="row">
				                        <div class="form-group">
				                            <div class="col-md-4">
				                                <label for="templatePath" class="control-label"><?=lang('cms.templatePath');?> </label>
				                                <input type="text" required name="template_path" class="form-control input-sm" id="templatePath" placeholder="<?=lang('cms.addTemplatePath')?>" value="<?php set_value('templatePath')?>">
				                            </div>
				                            <div class="col-md-4">
				                                <label for="assetUrl" class="control-label"><?=lang('cms.assetUrl');?> </label>
				                                <input type="text" required name="asset_url" class="form-control input-sm" id="assetUrl" placeholder="<?=lang('cms.addAssetUrl')?>" value="<?php set_value('assetUrl')?>">
				                            </div>
				                            <div class="col-md-4">
				                                <label for="notes" class="control-label"><?=lang('cms.notes');?> </label>
				                                <textarea name="notes" class="form-control input-sm" id="notes" value="<?php set_value('notes')?>" style="resize:none;"></textarea>
				                            </div>
					                    </div>
					                </div>
			                        <div class="row">
				                        <div class="form-group">
				                            <div class="col-md-4">
				                               <label for="lang" class="control-label"><?=lang('cms.lang');?> </label>
				                               <select name="lang" id="lang" class="form-control">
													<option value="">Select Language</option>
													<option <?= (set_value('lang') == "chinese") ? 'selected':'' ?> value="chinese">Chinese</option>
													<option <?= (set_value('lang') == "english") ? 'selected':'' ?> value="english">English</option>
													<option <?= (set_value('lang') == "indonesian") ? 'selected':'' ?> value="indonesian">Indonesian</option>
													<option <?= (set_value('lang') == "vietnamese") ? 'selected':'' ?> value="vietnamese">Vietnamese</option>
													<option <?= (set_value('lang') == "korean") ? 'selected':'' ?> value="korean">Korean</option>
												</select>
												<span id="error-lang" class="help-block" style="color:#ff6666;font-size:11px;"></span>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="loginTemplate" class="control-label"><?=lang('cms.loginTemplate');?> </label>
				                                <textarea name="login_template" class="form-control input-sm" id="loginTemplate" value="<?php set_value('loginTemplate')?>" style ="height: 200px; resize:none;"></textarea>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="loggedTemplate" class="control-label"><?=lang('cms.loggedTemplate');?> </label>
				                                <textarea name="logged_template" class="form-control input-sm" id="loggedTemplate" value="<?php set_value('loggedTemplate')?>" style ="height: 200px; resize:none;"></textarea>
				                            </div>
					                    </div>
					                </div>
			                        <div class="row">
				                        <div class="form-group">
				                            <div class="col-md-4">
				                                <label for="player_center_css" class="control-label"><?=lang('Player Center CSS');?> </label>
				                                <textarea name="player_center_css" class="form-control input-sm" id="player_center_css" value="<?php set_value('player_center_css')?>" style ="height: 200px; resize:none;"></textarea>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="admin_css" class="control-label"><?=lang('BackOffice CSS');?> </label>
				                                <textarea name="admin_css" class="form-control input-sm" id="admin_css" value="<?php set_value('admin_css')?>" style ="height: 200px; resize:none;"></textarea>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="aff_css" class="control-label"><?=lang('Affiliate CSS');?> </label>
				                                <textarea name="aff_css" class="form-control input-sm" id="aff_css" value="<?php set_value('aff_css')?>" style ="height: 200px; resize:none;"></textarea>
				                            </div>
					                    </div>
					                </div>
			                        <div class="row">
				                        <div class="form-group">
				                            <div class="col-md-4">
				                                <label for="favIcon" class="control-label"><?=lang('cms.favIcon');?> </label>
					                            <span id="form-input-file-container">
					                               <input type="file" id="favIconFilepath" name="favIconFilepath" class="form-control input-sm icon-upload-image" value="<?=set_value('favIconFilepath');?>">
					                            </span>
				                                <span id="error-favIconFilepath" class="help-block" style="color:green;font-size:11px;"></span>
				                                <span id="error-favIconFilepath-2" class="help-block" style="color:#ff6666;font-size:11px;"></span>
				                               <span style ="display:none;"class="help-block text-success" id="favIconFilepath-pic-desc"><i><b><?=lang('tool.am17')?>: </b><span id="favIcon-size">88KB</span></i> &nbsp;&nbsp;&nbsp;&nbsp;  <i><b><?=lang('tool.am18')?>: </b><span id="favIcon-format">mpeg</span></i>   </span>
				                                <img id="favIcon-img-prev" src="#" style="display:none;width:100px;height:50px;" />
				                                <input type="button" style="display:none;" class="btn btn-xs btn-danger change-image" id="favIcon-change-image" value="<?=lang('payment.changeImage')?>"  />
				                            </div>
				                            <div class="col-md-4">
				                                <label for="logoIconFilepath" class="control-label"><?=lang('cms.logoIcon');?> </label>
					                            <span id="form-input-file-container">
					                               <input type="file" id="logoIconFilepath" name="logoIconFilepath" class="form-control input-sm icon-upload-image" value="<?=set_value('logoIconFilepath');?>">
					                            </span>
				                                <span id="error-logoIconFilepath" class="help-block" style="color:green;font-size:11px;"></span>
				                                <span id="error-logoIconFilepath-2" class="help-block" style="color:#ff6666;font-size:11px;"></span>
				                               <span style ="display:none;"class="help-block text-success" id="logoIcon-pic-desc"><i><b><?=lang('tool.am17')?>: </b><span id="logoIcon-size">88KB</span></i> &nbsp;&nbsp;&nbsp;&nbsp;  <i><b><?=lang('tool.am18')?>: </b><span id="logoIcon-format">mpeg</span></i>   </span>
				                                <img id="logoIcon-img-prev" src="#" style="display:none;width:100px;height:50px;" />
				                                <input type="button" style="display:none;" class="btn btn-xs btn-danger change-image" id="logoIcon-change-image" value="<?=lang('payment.changeImage')?>"  />
				                                <button type="button" class="remove-image">x</button>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="logoIconHorizontalFilepath" class="control-label"><?=lang('cms.logoIconHorizontal');?> </label>
					                            <span id="form-input-file-container">
					                               <input type="file" id="logoIconHorizontalFilepath" name="logoIconHorizontalFilepath" class="form-control input-sm icon-upload-image"  value="<?=set_value('logoIconHorizontalFilepath');?>">
					                            </span>
				                                <span id="error-logoIconHorizontalFilepath" class="help-block" style="color:green;font-size:11px;"></span>
				                                <span id="error-logoIconHorizontalFilepath-2" class="help-block" style="color:#ff6666;font-size:11px;"></span>
				                               <span style ="display:none;"class="help-block text-success" id="logoIconHorizontal-pic-desc"><i><b><?=lang('tool.am17')?>: </b><span id="logoIconHorizontal-size">88KB</span></i> &nbsp;&nbsp;&nbsp;&nbsp;  <i><b><?=lang('tool.am18')?>: </b><span id="logoIconHorizontal-format">mpeg</span></i>   </span>
				                                <img id="logoIconHorizontal-img-prev" src="#" style="display:none;width:100px;height:50px;" />
				                                <input type="button" style="display:none;" class="btn btn-xs btn-danger change-image" id="logoIconHorizontal-change-image" value="<?=lang('payment.changeImage')?>"  />
				                            </div>
					                    </div>
					                </div>
			                        <div class="row">
				                        <div class="form-group">
				                        	<div class="col-md-4">
				                                <label for="companyTitle" class="control-label"><?=lang('cms.companyTitle');?> </label>
				                                <textarea name="company_title" class="form-control input-sm" id="companyTitle" value="<?php set_value('companyTitle')?>" style="resize:none;"></textarea>
				                            </div>
				                            <div class="col-md-4">
				                                <label for="contactSkype" class="control-label"><?=lang('cms.skype');?> </label>
				                                <input type="text" name="contact_skype" class="form-control input-sm" id="contactSkype" placeholder="<?=lang('cms.addSkype')?>" value="<?php set_value('contactSkype')?>">
				                            </div>
				                            <div class="col-md-4">
				                                <label for="email" class="control-label"><?=lang('cms.email');?> </label>
				                                <input type="email" required name="contact_email" class="form-control input-sm" id="email" placeholder="<?=lang('cms.addEmail')?>" value="<?php set_value('email')?>">
				                            </div>
					                    </div>
					                </div>
					                <div class="form-group">
					                    <input type="reset" id="reset-upload-form" value="<?=lang('aff.vb42');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"/>
					                    <input type="button" id="cancel-edit" value="<?=lang('lang.cancel');?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater m-l-5' : 'btn-default'?>" data-dismiss="modal"/>
					                    <input type="submit" id="btn-submit_" value="<?=lang('aff.vb41');?>" class="btn review-btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter m-l-5' : 'btn-info'?>"/>
					                </div>
			                    </form>
			                </div>
			            </div>
			        </div>
			            <!-- end of add static site -->
		      </div>
		    </div>
		  </div>
		</div>

<div class="panel panel-primary">
	<!-- End of my modal-->
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt pull-left">
            <i class="glyphicon glyphicon-globe"></i> <?=lang('role.113');?>
         <!--    <button id="addAffTagMngmtGlyph"  class="btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-plus-sign"></i></button> -->
        </h4>
        <a href="#" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-xs btn-info' : 'btn-sm btn-default'?>" id="add_static_site" data-toggle="modal" data-target="#addStaticSiteModal">
            <span class="glyphicon glyphicon-plus-sign" type="button"  ></span>
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="panel-body" id="details_panel_body">

        <form action="<?=site_url('cms_management/deleteSelectedStaticSites')?>" method="post" role="form">
            <div class="row">
                <div class="col-md-12 col-md-offset-0">
                    <table class="table table-bordered table-hover dataTable" id="staticSitesTable" style="width: 100%;">
                        <div class="">
                            <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('aff.vb25');?>">
                                <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                            </button>
                        </div>
                        <thead>
                            <tr>
                                <th width="4%"></th>
                                <th width="6%"><?=lang('aff.vb32');?></th>
                                <th width="4%" style="padding:8px"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                <th width="10%"><?=lang('cms.siteName');?></th>
<!--								<th width="10%">--><?//=lang('cms.siteUrl');?><!--</th>-->
								<th width="10%"><?=lang('cms.templateName');?></th>
								<th width="10%"><?=lang('cms.lang');?></th>
<!--								<th width="10%">--><?//=lang('cms.loginTemplate');?><!--</th>-->
<!--								<th width="10%">--><?//=lang('cms.loggedTemplate';?><!--</th>。-->
<!--								<th width="10%">--><?//=lang('cms.assetUrl');?><!--</th>-->
								<th width="10%"><?=lang('cms.favIcon');?></th>
								<th width="10%"><?=lang('cms.logoIcon');?></th>
								<th width="10%"><?=lang('cms.logoIconHorizontal');?></th>
<!--								<th width="10%">--><?//=lang('cms.companyTitle');?><!--</th>-->
<!--								<th width="10%">--><?//=lang('cms.skype');?><!--</th>-->
<!--								<th width="10%">--><?//=lang('cms.email');?><!--</th>-->
                                <th><?=lang('cms.notes');?></th>
                                <!--								<th width="10%">--><?//=lang('cms.extraInfo');?><!--</th>-->
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            	$uploadUri='/upload';
                            	$default_logo_file='og-login-logo.png';
                            	$this->utils->addSuffixOnMDB($uploadUri);
                            	$defaultLogoUrl=$this->utils->getDefaultLogoUrl();
                            	foreach ($static_sites as $row) {
                            		$fav_icon_url=!empty($row['fav_icon_filepath']) && $row['fav_icon_filepath']!=$default_logo_file ? $uploadUri.'/'.$row['fav_icon_filepath'] : $defaultLogoUrl;
                            		$logo_icon_url=!empty($row['logo_icon_filepath']) && $row['logo_icon_filepath']!=$default_logo_file ? $uploadUri.'/'.$row['logo_icon_filepath'] : $defaultLogoUrl;
                            		$logo_icon_horizontal_url=!empty($row['logo_icon_horizontal_filepath']) && $row['logo_icon_horizontal_filepath']!=$default_logo_file ? $uploadUri.'/'.$row['logo_icon_horizontal_filepath'] : $defaultLogoUrl;
                            	?>
                                    <tr data="<?php echo htmlspecialchars(json_encode($row));?>">
                                        <td></td>
                                        <td>
                                            <a href="#" class="editsite" data-toggle="modal" data-target="#addStaticSiteModal">
                                                <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?=lang('tool.cms03');?>"  data-placement="top" >
                                                </span>
                                            </a>
                                            <a class="delStaticSite" href="<?=site_url('cms_management/deleteStaticSite/' . $row['id'])?>" >
                                                <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?=lang('tool.cms04');?>"  data-placement="top">
                                                </span>
                                            </a>

                                            <?php
                                            if ($row['status'] == 1) {?>
                                                <a href="<?=site_url('cms_management/deactivateStaticSite/' . $row['id'])?>" >
                                                    <span class="glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="<?=lang('tool.cms01');?>"  data-placement="top">
                                                    </span>
                                                </a>
				                       	 	<?php
				                       	 	} else {?>
                                                <a href="<?=site_url('cms_management/activateStaticSite/' . $row['id'])?>" >
                                                    <span class="glyphicon glyphicon-ok-sign" data-toggle="tooltip" title="<?=lang('tool.cms02');?>"  data-placement="top">
                                                    </span>
                                                </a>
                                            <?php
                                            }  ?>
                                        </td>
                                        <td style="padding:8px"><input type="checkbox" class="checkWhite" id="<?=$row['id']?>" name="sites[]" value="<?=$row['id']?>" onclick="uncheckAll(this.id)"/></td>
                                        <td><?=$row['site_name']?></td>
<!--                                        <td>--><?//=$row['site_url']?><!--</td>-->
                                        <td><?=$row['template_name']?></td>
                                        <td><?=$row['lang']?></td>
<!--                                        <td>--><?//=htmlspecialchars($row['login_template'])?><!--</td>-->
<!--                                        <td>--><?//=htmlspecialchars($row['logged_template'])?><!--</td>-->
<!--                                        <td>--><?//=$row['asset_url']?><!--</td>-->
                                        <td>
                                        	<a href="<?=$fav_icon_url?>" target="_blank">
                                        		<img style="width:100px; height: 50px;" src="<?=$fav_icon_url?>"/>
                                        	</a>
                                        </td>
                                        <td>
                                        	<a href="<?=$logo_icon_url?>" target="_blank">
                                        		<img style="width:100px; height: 50px;" src="<?=$logo_icon_url?>"/>
                                        	</a>
                                        </td>
                                        <td>
                                        	<a href="<?=$logo_icon_horizontal_url?>" target="_blank">
                                        		<img style="width:100px; height: 50px;" src="<?=$logo_icon_horizontal_url?>"/>
                                        	</a>
                                        </td>
<!--                                      	<td>--><?//=lang($row['company_title'])?><!--</td>-->
<!--                                      	<td>--><?//=$row['contact_skype']?><!--</td>-->
<!--                                      	<td>--><?//=$row['contact_email']?><!--</td>-->
                                        <td><?=$row['notes']?></td>
<!--                                      	<td>--><?//=$row['extra_info']?><!--</td>-->
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col col-md-12">
                <label for="choice_static_site_integration"><?php echo lang('Integration Login');?>:&nbsp;</label>
                <select id="choice_static_site_integration">
                    <?php foreach ($static_sites as $row) { ?>
                        <option value="<?=$row['site_name']?>"><?=$row['site_name']?></option>
                    <?php } ?>
                </select>
            </div>

            <ul class="nav nav-tabs static_site_integration_nav">
                <li class="active"><a data-toggle="tab" href="#static_site_integration_new"><?=lang('Integration Login New');?></a></li>
                <li><a data-toggle="tab" href="#static_site_integration_developer"><?=lang('Integration Login For Developer');?></a></li>
            </ul>

            <div class="tab-content">
                <div id="static_site_integration_new" class="tab-pane nopadding fade in active">
                	<?php if($this->permissions->checkPermissions('generate_sites')){ ?>
                		<button id="regenerate_static_site_script"><?=lang('Generate Integration Login')?></button>
                    <?php }?>
                    <pre></pre>
                </div>
                <div id="static_site_integration_developer" class="tab-pane nopadding fade">
                    <pre></pre>
                </div>
            </div>
        </div>
        <div class="row player_main_js_manual">
            <ul class="nav nav-tabs static_site_help_block_nav">
                <li class="active"><a data-toggle="tab" href="#static_site_help_block_nav_wallet"><?=lang('Wallet');?></a></li>
            </ul>

            <div class="tab-content">
                <div id="static_site_help_block_nav_wallet" class="tab-pane help-block nopadding fade in active">
                    <div class="css_hack_container">
                        <h4>CSS Hack</h4>
                        <hr />
                        <div class="content">
                            <div class="entry">
                                <h5><del>Refresh Balance Legacy</del></h5>
                                <pre><code><button class="_player_balance">$0</button></code></pre>
                                <p>Refresh Balance and Show Balance</p>
                                <hr />
                            </div>
                            <div class="entry">
                                <h5>Refresh Balance</h5>
                                <pre><code><button class="_player_balance_refresh"><%- langText.Transfer %></button></code></pre>
                                <p>Refresh Balance</p>
                                <hr />
                            </div>
                            <div class="entry">
                                <h5>Display Balance</h5>
                                <pre><code><span class="_player_balance_span">$0</span></code></pre>
                                <p>Display Balance</p>
                                <hr />
                            </div>
                            <div class="entry">
                                <h5>Display dropdown wallet list</h5>
                                <pre><code><div class="_player_wallet_list"><span class="_player_balance_span">$0</span></div></code></pre>
                                <p>Display dropdown wallet list</p>
                                <hr />
                            </div>
                            <div class="entry">
                                <h5>Show/Hide dropdown wallet list</h5>
                                <pre><code><button class="_player_wallet_list_toggle"><i class="glyphicon glyphicon-refresh"></i></button></code></pre>
                                <p>Show/Hide dropdown wallet list</p>
                                <hr />
                            </div>
                            <div class="entry">
                                <h5>Show transfer window</h5>
                                <pre><code><button class="_player_wallet_transfer_window_toggle"><%- langText.Transfer %></button></code></pre>
                                <p>Show transfer window</p>
                                <hr />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){

    var default_logo_file="<?=$default_logo_file?>";
    var defaultLogoUrl="<?=$defaultLogoUrl?>";

    $('.player_main_js_manual .content pre>code').each(function(){
        var html = $(this).html();

        html = (function(ch){
            if (ch===null) return '';
            ch = ch.replace("&amp;","&");
            ch = ch.replace("&quot;","\"");
            ch = ch.replace("&#039;","\'");
            ch = ch.replace("&lt;","<");
            ch = ch.replace("&gt;",">");
            return ch;
        })(html);

        $(this).text(html);
    });

	$('.remove-image').on('click',function(){
		var staticSiteId = $('#staticSiteId').val();
		if(staticSiteId){
			if(confirm('<?=lang('sys.gd4')?>')){
				$.ajax({
				    method: "POST",
				    url: "/cms_management/removeLogoStaticSite/"+staticSiteId,
					data: { 'staticSiteId': staticSiteId },
					success: function(data){
						$('.remove-image').parent().find('img').attr("src",defaultLogoUrl); // remove image
						$('.remove-image').parent().find('#logoIcon-pic-desc').html(''); // removed description
					}
				})
			}
		}
	});

	$('.delStaticSite').on('click',function(){
		return confirm('<?=lang('sys.gd4')?>');
	});

    function populateDetails(data){
		// console.log(data);
	}
  /*------------FORM DATATABLE START-------------*/
    $('#staticSitesTable').DataTable({
        dom: "<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        "responsive": {
            details: {
                type: 'column'
            }
        },
        "columnDefs": [{
            className: 'control',
            orderable: false,
            targets: 0
        }, {
            orderable: false,
            targets: [0, 1, 2]
        }],
        "order": [3, 'asc'],
        //"dom": '<"top"fl>rt<"bottom"ip>',
        "fnDrawCallback": function(oSettings){
            $('.btn-action').prependTo($('.top'));
        }
    });
 /*------------FORM DATATABLE END-------------*/


 /*------------FORM VALIDATION START-------------*/

	var error = [],
   // fileInput = $('#logoIconFilepath'),
    fileInput = $('.icon-upload-image'),
    changeImage = $('.change-image'),
    //for logo icon
    logoIconSizeView = $('#logoIcon-size'),
    logoIconFormatView = $('#logoIcon-format'),
    logoIconImagePrev =$('#logoIcon-img-prev'),
    logoIconPicDescView = $('#logoIcon-pic-desc'),
    //end of logo icon

    //for logo icon horizontal
    logoIconHorizontalSizeView = $('#logoIconHorizontal-size'),
    logoIconHorizontalFormatView = $('#logoIconHorizontal-format'),
    logoIconHorizontalImagePrev =$('#logoIconHorizontal-img-prev'),
    logoIconHorizontalPicDescView = $('#logoIconHorizontal-pic-desc'),
    //end of logo icon horizontal

    //for logo favicon
    favIconSizeView = $('#favIcon-size'),
    favIconFormatView = $('#favIcon-format'),
    favIconImagePrev =$('#favIcon-img-prev'),
    favIconPicDescView = $('#favIcon-pic-desc'),
    //end of logo icon

    staticSiteUploadForm = $('#static-site-upload-form'),
    submit =$('#btn-submit_'),
    reset =$('#reset-upload-form'),
    cancelEdit = $('#cancel-edit'),
    helpBlock =$('.help-block'),
    FILE_INPUT_LABEL = "<?=lang('aff.vb40');?>";


    $('#add_static_site').on('click',function(){
    	reset.click();
    });

    function getLogoFilePath(logoFile){
    	if(logoFile && logoFile!= default_logo_file){
    		return '<?=$uploadUri?>/'+logoFile;
    	}else{
    		return defaultLogoUrl;
    	}
    }

    $(".editsite").on('click',function () {
        resetFormUpload();
        var data = jQuery.parseJSON($(this).closest("tr").attr('data'));

        $('#staticSiteId').val(data.id);
		$('#siteName').val(data.site_name);
		$('#siteUrl').val(data.site_url);
		$('#templateName').val(data.template_name);
		$('#templatePath').val(data.template_path);
		$('#notes').val(data.notes);
		$('#lang').val(data.lang);
		$('#loginTemplate').val(data.login_template);
		$('#loggedTemplate').val(data.logged_template);
		$('#assetUrl').val(data.asset_url);

		$('#player_center_css').val(data.player_center_css);
		$('#admin_css').val(data.admin_css);
		$('#aff_css').val(data.aff_css);

		// $('#ptGameTypeTemplate').val(data.pt_game_type_template);
		// $('#ptGameTemplate').val(data.pt_game_template);
		$('#favIcon-img-prev').attr('src',getLogoFilePath(data.fav_icon_filepath));
		$('#logoIcon-img-prev').attr('src',getLogoFilePath(data.logo_icon_filepath) );
		$('#logoIconHorizontal-img-prev').attr('src',getLogoFilePath(data.logo_icon_horizontal_filepath));
		$('#companyTitle').val(data.company_title);
		$('#contactSkype').val(data.contact_skype);
		$('#email').val(data.contact_email);

		$('#favIcon-img-prev,#logoIcon-img-prev,#logoIconHorizontal-img-prev').show();
    });

   submit.on('click',function(event){
		staticSiteUploadForm.submit();
		// console.log(staticSiteUploadForm);
		event.preventDefault();
    });

    fileInput.bind('change', function() {
	    if(requiredCheckHelp($(this).val(),$(this).attr('name'),FILE_INPUT_LABEL)){
	       if(checkChosenFileIfAccepted(this.files[0].name,$(this).attr('name'),FILE_INPUT_LABEL)) {
	            showImageDesc(this.files[0],$(this).attr('name'));
	            readShowImageURL(this,$(this).attr('name'));

	       }else{
	          imagePrev.hide();
	       }
	    }
    });

    fileInput.blur(function(){
        requiredCheckHelp($(this).val(),$(this).attr('name'),FILE_INPUT_LABEL)
    });

    //During edit
    changeImage.click(function(){
      fileInput.filestyle('disabled',false);
      $(this).hide();
      requiredCheckHelp($(this).val(),$(this).attr('name'),FILE_INPUT_LABEL);
    });


    staticSiteUploadForm.submit(function(){

    });

    reset.on('click', function(){
        resetFormUpload();
    });


    function resetFormUpload(){
	    error =[];
	    logoIconHorizontalPicDescView.hide();
	    logoIconPicDescView.hide();
	    logoIconImagePrev.hide();
	    logoIconHorizontalImagePrev.hide();

	    favIconImagePrev.hide();
	    favIconPicDescView.hide();
	    $('#staticSiteId').val('');
	}

    cancelEdit.on('click', function(){
       resetFormUpload();
    });


	function clearFileFormatView(){

	    logoIconSizeView.html("");
	    logoIconFormatView.html("");
	    logoIconPicDescView.hide();

	    logoIconHorizontalSizeView.html("");
		logoIconHorizontalFormatView.html("");
		logoIconHorizontalPicDescView.hide();

	    favIconSizeView.html("");
		favIconFormatView.html("");
		favIconPicDescView.hide();

	}

	function showImageDesc(FILE,imageTarget){
		var targetView;
		var targetFormatView;
		var targetViewDesc;

		switch(imageTarget){
			case "logoIconFilepath":
				targetView =logoIconSizeView;
				targetFormatView = logoIconFormatView;
				targetViewDesc = logoIconPicDescView;
			break;
			case "logoIconHorizontalFilepath":
				targetView =logoIconHorizontalSizeView;
				targetFormatView = logoIconHorizontalFormatView;
				targetViewDesc = logoIconHorizontalPicDescView;
			break;
			case "favIconFilepath":
				targetView =favIconSizeView;
				targetFormatView = favIconFormatView;
				targetViewDesc = favIconPicDescView;
			break;
		}

	    targetView.html(bytesToSize(FILE.size ));
	    targetFormatView.html(FILE.type);
	    targetViewDesc.show();
	}

	function bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
	};


	function readShowImageURL(input,imageTarget) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var imagePrev;
            switch(imageTarget){
            	case "logoIconFilepath":
            		imagePrev = logoIconImagePrev;
            	break;
            	case "logoIconHorizontalFilepath":
            		imagePrev = logoIconHorizontalImagePrev;
            	break;
            	case "favIconFilepath":
            		imagePrev = favIconImagePrev;
            	break;
            }
            reader.onload = function (e) {
               imagePrev.attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
	    }
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

    <?php
    $integration_js=<<<EOD
<script type="text/javascript">
(function() {
    var urlName = document.location.hostname;
    var prefix = window.location.protocol + '//player.';
    var urlArr = urlName.split('.');
    if(urlArr.length > 2){
        urlArr.shift();
        urlName = urlArr.join('.');
    }
    var player_js_url = prefix + urlName + '/pub/player_main_js/{SITE_NAME}/true/' + ('' + Math.random()).substr(2, 16);
    var player_css_url = prefix + urlName + '/resources/player/built_in/{SITE_NAME}_all.min.css?v=' + ('' + Math.random()).substr(2, 16);
    var html = '<link rel="stylesheet" href="' + player_css_url + '"></link>';
    html += '<script src="' + player_js_url + '"></script>';
    document.writeln(html);
})();
</script>
EOD;

    $integration_js_new=<<<EOD
<link rel="stylesheet" href="/resources/player/built_in/{SITE_NAME}_all.min.css?v={CMS_VERSION}" />
<script type="text/javascript">
(function() {
    var player_js_url='/resources/player/built_in/{SITE_NAME}_all.min.js?v={CMS_VERSION}';
    var html = '<script src="' + player_js_url + '"></script>';
    document.writeln(html);
})();
</script>
EOD;

    ?>
    var integration_js_tpl = decodeURIComponent("<?=rawurlencode($integration_js)?>");
    var integration_js_new_tpl = decodeURIComponent("<?=rawurlencode($integration_js_new)?>");
    var cms_version = "<?=$this->CI->utils->getCmsVersion();?>";
    $('#choice_static_site_integration').change(function(){
        var site_name = $(this).val();

        var integration_new = integration_js_new_tpl;
        integration_new = integration_new.replace(/{SITE_NAME}/g, site_name);
        integration_new = integration_new.replace(/{CMS_VERSION}/g, cms_version);
        $('#static_site_integration_new pre').text(integration_new);

        var integration_developer = integration_js_tpl;
        integration_developer = integration_developer.replace(/{SITE_NAME}/g, site_name);
        $('#static_site_integration_developer pre').text(integration_developer);
    });
    $('#choice_static_site_integration').trigger('change');

    $('#regenerate_static_site_script').on('click', function(){
        var site_name = $('#choice_static_site_integration').val();

        window.open("/cms_management/static_site_script_generate/" + site_name);
    });

}); //end ready
</script>