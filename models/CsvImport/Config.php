<?php
/**
 * Config.php
 *
 * Model class for the configuration of the csv import
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 11/30/15
 *
 */
class CsvImport_Config extends CsvImport_Abstract_Model
{

    /**
     * @param $values
     * @return CsvImport_Config
     * @throws Exception
     */
    public static function create($values)
    {
        $configMapper = new CsvImport_Mapper_Config();
        $configMapper->init();
        foreach ($values as $i => $row) {
            try{
                $result =  $configMapper->create($row);
            } catch(Exception $e) {
               throw $e;
            }
            $values[$i]['id'] = $result;
        }
        $config = new self();
        $config->setData($values);
        return $config;
    }

    /**
     * @param $id
     * @return CsvImport_Config
     */
    public static function getByProfileId($id)
    {
        $configMapper = new CsvImport_Mapper_Config();
        $configMapper->init();
        $row = $configMapper->getByProfileId($id);
        $values = $row->toArray();

        $config = new self();
        foreach($values as $key => $value) {
            foreach($value as $fieldkey => &$fieldvalue)
            {
                if($fieldkey == 'overwrite_empty') // boolean field -> should be boolean!
                {
                    $fieldvalue = intval($fieldvalue);
                }
            }
            $config->_data[$key] = $value;
        }
        return $config;
    }

    /**
     * @param $id
     * @return CsvImport_Config
     */
    public static function getById($id)
    {
        $configMapper = new CsvImport_Mapper_Config();
        $configMapper->init();
        $row = $configMapper->getById($id);
        $values = $row->toArray();

        $config = new self();
//        foreach($values as $key => $value) {
            foreach($values as $fieldkey => &$fieldvalue)
            {
                if($fieldkey == 'overwrite_empty') // boolean field -> should be boolean!
                {
                    $fieldvalue = intval($fieldvalue);
                }
            }
            $config->_data[$values['id']] = $values;
//        }
        return $config;
    }


    /**
     * Delete the config for a profile
     */
    public function deleteByProfileId($id)
    {
        $configMapper = new CsvImport_Mapper_Config();
        $this->setData(array());
        return $configMapper->deleteByProfileId($id);
    }


}