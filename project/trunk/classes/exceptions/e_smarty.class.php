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
   public function handle_exception() {
      $error_message = preg_replace('/(https?:|(<)add_mvc_path(>)|www|(.):)(.+)(tpl|php)(.+)([0-9]+)/', add::config()->add_dir, $this->data->message);
      $this->data->message = $error_message;
   }
}