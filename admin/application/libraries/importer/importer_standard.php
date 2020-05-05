<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/abstract_importer.php';

class Importer_standard extends Abstract_importer{

	function __construct() {
		parent::__construct();
		$this->import_player_csv_header=self::IMPORT_PLAYER_CSV_HEADER;
		$this->import_player_contact_csv_header=self::IMPORT_PLAYER_CONTACT_CSV_HEADER;
		$this->import_player_bank_csv_header=self::IMPORT_PLAYER_BANK_CSV_HEADER;
		$this->import_aff_csv_header=self::IMPORT_AFF_CSV_HEADER;
		$this->import_aff_contact_csv_header=self::IMPORT_AFF_CONTACT_CSV_HEADER;
	}

	const IMPORT_PLAYER_CSV_HEADER=['RealName', 'Birthday', 'GenderID', 'CountryID', 'Mobile',
		'CurrencyID', 'Email', 'CreateDate', 'UserCode', 'AffiliateCode',
		'OddsTypeID', 'RiskCateID', 'AvailableBalance', 'Status', 'UserCategoryID',
		'UserCategoryName', 'Last Login Time'];
	const IMPORT_PLAYER_CONTACT_CSV_HEADER=['UserCode', 'ContactType', 'ContactAccount'];
	const IMPORT_PLAYER_BANK_CSV_HEADER=['UserCode', 'UserBankAccountName', 'BankAccountNo', 'BankName', 'BranchBankName', 'Remark'];
	const IMPORT_AFF_CSV_HEADER=['AffiliateCode', 'Status', 'AffiliateID', 'Code Settings', 'CurrencyID',
		'CountryID', 'RealName', 'Gender', 'CreateDate', 'Birthday',
		'Mobile', 'Email', 'PromotionWebsite', 'Experience', 'Domain',
		'Notes', 'Remarks'];
	const IMPORT_AFF_CONTACT_CSV_HEADER=['AffiliateCode', 'ContactType', 'ContactAccount'];

	public function importCSV(array $files, &$summary, &$message){

	}


}

