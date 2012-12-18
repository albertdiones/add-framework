<?php
/**
 * array_entity class
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Extras
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS array_entity IMPLEMENTS Iterator {

   /**
    * $this->data contains the $array contents
    *
    * @since ADD MVC 0.0
    */
   protected $data = array();

   /**
    * __construct($array)
    * Fills $this->data with the $array contents
    * @param array $array
    * @since ADD MVC 0.0
    */
   protected function __construct($array) {
      if (isset($array)) {
         foreach ($array as $index=>$value) {
            if (is_array($value)) {
               $this->data[$index] = new static($array[$index]);
            }
            else {
               $this->data[$index] = $array[$index];
            }
         }
      }
   }


   /**
    * construct
    *
    * @since ADD MVC 0.4
    */
   final public static function construct($array) {
      return new static($array);
   }

   /**
    * Magic function __get
    * Gets $this->data[$varname]
    * @param mixed $varname
    * @since ADD MVC 0.0
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.get
    */
   public function __get($varname) {
      if (!static::valid_magic_property($varname)) {
         throw new e_developer("Invalid ".get_called_class()." variable $varname");
      }
      return $this->data[$varname];
   }

   /**
    * Magic function __set
    * Sets $this->data[$varname] = $value
    * @param mixed $varname
    * @param mixed $value
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.set
    * @since ADD MVC 0.0
    */
   public function __set($varname,$value) {
      if (!static::valid_magic_property($varname)) {
         throw new e_developer("Invalid ".get_called_class()." variable $varname");
      }
      $this->data[$varname] = $value;
   }

   /**
    * Magic function __isset()
    *
    * @param mixed $varname (scalar values only)
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.isset
    * @since ADD MVC 0.0
    */
   public function __isset($varname) {
      return array_key_exists($varname,$this->data);
   }

   /**
    * Magic function __unset()
    *
    * @param mixed $varname (scalar values only)
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.unset
    * @since ADD MVC 0.0
    */
   public function __unset($varname) {
      return static::__set($varname,null);
   }


   /**
    * Checks if the $this->data index is valid
    * @param string $varname
    * @todo consider merging/relating __isset() and this function.
    * @since
    */
   public function valid_magic_property($varname) {
      return true;
   }


/**
 * -----------------
 * Iterator Interface functions
 * @see http://www.php.net/manual/en/class.iterator.php
 * @see http://www.php.net/manual/en/language.oop5.iterations.php
 * @ignore
 * -----------------
 */


   /**
    * Iterator::rewind() implementation
    * @see http://www.php.net/manual/en/iterator.rewind.php
    * @since ADD MVC 0.0
    */
   public function rewind() {
      reset($this->data);
   }


   /**
    * Iterator::current() implementation
    * @see http://www.php.net/manual/en/iterator.current.php
    * @since ADD MVC 0.0
    */
   public function current() {
      return current($this->data);
   }


   /**
    * Iterator::key() implementation
    * @see http://www.php.net/manual/en/iterator.key.php
    * @since ADD MVC 0.0
    */
   public function key() {
      return key($this->data);
   }


   /**
    * Iterator::next() implementation
    * @see http://www.php.net/manual/en/iterator.next.php
    * @since ADD MVC 0.0
    */
   public function next() {
      return next($this->data);
   }


   /**
    * Iterator::valid() implementation
    * @see http://www.php.net/manual/en/iterator.valid.php
    * @since ADD MVC 0.0
    */
   public function valid() {
      $key = key($this->data);
      $var = ($key !== NULL && $key !== FALSE);
      return $var;
   }
}