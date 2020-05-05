<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/*
|--------------------------------------------------------------------------
| Active template
|--------------------------------------------------------------------------
|
| The $template['active_template'] setting lets you choose which template
| group to make active.  By default there is only one group (the
| "default" group).
|
 */
$template['active_template'] = 'default';

/*
|--------------------------------------------------------------------------
| Explaination of template group variables
|--------------------------------------------------------------------------
|
| ['template'] The filename of your master template file in the Views folder.
|   Typically this file will contain a full XHTML skeleton that outputs your
|   full template or region per region. Include the file extension if other
|   than ".php"
| ['regions'] Places within the template where your content may land.
|   You may also include default markup, wrappers and attributes here
|   (though not recommended). Region keys must be translatable into variables
|   (no spaces or dashes, etc)
| ['parser'] The parser class/library to use for the parse_view() method
|   NOTE: See http://codeigniter.com/forums/viewthread/60050/P0/ for a good
|   Smarty Parser that works perfectly with Template
| ['parse_template'] FALSE (default) to treat master template as a View. TRUE
|   to user parser (see above) on the master template
|
| Region information can be extended by setting the following variables:
| ['content'] Must be an array! Use to set default region content
| ['name'] A string to identify the region beyond what it is defined by its key.
| ['wrapper'] An HTML element to wrap the region contents in. (We
|   recommend doing this in your template file.)
| ['attributes'] Multidimensional array defining HTML attributes of the
|   wrapper. (We recommend doing this in your template file.)
|
| Example:
| $template['default']['regions'] = array(
|    'header' => array(
|       'content' => array('<h1>Welcome</h1>','<p>Hello World</p>'),
|       'name' => 'Page Header',
|       'wrapper' => '<div>',
|       'attributes' => array('id' => 'header', 'class' => 'clearfix')
|    )
| );
|
 */

/*
|--------------------------------------------------------------------------
| Default Template Configuration (adjust this or create your own)
|--------------------------------------------------------------------------
 */

$template['default']['name'] = 'default';
$template['default']['template'] = 'template/stable_center2';
$template['default']['regions'] = array(
    'main_content', 'sidebar_content', 'activenav', 'skin',
    'title', 'description', 'keywords', 'sidebar', 'username', 'player_id', 'mainwallet', 'isLogged', 'ignoreWebpush',
    'timeReminders',
);
$template['default']['parser'] = 'parser';
$template['default']['parser_method'] = 'parse';
$template['default']['parse_template'] = FALSE;

//for iframe_module.php
$template['iframe']['name'] = 'iframe';
$template['iframe']['template'] = 'template/iframe_template';
$template['iframe']['regions'] = array(
	'main_content', 'sidebar_content', 'activenav', 'skin',
	'title', 'description', 'keywords', 'sidebar', 'username', 'player_id', 'mainwallet', 'isLogged', 'ignoreWebpush',
	'timeReminders',
);
$template['iframe']['parser'] = 'parser';
$template['iframe']['parser_method'] = 'parse';
$template['iframe']['parse_template'] = FALSE;

// stable_center2
$template['stable_center2']['name'] = 'stable_center2';
$template['stable_center2']['template'] = 'template/stable_center2';
$template['stable_center2']['regions'] = array(
		'main_content', 'sidebar_content', 'activenav', 'skin',
		'title', 'description', 'keywords', 'sidebar', 'username', 'player_id', 'mainwallet', 'isLogged', 'ignoreWebpush',
		'timeReminders',
);
$template['stable_center2']['parser'] = 'parser';
$template['stable_center2']['parser_method'] = 'parse';
$template['stable_center2']['parse_template'] = FALSE;

// Inclusive Site Player Center
$template['ispc']['name'] = 'ispc';
$template['ispc']['template'] = 'template/ispc';
$template['ispc']['extend'] = 'stable_center2';
$template['ispc']['regions'] = array(
    'main_content', 'sidebar_content', 'activenav', 'skin',
    'title', 'description', 'keywords', 'sidebar', 'username', 'player_id', 'mainwallet', 'isLogged', 'ignoreWebpush',
    'timeReminders',
);
$template['ispc']['parser'] = 'parser';
$template['ispc']['parser_method'] = 'parse';
$template['ispc']['parse_template'] = FALSE;

/* End of file template.php */
/* Location: ./system/application/config/template.php */