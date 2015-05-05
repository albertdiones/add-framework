<?php

/**
 * Abstract page for editing a model instance
 *
 */
CLASS ctrl_abstract_edit_instance EXTENDS ctrl_tpl_page {

/**
 * common gpc: id
 *
 */
   public $common_gpc = array(
         '_REQUEST' => 'id'
      );


   /**
    * Pre mode process
    *
    * @param array $common_gpc
    *
    */
   public function pre_mode_process($common_gpc) {
      $this->assign('class',static::model());
      $this->assign('instance',{$this->data['class']}::get_instance($id));
      $this->assign('editable_fields',$this->editable_fields());
      $this->mode_gpc_edit['_POST'] = $this->data['editable_fields'];
   }


   /**
    * editable fields
    *
    * @since since
    */
   public function editable_fields() {
      $fields = array();
      foreach ( $this->data['instance'] as $field => $field_value )  {
         $fields[] = $field;
      }
      return $fields;
   }

   abstract public function model();
}