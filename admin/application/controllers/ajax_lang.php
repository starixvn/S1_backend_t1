<?php
class Ajax_lang extends CI_Controller {

 function __construct()
 {
  parent::__construct();
  $this->load->library('permissions');
 }

 public function index($pram)
 {
  echo lang($pram);
 }
}
?>
