<?php
use PHPUnit\Framework\TestCase;

class addTest extends TestCase
{

   public function setUp() {
      $C = (object) array(
         'add_dir' => realpath('./'),

         'app_namespace' => 'addph',

         'path'               => '/',

         'environment_status' => 'development',
         'version'            => '1.1',
         'developer_ips'      => array(
            #'123.123.123.123', # add.ph (server IP)
         ),
         'developer_emails'   => array('albert@add.ph','albertdiones@gmail.com'),


      );
      require $C->add_dir.'/init.php';
      add::$handle_shutdown = false;
   }


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
}
?>
