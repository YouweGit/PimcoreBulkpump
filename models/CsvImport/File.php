<?php

/**
 * File.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package  intratuin-pimcore
 * @author   Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date     12/3/15
 *
 */
class CsvImport_File extends CsvImport_Abstract_Model {


    const TMP_DIR = 'bulk-pump';


    /**
     * @var string full path an name of the file
     */
    protected $_fullpath;

    /**
     * @var string name of the file
     */
    protected $_fileName;

    /**
     * Head of the csv file
     *
     * @var array
     */
    protected $_head = array();

    /**
     * @param string $fileName
     */
    public function setFileName($fileName) {
        $this->_fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName() {
        return $this->_fileName;
    }

    /**
     * @param array $head
     */
    public function setHead($head) {
        $this->_head = $head;
    }

    /**
     * @return array
     */
    public function getHead() {
        return $this->_head;
    }

    /**
     * @param mixed $fullpath
     */
    public function setFullpath($fullpath) {
        $this->_fullpath = $fullpath;
    }

    /**
     * @return mixed
     */
    public function getFullpath() {
        return $this->_fullpath;
    }

    /**
     * Returns the plugin temp directory
     *
     * @return string
     */
    public static function getTmpPath() {
        $path = PIMCORE_TEMPORARY_DIRECTORY . DIRECTORY_SEPARATOR . self::TMP_DIR;
        if (!is_dir($path)) {
            \Pimcore\File::mkdir($path);
        }
        return $path;
    }

    /**
     *
     * @param $filename
     *
     * @throws Exception
     * @internal param string $fullpath full path to the file
     * @return \CsvImport_File
     */
    public static function init($filename) {
        $fullpath = self::getTmpPath() . '/' . $filename;
        if (!is_file($fullpath)) {
            throw new Exception($fullpath . ' is not valid file! ' . PHP_EOL);
        }
        $file = new self();
        $file->setFileName($filename);
        $file->setFullpath($fullpath);

        $head = self::readHead($filename);
        $file->setHead($head);

        $data = self::readCsv($fullpath);
        $file->setData($data);

        return $file;
    }

    /**
     * Reads the csv from the configured file path
     *
     * @param $csvFilePath
     *
     * @throws Exception
     * @internal param $filePathNode
     * @return array
     */
    public static function readCsv($csvFilePath) {
        $csv = self::read($csvFilePath);
        $keys = array_shift($csv);

        // cleanup the keys
        foreach ($keys as $i => $key) {
            $keys[$i] = $key;
        }

        // manage the csv
        $cleanCSV = [];
        foreach ($csv as $key => $data) {
            $kCount = count($keys);
            $dCount = count($data);
            if ($kCount != $dCount) {
//                print "\nCSV row length is inconsistent on line $key. Header length is '$kCount' and row length is '$dCount', pls check the the csv file!";
//                continue;
                if ($dCount > $kCount) {
                    $data = array_slice($data, 0, $kCount);
                } else {
                    throw new Exception("CSV row length is inconsistent on line $key. Header length is '$kCount' and row length is '$dCount', pls check the the csv file!");
                }
            };
            $csv[$key] = array_combine($keys, $data);
            foreach ($csv[$key] as $fieldKey => $fieldString) {
                $cleanCSV[$key][$fieldKey] = $fieldString;
            }
        }

        return $cleanCSV;
    }

    /**
     * Read the rows from a csv file
     */
    public function getRows() {


    }


    /**
     * @param   $filename
     *
     * @return  array
     */
    public static function readHead($filename) {
        $fullname = self::getTmpPath() . '/' . $filename;
        $csv = self::read($fullname, 1);
        $head = current($csv);

        return $head;
    }

    /**
     * @param   string $fullname full filename and path
     * @param   int $n linenumber
     *
     * @throws Exception
     * @return  array
     */
    public static function read($fullname, $n = null) {
        $return = array();
        $i = 0;
        if (!is_file($fullname)) {
            throw new Exception('Cant open : ' . $fullname);
        }
        $file = fopen($fullname, 'r');
        while (($data = fgetcsv($file)) !== false) {
            $i++;
            $return[] = $data;
            if ($n !== null && $i == $n) {
                return $return;
            }
        }
        fclose($file);

        return $return;
    }

}
