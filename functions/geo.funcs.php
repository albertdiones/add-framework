<?php
/**
 * Geo Location Functions
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Functions
 * @since ADD MVC 0.0
 * @version 0.0
 */

/**
 * Converts address into coordinates using Google Maps API
 * @param string $address the address to lookup
 * @since ADD MVC 0.0
 */
function address2coords($address) {
   $geocode_pending = true;
   $base_url = "http://maps.google.com/maps/geo?output=json";
   $request_url = $base_url . "&q=" . urlencode($address);
   while ($geocode_pending) {
      $response = json_decode(file_get_contents($request_url)) or die("url not loading");

      $status = $response->Status->code;
      if ($status == 200) {
         $geocode_pending  = false;
         $coordinates      = $response->Placemark[0]->Point->coordinates;
         $longitude        = $coordinates[0];
         $latitude         = $coordinates[1];
         $altitude         = $coordinates[2];
      }
      else if ($status == 620) {
         // sent geocodes too fast
         $delay += 100000;
      }
      else {
         // failure to geocode
         $geocode_pending = false;
         throw new e_unknown("Address " . $address . " failed to geocoded. Received status: " . $status);
      }
      usleep($delay);
  }
  return array($latitude,$longitude,$altitude,'latitude'=>$latitude,'longitude'=>$longitude,'altitude'=>$altitude);
}