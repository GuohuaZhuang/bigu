<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Post.php');
require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Category.php');

class Post_PostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

	public function addAction()
	{
		$this->view->editor = true;
		$request = $this->getRequest();
		$post_title = $request->getParam('post_title');
		if (empty($post_title)) { // 没有提交情况下
			$db_category = new Post_Model_DbTable_Category();
			$bigcategorys = $db_category->getAllBigCategory();
			$this->view->bigcategorys = $bigcategorys;
			return;
		}
		// Add one new post item to DBpublic function addAction()
		$post_content = $request->getParam('post_content');
		$post_pub_datetime = date('Y-m-d H:i:s');
		$auth = Zend_Auth::getInstance();
		$user = $auth->getStorage()->read();
		$post_author = $user['username'];
		$data = array('title' => $post_title,
				'content' => $post_content,
				'pub_datetime' => $post_pub_datetime,
				'author' => $post_author);
		$dbpost = new Post_Model_DbTable_Post();
		$dbpost->insert($data);
		
		// forwart to list posts
		$this->forward('list');
	}
	
	public function deleteAction()
	{
	}
	
	public function updateAction()
	{
	}
	
	public function listAction()
	{
		$request = $this->getRequest();
		$post_title = $request->getParam('post_title');
		$post_content = $request->getParam('post_content');
		
		if (get_magic_quotes_gpc())
			$pcontent = htmlspecialchars(stripslashes((string)$post_content));
		else
			$pcontent = htmlspecialchars((string)$post_content);
		
		echo $post_title. '<br/>';
		echo $pcontent. '<br/>';
	}
	
	public function viewAction()
	{
		echo '这里是view电视台<br/>';
	}
}
