<div class="container">
	<br/>
<?php if ($enable_credit) {
	?>
	<a href="<?php echo site_url('affiliate/playerAction/' . Affiliate::ACTION_NEW_DEPOSIT . '/' . $player_id . '/' . $affiliateId); ?>" class="btn btn-md btn-info"><?=lang('aff.action.newDeposit');?></a>
	<a href="<?php echo site_url('affiliate/playerAction/' . Affiliate::ACTION_NEW_WITHDRAW . '/' . $player_id . '/' . $affiliateId); ?>" class="btn btn-md btn-primary"><?=lang('aff.action.newWithdrawal');?></a>
	<a href="<?php echo site_url('affiliate/playerAction/' . Affiliate::ACTION_TRANSFER_FROM_SW . '/' . $player_id . '/' . $affiliateId); ?>" class="btn btn-md btn-warning"><?=lang('aff.action.transferFromSubwallet');?></a>
	<a href="<?php echo site_url('affiliate/playerAction/' . Affiliate::ACTION_TRANSFER_TO_SW . '/' . $player_id . '/' . $affiliateId); ?>" class="btn btn-md btn-success"><?=lang('aff.action.transferToSubwallet');?></a>
<?php }
?>
	<br/><br/>
	<!-- SIGNUP Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4><?=lang('lang.signupinfo');?></h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<table class="table">
					<tbody>
						<tr>
							<th style="width:25%;"><?=lang('reg.03');?></th>
							<td style="width:25%;"><?=$player_signup_info['username'];?></td>
							<th style="width:25%;"><?=lang('player.ui09');?></th>
							<td style="width:25%;"><?=$player_signup_info['typeOfPlayer'];?></td>
						</tr>
						<tr>
							<th style="width:25%;"><?=lang('aff.al24');?></th>
							<td style="width:25%;"><?=$player_signup_info['createdOn'];?></td>
							<th style="width:25%;"><?=lang('aff.ai40');?></th>
							<td style="width:25%;"><?=$player_signup_info['invitationCode'];?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- End of SIGNUP Information -->

	<!-- PERSONAL Information -->
	<div class="row">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h4><?=lang('aff.ai01');?></h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="code_panel_body">
				<table class="table">
					<tbody>
						<tr>
							<th style="width:25%;"><?=lang('reg.a09');?></th>
							<td style="width:25%;"><?=$player_account_info['firstName'];?></td>
							<th style="width:25%;"><?=lang('reg.a10');?></th>
							<td style="width:25%;"><?=$player_account_info['lastName'];?></td>
						</tr>
						<tr>
							<th style="width:25%;"><?=lang('reg.a23');?></th>
							<td style="width:25%;"><?=$player_account_info['country'];?></td>
							<th style="width:25%;"><?=lang('aff.ai05');?></th>
							<td style="width:25%;"><?=$player_account_info['gender'];?></td>
						</tr>
						<tr>
							<th style="width:25%;"><?=lang('ban.lang');?></th>
							<td style="width:25%;"><?=$player_account_info['language'];?></td>
							<th style="width:25%;"><?=lang('a_reg.5');?></th>
							<td style="width:25%;"><?=$player_account_info['citizenship'];?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- End of PERSONAL Information -->

<?php

if (!$this->utils->isHidePlayerContactOnAff()) {
	?>
	<!-- CONTACT Information -->
	<div class="row">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h4><?=lang('reg.74');?></h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="code_panel_body">
				<table class="table">
					<tbody>
						<tr>
							<th style="width:25%;"><?=lang('reg.a17');?></th>
							<td style="width:25%;"><?=$player_signup_info['email'];?></td>
							<th style="width:25%;"><?=lang('reg.a25');?></th>
							<td style="width:25%;"><?=$player_account_info['contactNumber'];?></td>
						</tr>
						<tr>
							<th style="width:25%;"><?=lang('reg.a30');?></th>
							<td style="width:25%;"><?=$player_account_info['imAccount'];?>:<?=$player_account_info['imAccountType'];?></td>
							<th style="width:25%;"><?=lang('reg.a35');?></th>
							<td style="width:25%;"><?=$player_account_info['imAccount2'];?>:<?=$player_account_info['imAccountType2'];?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- End of CONTACT Information -->
<?php

}
?>
	<!-- ################################################################## START Member's Logs ################################################################## -->
	<div class="row">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h4><?=lang('lang.playerlogs')?></h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="code_panel_body">
		    <ul class="nav nav-tabs">
		      <li><a href="#" data-load="/affiliate/transactionHistory/<?=$player_id?>" data-callback="transactionHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('pay.transhistory')?></a></li>
		      <li><a href="#" data-load="/affiliate/gamesHistory/<?=$player_id?>" data-callback="gamesHistory" data-params='{"player_id":<?=$player_id?>}'><?=lang('player.ui48')?></a></li>
		    </ul>
		    <div id="changeable_table"></div>
		  </div>
		</div>
	</div>
	<!-- ################################################################## END Member's Logs ################################################################## -->


</div>

<script type="text/javascript">
	$(function() {
    $('[data-load]').click( function (e) {

        e.preventDefault();

        var el = $(this);
        var url = el.data('load');
        var params = el.data('params');
        var callback = el.data('callback');
        var player_id = params['player_id'];

        if (el.parent('li').hasClass('active')) {
            return;
        }

        $('#changeable_table').load(url, params, function(data) {

            if (el.parent('ul.nav')) {
                el.parents('ul.nav').find('li').removeClass('active');
                el.parent('li').addClass('active');
            }

            $('#changeable_table .dateInput').each( function () {
                initDateInput($(this));
            });

            if (callback) {
                eval(callback)(player_id);
            }

        });
    });
});

function transactionHistory(player_id) {
    var dataTable = $('#transaction-table').DataTable({

        autoWidth: false,
        searching: false,
        dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        columnDefs: [
            { className: 'text-right', targets: [ 4,5,6,9 ] },
            { visible: false, targets: [ 10,11,12 ] }
        ],
        order: [[0, 'desc']],

        // SERVER-SIDE PROCESSING
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {

            data.extra_search = [
                {
                    'name':'dateRangeValueStart',
                    'value':$('#dateRangeValueStart').val()
                },
                {
                    'name':'dateRangeValueEnd',
                    'value':$('#dateRangeValueEnd').val()
                },
            ];

            $.post(base_url + 'api/transactionHistory/' + player_id, data, function(data) {

                if (data.summary) {
                    $('#summary').html('');
                    $.each(data.summary, function(key, value) {
                        $('#summary').append('<div class="col-xs-11">'+key+':</div><div class="col-xs-1">'+value+'</div>');
                    });
                }

                callback(data);
            },'json');

        }

    });

    $('#changeable_table #btn-submit').click( function() {
        dataTable.ajax.reload();
    });

}

function gamesHistory(player_id) {
    var dataTable = $('#gamehistory-table').DataTable({

        lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
        autoWidth: false,
        searching: false,
        dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        columnDefs: [
            { className: 'text-right', targets: [ 5,6,7,8 ] },
            { visible: false, targets: [ 1 ] }
        ],

        order: [[0, 'desc']],
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#changeable_table #search-form').serializeArray();
            $.post(base_url + 'api/gamesHistory/' + player_id, data, function(data) {
                $('#changeable_table #search-total').text(data.searchTotal);
                callback(data);
            },'json');

        }

    });

    $('#changeable_table input, #changeable_table select').change( function() {
        dataTable.ajax.reload();
    });

}
</script>