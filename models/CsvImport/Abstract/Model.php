<?php
/**
 * Model.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/2/15
 *
 */
abstract class CsvImport_Abstract_Model implements Iterator
{
    /**
     * @var int $_index index of itteration
     */
    protected $_index = 0;

    /**
     * @var array data holder for itteration
     */
    protected $_data = array();


    /**
     * @var int $id profile_id
     */
    protected $id;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        $data = $this->getData();
        if (empty($data)) {
            return true;
        }
        return false;
    }

    /**
     * Traditional getter
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Traditional setter
     *
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     *
     */
    public function __construct() {
        $this->_index = 0;
    }

    /**
     *
     */
    function rewind() {
        $this->_index = 0;
    }

    /**
     * @return mixed
     */
    function current() {
        return $this->_data[$this->_index];
    }

    /**
     * @return int|mixed
     */
    function key() {
        return $this->_index;
    }

    /**
     *
     */
    function next() {
        ++$this->_index;
    }

    /**
     * @return bool
     */
    function valid() {
        return isset($this->_data[$this->_index]);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
           return $this->_data[$name];
        }
        return null;
    }
}