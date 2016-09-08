<?php


class CsvImport_Logger
{
    const LOG_FILE_FILENAME_FORMAT = '%status%_%timestamp%.log';
    const LOG_ENTRY_FORMAT         = '%time% | %status% | %message%';

    const MESSAGE_ERROR = 'ERROR';
    const MESSAGE_LOG   = 'LOG';

    const STATUS_ERROR = 'error';
    const STATUS_OK    = 'ok';

    /** @var  string */
    private $status;
    /** @var int */
    private $timestamp;
    /** @var string */
    private $logFolder;
    /** @var array */
    public $entries;
    /** @var int */
    private $errorsCount;

    /**
     * @param  string  $logFolder
     * @param null|int $timestamp
     */
    public function __construct($logFolder, $timestamp = null)
    {
        $this->timestamp = ($timestamp) ? $timestamp : time();
        $this->logFolder = $logFolder;
        $this->errorsCount = 0;
        $this->entries = array();
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param string  $status
     * @param  string $message
     */
    protected function addEntry($status, $message)
    {
        $now = new Pimcore_Date();
        $replacements = array('%time%', '%status%', '%message%');
        $replaceWith = array($now->toString(Pimcore_Date::MYSQL_DATETIME), $status . "\t", $message);
        $this->entries[] = str_replace($replacements, $replaceWith, self::LOG_ENTRY_FORMAT);
    }

    /**
     * @param string $message
     */
    public function addError($message)
    {
        $this->addEntry(self::MESSAGE_ERROR, $message);
        $this->errorsCount++;
    }

    /**
     * @param string $message
     */
    public function addLog($message)
    {
        $this->addEntry(self::MESSAGE_LOG, $message);
    }

    public function flush()
    {
        if (!is_dir($this->logFolder)) {
            Pimcore_File::mkdir($this->logFolder);
        }

        $status = $this->errorsCount > 0 ? self::STATUS_ERROR : self::STATUS_OK;
        $replacements = array('%status%', '%timestamp%');
        $replaceWith = array($status, time());
        $filename = str_replace($replacements, $replaceWith, self::LOG_FILE_FILENAME_FORMAT);
        $fullPath = $this->logFolder . DIRECTORY_SEPARATOR . $filename;
        $content = implode("\n", $this->entries);

        file_put_contents($fullPath, $content, LOCK_EX);
    }


}