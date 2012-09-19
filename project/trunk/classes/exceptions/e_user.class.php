<?php
/**
 * e_user class
 * Custom Exception Class for user errors
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_user EXTENDS e_add {

   /**
    * Do not auto generate $message on e_user
    *
    * @param mixed $condition
    * @param string $message the error message
    * @param mixed $data extra data of the error
    * @param int $error_number the error code
    *
    * @since ADD MVC 0.8
    */
   public static function assert($condition, $message = null, $data=NULL, $error_number=NULL)  {
      if (!$message) {
         $message ="Failed to validate ($condition)";
      }
      parent::assert($condition, $message, $data, $error_number);
   }
}