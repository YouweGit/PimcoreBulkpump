<?php


namespace PimcoreBulkpump;


interface CustomImportInterface {
    /**
     * @param $config
     */
    public function __construct($config);

    /**
     * @param $object
     * @param array $row
     * @return mixed
     */
    public function import(&$object, array $row);

}