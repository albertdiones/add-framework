<?php
/**
 * Custom Exception Class for end-user errors
 *
 * Maybe this should be abstract?
 *
 * Throwing this kind of error inside the controller will cause the $error_message template variable to be filled
 * unless it is e_user_malicious (e_hack or e_spam)
 *
 * @see ctrl_tpl_page->execute()
 *
 * <code>
 * if (empty($username)) {
 *    throw new e_user("Please input your username"); # this is suppose to be e_user_input
 * }
 * </code>
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
         $message ="User error occurred";
      }
      parent::assert($condition, $message, $data, $error_number);
   }
}