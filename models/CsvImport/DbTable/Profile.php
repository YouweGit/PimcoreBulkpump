<?php

/**
 * Class CsvProductImporter_DbTable_Profile
 */
class CsvImport_DbTable_Profile extends Zend_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_name    = "csv_importer_profile";

    /**
     * @var string
     */
    protected $_primary = "id";

    /**
     * @var array
     */
    protected $_dependentTables = array('CsvImport_DbTable_Config');
}