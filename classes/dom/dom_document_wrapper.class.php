<?php

/**
 * Wrapper class for PHP's DOMDocument class
 *
 * @package ADD MVC\DOM
 * @since ADD MVC 0.4.2
 * @version 0.0
 *
 * @author albertdiones@gmail.com
 */
CLASS dom_document_wrapper EXTENDS dom_wrapper {

  /**
   * Constructor function
   *
   * @param DOMDocument $document the domdocument instance to wrap
   *
   * @since ADD MVC 0.4.2
   */
   protected function __construct(DOMDocument $document) {
      $this->instance = $document;
   }


   /**
    * Find an element using xpath
    *
    * @param string $xpath
    *
    * @return object node_list_wrapper
    *
    * @since ADD MVC 0.4.2
    */
   public function xfind($xpath) {
      return parent::factory($this->dom_xpath()->query($xpath));
   }


   /**
    * Returns the HTML of the document
    *
    * @return string
    *
    * @since ADD MVC 0.4.2
    */
   public function html() {
      $html = @$this->instance->saveHTML();
      $html = preg_replace('/\n$/','',$html);
      return $html;
   }


   /**
    * Returns the DOMXpath instance that this object will use
    *
    * @return DOMXpath
    *
    * @since ADD MVC 0.4.2
    */
   public function dom_xpath() {
      if (!isset($this->dom_xpath)) {
         $this->dom_xpath = new DOMXpath($this->instance);
      }
      return $this->dom_xpath;
   }
}