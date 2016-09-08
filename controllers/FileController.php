<?php


use Pimcore\File;

class BulkPump_FileController extends BulkPump_BaseController
{
    /** @var CsvProductImporter_FileConfigMapper */
    protected $filesConfigModelMapper;

    /**
     * @return array
     */
    private static function allowedUploadExtension()
    {
        return array('csv');
    }

    /**
     * @return array
     */
    private static function allowedUploadFilemime()
    {
        return array(
            'text/csv',
            'application/vnd.ms-excel'
        );
    }


    public function init()
    {
        parent::init();
        $this->filesConfigModelMapper = new CsvImport_Mapper_Config();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        $this->disableLayout();

        $tmpDir = CsvImport_File::getTmpPath();
        $dirArray = $this->_readDir($tmpDir);
        $object = new stdClass();
        $object->file = $dirArray;
        $this->_helper->json($object);
    }

    /**
     *
     *
     * @param $tmpDir
     *
     * @return array
     */
    protected function _readDir($tmpDir)
    {
        $array = array();
        if ($handle = opendir($tmpDir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && strpos($entry, 'csv') !== false) {
                    $file = $tmpDir . '/' . $entry;
                    $array[] = array(
                        'name'     => $entry,
                        'lastmod'  => filemtime($file),
                        'created'  => filectime($file),
                        'size'     => filesize($file),
                        'selected' => 0
                    );
                }
            }
            closedir($handle);
        }

        return $array;
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {

        if (!$id = $this->_getParam('id', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request',
                )
            );
        }

        $fileConfig = $this->filesConfigModelMapper->getById($id);
        if ($fileConfig === null) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'file_config_not_found',
                )
            );
        }

        $this->_helper->json(array(
                'success'      => true,
                'fieldMapping' => $fileConfig->toArray()
            )
        );
    }


    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->removeViewRenderer();
        $fileName = $_FILES['filedata']['name'];
        $fileType = $_FILES['filedata']['type'];
        $tmpName = $_FILES['filedata']['tmp_name'];
        $tmpDir = CsvImport_File::getTmpPath();


        $extension = File::getFileExtension($fileName);


        if (!in_array($extension, self::allowedUploadExtension()) || !in_array($fileType, self::allowedUploadFilemime())) {
            $this->_helper->json(array(
                'success' => false,
                'message' => 'file_type_not_accepted (ext: ' . $extension . '  mime: ' . $fileType . ')'
            ), false);
            Logger::error("the upload: " . print_r($_FILES, 1));
            $this->getResponse()->setHeader('Content-Type', 'text/html');

            return;
        }

        move_uploaded_file($tmpName, "$tmpDir/$fileName");



        $this->_helper->json(array(
            "success" => true
        ), false);

        // set content-type to text/html, otherwise (when application/json is sent) chrome will complain in
        // Ext.form.Action.Submit and mark the submission as failed
        $this->getResponse()->setHeader("Content-Type", "text/html");

    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        if (
            !is_array($data) ||
            !array_key_exists('fieldMapping', $data) ||
            !is_array($data['fieldMapping']) ||
            !array_key_exists('id', $data['fieldMapping'])
        ) {
            $this->_helper->json(array(
                'success' => false,
                'message' => 'invalid_request',
            ));
        }
        $id = $data['fieldMapping']['id'];
        unset($data['fieldMapping']['id']);

        $this->filesConfigModelMapper->update($id, $data['fieldMapping']);

        $fileConfig = $this->filesConfigModelMapper->getById($id);

        $this->_helper->json(array(
            'success'      => true,
            'message'      => 'field_mapping_saved',
            'fieldMapping' => $fileConfig->toArray()
        ));
    }

    /**x
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $result = $this->filesConfigModelMapper->delete($id);

        if (!$result) {
            $this->_helper->json(array(
                'success' => false,
                'message' => 'object_already_deleted',
            ));
        } else {
            $this->_helper->json(array(
                'success'      => true,
                'message'      => 'field_mapping_deleted',
                'fieldMapping' => array(),
            ));
        }

    }
}
