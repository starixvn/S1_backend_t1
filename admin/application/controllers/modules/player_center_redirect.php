<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

trait player_center_redirect {

	/**
	 * Ole777 reward site one-key login endpoint
	 * Creates a form of username and token, then automatically
	 * posts to reward system login URL (stored in config_secret_local)
	 *
	 * @uses	config item	'ole777_reward_conf'.'reward_login_url'
	 * @uses	view file	(view_root)/player/redirect_form
	 * @return	rendered view
	 */
	public function reward_site() {
		try {
			$username	= $this->authentication->getUsername();
			$token		= $this->authentication->getPlayerToken();

			if (empty($username) || empty($token)) {
				throw new Exception('Error getting username or token', 0x101);
			}

			$orconf	= $this->utils->getConfig('ole777_reward_conf');
			if (empty($orconf)) {
				throw new Exception('ole777_reward_conf not found', 0x102);
			}

			if (!isset($orconf['reward_login_url']) || empty($orconf['reward_login_url'])) {
				throw new Exception('reward_login_url not found or empty in ole777_reward_conf', 0x103);
			}

			$reward_login_url = $orconf['reward_login_url'];

			$data['username']	= $username;
			$data['token']		= $token;
			$data['title']		= lang('ole777_wager.please_wait');
			$data['from_host']	= $this->utils->getSystemUrl('player', '/');
			$data['reward_login_url']	= $reward_login_url;

			$this->load->view($this->utils->getPlayerCenterTemplate() . '/player/redirect_form', $data);
		}
		catch (Exception $ex) {
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, lang('error.default.message') . " ({$ex->getCode()})");
			$this->utils->debug_log(__METHOD__, 'exception', $ex->getCode(), $ex->getMessage());
			redirect('/home');
			return;
		}
	} // End function reward_site()

}

/// END OF FILE/////////////