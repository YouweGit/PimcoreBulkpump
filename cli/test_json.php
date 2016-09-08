<?php


$a =  [
    [
        'id'        =>  '123123',
        'sort_order' =>  '1',
        'class'        =>  'BulkPump\\ImportFilter\\Native\\stringToUpper',
        'params'    =>
            [
                [
                    'id'            =>  'only_first',
                    'value'         =>  false
                ]
            ]
    ],
    [
        'id'        =>  '5555',
        'sort_order' =>  '5',
        'class'        =>  'BulkPump\\ImportFilter\\Native\\stringToLower',
        'params'    =>
            [
                [
                    'id'            =>  'only_first',
                    'value'         =>  false
                ]
            ]
    ],
    [
        'id'        =>  '090909',
        'sort_order' =>  '4',
        'class'        =>  'BulkPump\\ImportFilter\\Native\\productIdToProduct',
    ],
    [
        'id'        =>  'iu23h4',
        'sort_order' =>  '3',
        'class'        =>  'BulkPump\\ImportFilter\\Native\\objectIdToObject',
        'params'    =>
            [
                [
                    'id'            =>  'multiple',
                    'value'         =>  false
                ],
                [
                    'id'            =>  'separator',
                    'value'         =>  ';'
                ],
            ]
    ],
    [
        'id'        =>  'afsvcdsv2',
        'sort_order' =>  '2',
        'class'        =>  'BulkPump\\ImportFilter\\Native\\debugFilter',
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
]
;

echo json_encode($a);