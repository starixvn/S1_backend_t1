<?php
trait withdrawal_process_flow_module {

	public function getAllTagsForPendingReview(){
		$this->load->model('player');
		$tags = $this->player->getAllTagsOnly();
        $tag_status = json_decode($this->operatorglobalsettings->getSetting('withdraw_pending_review_tags')->value,true);

		$arr = array('status' => 'success', 'tags' => $tags, 'tagStatus' => $tag_status);
		echo json_encode($arr);
	}

	public function pendingRequestTags(){
		$this->load->model(array('player'));
		$taggedIds = $this->input->post('tagId');
		
		if ($taggedIds) {
			
			$data = array("withdraw_pending_review_tags"=>json_encode($taggedIds));
			$newTag = $this->operatorglobalsettings->saveSettings($data);
				
			echo json_encode(array("status"=>"success",
								   "tagStatus" =>$taggedIds)
							);
		}else{
            $this->operatorglobalsettings->saveSettings( array("withdraw_pending_review_tags"=> null));
            $arr = array( "status"=>"success", "tagStatus" => array() );
            echo json_encode($arr);
		}
	}

	/*
	 * This function will check if player tag is under withdraw pending tag
	 *
	 * param int $playerId
	 */
	public function checkPlayerIfTagIsUnderPendingWithdrawTag($playerId){
		$this->load->model(array('player_model','operatorglobalsettings'));
		$playerTag = $this->player_model->getPlayerTags($playerId,true);

		if(!$playerTag) return false;
		$withdrawPendingTags = json_decode($this->operatorglobalsettings->getSetting('withdraw_pending_review_tags')->value,true);
		if(!empty($withdrawPendingTags)){
			foreach ($playerTag as $key => $value) {
				if(in_array($value, $withdrawPendingTags)){
					return true;
				}
			}
		}
		return false;
	}
}