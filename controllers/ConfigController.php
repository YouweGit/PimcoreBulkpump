<?php
/**
 * Object.php
 *
 * Object Controller
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 11/9/15
 *
 */

use Pimcore\Model\Object;

class PimcoreBulkpump_ConfigController extends PimcoreBulkpump_BaseController {

    /**
     * @var
     */
    protected $configMapper;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->configMapper = new CsvImport_Mapper_Config();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     *
     */
    public function indexAction () {
        if (!$profileId = $this->getParam('profileId') ) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'Need a profile id',
                )
            );
        };
        $profile = CsvImport_Profile::getById($profileId);
        $this->_helper->json(array(
                'success' => true,
                'message' => 'success',
                'fields'   => $profile->getConfig()->getData()
            )
        );
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction () {

        if (!$profileId = $this->getParam('profileId') ) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'Need a profile id',
                )
            );
        };
        $profile = CsvImport_Profile::getById($profileId);
        $config = $profile->getConfig();
        if ($config->isEmpty()) {
            $createdConfig = $profile->createConfig();
            if ($createdConfig) {
                $config = $createdConfig;
            }
        }

        $unserializedData = $config->getData();

//        echo("<script>alert('PHP: ".$unserializedData."');</script>");

//        for($index1 = 0; $index1 < count($unserializedData); $index1++){
//            for($index2 = 0; $index2 < count($unserializedData[$index1]); $index2++){
//                try{
//                    $unserializedData[$index1][$index2] = unserialize($unserializedData[$index1][$index2]);
//                }catch (Exception $e){}
//            }
//        }

        $this->_helper->json(array(
                'success' => true,
                'message' => 'success',
                'fields'  => $unserializedData
            )
        );




    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction() {

        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        if (
            !is_array($data) ||
            !array_key_exists('fields', $data) ||
            !is_array($data['fields']) ||
            !array_key_exists('id', $data['fields'])
        ) {
            $this->_helper->json(
                array(
                    'success' => false,
                    'message' => 'Invalid request!',
                )
            );
        }
//
//        if (! is_string($data['fields']['filters'])){
//            $data['fields']['filters'] = serialize($data['fields']['filters']);
//        }


        $id = $data['fields']['id'];
        unset($data['fields']['id']);
        try{
            $rows = $this->configMapper->update($id, $data['fields']);
        } catch(Exception $e) {
            $this->_helper->json(array(
                    'success'  => false,
                    'message'  => $e->getMessage(),
                )
            );
        }

        if (empty($rows)) {
            $this->_helper->json(array(
                    'success'  => false,
                    'message'  => 'no rows updated'
                )
            );
        }



        $this->_helper->json(array(
                'success'  => true,
                'message'  => 'config saved',
                'fields'   => $rows
            )
        );
    }
}
