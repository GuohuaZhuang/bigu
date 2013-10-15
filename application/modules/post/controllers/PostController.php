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
//     	$rule = "#src=\"/upload/img/(20[0-9]{6}/[0-9a-fA-F]{32}\.(png|jpg|jpeg|gif))#x";
//     	if (preg_match($rule, $html, $match)) {
//     		return '/upload/thumb/' . $match[1];
//     	}
    	$rule = "#(<img alt=\"[^(smiley)].*?\" style)#";
    	if (preg_match($rule, $html, $match)) {
    		$html = $match[0];
    		$rule_src = "#src=\"([^\"]*)\"#";
    		if (preg_match($rule_src, $html, $match)) {
    			return $match[1];
    		}
    	}
    	return null;
    }
    
    private function _addpost($request, $post_id)
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
    	$post_category = $request->getParam('post_select_bigcategory');
    	$post_sub_category = $request->getParam('post_select_subcategory');
    	// abstract
    	$post_abstract = $this->_extract_abstract($post_content);
    	// index_thumb
    	$post_index_thumb = $this->_find_thumbpath($post_content);
    	// Insert to DB
    	$dbpost = new Post_Model_DbTable_Post();
    	if (empty($post_id)) {
	    	$data = array('title' => $post_title,
	    			'content' => $post_content,
	    			'pub_datetime' => $post_pub_datetime,
	    			'author' => $post_author, 
	    			'category' => $post_category,
	    			'sub_category' => $post_sub_category,
	    			'abstract' => $post_abstract,
	    			'index_thumb' => $post_index_thumb);
	    	$dbpost->insert($data);
    	} else {
    		$data = array('title' => $post_title,
    				'content' => $post_content,
    				'category' => $post_category,
    				'sub_category' => $post_sub_category,
    				'abstract' => $post_abstract,
    				'index_thumb' => $post_index_thumb);
    		$where = $dbpost->getAdapter()->quoteInto('id=?', $post_id);
    		$dbpost->update($data, $where);
    	}
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
    	$post_id = $request->getParam('post_id');
    	$this->_addpost($request, $post_id);
    
    	// forwart to list posts
    	$this->forward('list');
    }
    
    public function editAction()
    {
    	$this->view->editor = true;
		$request = $this->getRequest();
		$post_id = $request->getParam('post_id');
		$db_post = new Post_Model_DbTable_Post();
		$where = $db_post->getAdapter()->quoteInto('id=?', $post_id);
		$result = $db_post->fetchRow($where);
		if (empty($result)) return false;
		$result = $result->toArray();
		$this->view->result = $result;
		
		$db_category = new Post_Model_DbTable_Category();
		$bigcategorys = $db_category->getAllBigCategory();
		$this->view->bigcategorys = $bigcategorys;
		
		if (!empty($result['category'])) {
			$db_category = new Post_Model_DbTable_Category();
			$subcategorys = $db_category->getAllSubCategory($result['category']);
			$this->view->subcategorys = $subcategorys;
		}
    }
    
	public function deleteAction()
	{
		$request = $this->getRequest();
		$post_id = $request->getParam('post_id');
		$db_post = new Post_Model_DbTable_Post();
		$where = $db_post->getAdapter()->quoteInto('id=?', $post_id);
		$num = $db_post->delete($where);
// 		if (empty($num)) return false;
		
    	// forwart to list posts
    	$this->forward('list');
	}
	
	public function listAction()
	{
		$db_post = new Post_Model_DbTable_Post();
		$result = $db_post->fetchAll(null, 'pub_datetime DESC')->toArray();
		$this->view->result = $result;
	}
	
	public function viewAction()
	{
		$request = $this->getRequest();
		$post_id = $request->getParam('post_id');
		$db_post = new Post_Model_DbTable_Post();
		$where = $db_post->getAdapter()->quoteInto('id=?', $post_id);
		$result = $db_post->fetchRow($where);
		if (empty($result)) return false;
		$result = $result->toArray();
		$this->view->result = $result;
	}
}
