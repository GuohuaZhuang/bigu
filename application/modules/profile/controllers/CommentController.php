<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Comment.php');
require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Post.php');
require_once(APPLICATION_PATH . '/util/Global.php');

class Profile_CommentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
    	$this->view->profile = true;
    	
    	$request = $this->getRequest();
    	$page = $request->getParam('page');
        $username = Util_Global::getUsername();
    	if (empty($username)) {
    		return false;
    	}
    	$db = new Post_Model_DbTable_Comment();
    	$result = $db->fetchAllByAuthor($username);
    	$this->view->result = $result;
    	
    	$total_count = $db->fetch_count($username);
    	$this->view->total_count = $total_count;
    }
    
    public function listAction()
    {
    	$this->redirect('/profile/comment/index');
    }
}
