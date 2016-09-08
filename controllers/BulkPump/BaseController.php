<?php

class BulkPump_BaseController extends \Pimcore\Controller\Action\Admin {

    public function init()
    {
        parent::init();
        $this->checkPermission('plugin_bulkpump_user');
    }

}


