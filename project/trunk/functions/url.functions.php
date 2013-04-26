<?php

/**
 * Returns the complete url according to $base
 *
 * @param string $base
 * @param string $url
 *
 * @since ADD MVC 0.5
 */
function build_url($base,$url) {
   $base_parts=url_parts($base);

   # https://code.google.com/p/add-mvc-framework/issues/detail?id=81
   if (preg_match('/^javascript\:/',$url)) {
      return $url;
   }

   if ($url[0]==='/') {
      return rtrim($base_parts['protocol_domain'],'/').$url;
   }
   if ($url[0]==='?') {
      if (!$base_parts['pathname'])
         $base_parts['pathname']='/';
      return $base_parts['protocol_domain'].$base_parts['pathname'].$url;
   }
   if ($url[0]==='#') {

   }
   if (preg_match('/^https?\:\/+/',$url)) {
      return $url;
   }

   return rtrim($base_parts['protocol_domain'],"/").$base_parts['path'].$url;
}

/**
 * Returns the URL parts of the url
 *
 * @param string $Url
 *
 * @since ADD MVC 0.5
 */
function url_parts($url) {
   if (!preg_match('/^(?P<protocol_domain>(?P<protocol>https?\:\/+)(?P<domain>([^\/\W]|[\.\-])+))(?P<request_uri>(?P<pathname>(?P<path>\/(.+\/)?)?(?P<file>[^\?\#]+?)?)?(?P<query_string>\?[^\#]*)?)(\#(?P<hash>.*))?$/',$url,$url_parts)) {
      echo debug_backtrace();
      throw new Exception("Invalid url: $url");
   }
   return $url_parts;
}
?>