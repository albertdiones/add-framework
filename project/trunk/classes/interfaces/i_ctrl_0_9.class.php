<?php
/**
 * i_ctrl controller interface
 *
 * future i_ctrl
 * @package ADD MVC\Extras
 * @since ADD MVC 0.6
 */
INTERFACE i_ctrl_0_9 {

   /**
    * execute() the execute function
    *
    * @since ADD MVC 0.6
    */
   public function execute();

   /**
    * print_response() - print the response according to data
    *
    * @param array $data the assigned data through assign()
    *
    * @since ADD MVC 0.6
    */
   public function print_response($data);


   /**
    * process_data() - process the data
    *
    * @param array $gpc the common gpc
    *
    * @since ADD MVC 0.6
    *
    * This is commented because of backwards compatibility reasons
    *
    */
   public function process_data($gpc);

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