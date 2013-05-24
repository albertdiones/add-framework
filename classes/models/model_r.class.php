<?php
/**
 * model_r abstract model class
 * readonly model (no update and delete)
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC Models
 * @since ADD MVC 0.0
 * @version 0.0
 */
ABSTRACT CLASS model_r EXTENDS model_rwd {

   /**
    * Attempt to assign a value to a field from the outside
    *
    * @param string $void
    * @param string $void2
    *
    * @since ADD MVC 0.0
    */
   public function __set($void,$void2) {
      throw new e_developer("Variable writing is not allowed on read only model ".get_called_class());
   }

   /**
    * Attempt to update the database row
    *
    * @since ADD MVC 0.0
    */
   public function update_db_row() {
      throw new e_developer("Updating not allowed on read only model ".get_called_class());
   }

   /**
    * Attempt to insert a new row
    * @param array $void
    * @since ADD MVC 0.0
    */
   static function add_new($void) {
      throw new e_developer("Adding new rows prohibited on read only model ".get_called_class());
   }

   /**
    * Attempt to delete this row
    *
    * @since ADD MVC 0.0
    */
   public function delete() {
      throw new e_developer("Deleting rows prohibited on read only model ".get_called_class());
   }
}