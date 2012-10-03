<?php

/**
 * i_ctrl controller interface
 *
 * @since ADD MVC 0.6
 */
INTERFACE i_ctrl_006 {

   /**
    * execute() the execute function
    *
    * @since ADD MVC 0.6
    */
   public function execute();

   /**
    * print_response() - print the response according to data
    *
    * @since ADD MVC 0.6
    */
   public function print_response($data);


   /**
    * process_data() - process the data
    *
    * @since ADD MVC 0.6
    */
   public function process_data();

   /**
    * assign data
    *
    * @since ADD MVC 0.6
    */
   public function assign(/* Polymorphic */);


   /**
    * sets the content_type or get the current one
    *
    * @param string $new_content_type
    *
    * @since ADD MVC 0.8
    */
   public function content_type($new_content_type = null);
}