<?php
// if (PHP_SAPI === 'cli') {
// 	exit('No web access allowed');
// }

class Ole777_wager extends CI_Controller {

	protected $usage = [
"
Usage: php admin/public/index.php cli/ole777_wager {command}" ,
"
Applicable commands:
	userinfo_fullsync   Upload all available users to remote db.  Run once only.
	userinfo_dailysync  Upload daily changes of user profiles to remote db.
	wagers_calc         Prepare daily wager records in local db.
	wagers_sync         Upload checked daily wager records to remote db.
	wagers_calc_range	Shorthand for running wagers_calc over a range.
	wagers_discard      Discard wagers in a given interval.  Actually moving
	                    them some years backwards so they are not accessible.
	wagers_remove       Maintenance use.
		(Use SBE/Marketing management/Ole777 Wager Sync to check wager records)
"];

	function __construct() {
		parent::__construct();
		$this->load->library(['ole_reward_lib']);
	}

	public function index() {
		$this->writelnhi($this->usage[0]);
		$this->writeln($this->usage[1]);
	}

	public function help() {
		$this->index();
	}

	public function userinfo_fullsync() {
		$res = $this->ole_reward_lib->userinfo_full_update();
		$this->writeln($res['mesg']);
	}

	public function userinfo_dailysync() {
		$res = $this->ole_reward_lib->userinfo_daily_update();
		$this->utils->debug_log(__METHOD__, "sync_result", $res);
		if (isset($res['mesg'])) {
			$this->writeln($res['mesg']);
		}
		else {

			$num_ins = count($res['inserted']);
			$num_upd = count($res['updated']);
			$json_ins = json_encode($res['inserted']);
			$json_upd = json_encode($res['updated']);
			$this->utils->debug_log(__METHOD__, "Userinfo daily sync complete", ['num_inserted' => $num_ins, 'num_updated' => $num_upd]);
			$this->writeln("Players inserted: $num_ins\nPlayers updated: $num_upd");
			$this->writeln("Inserted: $json_ins\nUpdated: $json_upd");
		}
	}

	public function wagers_calc($calc_date = null) {
		$res = $this->ole_reward_lib->build_daily_wagerdata($calc_date);
		$num_wagers = count($res[0]);
		$num_summaries = count($res[1]);
		$this->utils->debug_log(__METHOD__, "Calculation complete", ['num_wager_records' => $num_wagers, 'num_summary_items' => $num_summaries]);
		$disp_date = empty($calc_date) ? 'today' : $calc_date;
		$this->writeln("Calculation complete for {$disp_date}, generated {$num_wagers} wager records, {$num_summaries} summary items.");
	}

	public function wagers_calc_range($date_from = null, $date_to = null) {
		if (empty($date_from) || empty($date_to)) {
			$this->writeln("Specify date_from and date_to.");
		}
		$dt_from	= strtotime($date_from);
		$dt_to		= strtotime($date_to);
		for ($d = $dt_from; $d <= $dt_to; $d += 86400) {
			$calc_date = date('Y-m-d', $d);
			$res = $this->ole_reward_lib->build_daily_wagerdata($calc_date);
			$num_wagers = count($res[0]);
			$num_summaries = count($res[1]);
			if ($num_wagers <= 0) { continue; }
			$this->utils->debug_log(__METHOD__, "Calculation complete", ['calc_date' => $calc_date, 'num_wager_records' => $num_wagers, 'num_summary_items' => $num_summaries]);
			$disp_date = empty($calc_date) ? 'today' : $calc_date;
			$this->writeln("Calculation complete for {$disp_date}, generated {$num_wagers} wager records, {$num_summaries} summary items.");
		}
	}

	public function wagers_sync() {
		$res = $this->ole_reward_lib->wager_daily_update();
		if (empty($res)) {
			$this->writeln("Sync stopped.  Nothing to sync.");
			return;
		}
		foreach ($res as $key => $ids) {
			if (empty($ids)) {
				$this->writeln("{$key}: Sync successful");
			}
			else {
				$ids_joint = implode(', ', $ids);
				$this->writeln("{$key}: Sync failed.  Failed wager records: {$ids_joint}");
			}
		}
	}

	/**
	 * Discard wagers in a given interval - In effect move these wagers by $date_offset
	 * years backward so they are not accessible in general operation.
	 * @param	datetime	$date_from		Start of interval
	 * @param	datetime	$date_to		End of interval
	 * @param	int			$date_offset	offset, in years.  Note: positive means backward.
	 *
	 * @return	none
	 */
	public function wagers_discard($date_from, $date_to, $offset) {
		$dw_from	= date('Ymd', strtotime($date_from));
		$dw_to		= date('Ymd', strtotime($date_to));
		$offset		= intval($offset);

		$dw_min		= '20170101';
		$dw_present	= date('Ymd');

		$offset_min = 20;

		try {
			// if ($dw_from < $dw_min || $dw_from > $dw_present) {
			if ($dw_from > $dw_present) {
				throw new Exception("Illegal value '{$date_from}' for date_from, should be within [ {$dw_min}, {$dw_present} ]", 2501);
			}

			// if ($dw_to < $dw_min || $dw_to > $dw_present) {
			if ($dw_to > $dw_present) {
				throw new Exception("Illegal value '{$date_to}' for date_to, should be within [ {$dw_min}, {$dw_present} ]", 2502);
			}

			if ($dw_from > $dw_to) {
				$dwtmp = $dw_from; $dw_from = $dw_to; $dw_to = $dwtmp;
			}

			if (abs($offset) < $offset_min) {
				throw new Exception("Offset too low, cannot be within [ -{$offset_min}, +{$offset_min} ]", 2503);
			}

			// If all right
			$offset_abs = abs($offset);
			$offset_way = $offset > 0 ? 'backward' : 'forward';
			$this->writeln("wagers_discard: Shifting wager records.  Interval = [ {$dw_from}, {$dw_to} ], Offset = {$offset} ({$offset_abs} years {$offset_way})");
			$this->utils->debug_log(__METHOD__, 'Wager shift starting', [ 'from_to_raw' => "{$date_from}-{$date_to}", 'from_to' => "{$dw_from}-{$dw_to}", 'offset' => $offset, 'offset_notes' => "{$offset_abs} years {$offset_way}" ]);

			$res = $this->ole_reward_lib->wager_interval_shift($dw_from, $dw_to, $offset);

			$this->utils->debug_log(__METHOD__, 'Wager shift complete', $res);
			$this->writeln("wagers_discard: Wager shifting complete. interval = [ {$dw_from}, {$dw_to} ], Offset = {$offset}");
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ]);
			$this->writeln("wagers_discard: Exception: {$ex->getCode()}: {$ex->getMessage()}");
		}

	}

	public function wagers_remove($date_from, $date_to) {
		$dw_from	= date('Ymd', strtotime($date_from));
		$dw_to		= date('Ymd', strtotime($date_to));

		// $dw_min		 '20170101';
		$dw_present	= date('Ymd');

		$offset_min = 20;

		try {
			if ($dw_from > $dw_present) {
				throw new Exception("Illegal value '{$date_from}' for date_from, should be within [ {$dw_min}, {$dw_present} ]", 2601);
			}

			if ($dw_to > $dw_present) {
				throw new Exception("Illegal value '{$date_to}' for date_to, should be within [ {$dw_min}, {$dw_present} ]", 2602);
			}

			if ($dw_from > $dw_to) {
				$dwtmp = $dw_from; $dw_from = $dw_to; $dw_to = $dwtmp;
			}

			// If all right
			$this->writeln("wagers_remove: Removing wager records.  Interval = [ {$dw_from}, {$dw_to} ]");
			$this->utils->debug_log(__METHOD__, 'Wager removing starting', [ 'from_to_raw' => "{$date_from}-{$date_to}", 'from_to' => "{$dw_from}-{$dw_to}" ]);

			$res = $this->ole_reward_lib->wager_interval_remove($dw_from, $dw_to);

			$this->utils->debug_log(__METHOD__, 'Wager removing complete', $res);
			$this->writeln("wagers_remove: Wager removing complete. interval = [ {$dw_from}, {$dw_to} ]");
		}
		catch (Exception $ex) {
			$this->utils->debug_log(__METHOD__, 'Exception', [ 'code' => $ex->getCode(), 'mesg' => $ex->getMessage() ]);
			$this->writeln("wagers_discard: Exception: {$ex->getCode()}: {$ex->getMessage()}");
		}

	}

	protected function writeln($s) { echo "$s\n"; }
	protected function writelnhi($s) { echo "\033[1;37m$s\033[0m\n"; }

} // End of class Ole777_wager
