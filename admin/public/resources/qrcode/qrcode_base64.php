<?php
error_reporting(E_ERROR);
require_once 'phpqrcode/phpqrcode.php';
$url = base64_decode($_GET["data"]);
QRcode::png($url);
