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


if (!isset($C)) {
   $C = new STDClass();
}

# Sets the add_dir if not set. And it may be smart not to let the user(developer) set this on the first place
if (empty($C->add_dir)) {
   $C->add_dir = realpath(dirname(__FILE__));
}

require $C->add_dir.'/classes/add.class.php';

$GLOBALS[add::CONFIG_VARNAME] = add::config($C);

spl_autoload_register('add::load_class');
set_exception_handler('add::handle_exception');
set_error_handler('add::handle_error');
register_shutdown_function('add::handle_shutdown');


if (!isset($C->incs_dir))
   $C->incs_dir            = $C->root_dir.'/includes';

$C->classes_dirs        = array_merge(
      array( $C->incs_dir.'/classes' , $C->add_dir.'/classes'),
      isset($C->classes_dirs) && is_array($C->classes_dirs)
         ? $C->classes_dirs
         : array()
   );

if (!isset($C->configs_dir))
   $C->configs_dir         = $C->incs_dir.'/configs';

if (!isset($C->views_dir))
   $C->views_dir           = $C->incs_dir.'/views';

if (!isset($C->caches_dir))
   $C->caches_dir          = $C->incs_dir.'/caches';

add::load_functions('common');

add::environment_status(add::config()->environment_status);

if (add::is_development() && !is_writeable($C->caches_dir)) {
   $C->caches_dir = sys_get_temp_dir().'/add_mvc_caches';
   if (!file_exists($C->caches_dir))
      mkdir($C->caches_dir,0700);
}

if (!isset($C->assets_dir))
   $C->assets_dir          = $C->root_dir.'/assets';

if (!isset($C->images_dir))
   $C->images_dir          = $C->assets_dir.'/images';

if (!isset($C->css_dir))
   $C->css_dir             = $C->assets_dir.'/css';
$C->js_dir              = $C->assets_dir.'/js';

$C->domain              = ( $C->sub_domain ? "$C->sub_domain." : "" ).$C->super_domain;
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
 * Libraries
 */
add::load_lib('adodb');
add::load_lib('smarty');
