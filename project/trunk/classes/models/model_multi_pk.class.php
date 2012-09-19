<?php
/**
 * Abstract class for tables with more than one PKs
 * @package ADD MVC\Models
 * @author albertdiones@gmail.com
 * @version 0.1
 */
ABSTRACT CLASS model_multi_pk EXTENDS model_rwd {
   const TABLE_PK=NULL;
   #protected static $TABLE_PKS;

   /**
    * Cache of primary key values array
    *
    * @since ADD MVC 0.0
    */
   protected $id_array;

   /**
    * Gets an instance that represent the row from the table
    * @param array $pks an array of pks produced by model_multi_pk::serialize_pk_array
    * @version 0.1
    * @since version 0.1
    */
   static public function get_instance($pks) {

      $args = func_get_args();

      if (count($pks)===1)
         e_developer::assert(is_array($pks),"Multiple PK must be used on ".get_called_class()."::".__FUNCTION__." ".json_encode($pks));

      return call_user_func_array(array('parent','get_instance'),$args);
   }

   /**
    * Cache the model_rwd instance, optionally using the $field
    * @param model_rwd $instance with class model_rwd to cache
    * @param mixed $field to use as key to instance
    * @since ADD MVC 0.4
    */
   protected static function cache_instance(model_rwd $instance,$field=NULL) {

      $args = func_get_args();

      if (is_array($field)) {
         $args[1] = implode(",",$field);
      }


      return call_user_func_array(array('parent',__FUNCTION__),$args);

   }

   /**
    * Fetch cached instance with $field = $field_value
    *
    * @param string field
    * @param mixed $field_value
    *
    * @author albertdiones@gmail.com
    */
   static function get_cached_instance($field,$field_value) {
      $args = func_get_args();

      if (is_array($field)) {
         $args[0] = implode(",",$field);
      }

      if (is_array($field_value)) {
         $args[1] = model_multi_pk::serialize_pk_array($field_value);
      }

      return call_user_func_array(array('parent',__FUNCTION__),$args);
   }

   /**
    * Returns the table primary key fields array
    *
    * @since ADD MVC 0.0
    */
   static public function table_pk() {
      if (!isset(static::$TABLE_PKS)){
         throw new e_developer(get_called_class()."::\$TABLE_PKS is not set");
      }
      return static::$TABLE_PKS;
   }

   /**
    * Returns the row's primary key values array
    *
    * @since ADD MVC 0.0
    */
   public function id() {
      if (!isset($this->id_array)) {
         $this->id_array = static::row_pk($this->data);
      }
      return $this->id_array;
   }

   /**
    * Returns the table's cache key
    *
    * @since ADD MVC 0.0
    */
   static function cache_main_key() {
      return implode(",",static::table_pk());
   }

   /**
    * Returns the row's cache key
    *
    * @since ADD MVC 0.0
    */
   public function cache_main_id() {
      return self::cache_array_main_id($this->data);
   }

   /**
    * Returns the array $row 's cache key
    *
    * @param array $row the array of table row
    *
    * @since ADD MVC 0.0
    */
   static function cache_array_main_id($row) {
      return static::serialize_pk_array(static::row_pk($row));
   }

   /**
    * Returns the where clause to be used when searching for this row
    *
    * @since ADD MVC 0.0
    */
   public function pk_where() {
      $where_array = array();

      foreach ($this->table_pk() as $pk) {
         $where_array[$pk] = $this->{$pk};
      }

      $where = static::normalize_where(
            $where_array
            ,
            ""
         );

      return "($where)";
   }

   /**
    * Returns the serialized version of a primary key array
    *
    * @param array $pk_array the primary key array
    *
    * @since ADD MVC 0.0
    */
   protected static function serialize_pk_array($pk_array) {
      return implode(",",$pk_array);
   }

   /**
    * The string equivalent when object instance converted to string
    * @return string the string representation of object instances under this class
    * @since ADD MVC 0.4.3
    */
   public function __toString() {
      return get_called_class()."#".$this->cache_main_id();
   }


   /**
    * row_pk
    *
    * Gets the pk from the $row
    *
    * @param array $row
    *
    * @return array $pk_array
    *
    * @since ADD MVC 0.8
    */
   public static function row_pk($row) {
      $pk_array = array();
      foreach (static::table_pk() as $table_pk) {
         $pk_array[$table_pk] = $row[$table_pk];
      }
      return $pk_array;
   }

}