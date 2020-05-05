<?php
    // # INIT VARS
    // $manual_open    = false;
    // $sub_link       = false;

    // # GET SUB AFFILIATE TERMS
    // if(!empty($sub_affiliate_term) || $sub_affiliate_term != 0) $sub_affiliate_terms = $sub_affiliate_term;

    // $sub_affiliate = json_decode($sub_affiliate_terms)->terms;

    // if(!empty($sub_affiliate->manual_open)) { if($sub_affiliate->manual_open != false) $manual_open = true; }
    // if(!empty($sub_affiliate->sub_link)) { if($sub_affiliate->sub_link != false) $sub_link = true; }
?>
<br>
<div class="container">
<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
           <div class="nav-head panel-heading">
                <h3 class="panel-title"> <?=lang('aff.sb8');?>
                    <?php if($commonSettings['manual_open']) { ?>
                    <a href="<?=$sublink.$trackingCode;?>" target="_blank" class="btn-hov btn btn-sm btn-info pull-right" id="add_news">
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
                                <th><?=lang('aff.aj01');?></th>
                                <th><?=lang('Affiliate Domain');?></th>
                                <th><?=lang('Email');?></th>
                                <th><?=lang('aff.aj07');?></th>
                                <th><?=lang('aff.aj08');?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                        if (!empty($affiliates)) {

                        	foreach ($affiliates as $affiliate) {
                        		$name = $affiliate['lastname'] . ", " . $affiliate['firstname'];
                        		?>

                                    <tr <?php echo $affiliate['status']==Affiliatemodel::OLD_STATUS_INACTIVE ? 'class="danger"' : '' ;?> >
                                        <td>
                                            <?=$affiliate['username'];?>
                                        </td>
                                        <td>
                                            <a href="<?=$affiliate['affdomain']?>"><?=$affiliate['affdomain']?></a>
                                        </td>
                                        <td>
                                            <?=$affiliate['email'];?>
                                        </td>
                                            <td>
                                            <?php
                                            if ($affiliate['status'] == 0) {
                                				echo lang('aff.aj09');
                                			} else if ($affiliate['status'] == 1) {
                                				echo lang('aff.aj10');
                                			} else {
                                				echo lang('aff.aj11');
                                			}
                                			?>
                                            </td>
                                        <td>
                                        <?php if ($affiliate['status'] == 0) {?>
                                            <a href="#freeze" data-toggle="tooltip" title="<?=lang('tool.am04');?>" onclick="freezeAffiliate(<?=$affiliate['affiliateId']?>, '<?=$affiliate['username']?>')">
                                                <span class="glyphicon glyphicon-lock"></span></a>
                                        <?php }elseif ($affiliate['status'] == 1) {?>
                                            <a href="#activate" data-toggle="tooltip" title="<?=lang('tool.am05');?>" onclick="activateAffiliate(<?=$affiliate['affiliateId']?>, '<?=$affiliate['username']?>')">
                                                <span class="glyphicon glyphicon-user"></span></a>
                                        <?php } ?>
                                            <!-- <a href="#delete" data-toggle="tooltip" title="<?=lang('tool.am02');?>" onclick="deleteAffiliate(<?=$affiliate['affiliateId']?>, '<?=$affiliate['username']?>')">
                                                <span class="glyphicon glyphicon-trash"></span></a> -->
                                        </td>
                                    </tr>
                            <?php } ?>
                        <?php } ?>
                            </tbody>
                        </table>
                    </div>

                <br>

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
            "order": [ 0, 'asc' ]
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