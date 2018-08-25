<?php
/**
 * Created by PhpStorm.
 * User: lamudi-20150106
 * Date: 25/08/2018
 * Time: 19:18
 */
class debug_test EXTENDS \addph\framework\test\base {


   public function setUp() {
      parent::setUp();
   }

   public function tearDown() {

   }


   /**
    * @test
    * */
   public function test_is_developer() {
      $this->assertEquals(true,debug::is_developer());
   }


   /**
    * @test
    * */
   public function test_var_dump_string() {

      $test_string = "Hello World";

      ob_start();
      debug::var_dump($test_string);
      $result = ob_get_clean();


      $this->assertRegexp('/'.preg_quote($test_string,'/').'/',$result);
   }


   /**
    * @test
    * */
   public function test_print_data_string() {

      $label = "message";
      $test_string = "Hello World";

      ob_start();
      debug::print_data($label,$test_string);
      $result = ob_get_clean();


      $this->assertRegexp('/'.preg_quote($label,'/').'/',$result);
      $this->assertRegexp('/'.preg_quote($test_string,'/').'/',$result);
   }

   /**
    * @test
    * */
   public function test_print_data_array() {

      $label = "message";
      $test_strings = array("Hello", "World");

      ob_start();
      debug::print_data($label,$test_strings);
      $result = ob_get_clean();


      $this->assertRegexp('/'.preg_quote($label,'/').'/',$result);
      $this->assertRegexp('/'.preg_quote($test_strings[0],'/').'/',$result);
      $this->assertRegexp('/'.preg_quote($test_strings[1],'/').'/',$result);
   }

}