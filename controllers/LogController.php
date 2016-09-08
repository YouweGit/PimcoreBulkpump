<?php
/**
 * LogController.php
 *
 * Rest control your log entities and run environment
 *
 * @category Youwe Development
 * @package intratuin-pimcore
 * @author Bas Ouwehand <b.ouwehand@youwe.nl>
 * @date 12/7/15
 *
 */
class BulkPump_LogController extends BulkPump_BaseController
{
    /**
     *
     */
    public function indexAction()
    {
        $profileId = $this->_getParam('profileId');
        $logs = CsvImport_Log::getLogsByObjectIdSortedByDate($profileId);

        $this->_helper->json(array(
                'success' => true,
                'logs'    => $logs
            ));
    }

    public function importAction()
    {
        if (!$profileId = $this->getParam('profileId')) {
            $this->_helper->json(array(
                'success' => false,
                'message'    => 'parameter missing'
            ));
        }

        try {
            CsvDataMapper_Import_Profile::run($profileId);
        } catch( Exception $e) {
            $this->_helper->json(array(
                'success' => false,
                'message'    => $e->getMessage()
            ));
        }

        $this->_helper->json(array(
            'success' => true,
            'message'    => 'run succesfull'
        ));
    }
}