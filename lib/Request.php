<?php
/**
 * @package ly.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

class Request {

    protected function __construct() {}
    protected function __clone() {}

    // see https://github.com/BKcore/NoCSRF/blob/master/nocsrf.php
    protected static $csrfError = null;
    /**
     * Check CSRF tokens match between session and $origin.
     * Make sure you generated a token in the form before checking it.
     *
     * @param String $key The session and $origin key where to find the token.
     * @param Mixed $origin The object/associative array to retreive the token data from (usually $_POST).
     * @param Integer $timespan (Facultative) Makes the token expire after $timespan seconds. (null = never)
     * @param Boolean $multiple (Facultative) Makes the token reusable and not one-time. (Useful for ajax-heavy requests).
     *
     * @return Boolean Returns FALSE if a CSRF attack is detected, TRUE otherwise.
     */
    public static function csrfCheck( $key, $origin, $timespan=null, $multiple=false )
    {
        if ( !isset( $_SESSION[ 'csrf_' . $key ] ) )
        {
            self::$csrfError = 'Missing CSRF session token';
            return false;
        }

        if ( !isset( $origin[ $key ] ) )
        {
            self::$csrfError = 'Missing CSRF form token';
            return false;
        }

        $hash = $_SESSION[ 'csrf_' . $key ];

        // Free up session token for one-time CSRF token usage.
        if(!$multiple)
            $_SESSION[ 'csrf_' . $key ] = null;

        // Origin checks
        if( sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) != substr( base64_decode( $hash ), 10, 40 ) )
        {
            self::$csrfError =  'Form origin does not match token origin.';
            return false;
        }

        // Check if session token matches form token
        if ( $origin[ $key ] != $hash )
        {
            self::$csrfError = 'Invalid CSRF token.';
            return false;
        }

        // Check for token expiration
        if ( $timespan != null && is_int( $timespan ) && intval( substr( base64_decode( $hash ), 0, 10 ) ) + $timespan < time() )
        {
            self::$csrfError = 'CSRF token has expired.';
            return false;
        }

        // All ok
        return true;
    }

    /**
     * CSRF token generation method.
     *
     * @param String $key The session key where the token will be stored.
     * @return String The generated, base64 encoded token.
     */
    public static function csrfGet( $key )
    {
        $extra = sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] );
        // token generation (basically base64_encode any random complex string, time() is used for token expiration)
        $token = base64_encode(
            time() .
            sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) .
            \Misc::randomString( 32 )
        );
        // store the one-time token in session
        $_SESSION[ 'csrf_' . $key ] = $token;
        return $token;
    }

    /**
     * CSRF error string.
     *
     * @return String|False Error check CSRF.
     */
    public static function csrfErrorString()
    {
        return self::$csrfError;
    }



}