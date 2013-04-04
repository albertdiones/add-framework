<?php

/**
 * add_curl
 * advance cURL library
 *
 * @package ADD MVC\Extras
 * @version 1.0
 * @since ADD MVC 0.5
 *
 * @author albertdiones@gmail.com
 *
 */
CLASS add_curl {

   /**
    * Variable that holds the cURL resource variable
    *
    * @since ADD MVC 0.5
    */
   public $curl;


   /**
    * Variable that holds the URL of the cURL
    *
    * @since ADD MVC 0.5
    */
   public $url;

   /**
    * Variable that holds the header array
    *
    * @since ADD MVC 0.5
    */
   public $header = array(
         'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8 ( .NET4.0E)',
         'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
         'Accept-Language: en-us,en;q=0.5',
         'Accept-Encoding: gzip,deflate',
         'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
         'Keep-Alive: 115',
         'Connection: keep-alive'
      );


   /**
    * The cookie jar directory
    *
    * @since ADD MVC 0.5
    */
   public $cookie_dir;


   /**
    * Flag to enable caching
    *
    * @since ADD MVC 0.5
    */
   public $enable_cache = false;

   /**
    * The cache directory
    *
    * @since ADD MVC 0.5
    */
   public $cache_dir;

   /**
    * Enable proxy
    *
    * @since ADD MVC 0.5
    */
   public $enable_proxy = false;


   /**
    * Proxies IP:port
    *
    * @since ADD MVC 0.5
    */
   public $proxies = array(
         # '72.64.146.73:3128', # Example
      );


   /**
    * the proxy type constant
    *
    * @since ADD MVC 0.5
    */
   public $proxy_type = CURLPROXY_HTTP;


   /**
    * Current proxy
    *
    * @since ADD MVC 0.5
    */
   public $proxy;



   /**
    * Curl Options Array
    *
    */
   public $default_curl_options = array(
         CURLOPT_AUTOREFERER     => true,
         CURLOPT_ENCODING        => 'gzip,deflate',
         CURLOPT_MAXREDIRS       =>  5,
         CURLOPT_CONNECTTIMEOUT  => 10,
         CURLOPT_TIMEOUT         => 60,
         CURLOPT_FOLLOWLOCATION  => false
      );


   /**
    * Settings and stuffs
    *
    * @since ADD MVC 0.8-data_mining
    */
   public function __construct() {
      if (!isset($this->cookie_dir))
         $this->cookie_dir = add::config()->caches_dir.'/'.preg_replace('/\W+/','_',get_called_class()).'.class.cookies';
      if ($this->enable_cache) {
         $this->cache_dir = add::config()->caches_dir.'/'.preg_replace('/\W+/','_',get_called_class()).'.class.cache';
         if (!file_exists($this->cache_dir))
            mkdir($this->cache_dir,0777);
      }

      if ($this->enable_proxy && empty($this->proxy) && isset($this->proxies) && is_array($this->proxies) && $this->proxies) {
         $this->proxy = $this->proxies[array_rand($this->proxies)];
         $this->cookie_dir = add::config()->caches_dir.'/cookies_'.preg_replace('/\W+/','_',__FILE__)."_".preg_replace("/\W+/","_",$this->proxy);
      }

   }

   /**
    * Sets a field
    * (also updates the db row on the end of the script or after a call of $this->update_db_row())
    *
    * @param string $varname
    * @param mixed $value
    *
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.set
    * @since ADD MVC 0.10.7
    */
   public function __set($varname, $value) {
      if ($varname == 'enable_follow_location') {
         return $this->curl_options[CURLOPT_FOLLOWLOCATION] = $value;
      }
   }
   /**
    * Magic function __get
    * Gets $this->data[$varname]
    * @param mixed $varname
    * @since ADD MVC 0.0
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.get
    */
   public function __get($varname) {
      if ($varname == 'enable_follow_location') {
         return $this->curl_options[CURLOPT_FOLLOWLOCATION];
      }
   }

   /**
    * Magic function __isset()
    *
    * @param mixed $varname (scalar values only)
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.isset
    * @since ADD MVC 0.0
    */
   public function __isset($varname) {
      if ($varname == 'enable_follow_location') {
         return isset($this->curl_options[CURLOPT_FOLLOWLOCATION]);
      }
   }

   /**
    * Magic function __unset()
    *
    * @param mixed $varname (scalar values only)
    * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.unset
    * @since ADD MVC 0.0
    */
   public function __unset($varname) {
      if ($varname == 'enable_follow_location') {
         return static::__set($varname,null);
      }
   }

   /**
    * Advance cURL init
    *
    * @param string $url
    *
    * @since ADD MVC 0.5
    */
   function init($url) {

      $this->url = $url;

      $this->curl = curl_init();

      curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($this->curl, CURLINFO_HEADER_OUT, true);
      curl_setopt($this->curl, CURLOPT_URL, $url);

      if (preg_match('/https?\:\/\//',$url)) {
         curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
      }

      if ($cookie_dir = $this->cookie_dir) {
         curl_setopt($this->curl, CURLOPT_COOKIEJAR, $cookie_dir);
         curl_setopt($this->curl, CURLOPT_COOKIEFILE, $cookie_dir);
      }

      curl_setopt_array($this->curl,$this->default_curl_options);

      if ($this->enable_proxy) {
         curl_setopt($this->curl,CURLOPT_PROXYTYPE,$this->proxy_type);
         curl_setopt($this->curl,CURLOPT_PROXY,$this->proxy);
         curl_setopt($this->curl,CURLOPT_HTTPPROXYTUNNEL ,1);
      }

      return $this->curl;
   }

   /**
    * Parses the complete cURL response
    *
    * @param string $complete_response
    *
    * @since ADD MVC 0.5
    */
   public static function parse_response($complete_response){
      // Split response into header and body sections
      @list($response_headers, $response_body) = explode("\r\n\r\n", $complete_response, 2);
      $response_header_lines = explode("\r\n", $response_headers);

      // First line of headers is the HTTP response code
      $http_response_line = array_shift($response_header_lines);
      if(preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$http_response_line, $matches)) { $response_code = $matches[1]; }

      // put the rest of the headers in an array
      $response_header_array = array();
      foreach($response_header_lines as $header_line) {
         list($header,$value) = explode(': ', $header_line, 2);
         @$response_header_array[$header] .= $value."\n";
      }

      return @array(
            'code' => $response_code,
            'header' => $response_header_array,
            'body' => $response_body,
            'raw'=>$complete_response
         );
   }


   /**
    * returns complete cURL response of GET request
    *
    * @param string $url
    *
    * @since ADD MVC 0.5
    */
   public function get_response($url) {

      $this->init($url);

      curl_setopt($this->curl, CURLOPT_HEADER, 1);
      curl_setopt($this->curl, CURLOPT_REFERER, $url);

      $response = $this->exec();

      return static::parse_response($response);

   }

   /**
    * returns complete cURL response of POST request
    *
    * @param string $url
    * @param array  $post
    *
    * @since ADD MVC 0.5
    */
   public function post_response($url,$post) {

      $this->init($url);

      curl_setopt($this->curl, CURLOPT_HEADER, 1);
      curl_setopt($this->curl, CURLOPT_REFERER, $url);
      curl_setopt($this->curl, CURLOPT_POST, true);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

      $response = $this->exec();

      return static::parse_response($response);

   }

   /**
    * returns the response body of GET request
    *
    * @param string $url
    *
    * @since ADD MVC 0.5
    */
   public function get_body($url) {

      $this->init($url);

      if (!is_string($url)) {
         throw new e_developer("Invalid ".__CLASS__."->".__FUNCTION__."() parameter ",$url);
      }

      curl_setopt($this->curl, CURLOPT_REFERER, $url);

      if ($this->enable_cache) {


         $cache_path = $this->cache_path();

         if (!file_exists($cache_path)) {

            $response = $this->exec();

            if ($response) {
               file_put_contents($cache_path,$response);

               e_system::assert(file_exists($cache_path) && !is_dir($cache_path),"Failed to cache $cache_path");
            }
            else {
               throw new e_developer("Failed to fetch $this->url", array($this,$cache_path));
            }

         }

         if (file_exists($cache_path) && !is_dir($cache_path)) {
            $response = file_get_contents($cache_path);
         }
         else {
            $response = null;
         }

      }
      else {

         $response = $this->exec();

      }

      return $response;

   }


   /**
    * Returns the body of a post request
    *
    * @param string $url
    * @param mixed $post
    *
    * @since ADD MVC 0.6
    */
   public function post_body($url,$post) {

      $this->init($url);

      curl_setopt($this->curl, CURLOPT_HEADER, 0);
      curl_setopt($this->curl, CURLOPT_REFERER, $url);
      curl_setopt($this->curl, CURLOPT_POST, true);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

      $response = $this->exec();

      return $response;

   }


   /**
    * cache path of the current url
    *
    * @since ADD MVC 0.5
    */
   public function cache_path() {
      e_developer::assert($this->cache_dir,"Cache directory is blank");

      $cache_file_name = sha1($this->url);
      if ($this->enable_proxy) {
         $cache_file_name .= sha1($this->proxy);
      }

      return $this->cache_dir.'/'.$cache_file_name;
   }


   /**
    * delete cache file of the current url
    *
    * @since ADD MVC 0.5
    */
   public function cache_delete() {
      return unlink($this->cache_path());
   }

   /**
    * validates a URL if it's existing
    *
    * @param string $url
    *
    * @since ADD MVC 0.5
    */
   public static function is_valid_page($url) {
      $headers=get_headers($url,1);
      if (is_array($headers['Content-Type']))
         $headers['Content-Type']=$headers['Content-Type'][count($headers['Content-Type'])-1];
      if (is_array($headers['Content-Length']))
         $headers['Content-Length']=$headers['Content-Length'][count($headers['Content-Length'])-1];
      if ($headers['Content-Length']>500000) {
         throw new Exception("That page is too big: ".print_r($headers['Content-Length'],true)." bytes");
      }
      if (
            empty($headers['Content-Type'])
            ||
            (
               strpos($headers['Content-Type'],'html')
               ||
               strpos($headers['Content-Type'],'xml')
               ||
               strpos($headers['Content-Type'],'xhtml')
            )
         )
         return true;
      else
         throw new Exception("That is not a page ".print_r($headers['Content-Type'],true));
   }

   /**
    * Execute the cURL
    *
    * @since ADD MVC 0.5
    */
   public function exec() {
      $response = curl_exec($this->curl);

      if ($e = curl_error($this->curl)) {
         throw new e_system("curl error: (#".curl_errno($this->curl).")$e url:$this->url proxy: $this->proxy",null,curl_errno($this->curl));
      }

      $this->reset();

      return $response;
   }


   /**
    * reset()
    *
    * @since ADD MVC 0.5
    */
   public function reset() {
      curl_close($this->curl);
      unset($this->curl);
      unset($this->url);
   }


}