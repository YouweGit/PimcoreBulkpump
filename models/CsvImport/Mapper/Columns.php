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
class CsvImport_Mapper_Columns extends CsvImport_Abstract_Mapper
{
    public function getDbClassName()
    {
        return 'CsvImport_DbTable_Columns';
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteByProfileId($id)
    {
        $this->init();
        $where = $this->table->getAdapter()->quoteInto('profile_id = ?', $id);
        $ret = $this->table->delete($where);
        if ($ret > 0) {
            return true;
        } else {
            return false;
        }
    }
}