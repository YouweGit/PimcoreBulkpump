<?php
/**
 * ProfileMapper.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 11/2/15
 *
 */

class CsvImport_Mapper_Profile extends CsvImport_Abstract_Mapper
{

    /**
     * @return string
     */
    public function getDbClassName()
    {
        return 'CsvImport_DbTable_Profile';
    }

    /**
     * @param array $values
     * @return int|null
     */
    public function create(array $values)
    {
        $now =  new Zend_Date();
        $nowFormatted = $now->toString(\Pimcore\Date::MYSQL_DATETIME);
        $values['creationDate'] = $nowFormatted;
        $values['modificationDate'] = $nowFormatted;

        return parent::create($values);
    }

    /**
     * @param int|string $id
     * @param array $values
     * @return int
     */
    public function update($id, array $values)
    {
        //Don't change the creation date
        if (array_key_exists('creationDate', $values)) {
            unset($values['creationDate']);
        }
        // Update the modification date
        $now =  new Zend_Date();
        $values['modificationDate'] = $now->toString(\Pimcore\Date::MYSQL_DATETIME);


        return parent::update($id, $values);
    }
}