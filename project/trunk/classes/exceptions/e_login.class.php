<?php
/**
 * e_login class (Emailed)
 *
 * This will typically send email to developer_emails declared on config
 *
 * Custom Exception Class for hackish login required errors
 *
 * <code>
 * if (!member::current_logged_in()) {
 *    throw new e_hack("Attempt to vote without logging in",$current_user);
 * }
 * </code>
 *
 * @author albertdiones@gmail.com
 *
 * @package ADD MVC\Exceptions
 * @since ADD MVC 0.0
 * @version 0.0
 */
CLASS e_login EXTENDS e_hack {

}