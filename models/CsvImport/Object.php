<?php
use Pimcore\Model\Object\AbstractObject;

class CsvImport_Object extends CsvImport_Abstract_Model
{
    /**
     * Pimcore prefix used for objects
     */
    const OBJECT_PREFIX = 'Object_';

    /**
     * @param string $rawKey
     * @param string $objectClassName
     * @param AbstractObject $parent
     * @param bool $storeAsVariant
     * @return AbstractObject|Object_Product
     */
    public static function getOrCreateObject($rawKey, $objectClassName, $parent, $storeAsVariant)
    {
        $parentFullPath = $parent->getFullPath();
        $key = Pimcore_File::getValidFilename($rawKey);
        $saveToPath = Element_Service::correctPath($parentFullPath . '/' . $key);

        /** @var Object_Abstract|Object_Product $object */
        if (Object_Service::pathExists($saveToPath)) {
            $object = $objectClassName::getByPath($saveToPath);
        } else {
            $object = new $objectClassName();
            $object->setParent($parent);
            $object->setKey($key);
            if($storeAsVariant){
                $object->setType(AbstractObject::OBJECT_TYPE_VARIANT);
            }
        }

        return $object;
    }
}