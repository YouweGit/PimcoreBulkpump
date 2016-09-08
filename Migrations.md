
    ALTER TABLE `csv_importer_config` ADD `overwrite_empty` BOOLEAN NOT NULL DEFAULT FALSE AFTER `filters`;

    ALTER TABLE csv_importer_profile ADD `custom_class` varchar(255) DEFAULT NULL; 
    ALTER TABLE csv_importer_profile ADD `attribute_language_separator` varchar(32) DEFAULT NULL; 

    

