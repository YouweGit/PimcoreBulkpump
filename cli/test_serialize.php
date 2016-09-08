<?php

$workingDirectory = getcwd();
chdir(__DIR__);
include_once("../../../pimcore/cli/startup.php");

//execute in admin mode
define("PIMCORE_ADMIN", true);

$filters_array =
    [
        [
            'id'        =>  'BulkPump\\ImportFilter\\Native\\stringToUpper',
            'params'    =>
                [
                    [
                        'id'            =>  'only_first',
                        'value'         =>  false
                    ]
                ]
        ],
        [
            'id'        =>  'BulkPump\\ImportFilter\\Native\\stringToLower',
            'params'    =>
                [
                    [
                        'id'            =>  'only_first',
                        'value'         =>  false
                    ]
                ]
        ],
//        [
//            'id'        =>  'BulkPump\\ImportFilter\\Native\\productIdToProduct',
//        ],
//        [
//            'id'        =>  'BulkPump\\ImportFilter\\Native\\objectIdToObject',
//            'params'    =>
//                [
//                    [
//                        'id'            =>  'multiple',
//                        'value'         =>  false
//                    ],
//                    [
//                        'id'            =>  'separator',
//                        'value'         =>  ';'
//                    ],
//                ]
//        ],
        [
            'id'        =>  'BulkPump\\ImportFilter\\Native\\debugFilter',
            'params'    =>
                [
                    [
                        'id'            =>  'SomeBoolean',
                        'value'         =>  false
                    ],
                    [
                        'id'            =>  'SomeString',
                        'value'         =>  'hello'
                    ],
                    [
                        'id'            =>  'SomeInteger',
                        'value'         =>  null
                    ],
                    [
                        'id'            =>  'SomeSelect',
                        'value'         =>  'maximum',
                    ],

                ]
        ]
    ];

$serialized = serialize($filters_array);

echo "\n\n\n";
echo $serialized;
echo "\n\n\n";





