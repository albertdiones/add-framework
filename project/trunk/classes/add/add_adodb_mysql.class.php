<?php
/**
 * adodb mysql wrapper abstract
 *
 * Declare Connect() on your concrete class
 *
 * Being considered to be deprecated
 * @package ADD MVC ADODB wrappers
 * @version 0.0
 */
ABSTRACT CLASS add_adodb_mysql EXTENDS add_adodb {
   const VARNAME = 'MYSQL_D';
   const DB_TYPE = 'mysql';

   /**
    * Quote the fields and table
    *
    * @param mixed $arg string or array, if array will be treated as multiple fields
    *
    */
   public function meta_quote($arg) {
      if (is_array($arg)) {
         $Q_args = array();
         foreach ($arg as $item) {
            $Q_args[] = $this -> meta_quote($item);
         }
         return "(".implode(" , ",$Q_args).")";
      }
      else {
         return "`".str_replace('.','`.`',$arg)."`";
      }
   }

   /**
    * Handle specific mysql errors
    *
    */
   public function handle_error() {
      $last_operation = $this->add_last_operation;
      $data = array(
            'function_name'   => is_array($last_operation[0]) ? $last_operation[0][1] : $last_operation[0],
            'function_name_complete'   => is_array($last_operation[0]) ? (is_string($last_operation[0][0]) ? $last_operation[0][0].':' : get_class($last_operation[0][0]).'->').$last_operation[0][1] : $last_operation[0],
            'args'            => $last_operation[1]
         );

      switch (strtolower($data['function_name'])) {
         case 'autoexecute':
            $data['table'] = $data['args'][0];
            $data['fields'] = $data['args'][1];
            $data['operation'] = $data['args'][2];
         break;
      }

      $data['debug'] = $last_operation;
      if (class_exists('e_database'))
         throw new e_database($this->adodb->ErrorMsg(),$data,$this->adodb->ErrorNo());
      else
         throw new Exception($this->adodb->ErrorMsg(),$this->adodb->ErrorNo());
   }

   /**
    * Escape and quote a value
    *
    * @param mixed $arg
    *
    */
   public function quote($arg) {
      if (is_array($arg)) {
         $Q_args = array();
         foreach ($arg as $item) {
            $Q_args[] = $this -> quote($item);
         }
         return "(".implode(" , ",$Q_args).")";
      }
      else {
         return $this -> __call(__FUNCTION__,func_get_args());
      }
   }
}