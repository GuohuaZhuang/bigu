<?php

require_once(APPLICATION_PATH . '/modules/profile/models/DbTable/Profile.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require_once(APPLICATION_PATH . '/util/Global.php');
require_once(APPLICATION_PATH . '/modules/auth/plugins/Authadapter.php');

class Profile_PostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    public function indexAction()
    {
    	$this->view->profile = true;
    }
    
    public function listAction()
    {
    	$this->redirect('/profile/post/index');
    }
}
