<?php

/**
 * Class CsvProductImporter_DbTable_FileConfig
 */
class CsvImport_DbTable_Config extends Zend_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_name = "csv_importer_config";
    /**
     * @var string
     */
    protected $_primary = "id";
    /**
     * @var array
     */
    protected $_referenceMap = array(
        'CsvProductImporter_DbTable_Profile' => array(
            'columns'       => 'profile_id',
            'refTableClass' => 'CsvProductImporter_DbTable_Profile',
            'refColumns'    => 'id',
            'onDelete'      => self::CASCADE,
            'onUpdate'      => self::CASCADE
        )
    );
}