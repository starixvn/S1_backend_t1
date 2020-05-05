<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_manual_promotion_201604060948 extends CI_Migration {

	const PROMO_TYPE_NAME = '_SYSTEM_MANUAL';
	const PROMO_RULE_NAME = '_SYSTEM_MANUAL';
	const PROMO_CMS_NAME = '_SYSTEM_MANUAL';

	public function up() {

		$this->load->model(array('player_model'));

		$this->player_model->startTrans();

		$sql = <<<EOD
INSERT INTO promotype
(`promoTypeName`,`promoTypeDesc`,`createdBy`,`updatedBy`,`createdOn`,`updatedOn`,`status`,`promoTypeCode`)
VALUES
(?, 'only use by admin for manual bonus', '1', NULL, now(), now(), '0', ?)
EOD;

		$this->db->query($sql, array(self::PROMO_TYPE_NAME, self::PROMO_TYPE_NAME));
		$promotypeId = $this->db->insert_id();

		$sql = <<<EOD
INSERT INTO promorules
(`promoName`,`noEndDateFlag`,`promoCategory`,`applicationPeriodStart`,`applicationPeriodEnd`,
`promoCode`,`promoDesc`,`promoType`,`depositConditionType`,`depositConditionDepositAmount`,
`depositConditionNonFixedDepositAmount`,`nonfixedDepositAmtCondition`,`nonfixedDepositAmtConditionRequiredDepositAmount`,`bonusApplication`,`depositSuccesionType`,
`depositSuccesionCnt`,`depositSuccesionPeriod`,`bonusApplicationRule`,`bonusApplicationLimitRule`,`bonusApplicationLimitRuleCnt`,
`repeatConditionBetCnt`,`bonusReleaseRule`,`bonusReleaseToPlayer`,`bonusAmount`,`depositPercentage`,
`maxBonusAmount`,`withdrawRequirementRule`,`withdrawRequirementConditionType`,`withdrawRequirementBetAmount`,`withdrawRequirementBetCntCondition`,
`nonDepositPromoType`,`gameRequiredBet`,`gameRecordStartDate`,`gameRecordEndDate`,`createdOn`,
`updatedOn`,`createdBy`,`updatedBy`,`promoStatus`,`status`,`hide_date`,
`nonfixedDepositMinAmount`,`nonfixedDepositMaxAmount`,`json_info`,`formula`)
VALUES
(?, '0', ?, '2016-01-01 00:00:00',
'0000-00-00 00:00:00', '', '', '0', '0',
'0', '1', '0', '0', '0',
'0', '0', '4', '0', '0',
'0', '0', '3', '0', '0',
'0', '0', '0', '2', '0',
'0', '0', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00',
'2016-01-01 00:00:00', NULL, '1', NULL, '0',
'0', '2016-01-01 00:00:00', '0', '0',
'{\"applicationPeriodStart\":\"2016-01-01 00:00:00\",\"promoType\":\"0\",\"noEndDateFlag\":0,\"depositConditionNonFixedDepositAmount\":\"1\",\"nonfixedDepositMinAmount\":false,\"nonfixedDepositMaxAmount\":false,\"depositSuccesionType\":\"0\",\"depositSuccesionCnt\":false,\"depositSuccesionPeriod\":\"4\",\"bonusApplicationLimitRule\":\"0\",\"bonusApplicationLimitRuleCnt\":false,\"bonusReleaseRule\":\"3\",\"bonusReleaseToPlayer\":0,\"bonusAmount\":false,\"depositPercentage\":0,\"maxBonusAmount\":false,\"withdrawRequirementConditionType\":\"2\",\"withdrawRequirementBetAmount\":false,\"withdrawRequirementBetCntCondition\":false,\"nonDepositPromoType\":\"0\",\"gameRequiredBet\":false,\"gameRecordStartDate\":false,\"gameRecordEndDate\":false,\"hide_date\":\"2016-01-01 00:00:00\"}',
'{\"bonus_release\":\"0\",\"withdraw_condition\":null}'
)
EOD;

		$this->db->query($sql, array(self::PROMO_RULE_NAME, $promotypeId));
		$promoruleId = $this->db->insert_id();

		$sql = <<<EOD
INSERT INTO promocmssetting
(`promoName`,`promoDescription`,`promoDetails`,`createdOn`,`updatedOn`,
`createdBy`,`updatedBy`,`promoId`,`promoThumbnail`,`status`,
`language`)
VALUES (
?, 'only use by admin for manual bonus', '<p>only use by admin for manual bonus<br></p>', '2016-04-06 09:42:31', NULL,
'1', NULL, ?, '', 'active', 'en'
)
EOD;

		$this->db->query($sql, array(self::PROMO_CMS_NAME, $promoruleId));

		if (!$this->player_model->endTransWithSucc()) {
			throw new Exception('add manual promotion failed');
		}

	}

	public function down() {
		$this->load->model(array('player_model'));

		$this->player_model->startTrans();
		$this->db->query('delete from promocmssetting where promoName=?', array(self::PROMO_CMS_NAME));
		$this->db->query('delete from promorules where promoName=?', array(self::PROMO_RULE_NAME));
		$this->db->query('delete from promotype where promoTypeName=?', array(self::PROMO_RULE_NAME));
		if (!$this->player_model->endTransWithSucc()) {
			throw new Exception('delete manual promotion failed');
		}
	}
}
