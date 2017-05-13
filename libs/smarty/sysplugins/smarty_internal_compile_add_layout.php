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
      $GLOBALS['debug_add_layout'] = $attributes;

      $attributes = call_user_func_array('parent::'.__FUNCTION__,$args);

      if (isset($attributes['file'])) {
         $_smarty_tpl = $compiler->template;
         eval('$attributes[\'file\'] = '.$attributes['file'] .';');
         #file_put_contents('/tmp/debug_add_layout.txt',file_get_contents('/tmp/debug_add_layout.txt')."\r\nx\r\n".print_r($attributes,true));
         $attributes['file'] = preg_replace('/^(?!=layouts\/)/','layouts/',$attributes['file']);
         $attributes['file'] = preg_replace('/(?!=\.tpl)$/','.tpl',$attributes['file']);
         $attributes['file'] = "'".$attributes['file']."'";
      }

      return $attributes;
   }
}