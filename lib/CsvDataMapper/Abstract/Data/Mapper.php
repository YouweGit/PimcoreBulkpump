<?php

use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\Classificationstore\CollectionConfig;
use Pimcore\Model\Object\Classificationstore\GroupConfig;
use Pimcore\Model\Object\Classificationstore\KeyConfig;
use Pimcore\Model\Object\Product;
use Pimcore\Model\Object\Classificationstore;
use Pimcore\Model\Object;
use Pimcore\Model\Object\Classificationstore\CollectionGroupRelation;

use Pimcore\Model;
use Pimcore\Tool;

abstract class CsvDataMapper_Abstract_Data_Mapper implements CsvDataMapper_Interface_Data_Mapper {

    /**
     * internal use of Field types for the mapping
     */
    const MAPPING_SOURCE        = 'source';
    const MAPPING_TARGET        = 'target';
    const MAPPING_LANGUAGE      = 'language';
    const MAPPING_FIELDTYPE     = 'fieldtype';
    const MAPPING_FILTERS       = 'filters';
    const MAPPING_OVERWRITE_EMPTY       = 'overwrite_empty';

    /**
     * List of query => field configs
     * @var array
     */
    protected $_mapping = array(
        'o_key' => 'product_id'
    );

    /**
     * @var CsvImport_FIle
     */
    protected $_file;

    /**
     * @var $_contextObject
     */
    protected $_contextObject;

    /**
     * @param array $mapping
     *
     * @return $this
     */
    public function setMapping($mapping) {
        $this->_mapping = $mapping;

        return $this;
    }

    /**
     * @return array
     */
    public function getMapping() {
        return $this->_mapping;
    }

    /**
     * @param \CsvImport_FIle $file
     *
     * @throws Exception
     */
    public function setFile($file) {
        if (!$file instanceof CsvImport_File) {
            throw new Exception('import file not specified' . PHP_EOL);
        }
        $this->_file = $file;
    }

    /**
     * @return \CsvImport_FIle
     */
    public function getFile() {
        return $this->_file;
    }

    /**
     * Defined abstract
     */
    abstract public function getClassType();

    /**
     *
     */
    public function getObjects() {
        /* @todo abstract for all data types, xml, csv, etc */
        return $this->getfile()->getData();

    }

    /**
     * data context object
     */
    public function setContextObject($contextObject) {
        /** @todo Abstract file data lyer for all types,xml csv, etc */
        $this->_contextObject = (object)$contextObject;
    }

    /**
     * @return mixed
     */
    public function getContextObject() {
        return $this->_contextObject;
    }

    /**
     * @param $source
     *
     * @throws Exception
     * @return mixed
     */
    public function mapTarget($source) {
        $object = $this->getContextObject();
        if (!isset($object->$source)) {
            throw new Exception($source . ' is not a valid source ' . print_r($object, 1) . PHP_EOL);
        }

        return $object->$source;
    }

    /**
     * Get a mapping source by Target
     *
     * @param $target
     *
     * @throws Exception
     * @return bool
     */
    public function getMappingSource($target) {
        $mapping = $this->getMapping();
        foreach ($mapping as $mappingRow) {
            if ($mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_TARGET] === $target) {
                return $mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_SOURCE];
            }
        }

        return false;
    }

    /**
     * @param $source
     *
     * @return array
     */
    public function getMappingRows($source) {
        $mappingRows = [];
        $mapping = $this->getMapping();
        foreach ($mapping as $mappingRow) {
//            var_dump($mappingRow);
            if ($mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_SOURCE] == $source) {
                $mappingRows[] = $mappingRow;
            }
        }

        return $mappingRows;
    }

    /**
     * Map an object
     */
    public function mapObject(AbstractObject $object) {
        $contextObject = $this->getContextObject();

        foreach ($contextObject as $key => $value) {
            // first check if field mapped, else skip
            $mappingRows = $this->getMappingRows($key);
            if (empty($mappingRows)) {
                continue;
            }

            foreach ($mappingRows as $mappingRow) {
                $target = $mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_TARGET];
                if (!$target) {
                    continue;
                }
                $language           = $mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_LANGUAGE];
                $fieldType          = $mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_FIELDTYPE];
                $filters            = $mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_FILTERS];
                $overwrite_empty    = $mappingRow[CsvDataMapper_Abstract_Data_Mapper::MAPPING_OVERWRITE_EMPTY];

                $value = trim($value);
                if (strlen($value) > 0 || $overwrite_empty) {
                    // APPLY FILTERS
//                    echo '-----------------------------' . "\n";
//                    echo 'Language:  ' . $language . "\n";
//                    echo 'Fieldtype: ' . $fieldType . "\n";
//                    echo 'Filter:    ' . $filters . "\n";
//                    var_dump($filters);
                    if ($filters) {
                        $filters = json_decode($filters, true);
                        $this->filterChain($value, $language, $filters);
                    }
                    // TRY THE CUSTOM FUNCTION IF IT EXISTS FOR THIS FIELD!
                    $setter = 'import' . ucfirst($target);
                    if (method_exists($object, $setter)) {
                        $object->$setter($value);
                    } else {
                        switch ($fieldType) {
                            case "numeric" :
                                $object = CsvDataMapper_Data_Store::storeNumber($object, $target, $value, $language);
                                break;
                            case "wysiwyg" :
                                $object = CsvDataMapper_Data_Store::storeWysiwyg($object, $target, $value, $language);
                                break;
                            case "textarea" :
                            case "input" :
                                $object = CsvDataMapper_Data_Store::storeString($object, $target, $value, $language);
                                break;
                            case "datetime" :
                                $object = CsvDataMapper_Data_Store::storeDatetime($object, $target, $value, $language);
                                break;
                            case "checkbox":
                                $object = CsvDataMapper_Data_Store::storeBoolean($object, $target, $value, $language);
                                break;
                            default:
                                // "objects", "object", "href", .... but NOT empty (reserved)
                                if($fieldType)
                                {
                                    $object = CsvDataMapper_Data_Store::storeDefault($object, $target, $value, $language);
                                }

                                // if parent id is mapped and the value is not empty, override the value from the config.
                                if(($target === 'o_parentId') & $value !== null) {
                                    $object->setParentId($value);
                                }

                                break;
                        }
                    }
                }
            }
        }

        return $object;
    }

    public function filterChain(&$value, $language, $filters)
    {
        foreach($filters as $filter)
        {
//            echo "Filtering: " . $filter['id'] . "\n";

            $fc = new $filter['class'];
            /* @var $fc \BulkPump\ImportFilter\Base */
            if(!is_subclass_of($fc, '\BulkPump\ImportFilter\Base'))
            {
                throw new Exception('Fatal error: Improper filter detected: ' . $filter['id']);
            }
            $fc->setValue($value);
            $fc->setLanguage($language);
            if($filter['params']) {
                foreach($filter['params'] as $key => $value)
                {
                    $fc->setParameterValue($key, $value);
                }
            }
            $value = $fc->filter();
        }
    }

    /**
     * @param AbstractObject|Product $object
     * @param string $attributesStartAfter
     * @param string $classificationFieldName
     * @param string $classificationHeader
     * @param string $attributeLanguageSeparator
     * @return AbstractObject
     * @throws Exception
     */
    public function mapAttributes(AbstractObject $object, $attributesStartAfter, $classificationFieldName, $classificationHeader, $attributeLanguageSeparator) {
        $contextArray = (array)$this->getContextObject();
        $classificationFieldDef = $object->getClass()->getFieldDefinition($classificationFieldName);

        if (false === $classificationFieldDef) {
            throw new Exception(sprintf('Object "%s" has no field named "%s"', get_class($object), $classificationFieldName));
        }


        // search column index
        $index = array_search($attributesStartAfter, array_keys($contextArray));
        if (!$index) {
            throw new Exception('Index not found!');
        }


        // slice array from found index( and +1 after)
        $attributes = array_slice($contextArray, ($index + 1));
        if (!is_array($attributes) || (is_array($attributes) && empty($attributes))) {
            throw new Exception('Attributes not valid!');
        }

        if (false === array_key_exists($classificationHeader, $contextArray)) {
            throw new Exception('Classification field not set in current row!');
        }

        $csGroupsIds = $this->getClassificationGroupsByCollectionName($contextArray[$classificationHeader]);

        $containerData = array();

        foreach ($attributes as $columnName => $value) {
            // We only care about values that are not empty
            $parsedValue = trim((string)$value);
            if (strlen($parsedValue) === 0) {
                continue;
            }

            if($attributeLanguageSeparator) {
                $language = $this->getLanguageFromColName($columnName, $attributeLanguageSeparator);
                if ($language !== 'default') {
                    $columnName = $this->getColumnNameWithoutLanguage($columnName, $language, $attributeLanguageSeparator);
                }
            }


            $csGroupRelationsListing = new Classificationstore\KeyGroupRelation\Listing();
            $csGroupPlacehold = implode(',', array_fill(0, count($csGroupsIds), '?'));
            $conditions = $csGroupsIds;
            $conditions[] = $columnName;
            $csGroupRelationsListing->setCondition('groupId IN (' . $csGroupPlacehold . ')  AND name = ?', $conditions);
            $csRelations = $csGroupRelationsListing->load();

            if (empty($csRelations)) {
                $collectionNames = array_map(function ($csGroupsId) {
                    return GroupConfig::getById($csGroupsId)->getName();
                }, $csGroupsIds);
                //echo (sprintf('Couldn\'t find classification key with name "%s" within groups "%s" ' . "\n", $columnName, implode(', ', $collectionNames)));
                throw new Exception(sprintf('Couldn\'t find classification key with name "%s" within groups "%s" ', $columnName, implode(', ', $collectionNames)));
            }
            /** @var Classificationstore\KeyGroupRelation $csRelation */
            foreach ($csRelations as $csRelation) {

                $groupId = $csRelation->getGroupId();
                $keyId = $csRelation->getKeyId();

                $keyConfig = Object\Classificationstore\DefinitionCache::get($keyId);
                // Sanitize

                $value = trim($value);
                if ($keyConfig->getType() === 'numeric') {
                    if (strstr($value, ",") !== false) {
                        $value = str_replace(",", ".", $value);
                    }
                } else if ($keyConfig->getType() === 'multiselect') {
                    // Pimcore has hardcoded comma separated value
                    $value = str_replace(';', ',', $value);
                    $value = $this->str2lower($value);
                } else if ($keyConfig->getType() === 'select') {
                    $value = $this->str2lower($value);
                }


                $this->validateKeyConfig($keyConfig, $value);

                $containerData['data'][$language][$columnName] = array(
                    'value'   => $value,
                    'keyId'   => $keyId,
                    'groupId' => $groupId
                );
            }


        }

        // It can be that one product has no attribute
        if (empty($containerData) === false) {
            $object->setValue($classificationFieldName, $classificationFieldDef->getDataFromEditmode($containerData, $object));
        }

        return $object;

    }

    /**
     * @param KeyConfig $keyConfig
     *
     * @param string $value
     *
     * @throws Exception
     */
    private function validateKeyConfig($keyConfig, $value) {

        $keyConfigType = $keyConfig->getType();
        switch ($keyConfigType) {
            case 'input':
            case 'textarea':
            case 'wysiwyg':
                // No validation implemented yet
                break;
            case 'checkbox':
                // No validation implemented yet
                break;
            case 'numeric':

                if (preg_match('[^0-9\.-]', $value) > 0) {
                    //echo(sprintf("\"%s\" is not valid value for KeyConfig \"%s\" with type \"%s\"\n", $value, $keyConfig->getName(), $keyConfig->getType()));
                    throw new Exception(sprintf('Value "%s" is not valid for KeyConfig "%s" with type "%s"', $value, $keyConfig->getName(), $keyConfig->getType()));
                }

                break;
            case 'slider':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'select':

                /** @var Pimcore\Model\Object\ClassDefinition\Data\Multiselect $fieldDefinition */
                $fieldDefinition = Object\Classificationstore\Service::getFieldDefinitionFromKeyConfig($keyConfig);
                if (false === array_search($value, array_column($fieldDefinition->getOptions(), 'value'))) {
                    //echo(sprintf("\"%s\" is not valid value for KeyConfig \"%s\" with type \"%s\"\n", $value, $keyConfig->getName(), $keyConfig->getType()));
                    throw new Exception(sprintf('"%s" is not valid value for KeyConfig "%s" with type "%s"', $value, $keyConfig->getName(), $keyConfig->getType()));
                };

                break;
            case 'multiselect':
                // Pimcore has hardcoded comma separated value
                /** @var Pimcore\Model\Object\ClassDefinition\Data\Multiselect $fieldDefinition */
                $fieldDefinition = Object\Classificationstore\Service::getFieldDefinitionFromKeyConfig($keyConfig);
                $rawValues = explode(',', $value);
                foreach ($rawValues as $rawValue) {
                    if (false === array_search($rawValue, array_column($fieldDefinition->getOptions(), 'value'))) {
                        //echo(sprintf("\"%s\" is not valid value for KeyConfig \"%s\" with type \"%s\"\n", $value, $keyConfig->getName(), $keyConfig->getType()));
                        throw new Exception(sprintf('"%s" is not valid value for KeyConfig "%s" with type "%s"', $value, $keyConfig->getName(), $keyConfig->getType()));
                    };
                }
                break;
            case 'date':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'datetime':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'language':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'languagemultiselect':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'country':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'countrymultiselect':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            case 'table':
                $this->throwKeyConfigNotImplemented($keyConfigType);
                break;
            default:
                throw new Exception("'$keyConfigType' is not registered as a valid keyconfig type");
        }
    }

    function str2lower($val) {
        return mb_strtolower(trim($val), 'UTF-8');
    }

    /**
     * @param $keyConfigType
     *
     * @throws Exception
     */
    private function throwKeyConfigNotImplemented($keyConfigType) {
        throw new Exception(sprintf('"%s" validation not yet implemented', $keyConfigType));
    }

    /**
     * @param string $classificationNames
     *
     * @return array
     * @throws Exception
     */
    private function getClassificationGroupsByCollectionName($classificationNames) {
        $classificationNames = (array)explode(';', $classificationNames);
        $db = \Pimcore\Resource\Mysql::get();


        $csGroups = array();
        foreach ($classificationNames as $classificationName) {
            $query = $db->select()
                ->from(array('cgr' => CollectionGroupRelation\Resource::TABLE_NAME_RELATIONS), array('groupId'))
                ->join(array('c' => 'classificationstore_collections'), 'c.id = cgr.colId', array())
                ->where('c.name = ?', $classificationName);
            $csGroupsIds = $db->fetchCol($query);

            if (empty($csGroupsIds)) {
                throw new Exception('Couldn\'t find classification collection with name ' . $classificationName);
            }
            $csGroups = array_merge($csGroups, $csGroupsIds);
        }

        return $csGroups;
    }

    private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    private function getLanguageFromColName($columnName, $attributeLanguageSeparator) {

        $languages = Tool::getValidLanguages();

        foreach ($languages as $language) {
            if ($this->endsWith($columnName, $attributeLanguageSeparator . $language)) {
                return $language;
            }
        }
        return 'default';
    }

    private function getColumnNameWithoutLanguage($columnName, $languageCode, $attributeLanguageSeparator) {
        $words = explode($attributeLanguageSeparator . $languageCode, $columnName);
        array_splice($words, -1);
        return $words[0];
    }
}