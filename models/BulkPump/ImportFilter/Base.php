<?php

namespace BulkPump\ImportFilter;

abstract class Base
{
    var $enabled = true;
    var $name = null;
    var $description = null;
    var $parameters = array();

    var $value = null;
    var $language = null;
    var $value_is_set = false;
    var $parameter_values = array();

    public function __construct()
    {
        // init the defaults for the parameter values
        foreach ($this->parameters as &$param)
        {
            $this->parameter_values[$param['id']] = $param['default'];
        }
    }

    function getClass()
    {
        $reflect = new \ReflectionClass($this);
        return $reflect->getName();
    }

    function isEnabled() {
        return $this->enabled;
    }
    
    function getName()
    {
        return $this->name;
    }

    function getDescription()
    {
        return $this->description;
    }

    function setValue($value)
    {
        $this->value_is_set = true;
        $this->value = $value;
    }

    function setLanguage($lang)
    {
        $this->language = $lang;
    }

    function filter()
    {
        if(!$this->value_is_set)
        {
            throw new \Exception('Call to filter method, without setting the value to be filtered first');
        }
        $arguments = [];
        $arguments[] = $this->value;        // first parameter = value
        $arguments[] = $this->language;     // second parameter = language
        foreach($this->parameter_values as &$pv)    // rest of parameters = options
        {
            $arguments[] = $pv;
        }
        $reflex = new \ReflectionMethod($this, 'doFilter');
        return $reflex->invokeArgs($this, $arguments);
    }

    function getParameters()
    {
        return $this->parameters;
    }

    function getParameterValues()
    {
        return $this->parameter_values;
    }

    function getParameterValue($param_name)
    {
        if (array_key_exists($param_name, $this->parameter_values))
        {
            return $this->parameter_values[$param_name];
        }
        else
        {
            throw new \Exception('Trying to get a parameter which is not part of the filter');
        }
    }

    function setParameterValue($param_name, $value)
    {
        if (array_key_exists($param_name, $this->parameter_values))
        {
            $this->parameter_values[$param_name] = $value;
        }
        else
        {
            throw new \Exception('Trying to set a parameter which is not part of the filter');
        }
    }

}