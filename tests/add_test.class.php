<?php

class add_test extends \addph\framework\test\base
{

   /**
    * @test
    * */
   public function testStripNamespaceFromClass() {

      $classname = 'addph\framework\action_abstract';
      $classes_dir_namespace = '\addph';
      $expected_result = 'framework\action_abstract';
      $result = add::strip_namespace_from_class($classname,$classes_dir_namespace);
      $this->assertEquals($expected_result, $result);

   }


   /**
    * @test
    * */
   public function testStripNamespaceFromClass2() {

      $classname = 'addph\framework\action_abstract';
      $classes_dir_namespace = 'addph\\';
      $expected_result = 'framework\action_abstract';
      $result = add::strip_namespace_from_class($classname,$classes_dir_namespace);
      $this->assertEquals($expected_result, $result);
      
   }


   /**
    * @test
    * */
   public function test_classname2basename() {

      $classname = 'addph\framework\action_abstract';
      $expected_result = 'action_abstract.framework.addph';
      $result = add::classname2basename($classname);
      $this->assertEquals($expected_result, $result);

   }


   /**
    * @test
    * */
   public function test_classname2basename2() {

      $classname = 'action_abstract';
      $expected_result = 'action_abstract';
      $result = add::classname2basename($classname);
      $this->assertEquals($expected_result, $result);

   }



}
?>
