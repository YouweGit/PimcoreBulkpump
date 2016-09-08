<?php

use Pimcore\Model\Object;

class PimcoreBulkpump_FiltersController extends PimcoreBulkpump_BaseController
{

    public function indexAction()
    {
        $filters =
            [
                'success' => true,
                'message' => 'success',
                'fields' => [

                ]
            ];

        // get the files or classes of the NATIVE filters
        // /htdocs/plugins/BulkPump/models/BulkPump/ImportFilter/Native
        // /htdocs/website/models/BulkPumpFilter/
        $filter_sources = [
            [
                'path' => PIMCORE_PLUGINS_PATH . '/BulkPump/models/BulkPump/ImportFilter/Native/',
                'namespace' => 'BulkPump\\ImportFilter\\Native\\'
            ],
            [
                'path' => PIMCORE_WEBSITE_PATH . '/models/BulkPumpFilter/',
                'namespace' => 'BulkPumpFilter\\'
            ],
        ];
        // add the custom (project specific) filters

        foreach ($filter_sources as &$filter_source) {
            $filterfiles = glob($filter_source['path'] . '*.php');
            foreach ($filterfiles as $ff) {
                $filter_info = [];

                $ff_stripped = basename($ff, '.php');
                $ff_class = $filter_source['namespace'] . $ff_stripped;
                $fc = new $ff_class;

                $filter_info['class'] = $fc->getClass();
                $filter_info['name'] = $fc->getName();
                $filter_info['description'] = $fc->getDescription();
                $filter_info['params'] = $fc->getParameters();

                if($fc->isEnabled()) {
                    $filters['fields'][] = $filter_info;
                }
            }
        }
        $this->_helper->json($filters);
    }

}


