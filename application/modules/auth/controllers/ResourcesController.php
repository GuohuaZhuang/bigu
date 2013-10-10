<?php

class Auth_ResourcesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	echo 'resource haha';
    	exit();
        $this->view->manage = true;
        $users = new Auth_Model_DbTable_Users();
        $roles = new Auth_Model_DbTable_Roles();
        $result = $users->fetchAllR();
        $rolenames = $roles->getAllRolename();
        $this->view->result = $result;
        $this->view->rolenames = $rolenames;
    }

}