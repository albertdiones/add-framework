<?php
use PHPUnit\Framework\TestCase;

ABSTRACT CLASS add_base_test EXTENDS TestCase
{

   public function setUp() {

      if (!class_exists('add',false)) {
         $C = (object) array(
            'root_dir' => realpath('./'),
            'add_dir' => realpath('./'),
            'app_namespace' => 'addph',

            'path'               => '/',

            'environment_status' => 'development',
            'version'            => '1.1',
            'developer_ips'      => array(
               #'123.123.123.123', # add.ph (server IP)
            ),
            'developer_emails'   => array('albert@add.ph','albertdiones@gmail.com'),
            'content_type' => 'text/plain'
         );
         require $C->add_dir.'/init.php';
         add::$handle_shutdown = false;
      }
   }

   public function tearDown() {

   }


}
?>
