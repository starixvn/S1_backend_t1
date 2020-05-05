<?php
trait wallet_module {

	public function big_wallet_details($playerId){
		$result=['success'=>true];
		if(empty($playerId)){
			$result['success']=false;
			$result['message']=lang('Wrong Player Info');
			return $this->returnJsonResult($result);
		}

		$this->load->model(['wallet_model']);

		$bigWallet=$this->wallet_model->getBigWalletByPlayerId($playerId);

		$result['bigWallet']=$bigWallet;
		$this->returnJsonResult($result);
	}

}

///END OF FILE/////