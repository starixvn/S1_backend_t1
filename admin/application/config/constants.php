<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}

//move version to version.php
include dirname(__FILE__) . "/version.php";
//==========================================
// define('PRODUCTION_VERSION', '3.01.00.0101');
//==========================================

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

//FHI Constants
if (empty($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = 'localhost';
}
define('BASEURL', '/');
define('AGENT_TRACKING_BASE_URL', '/ag/');
define('STORAGEPATH', realpath(APPPATH . '../storage'));
define('PUBLICPATH', realpath(APPPATH . '../public'));
define('PLAYER_INTERNAL_BASE_PATH', '/player/internal');
define('PLAYER_INTERNAL_PATH', STORAGEPATH . PLAYER_INTERNAL_BASE_PATH);
define('VIEWPATH', realpath(APPPATH . 'views'));
define('IMAGEPATH', '/resources/images/');
define('IMAGEPATH_BANNER', '/resources/images/');
define('IMAGEPATH_PROMOBANNER', '/resources/images/promothumbnails/');
define('IMAGEPATH_DEPOSITSLIP', '/resources/depositslip/');
define('CSSPATH', '/resources/css/');
define('JSPATH', '/resources/js/');
define('THIRDPARTYPATH', '/resources/third_party/');
define('QRCODEPATH', '/resources/qrcode/qrcode.php?data=');
define('QRCODE_BASE64PATH', '/resources/qrcode/qrcode_base64.php?data=');
define('DEPOSITSLIPPATH', realpath('../../admin/public/resources/depositslip'));
define('PROMOTHUMBNAILPATH', realpath('../../admin/public/resources/images/promothumbnails'));
define('SHOPPING_BANNER_IMG_PATH', realpath('../../admin/public/resources/images/shopping_banner'));
define('STATICSITESPATH', realpath('../../admin/public/resources/images/static_sites'));
define('TUTORIALICONPATH', realpath('../../admin/public/resources/images/tutorial'));
define('VIPCOVERPATH', realpath('../../admin/public/resources/images/vip_cover'));
define('VIPBADGEPATH', realpath('../../admin/public/resources/images/vip_badge'));
define('PLAYER_INTERNAL_DEPOSIT_RECEIPT_PATH', '/deposit_receipt/');
define('PLAYER_INTERNAL_KYC_ATTACHMENT_PATH', '/kyc_attachment/');
define('PLAYER_INTERNAL_REMOTE_LOGS_PATH', '/remote_logs/');

//images path
define('PROMOIMAGEPATH', 'http://admin.hll999.com/resources/images/promothumbnails/');
//define('PROMOCMSBANNERPATH', 'http://admin.'.$_SERVER['HTTP_HOST'].'/resources/images/cmsbanner/');
define('PROMOCMSBANNERPATH', 'http://admin.hll999.com/resources/images/cmsbanner/');
//define('LOGOPATH', 'http://admin.hll999.com/resources/images/cmslogo/');
define('LOGOPATH', 'http://admin.hll999.com/resources/images/cmslogo/');

define('COMMON_COUNTRY_LIST', serialize(
	array('China' => 'China', 'Japan' => 'Japan', 'South Korea' => 'South Korea', 'United States' => 'United States'))
);

define('COUNTRY_LIST', serialize(
	array('Afghanistan' => 'Afghanistan', 'Albania' => 'Albania', 'Algeria' => 'Algeria', 'American Samoa' => 'American Samoa', 'Andorra' => 'Andorra', 'Angola' => 'Angola', 'Anguilla' => 'Anguilla', 'Antigua and Barbuda' => 'Antigua and Barbuda', 'Argentina' => 'Argentina', 'Armenia' => 'Armenia', 'Aruba' => 'Aruba' , 'Australia' => 'Australia', 'Austria' => 'Austria', 'Azerbaijan' => 'Azerbaijan', 'Bahamas' => 'Bahamas', 'Bahrain' => 'Bahrain', 'Bangladesh' => 'Bangladesh', 'Barbados' => 'Barbados', 'Belarus' => 'Belarus', 'Belgium' => 'Belgium', 'Belize' => 'Belize', 'Benin' => 'Benin', 'Bermuda' => 'Bermuda', 'Bhutan' => 'Bhutan', 'Bolivia' => 'Bolivia', 'Bosnia and Herzegovina' => 'Bosnia and Herzegovina', 'Botswana' => 'Botswana', 'Brazil' => 'Brazil', 'British Virgin Islands' => 'British Virgin Islands', 'Brunei Darussalam' => 'Brunei Darussalam', 'Brunei' => 'Brunei', 'Bulgaria' => 'Bulgaria', 'Burkina Faso' => 'Burkina Faso', 'Burundi' => 'Burundi', 'Cambodia' => 'Cambodia', 'Cameroon' => 'Cameroon', 'Canada' => 'Canada', 'Cape Verde' => 'Cape Verde', 'Cayman Islands' => 'Cayman Islands', 'Central African Republic' => 'Central African Republic', 'Chad' => 'Chad', 'Chile' => 'Chile', 'China' => 'China', 'Christmas Island' => 'Christmas Island', 'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands', 'Colombia' => 'Colombia', 'Comoros' => 'Comoros', 'Cook Islands' => 'Cook Islands', "Congo (Brazzaville)" => "Congo (Brazzaville)", 'Congo' => 'Congo', 'Democratic Republic of the Congo' => 'Democratic Republic of the Congo', 'Costa Rica' => 'Costa Rica', "Cote d'Ivoire" => "Cote d'Ivoire", 'Croatia' => 'Croatia', 'Cuba' => 'Cuba', 'Curacao' => 'Curacao', 'Cyprus' => 'Cyprus', 'Czech Republic' => 'Czech Republic', 'Denmark' => 'Denmark', 'Djibouti' => 'Djibouti', 'Dominica' => 'Dominica', 'Dominican Republic' => 'Dominican Republic', 'East Timor (Timor Timur)' => 'East Timor (Timor Timur)', 'Ecuador' => 'Ecuador', 'Egypt' => 'Egypt', 'El Salvador' => 'El Salvador', 'Equatorial Guinea' => 'Equatorial Guinea', 'Eritrea' => 'Eritrea', 'Estonia' => 'Estonia', 'Ethiopia' => 'Ethiopia', 'Falkland Islands' => 'Falkland Islands', 'Faroe Islands' => 'Faroe Islands', 'Fiji' => 'Fiji', 'Finland' => 'Finland', 'France' => 'France', 'French Guiana' => 'French Guiana', 'French Polynesia' => 'French Polynesia', 'French Southern Territories' => 'French Southern Territories', 'Gabon' => 'Gabon', 'Gambia' => 'Gambia', 'Georgia' => 'Georgia', 'Germany' => 'Germany', 'Ghana' => 'Ghana', 'Gibraltar' => 'Gibraltar', 'Greece' => 'Greece', 'Greenland' => 'Greenland', 'Grenada' => 'Grenada', 'Guadeloupe' => 'Guadeloupe', 'Guam' => 'Guam', 'Guatemala' => 'Guatemala', 'Guernsey' => 'Guernsey', 'Guinea' => 'Guinea', 'Guinea-Bissau' => 'Guinea-Bissau', 'Guyana' => 'Guyana', 'Haiti' => 'Haiti', 'Honduras' => 'Honduras', 'Hong Kong' => 'Hong Kong', 'Hungary' => 'Hungary', 'Iceland' => 'Iceland', 'India' => 'India', 'Indonesia' => 'Indonesia', 'Iran, Islamic Republic of' => 'Iran, Islamic Republic of', 'Iraq' => 'Iraq', 'Ireland' => 'Ireland', 'Isle of Man' => 'Isle of Man', 'Israel' => 'Israel', 'Italy' => 'Italy', 'Jamaica' => 'Jamaica', 'Japan' => 'Japan', 'Jersey' => 'Jersey', 'Jordan' => 'Jordan', 'Kazakhstan' => 'Kazakhstan', 'Kenya' => 'Kenya', 'Kiribati' => 'Kiribati', 'North Korea' => 'North Korea', 'South Korea' => 'South Korea', 'Kuwait' => 'Kuwait', 'Kyrgyzstan' => 'Kyrgyzstan', 'Laos' => 'Laos', 'Latvia' => 'Latvia', 'Lebanon' => 'Lebanon', 'Lesotho' => 'Lesotho', 'Liberia' => 'Liberia', 'Libya' => 'Libya', 'Liechtenstein' => 'Liechtenstein', 'Lithuania' => 'Lithuania', 'Luxembourg' => 'Luxembourg', 'Macau' => 'Macau', 'Macedonia' => 'Macedonia', 'Madagascar' => 'Madagascar', 'Malawi' => 'Malawi', 'Malaysia' => 'Malaysia', 'Maldives' => 'Maldives', 'Mali' => 'Mali', 'Malta' => 'Malta', 'Marshall Islands' => 'Marshall Islands', 'Martinique' => 'Martinique', 'Mauritania' => 'Mauritania', 'Mauritius' => 'Mauritius', 'Mayotte' => 'Mayotte', 'Mexico' => 'Mexico', 'Micronesia' => 'Micronesia', 'Moldova' => 'Moldova', 'Monaco' => 'Monaco', 'Mongolia' => 'Mongolia', 'Montenegro' => 'Montenegro', 'Montserrat' => 'Montserrat', 'Morocco' => 'Morocco', 'Mozambique' => 'Mozambique', 'Myanmar' => 'Myanmar', 'Namibia' => 'Namibia', 'Nauru' => 'Nauru', 'Nepal' => 'Nepal', 'Netherlands' => 'Netherlands', 'New Caledonia' => 'New Caledonia', 'New Zealand' => 'New Zealand', 'Nicaragua' => 'Nicaragua', 'Niger' => 'Niger', 'Nigeria' => 'Nigeria', 'Niue' => 'Niue', 'Norfolk Island' => 'Norfolk Island', 'Northern Mariana Islands' => 'Northern Mariana Islands', 'Norway' => 'Norway', 'Oman' => 'Oman', 'Pakistan' => 'Pakistan', 'Palau' => 'Palau', 'Palestine' => 'Palestine', 'Panama' => 'Panama', 'Papua New Guinea' => 'Papua New Guinea', 'Paraguay' => 'Paraguay', 'Peru' => 'Peru', 'Philippines' => 'Philippines', 'Pitcairn' => 'Pitcairn' , 'Poland' => 'Poland', 'Portugal' => 'Portugal', 'Puerto Rico' => 'Puerto Rico', 'Qatar' => 'Qatar', 'Reunion' => 'Reunion', 'Romania' => 'Romania', 'Russia' => 'Russia', 'Rwanda' => 'Rwanda', 'Saint Barthelemy' => 'Saint Barthelemy', 'Saint Helena' => 'Saint Helena', 'Saint Kitts and Nevis' => 'Saint Kitts and Nevis', 'Saint Lucia' => 'Saint Lucia', 'Saint Martin' => 'Saint Martin', 'Saint Pierre and Miquelon' => 'Saint Pierre and Miquelon', 'Saint Vincent' => 'Saint Vincent', 'Samoa' => 'Samoa', 'San Marino' => 'San Marino', 'Sao Tome and Principe' => 'Sao Tome and Principe', 'Saudi Arabia' => 'Saudi Arabia', 'Senegal' => 'Senegal', 'Serbia' => 'Serbia', 'Seychelles' => 'Seychelles', 'Sierra Leone' => 'Sierra Leone', 'Singapore' => 'Singapore', 'Sint Maarten' => 'Sint Maarten', 'Slovakia' => 'Slovakia', 'Slovenia' => 'Slovenia', 'Solomon Islands' => 'Solomon Islands', 'Somalia' => 'Somalia', 'South Africa' => 'South Africa', 'South Sudan' => 'South Sudan', 'Spain' => 'Spain', 'Sri Lanka' => 'Sri Lanka', 'Sudan' => 'Sudan', 'Suriname' => 'Suriname', 'Svalbard and Jan Mayen' =>'Svalbard and Jan Mayen', 'Swaziland' => 'Swaziland', 'Sweden' => 'Sweden', 'Switzerland' => 'Switzerland', 'Syria' => 'Syria', 'Taiwan' => 'Taiwan', 'Tajikistan' => 'Tajikistan', 'Tanzania' => 'Tanzania', 'Thailand' => 'Thailand', 'Timor-Leste' => 'Timor-Leste', 'Togo' => 'Togo', 'Tokelau' => 'Tokelau', 'Tonga' => 'Tonga', 'Trinidad and Tobago' => 'Trinidad and Tobago', 'Tunisia' => 'Tunisia', 'Turkey' => 'Turkey', 'Turkmenistan' => 'Turkmenistan', 'Turks and Caicos Islands' => 'Turks and Caicos Islands', 'Tuvalu' => 'Tuvalu', 'Uganda' => 'Uganda', 'Ukraine' => 'Ukraine', 'United Arab Emirates' => 'United Arab Emirates', 'United Kingdom' => 'United Kingdom', 'United States' => 'United States', 'United States Minor Outlying Islands' => 'United States Minor Outlying Islands', 'Uruguay' => 'Uruguay', 'US Virgin Islands' => 'US Virgin Islands', 'Uzbekistan' => 'Uzbekistan', 'Vanuatu' => 'Vanuatu', 'Vatican city' => 'Vatican city', 'Venezuela' => 'Venezuela', 'Vietnam' => 'Vietnam', 'Wallis and Futuna' => 'Wallis and Futuna', 'Western Sahara' =>'Western Sahara', 'Yemen' => 'Yemen', 'Zambia' => 'Zambia', 'Zimbabwe' => 'Zimbabwe'))
);

define('COUNTRY_ISO2', serialize(
	array('Afghanistan' => 'AF', 'Albania' => 'AL', 'Algeria' => 'DZ', 'American Samoa' => 'AS','Andorra' => 'AD', 'Angola' => 'AO', 'Anguilla' => 'AI', 'Antigua and Barbuda' => 'AG', 'Argentina' => 'AR', 'Armenia' => 'AM', 'Aruba' => 'AW', 'Australia' => 'AU', 'Austria' => 'AT', 'Azerbaijan' => 'AZ', 'Bahamas' => 'BS', 'Bahrain' => 'BH', 'Bangladesh' => 'BD', 'Barbados' => 'BB', 'Belarus' => 'BY', 'Belgium' => 'BE', 'Belize' => 'BZ', 'Benin' => 'BJ', 'Bermuda' => 'BM', 'Bhutan' => 'BT', 'Bolivia' => 'BO', 'Bosnia and Herzegovina' => 'BA', 'Botswana' => 'BW', 'Brazil' => 'BR', 'British Virgin Islands' => 'VG', 'Brunei' => 'BN', 'Bulgaria' => 'BG', 'Burkina Faso' => 'BF', 'Burundi' => 'BI', 'Cambodia' => 'KH', 'Cameroon' => 'CM', 'Canada' => 'CA', 'Cape Verde' => 'CV', 'Cayman Islands' => 'KY', 'Central African Republic' => 'CF', 'Chad' => 'TD', 'Chile' => 'CL', 'China' => 'CN', 'Christmas Island' => 'CX', 'Cocos (Keeling) Islands' => 'CC', 'Colombia' => 'CO', 'Comoros' => 'KM', 'Cook Islands' =>'CK', 'Congo' => 'CG', 'Congo (Brazzaville)' => 'CO', 'Democratic Republic of the Congo' =>'CD', 'Costa Rica' => 'CR', 'Cote d\'Ivoire' => 'CI', 'Croatia' => 'HR', 'Cuba' => 'CU', 'Curacao' => 'CW', 'Cyprus' => 'CY', 'Czech Republic' => 'CZ', 'Denmark' => 'DK', 'Djibouti' => 'DJ', 'Dominica' => 'DM', 'Dominican Republic' => 'DO', 'East Timor (Timor Timur)' => 'TL', 'Ecuador' => 'EC', 'Egypt' => 'EG', 'El Salvador' => 'SV', 'Equatorial Guinea' => 'GQ', 'Eritrea' => 'ER', 'Estonia' => 'EE', 'Ethiopia' => 'ET', 'Falkland Islands' => 'FK', 'Faroe Islands' => 'FO', 'Fiji' => 'FJ', 'Finland' => 'FI', 'France' => 'FR', 'French Guiana' => 'GF', 'French Polynesia' => 'PF', 'French Southern Territories' => 'TF', 'Gabon' => 'GA', 'Gambia' => 'GM', 'Georgia' => 'GE', 'Germany' => 'DE', 'Ghana' => 'GH', 'Gibraltar' => 'GI', 'Greece' => 'GR', 'Greenland' => 'GL', 'Grenada' => 'GD', 'Guadeloupe' => 'GP', 'Guam' => 'GU', 'Guatemala' => 'GT', 'Guernsey' => 'GG', 'Guinea' => 'GN', 'Guinea-Bissau' => 'GW', 'Guyana' => 'GY', 'Haiti' => 'HT', 'Honduras' => 'HN', 'Hong Kong' => 'HK', 'Hungary' => 'HU', 'Iceland' => 'IS', 'India' => 'IN', 'Indonesia' => 'ID', 'Iran, Islamic Republic of' => 'IR', 'Iraq' => 'IQ', 'Ireland' => 'IE', 'Isle of Man' => 'IM', 'Israel' => 'IL', 'Italy' => 'IT', 'Jamaica' => 'JM', 'Japan' => 'JP', 'Jersey' => 'JE', 'Jordan' => 'JO', 'Kazakhstan' => 'KZ', 'Kenya' => 'KE', 'Kiribati' => 'KI', 'North Korea' => 'KP', 'South Korea' => 'KR', 'Kuwait' => 'KW', 'Kyrgyzstan' => 'KG', 'Laos' => 'LA', 'Latvia' => 'LV', 'Lebanon' => 'LB', 'Lesotho' => 'LS', 'Liberia' => 'LR', 'Libya' => 'LY', 'Liechtenstein' => 'LI', 'Lithuania' => 'LT', 'Luxembourg' => 'LU', 'Macau' => 'MO', 'Macedonia' => 'MK', 'Madagascar' => 'MG', 'Malawi' => 'MW', 'Malaysia' => 'MY', 'Maldives' => 'MV', 'Mali' => 'ML', 'Malta' => 'MT', 'Marshall Islands' => 'MH', 'Martinique' =>'MQ', 'Mauritania' => 'MR', 'Mauritius' => 'MU', 'Mayotte' => 'YT', 'Mexico' => 'MX', 'Micronesia' => 'MD', 'Moldova' => 'FM', 'Monaco' => 'MC', 'Mongolia' => 'MN', 'Montenegro' => 'ME', 'Montserrat' => 'MS', 'Morocco' => 'MA', 'Mozambique' => 'MZ', 'Myanmar' => 'MM', 'Namibia' => 'NA', 'Nauru' => 'NR', 'Nepal' => 'NP', 'Netherlands' => 'NL', 'New Caledonia' => 'NC', 'New Zealand' => 'NZ', 'Nicaragua' => 'NI', 'Niger' => 'NE', 'Nigeria' => 'NG', 'Niue' => 'NU' , 'Norfolk Island' => 'NF', 'Northern Mariana Islands' => 'MP', 'Norway' => 'NO', 'Oman' => 'OM', 'Pakistan' => 'PK', 'Palau' => 'PW', 'Palestine' => 'PS', 'Panama' => 'PA', 'Papua New Guinea' => 'PG', 'Paraguay' => 'PY', 'Peru' => 'PE', 'Philippines' => 'PH', 'Pitcairn' => 'PN', 'Poland' => 'PL', 'Portugal' => 'PT', 'Puerto Rico' => 'PR', 'Qatar' => 'QA', 'Reunion' => 'RE', 'Romania' => 'RO', 'Russia' => 'RU', 'Rwanda' => 'RW', 'Saint Barthelemy' => 'BL', 'Saint Helena' => 'SH', 'Saint Kitts and Nevis' => 'KN', 'Saint Lucia' => 'LC', 'Saint Martin' => 'MF', 'Saint Pierre and Miquelon' => 'PM', 'Saint Vincent' => 'VC', 'Samoa' => 'WS', 'San Marino' => 'SM', 'Sao Tome and Principe' => 'ST', 'Saudi Arabia' => 'SA', 'Senegal' => 'SN', 'Serbia' => 'RS', 'Seychelles' => 'SC', 'Sierra Leone' => 'SL', 'Singapore' => 'SG', 'Sint Maarten' => 'SX', 'Slovakia' => 'SK', 'Slovenia' => 'SI', 'Solomon Islands' => 'SB', 'Somalia' => 'SO', 'South Africa' => 'ZA', 'South Sudan' => 'SS', 'Spain' => 'ES', 'Sri Lanka' => 'LK', 'Sudan' => 'SD', 'Suriname' => 'SR', 'Svalbard and Jan Mayen' =>'SJ', 'Swaziland' => 'SZ', 'Sweden' => 'SE', 'Switzerland' => 'CH', 'Syria' => 'SY', 'Taiwan' => 'TW', 'Tajikistan' => 'TJ', 'Tanzania' => 'TZ', 'Thailand' => 'TH', 'Togo' => 'TG', 'Tokelau' =>'TK', 'Tonga' => 'TO', 'Trinidad and Tobago' => 'TT', 'Tunisia' => 'TN', 'Turkey' => 'TR', 'Turkmenistan' => 'TM', 'Turks and Caicos Islands' => 'TC', 'Tuvalu' => 'TV', 'Uganda' => 'UG', 'Ukraine' => 'UA', 'United Arab Emirates' => 'AE', 'United Kingdom' => 'GB', 'United States' => 'US', 'United States Minor Outlying Islands' => 'UM', 'Uruguay' => 'UY', 'US Virgin Islands' =>'VI', 'Uzbekistan' => 'UZ', 'Vanuatu' => 'VU', 'Vatican city' => 'VA', 'Venezuela' => 'VE', 'Vietnam' => 'VN', 'Wallis and Futuna' =>'WF', 'Western Sahara' =>'EH', 'Yemen' => 'YE', 'Zambia' => 'ZM', 'Zimbabwe' => 'ZW'))
);

define('COUNTRY_NUMBER_LIST', serialize(
	array('(+7 840)' => '(+7 840)', '(+7 940)' => '(+7 940)', '(+93)' => '(+93)', '(+355)' => '(+355)', '(+213)' => '(+213)', '(+1 684)' => '(+1 684)', '(+376)' => '(+376)', '(+244)' => '(+244)', '(+1 264)' => '(+1 264)', '(+1 268)' => '(+1 264)', '(+54)' => '(+1 268)', '(+374)' => '(+374)', '(+297)' => '(+374)', '(+61)' => '(+61)', '(+672)' => '(+672)', '(+43)' => '(+43)', '(+994)' => '(+994)', '(+1 242)' => '(+1 242)', '(+973)' => '(+973)', '(+880)' => '(+880)', '(+1 246)' => '(+1 246)', '(+1 268)' => '(+1 268)', '(+375)' => '(+375)', '(+32)' => '(+32)', '(+501)' => '(+501)', '(+229)' => '(+229)', '(+1 441)' => '(+1 441)', '(+975)' => '(+975)', '(+591)' => '(+591)', '(+387)' => '(+387)', '(+267)' => '(+267)', '(+55)' => '(+55)', '(+246)' => '(+246)', '(+1 284)' => '(+1 284)', '(+673)' => '(+673)', '(+359)' => '(+359)', '(+226)' => '(+226)', '(+257)' => '(+257)', '(+855)' => '(+855)', '(+237)' => '(+237)', '(+1)' => '(+1)', '(+238)' => '(+238)', '(+ 345)' => '(+ 345)', '(+236)' => '(+236)', '(+235)' => '(+235)', '(+56)' => '(+56)', '(+86)' => '(+86)', '(+61)' => '(+61)', '(+57)' => '(+57)', '(+269)' => '(+269)', '(+242)' => '(+242)', '(+243)' => '(+243)', '(+682)' => '(+682)', '(+506)' => '(+506)', '(+225)' => '(+225)', '(+385)' => '(+385)', '(+53)' => '(+53)', '(+599)' => '(+599)', '(+537)' => '(+537)', '(+420)' => '(+420)', '(+45)' => '(+45)', '(+246)' => '(+246)', '(+253)' => '(+253)', '(+1 767)' => '(+1 767)', '(+1 809)' => '(+1 809)', '(+1 829)' => '(+1 829)', '(+1 849)' => '(+1 849)', '(+670)' => '(+670)', '(+56)' => '(+56)', '(+593)' => '(+593)', '(+20)' => '(+20)', '(+503)' => '(+503)', '(+240)' => '(+240)', '(+291)' => '(+291)', '(+372)' => '(+372)', '(+251)' => '(+251)', '(+500)' => '(+500)', '(+298)' => '(+298)', '(+679)' => '(+679)', '(+358)' => '(+358)', '(+33)' => '(+33)', '(+596)' => '(+596)', '(+594)' => '(+594)', '(+689)' => '(+689)', '(+241)' => '(+241)', '(+220)' => '(+220)', '(+995)' => '(+995)', '(+49)' => '(+49)', '(+233)' => '(+233)', '(+350)' => '(+350)', '(+30)' => '(+30)', '(+299)' => '(+299)', '(+1 473)' => '(+1 473)', '(+590)' => '(+590)', '(+1 671)' => '(+1 671)', '(+502)' => '(+502)', '(+224)' => '(+224)', '(+245)' => '(+245)', '(+595)' => '(+595)', '(+509)' => '(+509)', '(+504)' => '(+504)', '(+852)' => '(+852)', '(+36)' => '(+36)', '(+354)' => '(+354)', '(+91)' => '(+91)', '(+62)' => '(+62)', '(+98)' => '(+98)', '(+964)' => '(+964)', '(+353)' => '(+353)', '(+972)' => '(+972)', '(+39)' => '(+39)', '(+1 876)' => '(+1 876)', '(+81)' => '(+81)', '(+962)' => '(+962)', '(+7 7)' => '(+7 7)', '(+254)' => '(+254)', '(+686)' => '(+686)', '(+850)' => '(+850)', '(+82)' => '(+82)', '(+965)' => '(+965)', '(+996)' => '(+996)', '(+856)' => '(+856)', '(+371)' => '(+371)', '(+961)' => '(+961)', '(+266)' => '(+266)', '(+231)' => '(+231)', '(+218)' => '(+218)', '(+423)' => '(+423)', '(+370)' => '(+370)', '(+352)' => '(+352)', '(+853)' => '(+853)', '(+389)' => '(+389)', '(+261)' => '(+261)', '(+265)' => '(+265)', '(+60)' => '(+60)', '(+960)' => '(+960)', '(+223)' => '(+223)', '(+356)' => '(+356)', '(+692)' => '(+692)', '(+596)' => '(+596)', '(+222)' => '(+222)', '(+230)' => '(+230)', '(+262)' => '(+262)', '(+52)' => '(+52)', '(+691)' => '(+691)', '(+1 808)' => '(+1 808)', '(+373)' => '(+373)', '(+377)' => '(+377)', '(+976)' => '(+976)', '(+382)' => '(+382)', '(+1664)' => '(+1664)', '(+212)' => '(+212)', '(+95)' => '(+95)', '(+264)' => '(+264)', '(+674)' => '(+674)', '(+977)' => '(+977)', '(+31)' => '(+31)', '(+599)' => '(+599)', '(+1 869)' => '(+1 869)', '(+687)' => '(+687)', '(64)' => '(64)', '(+505)' => '(+505)', '(+227)' => '(+227)', '(+234)' => '(+234)', '(+683)' => '(+683)', '(+672)' => '(+672)', '(+1 670)' => '(+1 670)', '(+47)' => '(+47)', '(+968)' => '(+968)', '(+92)' => '(+92)', '(+680)' => '(+680)', '(+970)' => '(+970)', '(+507)' => '(+507)', '(+675)' => '(+675)', '(+595)' => '(+595)', '(+51)' => '(+51)', '(+63)' => '(+63)', '(+48)' => '(+48)', '(+351)' => '(+351)', '(+1 787)' => '(+1 787)', '(+1 939)' => '(+1 939)', '(+974)' => '(+974)', '(+262)' => '(+262)', '(+40)' => '(+40)', '(+7)' => '(+7)', '(+250)' => '(+250)', '(+685)' => '(+685)', '(+378)' => '(+378)', '(+966)' => '(+966)', '(+221)' => '(+221)', '(+381)' => '(+381)', '(+248)' => '(+248)', '(+232)' => '(+232)', '(+65)' => '(+65)', '(+421)' => '(+421)', '(+386)' => '(+386)', '(+677)' => '(+677)', '(+27)' => '(+27)', '(+500)' => '(+500)', '(+34)' => '(+34)', '(+94)' => '(+94)', '(+249)' => '(+249)', '(+597)' => '(+597)', '(+268)' => '(+268)', '(+46)' => '(+46)', '(+41)' => '(+41)', '(+963)' => '(+963)', '(+886)' => '(+886)', '(+992)' => '(+992)', '(+255)' => '(+255)', '(+66)' => '(+66)', '(+670)' => '(+670)', '(+228)' => '(+228)', '(+690)' => '(+690)', '(+676)' => '(+676)', '(+1 868)' => '(+1 868)', '(+216)' => '(+216)', '(+90)' => '(+90)', '(+993)' => '(+993)', '(+1 649)' => '(+1 649)', '(+688)' => '(+688)', '(+256)' => '(+256)', '(+380)' => '(+380)', '(+971)' => '(+971)', '(+44)' => '(+44)', '(+1)' => '(+1)', '(+598)' => '(+598)', '(+1 340)' => '(+1 340)', '(+998)' => '(+998)', '(+678)' => '(+678)', '(+58)' => '(+58)', '(+84)' => '(+84)', '(+1 808)' => '(+1 808)', '(+681)' => '(+681)', '(+967)' => '(+967)', '(+260)' => '(+260)', '(+255)' => '(+255)', '(+263)' => '(+263)'))
);

define('SBE_DEFAULT_THEME', 'tot');

define('DEFAULT_ITEMS_PER_PAGE', 10);

// log filename
define('LOG_FILE_NAME', 'cronjob-logs');

// http://ogdev.ddns.net:8090/display/OG/Constants
include dirname(__FILE__) . "/../../../submodules/core-lib/application/config/apis.php";

define('RESPONSE_RESULT_PATH', '/var/log/response_results');

define('MANUAL_ONLINE_PAYMENT', 1);
define('AUTO_ONLINE_PAYMENT', 2);
define('LOCAL_BANK_OFFLINE', 3);

define('SECOND_CATEGORY_ONLINE_BANK', 1);
define('SECOND_CATEGORY_ALIPAY', 2);
define('SECOND_CATEGORY_WEIXIN', 3);
define('SECOND_CATEGORY_QQPAY', 4);
define('SECOND_CATEGORY_UNIONPAY', 5);
define('SECOND_CATEGORY_JDPAY', 6);
define('SECOND_CATEGORY_BANK_TRANSFER', 7);
define('SECOND_CATEGORY_ATM_TRANSFER', 8);
define('SECOND_CATEGORY_CRYPTOCURRENCY', 9);
define('SECOND_CATEGORY_QUICKPAY', 10);

define('READONLY_DATABASE', 'readonly');
define('SECONDREAD_DATABASE', 'secondread');

define('CUSTOM_WITHDRAWAL_PROCESSING_STAGES', 6);

define('DEPOSIT_PROCESS_MODE1', 1);
define('DEPOSIT_PROCESS_MODE2', 2);
define('DEPOSIT_PROCESS_MODE3', 3);

define('BANK_TYPE_ALIPAY', 21);
define('BANK_TYPE_WECHAT', 22);

define('COUNTRY_NUMBER_LIST_FULL',
	serialize([
		"Afghanistan" => "93",
		"Albania" => "355",
		"Algeria" => "213",
		"American Samoa" => "1-684",
		"Andorra" => "376",
		"Angola" => "244",
		"Anguilla" => "1-264",
		"Antigua and Barbuda" => "1-268",
		"Argentina" => "54",
		"Armenia" => "374",
		"Aruba" => "297",
		"Australia" => "61",
		"Austria" => "43",
		"Azerbaijan" => "994",
		"Bahamas" => "1-242",
		"Bahrain" => "973",
		"Bangladesh" => "880",
		"Barbados" => "1-246",
		"Belarus" => "375",
		"Belgium" => "32",
		"Belize" => "501",
		"Benin" => "229",
		"Bermuda" => "1-441",
		"Bhutan" => "975",
		"Bolivia" => "591",
		"Bosnia and Herzegovina" => "387",
		"Botswana" => "267",
		"Brazil" => "55",
		"British Virgin Islands" => "1-284",
		"Brunei Darussalam" => "673",
		"Bulgaria" => "359",
		"Burkina Faso" => "226",
		"Burundi" => "257",
		"Cambodia" => "855",
		"Cameroon" => "237",
		"Canada" => "1",
		"Cape Verde" => "238",
		"Cayman Islands" => "1-345",
		"Central African Republic" => "236",
		"Chad" => "235",
		"Chile" => "56",
		"China" => "86",
		"Christmas Island" => "61",
		"Cocos (Keeling) Islands" => "61",
		"Colombia" => "57",
		"Comoros" => "269",
		"Cook Islands" => "682",
		"Costa Rica" => "506",
		"Cote d'Ivoire" => "225",
		"Croatia" => "385",
		"Cuba" => "53",
		"Curacao" => "599",
		"Cyprus" => "357",
		"Czech Republic" => "420",
		"Democratic Republic of the Congo" => "243",
		"Denmark" => "45",
		"Djibouti" => "253",
		"Dominica" => "1-767",
		"Dominican Republic" => ["1-809", "1-829", "1-849"],
		"Ecuador" => "593",
		"Egypt" => "20",
		"El Salvador" => "503",
		"Equatorial Guinea" => "240",
		"Eritrea" => "291",
		"Estonia" => "372",
		"Ethiopia" => "251",
		"Falkland Islands" => "500",
		"Faroe Islands" => "298",
		"Fiji" => "679",
		"Finland" => "358",
		"France" => "33",
		"French Guiana" => "594",
		"French Polynesia" => "689",
		"French Southern Territories" => "262",
		"Gabon" => "241",
		"Gambia" => "220",
		"Georgia" => "995",
		"Germany" => "49",
		"Ghana" => "233",
		"Gibraltar" => "350",
		"Greece" => "30",
		"Greenland" => "299",
		"Grenada" => "1-473",
		"Guadeloupe" => "590",
		"Guam" => "1-671",
		"Guatemala" => "502",
		"Guernsey" => "44-1481",
		"Guinea" => "224",
		"Guinea-Bissau" => "245",
		"Guyana" => "592",
		"Haiti" => "509",
		"Honduras" => "504",
		"Hong Kong" => "852",
		"Hungary" => "36",
		"Iceland" => "354",
		"India" => "91",
		"Indonesia" => "62",
		"Iran, Islamic Republic of" => "98",
		"Iraq" => "964",
		"Ireland" => "353",
		"Isle of Man" => "44",
		"Israel" => "972",
		"Italy" => "39",
		"Jamaica" => "1-876",
		"Japan" => "81",
		"Jersey" => "44-1534",
		"Jordan" => "962",
		"Kazakhstan" => "7",
		"Kenya" => "254",
		"Kiribati" => "686",
		"Kuwait" => "965",
		"Kyrgyzstan" => "996",
		"Laos" => "856",
		"Latvia" => "371",
		"Lebanon" => "961",
		"Lesotho" => "266",
		"Liberia" => "231",
		"Libya" => "218",
		"Liechtenstein" => "423",
		"Lithuania" => "370",
		"Luxembourg" => "352",
		"Macau" => "853",
		"Macedonia" => "389",
		"Madagascar" => "261",
		"Malawi" => "265",
		"Malaysia" => "60",
		"Maldives" => "960",
		"Mali" => "223",
		"Malta" => "356",
		"Marshall Islands" => "692",
		"Martinique" => "596",
		"Mauritania" => "222",
		"Mauritius" => "230",
		"Mayotte" => "262",
		"Mexico" => "52",
		"Micronesia" => "691",
		"Moldova" => "373",
		"Monaco" => "377",
		"Mongolia" => "976",
		"Montenegro" => "382",
		"Montserrat" => "1-664",
		"Morocco" => "212",
		"Mozambique" => "258",
		"Myanmar" => "95",
		"Namibia" => "264",
		"Nauru" => "674",
		"Nepal" => "977",
		"Netherlands" => "31",
		"New Caledonia" => "687",
		"New Zealand" => "64",
		"Nicaragua" => "505",
		"Niger" => "227",
		"Nigeria" => "234",
		"Niue" => "683",
		"Norfolk Island" => "672",
		"North Korea" => "850",
		"Northern Mariana Islands" => "1-670",
		"Norway" => "47",
		"Oman" => "968",
		"Pakistan" => "92",
		"Palau" => "680",
		"Palestine" => "970",
		"Panama" => "507",
		"Papua New Guinea" => "675",
		"Paraguay" => "595",
		"Peru" => "51",
		"Philippines" => "63",
		"Pitcairn" => "64",
		"Poland" => "48",
		"Portugal" => "351",
		"Puerto Rico" => ["1-787", "1-939"],
		"Qatar" => "974",
		"Reunion" => "262",
		"Romania" => "40",
		"Russia" => "7",
		"Rwanda" => "250",
		"Saint Barthelemy" => "590",
		"Saint Helena" => "290",
		"Saint Kitts and Nevis" => "1-869",
		"Saint Lucia" => "1-758",
		"Saint Martin" => "590",
		"Saint Pierre and Miquelon" => "508",
		"Samoa" => "685",
		"San Marino" => "378",
		"Sao Tome and Principe" => "239",
		"Saudi Arabia" => "966",
		"Senegal" => "221",
		"Serbia" => "381",
		"Seychelles" => "248",
		"Sierra Leone" => "232",
		"Singapore" => "65",
		"Sint Maarten" => "1-721",
		"Slovakia" => "421",
		"Slovenia" => "386",
		"Solomon Islands" => "677",
		"Somalia" => "252",
		"South Africa" => "27",
		"South Korea" => "82",
		"South Sudan" => "211",
		"Spain" => "34",
		"Sri Lanka" => "94",
		"Sudan" => "249",
		"Suriname" => "597",
		"Svalbard and Jan Mayen" => "47",
		"Swaziland" => "268",
		"Sweden" => "46",
		"Switzerland" => "41",
		"Syria" => "963",
		"Taiwan" => "886",
		"Tajikistan" => "992",
		"Tanzania" => "255",
		"Thailand" => "66",
		"Timor-Leste" => "670",
		"Togo" => "228",
		"Tokelau" => "690",
		"Tonga" => "676",
		"Trinidad and Tobago" => "1-868",
		"Tunisia" => "216",
		"Turkey" => "90",
		"Turkmenistan" => "993",
		"Turks and Caicos Islands" => "1-649",
		"Tuvalu" => "688",
		"Uganda" => "256",
		"Ukraine" => "380",
		"United Arab Emirates" => "971",
		"United Kingdom" => "44",
		"United States" => "1",
		"United States Minor Outlying Islands" => "1",
		"Uruguay" => "598",
		"US Virgin Islands" => "1",
		"Uzbekistan" => "998",
		"Vanuatu" => "678",
		"Vatican city" => "379",
		"Venezuela" => "58",
		"Vietnam" => "84",
		"Wallis and Futuna" => "681",
		"Western Sahara" => "212",
		"Yemen" => "967",
		"Zambia" => "260",
		"Zimbabwe" => "263"

	])
);

define('PHONE_REGISTERED_NONE', 0);
define('PHONE_REGISTERED_YET', 1);
define('PHONE_REGISTERED_YET_AND_CHANGE_PASSWORD', 2);

define('CMSBANNER_CATEGORY_HOME', 1);
define('CMSBANNER_CATEGORY_MOBILE_HOME', 2);

define('AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER', 0);
define('AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT', 1);

define('ERROR_NO_ERROR', 0);
define('ERROR_PLAYER_NOT_EXISTS',           200001);
define('ERROR_AGENT_ALREADY_EXISTS',        300001);
define('ERROR_AGENT_ALREADY_BINDING',       300002);
define('ERROR_AGENT_REGISTRATION_FAILED',   300003);

define('_COMMAND_LINE_NULL', '_null');

define('PLAYER_CENTER_LOGO_PREFER_DEFAULT', 0);
define('PLAYER_CENTER_LOGO_PREFER_UPLOAD', 1);
define('PLAYER_CENTER_LOGO_PREFER_WWW', 2);

define('PLAYER_CENTER_MOBILE_HEADER_STYLE_LOGO_AND_TEXT', 1);
define('PLAYER_CENTER_MOBILE_HEADER_STYLE_ALL_LOGO', 2);

define('FLASH_MESSAGE_TYPE_SUCCESS', 'success');
define('FLASH_MESSAGE_TYPE_DANGER', 'danger');
define('FLASH_MESSAGE_TYPE_WARNING', 'warning');

// standard game ERROR CODE
define('ERR_PLAYER_NOT_EXIST', 1);
define('ERR_INSUFFICIENT_BALANCE', 2);
define('ERR_PLAYER_IS_BLOCKED', 3);
define('ERR_CENTS_TRANSFER_NOT_ALLOWED', 4);
define('ERR_TIME_OUT', 5);
define('ERR_MAINTENANCE', 6);
define('ERR_EXCEED_DECIMAL_AMOUNT', 7);
define('ERR_AGENT_NOT_EXIST', 8);
define('ERR_GAME_KEY_NOT_EXIST', 9);
define('ERR_IP_NOT_AUTHORIZED', 10);
define('ERR_GAME_ERROR_CODE', 999);
define('ERR_TEST_NOTIFICATION_ERROR', 1000);

define('STANDARD_ERROR_MSG', serialize(
    array(
        ERR_PLAYER_NOT_EXIST => 'Player Not Exists',
        ERR_INSUFFICIENT_BALANCE => 'Insufficient Player Balance In Game',
        ERR_PLAYER_IS_BLOCKED => 'Player Is Blocked',
        ERR_CENTS_TRANSFER_NOT_ALLOWED => 'Cents Transfer Are Not Allowed',
        ERR_TIME_OUT => 'Time Out',
        ERR_MAINTENANCE => 'Maintenance',
        ERR_EXCEED_DECIMAL_AMOUNT => 'Exceed Decimal Amount',
        ERR_AGENT_NOT_EXIST => 'Agent Not Exist',
        ERR_GAME_KEY_NOT_EXIST => 'Game Key Not Exist',
        ERR_IP_NOT_AUTHORIZED => 'Ip Is Not Authorized',
        ERR_GAME_ERROR_CODE => 'Error! Check game_error_code',
        ERR_TEST_NOTIFICATION_ERROR => 'Player Not Exist',
    )
));

define('GAMEPLATFROMPATH', realpath(APPPATH . '../../submodules/game-lib/game_platform'));
define('GAMEPLATFROMPATH_T1GAMES', realpath(APPPATH . '../../submodules/game-lib/game_platform/t1_api'));

/* End of file constants.php */
/* Location: ./application/config/constants.php */