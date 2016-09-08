<?php

/**
 * Profile.php
 *
 * Interface for the profile running @see CsvImport_Profile
 *
 * @category Youwe Development
 * @package  intratuin-pimcore
 * @author   Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date     12/9/15
 *
 */
class CsvDataMapper_Import_Profile extends CsvDataMapper_Import {

    /**
     * @param array $profileId
     *
     * @return bool|void
     */
    public static function run($profileId) {
        $profile = CsvImport_Profile::getById($profileId);
        $config = array(
            'profileId'                  => $profile->getId(),
            'filename'                   => $profile->load_path,
            'path'                       => $profile->save_to_path,
            'importType'                 => $profile->import_type ? $profile->import_type : 'object',
            'attributesStartAfter'       => $profile->attributes_start_after,
            'classificationStoreField'   => $profile->classification_store_field,
            'classificationField'        => $profile->classification_field,
            'classname'                  => $profile->object,
            'attributeLanguageSeparator' => $profile->attribute_language_separator,
            'storeAsVariant'             => $profile->store_as_variant,
            'customClass'                => $profile->custom_class,
            'mappingType'                => 'profile',
        );
        parent::run($config);
    }
}

