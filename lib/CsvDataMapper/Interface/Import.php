<?php
/**
 * Import.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/9/15
 *
 */
interface CsvDataMapper_Interface_Import
{
    /**
     * @param $config
     * @return bool true if import successful
     */
    public static function run($config);

    /**
     * @param $config
     * @return mixed
     */
    public static function init($config);
}