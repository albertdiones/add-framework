<?php
/**
 * Hash functions
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Functions
 * @since ADD MVC 0.0
 * @version 0.0
 */

/**
 * Appends all sha1_file of all files in a directory
 *
 * @param string $dir The directory to hash
 * @param string $hash The hash to prepend
 * @param boolean $single_sha1 flag to return just a single hash
 * @since ADD MVC 0.0
 */
function sha1_dir($dir,$hash="",$single_sha1=true) {

   if (empty($dir))
      throw new Exception("Directory path is empty");

   if (!is_dir($dir))
      throw new Exception("$dir is not a directory");

   $files         = new DirectoryIterator($dir);
   $sorted_files  = array();

   foreach ($files as $file) {
      if ($file->isDot())
         continue;
      $sorted_files[] = $file->getPathName();
   }

   unset($file);
   sort($sorted_files);

   foreach ($sorted_files as $file) {
      if (is_dir($file))
         $hash .= sha1_dir($file,$hash,$single_sha1);
      else
         $hash .= sha1_file($file);
   }
   if ($single_sha1)
      return sha1($hash);
   else
      return $hash;
}