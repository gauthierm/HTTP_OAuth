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
require_once 'HTTP/OAuth/Parameter.php';

/**
 * HTTP_OAuth_ParameterList
 *
 * List of OAuth parameters. This contains specification parameters handling
 * and ArrayAccess, Countable, and IteratorAggregate features.
 *
 * @category  HTTP
 * @package   HTTP_OAuth
 * @author    Jeff Hodsdon <jeffhodsdon@gmail.com>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 Jeff Hodsdon <jeffhodsdon@gmail.com>, 2013 silverorange Inc.
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://pear.php.net/package/HTTP_OAuth
 */
class HTTP_OAuth_ParameterList extends HTTP_OAuth
    implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Array of HTTP_OAuth_Parameter objects
     *
     * @var array $parameters Parameters
     */
    protected $parameters = array();

    /**
     * Gets OAuth specific parameters from this list
     *
     * @return HTTP_OAuth_ParameterList OAuth specific parameters.
     */
    public function getOAuthOnly()
    {
        $list = new HTTP_OAuth_ParameterList();

        foreach ($this->parameters as $parameter) {
            if ($parameter->isOAuth()) {
                $list->add($parameter);
            }
        }

        return $list->sort();
    }

    /**
     * Sorts this list
     *
     * @return HTTP_OAuth_ParameterList the current object, for fluent
     *                                  interface.
     */
    public function sort()
    {
        usort($this->parameters, array('HTTP_OAuth_Parameter', 'compare'));
        return $this;
    }

    /**
     * Sets multiple parameters in this list
     *
     * OAuth specific parameters can optionally exclude the 'oauth_' prefix
     * in their name and will be automatically prefixed.
     *
     * @param array $params a name-value indexed array of parameter values.
     *
     * @return HTTP_OAuth_ParameterList the current object, for fluent
     *                                  interface.
     */
    public function setMulti(array $params)
    {
        foreach ($params as $name => $value) {
            $name = HTTP_OAuth_Parameter::getPrefixedName($name);
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Adds a parameter to this list
     *
     * @param mixed  $name  either a parameter name or a
     *                      {@link HTTP_OAuth_Parameter} object.
     * @param string $value the parameter value to use if <i>$name</i> is a
     *                      string.
     *
     * @return HTTP_OAuth_ParameterList the current object, for fluent
     *                                  interface.
     */
    public function add($name, $value = '')
    {
        if (!$name instanceof HTTP_OAuth_Parameter) {
            $name = new HTTP_OAuthParameter($name, $value);
        }

        $this->parameters[] = $name;

        return $this;
    }

    /**
     * Gets the value of the first paramter in this list by its name
     *
     * OAuth values can be referenced without the leading 'oauth_' if no
     * parameters exist in this list with the same name.
     *
     * @param string $name the name of the parameter value to get.
     *
     * @return mixed the first parameter value if exists in this list, else
     *               null.
     */
    public function __get($name)
    {
        $value = null;

        // check if un-prefixed parameter exists first
        if ($value === null) {
            $param = $this->getFirstByName($name);
            if ($param instanceof HTTP_OAuth_Parameter) {
                $value = $param->getValue();
            }
        }

        // then check if prefixed parameter exists
        if ($value === null) {
            $param = $this->getFirstByName(
                HTTP_OAuth_Parameter::getPrefixedName($name)
            );
            if ($param instanceof HTTP_OAuth_Parameter) {
                $value = $param->getValue();
            }
        }

        return $value;
    }

    /**
     * Sets a parameter value in this list
     *
     * If no parameter exists in this list with the specified name, one
     * is created and added. OAuth values can be referenced without the leading
     * 'oauth_' if no parameters exist in this list with the same name.
     *
     * @param string $name  the name of the parameter.
     * @param mixed  $value the value of the parameter.
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $param = $this->$name;
        if ($param instanceof HTTP_OAuth_Parameter) {
            $param->setValue($value);
        } else {
            $this->add($name, $value);
        }
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
        return (isset($this->$offset));
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
        return $this->$offset;
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
        $this->$offset = $value;
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
        $this->removeByName(HTTP_OAuth::getPrefixedName($offset));
    }

    /**
     * Removes parameters from this list by name
     *
     * @param string $name the name to remove.
     *
     * @return HTTP_OAuth_ParameterList the list of removed parameters.
     */
    public function removeByName($name)
    {
        $removed = new HTTP_OAuth_ParameterList();

        $total = count($this->parameters);

        for ($i = 0; $i < $total; $i++) {
            if ($this->parameters[$i]->getName() === $name) {
                $removed->add($this->parameters[$i]);
                array_splice($this->parameters, $i, 1);
                $i--;
                $total--;
            }
        }

        return $removed->sort();
    }

    /**
     * Gets parameters from this list by name
     *
     * @param string $name the name to get.
     *
     * @return HTTP_OAuth_ParameterList the parameters with the specified
     *                                  name.
     */
    public function getByName($name)
    {
        $list = new HTTP_OAuth_ParameterList();

        foreach ($this->parameters as $parameter) {
            if ($parameter->getName() === $name) {
                $list->add($parameter);
            }
        }

        return $list->sort();
    }

    /**
     * Gets the first parameter in this list with the specified name
     *
     * @param string $name the name to get.
     *
     * @return HTTP_OAuth_Parameter or null if no such parameter exists in
     *                              this list.
     */
    public function getFirstByName($name)
    {
        $param = null;

        $params = $this->getByName($name);
        if (count($params) > 0) {
            $param = $params->parameters[0];
        }

        return $param;
    }

    /**
     * Gets the num,ber of parameters in this list
     *
     * Fufills the Coutnable interface.
     *
     * @return integer the number of parameters in this list.
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * Gets an iterator for this list
     *
     * Fufills the IteratorAggregate interface.
     *
     * @return Iterator an iterator for this list.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }
}

?>
