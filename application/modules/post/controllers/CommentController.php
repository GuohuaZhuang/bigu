<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Comment.php');
require_once(APPLICATION_PATH . '/util/Global.php');

class Post_CommentController extends Zend_Controller_Action
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
    	$request = $this->getRequest();
    	$id_post = $request->getParam('comment_id_post');
    	$content = $request->getParam('comment_content');
    	$author = $request->getParam('comment_author');
    	if (empty($id_post) || empty($content)) {
    		echo '{"err": "添加失败，参数不足"}';
    	} else {
    		// pub_datetime
    		$pub_datetime = date('Y-m-d H:i:s');
    		// Insert to DB
    		$db = new Post_Model_DbTable_Comment();
    		$data = array('id_post' => $id_post, 'content' => trim($content), 
    			'pub_datetime' => $pub_datetime, 'author' => $author);
    		$result = $db->insert($data);
    		echo '{"success": "添加成功", "id": "'.$result.'"}';
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function deleteAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id');
    	if (empty($id)) {
    		echo '{"err": "删除失败，参数不足"}';
    	} else {
	    	$db = new Post_Model_DbTable_Comment();
	    	$where = $db->getAdapter()->quoteInto('id=?', $id);
	    	$num = $db->delete($where);
	    	echo '{"success": "删除成功", "num": "'.$num.'"}';
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
	}
	
	public function topAction()
	{
		$db = new Post_Model_DbTable_Comment();
		$result = $db->fetchAllByAuthor(null);
		if (empty($result)) {
			echo '{"success": "加载完成", "num": "'.'0'.'"}';
		} else {
			$rstr = '{"success": "加载完成", "num": "'.count($result).'", "d": [';
			$i = 0;
			foreach ($result as $item) {
				if (0 != $i) $rstr .= ', ';
				// {"id": "XXX", "content": "XXX", "pub_datetime": "XXX"}
				$rstr .= '{"id": "';
				$rstr .= $item['id'];
				$rstr .= '", "content": "';
				$rstr .= Util_Global::JsonReadable(trim($item['content']), TRUE);
				$rstr .= '", "author": "';
				$rstr .= $item['author'];
				$rstr .= '", "id_post": "';
				$rstr .= $item['id_post'];
				$rstr .= '", "avatar": "';
				$rstr .= $item['avatar'];
				$rstr .= '", "pub_datetime": "';
				$rstr .= $item['pub_datetime'];
				$rstr .= '"}';
				$i ++;
			}
			$rstr .= ']}';
			echo $rstr;
		}
		// stop layout and render
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
	}
    
    public function listAction()
    {
    	$request = $this->getRequest();
    	$id_post = $request->getParam('comment_id_post');
    	if (empty($id_post)) {
    		echo '{"err": "加载失败，参数不足"}';
    	} else {
	    	$db = new Post_Model_DbTable_Comment();
	    	$result = $db->fetchAllByPostid($id_post);
	    	if (empty($result)) {
	    		echo '{"success": "加载完成", "num": "'.'0'.'"}';
	    	} else {
	    		$rstr = '{"success": "加载完成", "num": "'.count($result).'", "d": [';
	    		$i = 0;
	    		foreach ($result as $item) {
	    			if (0 != $i) $rstr .= ', ';
	    			// {"id": "XXX", "content": "XXX", "pub_datetime": "XXX"}
	    			$rstr .= '{"id": "';
	    			$rstr .= $item['id'];
	    			$rstr .= '", "content": "';
	    			$rstr .= Util_Global::JsonReadable(trim($item['content']), TRUE);
	    			$rstr .= '", "author": "';
	    			$rstr .= $item['author'];
	    			$rstr .= '", "avatar": "';
	    			$rstr .= $item['avatar'];
	    			$rstr .= '", "pub_datetime": "';
	    			$rstr .= $item['pub_datetime'];
	    			$rstr .= '"}';
	    			$i ++;
	    		}
	    		$rstr .= ']}';
	    		echo $rstr;
	    	}
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    
}
