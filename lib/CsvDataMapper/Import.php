<?php

/**
 * Class CsvImport_Abstract_Import
 * @required \PathManager_PathManager
 */
class CsvDataMapper_Import extends CsvDataMapper_Abstract_Import
{


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


}

