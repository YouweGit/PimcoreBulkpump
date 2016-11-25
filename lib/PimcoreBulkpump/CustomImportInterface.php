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
     * @param \CsvDataMapper_Import $importObject
     * @return mixed
     */
    public function import(&$object, array $row, \CsvDataMapper_Import $importObject);

    public function setProfile($profile);
}