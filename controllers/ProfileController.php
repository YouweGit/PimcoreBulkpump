<?php

use Pimcore\File;

class BulkPump_ProfileController extends BulkPump_BaseController {
    /** @var CsvImport_Mapper_Profile */
    protected $profileModelMapper;

    public function init() {
        parent::init();
        $this->profileModelMapper = new CsvImport_Mapper_Profile();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction() {
        $request = $this->getRequest();

        $profiles = $this->profileModelMapper->read()->toArray();
        $this->_helper->json(array(
                'success'    => true,
                'totalCount' => count($profiles),
                'profiles'   => $profiles
            )
        );
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction() {
        if (!$id = $this->_getParam('id', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request_missing_profile_name',
                )
            );
        }

        $profile = $this->profileModelMapper->getById($id);
        if ($profile === null) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'profile_not_found',
                )
            );
        }

        $this->_helper->json(array(
                'success' => true,
                'files'   => $profile->toArray()
            )
        );
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction() {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        if (!is_array($data)
            || !array_key_exists('profiles', $data)
            || !is_array($data['profiles'])
            || !array_key_exists('profile_name', $data['profiles'])
        ) {
            $this->_helper->json(array(
                'success' => false,
                'message' => 'invalid_request_missing_profile',
            ));
        }
        $id = $this->profileModelMapper->create($data['profiles']);
        $profile = $this->profileModelMapper->getById($id);
        $this->_helper->json(array(
            'success'  => true,
            'message'  => 'profile_saved',
            'profiles' => $profile->toArray()
        ));
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
            !array_key_exists('profiles', $data) ||
            !is_array($data['profiles']) ||
            !array_key_exists('id', $data['profiles'])
        ) {
            $this->_helper->json(
                array(
                    'success' => false,
                    'message' => 'Invalid request!',
                )
            );
        }
        $id = $data['profiles']['id'];
        unset($data['profiles']['id']);

        try {
            // PROFILE: FILENAME ETC
            $this->profileModelMapper->update(
                $id,
                $data['profiles']
            );
        } catch (Exception $e) {
            $this->_helper->json(
                array(
                    'success' => false,
                    'message' => $e->getMessage(),
                )
            );
        }

        // if object is dirty, also update the configuration
        /** @todo create dirty and update functionality */
        // CONFIG: BINDINGS CSV COLUMNS TO CLASS FIELDS
        $profile = CsvImport_Profile::getById($id);

        $profile->updateColumns();
        $profile->updateConfig();

        $this->_helper->json(
            array(
                'success' => true,
                'message' => 'profile_saved',
                'files'   => $profile->getData()
            ));
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction() {
        $id = $this->_getParam('id');
        $result = $this->profileModelMapper->delete($id);

        if (!$result) {
            $this->_helper->json(array(
                'success' => false,
                'message' => 'object_already_deleted',
            ));
        } else {
            $this->_helper->json(array(
                'success' => true,
                'message' => 'profile_deleted',
                'files'   => array(),
            ));
        }

    }

    public function duplicateAction() {
        $profileId = $this->_getParam('profileId');

        $profile = CsvImport_Profile::getById($profileId);

        if ($profile === null) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'profile_not_found'
                )
            );
        }
        $profileData = $profile->getData();

        unset($profileData['id']);
        $profileData['profile_name'] = $profileData['profile_name'] . uniqid(' ');

        try {
            $profileId = $this->profileModelMapper->create($profileData);
            $configs = $profile->getConfig()->getData();

            foreach ($configs as $config) {
                unset($config['id']);
                $config['profile_id'] = $profileId;
                $configMapper = new CsvImport_Mapper_Config();
                $configMapper->create($config);
            }
        } catch (\Exception $e) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => $e->getMessage()
                )
            );
        }


        $this->_helper->json(array(
            'success' => true,
            'message' => 'duplication_was_successful'
        ));

    }

    public function importAction() {
        $profileId = $this->_getParam('profileId');

        $profile = $this->profileModelMapper->getById($profileId);
        if ($profile === null) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'profile_not_found'
                )
            );
        }

        $importer = new CsvProductImporter_Import($profile);
        if ($importer->run() === false) {
            $this->_helper->json(array(
                'success' => false,
                'message' => 'importing_failed_please_check_log'
            ));
        }
        $this->_helper->json(array(
            'success' => true,
            'message' => 'importing_was_successful_please_check_log'
        ));

    }
}
