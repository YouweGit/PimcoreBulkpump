<?php

/**
 * Mapper.php
 *
 * Data mapper pattern
 *
 * @category Youwe Development
 * @package  intratuin-pimcore
 * @author   Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date     12/2/15
 *
 */
abstract class CsvImport_Abstract_Mapper
{
    abstract function getDbClassName();

    /**
     * @var CsvImport_DbTable_Profile|CsvImport_DbTable_Config
     */
    protected $table;

    /**
     *
     */
    public function init()
    {
        $pimDb = \Pimcore\Resource\Mysql::get();
        Zend_Db_Table::setDefaultAdapter($pimDb->getResource());
        $dbClassName = $this->getDbClassName();
        $this->table = new $dbClassName();

        return $this;
    }

    /**
     * @param array $values
     *
     * @return int|null
     *
     */
    public function create(array $values)
    {
        $this->init();
        return $this->table->insert($values);
    }

    /**
     * @return mixed
     */
    public function read()
    {
        $this->init();
        $rows = $this->table->fetchAll();

        return $rows;
    }

    /**
     * @param int|string $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $this->init();
        $where = $this->table->getAdapter()->quoteInto('id = ?', $id);
        $ret = $this->table->delete($where);
        if ($ret > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int|string $profileId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getByProfileId($profileId, $query = null)
    {
        $this->init();
        $where = $this->table->getAdapter()->quoteInto('profile_id = ?', $profileId);
        if($query) {
            $where .= ' AND ' . $this->table->getAdapter()->quoteInto("csv_field LIKE ?", '%'.$query.'%');
        }
        $rows = $this->table->fetchAll($where);
        return $rows;
    }

    /**
     * @param int|string $id
     * @param array $values
     * @return int
     */
    public function update($id, array $values)
    {
        $this->init();
        $where = $this->table->getAdapter()->quoteInto('id = ?', $id);
        return $this->table->update($values, $where);
    }

    /**
     * @param string|int $id
     *
     * @return null|Zend_Db_Table_Row
     */
    public function getById($id)
    {
        $this->init();
        $where = $this->table->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->table->fetchRow($where);

        return $row;
    }
}