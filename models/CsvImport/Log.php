<?php
/**
 * Log.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/7/15
 *
 */
class CsvImport_Log extends CsvImport_Abstract_Model
{
    const PLUGIN_LOG_FOLDER = 'csv_importer';
    const MAX_LOGS_LIMIT    = 20;

    public static function getLogFolder()
    {
        return PIMCORE_LOG_DIRECTORY . DIRECTORY_SEPARATOR . self::PLUGIN_LOG_FOLDER;
    }

    /**
     * @param string $filename
     *
     * @return null|string
     */
    protected function parseStatusFromFilename($filename)
    {
        $re = "/^(" . CsvImport_Logger::STATUS_OK . "|" . CsvImport_Logger::STATUS_ERROR . ")/";
        if (preg_match($re, $filename, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * @param string $filepath
     *
     * @return array
     */
    protected static function getFileContentsAsArray($filepath)
    {
        if (!file_exists($filepath)) {
            return array();
        }
        $return = array();
        $lines = file($filepath);
        foreach ($lines as $record) {
            $recordCol = explode('|', trim($record));
            $recordCol = array_slice($recordCol, 0, 3);
            $return[] = $recordCol;
        }


        return $return;
    }

    /**
     * @param int|string $objectId
     * @param bool       $withContent
     *
     * @return array
     */
    public static function getLogsByObjectIdSortedByDate($objectId, $withContent = true)
    {
        $path = self::getLogFolder() . DIRECTORY_SEPARATOR . $objectId;
        $files = array();
        if (is_dir($path) === false) {
            return $files;
        }
        foreach (new RecursiveIteratorIterator(
                     new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS)) as $value) {
            if ($value->isFile()) {
                $filename = basename($value->getRealPath());
                $status = self::parseStatusFromFilename($filename);
                $content = $withContent ? self::getFileContentsAsArray($value->getRealPath()) : null;
                $files[] = array(
                    'status'   => $status,
                    'time'     => $value->getMTime(),
                    'realPath' => $value->getRealPath(),
                    'content'  => $content
                );
            }
        }

        usort($files, function ($a, $b) {
                return $a['time'] < $b['time'];
            });

        return $files;
    }

    /**
     * @param int|string $objectId
     *
     */
    public static function cleanupOldFiles($objectId)
    {
        $allFiles = self::getLogsByObjectIdSortedByDate($objectId, false);
        $filesToDelete = array_slice($allFiles, self::MAX_LOGS_LIMIT);

        foreach ($filesToDelete as $file) {
            @unlink($file['realPath']);
        }
    }
}