<?php
/**
 * Mapper.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/9/15
 *
 */
interface CsvDataMapper_Interface_Data_Mapper
{
    /**
     * Should set the mapping from the specific source
     *
     * @param $config
     * @return null
     */
    static public function init($config);

}