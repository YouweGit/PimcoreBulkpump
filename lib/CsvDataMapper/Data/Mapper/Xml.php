<?php
/**
 * Xml.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/10/15
 *
 */
class CsvDataMapper_Data_Mapper_Xml extends CsvDataMapper_Abstract_Data_Mapper
{
    /**
     * @return string
     */
    public function getClassType()
    {
        return 'Xml';
    }

    /**
     * @param $config
     * @throws Exception
     * @internal param string $filename
     * @return $this
     */
    public static function init($config)
    {
        $dataMapper = new self();

        if (empty($filename)) {
            throw new Exception('Expect a file name '.PHP_EOL);
        }

        return $dataMapper;
    }
}