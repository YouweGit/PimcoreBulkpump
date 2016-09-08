<?php

namespace BulkPump\ImportFilter\Native;

class productIdToProduct extends \BulkPump\ImportFilter\Base
{
    var $name           = 'Product ID to product';
    var $description    = 'Expects a product ID, will return a Product object (for use in HREF fields for example)';
    var $parameters     = [];

    function doFilter($value, $language)
    {
        $list = new \Pimcore\Model\Object\Product\Listing();
        $list->addConditionParam('o_id = ?', $value);
        $prod = $list->current();
        if (!$prod) {
            throw new \Exception('Product with id ' . $value . ' not found');
        }

        return $prod;
    }

}