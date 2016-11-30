<?php

namespace PimcoreBulkpump;

use Pimcore\API\Plugin as PluginLib;
use Pimcore\Resource;
use Pimcore\Console\ConsoleCommandPluginTrait;
use PimcoreBulkpump\Console\Command\ImporterCommand;
use Symfony\Component\Console\Command\Command;

class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface {
    use ConsoleCommandPluginTrait;

    public function init() {

        $front = \Zend_Controller_Front::getInstance();
        /** @var Zend_Controller_Router_Rewrite $router */
        $router = $front->getRouter();


        $restRoute = new \Zend_Rest_Route($front, array(), array(
            'PimcoreBulkpump' => array('profile'),
        ));
        $router->addRoute('csv_importer_rest_profile', $restRoute);

        $restRoute = new \Zend_Rest_Route($front, array(), array(
            'PimcoreBulkpump' => array('config'),
        ));
        $router->addRoute('csv_importer_rest_config', $restRoute);

        $restRoute = new \Zend_Rest_Route($front, array(), array(
            'PimcoreBulkpump' => array('filters'),
        ));
        $router->addRoute('csv_importer_rest_filters', $restRoute);

        $restRoute = new \Zend_Rest_Route($front, array(), array(
            'PimcoreBulkpump' => array('filterchain'),
        ));
        $router->addRoute('csv_importer_rest_filterchain', $restRoute);

        $this->initConsoleCommands();
    }

    /**
     * @return bool|string
     */
    public static function install (){
        // implement your own logic here
        $db = Resource::get();
        $userPermissions = self::getUserPermissions();
        foreach ($userPermissions as $up) {
            $db->insert("users_permission_definitions", $up);
        }

        $db->query("CREATE TABLE IF NOT EXISTS `csv_importer_profile` (
                      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `profile_name` VARCHAR(255),
                      `load_path` VARCHAR (255),
                      `save_to_path` VARCHAR(255),
                      `object` VARCHAR(255),
                      `key_field` VARCHAR(255),
                      `store_as_variant` TINYINT(1),
                      `creationDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                      `modificationDate` TIMESTAMP,
                      `import_type` char(10) DEFAULT 'object',
                      `attributes_start_after` varchar(255) DEFAULT NULL,
                      `classification_store_field` varchar(255) DEFAULT NULL,
                      `classification_field` varchar(255) DEFAULT NULL,
                      `attribute_language_separator` varchar(32) DEFAULT NULL,
                      `custom_class` varchar(255) DEFAULT NULL,
                      UNIQUE KEY `ix_profile_name` (`profile_name`),
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

        $db->query("CREATE TABLE IF NOT EXISTS `csv_importer_config` (
                      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `profile_id` INT(11) UNSIGNED NOT NULL,
                      `fieldtype` VARCHAR(255),
                      `csv_field` VARCHAR(255),
                      `pimcore_field` VARCHAR(255),
                      `language` VARCHAR(255),
                      `filters` MEDIUMBLOB,
                      `overwrite_empty` BOOLEAN DEFAULT FALSE,
                      KEY `csv_importer_files_config_FKIndex1` (`profile_id`),
                      CONSTRAINT `csv_importer_files_config_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `csv_importer_profile`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

        $db->query("
            CREATE TABLE IF NOT EXISTS  `csv_importer_columns` (
             `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
             `profile_id` INT( 11 ) UNSIGNED NOT NULL ,
             `csv_field` VARCHAR( 255 ) ,
            PRIMARY KEY (  `id` ) ,
            INDEX  `csv_importer_columns` (  `profile_id` ) ,
            FOREIGN KEY (  `profile_id` ) REFERENCES  `csv_importer_profile` (  `id` ) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE = INNODB AUTO_INCREMENT =1 DEFAULT CHARSET = utf8
        ");

        return true;
    }

    /**
     * @return array user permission names (untranslated)
     */
    public static function getUserPermissions()
    {
        $userPermissions = array(
            array("key" => "plugin_pimcorebulkpump_user"),
            //array("key" => "plugin_bulkpump_profiles"), //TODO: when we develop the plugin further
            //array("key" => "plugin_bulkpump_admin"), // TODO: when we develop the plugin further
        );
        return $userPermissions;
    }

    /**
     * @return bool|string
     */
    public static function uninstall (){
        // implement your own logic here
        self::_IfExistDropTable('csv_importer_columns');
        self::_IfExistDropTable('csv_importer_config');
        self::_IfExistDropTable('csv_importer_profile');

//        $db = Resource::get();
//        $userPermissions = self::getUserPermissions();
//        foreach ($userPermissions as $up) {
//            $db->delete("users_permission_definitions", $up);
//        }

        return true;
	}

    /**
     * Has to be truncated before deleted because of key cascading
     *
     * @param $tableName
     */
    protected static function _IfExistDropTable($tableName)
    {
        $db = Resource::get();
        $stmt = $db->query("SELECT *
          FROM information_schema.tables
          WHERE table_name = '$tableName'
          LIMIT 1;
        ");
        $result = $stmt->fetchAll();
        if(count($result) > 0) {
            $db->query("TRUNCATE $tableName");
            $db->query("DROP TABLE $tableName");
        }
    }

    /**
     * @return bool
     */
    public static function isInstalled () {
        // implement your own logic here
        if (self::tableExists('csv_importer_profile')
            || self::tableExists('csv_importer_config')
            || self::tableExists('csv_importer_columns')) {
            return true;
        }
        return false;
	}

    /**
     * @return bool
     */
    public static function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * @param $tableName
     * @return bool
     */
    private static function tableExists($tableName)
    {
        $db = Resource::get();
        $dbName = $db->getConfig()['dbname'];
        $tables = $db->query("SHOW TABLES FROM $dbName")->fetchAll();

        foreach ($tables as $key => $table) {
            if ($table['Tables_in_' . $dbName] == $tableName) {
                return true;
            }
        }

        return false;
    }

    public static function getTranslationFile($language)
    {
        if(file_exists(PIMCORE_PLUGINS_PATH . "/PimcoreBulkpump/translation/" . $language . ".csv")){
            return "/PimcoreBulkpump/translation/" . $language . ".csv";
        }
        return "/PimcoreBulkpump/translation/en.csv";

    }

    /**
     * Returns an array of commands to be added to the application.
     * To be implemented by plugin classes providing console commands.
     *
     * @return Command[]
     */
    public function getConsoleCommands()
    {
        return [
            new ImporterCommand(),
        ];
    }

}
