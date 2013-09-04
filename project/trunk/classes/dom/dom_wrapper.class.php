<?php
/**
 * DOM wrapper for dom and html elements manipulation, with CSS like searching (see find())
 * Based on the function names of Jquery
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC DOM Tools
 * @since ADD MVC 0.0
 * @version 0.0
 */
ABSTRACT CLASS dom_wrapper {

   /**
    * The $instance of the DOM object
    *
    * @since ADD MVC 0.0
    */
   protected $instance;

   /**
    * The dom xpath cache
    *
    * @since ADD MVC 0.4.2
    */
   protected $dom_xpath;


   /**
    * factory function, creates a new wrapped class
    *
    * @param $arg either domnode, domnodelist or domdocument instance
    *
    * @return object dom_document_wrapper, node_list_wrapper or dom_element_wrapper or false or null
    *
    * @todo Remove or fix the code on if (is_object($arg)) { onwards
    */
   public function factory($arg) {
      if ($arg===false || $arg===NULL)
         return false;

      if (is_string($arg)) {

         if (!$arg) {
            throw new e_developer("Blank string parameter passed to ".get_called_class()."::".__FUNCTION__);
         }

         if (isset($this) && $this instanceof self) {
            return $this->find($arg);
         }
         else {
            if (file_exists($arg) || filter_var($arg, FILTER_VALIDATE_URL)) {
               $file_contents = file_get_contents($arg);
            }
            else if ($arg) {
               $file_contents = $arg;
            }

            $fixed_contents = iconv("UTF-8", "ASCII//IGNORE//TRANSLIT",$file_contents);

            $fixed_contents = preg_replace( '/[^\P{Cc}\s]+/u', '', $fixed_contents);

            if (!$fixed_contents) {
               throw new e_developer("Fixing the content resulted to blank string",$arg);
            }

            $dom_document  = @DOMDocument::loadHTML($fixed_contents);

            if (!$dom_document) {
               throw new e_developer("Failed to create domdocument from contents",$arg);
            }

            $document = self::factory($dom_document);

            return $document;

         }
      }
      if (is_object($arg)) {
         $classname = strtolower(get_class($arg));
         switch ($classname) {
            case 'domelement':
            case 'domnode':
               return new dom_element_wrapper($arg);
            break;
            case 'domnodelist':
               return new node_list_wrapper($arg);
            break;
            case 'domdocument':
               return new dom_document_wrapper($arg);
            break;
            default:
               throw new Exception("Object not supported: $classname");
            break;
         }
      }
      else {
         throw new e_developer("Invalid argument for ".__CLASS__."::".__FUNCTION__,$arg);
      }
   }

  /**
   * enables function call by $arg()
   * example: $document('div > .odd');
   * @param string $arg the css3 string to query
   *
   * @see http://www.php.net/manual/en/language.oop5.magic.php#object.invoke
   */
   public function __invoke($arg) {
      return self::factory($arg);
   }

   /**
    * Magic function __call
    *
    * @param string $func
    * @param array $args
    *
    * @see http://sg.php.net/manual/en/language.oop5.overloading.php
    *
    * Will call any domdocument,domnodelist or domnode class methods
    */
   public function __call($func,$args) {
      if (method_exists($this->instance,$func))
         return call_user_func_array(array($this->instance,$func),$args);
      else
         throw new e_developer(__CLASS__." function $func does not exist!",$args);
   }

   /**
    * Magic function __get
    * @param mixed $varname
    * @since ADD MVC 0.0
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.get
    */
   public function __get($varname) {
      return $this->instance->$varname;
   }


   /**
    * find($selector)
    *
    * @param string $selector the css3 string selector
    *
    * @return Add_DOMNodeWrapper or Add_Add_DOMNodeListWrapper
    */
   public function find($selector) {
      return $this->xfind(self::css2xpath($selector));
   }

   /**
    * css2xpath($selector)
    * Converts CSS3 path to Xpath
    * @param string $selector css selector
    */
   public static function css2xpath($selector) {
      $selector_chunks = preg_split('/(?<!\\\\)\s+/',$selector);
      $xpath_parts = array();
      foreach ($selector_chunks as $selector_chunk) {
         $selector_chunk = str_replace(',',' | //',$selector_chunk);
         # .class or #id (without tagname)
         $selector_chunk = preg_replace('/^([\W])\w/','*$0',$selector_chunk);
         # [name] or [name=value]
         $selector_chunk = preg_replace('/\[(.+?(\=.+?)?)\]/','[@$1]',$selector_chunk);
         # .class
         $selector_chunk = preg_replace('/\.([\w-]+)/','[contains(concat(" ",normalize-space(@class)," ")," $1 ")]',$selector_chunk);
         # #id
         $selector_chunk = preg_replace('/^([\w\-]*)\#([\w\-]+)/','$1[@id="$2"]$3',$selector_chunk);
         # :eq(n)
         $selector_chunk = preg_replace_callback('/\:eq\((\d+)\)/',function($matches) { return "[".($matches[1]+1)."]"; },$selector_chunk);
         # :first
         $selector_chunk = preg_replace('/\:first/','[1]',$selector_chunk);
         $selector_chunk = preg_replace('/\:last/','[last()]',$selector_chunk);
         $selector_chunk = preg_replace_callback('/\:gt\((\d+)\)/',function($matches) { return "[position()>".($matches[1]+1)."]"; },$selector_chunk);
         $selector_chunk = preg_replace_callback('/\:lt\((\d+)\)/',function($matches) { return "[position()<".($matches[1]+1)."]"; },$selector_chunk);
         $selector_chunk = str_replace("\\ "," ",$selector_chunk);
         $selector_chunk = preg_replace('/\:contains\((.+)\)/','[contains(.,$1)]',$selector_chunk);
         $xpath_parts[] = $selector_chunk;
      }

      $xpath = array("/");
      foreach ($xpath_parts as $i=>$xpath_part) {
         if ($i>0) {
            if ($xpath_part==">") {
               continue;
            }
            if ($xpath_parts[$i-1]!=">") {
               $xpath_part = "/$xpath_part";
            }
            //echo $xpath_parts[$i-1]." - ".$xpath_part."<br />";
         }
         $xpath[] = $xpath_part;
      }
      $xpath = implode("/",$xpath);
      //add_debug::var_dump($xpath);
      return $xpath;
   }
}