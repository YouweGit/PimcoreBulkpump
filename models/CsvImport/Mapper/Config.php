<?php
/**
 * ConfigMapper.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 11/2/15
 *
 */

class CsvImport_Mapper_Config extends CsvImport_Abstract_Mapper
{
    public function getDbClassName()
    {
        return 'CsvImport_DbTable_Config';
    }
    // custom stuff

    /**
     * @param $id
     * @return bool
     */
    public function deleteByProfileId ($id)
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