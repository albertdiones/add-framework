<?php

/**
 * Network functions
 * @author albertdiones@gmail.com
 * @package ADD MVC\Functions
 *
 * @since ADD MVC 0.1
 * @version 0.0
 */

/**
 * ip_in_network(string $ip)
 * Checks if the IP is within the network of the server
 * @param string $ip the IP to check
 */
function ip_in_network($ip) {
   if (preg_match('/^(10\.\d+|192\.168)\.\d+\.\d+$/',$ip)) {
      return true;
   }
   else {
      return false;
   }
}


/**
 * bool current_ip_in_network(void)
 * Checks if the current user's ip is in the network
 * @uses ip_in_network
 */
function current_ip_in_network() {
   return ip_in_network(current_user_ip());
}

/**
 * string current_user_ip()
 * Gets the IP of the user
 *
 * @since ADD MVC 0.1
 */
function current_user_ip() {

   $ip_server_var_keys = array(
         'HTTP_CLIENT_IP',
         'HTTP_X_FORWARDED_FOR'
      );

   foreach ($ip_server_var_keys as $key) {
      if (!empty($_SERVER[$key])) {
         return $_SERVER[$key];
      }
   }

   return $_SERVER['REMOTE_ADDR'];

}