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
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */

require_once 'HTTP/OAuth.php';
require_once 'HTTP/OAuth/Parameter.php';
require_once 'HTTP/OAuth/ParameterList.php';

/**
 * HTTP_OAuth_Message
 *
 * Main message class for Request and Response classes to extend from.  Provider
 * and Consumer packages use this class as there parent for the request/response
 * classes. This contains specification parameters handling and ArrayAccess,
 * Countable, IteratorAggregate features.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 * @link      http://github.com/jeffhodsdon/HTTP_OAuth
 */
abstract class HTTP_OAuth_Message extends HTTT_OAuth
    implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Parameters of this message
     *
     * @var HTTP_OAuth_ParameterList
     */
    protected $parameters = null;

    public function __construct()
    {
        $this->parameters = new HTTP_OAuth_ParameterList();
    }

    /**
     * Gets OAuth specific parameters
     *
     * @return HTTP_OAuth_ParameterList OAuth specific parameters
     */
    public function getOAuthParameters()
    {
        return $this->parameters->getOAuthOnly();
    }

    /**
     * Get parameters
     *
     * @return array Request's parameters
     */
    public function getParameters()
    {
        return $this->parameters->sort();
    }

    /**
     * Set parameters
     *
     * @param array $params Name => value pair array of parameters
     *
     * @return void
     */
    public function setParameters(array $params)
    {
        $this->paramaters->setMulti($params);
    }

    /**
     * Get signature method
     *
     * @return string Signature method
     */
    public function getSignatureMethod()
    {
        $method = $this->signature_method;

        if ($method === null) {
            $method = 'HMAC-SHA1';
        }

        return $method;
    }

    /**
     * Get
     *
     * @param string $var Variable to get
     *
     * @return mixed Parameter if exists, else null
     */
    public function __get($var)
    {
        $value = null;

        // check if parameter exists
        if ($value === null) {
            $param = $this->parameters->var;
        }

        // check if method exists (i.e. getBody())
        if ($value ===  null) {
            $method = 'get' . ucfirst($var);
            if (method_exists($this, $method)) {
                $value = $this->$method();
            }
        }

        return $value;
    }

    /**
     * Set
     *
     * @param string $var Name of the variable
     * @param mixed  $val Value of the variable
     *
     * @return void
     */
    public function __set($var, $val)
    {
        $this->parameters->$var = $val;
    }

    /**
     * Offset exists
     *
     * @param string $offset Name of the offset
     *
     * @return bool Offset exists or not
     */
    public function offsetExists($offset)
    {
        return (isset($this->parameters[$offset]));
    }

    /**
     * Offset get
     *
     * @param string $offset Name of the offset
     *
     * @return string Offset value
     */
    public function offsetGet($offset)
    {
        return $this->parameters[$offset];
    }

    /**
     * Offset set
     *
     * @param string $offset Name of the offset
     * @param string $value  Value of the offset
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->parameters[$offset] = $value;
    }

    /**
     * Offset unset
     *
     * @param string $offset Name of the offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->parameters[$offset]);
    }

    /**
     * Count
     *
     * @return int Amount of parameters
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * Get iterator
     *
     * @return ArrayIterator Iterator for self::$parameters
     */
    public function getIterator()
    {
        return $this->parameters->getIterator();
    }
}

?>
