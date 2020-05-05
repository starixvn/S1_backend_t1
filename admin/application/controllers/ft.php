<?php
class Ft extends CI_Controller {

 function __construct()
 {
  parent::__construct();
 }

 public function index()
 {
  require_once(APPPATH . 'libraries/phpass-0.1/PasswordHash.php');
  $hasher = new PasswordHash('8', TRUE);
  $testps = $hasher->HashPassword("bnJIP&vgf");
  echo $testps;
  echo "<br />";
  echo BASEURL;	
  echo "<br />";
  echo APPPATH;	
  echo "<br />";
  echo $_SERVER['HTTP_HOST'];
  echo "<br />";
  echo str_replace("admin.","",$_SERVER['HTTP_HOST']);
 }
}
?>