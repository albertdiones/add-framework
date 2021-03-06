<?php
/**
 * add_encryptor
 *
 * A reversible encryption class
 *
 *
 * @package ADD MVC\Extras
 * @since ADD MVC 0.6
 * @version 0.1
 */
CLASS add_encryptor {


   /**
    * Default encryption cypher
    *
    * @since ADD MVC 0.6
    */
   const DEFAULT_CYPHER = MCRYPT_RIJNDAEL_256;


   /**
    * Default encryption mode
    *
    * @since ADD MVC 0.6
    */
   const DEFAULT_MODE = MCRYPT_MODE_CBC;

   /**
    * The string to encrypt
    *
    * @since ADD MVC 0.6
    */
   public $string;

   /**
    * The key to use on encryption
    *
    * @since ADD MVC 0.6
    */
   public $key;

   /**
    * cypher to use on encryption
    *
    * @since ADD MVC 0.6
    */
   public $cypher = self::DEFAULT_CYPHER;

   /**
    * The mode used for mcrypt_encrypt(), mcrypt_get_iv_size() and mcrypt_decrypt()
    *
    */
   public $mode = self::DEFAULT_MODE;


   /**
    * IV - change at your own will
    *
    */
   public $iv = 'RFc1TuMCWs7SL6LPBlKRsDXhTQWTV7';


   /**
    * Create a new instance of this class, pass the first argument
    *
    * @param string $string the string to encrypt
    * @param string $key custom string to use as key, omit to generate a key, then get the key using $this->key
    *
    * example:
    * {{{
    * $encryptor = new add_encryptor('myPassword','abcdefghij');
    * $encrypted_string = $encryptor->encrypt()
    * }}}
    *
    * @since ADD MVC 0.6
    */
   public function __construct($string = false, $key=false ) {
      if ($string) {
         $this->string = $string;
      }
      if ($key === false && empty($this->key)) {
         $key = static::generate_key(30);
      }

      if (is_string($key)) {
         $this->key = $key;
      }

   }

   /**
    * Encrypt $this->string passed from __construct($string)
    *
    * @return string encrypted string
    *
    * @since ADD MVC 0.6
    */
   public function encrypt() {
      if (empty($this->key)) {
         throw new e_developer("No valid key was found");
      }
      return static::string_encrypt($this->string,$this->key,$this->cypher,$this->mode, $this->iv);
   }

   /**
    * Decrypt the $encrypted_string and create a new instance of this class
    *
    * @param string $encrypted_string
    * @param string $key
    * @param int $cypher
    * @param int $mode
    *
    * @since ADD MVC 0.6
    */
   public static function from_encrypted($encrypted_string, $key = false, $cypher = false, $mode = false, $iv = false) {

      $decrypted_string = static::string_decrypt($encrypted_string, $key, $cypher, $mode);

      $instance                        = new static();
      $key && $instance -> key         = $key;
      $cypher && $instance -> cypher   = $cypher;
      $mode && $instance -> mode       = $mode;
      $iv && $instance -> iv           = $iv;

      $instance->string = $instance::string_decrypt($encrypted_string, $instance->key, $instance -> cypher, $instance -> mode, $instance -> iv);

      return $instance;

   }

   /**
    * Encrypt $string
    *
    * @param string $string string to encrypt
    * @param string $key key to use on encryption
    * @param int $cypher cypher to use
    * @param int $mode mode to use
    *
    * @since ADD MVC 0.6
    */
   public static function string_encrypt($string, $key, $cypher = false, $mode = false, &$iv = false) {

      if (!$cypher) {
         $cypher = static::DEFAULT_CYPHER;
      }

      if (!$mode) {
         $mode = static::DEFAULT_MODE;
      }

      if (!$iv) {
         $iv = mcrypt_create_iv(
               mcrypt_get_iv_size($cypher, $mode),
               MCRYPT_RAND
            );
      }

      $encrypted = mcrypt_encrypt(
            $cypher,
            $key,
            $string,
            $mode,
            $iv
         );

      return base64_encode($encrypted);
   }

   /**
    * Decrypt $string
    *
    * @param string $string encrypted string
    * @param string $key key to used on encryption
    * @param int $cypher cypher used
    * @param int $mode mode used
    *
    * @since ADD MVC 0.6
    */
   public static function string_decrypt($string, $key, $cypher = false, $mode = false, $iv = false) {

      if (!$cypher) {
         $cypher = static::DEFAULT_CYPHER;
      }

      if (!$mode) {
         $mode = static::DEFAULT_MODE;
      }

      $decrypted = mcrypt_decrypt(
            $cypher,
            $key,
            base64_decode($string),
            $mode,
            $iv
         );

      return trim($decrypted);
   }

   /**
    * Key generator
    *
    * @param int $length an integer to determine the length of key to be generated
    *
    * @since ADD MVC 0.6
    *
    * @author Bryan Requinala <brian.requinala@247talk.net>
    */

   public static function generate_key($length = 30) {
      $key = "";

      $key_content = "abcdefghijklamnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()_+~";

      for ($x=1; $x<=$length; $x++) {
         $random_character = $key_content{array_rand(str_split($key_content))};
         $key .= $random_character;
      }

      return $key;
   }

}