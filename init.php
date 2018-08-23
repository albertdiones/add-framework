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
$C = &$GLOBALS[add::CONFIG_VARNAME];

if ( php_sapi_name() == "cli") {
   add::content_type('text/plain');
}

# Set the handlers
spl_autoload_register('add::load_class');
set_exception_handler('add::handle_exception');
set_error_handler('add::handle_error');
register_shutdown_function('add::handle_shutdown');

require 'vendor/autoload.php';

# Set the includes dir
if (!isset($C->incs_dir)) {
   $C->incs_dir            = $C->root_dir.'/includes';
}

if (!isset($C->app_name) && !empty($C->super_domain)) {
   $C->app_name = $C->super_domain;
}
if (!isset($C->app_namespace) && !empty($C->app_name)) {
   $C->app_namespace = $C->app_name;
}


# Merge config declared class directories
$add_classes_dir = realpath($C->add_dir).'/classes';
$app_classes_dir = realpath($C->incs_dir).'/classes';


# Default namespace
$C->classes_dir_default_namespace[$add_classes_dir] = 'addph\\framework';
$C->classes_dir_default_namespace[$app_classes_dir] = $C->app_namespace;

foreach ($C->classes_dir_default_namespace as &$namespace) {
   $namespace = trim($namespace,'\\');
}

$C->classes_dirs = array_merge(
      array( $app_classes_dir ),
      isset($C->classes_dirs)
         ? (is_array($C->classes_dirs) ? $C->classes_dirs : (array) $C->classes_dirs)
         : array(),
      array($add_classes_dir)
   );
# Note: you can add $C->classes_dirs_filepath_callback[$class_dir] to your config to make custom class file finding



# Set these rarely used directory variables
if (!isset($C->configs_dir)) {
   $C->configs_dir         = $C->incs_dir.'/configs';
}

if (!isset($C->views_dir)) {
   $C->views_dir           = $C->incs_dir.'/views';
}
if (!isset($C->caches_dir)) {
   $C->caches_dir          = $C->incs_dir.'/caches';
}


# Load the common functions
add::load_functions('common');

# Just initialize the variables according to the environment status
add::environment_status(true);

/**
 * Set the exception emails
 *
 * @see http://code.google.com/p/add-mvc-framework/issues/detail?id=38
 *
 *
 */
if (isset($C->developer_emails)) {
   if (is_string($C->developer_emails)) {
      e_add::$email_addresses = $C->developer_emails;
   }
   else if ( is_object($C->developer_emails) || is_array($C->developer_emails) ) {
      e_add::$email_addresses = implode(", ", (array)$C->developer_emails );
   }
}

if (add::is_development() && !is_writeable($C->caches_dir)) {
   $C->caches_dir = sys_get_temp_dir().'/add_mvc_caches_'.sha1($C->root_dir);
   if (!file_exists($C->caches_dir)) {
      umask(0);
      mkdir($C->caches_dir);
   }
   else if (!is_dir($C->caches_dir)) {
      throw new e_system("Cache directory is not a directory", $C->caches_dir);
   }
}

if (!is_writeable($C->caches_dir)) {

   if (!file_exists($C->caches_dir)) {
      throw new e_system("Cache directory is not existing ",$C->caches_dir);
   }
   if (!is_dir($C->caches_dir)) {
      throw new e_system("Cache directory is not a directory (environment status: ".add::environment_status().")",$C->caches_dir);
   }

   $cache_files = new DirectoryIterator($C->caches_dir);

   foreach ($cache_files as $cache_file) {

      if ($cache_file->isDot()) {
         continue;
      }

      if (!is_writable($cache_file->getPathname())) {
         throw new e_system("Cache directory is not writeable and one (or more) of it's files are not writeable",array($C->caches_dir,$cache_file->getPathname()));
      }

   }

   trigger_error("Cache directory is not writeable",E_USER_WARNING);

   unset($cache_file,$cache_files);

}

if (!isset($C->assets_dir))
   $C->assets_dir = $C->root_dir.'/assets';

if (!isset($C->images_dir))
   $C->images_dir = $C->assets_dir.'/images';

if (!isset($C->css_dir))
   $C->css_dir    = $C->assets_dir.'/css';
$C->js_dir        = $C->assets_dir.'/js';

$C->domain        = ( $C->sub_domain ? "$C->sub_domain." : "" ).$C->super_domain;
$C->base_url      = "http://$C->domain$C->path";

set_include_path($C->incs_dir);


/**
 * assets
 * @author albertdiones@gmail.com
 */
$C->assets_path = $C->path.'assets/';
$C->css_path    = $C->assets_path.'css/';
$C->js_path     = $C->assets_path.'js/';
$C->images_path = $C->assets_path.'images/';
$C->assets_libs_path   = $C->assets_path.'libs/';


$C->smarty_class = 'add_smarty';

