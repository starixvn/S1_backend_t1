<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_ips_payment_in_external_system extends CI_Migration {

	public function up() {
		$this->db->query("UPDATE external_system set live_url='https://pay.ips.com.cn/ipayment.aspx',
sandbox_url='http://pay.ips.net.cn/ipayment.aspx',
live_key='',
live_secret='',
sandbox_key='000015',
sandbox_secret='GDgLwwdK270Qj1w4xho8lyTpRQZV9Jm5x4NwWOTThUa4fMhEBK9jOXFrKRT6xhlJuU2FEa89ov0ryyjfJuuPkcGzO5CeVx5ZIrkkt1aBlZV36ySvHOMcNv8rncRiy3DQ',
live_mode=0
where id=" . IPS_PAYMENT_API);
	}

	public function down() {
		// $this->db->query('DELETE * FROM `rolefunctions');
	}
}
