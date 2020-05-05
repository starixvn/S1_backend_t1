<?php
    # INIT VARS
    $manual_open    = false;
    $sub_link       = false;

    # GET SUB AFFILIATE TERMS
    if(!empty($sub_affiliate_term) || $sub_affiliate_term != 0) $sub_affiliate_terms = $sub_affiliate_term;

    $sub_affiliate = json_decode($sub_affiliate_terms)->terms;

    if(!empty($sub_affiliate->manual_open)) { if($sub_affiliate->manual_open != false) $manual_open = true; }
    if(!empty($sub_affiliate->sub_link)) { if($sub_affiliate->sub_link != false) $sub_link = true; }
?>
<br>
<div class="container">
<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
           <div class="panel-heading">
                <h3 class="panel-title"> <?=lang('aff.sb8');?>
                    <?php if($manual_open) { ?>
                    <a href="<?=$sublink.$trackingCode;?>" target="_blank" class="btn btn-sm btn-info pull-right" id="add_news">
                        <span class="glyphicon glyphicon-plus"></span> <?=lang('aff.asb9');?>
                    </a>
                    <?php } ?>
                    <span class="clearfix"></span>
                </h3>
<!-- <h4 class="panel-title"><i class="glyphicon glyphicon-cog"></i> Account Information </h4> -->

            </div>
            <div class="panel panel-body table-responsive" id="newsList">
                    <div class="col-md-12" style="margin: 30px 0 0 0;">
                        <table class="table table-striped table-hover" id="tableSubAffiliates">
                            <thead>
                            <tr>
                                <!-- <th></th> -->
                                <th><?=lang('aff.aj01');?></th>
                                <?php /*
                                <?=$this->session->userdata('name') == "checked" || !$this->session->userdata('name') ? '<th id="visible">' . lang('aff.aj02') . '</th>' : ''?>
                                <?=$this->session->userdata('email') == "checked" || !$this->session->userdata('email') ? '<th id="visible">' . lang('aff.aj03') . '</th>' : ''?>
                                <?=$this->session->userdata('country') == "checked" || !$this->session->userdata('country') ? '<th id="visible">' . lang('aff.aj04') . '</th>' : ''?>
                                <?=$this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on') ? '<th>' . lang('aff.aj06') . '</th>' : ''?>
                                */?>
                                <?=$this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col') ? '<th id="visible">' . lang('aff.aj07') . '</th>' : ''?>
                                <th><?=lang('aff.aj08');?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                        if (!empty($affiliates)) {

                        	foreach ($affiliates as $affiliates) {
                        		$name = $affiliates['lastname'] . ", " . $affiliates['firstname'];
                        		?>

                                    <tr>
                                        <?php /*
                                        <td><input type="checkbox" class="checkWhite" id="<?=$affiliates['affiliateId']?>" name="affiliates[]" value="<?=$affiliates['affiliateId']?>" onclick="uncheckAll(this.id)"></td>
                                        */ ?>
                                        <td>
                                            <!--a href="<?=BASEURL . 'affiliate_management/userInformation/' . $affiliates['affiliateId']?>"><?=$affiliates['username']?></a-->
                                            <a href="#"><?=$affiliates['username']?></a>
                                        </td>
                                        <?php /*
                                        <?php if ($this->session->userdata('name') == "checked" || !$this->session->userdata('name')) {?>
                                            <td id="visible"><?=($affiliates['lastname'] == '') && ($affiliates['firstname'] == '') ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $name?></td>
                                        <?php } ?>

                                        <?php if ($this->session->userdata('email') == "checked" || !$this->session->userdata('email')) {?>
                                            <td id="visible"><?=$affiliates['email'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $affiliates['email']?></td>
                                        <?php } ?>

                                        <?php if ($this->session->userdata('country') == "checked" || !$this->session->userdata('country')) {?>
                                            <td id="visible"><?=$affiliates['country'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $affiliates['country']?></td>
                                        <?php } ?>

                                        <?php if ($this->session->userdata('registered_on') == "checked" || !$this->session->userdata('registered_on')) {?>
                                            <td><?=$affiliates['createdOn'] == '' ? '<i class="help-block">' . lang('lang.norecyet') . '<i/>' : $affiliates['createdOn']?></td>
                                        <?php } ?>
                                        */ ?>
                                        <?php if ($this->session->userdata('status_col') == "checked" || !$this->session->userdata('status_col')) { ?>
                                            <td id="visible">
                                            <?php
                                            if ($affiliates['status'] == 0) {
                                            				echo lang('aff.aj09');
                                            			} else if ($affiliates['status'] == 1) {
                                            				echo lang('aff.aj10');
                                            			} else {
                                            				echo lang('aff.aj11');
                                            			}
                                            			?>
                                            </td>
                                        <?php } ?>
                                        <td>
                                        <?php if ($affiliates['status'] == 2) {?>
                                            <a href="#unfreeze" data-toggle="tooltip" title="<?=lang('tool.am03');?>" onclick="unfreezeAffiliate(<?=$affiliates['affiliateId']?>, '<?=$affiliates['username']?>')">
                                                <span class="glyphicon glyphicon-lock" style="color:green"></span></a>
                                        <?php } else if ($affiliates['status'] == 0) {?>
                                            <a href="#freeze" data-toggle="tooltip" title="<?=lang('tool.am04');?>" onclick="freezeAffiliate(<?=$affiliates['affiliateId']?>, '<?=$affiliates['username']?>')">
                                                <span class="glyphicon glyphicon-lock"></span></a>
                                        <?php } ?>
                                        <?php if ($affiliates['status'] == 1) {?>
                                            <a href="#activate" data-toggle="tooltip" title="<?=lang('tool.am05');?>" onclick="activateAffiliate(<?=$affiliates['affiliateId']?>, '<?=$affiliates['username']?>')">
                                                <span class="glyphicon glyphicon-user"></span></a>
                                        <?php } ?>
                                            <!-- <a href="#delete" data-toggle="tooltip" title="<?=lang('tool.am02');?>" onclick="deleteAffiliate(<?=$affiliates['affiliateId']?>, '<?=$affiliates['username']?>')">
                                                <span class="glyphicon glyphicon-trash"></span></a> -->
                                        </td>
                                    </tr>
                            <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                <br>

                <!-- <div class="row">
                    <div class="col-md-12">
                        <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#tableSubAffiliates').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            "order": [ 1, 'asc' ]
        } );
    } );
</script>

<script>
    // Affiliate ---------------------------------------------------------------------------------------------

    function activateAffiliate(affiliate_id, username) {
        if (confirm('Are you sure you want to activate this affiliate: ' + username + '?')) {
            window.location = base_url + "affiliate/activateAffiliate/" + affiliate_id + "/" + username;
        }
    }

    function freezeAffiliate(affiliate_id, username) {
        if (confirm('Are you sure you want to freeze this affiliate: ' + username + '?')) {
            window.location = base_url + "affiliate/freezeAffiliate/" + affiliate_id + "/" + username;
        }
    }

    function unfreezeAffiliate(affiliate_id, username) {
        if (confirm('Are you sure you want to unfreeze this affiliate: ' + username + '?')) {
            window.location = base_url + "affiliate/unfreezeAffiliate/" + affiliate_id + "/" + username;
        }
    }

    // function deleteAffiliate(affiliate_id, username) {
    //     if (confirm('Are you sure you want to delete this affiliate: ' + username + '?')) {
    //         window.location = base_url + "affiliate/deleteAffiliate/" + affiliate_id + "/" + username;
    //     }
    // }
</script>