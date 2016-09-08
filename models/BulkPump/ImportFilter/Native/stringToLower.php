<?php

namespace BulkPump\ImportFilter\Native;

class stringToLower extends \BulkPump\ImportFilter\Base
{
    var $name           = 'String to lowercase';
    var $description    = 'Converts a string to lower case';
    var $parameters     =
        [
            [
                'id'            =>  'only_first',
                'name'          =>  'Only the first character',
                'type'          =>  'boolean',
                'default'       =>  false
            ]
        ];

    function doFilter($value, $language, $only_first)
    {
        if($only_first)
        {
            return lcfirst($this->value);
        }
        return strtolower($this->value);
    }


}