<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="icon-info"></i> <strong><?=lang('aff.ai01');?></strong>
					<a href="<?=site_url('affiliate_management/userInformation/' . $affiliates['affiliateId'])?>" class="btn btn-danger btn-xs pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_info">
				<!-- Personal Info -->
				<form method="POST" action="<?=site_url('affiliate_management/verifyEditAffiliate/' . $affiliates['affiliateId'] . '/' . $affiliates['affiliatePayoutId'])?>" accept-charset="utf-8">
		            <input type="hidden" name="affiliateId" class="form-control input-sm" value="<?=$affiliates['affiliateId'];?>">
					<div class="row">
	                    <div class="col-md-12">
	                        <div class="table-responsive">
	                            <table class="table table-hover table-bordered">
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ap03');?></th>
	                            		<td class="col-md-4"><?=$affiliates['username']?></td>
	                            		<th class="active col-md-2"><?=lang('aff.ai11');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="zip" id="zip" class="form-control input-sm letters_numbers_only" value="<?=(set_value('zip') != null) ? set_value('zip') : $affiliates['zip'];?>">
											<span class="text-danger"><?php echo form_error('zip'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai02');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="firstname" id="firstname" class="form-control input-sm" value="<?=(set_value('firstname') != null) ? set_value('firstname') : $affiliates['firstname'];?>">
	                            			<span class="text-danger"><?php echo form_error('firstname'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai08');?></th>
	                            		<td class="col-md-4">
	                            			<input type="hidden" name="email_db" id="email_db" class="form-control input-sm" value="<?=(set_value('email') != null) ? set_value('email') : $affiliates['email'];?>">
											<input type="text" name="email" id="email" class="form-control input-sm" value="<?=(set_value('email') != null) ? set_value('email') : $affiliates['email'];?>">
											<span class="text-danger"><?php echo form_error('email'); ?>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai03');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="lastname" id="lastname" class="form-control input-sm" value="<?=(set_value('lastname') != null) ? set_value('lastname') : $affiliates['lastname'];?>">
											<span class="text-danger"><?php echo form_error('lastname'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai07');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="occupation" id="occupation" class="form-control input-sm" value="<?=(set_value('occupation') != null) ? set_value('occupation') : $affiliates['occupation'];?>">
											<span class="text-danger"><?php echo form_error('occupation'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai04');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="birthday" id="birthday" class="form-control input-sm dateInput" value="<?=(set_value('birthday') != null) ? set_value('birthday') : date("Y-m-d", strtotime($affiliates['birthday']));?>">
											<span class="text-danger"><?php echo form_error('birthday'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai06');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="company" id="company" class="form-control input-sm" value="<?=(set_value('company') != null) ? set_value('company') : $affiliates['company'];?>">
											<span class="text-danger"><?php echo form_error('company'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai05');?></th>
	                            		<td class="col-md-4">
	                            			<select name="gender" id="gender" class="form-control input-sm">
												<option value="Male" <?=($affiliates['gender'] == 'Male') ? 'selected' : ''?> ><?=lang('aff.ai76');?></option>
												<option value="Female" <?=($affiliates['gender'] == 'Female') ? 'selected' : ''?> ><?=lang('aff.ai77');?></option>
											</select>
											<span class="text-danger"><?php echo form_error('gender'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai16');?></th>
	                            		<td class="col-md-4">
	                            			<select name="imtype1" id="imtype1" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.50');?>" onchange="imCheck(this.value, '1');">
												<option value=""><?=lang('aff.ai81');?></option>
												<?php if($this->config->item('IM_list')):
												    foreach($this->config->item('IM_list') as $im){
												        echo '<option value=\''.$im.'\' '. ($affiliates['imType1'] == $im ? 'selected' : '') .'>'.lang($im).'</option>';
												    }
												endif;?>
											</select>
											<span class="text-danger"><?php echo form_error('imType1'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai14');?></th>
	                            		<td class="col-md-4">
                                            <div class="mobile_dialing_code">
                                                <select id="mobile_dialing_code" class="form-control selectpicker diling-code-field" name="mobile_dialing_code">
                                                    <option title="<?=lang('reg.77')?>" country="" value=""><?=lang('reg.77')?></option>
                                                    <?php if (! empty($frequentlyUsedCountryNumList)): ?>
                                                        <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                                            <?php foreach ($frequentlyUsedCountryNumList as $country => $nums) : ?>
                                                                <?php if (is_array($nums)) : ?>
                                                                    <?php foreach ($nums as $_nums) : ?>
                                                                        <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= ($mobile_dailing_num == $_nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?></option>
                                                                    <?php endforeach ; ?>
                                                                <?php else : ?>
                                                                    <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= ($mobile_dailing_num == $nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?></option>
                                                                <?php endif ; ?>
                                                            <?php endforeach ; ?>
                                                        </optgroup>
                                                    <?php endif ?>

                                                    <?php foreach ($countryNumList as $country => $nums) : ?>
                                                        <?php if (is_array($nums)) : ?>
                                                            <?php foreach ($nums as $_nums) : ?>
                                                                <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= ($mobile_dailing_num == $_nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?></option>
                                                            <?php endforeach ; ?>
                                                        <?php else : ?>
                                                            <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= ($mobile_dailing_num == $nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?></option>
                                                        <?php endif ; ?>
                                                    <?php endforeach ; ?>
                                                </select>
                                            </div>
                                            <div class="mobile_num">
    	                            			<input type="text" name="mobile" id="mobile" class="form-control input-sm number_only" value="<?=(set_value('mobile') != null) ? set_value('mobile') : $mobile_num;?>">
                                            </div>
                                            <div class="clearfix"></div>
											<span class="text-danger"><?php echo form_error('mobile'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai17');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="im1" id="im1" class="form-control input-sm" value="<?=(set_value('im1') != null) ? set_value('im1') : $affiliates['im1'];?>" <?=($affiliates['imType1'] == null && $affiliates['im1'] == null) ? 'readonly' : ''?> >
											<span class="text-danger"><?php echo form_error('im1'); ?></span>
	                            		</td>
	                            	</tr>

	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai15');?></th>
	                            		<td class="col-md-4">
                                            <div class="phone_dialing_code">
                                                <select id="phone_dialing_code" class="form-control selectpicker diling-code-field" name="phone_dialing_code">
                                                    <option title="<?=lang('reg.77')?>" country="" value=""><?=lang('reg.77')?></option>
                                                    <?php if (! empty($frequentlyUsedCountryNumList)): ?>
                                                        <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                                            <?php foreach ($frequentlyUsedCountryNumList as $country => $nums) : ?>
                                                                <?php if (is_array($nums)) : ?>
                                                                    <?php foreach ($nums as $_nums) : ?>
                                                                        <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= ($phone_dailing_num == $_nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?></option>
                                                                    <?php endforeach ; ?>
                                                                <?php else : ?>
                                                                    <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= ($phone_dailing_num == $nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?></option>
                                                                <?php endif ; ?>
                                                            <?php endforeach ; ?>
                                                        </optgroup>
                                                    <?php endif ?>

                                                    <?php foreach ($countryNumList as $country => $nums) : ?>
                                                        <?php if (is_array($nums)) : ?>
                                                            <?php foreach ($nums as $_nums) : ?>
                                                                <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= ($phone_dailing_num == $_nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?></option>
                                                            <?php endforeach ; ?>
                                                        <?php else : ?>
                                                            <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= ($phone_dailing_num == $nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?></option>
                                                        <?php endif ; ?>
                                                    <?php endforeach ; ?>
                                                </select>
                                            </div>
                                            <div class="phone_num">
	                            			    <input type="text" name="phone" id="phone" class="form-control input-sm number_only" value="<?=(set_value('phone') != null) ? set_value('phone') : $phone_num;?>">
                                            </div>
                                            <div class="clearfix"></div>
											<span class="text-danger"><?php echo form_error('phone'); ?></span>
                                        </td>
	                            		<th class="active col-md-2"><?=lang('aff.ai18');?></th>
	                            		<td class="col-md-4">
	                            			<select name="imtype2" id="imtype2" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.50');?>" onchange="imCheck(this.value, '2');">
												<option value=""><?=lang('aff.ai82');?></option>
												<?php if($this->config->item('IM_list')):
												    foreach($this->config->item('IM_list') as $im){
												        echo '<option value=\''.$im.'\' '. ($affiliates['imType2'] == $im ? 'selected' : '') .'>'.lang($im).'</option>';
												    }
												endif;?>
											</select>
											<span class="text-danger"><?php echo form_error('imType2'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai10');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="address" id="address" class="form-control input-sm" value="<?=(set_value('address') != null) ? set_value('address') : $affiliates['address'];?>">
											<span class="text-danger"><?php echo form_error('address'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai19');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="im2" id="im2" class="form-control input-sm" value="<?=(set_value('im2') != null) ? set_value('im2') : $affiliates['im2'];?>" <?=($affiliates['imType2'] == null && $affiliates['im2'] == null) ? 'readonly' : ''?> >
											<span class="text-danger"><?php echo form_error('im2'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai09');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="city" id="city" class="form-control input-sm" value="<?=(set_value('city') != null) ? set_value('city') : $affiliates['city'];?>">
											<span class="text-danger"><?php echo form_error('city'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('aff.ai21');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="website" id="website" class="form-control input-sm" value="<?=(set_value('website') != null) ? set_value('website') : $affiliates['website'];?>">
											<span class="text-danger"><?php echo form_error('website'); ?></span>
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai13');?></th>
	                            		<td class="col-md-4">
	                            			<select name="country" id="country" class="form-control input-sm">
												<option value="">Select Country</option>
												<?php foreach (unserialize(COUNTRY_LIST) as $key) :?>
								                    <option value="<?=$key?>" <?=($affiliates['country'] == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
								                <?php endforeach;?>
											</select>
											<span class="text-danger"><?php echo form_error('country'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"><?=lang('Parent Affiliate');?></th>
	                            		<td class="col-md-4">
                                            <?php if (!empty($affiliates_parent)): ?>
                                                <?=$affiliates_parent['username'];?>
                                            <?php endif; ?>
                                        <!--
                                        <select class="form-control" name="parent_id">
	                            			<?php if (!isset($is_parent)) {?>
	                            				<option value="0"><?php echo lang('Set as parent affiliate') ?></option>
	                            			<?php }?>
	                            			<?php foreach ($affiliates_parents as $key) {?>
	                            				<option value="<?php echo $key['affiliateId']; ?>" <?php echo $isCheked = $parent_id == $key['affiliateId'] ? 'selected' : ''; ?>><?php echo $key['username']; ?></option>
	                            			<?php }?>
	                            			</select>
											<span class="text-danger"><?php echo form_error('parent_username'); ?></span>
                                        -->
	                            		</td>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('aff.ai12');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="state" id="state" class="form-control input-sm" value="<?=(set_value('state') != null) ? set_value('state') : $affiliates['state'];?>">
											<span class="text-danger"><?php echo form_error('state'); ?></span>
	                            		</td>
	                            		<?php if ($this->permissions->checkPermissions('edit_affiliate_domain_name')) {?>
	                            		<th class="active col-md-2"><?=lang('lang.affdomain');?></th>
	                            		<td class="col-md-4">
											<span class="text-danger"><?php echo form_error('affdomain'); ?></span>
											<input name="affdomain" class="form-control" value="<?=(set_value('affdomain') != null) ? set_value('affdomain') : $affiliates['affdomain']; ?>">
	                            		</td>
	                            		<?php }?>
	                            	</tr>
	                            	<tr>
	                            		<th class="active col-md-2"><?=lang('Prefix of player');?></th>
	                            		<td class="col-md-4">
	                            			<input type="text" name="prefix_of_player" id="prefix_of_player" class="form-control input-sm" value="<?=(set_value('prefix_of_player') != null) ? set_value('prefix_of_player') : $affiliates['prefix_of_player'];?>">
											<span class="text-danger"><?php echo form_error('checkPrefixOfPlayer'); ?></span>
	                            		</td>
	                            		<th class="active col-md-2"></th>
	                            		<td class="col-md-4">
	                            		</td>
	                            	</tr>
	                            </table>
	                        </div>
	                    </div>
	             	</div>
					<div class="row">
						<center>
							<input type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" value="<?=lang('lang.save');?>"/>
							<a href="<?=site_url('affiliate_management/userInformation/' . $affiliates['affiliateId'])?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-danger'?>" id="view_affiliate"><?=lang('lang.cancel');?></a>
						</center>
					</div>
				</form>
				<!-- End of Personal Info -->
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

</script>