<?php
/**
 * Date functions
 *
 * @deprecated ... not sure why is this even here and existing
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Functions
 * @since ADD MVC 0.0
 * @version 0.0
 */

/**
 * server date from the gmt timestamp
 * @param string $format the date format
 * @param int $gmt_timestamp the timestamp on GMT timezone
 *
 * @see http://www.php.net/manual/en/function.date.php, http://www.php.net/manual/en/class.datetime.php#datetime.constants.types
 * @todo automatically fetch the timezone offset from the server settings
 */
function date_from_gmttimestamp($format,$gmt_timestamp) {
   return date($format,$gmt_timestamp+3600*-7);
}