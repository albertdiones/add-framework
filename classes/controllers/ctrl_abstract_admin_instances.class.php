<?php

/**
 * abstract admin pages
 *
 */
CLASS ctrl_abstract_admin_instances EXTENDS ctrl_abstract_instances {
   /**
    *
    * @deprecated use standard_prefix()
    *
    */
   static $standard_prefix = '/^[^\W_]+\_[^\W_]+\_admin\_\_?/';

   /**
    * Model name cache
    *
    */
   protected $model;


   /**
    *
    * Defaults
    *
    *
    * @var array
    */
   public $data = array(
      'add_enabled' => true,
      'edit_enabled' => true,
      'export_enabled' => true,
   );


   /**
    * gpc for mode edit
    *
    * @since homeland voice 0.0
    */
   public $mode_gpc_edit = array();

   /**
    * gpc for mode add
    *
    * @since homeland voice 0.0
    */
   public $mode_gpc_add = array();

   /**
    * gpc for mode delete
    *
    * @since homeland voice 0.0
    */
   public $mode_gpc_delete = array();


   /**
    * gpc for mode delete
    *
    * @since homeland voice 0.0
    */
   public $mode_gpc_export = array('_GET' => array('type') );

   /**
    * Standard prefix for classes inheriting this class
    *
    */
   public function standard_prefix() {
      return static::$standard_prefix;
   }


   /**
    * Gets the admin model
    *
    */
   public function admin_model() {
      return "admin";
   }


   /**
    * Require admin login
    *
    * @param array $common_gpc
    *
    */
   public function pre_mode_process($common_gpc) {
      $admin = $this->admin_model();
      $admin::require_logged_in();
      $this->assign('model_class',static::model());
      $this->assign('columns',$this->get_columns());
      $model_class = &$this->data['model_class'];
      if ($this-> mode == 'edit') {
         extract($common_gpc);
         $instance = $model_class::get_instance($common_gpc[$model_class::TABLE_PK]);
         if (!$instance) {
            throw new e_user_input("Failed to get row",array($model_class,$model_class::TABLE_PK,$common_gpc));
         }
         $this->assign('instance', $instance);
         $this->assign('article_id', $id);
         $this->assign('editable_columns',$this->editable_columns());
         $this->mode_gpc_edit['_POST'] = array_merge($this->mode_gpc_edit,array_keys($this->data['editable_columns']));
         #debug::var_dump($this->mode_gpc_edit,$this->data);
      }
      else if ($this-> mode == 'add') {
         extract($common_gpc);
         $this->assign('addable_columns',$this->get_columns());
         $this->mode_gpc_add['_POST'] = array_keys($this->data['addable_columns']);
      }
      else if ($this-> mode == 'delete') {
         extract($common_gpc);
         $this->assign('instance',$model_class::get_instance($id));
      }
   }

   /**
    * Export as CSV
    */
   public function process_mode_export($gpc) {
      $model = static::model();
      $this->assign('instances',$model::get_where_order_page(null));
      $tmpfile = tmpfile();
      set_time_limit(static::get_time_limit($gpc)/3);
      $rows = $this->get_rows(array());
      fputcsv($tmpfile,array_keys($rows[0]));
      foreach ($rows as $row) {
         fputcsv($tmpfile,$row);
      }
      set_time_limit(static::get_time_limit($gpc)*(2/3));
      fseek($tmpfile, 0);
      ob_start('ob_gzhandler');
      $fstat = fstat($tmpfile);
      while ($chunk = fread($tmpfile,$fstat['size'])) {
         echo $chunk;
      }
      fclose($tmpfile);
      header("Content-Disposition: attachment; filename=quad_week_trend.csv");
      add::content_type('text/csv');
      ob_end_flush(); # Required
      add::shutdown();
   }


   /**
    * process mode for edit
    *
    * @since homeland voice 0.0
    */
   public function process_mode_edit($gpc) {
      extract($gpc);
      #debug::var_dump($gpc);
      if ($sub_mode == 'submit') {
         #debug::var_dump($this->data['editable_columns']);
         foreach ($this->data['editable_columns'] as $column => $column_data) {
            # Make sure it's really on the form
            if ($gpc[$column] !== NULL) {
               $this->data['instance']->$column = $gpc[$column];
            }
         }
         $this->data['instance']->update_db_row();
         add::redirect("?mode=default");
      }
   }

   /**
    * editable columns
    *
    * @since since
    */
   public function editable_columns() {
      $columns = array();
      foreach ( $this->get_columns() as $column => $column_label )  {
         if ($column_label['editable']) {
            $columns[] = $column;
         }
      }
      return $columns;
   }


   /**
    * process mode for edit
    *
    * @since homeland voice 0.0
    */
   public function process_mode_add($gpc) {
      extract($gpc);
      if ($sub_mode == 'submit') {
         $row_array = array();
         foreach ($this->data['addable_columns'] as $column) {
            if (${$column} !== NULL) {
               $row_array[$column] = ${$column};
            }
         }

         $model_class = &$this->data['model_class'];
         $new_instance = $model_class::add_new($row_array);

         add::redirect("?mode=default");
      }
   }

   /**
    * process mode for delete
    *
    * @since homeland voice 0.0
    */
   public function process_mode_delete($gpc) {
      extract($gpc);

      if ($sub_mode == 'submit') {
         $instance = $this->data['instance'];

         $this->admin_can_admin_instance();

         if ($instance)
            $instance->delete();
         add::redirect('?mode=default');
      }
   }

   /**
    * @todo admin check
    *
    * @return bool
    */
   public function admin_can_admin_instance() {
      return false;
   }


   /**
    * Get (desired) time limit for the whole execution
    *
    * @param array $data
    *
    */
   public static function get_time_limit($data) {
      if ($data['mode'] == 'export') {
         return 180;
      }
      else {
         return 30;
      }
   }



   /**
    * view_file_path() default
    *
    */
   public function view_filepath() {
      # This still returns the instances.tpl
      $real_tpl = parent::view_filepath();
      if ($this->view()->templateExists($real_tpl)) {
         return $real_tpl;
      }
      else {
         return "layouts/admin_instances.tpl";
      }

   }
}