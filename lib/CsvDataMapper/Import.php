<?php

/**
 * Class CsvImport_Abstract_Import
 */
class CsvDataMapper_Import extends CsvDataMapper_Abstract_Import
{
    /** @var array Data store for saving row data over multiple rows */
    private $_store = array();

    public static function init($config)
    {
        // disable versioning
        \Pimcore\Model\Version::disable();
        $import = new self();
        $import ->setConfig($config);
        return $import;
    }

    /**
     * @param       array           $config
     *                                  - filename : name of the file that is being imported
     * @throws      Exception
     * @return      bool            If has failures will return false
     */
    public static function run($config)
    {

        $import = self::init($config);
        try {
            $import->_import();
        } catch (Exception $e) {
            throw new Exception($e);
        }
        return true;
    }

    /**
     * Retrieve data from the store
     *
     * @param string $key Key where it should be stored
     *
     * @return mixed|null Returned data
     */
    public function getDataStoreAttribute(string $key)
    {
        if (isset($this->_store[$key])) {
            return $this->_store[$key];
        }

        return null;
    }

    /**
     * Save the data in the store
     *
     * @param string $key   The key where data should be stored
     * @param mixed  $value Data be stored in the object
     */
    public function setDataStoreAttribute(string $key, $value)
    {
        $this->_store[$key] = $value;
    }


}

