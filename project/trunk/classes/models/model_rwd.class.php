<?php
/**
 * model_rwd class
 * An object class representing a table, then rows are represented by instance of this class
 * Aims to be compatible with any databases
 * static variable $D should be declared, and the functions are biased to adodb object class
 *
 * @package ADD MVC\Models
 * @version 1.4.1
 * @since ADD MVC 0.0
 * @author albertdiones@gmail.com
 *
 * @todo create smarty assign() support function that will return an array with all the variables possibly needed by smarty template
 */
ABSTRACT CLASS model_rwd EXTENDS array_entity {

   /**
    * The table pk
    * default table pk is 'id'
    * @since ADD MVC 0.0
    */
   const TABLE_PK = 'id';

   /**
    * The version of model_rwd
    *
    * @since ADD MVC 0.0
    */
   const VERSION = '1.4.2';

   /**
    * $this->data
    * will contain field values of the row
    * @since ADD MVC 0.0
    */
   protected $data;


   /**
    * $this->updated_data
    * will contain $updated fields
    *
    * @todo instead of recording field values, record only the "fields to update"
    *
    * @since ADD MVC 0.0
    */
   protected $updated_data=array();


   /**
    * model_rwd::$instances the instance cache
    *
    * @since ADD MVC 0.0
    */
   protected static $instances=array();



   /**
    * The adodb object (wrapper)
    *
    * @deprecated use static::db() instead
    *
    * @since ADD MVC 0.0
    */
   static $D;

   /**
    * db function
    *
    * @return an adodb object or a compatbile one
    *
    * @since ADD MVC 0.6
    */
   public static function db() {
      if (!static::$D)
         throw new e_developer("No default db variable");
      return static::$D;
   }

   /**
    * Sets a field
    * (also updates the db row on the end of the script or after a call of $this->update_db_row())
    *
    * @param string $varname
    * @param mixed $value
    *
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.set
    * @since ADD MVC 0.0
    */
   public function __set($varname,$value) {

      $validation_result = static::validate_field($varname,$value);

      if ( $validation_result instanceof Exception ) {
         throw $validation_result;
      }

      $previous_value = $this->data[$varname];
      parent::__set($varname,$value);
      if ($previous_value != $value) {
         $this->updated_data[$varname] = $value;
      }
   }

   /**
    * validates if the property is a valid field of the table
    *
    * @param string $varname
    *
    * @see array_entity::__set() and array_entity::__get()
    * @since ADD MVC 0.0
    */
   public function valid_magic_property($varname) {
      if (!isset($this->data[$varname])) {
         if (!array_key_exists($varname,$this->data)) {
            return false;
         }
      }
      return true;
   }

   /**
    * Script to run when the object instance is destructed
    * @see http://www.php.net/manual/en/language.oop5.decon.php
    * @since ADD MVC 0.0
    */
   public function __destruct() {
      if ($this->updated_data) {
         $this->update_db_row();
      }
   }

   /**
    * The string equivalent when object instance converted to string
    * @return string the string representation of object instances under this class
    * @since ADD MVC 0.0
    */
   public function __toString() {
      return get_called_class()."#".$this->id();
   }


   /**
    * Script to run when this class is loaded
    *
    * @since ADD MVC 0.8
    */
   public static function __add_loaded() {
      $class_reflection = new ReflectionClass(get_called_class());

      if (!$class_reflection->isAbstract()) {
         $table_constant = get_called_class().'::TABLE';
         e_developer::assert(DEFINED($table_constant),"$table_constant is not declared");
      }

   }

   /**
    * model_rwd->update_db_row()
    * Updates the fields of the database table that has been updated since the object has been instantiated
    *
    * @todo experiment with the second parameter of autoExecute(), see if there's error on special keyword fields
    *
    * @return boolean true if success false if failed
    * @since ADD MVC 0.0
    */
   public function update_db_row() {
      if ($this->updated_data) {
         $result = static::db()->autoExecute(static::db()->meta_quote(static::TABLE),$this->updated_data,"UPDATE",$this->pk_where());
         $this->updated_data = array();
         return true;
      }
      else {
         return false;
      }
   }


   /**
    * model_rwd::get_instance($arg)
    * Gets an instance that represent the row from the table
    * $arg can either be the primary key or
    * the field value of the field related to the variable type
    *
    * examples:
    *    member::get_instance(1); // gets the member::TABLE row with primary key 1
    *    admin::get_instance(123);
    *    member::get_instance('albert@add.ph');
    *
    * @return object $model the model_rwd class object
    * @param mixed $arg the primary key or the field value to search
    * @since ADD MVC 0.0
    */
   public static function get_instance($arg) {

      if ( $arg === null )
         return false;


      $field = static::get_value_index_field($arg);
      $field_value = $arg; # alias for readability

      #http://code.google.com/p/add-mvc-framework/issues/detail?id=3&can=1
      if (is_scalar($field) && !is_scalar($arg)) {
         throw new e_developer(get_called_class()."::get_instance() passed with array instead of scalar PK/index value");
      }

      $cached_instance = static::get_cached_instance($field,$field_value);

      if ($cached_instance instanceof model_rwd) {
         $instance = $cached_instance;
      }
      else {
         $instances = static::instances_from_sql(
               "
               SELECT %s FROM %s
               WHERE ".static::db()->meta_quote($field)." = ".str_replace('%','%%',static::db()->quote($field_value))
            );
         if (!$instances)
            return false;
         $instance = $instances[0];
         if ($field != static::table_pk())
            static::cache_instance($instance,$field);
      }
      return $instance;
   }

   /**
    * get_value_index_field($arg)
    * returns the index field to be used on fetching
    * redeclare on classes for polymorphic get_instance()
    *
    * @see model_rwd::get_instance
    * @param mixed $arg
    * @since ADD MVC 0.0
    */
   protected static function get_value_index_field($arg) {
      if (!isset(static::$type_index_fields) || is_null($arg)) {
         return static::table_pk();
      }
      else {
         $data_type = gettype($arg);

         if (ctype_digit("$arg")) {
            $data_type = "int";
         }

         if (isset(static::$type_index_fields[$data_type])) {
            return static::$type_index_fields[$data_type];
         }
         else {
            throw new e_developer("Invalid data type($data_type [$arg] ) for index field of ".get_called_class());
         }
      }
   }


   /**
    * Creates a new instance using the array $row
    * Automatically cache it by the PK of the table
    * Take note that, the $row is discarded if the row is already cached
    *
    * @param array $row complete array of fields and values of the row
    *
    */
   static function get_row_instance($row) {
      if (!$row)
         return false;


      # Fix for issue #3
      if (!static::row_pk($row)) {
         throw new e_developer("Model ".get_called_class()." PK is not existing",array($row, static::TABLE, static::TABLE_PK));
      }

      $cached_instance = static::get_cached_instance(static::cache_main_key(),static::cache_array_main_id($row));

      if ($cached_instance) {
         $instance = $cached_instance;
      }
      else {
         $instance = new static($row);
         static::cache_instance($instance);
      }

      return $instance;
   }


   /**
    * row_pk
    *
    * Gets the pk from the $row
    *
    * @param array $row
    *
    * @return string $pk
    *
    * @since ADD MVC 0.8
    */
   public static function row_pk($row) {
      return $row[static::TABLE_PK];
   }

   /**
    * Cache the model_rwd instance, optionally using the $field
    *
    * @param model_rwd $instance with class model_rwd to cache
    * @param mixed $field to use as key to instance
    * @since ADD MVC 0.0
    */
   protected static function cache_instance(model_rwd $instance,$field=NULL) {

      $class       = get_called_class();
      $table       = static::TABLE;

      if (!$field) {
         $field    = static::cache_main_key();
         $field_value = $instance->cache_main_id();
      }
      else {
         $field_value = $instance->$field;
      }

      e_developer::assert(
            !isset(static::$instances[$class][$table][$field][$field_value]),
            "Attempt to cache $class row ($table:$field:$field_value) twice",
            NULL,
            array(
                  static::$instances,
                  get_defined_vars()
               )
         );
      static::$instances[$class][$table][$field][$field_value] = $instance;
      return static::$instances[$class][$table][$field][$field_value];
   }

   /**
    * Fetch cached instance with $field = $field_value
    *
    * @param string $field name
    * @param string $field_value to search
    * @since ADD MVC 0.0
    */
   static function get_cached_instance($field,$field_value) {
      $class       = get_called_class();
      $table       = static::TABLE;

      e_developer::assert(is_scalar($field),"Field must be scalar",$field);
      e_developer::assert(is_scalar($field_value),"Field value must be scalar",$field_value);

      if (isset(static::$instances[$class][$table][$field][$field_value])) {
         return static::$instances[$class][$table][$field][$field_value];
      }
      else
         return false;
   }

   /**
    * Gets array of instance from the sql
    * @param string $sql_format must contain 2 %s, the first is for to be replaced with "SELF::TABLE.*" and the "SELF::TABLE". For literal "%", escape them with "%%" (two percent signs) or else it will cause (very) unexpected behavior
    * @param int $page to fetch
    * @param int $per_page items per page
    * @since ADD MVC 0.0
    */
   public static function instances_from_sql($sql_format,$page=null,$per_page=null) {
      e_developer::assert($sql_format,"Missing argument on ".__FUNCTION__);
      $Q_table = static::db()->meta_quote(static::TABLE);

      $sql = sprintf(
            $sql_format,
            "$Q_table.*",
            $Q_table
         );

      e_developer::assert($sql,"Failed to create sql from '$sql_format'");

      /**
       * @since 1.2
       */
      if ($page && $per_page) {
         $offset = (($page-1)*$per_page);
         $numrows = $per_page;
      }
      else {
         $offset = $numrows = -1;
      }

      $result = static::db()->SelectLimit(
            $sql,
            $numrows,
            $offset
         );

      $rows = $result->getArray();

      $instances  = array();

      foreach ($rows as $row) {
         $instances[] = static::get_row_instance($row);
      }

      return $instances;
   }

   /**
    * Centralized select query
    * @param mixed $conditions array or string of conditions
    * @param string $order_by string to use as order by
    * @param int $page for limit clause
    * @param int $per_page number of items per page for limit clause
    * @since ADD MVC 0.0
    */
   public static function get_where_order_page($conditions,$order_by=NULL,$page=NULL,$per_page=10) {
      return static::instances_from_sql(
                  "SELECT %s FROM %s "
                  .str_replace(
                        '%',
                        '%%',
                        static::normalize_where($conditions)
                        .static::normalize_order_by($order_by)
                     ),
                  $page,
                  $per_page
               );
   }

   /**
    * get the count of rows from static::TABLE
    * @param mixed $conditions to query
    * @since ADD MVC 0.0
    */
   static function get_count($conditions=array()) {
      return (int)static::db()->getOne(
         "SELECT count(*) AS count
            FROM ".static::db()->meta_quote(static::TABLE)
            .static::normalize_where($conditions)
         );
   }

   /**
    * get_page_instances
    * get all instance from static::TABLE matching ALL $conditions , with pagination of page 1, 10 rows/page
    * @param int $page to fetch
    * @param string $order_by order by clause
    * @param array $conditions the where clause conditions
    * @param array $per_page item count
    * @since ADD MVC 0.0
    */
   static function get_page_instances($page=1,$order_by = '',$conditions=array(),$per_page=10) {
     return static::get_where_order_page($conditions,$order_by,$page,$per_page);
   }


   /**
    * Insert a new row and return the new object instance
    * @param array $row_data the array of row to add
    * @return object $instance of that row that has been inserted
    * @since ADD MVC 0.0
    */
   static function add_new($row_data) {

      if (!static::validate_row($row_data)) {
         return false;
      }

      static::db()->AutoExecute(static::TABLE,$row_data,"INSERT");

      $table_pk = static::table_pk();

      if (is_string($table_pk)) {
         $id = isset($row_data[static::table_pk()]) ? $row_data[static::table_pk()] : static::db()->Insert_ID();
      }
      # Multi PK support
      else if (is_array($table_pk)) {
         foreach ($table_pk as $one_pk) {
            $id[$one_pk] = $row_data[$one_pk];
         }
      }


      if (!$id)
         return false;

      $instance = static::get_instance($id);

      if (!$instance) {
         throw new e_developer("Failed to insert new data (".static::TABLE." #$id) ".json_encode($row_data));
      }
      return $instance;
   }

   /**
    * validates associative array for row insertion
    * @param array $row_data associative array of the table row
    * @since ADD MVC 0.0
    * @version 0.1
    */
   static function validate_row(&$row_data) {
      foreach ($row_data as $field => &$value) {
         $validation_results = static::validate_field($field,$value);
         if ($validation_results!==true) {
            if ($validation_results instanceof Exception)
               throw $validation_results;
            else
               throw new e_unknown("Failed to add new row because $field was invalid");
            return false;
         }
      }
      return true;
   }

   /**
    * Returns the ID of the row
    * @since ADD MVC 0.0
    */
   public function id() {
      return $this->data[static::table_pk()];
   }

   /**
    * Centralized creator of "WHERE" clause
    *
    * @param mixed $conditions to serialize/normalize, either array or string
    * @param string $prefix to add to the where clause
    *
    * @return string the where clause string
    * @since ADD MVC 0.0
    */
   static function normalize_where($conditions, $prefix = "WHERE") {
      $where_clause = "";
      if (is_array($conditions)) {
         $where_conditions = array();
         $args = array();
         foreach ($conditions as $field=>$value) {
            if (strpos($field,':')) {
               list($field,$operator) = explode(":",$field);
            }
            else {
               $operator = "=";
            }
            $where_conditions[] = static::db()->meta_quote($field)."$operator".static::db()->quote($value);
         }

         if ($where_conditions) {
            $where_clause = "$prefix ".implode(" AND ",$where_conditions);
         }
      }
      else if (is_string($conditions)) {
         $where_clause = "$prefix ".$conditions;
      }
      return " $where_clause ";
   }

   /**
    * Centralized creator the order by clause by string $order_by
    * @param string $order_by string
    * @return string the order by clause string
    * @since ADD MVC 0.0
    */
   static function normalize_order_by($order_by) {
      return $order_by ? " ORDER BY $order_by " : " ";
   }


   /**
    * Validates $field $value
    * Redeclare this on each class to be able to uniformize values
    * @param string $field the database field name
    * @param string $value the value to validate
    * @since ADD MVC 0.0
    */
   static function validate_field($field,&$value) {
      return true;
   }

   /**
    * model_rwd->delete()
    * @return boolean true on success false on failure
    * @todo use adodb abstraction to add the limit
    * @todo make this function support static calling or make a separate static version of this function
    * @since ADD MVC 0.0
    */
   public function delete() {
      $delete_query_successful = static::db()->query(
            "
            DELETE
            FROM ".static::TABLE."
            WHERE ".$this->pk_where().
            static::db()->hasLimit ? " LIMIT 1" : ""
         );

      if ($delete_query_successful) {
         return (bool) static::db()->Affected_Rows();
      }
      else {
         return false;
      }

   }


   /**
    * get_one_where function
    * get an instance of a row matching the $conditions
    * @param mixed $conditions the conditions that should match the row
    * @param string $order_by the order by value
    * @deprecated
    * @todo create new function model_rwd::get_one($conditions,$order_by)
    */
   static function get_one_where($conditions=array(),$order_by="") {
      $rows = static::get_where_order_page($conditions,$order_by,1,1);
      return $rows ? array_shift($rows) : false;
   }


/**
 * -----------------
 * Extension support functions
 * Classes extending this function can extend these to support other structures
 * -----------------
 */
   /**
    * TABLE pk function
    * Returns the table's primary key
    * @see model_multi_pk::table_pk()
    */
   static function table_pk() {
      return static::TABLE_PK;
   }

   /**
    * Returns the scalar index to use caching instances of this class using the primary key
    * @see model_rwd::get_row_instance(), model_rwd::cache_instance(), see model_multi_pk::cache_main_key()
    * @since ADD MVC 0.0
    */
   static function cache_main_key() {
      return static::TABLE_PK;
   }

   /**
    * Returns the cache_main_key() value
    * the scalar index to use when caching $this instance using the primary key
    * @return string primary key value
    * @since ADD MVC 0.0
    */
   public function cache_main_id() {
      return static::cache_array_main_id($this->data);
   }

   /**
    * Returns the cache_main_key() value
    * the scalar index to use when caching an instance using the primary key
    * if the instance is __construct()`ed using $row argument
    * on normal
    *
    * @param array $row
    *
    * @return string primary key value
    * @since ADD MVC 0.0
    */
   static function cache_array_main_id($row) {
      return $row[static::cache_main_key()];
   }

   /**
    * returns the where clause (without "WHERE") to fetch $this instance
    * @return string where clause
    * @since ADD MVC 0.0
    */
   public function pk_where() {
      $where = static::normalize_where(
            array(
                  $this->table_pk() => $this->id()
               )
            ,
            ""
         );
      return "($where)";
   }
/**
 * -----------------
 * Debugging functions
 * -----------------
 */

  /**
   * var_dump()s the instance cache
   * @since ADD MVC 0.0
   */
   public static function debug_instances() {
      $class = get_called_class();
      if ($class)
         debug::var_dump(self::$instances[$class]);
      else
         debug::var_dump(self::$instances);
   }

   /**
    * returns $this->data array for debugging
    * @since ADD MVC 0.0
    */
   public function data_array() {
      return $this->data;
   }

/**
 * -----------------
 * Non-instance related (fields and arrays)
 * -----------------
 */
   /**
    * get_column($field)
    *
    * @param array $field the field to fetch
    * @param $where the conditions
    *
    * @since 1.1
    */
   public static function get_column_array($field,$where=array()) {

      $column = static::db()->getAssoc(
            "
            SELECT ".static::table_pk().",".static::db()->meta_quote($field)
            ."FROM   ".static::TABLE
            .static::normalize_where($where)
         );

      return $column;
   }


   /**
    * column_names
    *
    * @since ADD MVC 0.8.0
    */
   public static function meta_columns() {
      return static::db()->MetaColumns(static::TABLE);
   }

/**
 * -----------------
 * Complex special functions
 * -----------------
 */
   /**
    * smart_field_query($query,$field,$threshold=0.5,$allowed_difference=0.25)
    *
    * Queries for $field that looks like $query
    * This function has the ability to query for a field value even though it is misspelled
    * For very big tables, this might cause CPU spikes
    *
    * @param string $query the keyword to search for
    * @param string $field the field to search at
    * @param float $threshold of the match, i.e. the required similarity between the $query and the $field value
    * @param float $allowed_difference from the highest match to allow (adjust to increase or decrease number of results)
    *
    * @since ADD MVC 0.0
    *
    * @todo create another function that supports pagination
    */
   static function smart_field_query($query,$field,$threshold=0.5,$allowed_difference=0.25) {

      $instances = array();

      # backward support
      $allowed_difference *= 100;
      $threshold *= 100;

      if (!$query || !$field) {
         return array();
      }

      $Q_like_query = static::db()->quote("%$query%");
      $Q_field      = static::db()->meta_quote($field);
      $query_length = strlen($query);

      $instances = static::get_where_order_page($Q_field." LIKE $Q_like_query");

      if (!$instances) {
         $Q_like_query0_start = static::db()->quote("%".$query{0}."%");
         $Q_like_query0       = static::db()->quote("% ".$query{0}."%");

         $probable_rows = static::db()->getArray(
               "
               SELECT ".static::table_pk().", $Q_field
               FROM ".static::TABLE."
               WHERE $Q_field LIKE $Q_like_query0_start OR ".$Q_field." LIKE $Q_like_query0
               "
            );

         $instance_scores = array();
         foreach ($probable_rows as $index => $probable_row) {
            similar_text($probable_row[$field],$query, $similarity_percent);
            $instance_scores[$index] = $similarity_percent;
            unset($similarity_percent, $probable_row);
         }

         if ($instance_scores) {
            $highest_score = max($instance_scores);

            if ( $highest_score >= $threshold ) {
               arsort($instance_scores);

               foreach ($instance_scores as $index=>$instance_score) {
                  if ( ($highest_score-$instance_score) < $allowed_difference ) {
                     if (count($probable_rows[$index]) == 2) {
                        $pk = $probable_rows[$index][static::table_pk()];
                     }
                     else {
                        $multi_pk = $probable_rows[$index];
                        array_pop($multi_pk);
                        $pk = $multi_pk;
                     }
                     $instances[] = static::get_instance($pk);
                  }
               }

            }

         }

      }
      return $instances;
   }

   /**
    * Smart field query one
    * Returns the first instance (closest match) on smart_field_query() results
    * @see model_rwd::smart_field_query()
    * @return object $instance of the closest matched row
    * @since ADD MVC 0.0
    */
   static function smart_field_query_one(/* same as smart_field_query() arguments*/) {
      $instances = call_user_func_array(array(get_called_class(),'smart_field_query'),func_get_args());
      return array_shift($instances);
   }



/**
 * -----------------
 * DEPRECATED functions
 * -----------------
 */
   /**
    * db_get_instance_cache()
    * This function is ussually called inside get_instance()
    * This should be called on descendant classes if you want other $field than the static::table_pk()
    *
    * @param string $table
    * @param string $field
    * @param string $field_value
    *
    * @deprecated use get_row_instance()
    */
   protected static function db_get_instance_cache($table,$field,$field_value) {
      $class = get_called_class();
      if (!isset(static::$instances[$class][$table][$field][$field_value])) {

         $row = static::db_row_array($table,$field,$field_value);

         if ($row)
            static::$instances[$class][$table][$field][$field_value] = static::get_row_instance($row);
         else
            return false;
      }

      return static::$instances[$class][$table][$field][$field_value];
   }

   /**
    * Deprecated function
    * @param string $table
    * @param string $field
    * @param string $field_value
    * @deprecated use get_row_instance()
    */
   static function db_row_array($table,$field,$field_value) {
      e_developer::assert(is_object(static::db()),get_called_class().' $D is not an object');
      $row = static::db()->getRow(
            "
            SELECT * FROM ".static::db()->meta_quote($table)."
            WHERE ".static::db()->meta_quote($field)." = ".static::db()->quote($field_value)
         );
      return $row;
   }

   /**
    * get all object instances from the static::TABLE
    * optional: get all object instance of rows matching ALL $conditions
    *
    * @param mixed $conditions
    * @param string $order_by
    *
    * @deprecated use model_rwd::get_where_order_page
    */
   static function get_all($conditions=array(),$order_by="") {
      return static::get_where_order_page($conditions,$order_by);
   }


   /**
    * get all object instance of rows matching ALL $conditions
    *
    * @param mixed $conditions
    * @param string $order_by
    *
    * @deprecated static::get_where_order_page
    */
   static function get_all_where($conditions=array(),$order_by="") {
      return static::get_where_order_page($conditions,$order_by);
   }

   /**
    * get_page_where
    * get all instance from static::TABLE matching ALL $conditions , with pagination of page 1, 10 rows/page
    *
    * @param int $page
    * @param mixed $conditions
    * @param int $per_page
    *
    * @deprecated see static::get_page_instances()
    */
   static function get_page_where($page=1,$conditions=array(),$per_page=10) {
      return static::get_where_order_page($conditions,null,$page,$per_page);
   }


   /**
    * call get instance from the sql
    * @param string $sql
    * @deprecated use model_rwd::instances_from_sql()
    */
   static function get_all_id_query($sql) {
      $rows = static::db()->getAll($sql);
      $instances = array();

      foreach ($rows as $row) {
         $instance = static::get_instance(array_shift($row));
         if ($instance)
            $instances[] = $instance;
         else
            trigger_error("Failed to get instance from: ".json_encode($row));
      }

      return $instances;
   }

}
