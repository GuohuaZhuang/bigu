<?php

require(APPLICATION_PATH . '/modules/auth/models/DbTable/Roles.php');

class Auth_RolesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->manage = true;
        $roles = new Auth_Model_DbTable_Roles();
        $result = $roles->fetchAll()->toArray();
        $this->view->result = $result;
    }

}
