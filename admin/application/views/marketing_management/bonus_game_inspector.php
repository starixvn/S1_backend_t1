
<!DOCTYPE html>
<?php

	function array_prepare($ar, $widths) {

		$tbody = '';
		$thead_ar = [];
		$row_count = 0;

		if (!is_array($ar) || count($ar) == 0) {
			$tbody = '<tr></tr>';
		}
		else {
			foreach ($ar as $key => $row) {
				++$row_count;
				$tbody .= '<tr>';
				foreach ($row as $col => $cell) {
					$wid = null;
					if (is_array($widths)) {
						$wid = isset($widths[$col]) ? $widths[$col] : $widths['_default'];
					}
					if (!isset($thead_ar[$col]))
						{ $thead_ar[$col] = "<th style='width: $wid%;'>$col</th>"; }
					$tbody .= "<td style='width: $wid%;'>$cell</td>";
				}
				$tbody .= '</tr>';
			}
		}

		return [$tbody, $thead_ar, $row_count];
	}

	function view_array($ar, $table) {
		$widths = null;
		$table_layout = '';
		if (isset($table['widths'])) {
			$widths = $table['widths'];
			$table_layout = 'table-layout: fixed;';
		}

		list($tbody, $thead_ar, $row_count) = array_prepare($ar, $widths);

		$show_table_class = $table['show'] == 1 ? '' : 'wrapped';
		$show_tbody_html  = $table['show'] == 1 ? '' : ' style="display: none; " ';

		$thead = implode('', $thead_ar);

		$no_data_class = $row_count == 0 ? 'nodata' : '';

		?>
			<table id="tbl-<?= $table['id'] ?>" class="<?= $show_table_class ?> <?= $no_data_class ?>" data-count="<?= $row_count ?>" style="<?= $table_layout ?>">
				<caption>
					<?= $table['id'] ?>
					<div class="header hd-left">
						<?= sprintf("%02d (%d)", $table['seq'], $row_count) ?>
					</div>
					<div class="header hd-right">
						<i class="fa up fa-chevron-up"></i>
						<i class="fa down fa-chevron-down"></i>
					</div>
				</caption>
				<tbody  >
					<tr><?= $thead ?></tr>
					<?= $tbody ?>
				</tbody>
			</table>
		<?php
	}


	function array_as_form($ar, $opt = []) {
		?>
			<table class="tedit-frm" id="tedit-frm">
				<caption>
					<?= isset($opt['title']) ? $opt['title'] : '' ?>
				</caption>
				<thead>
					<tr>
						<td colspan="5"></td>
						<td colspan="5" style="text-align: right;">
							<button type="button" class="dbop export">export</button>
							<button type="button" class="dbop cancel">reload</button>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="sm">id</th>
						<th class="sm">game_id</th>
						<th class="sm">gametype_id</th>
						<th class="sm">theme_id</th>
						<th class="md">res_name</th>
						<th class="sm">index</th>
						<th class="lg">value</th>
						<th class="md">type</th>
						<th class="md">updated_at</th>
						<th class="md">preview</th>
						<th class="md">op</th>
					</tr>
					<?php foreach ($ar as $row) : ?>
						<tr>
							<td class="sm"><input type="text" class="col id"          value="<?= $row["id"         ] ?>" name="id"          /></td>
							<td class="sm"><input type="text" class="col game_id"     value="<?= $row["game_id"    ] ?>" name="game_id"     /></td>
							<td class="sm"><input type="text" class="col gametype_id" value="<?= $row["gametype_id"] ?>" name="gametype_id" /></td>
							<td class="sm"><input type="text" class="col theme_id"    value="<?= $row["theme_id"   ] ?>" name="theme_id"    /></td>
							<td class="md"><input type="text" class="col res_name"    value="<?= $row["res_name"   ] ?>" name="res_name"    /></td>
							<td class="sm"><input type="text" class="col index"       value="<?= $row["index"      ] ?>" name="index"       /></td>
							<td class="lg"><input type="text" class="col value"       value="<?= $row["value"      ] ?>" name="value"       /></td>
							<td class="md"><input type="text" class="col type"        value="<?= $row["type"       ] ?>" name="type"        /></td>
							<td class="md"><input type="text" class="col updated_at"  value="<?= $row["updated_at" ] ?>" name="updated_at"  /></td>
							<td class="md">
								<?php if (!empty($row['fullpath'])) : ?>
									<img height="25" src="<?= $row['fullpath'] ?>" />
									<span class="im-error" style="display: none;">
										<i class="fa fa-times"></i> NOT FOUND</span>
								<?php endif; ?>
							</td>
							<td class="md">
								<button type="button" class="op cp">cp</button>
								<button type="button" class="op pair rm">rm</button>
								<button type="button" class="op pair undo" style="display: none;">undo</button>
								<button type="button" class="op ok" style="display: none;">ok</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="float-frame" style="display: none;">
				<div class="im-info">
				</div>
				<img src="http://admin.og.local/resources/images/bonus_games/wheel/newyear/newyear_arrow.png" />
			</div>

		<?php
	}
?>
<html>
	<head>
		<title>SMERCH-NOSE</title>
		<link rel="stylesheet" href="/resources/css/font-awesome.min.css" />
	</head>
	<style type="text/css">
		body { font-family: sans-serif; font-size: 9pt; }
		h3 { font-style: italic; background-color: #c3e0f9; padding: 0.5em 0; text-align: center; }
		table { margin: 1.5em 0; font-size: 9pt; border-collapse: collapse; width: 90%;}
		caption { padding: 0.3em 0; font-style: italic; font-weight: bold; background-color: #c3e0f9; }
		table.wrapped { margin: 0.5em 0; }
		table.wrapped caption { /*background-color: #f9dfc3;*/ background-color: #e2ecf4; }
		table.wrapped tbody { display: none; }
		table.nodata caption { color: #999; }
		caption .header.hd-right { float: right; }
		caption .header.hd-left  { float: left; }
		caption > .header { padding: 0 0.5em; }
		caption > * > .up, .wrapped caption > * > .down { display: block; }
		.wrapped caption > * > .up, caption > * > .down { display: none; }
		caption .header i.fa { padding-top: 2px; }
		td,th { border: 1px solid #bbb; padding: 0.3em; font-size: 8pt; word-break:break-all; }
		th { font-style: italic; font-weight: normal; }
		td { font-family: iosevka; ; }
		.menu { width: 170px; z-index: 100; position: fixed; right: 5px; top: 40px; background-color: #00800033;}
		.menu ul { margin: 1em 0em 1em -2em; list-style-type: none; }
		.menu ul li { line-height: 1.4em; font-style: italic; }
		.menu ul li.active a { color: white; background-color: #0a6995; }
		.menu a         { color: black; padding: 0.1em 0.4em; text-decoration: none;}
		.menu a:link    { color: black; }
		.menu a:visited { color: black; }
		.menu a:hover   { color: white; background-color: #1497d3;}
		.menu a:active  { color: white; }
		.menu li.buttons { margin-top: 0.4em; line-height: 2em;}
		.menu li.buttons .button { border-radius: 10px; font-size: 9pt; border: 1px solid gray; height: 1.7em; }
		.menu li.buttons .button:hover { background-color: #226797; color: white; }
		.menu li.buttons .button:active { background-color: #319be5; color: white; }
		.form-row { margin-bottom: 1em; }
		.cell-w { width: 80%; }
		.cell-w textarea { width: 100%; }
		.pull-right { float: right; }
		.form-row button { width: 80px; }
		textarea { font-size: 9pt; font-family: iosevka; }
		.api-tests { font-size: 9pt; font-family: iosevka; }
		.api-tests input { font-size: 9pt; font-family: iosevka; }
		.api-params .fc { display: inline-block; }
		.fc.f1 { width: 140px; }
		.fc.f2 { width: 400px; }
		.fc.f3 { width: 300px; }
		.fc input { width: 90%; }
		.fc .lamp { margin-right: 0.5em; }
		.lamp.red	{ color: red; }
		.lamp.green	{ color: green; }
		#api-urls { width: 800px; height: 120px; border: 1px solid #999; overflow-y: scroll; overflow-x: hidden; white-space: pre; word-break: break-all; }
		.api-urls { width: 80%; }
		.api-urls input { width: 80% }
		table.tedit-frm { /*table-layout: fixed; */ }
		.tedit-frm td, .tedit-frm th { border: none; text-align: center; /*display: inline-block;*/ }
		.tedit-frm th { background-color: #b2ebd8; height: 2em; word-break: break-all; }
		.tedit-frm td input { font-family: iosevka; font-size: 9pt; width: 100%; border-radius: 10px; border: 1px solid #aaa; ;}
		/*.tedit-frm th.sm, .tedit-frm td.sm { min-width: 3%; }
		.tedit-frm th.md, .tedit-frm td.md { min-width: 10%; }
		.tedit-frm th.lg, .tedit-frm td.lg { min-width: 15%; }*/
		.tedit-frm th.sm { width: 45px; }
		.tedit-frm th.lg { width: 305px; }
		.tedit-frm th.md { width: 155px; }
		.tedit-frm td.sm input { text-align: center; width: 40px; }
		.tedit-frm td.lg input { padding: 0 5px; width: 300px; }
		.tedit-frm td.md input { padding: 0 5px; width: 150px; }
		.tedit-frm button { font-family: iosevka; font-size: 9pt; border-radius: 10px; border: 1px solid #aaa; background-color: #def0ff; }
		.tedit-frm button:hover  { color: yellow; background-color: #777; }
		.tedit-frm button:active { color: orange; background-color: #000; }
		.tedit-frm tr.removed input { color: gray; background: #ddd; }
		.tedit-frm .im-error { padding: 0 0.7em; font-size: 9pt; color: #ab1b1b; font-weight: bold; }
		.tedit-frm tr.op-disabled { background-color: #e6e6dc; cursor: not-allowed; }
		.tedit-frm tr.current { background: linear-gradient(#f1f4f6, #ddeaf4, #f1f4f6); }
		.tedit-frm tr.op-disabled.current { background: none; background-color: #e6e6dc; }

		.float-frame { position: fixed; top: 20px; left: 20px; z-index: 400; background-color: #8ad09d87; padding: 15px; border-radius: 10px; font-style: italic; font-weight: bold; text-align: center; }
		.float-frame img { background-color: #33755175;}
		.float-frame .im-info { padding: 0.25em 1em; color: #fff; background: #242;}

	</style>
	<body>
		<div class="menu_wrap">
			<div class="menu">
				<ul>
					<?php foreach ($tables as $alias => $table) : ?>
						<li>
							<i class='fa fa-angle-right'></i>
							<a href="#tbl-<?= $table['id'] ?>"><?= sprintf("%02d", $table['seq']) ?> <?= $alias ?></a>
						</li>
					<?php endforeach; ?>
					<li class="buttons">
						<button type="button" class="button open-all">Open all</button>
						<button type="button" class="button shrink-all">Shrink all</button>
						<button type="button" class="button non-blank-only">Non-blank only</button>
						<br />
						<button type="button" class="button dbop-sw on" style="display: none;">dbop</button>
						<button type="button" class="button dbop-sw off" style="display: none;">dbop off</button>
					</li>
				</ul>
			</div>
		</div>

		<div class="api-tests">
			<h4>API test sample calls</h4>
			<div class="api-params">
				<div><label>

					<div class="fc f1">token</div>
					<div class="fc f2"><span class="lamp"><i class="fa fa-circle"></i></span><input type="text" id="token" /></div>
					<div class="fc f3"><button id="recalc">RECALC</button></div>
				</label></div>
				<div><label>
					<div class="fc f1">player_game_id</div>
					<div class="fc f2"><span class="lamp"><i class="fa fa-circle"></i></span><input type="text" id="player_game_id" /></div>
				</label></div>
				<div><label>
					<div class="fc f1">external_request_id</div>
					<div class="fc f2"><span class="lamp"><i class="fa fa-circle"></i></span><input type="text" id="external_request_id" /></div>
				</label></div>
				<div><label>
					<div class="fc f1">request_promotion_id</div>
					<div class="fc f2"><span class="lamp"><i class="fa fa-circle"></i></span><input type="text" id="request_promotion_id" /></div>
				</label></div>
				<div><label>
					<div class="fc f1">datatype</div>
					<div class="fc f2"><span class="lamp"><i class="fa fa-circle"></i></span><input type="text" id="datatype" /></div>
				</label></div>
			</div>
			<!-- <div id="api-urls"></div> -->
			<div class="api-urls">
				<div>
					<span class="lamp"><i class="fa fa-circle"></i></span>
					<input type="text" class="apit request_play_game_list" />
					<button type="button" class="apiurls cp">cp</button>
				</div>
				<div>
					<span class="lamp"><i class="fa fa-circle"></i></span>
					<input type="text" class="apit request_bonus" />
					<button type="button" class="apiurls cp">cp</button>
				</div>
				<div>
					<span class="lamp"><i class="fa fa-circle"></i></span>
					<input type="text" class="apit release_bonus" />
					<button type="button" class="apiurls cp">cp</button>
				</div>
				<div>
					<span class="lamp"><i class="fa fa-circle"></i></span>
					<input type="text" class="apit get_bonus_history_list" />
					<button type="button" class="apiurls cp">cp</button>
				</div>
			</div>
		</div>

		<?php foreach ($tables as $alias => $table) : ?>
			<?php view_array($dset[$alias], $table); ?>
		<?php endforeach; ?>

		<?php if (!empty($ops)) : ?>
			<div class="ops" id="ops">
				<h3>table operations</h3>
				<div class="form-row">
					<div class="cell-w">
						<label><input type="radio" name="table" value="promo_game_player_to_games" /> promo_game_player_to_games</label>
						<label><input type="radio" name="table" value="promo_game_resources" /> promo_game_resources</label>
						<label><input type="radio" name="table" value="promo_game_player_game_history" /> promo_game_player_game_history</label>
					</div>
					<div class="cell-w">
						<label><input type="radio" name="op" value="insert" /> Insert</label>
						<label><input type="radio" name="op" value="delete" /> Delete</label>
						<label><input type="radio" name="op" value="dump" /> Dump</label>
						<label><input type="radio" name="op" value="import" /> Import</label>
						<span class="pull-right">
							<button class="op_button" id="op_go" type="button">Go</button>
							<button class="op_button" id="op_test" type="button">Test</button>
						</span>
					</div>
				</div>
				<div class="form-row">
					<div class="cell-w">
						<textarea id="op_arg" name="op_arg" cols="120" rows="4"></textarea>
						<textarea id="op_resp" name="op_resp" cols="120" rows="20" readonly></textarea>
					</div>
				</div>
			</div>


			<?php array_as_form($dset['resources_ext'], [ 'title' => 'promo_game_resources']); ?>
		<?php endif; ?>

	</body>
</html>

<script type="text/javascript" src="/resources/js/jquery-1.11.1.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		(function init_rm_undo() {
			$('.tedit-frm').on('click', '.op.pair', function () {
				var b_toggle = $(this).siblings('.op.pair:hidden');
				$(this).toggle();
				$(b_toggle).toggle();
			});

			$('.tedit-frm').on('click', '.op.rm', function () {
				var row = $(this).parents('tr');
				$(row).addClass('removed');
				$(this).siblings('.op.ok').attr('data-act', 'rm').show();
			});

			// $('.tedit-frm .op.undo').click( function () {
			$('.tedit-frm').on('click', '.op.undo', function () {
				var row = $(this).parents('tr');
				$(row).removeClass('removed');
				$(this).siblings('.op.ok').removeAttr('data-act').hide();
			});
		})();

		(function init_overhead() {
			$('.tedit-frm .dbop.cancel').click( function () {
				window.location.reload();
			});

			$('.tedit-frm .dbop.export').click( function () {
				var xhr = $.get('/marketing_management/test_bonus_game_resource_export' , {}
				)
				.done( function(resp) {
					op_res_out('** resources export')
					op_res_out(resp.result);
				})
				.fail( function(resp) {
					console.log('export error', resp)
				});
			});
		})();

		// $('.tedit-frm input').change( function () {
		$('.tedit-frm').on('change', 'input', function () {
			var row = $(this).parents('tr');
			$(row).find('.op.ok').show();
		})

		// $('.tedit-frm .op.cp').click(function() {
		$('.tedit-frm').on('click', '.op.cp', function() {
			var new_row = $(this).parents('tr').clone();
			$(new_row).find('.col.id').val('');
			// $(new_row).find('.op.ok').attr('data-act').show();
			$(new_row).find('.op.ok').attr('data-act', 'ins').show();
			var tbody = $(this).parents('tbody');
			$(tbody).append(new_row);
		});

		// $('.tedit-frm').on('error', 'img', function () {
		// 	console.log('img-error', this);
		// 	$(this).next('.im-error').show();
		// 	$(this).hide();
		// });

		$('.tedit-frm').on('click', '.op.ok', function() {
			var row = $(this).parents('tr');
			var id_val = $(row).find('.col.id').val();
			var act = $(this).data('act');
			var button = this;

			var opverb = null;
			if (!id_val) {
				if (act == 'rm') {
					console.log('remove html');
					$(row).remove();
					return;
				}
				else {
					console.log('insert');
					opverb = 'insert';
				}
			}
			else {
				if (act == 'rm') {
					console.log('remove db row');
					opverb = 'remove';
				}
				else {
					console.log('update');
					opverb = 'update';
				}
			}

			var params_ar = { arg: {} };
			$(row).find('input').each(function () {
				var name = $(this).attr('name');
				var val = $(this).val();
				params_ar.arg[name] = val;
			});

			// var params = $(params_ar).serializeArray();
			params_ar.arg.action = opverb;
			$.post('/marketing_management/test_bonus_game_resource_ops' ,
				params_ar ,
				function (resp) {
					if (resp.success = true) {
						// window.location.reload();
						$(button).hide()
						$(button).parents('tr').addClass('op-disabled');
						$(button).parents('tr').find('td input, td button').attr('disabled', 1);
						setTimeout(function() { alert(opverb + ' successful'); }, 100);
					}
					else {
						console.log('error', resp);
					}
				}
			);
		});

		$('.tedit-frm').on('mouseenter', 'img', function () {
			$('.float-frame').show();
			var im_src = $(this).attr('src');
			var win_h = $(window).height(), win_w = $(window).width();
			var ava_win_h = win_h * 0.8, ava_win_w = win_w * 0.3;
			var im_hndl = $('.float-frame img');
			$(im_hndl).removeAttr('height').removeAttr('width');
			$(im_hndl).attr('src', im_src);

			var frame_im = $(im_hndl)[0];
			var nw = frame_im.naturalWidth, nh = frame_im.naturalHeight;
			if (nw > ava_win_w) {
				$(im_hndl).removeAttr('height').attr('width', ava_win_w);
			}
			if (nh > ava_win_h) {
				$(im_hndl).removeAttr('width').attr('height', ava_win_h);
			}
			var rw = $(im_hndl).width(), rh = $(im_hndl).height();

			var im_info_hndl = $('.float-frame .im-info');
			var im_info = '%nw x %nh resized to %rw x %rh'
				.replace('%nw', nw).replace('%nh', nh)
				.replace('%rw', rw.toFixed(0)).replace('%rh', rh.toFixed(0));
			$(im_info_hndl).text(im_info);

		});

		$('.tedit-frm').on('mouseleave', 'img', function () {
			$('.float-frame').hide();
		});

		$('.tedit-frm').on('mouseenter', 'tr', function () {
			$(this).addClass('current');
		});
		$('.tedit-frm').on('mouseleave', 'tr', function () {
			$(this).removeClass('current');
		});

		// Delayed img checking
		setTimeout(function () {
			$('.tedit-frm img').each(function () {
				// console.log(this.naturalHeight, this.naturalWidth);
				if (this.naturalHeight == 0 && this.naturalWidth == 0) {
					$(this).next('.im-error').show();
					$(this).hide();
				}
			});
		}, 900);

	});
</script>
<script type="text/javascript">

 	var basepath = location.origin + '/api/t1t_game/';
 	var api_list = [
 		{ api: 'request_play_game_list' ,
 		  params: [ 'token' ]
 		} ,
 		{ api: 'request_bonus' ,
 		  params: [ 'token' , 'external_request_id' , 'player_game_id' ]
 		} ,
 		{ api: 'release_bonus' ,
 		  params: [ 'token' , 'external_request_id' , 'request_promotion_id' ]
 		} ,
 		{ api: 'get_bonus_history_list' ,
 		  params: [ 'token' ] ,
 		  opt_params : [ 'datatype' ]
 		}
 	];

 	$(document).ready(function () {
 		$('#recalc').click( function () {
 			// $('#api-urls').html('');
 			for (var i in api_list) {
 				var apit = api_list[i];

 				var get_arg_ar = [];
 				var comp = true;
 				for (var j in apit.params) {
 					var field = apit.params[j];
 					var val = $('#' + field).val();
 					var lamp = $('#' + field).siblings('.lamp');
 					$(lamp).removeClass('red green');
 					if (val.length == 0) {
 						$(lamp).addClass('red');
 						comp = false;
 					}
 					else {
 						$(lamp).addClass('green');
 					}
 					get_arg_ar.push(field + "=" + val);
 				}
 				for (var k in apit.opt_params) {
 					var field = apit.opt_params[j];
 					var val = $('#' + field).val();

 					get_arg_ar.push(field + "=" + val);
 				}


 				var get_args = get_arg_ar.join('&');
 				var apit_path_tmpl = basepath + "$API?$GETARGS";
 				var apit_path = apit_path_tmpl.replace('$API', apit.api).replace('$GETARGS', get_args);
 				// Fill result
 				var input = $('input.apit.' + apit.api);
 				var lamp = $(input).siblings('.lamp');
 				$(input).val(apit_path);
 				$(lamp).removeClass('red green').addClass(comp ? 'green' : 'red');

 				console.log(apit.api, comp);
 			}
 		});

 		$('button.apiurls.cp').click(function () {
 			var input = $(this).siblings('input.apit');
 			$(input)[0].select();
 			document.execCommand('Copy');
 		});
 	});

</script>
<script>
	function op_res_out(txt) {
		var op_resp_txt = $('#op_resp').text();
		var ts = (new Date()).toTimeString().substr(0, 8);
		op_resp_txt += "-- " + ts + "\n";
		op_resp_txt += txt + "\n";
		$('#op_resp').text(op_resp_txt);
	}

	function toggle_table(target) {
		var toggle_delay = 100;

		var table = $(this).parent('table');
		var tbody = $(this).siblings('tbody');

		if ($(tbody).is(':visible')) {
			$(table).addClass('wrapped');
		}
		else {
			$(table).removeClass('wrapped');
		}
	}

	$(document).ready( function () {
		(function handler_op_go_click() {
			var button = '.op_button';
			$(button).click(function () {
				var args = {
					op: $('input[name="op"]:checked').val() ,
					op_arg: $('#op_arg').val() ,
					table: $('input[name="table"]:checked').val() ,
				};
				if ($(this).attr('id') == 'op_go') {
					args['go'] = 1;
				}
				$.post('/marketing_management/test_bonus_game_ops', args, function(res) {
					if (res.success == true) {
						op_res_out(res.result);
					}
					else {
						op_res_out('ERROR - ' + res.message);
					}
				});
			});
		})();

		(function init_table_toggle() {
			$('caption').click(toggle_table);
			// Init run
			// $('caption.wrapped').each(function() { toggle_table(this); });
		})();

		(function init_menu_a_click() {
			$('.menu ul li').click(function () {
				$(this).siblings().each(function () {
					$(this).removeClass('active');
				})
				$(this).addClass('active');
			})
		})();

		(function init_menu_buttons() {
			$('.menu li.buttons .open-all').click(function () {
				$('table.wrapped').removeClass('wrapped');
			});
			$('.menu li.buttons .shrink-all').click(function () {
				$('table').addClass('wrapped');
			});
			$('.menu li.buttons .non-blank-only').click(function () {
				$('table').removeClass('wrapped');
				$('table[data-count="0"]').addClass('wrapped');
			});

			$('.menu li.buttons .dbop-sw.on').click(function () {
				window.location = '?dbop=1#ops';
			});

			$('.menu li.buttons .dbop-sw.off').click(function () {
				window.location = '?';
			});

			if (window.location.search.indexOf('dbop') > 0) {
				$('.menu li.buttons .dbop-sw.off').show();
			}
			else {

				$('.menu li.buttons .dbop-sw.on').show();
			}
		})();


	});
</script>