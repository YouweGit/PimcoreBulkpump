<?php

/**
 * Propel Etim Viewer/Editor
 * Class YouweEtim_AdminController
 */
class BulkPump_ParamsController extends \Pimcore\Controller\Action\Admin
{

    /**
     * @var Zend_Locale
     */
//    private $locale = null;
//
//    public function init()
//    {
//        parent::init();
//        $this->locale = Zend_Registry::get("Zend_Locale");
//    }

//    public function indexAction()
//    {
//        $this->_helper->json(array("datas" => array()));
//    }


    public function getAction()
    {
        if (!$config_id = $this->_getParam('config_id', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request_missing_config_id',
                )
            );
        }

        if (!$filter_id = $this->_getParam('filter_id', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request_missing_filter_id',
                )
            );
        }

        $config_object = CsvImport_Config::getById($config_id);
        $data = $config_object->getData();
        $data = $data[$config_id];

        $found = false;
        $filters = [];
        if($data['filters']) {
            $sort_order = 0;
            $filters = json_decode($data['filters'], true);
            foreach($filters as &$filter)
            {
                if(isset($filter['class'])) {
                    $fc = new $filter['class'];
                    /* @var $fc \BulkPump\ImportFilter\Base */
                    if(!is_subclass_of($fc, '\BulkPump\ImportFilter\Base'))
                    {
                        throw new Exception('Fatal error: Improper filter detected: ' . $filter['class']);
                    }
                    $filter['sort_order'] = ++$sort_order;
                    $filter['name'] = $fc->getName();
                    $filter['description'] = $fc->getDescription();
                    $filter['parameters'] = $fc->getParameters();
                    if($filter['id'] == $filter_id)
                    {
                        $found = true;
                        break;
                    }
                }
            }
        }

        if($found)
        {
            foreach($filter['parameters'] as &$fp)
            {
                $fp['value'] = $fp['default'];
                // overwrite with real saved value
                if(isset($filter['params'][$fp['id']]))
                {
                    $fp['value'] = $filter['params'][$fp['id']];
                }
//                if($fp['type'] == 'boolean') {
//                    $fp['value'] = intval($fp['value']);
//                }
            }
        }
        
        $this->_helper->json(array(
                'success' => true,
                'message' => 'params retrieved',
                'fields'  => $filter
            )
        );
    }
    
    
    public function saveAction()
    {
//        $body = $this->getRequest()->getRawBody();
//        $data = Zend_Json::decode($body);

//        var_dump($data);
        
        if (!$config_id = $this->_getParam('config_id', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request_missing_config_id',
                )
            );
        }

        if (!$filter_id = $this->_getParam('filter_id', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request_missing_filter_id',
                )
            );
        }

//        var_dump($config_id, $filter_id);
        
        $config_object = CsvImport_Config::getById($config_id);
        $data = $config_object->getData();
        $data = $data[$config_id];

        $found = false;
        $filters = [];
        if($data['filters']) {
            $filters = json_decode($data['filters'], true);
            foreach($filters as &$filter) 
            {
                if(isset($filter['class'])) {
                    $fc = new $filter['class'];
                    /* @var $fc \BulkPump\ImportFilter\Base */
                    if(!is_subclass_of($fc, '\BulkPump\ImportFilter\Base'))
                    {
                        throw new Exception('Fatal error: Improper filter detected: ' . $filter['class']);
                    }
                    if($filter['id'] == $filter_id)
                    {
                        $found = true;
                        break;
                    }
                }
            }
        }
        
//        var_dump($filter);
//        var_dump($filter['class']);
//        var_dump($filter['params']);
        
//        $filter['params']['12test12'] = 'testing';
        
        $params = $fc->getParameters();
        foreach($params as $param)
        {
            $value = $this->_getParam($param['id'], false);
            if($param['type'] == 'boolean') {
                $value = intval($value);
            }
            $filter['params'][$param['id']] = $value;
        }
        
        $data['filters'] = json_encode($filters);
        
        $cnf = new CsvImport_Mapper_Config();
        $cnf->update($config_id, $data);
        
        $this->_helper->json(array(
                'success' => true,
                'message' => 'params retrieved'
            )
        );
    }
    
    
    
    
}
