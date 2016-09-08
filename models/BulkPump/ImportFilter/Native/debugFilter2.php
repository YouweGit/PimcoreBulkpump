<?php

namespace BulkPump\ImportFilter\Native;

class debugFilter2 extends \BulkPump\ImportFilter\Base
{
    var $enabled        = false;  // just for debugging
    var $name           = 'Debug filter 2';
    var $description    = 'Debug filter 2 to test multiple params of the same type';
    var $parameters     =
        [
            [
                'id'            =>  'SomeBoolean',
                'name'          =>  'Some Boolean',
                'type'          =>  'boolean',
                'default'       =>  false
            ],
            [
                'id'            =>  'AnotherBoolean',
                'name'          =>  'Another Boolean',
                'type'          =>  'boolean',
                'default'       =>  false
            ],
            [
                'id'            =>  'ThirdBoolean',
                'name'          =>  'Third Boolean',
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
                'id'            =>  'SecondString',
                'name'          =>  'Second String',
                'type'          =>  'string',
                'default'       =>  'hello two'
            ],
            [
                'id'            =>  'ThirdString',
                'name'          =>  'Third String',
                'type'          =>  'string',
                'default'       =>  'hello three'
            ],
            [
                'id'            =>  'SomeInteger',
                'name'          =>  'Some Integer',
                'type'          =>  'integer',
                'default'       =>  null
            ],
            [
                'id'            =>  'SecondInteger',
                'name'          =>  'Second Integer',
                'type'          =>  'integer',
                'default'       =>  null
            ],
            [
                'id'            =>  'ThirdInteger',
                'name'          =>  'Third Integer',
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
            [
                'id'            =>  'SecondSelect',
                'name'          =>  'Second Select',
                'type'          =>  'select',
                'default'       =>  'meat',
                'options'       =>  ['fruit', 'vegetables', 'meat', 'potatoes', 'broccoli']
            ],
            [
                'id'            =>  'ThirdSelect',
                'name'          =>  'Third Select',
                'type'          =>  'select',
                'default'       =>  'blue',
                'options'       =>  ['blue', 'brown', 'green', 'gray']
            ],

        ];

    function doFilter($value, $language, $SomeBoolean, $SomeString, $SomeInteger, $SomeSelect)
    {
        return $this->value;
    }


}