<?php
/**
 * e_smarty class
 * Custom Exception Class for smarty errors
 */
CLASS e_smarty EXTENDS e_system {

   /**
    * Handle exception
    *
    * @since
    */
   public function getMessage() {
      $error_message = parent::getMessage();
      $error_message = str_replace(add::config()->root_dir, 'add-mvc-path', $error_message);
      return $error_message;
   }
}