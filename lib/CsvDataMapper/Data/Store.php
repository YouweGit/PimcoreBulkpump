<?php
use Pimcore\Model\Object\AbstractObject;

/**
 * Store.php
 *
 * <description>
 *
 * @category Youwe Development
 * @package  intratuin-pimcore
 * @author   Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date     12/9/15
 *
 */
class CsvDataMapper_Data_Store {
    const DATETIME_FORMAT = 'dd-MM-yyyy HH:mm:ss';
    const DATETIME_LOCALE = 'nl_NL';

    /**
     * Store string to object
     *
     * @param AbstractObject $object
     * @param                                $target
     * @param                                $value
     * @param                                $language
     *
     * @return AbstractObject
     * @throws Exception
     */
    public static function storeWysiwyg($object, $target, $value, $language) {
        $setter = 'set' . ucfirst($target);
        if (!method_exists($object, $setter)) {
            throw New Exception(sprintf('Object "%s" has no method "%s"', get_class($object), $setter));
        }

        $value = nl2br($value);

        if (strlen((string)$language) > 0) {
            $object->$setter($value, $language);
        } else {
            $object->$setter($value);
        }

        return $object;
    }

    /**
     * Store string to object
     *
     * @param AbstractObject $object
     * @param                                $target
     * @param                                $value
     * @param                                $language
     *
     * @return AbstractObject
     * @throws Exception
     */
    public static function storeString($object, $target, $value, $language) {
        $setter = 'set' . ucfirst($target);
        if (!method_exists($object, $setter)) {
            throw New Exception(sprintf('Object "%s" has no method "%s"', get_class($object), $setter));
        }

        if (strlen((string)$language) > 0) {
            $object->$setter($value, $language);
        } else {
            $object->$setter($value);
        }

        return $object;
    }

    /**
     * Store datetime to object ex. 02-05-2016 00:30:00
     *
     *
     *
     * @param AbstractObject $object
     * @param string $target
     * @param string $value
     * @param string $language
     *
     * @return AbstractObject
     * @throws Exception
     * @throws Zend_Date_Exception
     */
    public static function storeDatetime($object, $target, $value, $language) {

        $setter = 'set' . ucfirst($target);
        if (!method_exists($object, $setter)) {
            throw New Exception(sprintf('Object "%s" has no method "%s"', get_class($object), $setter));
        }
        if (strlen((string)$value) === 0) {
            // Nothing to do here
            return null;
        }
        // Validate datetime
        $locale = new \Zend_Locale(self::DATETIME_LOCALE);
        $dateValidator = new Zend_Validate_Date(array('format' => self::DATETIME_FORMAT, 'locale', 'locale' => $locale));

        if (!$dateValidator->isValid($value)) {
            throw new \Zend_Date_Exception(sprintf('Provided datetime is invalid "%s", it should match "%s"', $value, self::DATETIME_FORMAT));
        }

        $date = Zend_Locale_Format::getDate($value, array('date_format' => self::DATETIME_FORMAT, 'locale' => $locale));
        // Yes you need a pimcore date, else will be ignored
        $pimcoreDate = new Pimcore\Date($date);
        if (strlen((string)$language) > 0) {
            $object->$setter($pimcoreDate, $language);
        } else {
            $object->$setter($pimcoreDate);
        }

        return $object;
    }

    public static function storeBoolean($object, $target, $value, $language) {


        $knownTrueValues = array('true', 't', 'y', 'yes', 'j', 'ja', '1', 'on');
        $knownFalseValues = array('false', 'f', 'n', 'no', 'n', 'nein', '0', 'off', 'null', '', null);


        if (preg_grep("/^$value$/i", $knownFalseValues)) {
            $value = false;
        } elseif (preg_grep("/^$value$/i", $knownTrueValues)) {
            $value = true;
        } else {
            // Use this as a fallback in case the value is not registered
            // convert to boolean (also strings like "no", "off", "false" etc
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }


        $setter = 'set' . ucfirst($target);
        if (!method_exists($object, $setter)) {
            throw New Exception(sprintf('Object "%s" has no method "%s"', get_class($object), $setter));
        }

        if (strlen((string)$language) > 0) {
            $object->$setter($value, $language);
        } else {
            $object->$setter($value);
        }

        return $object;
    }

    public static function storeNumber($object, $target, $value, $language) {
        $setter = 'set' . ucfirst($target);
        if (!method_exists($object, $setter)) {
            throw New Exception(sprintf('Object "%s" has no method "%s"', get_class($object), $setter));
        }
        if (strstr($value, ",") !== false) {
            $value = str_replace(",", ".", $value);
        }

        if (preg_match('[^0-9\.-]', $value) > 0) {
            throw new Exception(sprintf('Value "%s" is not a valid integer/float', $value));
        }

        $value = (float)$value;
        if (strlen((string)$language) > 0) {
            $object->$setter($value, $language);
        } else {
            $object->$setter($value);
        }

        return $object;
    }

    public static function storeDefault($object, $target, $value, $language) {
        $setter = 'set' . ucfirst($target);
        if (!method_exists($object, $setter)) {
            throw New Exception(sprintf('Object "%s" has no method "%s"', get_class($object), $setter));
        }

        if (strlen((string)$language) > 0) {
            $object->$setter($value, $language);
        } else {
//            echo "\n";
//            echo $object . "\n";
//            echo $setter . "\n";
//            echo $value . "\n";
//            echo "\n";
            $object->$setter($value);
//            var_dump($res);
        }

        return $object;
    }


    /**
     * @param AbstractObject $object
     *
     * @throws Exception
     * @return AbstractObject
     */
    // STUFF THAT BAS LEFT:
    public static function saveObject($object) {
        try {
            $object->save();
        } catch (Exception $e) {
            throw new Exception("Could not save object : " . $e->getMessage());
        }

        return $object;
    }

}