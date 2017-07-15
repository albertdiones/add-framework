<?php

/**
 * Created by PhpStorm.
 * @author albert
 * Date: 1/18/16
 * Time: 4:41 AM
 */
CLASS fluid_model {

   protected $class;
   protected $TABLE;
   protected $TABLE_PK;

   public function __construct($table, $pk, $db) {
      $this->table = $table;
      $this->table_pk = $pk;
      $this->db = $db;
      $this->class = preg_replace('/\W+/','_',__CLASS__.'_'.$table.'_'.$pk);

      eval('
      CLASS '.$this->class.' EXTENDS model_rwd {
         const TABLE = "'.addslashes($this->table).'";
         const TABLE_PK = "'.addslashes($this->table_pk).'";
         static $adodb;
         public static function db() {
            return static::$adodb;
         }
      }');
      $class = $this->class;
      $class::$adodb = $db;

   }

   public function __toString() {
      return $this->class;
   }

}