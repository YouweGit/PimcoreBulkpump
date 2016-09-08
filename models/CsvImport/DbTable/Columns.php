<?php
/**
 * Columns.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/2/15
 *
 */
/**
 * Class CsvProductImporter_DbTable_FileConfig
 */
class CsvImport_DbTable_Columns extends Zend_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_name = "csv_importer_columns";
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