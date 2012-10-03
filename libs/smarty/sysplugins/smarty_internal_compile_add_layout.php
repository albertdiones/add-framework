<?php

/**
 * ADD MVC Extends of {extends} smarty tag. Require the layout to be in views/layouts/ directory
 *
 * @since ADD MVC 0.10
 */
CLASS Smarty_Internal_Compile_Add_Layout EXTENDS Smarty_Internal_Compile_Extends {

   /**
    * Here we change the path to layouts/path
    *
    * @since ADD MVC 0.10
    */
   public function getAttributes() {
      $result = call_user_func_array('parent::'.__FUNCTION__,func_get_args());
      $result['file'] = preg_replace('/^(\\\'|\")/','$0layouts/',$result['file']);
      $result['file'] = preg_replace('/(\\\'|\")$/','.tpl$0',$result['file']);
      return $result;
   }
}