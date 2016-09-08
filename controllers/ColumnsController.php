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

class BulkPump_ColumnsController extends BulkPump_BaseController {

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
        $query = $this->getParam('query');
        $columns = CsvImport_Columns::getByProfileId($profileId, $query);
        $this->_helper->json(array(
                'success' => true,
                'message' => 'success',
                'fields'   => $columns->getData()
            )
        );
    }
}
