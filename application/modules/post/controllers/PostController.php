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
    
    private function _transform_content($html)
    {
    	$value = '';
    	if ( get_magic_quotes_gpc() )
    		$value = stripslashes((string)$html);
    	else
    		$value = (string)$html;
    	return $value;
    }
    
    private function _extract_abstract($html)
    {
    	$value = strip_tags($html);
    	return mb_substr($value, 0, 400, 'UTF-8');
    }
    
    private function _find_thumbpath($html)
    {
    	$rule = "#src=\"/upload/img/(20[0-9]{6}/[0-9a-fA-F]{32}\.(png|jpg|jpeg|gif))#x";
    	if (preg_match($rule, $html, $match)) {
    		return '/upload/thumb/' . $match[1];
    	}
    	return null;
    }
    
    private function _addpost($request)
    {
    	// title
    	$post_title = $request->getParam('post_title');
    	// content
    	$post_content = $this->_transform_content($request->getParam('post_content'));
    	// pub_datetime
    	$post_pub_datetime = date('Y-m-d H:i:s');
    	// author
    	$auth = Zend_Auth::getInstance();
    	$user = $auth->getStorage()->read();
    	$post_author = $user['username'];
    	// category and sub_category
    	$category = $request->getParam('post_select_bigcategory');
    	$sub_category = $request->getParam('post_select_subcategory');
    	// abstract
    	$abstract = $this->_extract_abstract($post_content);
    	// index_thumb
    	$index_thumb = $this->_find_thumbpath($post_content);
    	// Insert to DB
    	$data = array('title' => $post_title,
    			'content' => $post_content,
    			'pub_datetime' => $post_pub_datetime,
    			'author' => $post_author, 
    			'category' => $category,
    			'sub_category' => $sub_category,
    			'abstract' => $abstract,
    			'index_thumb' => $index_thumb);
    	$dbpost = new Post_Model_DbTable_Post();
    	$dbpost->insert($data);
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
		// add post to db
		$this->_addpost($request);
		
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
