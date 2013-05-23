<?php
/**
 * i_ctrl controller interface file
 *
 * @package ADD MVC\Extras\Interfaces
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
    * @param array $data assigned variables
    *
    *
    * @see $this->assign()
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
    * @todo overwrite this with i_ctrl_0_9
    *
    */
   /* public function process_data($gpc);*/

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