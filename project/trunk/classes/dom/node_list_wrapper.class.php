<?php
/**
 * Wrapper for PHP's DOMNodeList
 *
 * When used like a dom_element_wrapper, it will do the operation on the first item on the list
 *
 * @package ADD MVC\DOM
 *
 *
 * @author albertdiones@gmail.com
 */
CLASS node_list_wrapper EXTENDS dom_wrapper IMPLEMENTS Iterator {

   /**
    * The internal iterator key
    *
    * @since ADD MVC 0.4.2
    */
   private $iterator_key=0;


   /**
    * Constructs new instance from DOMNodeList object
    *
    * @see dom_wrapper::find()
    *
    * @param DOMNodeList $domnodelist The DOMNodelist
    *
    * @since ADD MVC 0.4.2
    */
   protected function __construct(DOMNodeList $domnodelist) {
      $this->instance = $domnodelist;
   }


   /**
    * Find an element inside the first element using xfind
    *
    * @param string $xpath
    *
    * @since ADD MVC 0.4.2
    */
   public function xfind($xpath) {
      #debug::var_dump(parent::factory($this->first_item())->html());
      return parent::factory($this->dom_xpath()->query($xpath));
   }


   /**
    * Returns the DOMXPath object to use
    *
    * @since ADD MVC 0.4.2
    */
   public function dom_xpath() {
      if (!isset($this->dom_xpath)) {
         if ($this->first_item()) {
            $this->dom_xpath = new DOMXpath($this->first_item()->dummy_document());
         }
         else {
            $this->dom_xpath = new DOMXpath(new DOMDocument);
         }
      }
      return $this->dom_xpath;
   }


   /**
    * Returns the value attribute
    *
    * @since ADD MVC 0.4.2
    */
   public function val() {
      return $this->first_item()->getAttribute('value');
   }


   /**
    * Returns the innerHTML of the first element
    *
    * @since ADD MVC 0.4.2
    */
   public function html() {
      if (!$this->first_item())
         return "";
      return $this->first_item()->html();
   }


   /**
    * Returns the parent element of the first element
    *
    * @since ADD MVC 0.4.2
    */
   public function parent() {
      return $this->first_item()->parentNode;
   }


   /**
    * Returns the value of attribute $name of the first element
    *
    * @param string $name the attribute name
    *
    * @since ADD MVC 0.4.2
    */
   public function attr($name) {
      return $this->first_item()->getAttribute($name);
   }


   /**
    * Returns the text value of the first element (non html)
    *
    * @since ADD MVC 0.4.2
    */
   public function text() {
      if ($this->first_item())
         return strip_tags($this->html());
      else
         return "";
   }

   /**
    * Iterator function rewind()
    *
    * @since ADD MVC 0.4.2
    */
   public function rewind() {
      $this->iterator_key=0;
   }

   /**
    * Iterator fucntion current()
    *
    * @since ADD MVC 0.4.2
    */
   public function current() {
      if ($this->valid())
         return parent::factory($this->instance->item($this->iterator_key));
      else
         return false;
   }

   /**
    * Iterator fucntion key()
    *
    * @since ADD MVC 0.4.2
    */
   public function key() {
      return $this->iterator_key;
   }

   /**
    * Iterator fucntion next()
    *
    * @since ADD MVC 0.4.2
    */
   public function next() {
      $this->iterator_key++;
      if ($this->valid())
         return parent::factory($this->instance->item($this->iterator_key));
      else
         return false;
   }

   /**
    * Iterator fucntion valid()
    *
    * @since ADD MVC 0.4.2
    */
   public function valid() {
      $valid = $this->instance->item($this->iterator_key);
      return (bool)$valid;
   }

   /**
    * Gets the first item from the node list
    *
    */
   public function first_item() {
      if ($this->instance->item(0))
         return dom_wrapper::factory($this->instance->item(0));
      else
         return false;
   }
}