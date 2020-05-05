<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_agent_payment_history_20171107 extends CI_Migration {

	private $tableName = 'agent_payment_history';

	public function up() {
		$sql = <<<EOD
CREATE TABLE IF NOT EXISTS `agent_payment_history` (
  `agent_payment_history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,                                   
  `agent_id` int(10) unsigned NOT NULL DEFAULT '0',                                                    
  `payment_method` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',                                
  `amount` double NOT NULL DEFAULT '0',                                                                   
  `fee` double NOT NULL DEFAULT '0',                                                                      
  `status` int(11) NOT NULL DEFAULT '1',                                                                  
  `reason` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,                                             
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',                                            
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',                                            
  `agent_payment_id` int(10) unsigned NOT NULL DEFAULT '0',                                             
  `processed_on` datetime DEFAULT NULL,                                                                    
  `processed_by` int(11) DEFAULT NULL,                                                                     
  PRIMARY KEY (`agent_payment_history_id`),                                                              
  KEY `FK_agent_payment_history_agent_id` (`agent_id`)                                            
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=111;
EOD;
		$this->db->query($sql);
	}

	public function down() {
		$this->dbforge->drop_table($this->tableName, TRUE);
	}
}

///END OF FILE//////////
