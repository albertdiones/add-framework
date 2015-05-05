<?php

/**
 * Row instances page
 * A collection page for rows of a table
 * with links to the item page of the row
 *
 * @see http://schema.org/CollectionPage
 * @since auto_invoice 0.2
 */
ABSTRACT CLASS ctrl_abstract_instances EXTENDS ctrl_tpl_page {

   /**
    * instances per page
    *
    * @var int
    *
    */
   public $instances_per_page = 10;


   /**
    * The Common GPC
    *
    */
   protected $common_gpc = array(
         '_REQUEST' => array('page')
      );


   /**
    * Standard prefix for classes inheriting this class
    *
    */
   static $standard_prefix = '/^[^\W_]+\_([^\W_]+\_\_)*/';

   /**
    * Model name cache
    *
    */
   protected $model;

   public function execute() {
      $model_class = $this->model();
      $this->common_gpc['_REQUEST'][] = $model_class::TABLE_PK;
      parent::execute();
   }

   /**
    * Pre mode process - get the model class
    *
    * @param array $common_gpc
    *
    */
   public function pre_mode_process($common_gpc) {
      $this->assign('model_class',static::model());
         $this->assign('columns',$this->get_columns());
      $model_class = &$this->data['model_class'];
   }

   /**
    * Gets the instances to deal with
    *
    * @param array $gpc
    *
    */
   public function process_mode_default($gpc) {
      # Moved to post_mode_process()
   }

   /**
    * Gets the instances to deal with
    */
   public function post_mode_process($common_gpc) {
      $this->assign('instances',$this->get_instances($common_gpc));
      $this->assign('rows',$this->get_rows($common_gpc));
      $this->assign('pagination',$this->get_pagination($common_gpc));
   }


   /**
    * Gets the row instances to deal with
    *
    * @param array $data
    *
    */
   public function get_instances($data) {
      $model = static::model();
      return $model::get_where_order_page($this->where(), $model::table_pk()." DESC",$data['page'] ? $data['page'] : 1, $this->instances_per_page);
   }

   /**
    * Where
    */
   public function where() {
      return null;
   }

   /**
    * Gets the row instances to deal with
    *
    * @param array $data
    *
    */
   public function get_rows($data = array()) {
      $rows = array();
      foreach ($this->data['instances'] as $instance) {
         $row = $this->row_from_instance($instance);
         if (method_exists($instance,'href')) {
            $row['href'] = $instance->href();
         }
         $rows[] = $row;
      }
      $walk_callback = array($this,'walk_row');
      if (is_callable($walk_callback) && method_exists($walk_callback[0],$walk_callback[1])) {
         array_walk($rows,$walk_callback);
      }
      #debug::var_dump($rows);
      return $rows;
   }

   /**
    * Gets the rows from the table
    *
    * @since homeland voice 0.0
    */
   public function row_from_instance(model_rwd $instance) {

      $row = array();
      foreach ($instance->data_array() as $column => $value) {

         if (!isset($this->data['columns'][$column])) {
            continue;
         }

         $filter_column_method = "table_format_column_$column";

         if (method_exists($this,$filter_column_method)) {
            $value = $this->$filter_column_method($value);
         }

         $row[$column] = $value;
      }

      return $row;
   }



   /**
    * Gets the model name
    *
    * @return string model class name
    *
    */
   public function model() {

      $count = 0;
      $called_class = get_called_class();

      if (!isset($this->model)) {
         $this->model = preg_replace(static::$standard_prefix, '', $called_class, 1, $count);

         if (!$count) {
            throw new e_developer("Can't auto find model for ".$called_class);
         }

         if (!class_exists($this->model)) {
            throw new e_developer("Can't find model $this->model for ".$called_class);
         }

      }

      return $this->model;
   }

   /**
    * Gets the colulmns to put into the table
    *
    * @param array $data
    *
    */
   public function get_columns( $data = array() ) {
      $class = $this->data['model_class'];
      $column_objects = $class::meta_columns();

      $columns = array();

      foreach ($column_objects as $column_object) {
         $columns[$column_object->name] = (array) $column_object;

         # camel case
         $column_label = ucwords(preg_replace('/([A-Z]+[a-z0-9]++|[A-Z]{2,}(?=[A-Z]))(?!$)/','$0 ',$columns[$column_object->name]['name']));

         # underscores
         $column_label = ucwords(preg_replace('/[\W_]+/',' ',$column_label));

         $columns[$column_object->name]['label'] = $column_label ? $column_label : $columns[$column_object->name]['name'];
         $columns[$column_object->name]['editable'] = !$columns[$column_object->name]['auto_increment'];
      }

      return $columns;
   }


   /**
    * Gets the pagination of the collection page
    *
    * @param array $data
    *
    */
   public function get_pagination($data) {
      $model = static::model();
      return new pagination($model::get_count($this->where()),$data['page'] > 1 ? (int) $data['page'] : 1,$this->instances_per_page);
   }

   /**
    * view_file_path() default
    *
    */
   public function view_filepath() {

      $real_tpl = parent::view_filepath();
      if ($this->view()->templateExists($real_tpl)) {
         return $real_tpl;
      }
      else {
         return "layouts/instances.tpl";
      }

   }

}