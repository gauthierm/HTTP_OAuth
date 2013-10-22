<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * HTTP_OAuth
 *
 * Implementation of the OAuth specification
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>, 2013 silverorange Inc.
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 */

require_once 'HTTP/OAuth.php';

/**
 * HTTP_OAuth_Parameter
 *
 * Key-value parameter. This can be either a HTTP auth header parameter, an
 * HTTP GET parameter, or an HTTP POST/PUT parameter.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>, 2013 silverorange Inc.
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 */
class HTTP_OAuth_Parameter extends HTTP_OAuth
{
    /**
     * OAuth Parameters
     *
     * @var string $oauthParams OAuth parameters
     */
    static protected $oauthParams = array(
        'oauth_consumer_key',
        'oauth_token',
        'oauth_token_secret',
        'oauth_signature_method',
        'oauth_signature',
        'oauth_timestamp',
        'oauth_nonce',
        'oauth_verifier',
        'oauth_version',
        'oauth_callback',
        'oauth_session_handle',
    );

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $value = '';

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Prefix parameter
     *
     * Prefixes a parameter name with oauth_ if it is a valid oauth paramter
     *
     * @param string $param Name of the parameter
     *
     * @return string Prefix parameter
     */
    public static function getPrefixedName($name)
    {
        if (in_array('oauth_' . $name, self::$oauthParams)) {
            $name = 'oauth_' . $name;
        }

        return $name;
    }

    public static function compare(HTTP_OAuth_Parameter $a, HTTP_OAuth_Parameter $b)
    {
        $compare = strcmp($a->name, $b->name);

        // if parameter names are equivalent, RFC 5849 says they are sorted by
        // value.
        if ($compare === 0) {
            $compare = strcmp($a->value, $b->value);
        }

        return $compare;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = (string)$value;
        return $this;
    }

    public function isOAuth()
    {
        return (in_array($this->name, self::$oauthParams));
    }

    public function getEncoded()
    {
        return HTTP_OAuth::urlencode($this->name).'='.HTTP_OAuth::urlencode($this->value);
    }
}

?>
