<?php
/**
 * Csv Import Script
 *
 *
 * @category Youwe Development
 * @package  psg.pimcore
 * @author   Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date     6/1/15
 *
 */
$workingDirectory = getcwd();
chdir(__DIR__);
include_once("../../../pimcore/cli/startup.php");

//execute in admin mode
define("PIMCORE_ADMIN", true);

///some command line options for my importer
try {
    $opts = new Zend_Console_Getopt(array(
        'profileId|p=i' => 'profile required integer parameter '
    ));
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

$profileId = $opts->getOption('profileId');
try {
    CsvDataMapper_Import_Profile::run($profileId);
} catch( Exception $e) {
    die($e);
}
