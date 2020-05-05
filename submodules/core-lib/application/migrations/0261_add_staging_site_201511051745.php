<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_staging_site_201511051745 extends CI_Migration {

	private $tableName = 'static_sites';

	public function up() {

		$sql = <<<EOD
insert into static_sites(site_name,site_url,template_name,template_path,status,lang,login_template,logged_template,asset_url,popup_template,pt_game_type_template,pt_game_template)
select ?,site_url,template_name,template_path,status,lang,login_template,logged_template,asset_url,popup_template,pt_game_type_template,pt_game_template
 from static_sites where site_name="default"
EOD;

		$this->db->query($sql, array('staging'));

	}

	public function down() {
		$this->db->where("site_name", 'staging')->delete($this->tableName);
	}
}