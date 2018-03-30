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
DEFINE('ADD_MIN_PHP_VERSION','5.4.0');
if (version_compare(phpversion(),ADD_MIN_PHP_VERSION) === -1) {
   die("ADD MVC Error: PHP version must be at least ".ADD_MIN_PHP_VERSION." or higher!");
}


if (!isset($add_framework_config)) {
   if (isset($C)) {
      $add_framework_config = $C;
   }
   else {
      $add_framework_config = new STDClass();
   }
}

# Sets the add_dir if not set. And it may be smart not to let the user(developer) set this on the first place
if (empty($add_framework_config->add_dir)) {
   $add_framework_config->add_dir = realpath(dirname(__FILE__));
}

require $add_framework_config->add_dir.'/classes/add.class.php';

$GLOBALS[add::CONFIG_VARNAME] = add::config($add_framework_config);

if ( php_sapi_name() == "cli") {
   add::content_type('text/plain');
}

# Set the handlers
spl_autoload_register('add::load_class');
set_exception_handler('add::handle_exception');
set_error_handler('add::handle_error');
register_shutdown_function('add::handle_shutdown');

# Set the includes dir
if (!isset($add_framework_config->incs_dir)) {
   $add_framework_config->incs_dir            = $add_framework_config->root_dir.'/includes';
}


# Merge config declared class directories
$add_framework_config->classes_dirs        = array_merge(
      array( $add_framework_config->incs_dir.'/classes'),
      isset($add_framework_config->classes_dirs)
         ? (is_array($add_framework_config->classes_dirs) ? $add_framework_config->classes_dirs : (array) $add_framework_config->classes_dirs)
         : array(),
      array($add_framework_config->add_dir.'/classes')
   );
# Note: you can add $C->classes_dirs_filepath_callback[$class_dir] to your config to make custom class file finding

# Set these rarely used directory variables
if (!isset($add_framework_config->configs_dir)) {
   $add_framework_config->configs_dir         = $add_framework_config->incs_dir.'/configs';
}

if (!isset($add_framework_config->views_dir)) {
   $add_framework_config->views_dir           = $add_framework_config->incs_dir.'/views';
}
if (!isset($add_framework_config->caches_dir)) {
   $add_framework_config->caches_dir          = $add_framework_config->incs_dir.'/caches';
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
if (isset($add_framework_config->developer_emails)) {
   if (is_string($add_framework_config->developer_emails)) {
      e_add::$email_addresses = $add_framework_config->developer_emails;
   }
   else if ( is_object($add_framework_config->developer_emails) || is_array($add_framework_config->developer_emails) ) {
      e_add::$email_addresses = implode(", ", (array)$add_framework_config->developer_emails );
   }
}

if (add::is_development() && !is_writeable($add_framework_config->caches_dir)) {
   $add_framework_config->caches_dir = sys_get_temp_dir().'/add_mvc_caches_'.sha1($add_framework_config->root_dir);
   if (!file_exists($add_framework_config->caches_dir)) {
      umask(0);
      mkdir($add_framework_config->caches_dir);
   }
   else if (!is_dir($add_framework_config->caches_dir)) {
      throw new e_system("Cache directory is not a directory", $add_framework_config->caches_dir);
   }
}

if (!is_writeable($add_framework_config->caches_dir)) {

   if (!file_exists($add_framework_config->caches_dir)) {
      throw new e_system("Cache directory is not existing ",$add_framework_config->caches_dir);
   }
   if (!is_dir($add_framework_config->caches_dir)) {
      throw new e_system("Cache directory is not a directory (environment status: ".add::environment_status().")",$add_framework_config->caches_dir);
   }

   $cache_files = new DirectoryIterator($add_framework_config->caches_dir);

   foreach ($cache_files as $cache_file) {

      if ($cache_file->isDot()) {
         continue;
      }

      if (!is_writable($cache_file->getPathname())) {
         throw new e_system("Cache directory is not writeable and one (or more) of it's files are not writeable",array($add_framework_config->caches_dir,$cache_file->getPathname()));
      }

   }

   trigger_error("Cache directory is not writeable",E_USER_WARNING);

   unset($cache_file,$cache_files);

}

if (!isset($add_framework_config->assets_dir))
   $add_framework_config->assets_dir = $add_framework_config->root_dir.'/assets';

if (!isset($add_framework_config->images_dir))
   $add_framework_config->images_dir = $add_framework_config->assets_dir.'/images';

if (!isset($add_framework_config->css_dir))
   $add_framework_config->css_dir    = $add_framework_config->assets_dir.'/css';
$add_framework_config->js_dir        = $add_framework_config->assets_dir.'/js';

$add_framework_config->domain        = ( $add_framework_config->sub_domain ? "$add_framework_config->sub_domain." : "" ).$add_framework_config->super_domain;
$add_framework_config->base_url      = "http://$add_framework_config->domain$add_framework_config->path";

set_include_path($add_framework_config->incs_dir);


/**
 * assets
 * @author albertdiones@gmail.com
 */
$add_framework_config->assets_path = $add_framework_config->path.'assets/';
$add_framework_config->css_path    = $add_framework_config->assets_path.'css/';
$add_framework_config->js_path     = $add_framework_config->assets_path.'js/';
$add_framework_config->images_path = $add_framework_config->assets_path.'images/';
$add_framework_config->assets_libs_path   = $add_framework_config->assets_path.'libs/';


/**
 * Libraries
 */
add::load_lib('adodb');
add::load_lib('smarty');
