<?php

use Pimcore\Model\Object;


class PimcoreBulkpump_FilterchainController extends \Pimcore\Controller\Action\Admin
{

    public function indexAction()
    {
        if (!$id = $this->_getParam('configId', false)) {
            $this->_helper->json(array(
                    'success' => false,
                    'message' => 'invalid_request_missing_id',
                )
            );
        }

        $config_object = CsvImport_Config::getById($id);
        $data = $config_object->getData();
        $data = $data[$id];

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
                   // $filter['sort_order'] = ++$sort_order;
                    $filter['name'] = $fc->getName();
                    $filter['description'] = $fc->getDescription();
                }
                else
                    unset($filter);
            }
        }

        $this->_helper->json(array(
                'success' => true,
                'message' => 'params retrieved',
                'fields'  => $filters
            )
        );
    }


    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        $config_id = $data['configId'];
        $config_object = CsvImport_Config::getById($config_id);
        $config = $config_object->getData();

        $config = $config[$config_id];

        $filters = $config['filters'];
        if($filters)
        {
            $filters = json_decode($filters, true);
        }
        else {
            $filters = array();
        }

        $filter_id = $data['fields']['id'];
        $updated = false;
        foreach($filters as &$f)
        {
            if($f['id'] == $filter_id)
            {
                $f['sort_order']    = $data['fields']['sort_order'];
                $f['class']         = $data['fields']['class'];
                // ignore params here -> not part of this list
                $updated = true;
            }
        }

        if(!$updated && $filter_id)
        {
            $filters[] = [
                'id'            =>  $filter_id,
                'sort_order'    =>  $data['fields']['sort_order'],
                'class'         =>  $data['fields']['class'],
                'params'         =>  []
            ];
        }

        $config['filters'] = json_encode($filters);

        $cnf = new CsvImport_Mapper_Config();
        $cnf->update($config_id, $config);

        $cnf_object = CsvImport_Config::getById($config_id);
        $cnf = $cnf_object->getData();

        $data = $cnf[$config_id];

        $filter_output = [];
        if($data['filters']) {
            $sort_order = 0;
            $filters = json_decode($data['filters'], true);
            foreach($filters as &$filter)
            {
                if(isset($filter['class'])) {

                    $newfilter = $filter;

                    $fc = new $filter['class'];
                    /* @var $fc \BulkPump\ImportFilter\Base */
                    if(!is_subclass_of($fc, '\BulkPump\ImportFilter\Base'))
                    {
                        throw new Exception('Fatal error: Improper filter detected: ' . $filter['class']);
                    }
                    $newfilter['sort_order'] = $filter['sort_order'];//++$sort_order;
                    $newfilter['name'] = $fc->getName();
                    $newfilter['description'] = $fc->getDescription();
                    $filter_output[$newfilter['id']] = $newfilter;
                }
            }
        }

        $this->_helper->json(array(
                'success' => true,
                'message' => 'filters_saved',
                'fields'  => $filter_output[$filter_id]
            )
        );
    }


    public function putAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        $config_id = $data['configId'];
        $config_object = CsvImport_Config::getById($config_id);
        $config = $config_object->getData();

        $config = $config[$config_id];

        $filters = $config['filters'];
        if($filters)
        {
            $filters = json_decode($filters, true);
        }
        else {
            $filters = array();
        }

        $filter_id = $data['fields']['id'];
        $updated = false;
        foreach($filters as &$f)
        {
            if($f['id'] == $filter_id)
            {
                $f['sort_order']    = $data['fields']['sort_order'];
                $f['class']         = $data['fields']['class'];
                // ignore params here -> not part of this list
                $updated = true;
            }
        }

        if(!$updated && $filter_id)
        {
            $filters[] = [
                'id'            =>  $filter_id,
                'sort_order'    =>  $data['fields']['sort_order'],
                'class'         =>  $data['fields']['class'],
                'params'         =>  []
            ];
        }

        $config['filters'] = json_encode($filters);

        $cnf = new CsvImport_Mapper_Config();
        $cnf->update($config_id, $config);

        $cnf_object = CsvImport_Config::getById($config_id);
        $cnf = $cnf_object->getData();

        $data = $cnf[$config_id];

        $filter_output = [];
        if($data['filters']) {
            $sort_order = 0;
            $filters = json_decode($data['filters'], true);
            foreach($filters as &$filter)
            {
                if(isset($filter['class'])) {

                    $newfilter = $filter;

                    $fc = new $filter['class'];
                    /* @var $fc \BulkPump\ImportFilter\Base */
                    if(!is_subclass_of($fc, '\BulkPump\ImportFilter\Base'))
                    {
                        throw new Exception('Fatal error: Improper filter detected: ' . $filter['class']);
                    }
                    $newfilter['sort_order'] = $filter['sort_order'];//++$sort_order;
                    $newfilter['name'] = $fc->getName();
                    $newfilter['description'] = $fc->getDescription();
                    $filter_output[$newfilter['id']] = $newfilter;
                }
            }
        }

        $this->_helper->json(array(
                'success' => true,
                'message' => 'filters_saved',
                'fields'  => $filter_output[$filter_id]
            )
        );
    }

    
    public function deleteAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        $config_id = $data['configId'];
        $config_object = CsvImport_Config::getById($config_id);
        $config = $config_object->getData();

        $config = $config[$config_id];

        $filters = $config['filters'];
        if($filters)
        {
            $filters = json_decode($filters, true);
        }
        else {
            $filters = array();
        }

        $filter_id = $data['fields'];
        $updated = false;
        foreach($filters as $key => &$f)
        {
            if($f['id'] == $filter_id)
            {
                $updated = true;
                break;
            }
        }

        if($updated) unset($filters[$key]);

        $config['filters'] = json_encode(array_values($filters));

        $cnf = new CsvImport_Mapper_Config();
        $cnf->update($config_id, $config);

        $this->_helper->json(array(
                'success' => true,
                'message' => 'filters_saved'
            )
        );
    }
}


