<?php

namespace BulkPump\ImportFilter\Native;

use Pimcore\Model\Object\AbstractObject;

class objectIdToObject extends \BulkPump\ImportFilter\Base
{
    var $name           = 'Object ID to object';
    var $description    = 'Expects an object ID, will return an Object (for use in HREF/MultiHREF fields for example)';
    var $parameters     =
        [
            [
                'id'            =>  'multiple',
                'name'          =>  'Multiple',
                'type'          =>  'boolean',
                'default'       =>  false
            ],
            [
                'id'            =>  'separator',
                'name'          =>  'Separator',
                'type'          =>  'string',
                'default'       =>  ';'
            ],
        ];

    function doFilter($value, $language, $multiple, $separator)
    {
        $objects = array();
        $ids = explode($separator, $value);
        foreach ($ids as $id) {
            $list = new AbstractObject\Listing();
            $list->addConditionParam('o_id = ?', $id);
            $obj = $list->current();
            if (!$obj) {
                throw new \Exception('Object with id ' . $id . ' not found');
            }
            /* @var $cat \Pimcore\Model\Object\Category */
            $objects[] = $obj;
        }

        if(!$multiple) {
            if(count($objects) > 0) {
                $objects = $objects->current();
            }
            else {
                $objects = false;
            }
        }

        return $objects;
    }


}