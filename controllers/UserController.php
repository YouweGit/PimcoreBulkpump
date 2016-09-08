<?php
/**
 * Created by PhpStorm.
 * User: Neger Des Heils
 * Date: 3-6-16
 * Time: 12:43
 */

// extending Action\Admin so the extra security doesnt block requests to check security
class PimcoreBulkpump_UserController extends \Pimcore\Controller\Action\Admin {


    public function permissionAction() {
        $right = trim($this->_getParam('permission'));
        $user = \Pimcore_Tool_Admin::getCurrentUser();
        $allowed = $user->isAllowed($right);
        $this->_helper->json(array('success' => $allowed));
    }


}