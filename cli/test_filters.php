<?php

$workingDirectory = getcwd();
chdir(__DIR__);
include_once("../../../pimcore/cli/startup.php");

//execute in admin mode
define("PIMCORE_ADMIN", true);

$Filter = new PimcoreBulkpump\ImportFilter\Native\stringToUpper();
$Filter->setValue('test');
$Filter->setParameterValue('only_first', true);
$result = $Filter->filter();
var_dump($result);

$Filter = new PimcoreBulkpump\ImportFilter\Native\stringToLower();
$Filter->setValue('TEST');
$Filter->setParameterValue('only_first', true);
$result = $Filter->filter();
var_dump($result);

$Filter = new PimcoreBulkpump\ImportFilter\Native\debugFilter();
$Filter->setValue('nothing will happen');
$result = $Filter->filter();
var_dump($result);

//            $list = new \Pimcore\Model\Object\Product\Listing();
//            $prods = $list->getObjects();

$Filter = new PimcoreBulkpump\ImportFilter\Native\productIdToProduct();
//            $Filter->setValue($prods[0]->getId());
$Filter->setValue(1);
$result = $Filter->filter();
var_dump($result);

$Filter = new PimcoreBulkpump\ImportFilter\Native\objectIdToObject();
//            $Filter->setValue($prods[0]->getId() . ';' . $prods[1]->getId());
$Filter->setValue('1;2;3');
$result = $Filter->filter();
var_dump($result);

//var_dump($upperFilter->getParameters());
//var_dump($upperFilter->getParameterValues());
