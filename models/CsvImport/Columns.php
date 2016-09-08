<?php
/**
 * Columns.php
 *
 * @required CsvDataMapper_Helper
 *
 * Custom model implementation for custom table
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/2/15
 *
 */
class CsvImport_Columns extends CsvImport_Abstract_Model
{
    /**
     * @param $values
     * @return array
     * @throws Exception
     */
    public static function create($values)
    {
        $mapper = new CsvImport_Mapper_Columns();
        $mapper->init();
        foreach ($values as $i => $row) {
            try {
                $result =  $mapper->create($row);
            } catch(Exception $e) {
                throw $e;
            }
            $values[$i]['id'] = $result;
        }
        $columns = new self();
        $columns->setData($values);
        return $columns;
    }

    /**
     * @param $id
     * @return CsvImport_Columns
     */
    public static function getByProfileId($id, $query = null)
    {
        $mapper = new CsvImport_Mapper_Columns();
        $mapper->init();

        $row    = $mapper->getByProfileId($id, $query);
        $values = $row->toArray();
        $object = new self();
        foreach($values as $key => $value) {
            $object->_data[$key] = $value;
        }
        return $object;
    }

    /**
     * @param $filename
     * @param $profileId
     * @throws Exception
     * @return array
     */
    public static function createFromFile($filename, $profileId)
    {
        $columns = CsvImport_File::readHead($filename);
        if(empty($columns)) {
            throw new Exception($filename . 'file is empty or corrupted ');
        }
        $data    = array();
        foreach ($columns as $field) {
            $data[] = array (
                'csv_field'     => $field,
                'profile_id'    => (int) $profileId
            );
        }
        return CsvImport_Columns::create($data);
    }

    /**
     * @param $filename
     * @param $profileId
     * @throws Exception
     * @return array
     */
    public static function updateFromFile($filename, $profileId)
    {
        // get existing columns in array
        $colMapper             = new CsvImport_Mapper_Columns();
        $colMapper->init();
        $row                = $colMapper->getByProfileId($profileId);
        $existing_columns_raw   = $row->toArray();
//        Logger::debug('EXISTING COLUMNS RAW:');
//        Logger::debug(var_export($existing_columns_raw,1));
//      array (
//        0 =>
//            array (
//                'id' => 211,
//                'profile_id' => 5,
//                'csv_field' => 'product_id',
//            ),
//        1 =>
//            array (
//                'id' => 212,
//                'profile_id' => 5,
//                'csv_field' => 'name_NL',
//            ),
//        2 =>
//            array (
//                'id' => 213,
//                'profile_id' => 5,
//                'csv_field' => 'price_NL',
//            ),
        $existing_columns = array();
        foreach($existing_columns_raw as &$excol)
        {
            $existing_columns[$excol['id']] = $excol['csv_field'];
        }
//        Logger::debug('EXISTING COLUMNS:');
//        Logger::debug(var_export($existing_columns,1));
//        array (
//            'product_id' => 1,
//            'name_NL' => 1,
//            'price_NL' => 1,
//            'unit' => 1,
//            'stock' => 1,

        $columns = CsvImport_File::readHead($filename);
        if(empty($columns)) {
            throw new Exception($filename . ' file is empty or corrupted ');
        }
        $data    = array();
        foreach ($columns as $field) {
            // only add field to data if its not in existing-columns array (new field!)
            if(!in_array($field, $existing_columns))
            {
                $data[] = array (
                    'csv_field'     => $field,
                    'profile_id'    => (int) $profileId
                );
            }
            else {
                // ELSE: remove the field from existing-columns array (field exists already)
                $index = array_search($field, $existing_columns);
                unset($existing_columns[$index]);
            }
        }
        // Remove all columns that are left in existing-columns array from the database
//        Logger::debug('REMAINING COLUMNS:');
//        Logger::debug(var_export($existing_columns,1));

        foreach($existing_columns as $excol_id => $column_name) {
            $colMapper->delete($excol_id);
        }

        return CsvImport_Columns::create($data);
    }

    /**
     * Delete the columns for a profile
     */
    public function deleteByProfileId($id)
    {
        $mapper = new CsvImport_Mapper_Columns();
        $mapper->init();
        $mapper->deleteByProfileId($id);
        $this->setData(array());
        return $this;
    }

    /**
     * Returns array[id => csv_field]
     * @return array
     */
    public function getKeyValueMap() {
        $columns = [];
        foreach($this->_data as $row)
        {
            $columns[$row['id']] = $row['csv_field'];
        }
        return $columns;
    }
}