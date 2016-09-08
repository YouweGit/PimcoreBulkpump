<?php

class PimcoreBulkpump_BaseController extends \Pimcore\Controller\Action\Admin {

    public function init()
    {
        parent::init();
        $this->checkPermission('plugin_pimcorebulkpump_user');
    }

}


