<?php

namespace BulkPump\ImportFilter\Native;

class debugFilter extends \BulkPump\ImportFilter\Base
{
    var $enabled         = false;  // enable for debugging purpose
    var $name           = 'Debug filter to test params';
    var $description    = 'Demo debug filter, to demonstrate all different kinds of possible parameters';
    var $parameters     =
        [
            [
                'id'            =>  'SomeBoolean',
                'name'          =>  'Some Boolean',
                'type'          =>  'boolean',
                'default'       =>  false
            ],
            [
                'id'            =>  'SomeString',
                'name'          =>  'Some String',
                'type'          =>  'string',
                'default'       =>  'hello'
            ],
            [
                'id'            =>  'SomeInteger',
                'name'          =>  'Some Integer',
                'type'          =>  'integer',
                'default'       =>  null
            ],
            [
                'id'            =>  'SomeSelect',
                'name'          =>  'Some Select',
                'type'          =>  'select',
                'default'       =>  'maximum',
                'options'       =>  ['minimum', 'average', 'maximum']
            ],

        ];

    function doFilter($value, $language, $SomeBoolean, $SomeString, $SomeInteger, $SomeSelect)
    {
        return $this->value;
    }


}