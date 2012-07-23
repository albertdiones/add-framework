<?php
/**
 * ADD MVC Framework Initialization
 * requires $C config variable
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Functions
 * @since ADD MVC 0.0
 * @version 0.2
 */

/**
 * Minimum version requirement
 * To be used to check if PHP Version is correct
 * @since ADD MVC 0.1
 */
DEFINE('ADD_MIN_PHP_VERSION','5.3.8');
if (version_compare(phpversion(),ADD_MIN_PHP_VERSION) === -1) {
   die("ADD MVC Error: PHP version must be at least ".ADD_MIN_PHP_VERSION." or higher!");
}


if (!isset($C))
   die("No config found");
require $C->add_dir.'/classes/add.class.php';

$GLOBALS[add::CONFIG_VARNAME] = add::config($C);

spl_autoload_register('add::load_class');
set_exception_handler('add::handle_exception');
set_error_handler('add::handle_error');
register_shutdown_function('add::handle_shutdown');


$C->incs_dir            = $C->root_dir.'/includes';

$C->classes_dir         = $C->incs_dir.'/classes';

$C->configs_dir         = $C->incs_dir.'/configs';
$C->views_dir           = $C->incs_dir.'/views';
$C->caches_dir          = $C->incs_dir.'/caches';

if (add::is_development() && !is_writeable($C->caches_dir)) {
   $C->caches_dir = sys_get_temp_dir().'/add_mvc_caches';
   if (!file_exists($C->caches_dir))
      mkdir($C->caches_dir,0700);
}

$C->assets_dir          = $C->root_dir.'/assets';
$C->images_dir          = $C->assets_dir.'/images';
$C->css_dir             = $C->assets_dir.'/css';
$C->js_dir              = $C->assets_dir.'/js';

$C->domain              = $C->sub_domain.".".$C->super_domain;
$C->base_url            = "http://$C->domain".$C->path;

set_include_path($C->incs_dir);


/**
 * assets
 * @author albertdiones@gmail.com
 */
$C->assets_path = $C->base_url.'assets/';
$C->css_path    = $C->assets_path.'css/';
$C->js_path     = $C->assets_path.'js/';
$C->images_path = $C->assets_path.'images/';
$C->assets_libs_path   = $C->assets_path.'libs/';

/**
 * No errors if live
 *
 * @since 0.7
 */
if (add::is_live()) {
   error_reporting(0);
}
else {
   error_reporting(E_ALL);
}

add::load_functions('common');

/**
 * Libraries
 */
add::load_lib('adodb');
add::load_lib('smarty');
