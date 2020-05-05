<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left"><?=lang('nav.banner');?></h4>

					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body table-responsive" id="affiliate_panel_body">

					<div class="col-md-12" id="view_banner" style="margin: 30px 0 0 0;">
						<table class="table table-striped table-hover" id="bannerTable" style="width:100%">
							<thead>
								<tr>
									<th></th>
									<th class="input-sm"><?=lang('lang.date');?></th>
									<th class="input-sm"><?=lang('ban.name');?></th>
									<th class="input-sm"><?=lang('ban.size');?></th>
									<th class="input-sm"><?=lang('ban.lang');?></th>
									<th class="input-sm"><?=lang('ban.thumb');?></th>
									<th class="input-sm"><?=lang('ban.links');?></th>
									<th class="input-sm"><?=lang('lang.status');?></th>
									<th class="input-sm"><?=lang('ban.download');?></th>
								</tr>
							</thead>

							<tbody>
							<?php if (!empty($banner)) {

								$tracking_code = $this->session->userdata('affiliateTrackingCode');

								foreach ($banner as $value) {
									// $image_url= $this->utils->getSystemUrl("admin").'/affiliate_management/get_banner/'.$value['bannerId'];
									// $image_download_url= $this->utils->getSystemUrl("admin").'/affiliate_management/download_banner/'.$value['bannerId'];
									$image_url=$this->utils->getSystemUrl("player", '/pub/banner/'.$value['bannerId']);
									// $image_download_url = $this->utils->getSystemUrl("player", '/pub/banner/'.$value['bannerId']);
									$image_download_url = '/affiliate/download_banner/'.$value['bannerId'];

									if(!empty($fixed_banner_player_url)){
										$pub_image_url=$fixed_banner_player_url.'/pub/banner/'.$value['bannerId'].'/'.$tracking_code;
									} else {
										$pub_image_url=$this->utils->getSystemUrl('player','/pub/banner/'.$value['bannerId'].'/'.$tracking_code);
									}
									?>
									<tr>
										<td></td>
										<td class="input-sm"><?=$value['createdOn']?></td>
										<td class="input-sm"><?=$value['bannerName']?></td>
										<td class="input-sm"><?=$value['width'] . " x " . $value['height']?></td>
										<td class="input-sm"><?=$value['language']?></td>
										<td class="input-sm"><a href="#" onclick="window.open('<?=$image_url?>','_blank', 'width=<?=$value['width']?>,height=<?=$value['height']?>,scrollbars=yes,status=yes,resizable=no,screenx=0,screeny=0')"><img src="<?=$image_url?>" style="width: 50px; height: 40px;"/></a></td>
										<td class="input-sm">
											<?php echo $pub_image_url; ?>
										</td>
										<td class="input-sm"><?=($value['status'] == 0) ? lang('Active') : lang('Inactive')?></td>
										<td class="input-sm"><a href="<?php echo "$image_download_url"; ?>" target="_blank" data-toggle="tooltip" title="<?=lang('ban.download');?>"><i class="glyphicon glyphicon-download-alt"></i></a></td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#bannerTable').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        } );
    } );
</script>