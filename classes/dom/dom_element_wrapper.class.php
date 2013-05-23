<?php
/**
 * HTML Element class
 * To be merged with add_dom classes I've made
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\DOM
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS dom_element_wrapper EXTENDS dom_wrapper {

   /**
    * The tagname of the dom element
    *
    * @since ADD MVC 0.0
    */
   protected $tag_name;


   /**
    * The dom document that owns this element
    *
    * @since ADD MVC 0.0
    */
   public $dummy_document;

   /**
    * construct function
    *
    * @param mixed $arg1 tag name or a dom element
    * @param string $value the html contents
    * @param string $namespaceURI the dom namespace
    *
    * @since ADD MVC 0.0
    */
   public function __construct($arg1=NULL,$value=NULL,$namespaceURI=NULL) {
      if (is_string($arg1)) {
         $tag_name = $arg1;
         $elem = new DOMElement( $tag_name, $value, $namespaceURI );
      }
      else if ($arg1 instanceof DOMElement) {
         $elem = $arg1;
      }
      $this->instance = $elem;
   }

   /**
    * Creates a dummy document if there's no ownerdocument
    *
    * @since ADD MVC 0.0
    */
   public function get_document() {

      if (!isset($this->instance->ownerDocument)) {
         $this->document = new DomDocument;
         $this->document->appendChild($this->instance);
      }

      return $this->instance->ownerDocument;
   }


   /**
    * Creates a dummy document
    *
    * @since ADD MVC 0.0
    */
   public function dummy_document() {

      if (!isset($this->dummy_document)) {
         if (!isset($this->instance->ownerDocument)) {
            $this->dummy_document = $this->get_document();
         }
         else {
            $this->dummy_document = new DomDocument;
            $this->dummy_document->appendChild($this->dummy_document->importNode($this->instance,true));
         }
      }

      return $this->dummy_document;
   }


   /**
    * The html contents
    *
    * @since ADD MVC 0.0
    */
   public function html() {
      $doc = new DOMDocument();
      foreach ($this->instance->childNodes as $child) {
         $doc->appendChild($doc->importNode($child, true));
      }
      $html = @$doc->saveHTML();
      $html = preg_replace('/\n$/','',$html);
      return $html;
   }

   /**
    * Get an attribute
    *
    * @param string $name the name of the attribute
    * @param string $value if set, sets the value
    *
    * @since ADD MVC 0.0
    */
   public function attr($name,$value=false) {

      if ($value) {
         return $this->setAttribute($name,$value);
      }

      return $this->getAttribute($name);
   }

   /**
    * Append content on the end of the dom element
    *
    * @param string $content the content to append
    *
    * @since ADD MVC 0.0
    */
   public function append($content) {
      $doc = new DOMDocument();
      $random_element = "xml".rand(1,100);
      $doc->loadXML("<$random_element>".$content."</$random_element>");
      $node = $doc->documentElement->firstChild;
      $imported_node = $this->get_document()->importNode($node,true);
      $this->appendChild($imported_node);
      return $imported_node;
   }

   /**
    * Returns the HTML code of the DOM element
    *
    * @since ADD MVC 0.0
    */
   public function __toString() {
      return $this->C14N();
   }

   /**
    * Inserts an element on the start of the element
    *
    * @param DOMNode $newnode the new domnode to prepend
    * @param DOMNode $refnode the ref node
    *
    * @since ADD MVC 0.0
    */
   public function insertBefore( DOMNode $newnode, DOMNode $refnode = NULL) {
      if (is_string($newnode)) {
         $newnode = self::factory($newnode);
      }
      else if (! $newnode instanceof DOMNode) {

      }
   }


   /**
    * Find an element using xfind
    *
    * @param string $xpath
    *
    * @since ADD MVC 0.4.2
    */
   public function xfind($xpath) {
      return parent::factory($this->dom_xpath()->query($xpath));
   }

   /**
    * Returns the DOMXPath object to use
    *
    * @since ADD MVC 0.4.2
    */
   public function dom_xpath() {
      if (!isset($this->dom_xpath)) {
         $this->dom_xpath = new DOMXpath($this->dummy_document());
      }
      return $this->dom_xpath;
   }

   /**
    * Returns the text value of the first element (non html)
    *
    * @since ADD MVC 0.4.2
    */
   public function text() {
      return strip_tags($this->html());
   }
}