<?php
/**
 * Object.php
 *
 * Mappers are ordered by source type from which they map
 *
 * Profile,
 * Xml,
 * enz
 *
 * CsvDataMapper/Data/Mapper/object.php
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/9/15
 *
 */
class CsvDataMapper_Data_Mapper_Profile extends CsvDataMapper_Abstract_Data_Mapper
{
    /**
     * @return string
     */
    public function getClassType()
    {
        return 'Profile';
    }

    /**
     * @param       $config
     * @throws      Exception
     * @internal    param string $filename
     * @return      $this
     */
    public static function init($config)
    {
        if (!isset($config['profileId'])) {
            throw new Exception("profileId needed in config for profile mapping");
        }
        $profileId  = $config['profileId'];
        $dataMapper = new self();
        $mapping    = array();
        $config     = CsvImport_Config::getByProfileId($profileId);
        foreach($config->getData() as $row) {
            $mapping[]  = array(
                CsvDataMapper_Abstract_Data_Mapper::MAPPING_SOURCE      => $row['csv_field'],
                CsvDataMapper_Abstract_Data_Mapper::MAPPING_TARGET      => $row['pimcore_field'],
                CsvDataMapper_Abstract_Data_Mapper::MAPPING_LANGUAGE    => $row['language'],
                CsvDataMapper_Abstract_Data_Mapper::MAPPING_FIELDTYPE   => $row['fieldtype'],
                CsvDataMapper_Abstract_Data_Mapper::MAPPING_FILTERS     => $row['filters']
            );
        }
        $dataMapper->setMapping($mapping);
        return $dataMapper;
    }
}