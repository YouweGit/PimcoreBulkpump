<?php

use Pimcore\Model\Object\ClassDefinition\Data\Localizedfields;

class CsvImport_Profile extends CsvImport_Abstract_Model
{

    /**
     * @var array
     */
    protected $config          = array();
    protected $columns         = array();
    private   $uniqueFieldList = array();

    /**
     * @param $id
     *
     * @return CsvImport_Profile
     * @throws Exception
     */
    public static function getById($id)
    {
        if (!is_numeric($id)) {
            throw new Exception(' Is not a valid id ' . $id);
        }
        $profileMapper = new CsvImport_Mapper_Profile();
        $profileMapper->init();
        $row = $profileMapper->getById($id);

        if(!$row){
            throw new Exception("Profile not found");
        }
        $profile = new self();
        $profile->id = $id;
        $profile->_data = $row->toArray();

        return $profile;
    }

    /**
     * @return CsvImport_Config
     */
    public function getConfig()
    {

        if (empty($this->config)) {
            $this->config = CsvImport_Config::getByProfileId($this->getId());
        }

        return $this->config;
    }

    /**
     * @param CsvImport_Config $config
     *
     * @return $this
     */
    public function setConfig(CsvImport_Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createConfig()
    {
        $tmpClassName = $this->object;
        if (!$tmpClassName) {
            return null;
        }

        $className = '\\Pimcore\\Model\\Object\\' . ucfirst($tmpClassName);

        if (strlen($tmpClassName) === 0 || !class_exists($className)) {
            throw new Exception("Invalid class name : " . $className . PHP_EOL);
        }

        $object = call_user_func($className . '::create');
        $vars = $this->_stripVars($object);
        $localizefields = $this->_stripLocalizedFields($object);
        $result = array_merge($vars, $localizefields);

        foreach ($result as $i => $row) {
            $result[$i]['profile_id'] = $this->getId();
        }
        $config = CsvImport_Config::create($result);
        $this->setConfig($config);

        return $config;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function updateConfig()
    {
        $tmpClassName = $this->object;
        if (!$tmpClassName) {
            return null;
        }

        $className = '\\Pimcore\\Model\\Object\\' . ucfirst($tmpClassName);

        if (strlen($tmpClassName) === 0 || !class_exists($className)) {
            throw new Exception("Invalid class name : " . $className . PHP_EOL);
        }

        $config = $this->getConfig();
        if ($config->isEmpty()) {
            return $this->createConfig();
        }

        $object = call_user_func($className . '::create');
        $vars = $this->_stripVars($object);
        $localizedFields = $this->_stripLocalizedFields($object);
        $fields = array_merge($vars, $localizedFields);



        $columnsModel = CsvImport_Columns::getByProfileId($this->getId());
        $columns = $columnsModel->getKeyValueMap();

        $newProfileConfig = new CsvImport_Mapper_Config();

        // delete things that don't exists at all or they have
        foreach ($config->getData() as $data) {

            $key = array_filter($fields, function ($field) use ($data) {
                if (
                    $field['pimcore_field'] === $data['pimcore_field'] &&
                    $field['fieldtype'] === $data['fieldtype'] &&
                    array_key_exists('language', $field) === false &&
                    $this->isFieldUnique($data) === true
                ) {
                    return true;

                } elseif (
                    $field['pimcore_field'] === $data['pimcore_field'] &&
                    $field['fieldtype'] === $data['fieldtype'] &&
                    array_key_exists('language', $field) === true &&
                    array_key_exists('language', $data) === true &&
                    $field['language'] === $data['language'] &&
                    $this->isFieldUnique($data) === true
                ) {
                    return true;
                }

                return false;

            });

            $dataId = $data['id'];

            if (empty($key)) {
                $newProfileConfig->delete($dataId);
            }

            // Empty csv field config if does not exist anymore in the csv
            $csvField = $data['csv_field'];
            if ($csvField != '' && !in_array($csvField, $columns)) {
                $newProfileConfig->update($dataId, ['csv_field' => '']);
            }
        }
        $profileData = $this->getConfig()->getData();

        // add if not exists
        foreach ($fields as $i => $field) {

            $key = array_filter($profileData, function ($profileData) use ($field) {
                if (
                    $profileData['pimcore_field'] === $field['pimcore_field'] &&
                    $profileData['fieldtype'] === $field['fieldtype'] &&
                    array_key_exists('language', $field) === false
                ) {
                    return true;

                } elseif (
                    $profileData['pimcore_field'] === $field['pimcore_field'] &&
                    $profileData['fieldtype'] === $field['fieldtype'] &&
                    array_key_exists('language', $field) === true &&
                    array_key_exists('language', $profileData) === true &&
                    $field['language'] === $profileData['language']
                ) {
                    return true;
                }

                return false;

            });

            if (empty($key)) {
                $newConfig = array('profile_id' => $this->getId());
                $newProfileConfig->create(array_merge($newConfig, $field));
            }
        }
    }

    /**
     * @param $fieldConf
     *
     * @return bool
     */
    private function isFieldUnique($fieldConf)
    {
        $fName = $fieldConf['pimcore_field'];
        $fType = $fieldConf['fieldtype'];
        $fLanguage = $fieldConf['language'];
        $fProfileId = $fieldConf['profile_id'];
        $uniqueName = $fProfileId . '~' . $fName . '~' . $fType . '~' . $fLanguage;
        if (in_array($uniqueName, $this->uniqueFieldList)) {
            return false;
        }

        $this->uniqueFieldList[] = $uniqueName;

        return true;
    }

    /**
     * @param \Pimcore\Model\Object\AbstractObject $object
     *
     * @return array
     */
    protected function _stripVars(Pimcore\Model\Object\AbstractObject $object)
    {

        $return = array();
        $definitions = $object->getClass()->getFieldDefinitions();
        $vars = array_keys(get_object_vars($object));

        $protected_vars = array(
            'o_classId',
            'o_className',
            'o_class',
            'o_versions',
            'lazyLoadedFields',
            'localizedfields',
            'scheduledTasks',
            'o_id',
            'o_type',
            'o_creationDate',
            'o_modificationDate',
            'o_userOwner',
            'o_userModification',
            'o_properties',
            'o_hasChilds',
            'o_siblings',
            'o_hasSiblings',
            'o_dependencies',
            'o_childs',
            'o_locked',
            'o_elementAdminStyle',
            'o___loadedLazyFields'
        );
        $diff = array_diff($vars, $protected_vars);

        foreach ($diff as $k => $v) {
            $return[] = array(
                'pimcore_field' => $v,
                'fieldtype'     => $definitions[$v]->fieldtype
            );
        }

        return $return;
    }

    /**
     * @param \Pimcore\Model\Object\AbstractObject $object
     *
     * @return array
     */
    public function _stripLocalizedFields(Pimcore\Model\Object\AbstractObject $object)
    {

        $definitions = $object->getClass()->getFieldDefinitions();
        $return = array();
        if (!isset($definitions['localizedfields'])) {
            return $return;
        }
        /** @var Localizedfields $localizeFields */
        $localizedFields = $definitions['localizedfields'];
        $languages = \Pimcore\Tool::getValidLanguages();

        /** @note if some one has a better solution for this he is welcome */
        foreach ($languages as $language) {
            $this->recursiveGetLocalizedChilds($localizedFields, $language, $return);
        }


        return $return;
    }

    /**
     * @param Localizedfields $localizedFields
     * @param string          $language
     * @param array           $return
     *
     * @return null
     */
    private function recursiveGetLocalizedChilds($localizedFields, $language, &$return)
    {
        if ($localizedFields->hasChilds() === false) {
            return null;
        }
        foreach ($localizedFields->getChilds() as $field) {
            $return[] = array(
                'pimcore_field' => $field->name,
                'fieldtype'     => $field->fieldtype,
                'language'      => $language
            );
        }
        if ($localizedFields->getReferencedFields()) {
            foreach ($localizedFields->getReferencedFields() as $referencedFields) {
                $this->recursiveGetLocalizedChilds($referencedFields, $language, $return);
            }
        }

        return null;
    }

    /**
     * @return CsvImport_Columns
     */
    public function getColumns()
    {
        if (empty($this->columns)) {
            $this->columns = CsvImport_Columns::getByProfileId($this->getId());
        }

        return $this->columns;
    }

    /**
     * @return CsvImport_Columns
     */
    public function createColumns()
    {
        $filename = $this->load_path;
        $fullname = CsvImport_File::getTmpPath() . '/' . $filename;
        if (!is_file($fullname)) {
            return [];
        }
        $id = $this->getId();
        $this->columns = CsvImport_Columns::createFromFile($filename, $id);

        return $this->columns;
    }


    /**
     * @return CsvImport_Columns
     */
    public function updateColumns()
    {
        $filename = $this->load_path;
        $fullname = CsvImport_File::getTmpPath() . '/' . $filename;
        if (!is_file($fullname)) {
            return [];
        }
        $id = $this->getId();
        $this->columns = CsvImport_Columns::updateFromFile($filename, $id);

        return $this->columns;
    }


}