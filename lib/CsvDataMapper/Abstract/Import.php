<?php
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object;
use Pimcore\File;
use Pimcore\Logger;

/**
 * Import.php
 *
 * @depends  CsvImport_Object
 *
 * @category Youwe Development
 * @package  intratuin-pimcore
 * @author   Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date     12/9/15
 *
 */
abstract class CsvDataMapper_Abstract_Import implements CsvDataMapper_Interface_Import {
    const IMPORT_TYPE_OBJECT = "object";
    const IMPORT_TYPE_ATTRIBUTES = "attributes";
    const IMPORT_TYPE_CUSTOM_CLASS = "custom";
    /**
     * @var CsvDataMapper_Abstract_Data_Mapper
     */
    protected $_dataMapper;

    /**
     * @var
     */
    protected $_config;

    /**
     * @param \CsvDataMapper_Abstract_Data_Mapper $dataMapper
     */
    public function setDataMapper($dataMapper) {
        $this->_dataMapper = $dataMapper;
    }

    /**
     * @return \CsvDataMapper_Abstract_Data_Mapper
     */
    public function getDataMapper() {
        return $this->_dataMapper;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config) {
        $this->_config = $config;
    }

    /**
     * @return mixed
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * Check for a config field or throw exception
     *
     * @param string $field
     *
     * @return string
     * @throws Exception
     */
    public function checkConfigField($field) {
        $config = $this->getConfig();
        if (!isset($config[$field])) {
            throw new Exception("`$field` needed in configuration");
        }

        return $config[$field];
    }

    /**
     * @param $config
     *
     * @throws Exception
     * @internal param $dataType
     * @return \CsvDataMapper_Abstract_Data_Mapper /CsvDataMapper_Abstract_Data_Mapper
     */
    protected function _initDataMapper($config) {

        $mappingType = $this->checkConfigField('mappingType');
        $filename = $this->checkConfigField('filename');

        $dataMapperName = 'CsvDataMapper_Data_Mapper_' . ucfirst($mappingType);

         /** @var CsvDataMapper_Abstract_Data_Mapper $dataMapper */
        $dataMapper = $dataMapperName::init($config);

        // handle data source
        /** $file todo delegate to source class  */
        $file = CsvImport_File::init($filename);
        $dataMapper->setFile($file);
        $this->setDataMapper($dataMapper);

        return $dataMapper;
    }

    /**
     * @return null
     */
    protected function _import() {
        $config = $this->getConfig();
        $importType = $this->checkConfigField('importType');

        /** @var array $config */
        /** Here we start reading the config of the importer */
        /** @var CsvDataMapper_Abstract_Data_Mapper $dataMapper */
        $dataMapper = $this->_initDataMapper($config);
        foreach ($dataMapper->getObjects() as $dataObject) {
            $dataMapper->setContextObject($dataObject);
            switch ($importType) {
                case self::IMPORT_TYPE_OBJECT:
                case self::IMPORT_TYPE_CUSTOM_CLASS:
                    self::importObject();
                    break;
                case self::IMPORT_TYPE_ATTRIBUTES :
                    self::importAttributes();
                    break;
            }
        }
    }

    /**
     * @throws Exception
     * @internal param $config
     */
    protected function importObject() {
        $className = $this->checkConfigField('classname');
        $path = $this->checkConfigField('path');
        $storeAsVariant = $this->checkConfigField('storeAsVariant');
        $objectClassName = CsvImport_Object::OBJECT_PREFIX . ucfirst($className);
        $parent = Pimcore\Model\Object\Service::createFolderByPath($path);

        $dataMapper = $this->getDataMapper();
        $keyMap = $dataMapper->getMappingSource('o_key');
        $key = $dataMapper->mapTarget($keyMap);
        if (!$key) {
            throw new Exception('Could not find a valid key in data mapping');
        }

        // check if we have this object, not create it
        try {
            $object = CsvImport_Object::getOrCreateObject($key, $objectClassName, $parent, $storeAsVariant);
        } catch (Exception $e) {
            throw new Exception("Could not save object : " . $e->getMessage());
        }

        // map the values to the object
        /** @var Object\Concrete $object */
        $object = $dataMapper->mapObject($object);

        $importType = $this->checkConfigField('importType');

        if($importType === self::IMPORT_TYPE_CUSTOM_CLASS){
            $this->importCustomClass($object, $this);
        }

        $this->setPublishedValue($object);

        CsvDataMapper_Data_Store::saveObject($object);
        Logger::info("Imported object '{$object->getId()}'");
    }

    /**
     * @param $object
     * @param CsvDataMapper_Import $importObject
     * @throws Exception
     */
    protected function importCustomClass(&$object, \CsvDataMapper_Import $importObject) {
        $class = $this->checkConfigField('customClass');

        if (class_exists($class) === false) {
            throw new Exception('Importer class not found!');
        }
        /** @var \BulkPump\CustomImportInterface $importer */
        $importer = new $class($this->getConfig());
        if (!$importer instanceof \PimcoreBulkpump\CustomImportInterface) {
            throw new Exception('Importer class has to implement "\BulkPump\CustomImportInterface"!');
        }

        $importer->import($object, (array)$this->getDataMapper()->getContextObject(), $importObject);
    }
    /**
     * Import classification attributes
     * @throws Exception
     */
    protected function importAttributes() {

        $className = $this->checkConfigField('classname');
        $attributesStartAfter = $this->checkConfigField('attributesStartAfter');
        $classificationStoreField = $this->checkConfigField('classificationStoreField');
        $classificationField = $this->checkConfigField('classificationField');
        $attributeLanguageSeparator = $this->checkConfigField('attributeLanguageSeparator');
        $fullClassName = 'Pimcore\\Model\\Object\\' . ucfirst($className);
        $fullClassNameListing = $fullClassName . '\\Listing';
        if (strlen($className) === 0 || class_exists($fullClassName) === false || class_exists($fullClassNameListing) === false) {
            throw new \Exception("Class not found '$fullClassName' or '$fullClassNameListing'");
        }

        $dataMapper = $this->getDataMapper();
        $keyMap = $dataMapper->getMappingSource('o_key');
        $key = $dataMapper->mapTarget($keyMap);

        if (!$key || (string)strlen($key) === 0) {
            throw new Exception('Current row does not contain a key, importing cannot continue');
        }
        $object = $this->getObjectOrVariantByKey($fullClassName, $fullClassNameListing, $key);
        $object = $dataMapper->mapAttributes($object, $attributesStartAfter, $classificationStoreField, $classificationField, $attributeLanguageSeparator);
        CsvDataMapper_Data_Store::saveObject($object);

        Logger::info("Imported attributes for object '{$object->getId()}'");
    }



    /**
     * Check if the object should be published, unpublished, or no-change:
     *
     * @param Object\Concrete $object
     *
     * @return bool
     */
    private function setPublishedValue($object) {
        $dataMapper = $this->getDataMapper();
        $keyMap = $dataMapper->getMappingSource('o_published');
        // there is no field mapped to is_published
        // so default = publish it
        if (!$keyMap) {
            $object->setPublished(true);

            return true; // Nothing to do here anymore
        }
        $value = $dataMapper->mapTarget($keyMap);

        $value = trim((string)$value);

        $valueIsEmpty = $value === '' || strtolower($value) === 'null';
        // Object is new and there is no value specified
        if ($valueIsEmpty && !$object->getId()) {
            $object->setPublished(true);

            return true;

        } // Object exists already but since the value is empty we leave the status unchanged
        else if ($valueIsEmpty) {
            return true;
        }
        // update with boolean value from column
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $object->setPublished($value);

        return true;
    }

    /**
     * @param string $fullClassName
     * @param string $fullClassNameListing
     * @param string $key
     * @return AbstractObject
     * @throws Exception
     */
    protected function getObjectOrVariantByKey($fullClassName, $fullClassNameListing, $key) {
        /** @var \Pimcore\Model\Object\Listing $listingObj */
        $listingObj = new $fullClassNameListing();
        $key = File::getValidFilename($key);
        $listingObj->setCondition('o_key = ?', $key);
        $listingObj->setObjectTypes(array(AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT));
        $listingObj->setUnpublished(true);
        /** @var AbstractObject $object */
        $object = $listingObj->current();

        if (!$object || is_a($object, $fullClassName) === false) {
            throw new Exception(sprintf('Couldn\'t find a valid "%s" with o_key %s', $fullClassName, $key));
        }
        return $object;
    }
}
