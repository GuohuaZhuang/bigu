<?php

class Info_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$this->forward('aboutus');
    }
    
    public function aboutusAction()
    {
    	$this->view->info = true;
    }
    
    public function joinusAction()
    {
    	$this->view->info = true;
    }
    
    public function contactusAction()
    {
    	$this->view->info = true;
    }

}

