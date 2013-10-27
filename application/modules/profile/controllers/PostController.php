<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Comment.php');
require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Post.php');
require_once(APPLICATION_PATH . '/util/Global.php');

class Profile_PostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
//     	$this->view->profile = true;
    	
//     	$request = $this->getRequest();
//     	$page = $request->getParam('page');
//     	$category = $request->getParam('category');
//     	$sub_category = $request->getParam('sub_category');
//         $username = Util_Global::getUsername();
//     	if (empty($username)) {
//     		return false;
//     	}
//     	$db_post = new Post_Model_DbTable_Post();
//     	$result = $db_post->fetch_list($page, 10, $username, $category, $sub_category);
//     	$this->view->result = $result;
    	
//     	$total_count = $db_post->fetch_count($username, $category, $sub_category);
//     	$this->view->total_count = $total_count;
    }
    
    public function listAction()
    {
    	$request = $this->getRequest();
    	$username = $request->getParam('username');
    	if (empty($username)) $username = Util_Global::getUsername();
    	if (empty($username)) {
    		return false;
    	}
    	$this->redirect('/post/post/list?author='.$username);
    }
}
