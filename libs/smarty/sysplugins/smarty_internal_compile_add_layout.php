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
    * @param object $compiler
    * @param array $attributes
    *
    * @see Smarty_Internal_CompileBase::getAttributes($compiler, $attributes)
    *
    * @since ADD MVC 0.10
    */
   public function getAttributes($compiler, $attributes) {
      $args = func_get_args();
      if (isset($args[1][0]['file'])) {
         $args[1][0]['file'] = preg_replace('/^(\\\'|\")/','$0layouts/',$args[1][0]['file']);
         $args[1][0]['file'] = preg_replace('/(\\\'|\")$/','.tpl$0',$args[1][0]['file']);
      }
      $result = call_user_func_array('parent::'.__FUNCTION__,$args);

      return $result;
   }
}