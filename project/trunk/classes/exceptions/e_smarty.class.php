<?php
/**
 * e_smarty class
 * Custom Exception Class for smarty errors
 */
CLASS e_smarty EXTENDS e_system {

   /**
    * Handle exception
    *
    */
   public function handle_exception() {
      $this->message = str_replace(add::config()->root_dir, '< mvc-path >', $this->data->message);
      parent::handle_exception();
   }
}