<?php

namespace BulkPump\ImportFilter\Native;

class stringToUpper extends \BulkPump\ImportFilter\Base
{
    var $name           = 'String to uppercase';
    var $description    = 'Converts a string to UPPER case';
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
            return ucfirst($this->value);
        }
        return strtoupper($this->value);
    }


}