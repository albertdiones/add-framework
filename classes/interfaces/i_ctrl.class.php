<?php

/**
 * i_ctrl controller interface
 *
 * @since ADD MVC 0.6
 */
INTERFACE i_ctrl {

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
}